<?php
namespace App\Http\Controllers;

use App\Board;
use Auth;
use DirkGroenen\Pinterest\Pinterest;
use Illuminate\Http\Request;

class HomeController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$pinterest = new Pinterest(config("services.pinterest.appid"), config("services.pinterest.appsecret"));
		$loginurl = $pinterest->auth->getLoginUrl("https://magnet.havocstudios.com/pinterest/callback", ['read_public']);

		$user = Auth::user();

		$boards = Board::withCount('pins')->where('user_id', $user->id)->orderBy('name')->get();

		return view('home', [
			'pinterestLoggedIn' => $user->pinterestLoggedIn(),
			'pinterestLoginUrl' => $loginurl,
			'boards' => $boards,
		]);


	}

}
