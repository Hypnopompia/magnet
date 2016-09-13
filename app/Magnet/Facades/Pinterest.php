<?php
namespace Magnet\Facades;

use Illuminate\Support\Facades\Facade;

class Pinterest extends Facade {

	public static function getFacadeAccessor() {
		return 'pinterest';
	}

}