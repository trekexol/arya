<?php

namespace App\Imports;

use App\TempMovimientos;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Auth;


class TempMovimientosImport implements  ToModel, WithMappedCellsg
{
    public function mapping(): array
    {
        return [
            'fecha'  => 'B4',
            'referencia'  => 'C4',
            'descripcion'  => 'D4',
            'debe'  => 'E4',
            'haber'  => 'F4',
            
        ];
    }

    public function model(array $row)
    {
  

       $Client = new TempMovimientos([
            
            'banco'                        => 'bancamiga',
            'referencia_bancaria'                 => $row['referencia'], 
            'descripcion'                   => $row['descripcion'],
            'fecha'                 => date('Y-m-d h:i:s', $row['fecha']), 
            'haber'                      => $row['haber'], 
            'debe'                => $row['debe'], 
           
        ]);

        $Client->setConnection(Auth::user()->database_name);

        return $Client;

    }

    public function onError(\Throwable $e)
    {
        // Handle the exception how you'd like.
    }
}