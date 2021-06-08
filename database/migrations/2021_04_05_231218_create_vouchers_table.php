<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVouchersTable extends Migration
{
    public function up()
    {
        Schema::create("vouchers", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("code");
            $table->float("percentage");
            $table->longText("description")->nullable();
            $table->timestamp("expired_at");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("vouchers");
    }
}
