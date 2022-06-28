<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHeaderVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('header_vouchers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_anticipo')->nullable();
            $table->unsignedBigInteger('id_payment_order')->nullable();
            $table->unsignedBigInteger('id_nomina')->nullable();

            $table->string('reference',60)->nullable();
            $table->string('description',150);
            $table->date('date');

            $table->string('centro_cos',50)->nullable();
            $table->string('status',1);

            $table->foreign('id_anticipo')->references('id')->on('anticipos');
            $table->foreign('id_nomina')->references('id')->on('nominas');
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
        Schema::dropIfExists('header_vouchers');
    }
}
