<?php

use App\Models\Transaction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create("transactions", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("transaction_id")
                ->nullable()
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("user_id")
                ->index()
                ->constrained()
                ->onDelete("cascade");
            $table->double("from")->default(0);
            $table->double("to")->default(0);
            $table->double("amount");
            $table
                ->foreignId("voucher_id")
                ->nullable()
                ->constrained();
            $table->string("uuid");
            $table->string("payment_method");
            $table->string("status")->default(Transaction::PENDING);
            $table->boolean("is_paid")->default(false);
            $table->string("staging_url")->nullable();
            $table->json("invoice_request")->nullable();
            $table->json("invoice_response")->nullable();
            $table->unsignedBigInteger("transactionable_id")->nullable();
            $table->string("transactionable_type")->nullable();
            $table->string("description")->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("transactions");
    }
}
