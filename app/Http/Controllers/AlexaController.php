<?php
namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

class AlexaController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth:api,log.requests');
		// $this->middleware('auth:api');
	}

// {
//   "version": "1.0",
//   "response": {
//     "outputSpeech": {
//       "type": "SSML",
//       "ssml": "<speak> Here's your fact: On Mars, the Sun appears about half the size as it does on Earth. </speak>"
//     },
//     "card": {
//       "content": "On Mars, the Sun appears about half the size as it does on Earth.",
//       "title": "My Space Facts",
//       "type": "Simple"
//     },
//     "shouldEndSession": true
//   },
//   "sessionAttributes": {}
// }

	public function skill(Request $request) {
		return [
			'version' => '1.0',
			'response' => [
				'outputSpeech' => [
					'type' => 'SSML',
					'ssml' => '<speak>Here is a pin.</speak>'
				],
				'card' => [
					'content' => 'Here is your pin.',
					'title' => 'Magnet Pin',
					'type' => 'Simple'
				]
			],
			'sessionAttributes' => []
		];
	}
}
