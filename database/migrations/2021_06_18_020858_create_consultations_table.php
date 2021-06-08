<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsultationsTable extends Migration
{
    public function up()
    {
        Schema::create("consultations", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("teacher_id")
                ->index()
                ->constrained("users");
            $table
                ->foreignId("user_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->string("name");
            $table->string("problem");
            $table->longText("notes")->nullable();
            $table->longText("advice")->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("consultations");
    }
}
