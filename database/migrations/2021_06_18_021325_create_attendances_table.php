<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    public function up()
    {
        Schema::create("attendances", function (Blueprint $table) {
            $table->id();
            $table->uuid("uuid")->nullable();
            $table->string("name")->nullable();
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
            $table
                ->foreignId("agenda_id")
                ->nullable()
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->boolean("attended")->default(false);
            $table->boolean("is_bimbel")->default(false);
            $table->string("reason")->nullable();
            $table->timestamps();
            $table->timestamp('date')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists("attendances");
    }
}
