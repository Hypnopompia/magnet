<?php
namespace App\Http\Controllers;

use App\Board;
use App\Magnet\Workerjob;
use App\Pin;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Log;

class SQSController extends Controller
{
	public function sqs(Request $request) {
		Log::debug("job", $request->all());

		switch ($request->jobname) {
			case 'ImportBoards':
				User::find($$request->jobdata['user_id'])->importBoards();
				break;
			case 'ImportPins':
				$board = Board::find($request->jobdata['board_id']);
				$board->user->importPins($board);
				break;
			case 'ResolvePinLink':
				Pin::find($request->jobdata['pin_id'])->resolvePinLink();
				break;
			case 'DownloadImage':
				Pin::find($request->jobdata['pin_id'])->downloadImage();
				break;
			default:
				throw new \Exception('Job type not found: ' . $request->jobname);
		}
		return response()->json(['ok' => true]);
	}

	public function addBoardsJobs(Request $request) {
		$workerjob = new Workerjob;

		foreach (User::all() as $user) {
			$workerjob->addJob('ImportBoards', ['user_id' => $user->id]);
		}

		$workerjob->send();

		return response()->json(['ok' => true]);
	}

	public function addPinsJobs(Request $request) {
		$workerjob = new Workerjob;
		$users = User::whereNotNull('pinterestaccesstoken')->get();

		if (!$users) {
			return response()->json(['ok' => true]);
		}

		foreach ($users as $user) {
			// Get the first board that either hasn't been imported or hasn't been updated in more than a day
			$board = $user->boards()
				->where(function($q){
					$q->where('updated_at', '<', Carbon::now()->subDay() )
					->orWhere('imported', false);
				})
				->orderBy('imported')
				->orderBy('updated_at')
				->first();

			if ($board) {
				Log::debug("Adding ImportPins Job", ['user' => $user, 'board' => $board]);
				$workerjob->addJob('ImportPins', ['board_id' => $board->id]);

				if ($board->imported) {
					$board->touch();
				} else {
					$board->imported = true;
					$board->save();
				}
			} else {
				Log::debug("No boards need importing", ['user' => $user]);
			}
		}

		$workerjob->send();

		return response()->json(['ok' => true]);
	}
}
