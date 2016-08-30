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
            $table->integer('user_id');
            $table->integer('board_id');
            $table->string("pinterestid")->unique();
            $table->string("link", 512)->nullable();
            $table->string("url", 1024)->nullable();
            $table->string("note", 512)->nullable();
            $table->string("imageurl")->nullable();
            $table->string("imagewidth")->nullable();
            $table->string("imageheight")->nullable();
            $table->string("color")->nullable();


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
        Schema::drop('pins');
    }
}
