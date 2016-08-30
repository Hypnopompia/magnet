<?php

namespace App\Http\Controllers;

use App\Magnet\Workerjob;
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

        $workerjob = new Workerjob;
        $workerjob->addJob('ImportBoards', ['user_id' => $user->id]);
        $workerjob->send();

        return redirect("home");
    }
}
