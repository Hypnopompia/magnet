<?php
namespace App\Http\Controllers;

use App\User;
use Auth;
use DB;
use Illuminate\Http\Request;
use Laravel\Passport\Passport;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\ValidationData;

class AlexaController extends Controller
{
	public function __construct() {
		$this->middleware(['log.requests']);
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

    private function isAccessTokenRevoked($tokenId) {
        return DB::table('oauth_access_tokens')
                    ->where('id', $tokenId)->where('revoked', 1)->exists();
    }

	private function validateToken($jwt) {
		$token = (new Parser())->parse($jwt);

        if ($token->verify(new Sha256(), file_get_contents(Passport::keyPath('oauth-public.key'))) === false) {
            // throw OAuthServerException::accessDenied('Access token could not be verified');
            return false;
        }

        // Ensure access token hasn't expired
        $data = new ValidationData();
        $data->setCurrentTime(time());

        if ($token->validate($data) === false) {
            // throw OAuthServerException::accessDenied('Access token is invalid');
            return false;
        }

        // Check if token has been revoked
        if ($this->isAccessTokenRevoked($token->getClaim('jti'))) {
            // throw OAuthServerException::accessDenied('Access token has been revoked');
            return false;
        }

        return [
        	'user_id' => $token->getClaim('sub')
        ];
	}

	public function skill(Request $request) {
		// https://developer.amazon.com/public/solutions/alexa/alexa-skills-kit/docs/handling-requests-sent-by-alexa
		if ($request->session['application']['applicationId'] != config('magnet.alexaAppId')) {
			return "Invalid applicationId";
		}

		if (!isset($request->session['user']['accessToken']) || !$token = $this->validateToken($request->session['user']['accessToken']) ) {
			return [
				'version' => '1.0',
				'response' => [
					'outputSpeech' => [
						'type' => 'SSML',
						'ssml' => '<speak>You must have a magnet account to use this skill. Please use the Alexa app to link your Amazon account with your magnet Account.</speak>'
					],
					'card' => [
						'type' => 'LinkAccount'
					],
					'shouldEndSession' => true
				],
				'sessionAttributes' => []
			];
		}

		Auth::loginUsingId($token['user_id']);
		$user = Auth::user();

		switch ($request['request']['type']) {
			case 'LaunchRequest':
				break;
			case 'IntentRequest':
				break;
			case 'SessionEndedRequest':
				break;
		}

		return [
			'version' => '1.0',
			'response' => [
				'outputSpeech' => [
					'type' => 'SSML',
					'ssml' => '<speak>Hello, ' . $user->name . '</speak>'
				],
				'card' => [
					'content' => 'Hello, ' . $user->name . '',
					'title' => 'Magnet',
					'type' => 'Simple'
				]
			],
			'sessionAttributes' => []
		];
	}
}
