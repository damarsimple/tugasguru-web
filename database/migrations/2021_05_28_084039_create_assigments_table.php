<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssigmentsTable extends Migration
{
    public function up()
    {
        Schema::create("assigments", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->longText("content");
            $table->timestamp("close_at")->nullable();
            $table->boolean("is_odd_semester")->default(true);
            $table
                ->foreignId("classroom_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("teacher_id")
                ->index()
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
        Schema::dropIfExists("assigments");
    }
}
