<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('segment_id');
            $table->unsignedBigInteger('subsegment_id')->nullable();
            $table->unsignedBigInteger('twosubsegment_id')->nullable();
            $table->unsignedBigInteger('threesubsegment_id')->nullable();
            $table->unsignedBigInteger('unit_of_measure_id')->nullable();
            $table->unsignedBigInteger('id_account')->nullable();
            $table->unsignedBigInteger('id_user');

            $table->string('code_comercial',30)->unique();
            $table->string('type',15);
            $table->string('description',150);
            $table->decimal('price', 64, 2);
            $table->decimal('price_buy', 64, 2);
            $table->decimal('cost_average', 64, 2);
            $table->string('photo_product',150)->nullable();
            $table->string('money',2);
            $table->string('exento',1);
            $table->string('islr',1);
            $table->decimal('special_impuesto', 64, 2);

            $table->string('status',1);
            
            
            $table->foreign('segment_id')->references('id')->on('segments');
            $table->foreign('subsegment_id')->references('id')->on('subsegments');
            $table->foreign('twosubsegment_id')->references('id')->on('two_subsegments');
            $table->foreign('threesubsegment_id')->references('id')->on('three_subsegments');
            $table->foreign('unit_of_measure_id')->references('id')->on('unit_of_measures');
            $table->foreign('id_user')->references('id')->on('users');
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
        Schema::dropIfExists('products');
    }
}
