<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePinsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pins', function (Blueprint $table) {
			$table->increments('id');
			$table->string("pinterestid")->unique();
			$table->integer('board_id');
			$table->string("pinteresturl", 512)->nullable();
			$table->string("pinterestlink", 512)->nullable();
			$table->string("url", 2048)->nullable();
			$table->string("pinterestimage")->nullable();
			$table->string("image")->nullable();
			$table->string("imagewidth")->nullable();
			$table->string("imageheight")->nullable();
			$table->text("note")->nullable();
			$table->string("color")->nullable();
			$table->timestamp("pinterestcreated_at");
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('pins');
	}
}
