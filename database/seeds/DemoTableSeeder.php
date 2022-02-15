<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('companies')->insert([
            'login' => 'trekexol',
            'email' => 'CEFREITAS.16@GMAIL.COM',
            'code_rif' => '003548625-9',
            'razon_social' => 'Empresa DEMO ARYA',
            'period' => 2021,
            'phone' => '0424 201-3215',
            'address' => 'PLAZA VENEZUELA',
            'franqueo_postal' => '1010',
            'tax_1' => 16,
            'tax_2' => 0,
            'tax_3' => 0,
            'retention_islr' => 0,
            'tipoinv_id' => 1,
            'tiporate_id' => 1,
            'rate' => 3115193.41,
            'rate_petro' => 9000000.00,
            'foto_company' => 'default',
            'status' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        DB::table('professions')->insert([
            'name' => 'Administrador',
            'description' => 'Supervisa el area administrativa',
            'status' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('professions')->insert([
            'name' => 'Secretaria',
            'description' => 'Se encarga de la parte administrativa',
            'status' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('positions')->insert([
            'id' => 1,
            'name' => 'Ingeniero',
            'description' => 'Se encarga del sistema',
            'status' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('positions')->insert([
            'id' => 2,
            'name' => 'Contador',
            'description' => 'Se encarga de las finanzas',
            'status' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        DB::table('unit_of_measures')->insert([
            'id' => 1,
            'code' => 'Kgs',
            'description' => 'Kilogramos',
            'status' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('unit_of_measures')->insert([
            'id' => 2,
            'code' => 'Lts',
            'description' => 'Litros',
            'status' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('modelos')->insert([
            'id' => 1,
            'description' => 'Chevrolet',
            'status' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('modelos')->insert([
            'id' => 2,
            'description' => 'Toyota',
            'status' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('colors')->insert([
            'id' => 1,
            'description' => 'Negro',
            'status' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('colors')->insert([
            'id' => 2,
            'description' => 'Blanco',
            'status' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('transports')->insert([
            'modelo_id' => 1,
            'color_id' => 1,
            'user_id' => 1,
            'type' => 'Carro',
            'placa' => 'ABF22N',
            'status' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('segments')->insert([
            'id' => 1,
            'description' => 'Bebidas',
            'status' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('segments')->insert([
            'id' => 2,
            'description' => 'Alimentos',
            'status' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('subsegments')->insert([
            'id' => 1,
            'segment_id' => 1,
            'description' => 'Refrescos',
            'status' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('subsegments')->insert([
            'id' => 2,
            'segment_id' => 2,
            'description' => 'Lata',
            'status' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('salary_types')->insert([
            'id' => 1,
            'name' => 'Alto',
            'description' => '600 a 800',
            'status' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('salary_types')->insert([
            'id' => 2,
            'name' => 'Medio',
            'description' => '400 a 600',
            'status' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
       
        DB::table('comision_types')->insert([
            'description' => 'Comision 1',
            'status' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        DB::table('branches')->insert([
            'id' => 1,
            'company_id' => 1,
            'parroquia_id' => 10111,
            'description' => 'Sucursal Principal',
            'direction' => 'Chacaito',
            'phone' => '0212-7651562',
            'phone2' => '0212-7651569',
            'person_contact' => 'Nestor',
            'phone_contact' => '0414-2351562',
            'observation' => 'Ninguna',
            'status' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        DB::table('academiclevels')->insert([
            'id' => 1,
            'name' => 'Licenciado',
            'description' => 'Graduado Universitario',
            'status' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('academiclevels')->insert([
            'id' => 2,
            'name' => 'Bachiller',
            'description' => 'Finalizo Bachillerato',
            'status' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('employees')->insert([
            'id' => 1,
            'position_id' => 1,
            'salary_types_id' => 1,
            'profession_id' => 1,
            'estado_id' => 1,
            'municipio_id' => 101,
            'parroquia_id' => 10102,
            'branch_id' => 1,
            'id_empleado' => '26.396.591',
            'nombres' => 'Primer Empleado',
            'apellidos' => 'Prueba',
            'fecha_ingreso' => '2021-04-01',
            'fecha_nacimiento' => '1997-09-09',
            'direccion' => 'Fuerzas Armadas',
            'monto_pago' => 1561651156.10,
            'email' => 'cefreitas.16@gmail.com',
            'telefono1' => '0414 236-1514',
            'acumulado_prestaciones' => 0,
            'acumulado_utilidades' => 0,
            'amount_utilities' => 'Ma',
            'status' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('Vendors')->insert([
            'id' => 1,
            'parroquia_id' => 10113,
            'comision_id' => 1,
            'employee_id' => 1,
            'user_id' => 1,
            'code' => '0001',
            'cedula_rif' => '27.615.651',
            'name' => 'Primer Vendedor',
            'surname' => 'Prueba',
            'email' => 'Eduardoperez17@gmail.com',
            'phone' => '0414 255-1545',
            'phone2' => '0412 255-1545',
            'comision' => 0,
            'status' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('clients')->insert([
            'id' => 1,
            'id_vendor' => 1,
            'id_user' => 1,
            'type_code' => 'V-',
            'cedula_rif' => '17.615.785',
            'name' => 'Primer Cliente',
            'name_ref' => 'Primer Cliente',
            'email' => 'correo@gmail.com',
            'personcontact' => 'Carlos',
            'direction' => 'Chacao',
            'city' => 'Caracas',
            'country' => 'Venezuela',
            'phone1' => '0414 265-1651',
            'phone2' => '0424 965-2952',
            'days_credit' => 9,
            'amount_max_credit' => 999999999999.99,
            'percentage_retencion_iva' => 0,
            'percentage_retencion_islr' => 0,
            'status' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('products')->insert([
            'id' => 1,
            'segment_id' => 2,
            'subsegment_id' => 2,
            'unit_of_measure_id' => 1,
            'id_user' => 1,
            'code_comercial' => '1001',
            'type' => 'MERCANCIA',
            'description' => 'Producto 1',
            'price' => 10,
            'price_buy' => 5,
            'cost_average' => 4,
            'money' => 'D',
            'exento' => 0,
            'islr' => 0,
            'special_impuesto' => 0,
            'status' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('products')->insert([
            'id' => 2,
            'segment_id' => 2,
            'subsegment_id' => 2,
            'unit_of_measure_id' => 1,
            'id_user' => 1,
            'code_comercial' => '1002',
            'type' => 'MERCANCIA',
            'description' => 'Producto 2 Bs',
            'price' => 10,
            'price_buy' => 5,
            'cost_average' => 4,
            'money' => 'Bs',
            'exento' => 0,
            'islr' => 0,
            'special_impuesto' => 0,
            'status' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('inventories')->insert([
            'id' => 1,
            'id_user' => 1,
            'product_id' => 1,
            'code' => 1001,
            'amount' => 100,
            'status' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('inventories')->insert([
            'id' => 2,
            'id_user' => 1,
            'product_id' => 2,
            'code' => 1002,
            'amount' => 100,
            'status' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('providers')->insert([
            'id' => 1,
            'code_provider' => '1000',
            'razon_social' => 'Proveedor Prueba',
            'direction' => 'Chacaito',
            'city' => 'Caracas',
            'country' => 'Venezuela',
            'phone1' => '0414 216-5165',
            'phone2' => '0212 651-5162',
            'has_credit' => 1,
            'days_credit' => 1,
            'amount_max_credit' => 9999999999.99,
            'porc_retencion_iva' => 0.00,
            'porc_retencion_islr' => 0.00,
            'balance' => 0.00,
            'status' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }
}
