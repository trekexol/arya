<?php

namespace App\Imports;

use App\ExpensesDetail;
use App\Product;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Auth;

class ProductImport implements ToModel,WithHeadingRow, SkipsOnError
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
        /*Validator::make($rows->toArray(), [
            '*.name' => 'required',
            '*.email' => 'required',
            '*.password' => 'required',
        ])->validate();*/

        $product = new Product([
            
            'id'                    => $row['id'],
            'segment_id'            => $row['id_segmento'], 
            'subsegment_id'         => $row['id_subsegmento'], 
            'twosubsegment_id'      => $row['id_twosubsegment'] ?? null, 
            'threesubsegment_id'    => $row['id_threesubsegment'] ?? null, 
            'unit_of_measure_id'    => $row['id_unidadmedida'], 
            'code_comercial'        => $row['codigo_comercial'], 
            'type'                  => $row['tipo_mercancia_o_servicio'], 
            'description'           => $row['descripcion'], 
            'price'                 => $row['precio'], 
            'price_buy'             => $row['precio_compra'], 
            'cost_average'          => $row['costo_promedio'], 
            'photo_product'         => $row['foto'], 
            'money'                 => $row['moneda_d_o_bs'], 
            'exento'                => $row['exento_1_o_0'], 
            'islr'                  => $row['islr_1_o_0'], 
            'id_user'               => $user->id,
            'special_impuesto'      => 0,
            'status'                => $row['status'],
            'created_at'            => $date,
            'updated_at'            => $date,
        ]);
        $product->setConnection(Auth::user()->database_name);

        if($product->status == '1'){
            return $product;
        }
        

    }

    public function onError(\Throwable $e)
    {
        // Handle the exception how you'd like.
    }
}
