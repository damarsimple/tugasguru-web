<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    public function up()
    {
        Schema::create("subscriptions", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->unsignedBigInteger("price");
            $table->unsignedBigInteger("duration");
            $table->json("ability");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("subscriptions");
    }
}
