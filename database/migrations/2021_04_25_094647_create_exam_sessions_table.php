<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateExamSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamp('open_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('closed_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->string('token');
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
        Schema::dropIfExists('exam_sessions');
    }
}
