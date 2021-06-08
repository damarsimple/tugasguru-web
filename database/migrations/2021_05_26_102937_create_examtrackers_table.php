<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamtrackersTable extends Migration
{
    public function up()
    {
        Schema::create("examtrackers", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("exam_id")
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("user_id")
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("examsession_id")
                ->constrained()
                ->onDelete("cascade");
            $table->timestamp("last_activity")->useCurrent();
            $table->unsignedBigInteger("minute_passed")->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("examtrackers");
    }
}
