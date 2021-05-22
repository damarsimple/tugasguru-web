<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('absents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->index()->constrained('users');
            $table->foreignId('user_id')->index()->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('type');
            $table->longText('reason');
            $table->timestamp('start_at');
            $table->timestamp('finish_at')->nullable();
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
        Schema::dropIfExists('absents');
    }
}
