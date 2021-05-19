<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->references('id')
                ->on('users')->index()->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('classtype_id')->index()->constrained()->onDelete('cascade');
            $table->boolean('editable')->default(true);
            $table->enum('type', ['MULTI_CHOICE', 'ESSAY', 'FILLER']);
            $table->longText('content');
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
        Schema::dropIfExists('questions');
    }
}
