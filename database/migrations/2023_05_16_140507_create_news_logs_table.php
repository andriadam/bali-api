<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action');
            $table->unsignedBigInteger('news_id');
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->string('category');
            $table->text('body');
            $table->string('image');
            $table->timestamps();

            // $table->foreign('news_id')->references('id')->on('news')->onDelete('cascade');
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news_logs');
    }
}
