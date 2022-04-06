<?php

namespace App\Imports;

use App\ExpensesDetail;
use App\Inventory;
use App\Product;
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

       $inventory = new Inventory([
            'id'                => $row['id'],
            'product_id'        => $row['id'],
            'id_user'           => $user->id,
            'code'              => $row['codigo_comercial'], 
            'amount'            => $row['cantidad_actual'],
            'status'            => 1,
            'created_at'        => $date,
            'updated_at'        => $date,
            
        ]);

             
        $inventory->setConnection(Auth::user()->database_name);

        return $inventory;

    }

    public function onError(\Throwable $e)
    {
        // Handle the exception how you'd like.
    }
}
