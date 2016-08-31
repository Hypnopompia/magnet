<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyBoardsAddImage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('boards', function ($table) {
            $table->string("description")->nullable();
            $table->string("imageurl")->nullable();
            $table->string("imagewidth")->nullable();
            $table->string("imageheight")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('boards', function ($table) {
           $table->dropColumn('description');
           $table->dropColumn('imageurl');
           $table->dropColumn('imagewidth');
           $table->dropColumn('imageheight');
        });
    }
}
