<?php

namespace App\Imports;

use App\ExpensesDetail;
use App\Product;
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
        ->find($row['id_combo']);



        if (empty($buscar_product)) {
            
            $costo_calculado = 0;

            $id_index = $row['id_combo'];

            foreach ($row as $row_t) {
                 
                if($id_index == $row_t['id_combo']) {
                    $costo_calculado += $row_t['cantidad_producto'] * $row_t['precio_compra_prod'];
                }

            }
 
             $product = DB::connection(Auth::user()->database_name)->table('products')->insert([
                 'id'                    => $row['id_combo'],
                 'segment_id'            => 1, 
                 'subsegment_id'         => null, 
                 'twosubsegment_id'      => null, 
                 'threesubsegment_id'    => null,
                 'id_account'            => null,
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
             /*
             $global = new GlobalController; 
             $global->transaction_inv('entrada',$row['id'],'Entrada Masiva de Inventario',$row['cantidad_actual'],$row['precio'],$date,1,1,0,0,0,0,0);
             */
        }

        if ($row['id_combo'] && $row['id_producto'] != '') {

            $combo_product = DB::connection(Auth::user()->database_name)->table('combo_products')->insert([
                'id'                    => 'AUTO', 
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
