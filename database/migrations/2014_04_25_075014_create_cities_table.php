<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitiesTable extends Migration
{
    public function up()
    {
        Schema::create("cities", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("province_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->string("name");
            $table->string("type");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("cities");
    }
}
