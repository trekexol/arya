<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_notes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_client')->nullable();
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_vendor')->nullable();
            $table->unsignedBigInteger('id_transport')->nullable();
            $table->unsignedBigInteger('id_quotation')->nullable();

            $table->string('serie',30)->nullable();
            $table->date('date');
            $table->string('observation',150)->nullable();
            $table->string('coin',15)->nullable();

            $table->decimal('rate',64,2)->nullable()->comment = 'Tasa de cambio a bolivares del banco central de venezuela';

            $table->decimal('retencion_iva',64,4)->nullable();
            $table->decimal('retencion_islr',64,4)->nullable();
            $table->decimal('base_imponible',64,4)->nullable();
            $table->decimal('amount',64,4)->nullable();
            $table->decimal('amount_iva',64,4)->nullable();
            $table->decimal('amount_with_iva',64,4)->nullable();
            
            $table->string('status',1);
           
            $table->foreign('id_client')->references('id')->on('clients');
            $table->foreign('id_vendor')->references('id')->on('vendors');
            $table->foreign('id_transport')->references('id')->on('transports');
            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('id_quotation')->references('id')->on('quotations');
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
        Schema::dropIfExists('credit_notes');
    }
}
