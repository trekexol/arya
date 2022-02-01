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

            $table->unsignedBigInteger('id_quotation')->nullable();
            $table->unsignedBigInteger('id_expense')->nullable();

            $table->date('date');
            $table->string('type',20);
            $table->decimal('price',64,2);
            $table->decimal('amount',64,2);
            $table->decimal('amount_real',64,2);
            
            $table->string('status',1);

            
            $table->foreign('id_quotation')->references('id')->on('quotations');
            $table->foreign('id_expense')->references('id')->on('expenses_and_purchases');
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
