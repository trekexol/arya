<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */ 
  
    public function up()
    {
        
        Schema::create('clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_vendor')->nullable();
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_cost_center')->nullable();
            $table->string('type_code',2);
            $table->string('name',80);
            $table->string('name_ref',80);
            $table->string('cedula_rif',20);
            $table->string('direction',200);
            $table->string('city',20);
            $table->string('country',20);
            $table->string('phone1',20);
            $table->string('phone2',20)->nullable();
            $table->string('email',80);
            $table->string('personcontact',80);
            $table->integer('days_credit');
            $table->decimal('amount_max_credit', 64, 2)->nullable();
            $table->decimal('percentage_retencion_iva', 64, 2)->nullable();
            $table->decimal('percentage_retencion_islr', 64, 2)->nullable();
            $table->decimal('aliquot', 64, 2)->nullable();            
            $table->string('status',1);
            $table->foreign('id_vendor')->references('id')->on('vendors');
            $table->foreign('id_user')->references('id')->on('users');
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
        Schema::dropIfExists('clients');
    }
}
