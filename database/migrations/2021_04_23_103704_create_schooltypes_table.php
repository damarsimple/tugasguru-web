<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchooltypesTable extends Migration
{
    public function up()
    {
        Schema::create("schooltypes", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->integer("level");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("schooltypes");
    }
}
