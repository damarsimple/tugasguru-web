<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('examtype_id')->constrained();
            $table->foreignId('teacher_id')->constrained();
            $table->longText('description')->nullable();
            $table->longText('hint')->nullable();
            $table->boolean('is_odd_semester')->default(true);
            $table->year('education_year_start');
            $table->year('education_year_end');
            $table->string('code');
            $table->string('token')->nullable();
            $table->integer('kkm');
            $table->integer('time_limit')->default(120);
            $table->boolean('allow_show_result')->default(true);
            $table->boolean('shuffle')->default(true);
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
        Schema::dropIfExists('exams');
    }
}