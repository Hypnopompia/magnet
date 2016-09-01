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
		foreach ($this->boards as $board) {
			Pin::where('board_id', $board->id)->delete();
			$board->delete();
		}

		$workerjob = new Workerjob;
		$workerjob->addJob('ImportBoards', ['user_id' => $this->id]);
		$workerjob->send();

		return $this;
	}

	public function fetchPinterestBoards($cursor = null) {
		$pinterest = new Pinterest(config("services.pinterest.appid"), config("services.pinterest.appsecret"));
		$pinterest->auth->setOAuthToken($this->pinterestaccesstoken);

		$options = ['fields' => 'id,name,url,description,creator,created_at,counts,image'];

		if ($cursor) {
			$options['cursor'] = $cursor;
		}

		try {
			$boards = $pinterest->users->getMeBoards($options);
		} catch (\Exception $e) {
			Log::error("fetchPinterestBoards", ['exception' => $e->getMessage(), 'user' => $this]);
			return false;
		}

		return $boards;
	}

	public function importBoards() {
		$cursor = null;

		do {
			$boards = $this->fetchPinterestBoards($cursor);

			if ($boards->pagination) {
				$cursor = $boards->pagination['cursor'];
			}

			foreach ($boards as $board) {
				Board::savePinterestBoard($this, $board);
			}

		} while ($boards && $boards->pagination);
	}

}
