<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizRewardsTable extends Migration
{
    public function up()
    {
        Schema::create("quiz_rewards", function (Blueprint $table) {
            $table->id();
            $table->double("reward");
            $table
                ->foreignId("quiz_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("reward_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("quiz_rewards");
    }
}
