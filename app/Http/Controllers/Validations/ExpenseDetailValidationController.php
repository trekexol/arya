<?php

namespace App\Http\Controllers\Validations;

use App\DetailVoucher;
use App\ExpensesAndPurchase;
use App\ExpensesDetail;
use App\HeaderVoucher;
use App\FacturasCour;
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

        $expense->base_imponible = $base_imponible;
        $expense->amount = $total;
        $expense->amount_iva = ($base_imponible * $expense->iva_percentage) /100;
        $expense->amount_with_iva =  $expense->amount + $expense->amount_iva;

        $expense->retencion_islr = ($total_retiene_islr * $expense->islr_concepts['value'])/100;
        $expense->retencion_iva = ($expense->amount_iva * $expense->providers['porc_retencion_iva']) / 100;
        $expense->save();

           /*********COURIERTOOL *********/
           $facour = FacturasCour::on(Auth::user()->database_name)
           ->where('id_expense',$expense->id)
           ->where('estatus',1)
           ->first();

           if($facour){

               $facour->monto = $total;
               $facour->save();
           }
            /*********COURIERTOOL *********/


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
            if($expense->amount_with_iva != 0){
                $this->add_movement($bcv,$header_voucher->id,$account_cuentas_por_pagar_proveedores->id,$expense->id,$user->id,0,$expense->amount_with_iva);
            }
        }
    }

    public function deleteExpenseMovements($expense){

        if(isset($expense)){
            $detail = DB::connection(Auth::user()->database_name)->table('detail_vouchers')
            ->where('id_expense',$expense->id)
            ->first();

            DB::connection(Auth::user()->database_name)->table('detail_vouchers')
            ->join('header_vouchers','header_vouchers.id','detail_vouchers.id_header_voucher')
            ->where('id_expense',$expense->id)
            ->delete();

            if(isset($detail)){
                DB::connection(Auth::user()->database_name)->table('header_vouchers')
                ->where('id',$detail->id_header_voucher)
                ->delete();
            }
        }
    }

    public function add_movement($bcv,$id_header,$id_account,$id_expense,$id_user,$debe,$haber)
    {

        $detail = new DetailVoucher();
        $detail->setConnection(Auth::user()->database_name);

        $detail->id_account = $id_account;
        $detail->id_header_voucher = $id_header;
        $detail->user_id = $id_user;
        $detail->tasa = $bcv;
        $detail->id_expense = $id_expense;

        $detail->debe = $debe;
        $detail->haber = $haber;


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
