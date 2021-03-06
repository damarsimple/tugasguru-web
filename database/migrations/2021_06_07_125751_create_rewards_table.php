<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRewardsTable extends Migration
{
    public function up()
    {
        Schema::create("rewards", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->longText("description");
            $table->double("prize_pool");
            $table->double("reward");
            $table->integer("minimum_play_count");
            $table->boolean("is_active")->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("rewards");
    }
}
