<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessesTable extends Migration
{
    public function up()
    {
        Schema::create("accesses", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->double("price");
            $table->string('roles')->nullable();
            $table->unsignedBigInteger("duration");
            $table->json("ability");
            $table->unsignedBigInteger("limit")->default(0);
            $table->boolean("is_limit")->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("accesses");
    }
}
