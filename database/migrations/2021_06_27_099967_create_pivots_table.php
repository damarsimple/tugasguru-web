<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePivotsTable extends Migration
{
    public function up()
    {
        Schema::create("extracurricular_student_ppdb", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("student_ppdb_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("extracurricular_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->timestamps();
        });

        Schema::create("user_school", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("user_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("school_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->timestamps();
        });

        Schema::create("classroom_school", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("classroom_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("school_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->timestamps();
        });

        Schema::create("classtype_schooltype", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("classtype_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("schooltype_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->timestamps();
        });

        Schema::create("school_subject", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("subject_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("school_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->timestamps();
        });

        Schema::create("major_school", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("school_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("major_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->timestamps();
        });

        Schema::create("extracurricular_student", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("extracurricular_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("student_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->timestamps();
        });

        Schema::create("extracurricular_school", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("extracurricular_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("school_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->timestamps();
        });

        Schema::create("classroom_user", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("classroom_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("user_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->timestamps();
        });

        Schema::create("exam_supervisor", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("exam_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("user_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->timestamps();
        });

        Schema::create("exam_examsession", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("exam_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("examsession_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->timestamps();
        });

        Schema::create("exam_question", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("exam_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("question_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->timestamps();
        });

        Schema::create("question_quiz", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("quiz_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("question_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->timestamps();
        });

        Schema::create("packagequestion_question", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("packagequestion_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("question_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->timestamps();
        });

        Schema::create("classtype_subject", function (Blueprint $table) {
            $table->id();
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

        Schema::create("article_subject", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("article_id")
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

        Schema::create("article_classtype", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("article_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("classtype_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->timestamps();
        });

        Schema::create("subject_user", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("subject_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("user_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->float("kkm")->default(75);
            $table->timestamps();
        });

        Schema::create("room_user", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("room_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("user_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->boolean("is_administrator")->default(false);
            $table->timestamps();
        });

        Schema::create("access_user", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("access_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("user_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->timestamp("expired_at");
            $table->timestamps();
        });

        Schema::create("report_user", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("report_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("user_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->timestamps();
        });

        Schema::create("school_user", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("user_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("school_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->boolean("is_homeroom")->default(false);
            $table->boolean("is_headmaster")->default(false);
            $table->boolean("is_administrator")->default(false);
            $table->boolean("is_counselor")->default(false);
            $table->timestamps();
        });

        Schema::create("user_user", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("user_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->unsignedBigInteger("follower_id");
            $table
                ->foreign("follower_id")
                ->references("id")
                ->on("users")
                ->onDelete("cascade");
            $table->boolean("is_accepted")->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("exam_sessions");
    }
}
