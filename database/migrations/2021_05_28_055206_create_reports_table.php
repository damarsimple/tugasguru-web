<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    public function up()
    {
        Schema::create("reports", function (Blueprint $table) {
            $table->id();
            $table->string("name")->nullable();
            $table
                ->foreignId("user_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->json("data");
            $table->string("type");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("reports");
    }
}
