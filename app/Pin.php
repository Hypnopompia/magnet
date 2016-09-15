<?php

namespace App;

use AWS;
use App\Board;
use App\Jobs\DownloadImage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Image;
use Log;

class Pin extends Model
{

	public static function boot() {
		parent::boot();

		Pin::created(function ($pin) {
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

	public static function savePinterestPin(Board $board, $pinterestPin) {
		$pin = Pin::where('pinterestid', $pinterestPin->id)->first();

		if ($pin && $pin->board_id !== $board->id) { // We found a pin, but it's on the wrong board so reimport it to the right board.
			Log::info('pinOnWrongBoard', ['pin' => $pin]);
			$pin->delete();
			$pin = false;
		}

		if (!$pin) {
			$pin = new Pin();
			$pin->pinterestid = $pinterestPin->id;
			$pin->board_id = $board->id;
		}

		$pin->pinteresturl = $pinterestPin->url;
		$pin->pinterestlink = $pinterestPin->link;
		$pin->url = $pinterestPin->original_link;
		$pin->note = $pinterestPin->note;
		$pin->color = $pinterestPin->color;
		$pin->pinterestcreated_at = new Carbon($pinterestPin->created_at);

		if (isset($pinterestPin->image['original']['url'])) {
			$pin->pinterestimage = $pinterestPin->image['original']['url'];
		}

		if (isset($pinterestPin->image['original']['width'])) {
			$pin->imagewidth = $pinterestPin->image['original']['width'];
		}

		if (isset($pinterestPin->image['original']['height'])) {
			$pin->imageheight = $pinterestPin->image['original']['height'];
		}

		$pin->save();

		if ($pin && $pin->image == null) {
			dispatch(new DownloadImage($pin));
		}
		return $pin;
	}

	public function resolvePinLink() {
		$this->url = Pin::followLinkUrl($this->pinterestlink);
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
		if (!$this->pinterestimage) {
			return false;
		}

		try {
			$image = Image::make($this->pinterestimage);
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

		$filename = $this->board->user_id . "/" . sha1($image->encode($ext)) . "." . $ext;
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
