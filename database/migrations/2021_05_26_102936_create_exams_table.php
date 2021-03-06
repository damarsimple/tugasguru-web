<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamsTable extends Migration
{
    public function up()
    {
        Schema::create("exams", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table
                ->foreignId("subject_id")
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("examtype_id")
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("classroom_id")
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("user_id")
                ->constrained("users")
                ->onDelete("cascade");
            $table->longText("description")->nullable();
            $table->longText("hint")->nullable();
            $table->boolean("is_odd_semester")->default(true);
            $table->year("education_year_start");
            $table->year("education_year_end");
            $table->integer("time_limit")->default(120);
            $table->boolean("allow_show_result")->default(true);
            $table->boolean("shuffle")->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("exams");
    }
}
