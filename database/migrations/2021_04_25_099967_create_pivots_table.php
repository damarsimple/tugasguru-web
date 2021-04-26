<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePivotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classroom_teacher_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained();
            $table->foreignId('classroom_id')->constrained();
            $table->foreignId('subject_id')->constrained();
            $table->timestamps();
        });

        Schema::create('teacher_school', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained();
            $table->foreignId('school_id')->constrained();
            $table->timestamps();
        });

        foreach (['teacher_teacher', 'teacher_student'] as $y) {
            Schema::create($y, function (Blueprint $table) {
                $table->id();
                $table->foreignId('teacher_id')->constrained();
                $table->foreignId('student_id')->constrained();
                $table->boolean('is_accepted')->default(false);
                $table->boolean('is_rejected')->default(false);
                $table->timestamps();
            });
        }
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
