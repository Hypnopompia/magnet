<?php

namespace App\Http\Middleware;

use Closure;
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
			'headers' => getallheaders(),
			'request' => $request->all(),
			'response' => $response
		]);
	}

}
