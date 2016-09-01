<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBoardsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('boards', function (Blueprint $table) {
			$table->increments('id');
			$table->string('pinterestid')->unique();
			$table->integer('user_id');
			$table->string('name');
			$table->string('description')->nullable();
			$table->string('pinteresturl')->nullable();
			$table->string('pinterestimage')->nullable();
			$table->string('image')->nullable();
			$table->integer('imagewidth')->nullable();
			$table->integer('imageheight')->nullable();
			$table->timestamp('pinterestcreated_at');
			$table->timestamp('refreshed_at')->nullable();
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
		Schema::dropIfExists('boards');
	}
}
