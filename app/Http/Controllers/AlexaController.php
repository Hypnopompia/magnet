<?php
namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

class AlexaController extends Controller
{
	public function __construct()
	{
		$this->middleware(['log.requests', 'auth:api']);
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
		// https://developer.amazon.com/public/solutions/alexa/alexa-skills-kit/docs/handling-requests-sent-by-alexa

		if ($request->session['application']['applicationId'] != config('magnet.alexaAppId')) {
			return "Invalid applicationId";
		}

		// switch ($request->request->type) {
		// 	case 'LaunchRequest':
		// 		break;
		// 	case 'IntentRequest':
		// 		break;
		// 	case 'SessionEndedRequest':
		// 		break;
		// }

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
