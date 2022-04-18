<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_quotation')->nullable();
            $table->unsignedBigInteger('id_account')->nullable();


            $table->integer('payment_type');
            $table->decimal('amount',64,2);
            $table->decimal('rate',64,2);
            $table->integer('credit_days')->nullable();
            $table->string('reference',50)->nullable();
            $table->integer('IGTF_percentage')->nullable();

            $table->string('status',1);

            $table->foreign('id_quotation')->references('id')->on('quotations');
            $table->foreign('id_account')->references('id')->on('accounts');
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
        Schema::dropIfExists('quotation_payments');
    }
}
