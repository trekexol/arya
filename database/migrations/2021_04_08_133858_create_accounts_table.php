<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
             $table->integer('code_one');
             $table->integer('code_two');
             $table->integer('code_three');
             $table->integer('code_four');
             $table->integer('code_five');
             $table->integer('period');
             $table->string('description',100);
             $table->string('type',10);
             $table->integer('level');
             $table->decimal('balance_previus',64,2);
             $table->decimal('rate',64,2)->nullable();
             $table->string('coin',10)->nullable();

             $table->string('status',1);

             //poner mejor un unique
            // $table->primary(['code_one', 'code_two', 'code_three', 'code_four', 'period']);
           
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
        Schema::dropIfExists('accounts');
    }
}
