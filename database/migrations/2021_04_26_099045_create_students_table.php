<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained()->onDelete('cascade')
                ->references('id')
                ->on('users');
            $table->foreignId('classtype_id')->nullable()->index()->constrained()->onDelete('cascade');
            $table->bigInteger('nisn');
            $table->foreignId('school_id')->index()->constrained()->onDelete('cascade');
            $table->string('specialty')->nullable();
            $table->string('academic_degree')->nullable();
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
        Schema::dropIfExists('students');
    }
}
