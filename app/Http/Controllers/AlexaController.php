<?php
namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

class AlexaController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function skill() {
		return ['foo' => 'bar'];
	}
}
