<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentAssigmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_assigments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assigment_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->longText('content')->nullable();
            $table->string('external_url')->nullable();
            $table->float('grade', unsigned: true)->default(0);
            $table->boolean('is_graded')->default(false);
            $table->longText('comment')->nullable();
            $table->smallInteger('edited_times')->default(1);
            $table->timestamp('turned_at')->nullable();
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
        Schema::dropIfExists('student_assigments');
    }
}
