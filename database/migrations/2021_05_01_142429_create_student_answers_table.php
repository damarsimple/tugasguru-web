<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('answer_id')->nullable()->constrained();
            $table->foreignId('examresult_id')->nullable()->constrained();
            $table->foreignId('question_id')->constrained();
            $table->foreignId('student_id')->constrained();
            $table->foreignId('exam_id')->constrained();
            $table->foreignId('examsession_id')->constrained();
            $table->string('content')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->boolean('is_proccessed')->default(false);
            $table->float('grade', unsigned: true)->default(0);
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
        Schema::dropIfExists('student_answers');
    }
}
