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
        Schema::create('teacher_school', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('school_id')->index()->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('classroom_school', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('school_id')->index()->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('classtype_school', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classtype_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('school_id')->index()->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('school_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('school_id')->index()->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('classroom_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->index()->constrained()->onDelete('cascade');
            $table->timestamps();
        });


        Schema::create('assigment_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assigment_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->index()->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('exam_supervisor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('supervisor_id')->references('id')->on('teachers')->index()->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('exam_examsession', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('examsession_id')->index()->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('exam_question', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->index()->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('classtype_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classtype_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->index()->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('article_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->index()->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('article_classtype', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('classtype_id')->index()->constrained()->onDelete('cascade');
            $table->timestamps();
        });


        Schema::create('subject_teacher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->index()->constrained()->onDelete('cascade');
            $table->float('kkm')->default(75);
            $table->timestamps();
        });

        Schema::create('teacher_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->index()->constrained()->onDelete('cascade');
            $table->boolean('is_accepted')->default(false);
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
