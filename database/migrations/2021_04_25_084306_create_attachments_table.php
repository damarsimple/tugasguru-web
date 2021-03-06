<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttachmentsTable extends Migration
{
    public function up()
    {
        Schema::create("attachments", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("user_id")
                ->nullable()
                ->constrained()
                ->onDelete("cascade");
            $table->string("name");
            $table->string("mime")->nullable();
            $table->unsignedBigInteger("attachable_id")->nullable();
            $table->string("attachable_type")->nullable();
            $table->boolean("is_proccessed")->default(false);
            $table->string("original_size")->nullable();
            $table->string("compressed_size")->nullable();
            $table->string("role")->nullable();
            $table->string("description")->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("attachments");
    }
}
