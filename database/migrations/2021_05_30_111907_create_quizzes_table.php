<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizzesTable extends Migration
{
    public function up()
    {
        Schema::create("quizzes", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table
                ->foreignId("user_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("subject_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->longText("description");
            $table->unsignedBigInteger("played_count")->default(0);
            $table->boolean("is_rewarded")->default(false);
            $table
                ->enum("difficulty", ["EASY", "MEDIUM", "HARD"])
                ->default("EASY");
            $table
                ->enum("visibility", ["PUBLIK", "PRIVAT", "SELECTPEOPLE"])
                ->default("PUBLIK");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("quizzes");
    }
}
