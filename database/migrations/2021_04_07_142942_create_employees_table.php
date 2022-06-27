<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('position_id');
            $table->unsignedBigInteger('salary_types_id');
            $table->unsignedBigInteger('profession_id');
            $table->unsignedBigInteger('estado_id');
            $table->unsignedBigInteger('municipio_id');
            $table->unsignedBigInteger('parroquia_id');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('id_empleado',50);//cedula
            $table->string('apellidos',160);
            $table->string('nombres',160);
            $table->date('fecha_ingreso');
            $table->date('fecha_egreso')->nullable();
            $table->date('fecha_nacimiento');
            $table->string('direccion',250);
            $table->double('monto_pago', 64, 2);
            $table->double('asignacion_general', 64, 2)->nullable();
            $table->string('email',100);
            $table->string('telefono1',20);

            $table->unsignedBigInteger('dias_acumulado_prestaciones');
            $table->unsignedBigInteger('dias_acumulado_vacaciones');
            $table->double('int_acumulado_prestaciones', 64, 2);
            
            $table->double('acumulado_prestaciones', 64, 2);
            $table->double('acumulado_utilidades', 64, 2);

            $table->string('status',1);
            $table->string('code_employee',30)->nullable();
            $table->string('amount_utilities',2);


            $table->foreign('parroquia_id')->references('id')->on('parroquias');
            $table->foreign('municipio_id')->references('id')->on('municipios');
            $table->foreign('estado_id')->references('id')->on('estados');
            $table->foreign('profession_id')->references('id')->on('professions');
            $table->foreign('salary_types_id')->references('id')->on('salary_types');
            $table->foreign('position_id')->references('id')->on('positions');
            $table->foreign('branch_id')->references('id')->on('branches');
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
        Schema::dropIfExists('employe');
    }
}
