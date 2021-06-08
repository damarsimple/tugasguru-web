<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrivateRoomsTable extends Migration
{
    public function up()
    {
        Schema::create("private_rooms", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("first_id")
                ->references("id")
                ->on("users")
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("second_id")
                ->references("id")
                ->on("users")
                ->constrained()
                ->onDelete("cascade");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("private_rooms");
    }
}
