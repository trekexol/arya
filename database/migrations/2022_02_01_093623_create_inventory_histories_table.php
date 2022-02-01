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
            $table->date('date');
            $table->unsignedBigInteger('id_product');
            $table->string('description',100);
            $table->string('type',20);
            $table->decimal('price',64,2);
            $table->integer('amount');	int(20)
            $table->integer('amount_real');
            $table->string('status',1);
            $table->string('branch',60);
            $table->string('centro_cost',60);
            $table->integer('number_invoice');
            $table->integer('number_delivery_note');
            $table->integer('user');
            $table->foreign('user')->references('id')->on('users');
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
