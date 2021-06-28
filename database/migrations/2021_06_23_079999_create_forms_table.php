<?php

use App\Models\Form;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormsTable extends Migration
{
    public function up()
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("user_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("school_id")
                ->nullable()
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->json('data');
            $table->string('type')->nullable();
            $table->longText('comment')->nullable();
            $table->boolean('is_ppdb')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->smallInteger('status', unsigned: true)->default(Form::PENDING);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('forms');
    }
}
