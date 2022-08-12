<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNominaConceptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nomina_concepts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_formula_q')->nullable();
            $table->unsignedBigInteger('id_formula_m')->nullable();
            $table->unsignedBigInteger('id_formula_s')->nullable();
            $table->unsignedBigInteger('id_account_debe')->nullable();
            $table->unsignedBigInteger('id_account_haber')->nullable();
            $table->string('abbreviation',15);
            $table->integer('order');
            $table->string('description',60);
            $table->string('type',20);
            $table->string('sign',1);
            $table->string('calculate',1);
            $table->string('prestations',1);

            $table->decimal('minimum',64,2)->nullable();
            $table->decimal('maximum',64,2)->nullable();

            $table->string('status',1);

            $table->foreign('id_formula_q')->references('id')->on('nomina_formulas');
            $table->foreign('id_formula_m')->references('id')->on('nomina_formulas');
            $table->foreign('id_formula_s')->references('id')->on('nomina_formulas');
            $table->foreign('id_account_debe')->references('id')->on('accounts');
            $table->foreign('id_account_haber')->references('id')->on('accounts');
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
        Schema::dropIfExists('nomina_concepts');
    }
}
