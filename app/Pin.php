<?php

namespace App;

use App\Magnet\Workerjob;
use Illuminate\Database\Eloquent\Model;

use AWS;
use Image;

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

	public function boards()
	{
		return $this->belongsTo('App\Board');
	}

	public static function resolvePinLinkJob($jobData) {
		$pin = Pin::find($jobData['pin_id']);
		if (!$pin) {
			return false;
		}

		$pin->url = Pin::followLinkUrl($pin->link);
		$pin->save();
		return true;
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

	public static function downloadImageJob($jobData) {
		$pin = Pin::find($jobData['pin_id']);
		if (!$pin) {
			return false;
		}

		$pin->downloadImage();
	}

	public function downloadImage() {
		if (!$this->imageurl) {
			return false;
		}

		$image = Image::make($this->imageurl);
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

		$filename = $this->user_id . "/" . md5($image->encode($ext)) . "." . $ext;
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

		echo $filename . "\n";

		return $this;
	}
}
