<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassroomTeacherSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classroom_teacher_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->index()->onDelete('cascade');
            $table->foreignId('classroom_id')->index()->onDelete('cascade');
            $table->foreignId('subject_id')->index()->onDelete('cascade');
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
        Schema::dropIfExists('classroom_teacher_subjects');
    }
}
