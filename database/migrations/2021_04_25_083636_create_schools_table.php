<?php

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('npsn')->unique();
            $table->foreignId('province_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('city_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('district_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('schooltype_id')->index()->constrained()->onDelete('cascade');
            $table->string('address');
            $table->longText('description')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longtitude')->nullable();
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
        Schema::dropIfExists('schools');
    }
}
