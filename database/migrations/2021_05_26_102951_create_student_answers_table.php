<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentAnswersTable extends Migration
{
    public function up()
    {
        Schema::create('student_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('answer_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('examresult_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('examsession_id')->constrained()->onDelete('cascade');
            $table->string('content')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->boolean('is_proccessed')->default(false);
            $table->float('grade', unsigned: true)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_answers');
    }
}
