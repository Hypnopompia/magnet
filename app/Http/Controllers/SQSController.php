<?php
namespace App\Http\Controllers;

use App\Magnet\Workerjob;
use App\User;
use App\Pin;
use Illuminate\Http\Request;
use Log;

class SQSController extends Controller
{
	public function sqs(Request $request) {
		Log::debug("job", $request->all());

		switch ($request->jobname) {
			case 'ImportBoards':
				User::importBoardsJob($request->jobdata);
				break;
			case 'ImportPins':
				User::importPinsJob($request->jobdata);
				break;
			case 'ResolvePinLink':
				Pin::resolvePinLinkJob($request->jobdata);
				break;
			default:
				throw new \Exception('Job type not found: ' . $request->jobname);
		}
		return response()->json(['ok' => true]);
	}

	public function addjobs(Request $request) {
		// $workerjob = new Workerjob;
		// $workerjob->addJob('InvitationReminders', []);
		// $workerjob->send();

		return response()->json(['ok' => true]);
	}
}
