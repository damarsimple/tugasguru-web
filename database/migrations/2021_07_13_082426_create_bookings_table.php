<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("user_id")
                ->index()
                ->constrained('users')
                ->onDelete("cascade");
            $table
                ->foreignId("teacher_id")
                ->index()
                ->constrained('users')
                ->onDelete("cascade");
            $table->timestamp('start_at');
            $table->string('reason')->nullable();
            $table->string('address');
            $table->boolean('is_approved')->default(false);
            $table->string('status')->default('PENDING');
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
        Schema::dropIfExists('bookings');
    }
}
