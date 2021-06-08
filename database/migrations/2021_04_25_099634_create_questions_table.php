<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    public function up()
    {
        Schema::create("questions", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("user_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("subject_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("classtype_id")
                ->nullable()
                ->index()
                ->constrained()
                ->onDelete("cascade");

            $table->boolean("editable")->default(true);
            $table->enum("type", [
                "MULTI_CHOICE",
                "ESSAY",
                "FILLER",
                "SURVEY",
                "SLIDE",
                "MANY_ANSWERS",
            ]);
            $table->longText("content");
            $table
                ->enum("visibility", ["PUBLIK", "PRIVAT", "SELECTPEOPLE"])
                ->default("PUBLIK");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("questions");
    }
}
