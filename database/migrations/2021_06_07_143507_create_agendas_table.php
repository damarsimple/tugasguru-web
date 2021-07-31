<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgendasTable extends Migration
{
    public function up()
    {
        Schema::create("agendas", function (Blueprint $table) {
            $table->id();
            $table->uuid("uuid")->nullable();
            $table
                ->foreignId("user_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("school_id")
                ->nullable()
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->string("name");
            $table->unsignedBigInteger("agendaable_id")->nullable();
            $table->string("agendaable_type")->nullable();
            $table->longText("description")->nullable();
            $table->timestamp("finish_at")->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("agendas");
    }
}
