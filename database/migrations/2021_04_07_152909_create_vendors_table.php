<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('parroquia_id');
            $table->unsignedBigInteger('comision_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('user_id');
            $table->string('code',20);
            $table->string('cedula_rif',30);
            $table->string('name',100);
            $table->string('surname',100);
            $table->string('email',150);
            $table->string('phone',30);
            $table->string('phone2',30)->nullable();
            $table->decimal('comision',64,2);
            $table->string('instagram',100)->nullable();
            $table->string('facebook',100)->nullable();
            $table->string('twitter',100)->nullable();
            $table->string('especification',150)->nullable();
            $table->string('observation',150)->nullable();
            $table->string('direction',150)->nullable();
           
            $table->string('status',1);
            
            $table->foreign('parroquia_id')->references('id')->on('parroquias');
            $table->foreign('comision_id')->references('id')->on('comision_types');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('vendors');
    }
}
