<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamsessionsTable extends Migration
{
    public function up()
    {
        Schema::create("examsessions", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table
                ->foreignId("exam_id")
                ->constrained()
                ->onDelete("cascade");
            $table->timestamp("open_at")->nullable();
            $table->timestamp("close_at")->nullable();
            $table->string("token")->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("exam_sessions");
    }
}
