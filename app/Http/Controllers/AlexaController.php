<?php
namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

class AlexaController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth:api');
	}

	public function skill(Request $request) {
		return ['foo' => 'bar', 'user' => $request->user()];
	}
}
