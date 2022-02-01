<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_quotation');
            $table->unsignedBigInteger('id_inventory');
            $table->integer('amount');
            $table->decimal('discount',64,2);
            $table->decimal('price',64,4);
            $table->decimal('rate',64,2);
            $table->boolean('retiene_iva');
            $table->boolean('retiene_islr');
            $table->string('status',1);
            $table->integer('id_inventory_histories');
            $table->foreign('id_quotation')->references('id')->on('quotations');
            $table->foreign('id_inventory')->references('id')->on('inventories');
            $table->foreign('id_inventory_histories')->references('id')->on('inventory_histories');
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
        Schema::dropIfExists('quotation_products');
    }
}
