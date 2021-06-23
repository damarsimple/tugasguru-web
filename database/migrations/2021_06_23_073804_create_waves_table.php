<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('waves', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('school_id')->index()->constrained()->onDelete('cascade');
            $table->year("education_year_start");
            $table->year("education_year_end");
            $table->unsignedBigInteger('max_join');

            $table->boolean('allow_extracurricular')->default(false);
            $table->boolean('allow_major')->default(false);

            $table->timestamp('open_at');
            $table->timestamp('close_at');
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
        Schema::dropIfExists('waves');
    }
}
