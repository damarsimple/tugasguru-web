<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubjectsTable extends Migration
{
    public function up()
    {
        Schema::create("subjects", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("abbreviation")->nullable();
            $table->longText("description")->nullable();
            $table->string("type")->default("GENERAL"); //VOCATIONAL , LOCAL CONTENT, SPECIAL DEVELOPMENT
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("subjects");
    }
}
