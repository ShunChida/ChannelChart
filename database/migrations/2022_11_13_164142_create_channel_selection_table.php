<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChannelSelectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel_selection', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('channel_list_id');
            $table->unsignedBigInteger('channel_id');
            $table->timestamps();

            $table->foreign('channel_list_id')->references('id')->on('channel_lists')->onDelete('cascade');
            $table->foreign('channel_id')->references('id')->on('channels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('channel_selection');
    }
}
