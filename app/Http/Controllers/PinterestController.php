<?php

namespace App\Http\Controllers;

use App\Jobs\ImportBoards;
use Auth;
use DirkGroenen\Pinterest\Pinterest;
use Illuminate\Http\Request;
use Log;

class PinterestController extends Controller
{
    public function __construct()
    {
    }

    public function callback(Request $request)
    {
        $pinterest = new Pinterest(config("services.pinterest.appid"), config("services.pinterest.appsecret"));

        $token = $pinterest->auth->getOAuthToken($request->code);

        Log::debug('token', ["token" => $token->access_token]);

        $user = Auth::user();
        $user->pinterestaccesstoken = $token->access_token;
        $user->save();

        dispatch(new ImportBoards($user));

        return redirect("home");
    }
}
