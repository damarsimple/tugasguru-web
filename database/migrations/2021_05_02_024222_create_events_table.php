<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    public function up()
    {
        Schema::create("events", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("user_id")
                ->constrained()
                ->onDelete("cascade");
            $table->string("name")->nullable();
            $table->timestamp("begin_at")->nullable();
            $table->unsignedBigInteger("eventable_id")->nullable();
            $table->string("eventable_type")->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("events");
    }
}
