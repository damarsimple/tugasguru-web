<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizzesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('user_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('classtype_id')->nullable()->index()->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->index()->constrained()->onDelete('cascade');
            $table->longText('description');
            $table->enum('difficulty', ['EASY', 'MEDIUM', 'HARD'])->default('EASY');
            $table->enum('visibility', ['PUBLIK', 'PRIVAT', 'SELECTPEOPLE'])->default('PUBLIK');
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
        Schema::dropIfExists('quizzes');
    }
}
