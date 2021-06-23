<?php

use App\Models\StudentPpdb;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentPpdbsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_ppdbs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('changer_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('status')->default(StudentPpdb::PENDING);
            $table->longText('comment')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('wave_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('form_id')->nullable()->constrained()->onDelete('cascade');
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
        Schema::dropIfExists('student_ppdbs');
    }
}
