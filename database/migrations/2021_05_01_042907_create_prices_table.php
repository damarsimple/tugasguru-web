<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricesTable extends Migration
{
    public function up()
    {
        Schema::create("prices", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("price");
            $table->unsignedBigInteger("priceable_id")->nullable();
            $table->string("priceable_type")->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("prices");
    }
}
