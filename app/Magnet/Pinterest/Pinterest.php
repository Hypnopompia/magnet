<?php
namespace Magnet\Pinterest;

use App\User;
use DirkGroenen\Pinterest\Pinterest as PinterestSDK;

class Pinterest {
	protected $pinterest = null;
	protected $user = null;

	public function __construct() {
		$this->pinterest = new PinterestSDK(config("services.pinterest.appid"), config("services.pinterest.appsecret"));
	}

	public function withUser(User $user) {
		$this->user = $user;
		$this->pinterest->auth->setOAuthToken($this->user->pinterestaccesstoken);

		return $this;
	}

	public function userInfo() {
		return $this->pinterest->users->me();
	}

	public function getBoards($options) {
		return $this->pinterest->users->getMeBoards($options);
	}

	public function getBoardPins($boardId, $options) {
		return $this->pinterest->pins->fromBoard($boardId, $options);
	}

	public function getRateLimitRemaining() {
		return $this->pinterest->getRateLimitRemaining();
	}

	public function getRateLimit() {
		return $this->pinterest->getRateLimit();
	}
}
