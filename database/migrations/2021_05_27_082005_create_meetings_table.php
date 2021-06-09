<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeetingsTable extends Migration
{
    public function up()
    {
        Schema::create("meetings", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->json("data")->nullable();
            $table->timestamp("start_at")->useCurrent();
            $table->string("description")->nullable();
            $table->json("content")->nullable();
            $table->timestamp("finish_at")->nullable();
            $table
                ->foreignId("article_id")
                ->nullable()
                ->index();
            $table
                ->foreignId("classroom_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("user_id")
                ->constrained("users")
                ->onDelete("cascade");
            $table
                ->foreignId("subject_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("meetings");
    }
}
