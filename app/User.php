<?php

namespace App;

use App\Board;
use App\Magnet\Workerjob;
use DirkGroenen\Pinterest\Endpoints\Boards;
use DirkGroenen\Pinterest\Endpoints\Pins;
use DirkGroenen\Pinterest\Pinterest;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Log;

class User extends Authenticatable
{
	use Notifiable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', 'email', 'password',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token',
	];

	public function boards() {
		return $this->hasMany("App\Board");
	}

	public function pinterestLoggedIn() {
		if ($this->pinterestaccesstoken) {
			return true;
		}
		return false;
	}

	public function reset() {
		Board::where('user_id', $this->id)->delete();
		Pin::where('user_id', $this->id)->delete();
		// $this->pinterestaccesstoken = null;
		// $this->save();

		$workerjob = new Workerjob;
		$workerjob->addJob('ImportBoards', ['user_id' => $this->id]);
		$workerjob->send();

		return $this;
	}

	public function printBoards() {
		$pinterest = new Pinterest(config("services.pinterest.appid"), config("services.pinterest.appsecret"));
		$pinterest->auth->setOAuthToken($this->pinterestaccesstoken);

		$page = [];
		$boards = $pinterest->users->getMeBoards( array_merge($page, ['fields' => 'id,name,url,description,creator,created_at,counts,image']) );
		dd($boards);
	}

	public function importBoards() {
		$workerjob = new Workerjob;

		$pinterest = new Pinterest(config("services.pinterest.appid"), config("services.pinterest.appsecret"));
		$pinterest->auth->setOAuthToken($this->pinterestaccesstoken);

		$boards = false;
		do {
			$page = [];

			if ($pins && is_array($pins->pagination)) {
				$page['cursor'] = $pins->pagination['cursor'];
			}

			$options = array_merge($page, ['fields' => 'id,name,url,description,creator,created_at,counts,image']);

			$boards = $pinterest->users->getMeBoards( $options );

			foreach ($boards as $b) {
				$newBoard = [
					'user_id' => $this->id,
					'pinterestid' => $b->id,
					'name' => $b->name,
					'description' => $b->description,
				];

				if (isset($b->image['60x60']['url'])) {
					$newBoard['imageurl'] = $b->image['60x60']['url'];
				}

				if (isset($b->image['60x60']['width'])) {
					$newBoard['imagewidth'] = $b->image['60x60']['width'];
				}

				if (isset($b->image['60x60']['height'])) {
					$newBoard['imageheight'] = $b->image['60x60']['height'];
				}

				$board = Board::unguarded(function() use ($newBoard) {
					return Board::firstOrCreate($newBoard);
				});

				// $workerjob->addJob('ImportPins', ['board_id' => $board->id]);
			}

		} while ($boards && $boards->pagination);

		$workerjob->send();
	}

	public function importPins(Board $board) {
		$workerjob = new Workerjob;

		$pinterest = new Pinterest(config("services.pinterest.appid"), config("services.pinterest.appsecret"));
		$pinterest->auth->setOAuthToken($this->pinterestaccesstoken);
		$pins = false;

		do {
			$page = [];

			if ($pins && is_array($pins->pagination)) {
				$page['cursor'] = $pins->pagination['cursor'];
			}

			$options = array_merge($page, ['fields' => 'id,link,url,note,color,media,attribution,image,metadata']);

			try {
				$pins = $pinterest->pins->fromBoard($board->pinterestid, $options);

				foreach ($pins as $p) {
					$newPin = [
						'user_id' => $this->id,
						'board_id' => $board->id,
						'pinterestid' => $p->id,
						'link' => $p->link,
						'note' => $p->note,
						'color' => $p->color,
					];

					if (isset($p->image['original']['url'])) {
						$newPin['imageurl'] = $p->image['original']['url'];
					}

					if (isset($p->image['original']['width'])) {
						$newPin['imagewidth'] = $p->image['original']['width'];
					}

					if (isset($p->image['original']['height'])) {
						$newPin['imageheight'] = $p->image['original']['height'];
					}

					$pin = Pin::unguarded(function() use ($newPin) {
						try {
							return Pin::firstOrCreate($newPin);
						} catch (\Exception $e) {
							Log::error('pinCreateFailed', ['pin' => $newPin, 'error' => $e->getMessage()]);
							return false;
						}
					});

					if ($pin && $pin->image == null) {
						$workerjob->addJob('DownloadImage', ['pin_id' => $pin->id]);
					}
				}

			} catch (\Exception $e) {
				Log::error("fromBoardFailed", [ 'user' => $this, 'board' => $board, 'options' => $options, 'error' => $e->getMessage() ]);
			}

		} while ($pins && $pins->pagination);

		$workerjob->send();
	}
}
