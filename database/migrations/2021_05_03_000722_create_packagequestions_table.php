<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagequestionsTable extends Migration
{
    public function up()
    {
        Schema::create("packagequestions", function (Blueprint $table) {
            $table->id();
            $table->string("name")->nullable();
            $table
                ->foreignId("user_id")
                ->index()
                ->constrained()
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
            $table->boolean("editable")->default(true);
            $table
                ->enum("visibility", ["PUBLIK", "PRIVAT", "SELECTPEOPLE"])
                ->default("PUBLIK");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("packagequestions");
    }
}
