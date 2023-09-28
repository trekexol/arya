<?php

namespace App\Http\Controllers\Movement;

use App\Account;
use App\DetailVoucher;
use App\HeaderVoucher;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MovementProductImportController extends Controller
{
    public function add_movement($id_account,$amount,$amountp,$rate,$coin){

        
        
        $account_mecancia_para_venta = Account::on(Auth::user()->database_name)->where('code_one',1)->where('code_two',1)->where('code_three',3)->where('code_four',1)->where('code_five',1)->first();  
        $account_mecancia_prima = Account::on(Auth::user()->database_name)->where('code_one',1)->where('code_two',1)->where('code_three',3)->where('code_four',1)->where('description','LIKE','%Materia Prima%')->first();  
        
        $header_voucher  = new HeaderVoucher();
        $header_voucher->setConnection(Auth::user()->database_name);
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d'); 
        $user =   auth()->user();
        $header_voucher->description = "Incremento de Inventario de Forma Masiva "." Tasa: ".$rate;
        $header_voucher->date = $datenow;
        $header_voucher->status =  "1";
        $header_voucher->save();


        if ($coin == 'dolares'){
            $rate = $rate;
        } else {
            $rate = 1;
        }

        $detail = new DetailVoucher();
        $detail->setConnection(Auth::user()->database_name);
        $detail->id_account = $account_mecancia_para_venta->id;
        $detail->id_header_voucher = $header_voucher->id;
        $detail->user_id = $user->id;
        $detail->tasa = $rate;
        $detail->debe = $amount*$rate;
        $detail->haber = 0;
        $detail->status =  "C";
        $detail->save();

        $detail = new DetailVoucher();
        $detail->setConnection(Auth::user()->database_name);
        $detail->id_account = $account_mecancia_prima->id;
        $detail->id_header_voucher = $header_voucher->id;
        $detail->user_id = $user->id;
        $detail->tasa = $rate;
        $detail->debe = $amountp*$rate;
        $detail->haber = 0;
        $detail->status =  "C";
        $detail->save();

        $detail2 = new DetailVoucher();
        $detail2->setConnection(Auth::user()->database_name);
        $detail2->id_account = $id_account;
        $detail2->id_header_voucher = $header_voucher->id;
        $detail2->user_id = $user->id;
        $detail2->tasa = $rate;
        $detail2->debe = 0;
        $detail2->haber = ($amount + $amountp) * $rate;
        $detail2->status =  "C";
        $detail2->save();
         
        /*Le cambiamos el status a la cuenta a M, para saber que tiene Movimientos en detailVoucher */   
        $account = Account::on(Auth::user()->database_name)->findOrFail($detail->id_account);

        if($account->status != "M"){
            $account->status = "M";
            $account->save();
        }
    }
}
