<?php
namespace Magnet\Pinterest;

use Illuminate\Support\ServiceProvider;

class PinterestServiceProvider extends ServiceProvider {
	public function register() {
		$this->app->bind('pinterest', 'Magnet\Pinterest\Pinterest');
	}
}