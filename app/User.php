<?php

namespace App;

use App\Board;
use App\Jobs\ImportBoards;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Log;
use Pinterest;

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

    public function pins() {
        return $this->hasManyThrough('App\Pin', 'App\Board');
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

		dispatch(new ImportBoards($this));

		return $this;
	}

	public function getPinterestUserInfo() {
		return Pinterest::withUser($this)->userInfo();
	}

	public function fetchPinterestBoards($cursor = null) {
		$options = ['fields' => 'id,name,url,description,creator,created_at,counts,image'];

		if ($cursor) {
			$options['cursor'] = $cursor;
		}

		try {
			$boards = Pinterest::withUser($this)->getBoards($options);
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

			if (!$boards) {
				return false;
			}

			if ($boards->pagination) {
				$cursor = $boards->pagination['cursor'];
			}

			foreach ($boards as $board) {
				Board::savePinterestBoard($this, $board);
			}

		} while ($boards && $boards->pagination);
	}

}
