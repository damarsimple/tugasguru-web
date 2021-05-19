<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('classroom_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->references('id')
                ->on('users')->index()->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->index()->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('attendable_id')->nullable();
            $table->string('attendable_type')->nullable();
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
        Schema::dropIfExists('attendances');
    }
}
