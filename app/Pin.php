<?php

namespace App;

use App\Magnet\Workerjob;
use Illuminate\Database\Eloquent\Model;

use AWS;
use Image;
use Log;

class Pin extends Model
{

	public static function boot() {
		parent::boot();

		Pin::created(function ($pin) {
			$workerjob = new Workerjob;
			$workerjob->addJob('ResolvePinLink', ['pin_id' => $pin->id]);
			$workerjob->send();
		});
	}

	public function board() {
		return $this->belongsTo('App\Board');
	}

	public function getUrllinkAttribute($value) {
		if ($this->url) {
			return $this->url;
		}

		return $this->link;
	}

	public function getImagelinkAttribute($value) {
		if ($this->image) {
			return "https://s3.amazonaws.com/" . config("magnet.imageBucket") . "/" . $this->image;
		}

		return $this->imageurl;
	}

	public function resolvePinLink() {
		$this->url = Pin::followLinkUrl($this->link);
		$this->save();
		return $this;
	}

	public static function followLinkUrl($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Must be set to true so that PHP follows any "Location:" header
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$a = curl_exec($ch); // $a will contain all headers

		$url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL); // This is what you need, it will return you the last effective URL

		// Uncomment to see all headers
		/*
		echo "<pre>";
		print_r($a);echo"<br>";
		echo "</pre>";
		*/

		return $url; // Voila
	}

	public function downloadImage() {
		if (!$this->imageurl) {
			return false;
		}

		try {
			$image = Image::make($this->imageurl);
		} catch (\Exception $e) {
			Log::error('Pin.downloadImageFailed', ['error' => $e->getMessage()]);
			return false;
		}

		switch ($image->mime()) {
			case 'image/jpeg':
				$ext = 'jpg';
				break;
			case 'image/gif':
				$ext = 'gif';
				break;
			case 'image/png':
				$ext = 'png';
				break;
			default:
				Log::debug("Pin.downloadImage", ['unknownMimeType' => $image->mime()]);
				return false;
		}

		$filename = $this->user_id . "/" . sha1($image->encode($ext)) . "." . $ext;
		$s3 = AWS::createClient('s3');

		$s3->putObject([
			'Bucket'      => config('magnet.imageBucket'),
			'Key'         => $filename,
			'Body'        => $image->encode($ext),
			'ContentType' => $image->mime(),
			'ACL'         => 'public-read'
		]);

		$this->image = $filename;
		$this->save();

		return $this;
	}
}
