<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('views')->default(0);
            $table->boolean('is_paid')->default(false);
            $table
                ->foreignId("user_id")
                ->constrained("users")
                ->onDelete("cascade");
            $table
                ->foreignId("classtype_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("subject_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
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
        Schema::dropIfExists('courses');
    }
}
