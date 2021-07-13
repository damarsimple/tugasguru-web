<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutosavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('autosaves', function (Blueprint $table) {
            $table->id();
            $table->uuid('identifier')->nullable();
            $table
                ->foreignId("user_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->json('data');
            $table->string('type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('autosaves');
    }
}
