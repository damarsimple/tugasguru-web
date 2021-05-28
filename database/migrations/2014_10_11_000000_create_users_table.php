<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('dob')->useCurrent();
            $table->timestamp('email_verified_at')->nullable();
            $table->longText('address')->nullable();
            $table->foreignId('province_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('city_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('district_id')->index()->constrained()->onDelete('cascade');
            $table->string('phone')->unique();
            $table->integer('gender')->nullable();
            $table->string('password');
            $table->string('roles');
            $table->string('specialty')->nullable();
            $table->string('academic_degree')->nullable();
            $table->json('hidden_attribute')->nullable();

            $table->foreignId('school_id')->index()->nullable();

            $table->double('balance')->default(0);

            // students
            $table->foreignId('parent_id')
                ->nullable()
                ->references('id')
                ->on('users');

            $table->foreignId('classtype_id')->nullable();
            $table->bigInteger('nisn')->nullable();

            // teachers

            $table->boolean('is_bimbel')->default(false);
            $table->boolean('is_bk')->default(false);

            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
