<?php

namespace App\Http\Controllers\Validations;

use App\DetailVoucher;
use App\ExpensesAndPurchase;
use App\ExpensesDetail;
use App\HeaderVoucher;
use App\Http\Controllers\Controller;
use App\Permission\Models\Account;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseDetailValidationController extends Controller
{


    public function calculateExpenseModify($id_expense){

        if(isset($id_expense)){
            $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->findOrFail($id_expense);

            $this->deleteExpenseMovements($expense);
            
            $this->updateExpenseTotal($expense);

            $this->updateExpenseMovements($expense);
            
        }else{
            return redirect('/expensesandpurchases')->withDanger('El Pago no existe');
        } 

    }

    public function updateExpenseTotal($expense){

        $expense_details = DB::connection(Auth::user()->database_name)->table('expenses_details')
                    ->where('id_expense',$expense->id)
                    ->get();

        $total= 0;
        $base_imponible= 0;
        
        $retiene_iva = 0;

        $total_retiene_iva = 0;
        $total_retiene_islr = 0;

        foreach($expense_details as $var)
        {
            $total += ($var->price * $var->amount);
            
            if($var->exento == 0){
                $base_imponible += ($var->price * $var->amount); 
            }

            if($var->islr == 1){
                $total_retiene_islr += ($var->price * $var->amount); 
            }
        }

        $expense->amount = $total;
        $expense->amount_iva = ($total * $expense->iva_percentage) /100;
        $expense->amount_with_iva =  $expense->amount + $expense->amount_iva;

        //$expense->retencion_islr = 
        $expense->save();

    }
    public function updateExpenseMovements($expense){

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d'); 

        $bcv = $expense->rate;

        $user       =   auth()->user();
        
        $header_voucher  = new HeaderVoucher();
        $header_voucher->setConnection(Auth::user()->database_name);


        $header_voucher->description = "Compras de Bienes o servicios.";
        $header_voucher->date = $date_payment ?? $datenow;
        
    
        $header_voucher->status =  "1";
    
        $header_voucher->save();
    
        $expense_details = ExpensesDetail::on(Auth::user()->database_name)->where('id_expense',$expense->id)->get();
        
        foreach($expense_details as $var){
            $account = Account::on(Auth::user()->database_name)->find($var->id_account);
            
            if(isset($account)){
                $this->add_movement($bcv,$header_voucher->id,$account->id,$expense->id,$user->id,$var->price * $var->amount,0);
            }
        }

        //Credito Fiscal IVA por Pagar

        $account_credito_iva_fiscal = Account::on(Auth::user()->database_name)->where('description', 'like', 'IVA (Credito Fiscal)')->first();
            
        if(isset($account_credito_iva_fiscal)){
            if($expense->amount_iva != 0){
                $this->add_movement($bcv,$header_voucher->id,$account_credito_iva_fiscal->id,$expense->id,$user->id,$expense->amount_iva,0);
            }
        }

        //Al final de agregar los movimientos de los pagos, agregamos el monto total de los pagos a cuentas por cobrar clientes
        $account_cuentas_por_pagar_proveedores = Account::on(Auth::user()->database_name)->where('description', 'like', 'Cuentas por Pagar Proveedores')->first(); 
        
        if(isset($account_cuentas_por_pagar_proveedores)){
            $this->add_movement($bcv,$header_voucher->id,$account_cuentas_por_pagar_proveedores->id,$expense->id,$user->id,0,$expense->amount_with_iva);
        }
    }

    public function deleteExpenseMovements($expense){
        DB::connection(Auth::user()->database_name)->table('detail_vouchers')
        ->join('header_vouchers','header_vouchers.id','detail_vouchers.id_header_voucher')
        ->where('id_expense',$expense->id)
        ->delete(['detail_vouchers','header_vouchers']);
    }




    public function updateMovement($expense_detail){

        
        $amount_old = $expense_detail->price_old * $expense_detail->amount_old;

        $amount_new = $expense_detail->price * $expense_detail->amount;
        
        $difference_cuentas_por_pagar = 0;
        $difference_amount = 0;

        if($expense_detail->exento == 0){
            
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

        if(empty($movement_iva)){
            $movement_iva = $this->add_movement($expense_detail);
        }
        if(empty($movement_iva)){
            $movement_iva = $this->add_movement($expense_detail);
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

        if($expense_detail->exento == 0){
            
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
        
        if(empty($movement_iva)){
            $movement_iva = $this->add_movement($expense_detail);
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
        ->join('accounts','accounts.id','detail_vouchers.id_account')
        ->where('detail_vouchers.id_expense',$expense_detail->id_expense)
        ->where('accounts.description','IVA (Credito Fiscal)')
        ->select('detail_vouchers.*')
        ->first();

        return $movement;
    }

    public function checkMovementCuentaPorPagar($expense_detail){
       
        $movement = DB::connection(Auth::user()->database_name)->table('detail_vouchers')
        ->join('accounts','accounts.id','detail_vouchers.id_account')
        ->where('id_expense',$expense_detail->id_expense)
        ->where('accounts.description','Cuentas por Pagar Proveedores')
        ->select('detail_vouchers.*')
        ->first();

        return $movement;
    }

    public function updateAmountExpense($expense_detail,$amount,$amount_iva){

        $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->findOrFail($expense_detail->id_expense);

        if(isset($expense)){
            if($expense_detail->exento == false){
                $expense->base_imponible += $amount;
            }

            $expense->amount_iva = $amount_iva;

            if($amount != 0){
                $expense->amount += $amount;
                $expense->amount_with_iva = $expense->amount + $expense->amount_iva;
            }
            
            $expense->save();
        }
    }


    public function validateChangeExento($expense_detail,$new_exento){
        
       
        if($expense_detail->exento != $new_exento){
           
            if($expense_detail->expenses['status'] == 'P'){
                $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->findOrFail($expense_detail->id_expense);
    
                $amount = $expense_detail->price * $expense_detail->amount;

                $difference_cuentas_por_pagar = 0;

                if($new_exento == 1){
                    $expense->base_imponible -= $amount;

                }else{
                    $expense->base_imponible += $amount;
                }
               
                
                $movement_iva = $this->checkMovementIva($expense_detail);
                
                if($new_exento == 0){
                    if(empty($movement_iva)){
                        $movement_iva = $this->add_movement($expense_detail);
                    }
                }
               
                if(isset($movement_iva)){
          
                    $amount_iva_old = bcdiv(($amount * $expense_detail->expenses['iva_percentage']) / 100, '1', 2);
                  
                    $detail_iva = DetailVoucher::on(Auth::user()->database_name)->findOrFail($movement_iva->id);
        
                    if($detail_iva->debe == 0){
                        $amount_iva_debe_new = $amount_iva_old;
                    }else{
                        $amount_iva_debe_new = $detail_iva->debe - $amount_iva_old;
                    }
                            
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
        
                    $detail_cuentas_por_pagar->save();
        
                }
               
                if($new_exento == 1){
                    $expense->amount_iva += $difference_cuentas_por_pagar;
                    $expense->amount_with_iva += $difference_cuentas_por_pagar;

                }else{
                    $expense->amount_iva += $difference_cuentas_por_pagar;
                    $expense->amount_with_iva += $difference_cuentas_por_pagar;
                }
                

                $expense->save();
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

    public function add_movement($expense_detail){

        $check = $this->checkMovementIva($expense_detail);
        if(empty($check)){
            $detail = new DetailVoucher();
            $detail->setConnection(Auth::user()->database_name);
            $user       =   auth()->user();
    
            $account_iva = DB::connection(Auth::user()->database_name)->table('accounts')
                    ->where('accounts.description','IVA (Credito Fiscal)')
                    ->first();
            $detail_expense = DB::connection(Auth::user()->database_name)->table('detail_vouchers')
                    ->where('detail_vouchers.id_expense',$expense_detail->id_expense)
                    ->first();
    
            $detail->id_account = $account_iva->id;
            $detail->id_header_voucher = $detail_expense->id_header_voucher;
            $detail->user_id = $user->id;
            $detail->tasa = $expense_detail->rate;
            $detail->id_expense = $expense_detail->id_expense;
    
            $detail->debe = 0;
            $detail->haber = 0;
           
          
            $detail->status =  "C";
    
             /*Le cambiamos el status a la cuenta a M, para saber que tiene Movimientos en detailVoucher */
             
                $account = Account::on(Auth::user()->database_name)->findOrFail($detail->id_account);
    
                if($account->status != "M"){
                    $account->status = "M";
                    $account->save();
                }
             
        
            $detail->save();
    
        }
        
    }
}
