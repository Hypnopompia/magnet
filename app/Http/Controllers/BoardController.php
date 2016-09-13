<?php
namespace App\Http\Controllers;

use App\Board;
use Auth;
use Illuminate\Http\Request;

class BoardController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function index(Board $board) {
		$this->authorize('view', $board);

		$user = Auth::user();
		$board->load('pins');

		return view('board', [
			'board' => $board,
		]);
	}
}
