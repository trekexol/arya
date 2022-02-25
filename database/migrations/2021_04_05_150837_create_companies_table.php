<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('login',150)->unique();
            $table->string('email',255)->unique();                          //
            $table->string('code_rif');
            $table->string('razon_social',255)->unique();
            $table->string('period',4);
            $table->string('phone',15);
            $table->string('address',500);
            $table->string('franqueo_postal',15)->nullable();
            $table->decimal('tax_1',15,2)->default(0);
            $table->decimal('tax_2',15,2)->default(0);
            $table->decimal('tax_3',15,2)->default(0);
            $table->decimal('retention_islr',15,2)->default(0);
            $table->unsignedBigInteger('tipoinv_id');
            $table->unsignedBigInteger('tiporate_id');
            $table->decimal('rate',64,2);
            $table->decimal('rate_bcv',64,2)->default(0);
            $table->decimal('rate_petro',64,2);
            $table->string('foto_company',255)->nullable();
            $table->string('pie_pagina',20)->nullable();
            $table->string('status',1);
            $table->bigInteger('format_header_lines')->default(0);
            $table->bigInteger('format_header_lines_med')->default(0);
            $table->string('message_from_email',250)->nullable();
            $table->timestamps();


            $table->foreign('tipoinv_id')->references('id')->on('inventary_types');
            $table->foreign('tiporate_id')->references('id')->on('rate_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
