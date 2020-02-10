<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersSongsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_songs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('userid');   //用户id
            $table->integer('musicdbpk');   //歌曲总库id
            $table->timestamp('date');   //日期
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_songs');
    }
}
