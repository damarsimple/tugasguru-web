<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassRoomsTable extends Migration
{
    public function up()
    {
        Schema::create("classrooms", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("user_id")
                ->constrained("users")
                ->onDelete("cascade");
            $table
                ->foreignId("classtype_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("school_id")
                ->constrained()
                ->onDelete("cascade");
            $table->string("name");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("classrooms");
    }
}
