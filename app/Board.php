<?php

namespace App;

use Log;
use Carbon\Carbon;
use Pinterest;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
	public function user() {
		return $this->belongsTo("App\User");
	}

	public function pins()
	{
		return $this->hasMany('App\Pin')->orderBy('pinterestcreated_at', 'DESC');
	}


	public static function savePinterestBoard(User $user, $pinterestBoard) {
		$board = Board::where('pinterestid', $pinterestBoard->id)->first();

		if (!$board) {
			$board = new Board();
			$board->pinterestid = $pinterestBoard->id;
			$board->user_id = $user->id;
		}

		$board->name = $pinterestBoard->name;
		$board->description = $pinterestBoard->description;
		$board->pinteresturl = $pinterestBoard->url;
		$board->pinterestcreated_at = new Carbon($pinterestBoard->created_at);

		if (isset($pinterestBoard->image['60x60']['url'])) {
			$board->pinterestimage = $pinterestBoard->image['60x60']['url'];
		}

		if (isset($pinterestBoard->image['60x60']['width'])) {
			$board->imagewidth = $pinterestBoard->image['60x60']['width'];
		}

		if (isset($pinterestBoard->image['60x60']['height'])) {
			$board->imageheight = $pinterestBoard->image['60x60']['height'];
		}

		$board->save();

		return $board;
	}

	public function fetchPinterestPins($cursor = null) {
		$options = ['fields' => 'id,link,note,url,attribution,original_link,color,counts,created_at,creator,image,media,metadata'];

		if ($cursor) {
			$options['cursor'] = $cursor;
		}

		try {
			$pins = Pinterest::withUser($this->user)->getBoardPins($this->pinterestid, $options);
			Log::info("fetchPins", ['board_id' => $this->id, 'cursor' => $cursor, 'ratelimitremaining' => Pinterest::getRateLimitRemaining(), 'ratelimit' => Pinterest::getRateLimit() ]);
		} catch (\Exception $e) {
			Log::error("fetchPinterestPins", ['exception' => $e->getMessage(), 'user' => $this]);
			return false;
		}

		return $pins;
	}

	public function importPins() {
		$cursor = null;
		do {
			$pins = $this->fetchPinterestPins($cursor);

			if (!$pins) {
				return false;
			}

			if ($pins->pagination) {
				$cursor = $pins->pagination['cursor'];
			}

			foreach ($pins as $pin) {
				Pin::savePinterestPin($this, $pin);
			}
		} while ($pins && $pins->pagination);

		$this->refreshed_at = Carbon::now();
		$this->save();

		return $this;
	}
}
