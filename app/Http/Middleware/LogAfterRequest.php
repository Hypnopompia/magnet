<?php

namespace App\Http\Middleware;

use Closure;
use Request;
use Illuminate\Support\Facades\Log;

class LogAfterRequest {

	public function handle($request, Closure $next)
	{
		return $next($request);
	}

	public function terminate($request, $response)
	{
		Log::info('app.requests', [
			'url' => $request->fullUrl(),
			'method' => $request->method(),
			'requestHeaders' => Request::all(),
			'request' => $request->all(),
			'response' => $response
		]);
	}

}
