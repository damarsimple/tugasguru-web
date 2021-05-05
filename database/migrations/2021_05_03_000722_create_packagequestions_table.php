<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagequestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packagequestions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->foreignId('teacher_id')->index()->constrained();
            $table->foreignId('subject_id')->index()->constrained();
            $table->boolean('editable')->default(true);
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
        Schema::dropIfExists('packagequestions');
    }
}
