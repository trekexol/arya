<?php

namespace App\Http\Controllers\Validations;

use App\DetailVoucher;
use App\ExpensesAndPurchase;
use App\ExpensesDetail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseDetailValidationController extends Controller
{
    public function updateMovement($expense_detail){

        
        $amount_old = $expense_detail->price_old * $expense_detail->amount_old;

        $amount_new = $expense_detail->price * $expense_detail->amount;
        
        $difference_cuentas_por_pagar = 0;
        $difference_amount = 0;

        if($expense_detail->exento == false){
            
            $movement = $this->checkMovement($expense_detail,$amount_old);
            $movement_iva = $this->checkMovementIva($expense_detail);
            
        }else{
            $movement = $this->checkMovement($expense_detail,$amount_old);
            $movement_iva = null;
        }

       
        if(isset($movement)){
            
            $detail = DetailVoucher::on(Auth::user()->database_name)->findOrFail($movement->id);
            $difference_cuentas_por_pagar = $amount_new - $detail->debe;
            $difference_amount = $amount_new - $detail->debe;
            $detail->debe = $amount_new;
            $detail->save();
        }
        
        if(isset($movement_iva)){
          
            $amount_iva_old = bcdiv(($amount_old * $expense_detail->expenses['iva_percentage']) / 100, '1', 2);

            $amount_iva_new = bcdiv(($amount_new * $expense_detail->expenses['iva_percentage']) / 100, '1', 2);
          
            $detail_iva = DetailVoucher::on(Auth::user()->database_name)->findOrFail($movement_iva->id);

            $amount_iva_debe_new = $detail_iva->debe - $amount_iva_old + $amount_iva_new;

            $difference_cuentas_por_pagar += $amount_iva_debe_new - $detail_iva->debe;

            $detail_iva->debe = $amount_iva_debe_new;

            $detail_iva->save();

        }

        if($difference_cuentas_por_pagar != 0){
            $movement_cuentas_por_pagar = $this->checkMovementCuentaPorPagar($expense_detail);

            $detail_cuentas_por_pagar = DetailVoucher::on(Auth::user()->database_name)->findOrFail($movement_cuentas_por_pagar->id);

            $detail_cuentas_por_pagar->haber += $difference_cuentas_por_pagar;

            $detail_cuentas_por_pagar->save();

            $this->updateAmountExpense($expense_detail,$difference_amount,$amount_iva_debe_new);
        }

        
    }

    public function deleteMovement($expense_detail){

        $amount = $expense_detail->price * $expense_detail->amount;

        $difference_cuentas_por_pagar = 0;

        if($expense_detail->exento == false){
            
            $movement = $this->checkMovement($expense_detail,$amount);
            $movement_iva = $this->checkMovementIva($expense_detail);
            
        }else{
            $movement = $this->checkMovement($expense_detail,$amount);
            $movement_iva = null;
        }

        if(isset($movement)){
            
            $detail = DetailVoucher::on(Auth::user()->database_name)->findOrFail($movement->id);
            $difference_cuentas_por_pagar -= $amount;
            $detail->delete();
        }
        
        if(isset($movement_iva)){
          
            $amount_iva_old = bcdiv(($amount * $expense_detail->expenses['iva_percentage']) / 100, '1', 2);
          
            $detail_iva = DetailVoucher::on(Auth::user()->database_name)->findOrFail($movement_iva->id);

            $amount_iva_debe_new = $detail_iva->debe - $amount_iva_old;

            $difference_cuentas_por_pagar += $amount_iva_debe_new - $detail_iva->debe;

            $detail_iva->debe = $amount_iva_debe_new;
            if($detail_iva->debe == 0){
                $detail_iva->delete();
            }else{
                $detail_iva->save();
            }
        }

        if($difference_cuentas_por_pagar != 0){
            $movement_cuentas_por_pagar = $this->checkMovementCuentaPorPagar($expense_detail);

            $detail_cuentas_por_pagar = DetailVoucher::on(Auth::user()->database_name)->findOrFail($movement_cuentas_por_pagar->id);
            
            $detail_cuentas_por_pagar->haber += $difference_cuentas_por_pagar;

            if($detail_cuentas_por_pagar->haber == 0){
                $detail_cuentas_por_pagar->delete();
            }else{
                $detail_cuentas_por_pagar->save();
            }
            $this->updateAmountExpense($expense_detail,$amount*-1,$amount_iva_debe_new);
        }
    }

    public function checkMovement($expense_detail,$amount){

        $movement = DB::connection(Auth::user()->database_name)->table('detail_vouchers')
                    ->where('id_expense',$expense_detail->id_expense)
                    ->where('debe',$amount)
                    ->first();

        return $movement;
    }

    public function checkMovementIva($expense_detail){
       
        $movement = DB::connection(Auth::user()->database_name)->table('detail_vouchers')
        ->where('id_expense',$expense_detail->id_expense)
        ->where('id_account',70)
        ->first();

        return $movement;
    }

    public function checkMovementCuentaPorPagar($expense_detail){
       
        $movement = DB::connection(Auth::user()->database_name)->table('detail_vouchers')
        ->where('id_expense',$expense_detail->id_expense)
        ->where('id_account',174)
        ->first();

        return $movement;
    }

    public function updateAmountExpense($expense_detail,$amount,$amount_iva){

        $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->findOrFail($expense_detail->id_expense);

        if(isset($expense)){
            if($expense_detail->exento == false){
                $expense->base_imponible += $amount;
            }
            $expense->amount += $amount;
            $expense->amount_iva = $amount_iva;
            $expense->amount_with_iva = $expense->amount + $expense->amount_iva;
    
            $expense->save();
        }
    }


    public function validateChangeExento($expense_detail,$new_exento){
        
       
        if($expense_detail->exento != $new_exento){
           
            if($expense_detail->expenses['status'] == 'P'){
                $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->findOrFail($expense_detail->id_expense);
    
                if($new_exento == 1){
                    $expense->base_imponible -= $expense_detail->price * $expense_detail->amount;
                }else{
                    $expense->base_imponible += $expense_detail->price * $expense_detail->amount;
                }
                $expense->save();
                return;
            }
        }
        return;
    }

    public function validateDeleteExento($expense_detail){

        if($expense_detail->exento == 0){
            $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->findOrFail($expense_detail->id_expense);
        
            $expense->base_imponible -= $expense_detail->price * $expense_detail->amount;
                
            $expense->save();
        }
    }
}
