<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_product');
            $table->unsignedBigInteger('id_user');
            //es la sucursal que hizo el movimiento de inventario
            $table->unsignedBigInteger('id_branch')->nullable();
            //centro de costo es la sucursal que va a pagar la factura o el inventario
            $table->unsignedBigInteger('id_centro_costo')->nullable();
            $table->unsignedBigInteger('id_quotation_product')->nullable();
            $table->unsignedBigInteger('id_expense_detail')->nullable();

            $table->date('date');
            $table->string('type',20);
            $table->decimal('price',64,2);
            $table->decimal('amount',64,2);
            $table->decimal('amount_real',64,2);
            
            $table->string('status',1);

            
            $table->foreign('id_quotation_product')->references('id')->on('quotation_products');
            $table->foreign('id_expense_detail')->references('id')->on('expenses_details');
            $table->foreign('id_centro_costo')->references('id')->on('branches');
            $table->foreign('id_branch')->references('id')->on('branches');
            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('id_product')->references('id')->on('inventories');
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
        Schema::dropIfExists('inventory_histories');
    }
}
