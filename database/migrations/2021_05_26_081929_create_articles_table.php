<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    public function up()
    {
        Schema::create("articles", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("user_id")
                ->nullable()
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("school_id")
                ->nullable()
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("teacher_id")
                ->nullable()
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->string("name");
            $table->string("slug")->nullable();
            $table->longText("content");
            $table->boolean("is_paid")->default(false);
            $table->string("role")->nullable();
            $table
                ->enum("visibility", ["PUBLIK", "PRIVAT", "SELECTPEOPLE"])
                ->default("PUBLIK");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("articles");
    }
}
