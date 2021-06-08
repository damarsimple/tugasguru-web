<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClasstypesTable extends Migration
{
    public function up()
    {
        Schema::create("classtypes", function (Blueprint $table) {
            $table->id();
            $table->integer("level");
            $table
                ->foreignId("schooltype_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("classtypes");
    }
}
