<?php

use App\Models\Transaction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index()->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('from')->default(0);
            $table->unsignedBigInteger('to')->default(0);
            $table->unsignedBigInteger('amount');
            $table->string('uuid');
            $table->string('payment_method');
            $table->string('status')->default(Transaction::PENDING);
            $table->boolean('is_paid')->default(false);
            $table->string('staging_url')->nullable();
            $table->json('invoice_request')->nullable();
            $table->json('invoice_response')->nullable();
            $table->unsignedBigInteger('transactionable_id')->nullable();
            $table->string('transactionable_type')->nullable();
            $table->string('description')->nullable();
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
        Schema::dropIfExists('transactions');
    }
}
