<?php

namespace App;

use DirkGroenen\Pinterest\Pinterest;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    public function user() {
    	return $this->belongsTo("App\User");
    }

    public function pins()
    {
        return $this->hasMany('App\Pin');
    }

	public function printPins($cursor = null) {
		$pinterest = new Pinterest(config("services.pinterest.appid"), config("services.pinterest.appsecret"));
		$pinterest->auth->setOAuthToken($this->user->pinterestaccesstoken);

		$page = [];
		if ($cursor) {
			$page['cursor'] = $cursor;
		}

		$options = array_merge($page, ['fields' => 'id,link,url,note,color,media,attribution,image,metadata']);
		$pins = $pinterest->pins->fromBoard($this->pinterestid, $options);
		return $pins;
	}
}
