<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsentsTable extends Migration
{
    public function up()
    {
        Schema::create("absents", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("user_id")
                ->nullable()
                ->index()
                ->constrained("users");
            $table
                ->foreignId("receiver_id")
                ->index()
                ->constrained('users')
                ->onDelete("cascade");
            $table->string("type");
            $table->longText("reason");
            $table->timestamp("start_at");
            $table->timestamp("finish_at")->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("absents");
    }
}
