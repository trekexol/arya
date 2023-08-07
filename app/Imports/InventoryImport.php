<?php

namespace App\Imports;

use App\ExpensesDetail;
use App\Inventory;
use App\Product;
use App\Http\Controllers\GlobalController;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Auth;


class InventoryImport implements ToModel,WithHeadingRow, SkipsOnError
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

        if ($row['precio_compra'] > 0) {
            $precio_compra = $row['precio_compra'];
            Product::on(Auth::user()->database_name)->where('id',$row['id'])->update(['price_buy' => $precio_compra]);
        }
        if ($row['precio'] > 0) {
            $precio = $row['precio'];
            Product::on(Auth::user()->database_name)->where('id',$row['id'])->update(['price' => $precio]);
        }

        $global = new GlobalController; 
        $global->transaction_inv('entrada',$row['id'],'Entrada Masiva de Inventario',$row['cantidad_actual'],$row['precio'],$date,1,1,0,0,0,0,0);

        return ;

    }

    public function onError(\Throwable $e)
    {
        // Handle the exception how you'd like.
    }
}
