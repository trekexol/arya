<?php

namespace App\Imports;

use App\ExpensesDetail;
use App\Product;
use App\Account;
use App\ComboProduct;
use App\Http\Controllers\GlobalController;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ComboImport implements ToModel,WithHeadingRow, SkipsOnError
{
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $user       =   auth()->user();
        $date = Carbon::now();


        $buscar_product = Product::on(Auth::user()->database_name)
        ->where('type','COMBO')
        ->where('id',$row['id_combo'])
        ->get();



        if($row['cantidad_producto'] == 0 OR $row['cantidad_producto'] == null){
            $row['cantidad_producto'] = 1;
        }

        if($row['precio_venta_combo'] == 0 OR $row['precio_venta_combo'] == null){
            $row['precio_venta_combo'] = 1;
        }


        if ($buscar_product->count() == 0) { // si no hay producto TIPO COMBO lo crea.

            $costo_calculado = 0;

            $account = Account::on(Auth::user()->database_name)
            ->where('description','LIKE','%Mercancia para la Venta%')
            ->first();

            if (isset($account)) {
                $id_account = $account->id;
            } else {
                $id_account = 25;
            }

             $product = DB::connection(Auth::user()->database_name)->table('products')->insert([
                 'id'                    => $row['id_combo'],
                 'segment_id'            => 1,
                 'subsegment_id'         => null,
                 'twosubsegment_id'      => null,
                 'threesubsegment_id'    => null,
                 'id_account'            => $id_account,
                 'unit_of_measure_id'    => 1,
                 'code_comercial'        => $row['codigo_comercial_combo'],
                 'type'                  => 'COMBO',
                 'description'           => $row['nombre_combo'],
                 'price'                 => $row['precio_venta_combo'],
                 'price_buy'             => $costo_calculado,
                 'cost_average'          => 0,
                 'photo_product'         => null,
                 'money'                 => 'D',
                 'exento'                => 0,
                 'islr'                  => 0,
                 'id_user'               => $user->id,
                 'special_impuesto'      => 0,
                 'status'                => 1,
                 'created_at'            => $date,
                 'updated_at'            => $date
             ]);

            /* $global = new GlobalController;
             $global->transaction_inv('creado',$row['id_combo'],'Importacion Masiva de Combos',0,$row['precio_venta_combo'],$date,1,1,0,0,0,0,0);*/

        }

        /***SE BUSCA SI EL COMBO EXISTE CON EL PRODUCTO */
        $buscarcombo = DB::connection(Auth::user()->database_name)
                        ->table('combo_products')
                        ->where('id_combo',$row['id_combo'])
                        ->where('id_product',$row['id_producto'])
                        ->get();

        /*****SI EXISTE SE ACTUALIZA */
        if($buscarcombo->count() > 0){


            ComboProduct::on(Auth::user()->database_name)
            ->where('id_combo',$row['id_combo'])
            ->where('id_product',$row['id_producto'])
            ->update(['amount_per_product' => $row['cantidad_producto']]);

            Product::on(Auth::user()->database_name)
            ->where('id',$row['id_combo'])
            ->update(['price' => $row['precio_venta_combo']]);

        }else{
               /*****SI NO SE CREA */
            $combo_product = DB::connection(Auth::user()->database_name)->table('combo_products')->insert([
                'id_combo'              => $row['id_combo'],
                'id_product'            => $row['id_producto'],
                'amount_per_product'    => $row['cantidad_producto']

            ]);

        }



         return;
    }





    public function onError(\Throwable $e)
    {
        // Handle the exception how you'd like.
    }
}
