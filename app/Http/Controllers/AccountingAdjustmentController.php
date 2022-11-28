<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

use App;
use App\Account;
use App\Anticipo;
use App\Quotation;
use App\DetailVoucher;
use App\ExpensesAndPurchase;
use App\Http\Controllers\UserAccess\UserAccessController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AccountingAdjustmentController extends Controller
{

    public function __construct(){

        $this->middleware('auth');

        $this->middleware('valiuser')->only('create');
        $this->middleware('valimodulo:Ajustes Contables');

    }
 
    public function index($coin = null)
    {
         
            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');
            if(empty($coin)){
                $coin = "bolivares";
            }


           
                            $detailvouchers = DetailVoucher::on(Auth::user()->database_name)
                            ->join('header_vouchers', 'header_vouchers.id', '=', 'detail_vouchers.id_header_voucher')
                            ->join('accounts', 'accounts.id', '=', 'detail_vouchers.id_account')
                            ->where('header_vouchers.date', $datenow)
                            ->select('detail_vouchers.*','header_vouchers.date as date','header_vouchers.description as description','header_vouchers.id_anticipo as id_anticipo','accounts.description as account_description')
                            ->orderBy('header_vouchers.date','desc')
                            ->orderBy('detail_vouchers.id','desc')
                            ->get();
                            
                            
                            if (!empty($detailvouchers)) {
                                foreach ($detailvouchers as $var) {
                                   
                                    $anticipos = Anticipo::on(Auth::user()->database_name)->find($var->id_anticipo);
                                    
                                    if (isset($anticipos)) {
                                        $quotation_anticipo = Quotation::on(Auth::user()->database_name)->find($anticipos->id_quotation);
                                        $expense_anticipo = ExpensesAndPurchase::on(Auth::user()->database_name)->find($anticipos->id_expense);
                                        
                
                                        if (isset($quotation_anticipo)) {
                
                                            if (isset($quotation_anticipo->date_billing)) {
                                            $var->id_anticipo .= ' (Fact: '.$quotation_anticipo->number_invoice.') Fecha Anticipo: '.date_format(date_create($anticipos->date),"d-m-Y");
                                            } else {
                                            $var->id_anticipo .= ' (NE: '.$quotation_anticipo->number_delivery_note.') Fecha Anticipo: '.date_format(date_create($anticipos->date),"d-m-Y");   
                                            }
                
                                        }
                
                                                            
                                        if (isset($expense_anticipo)) {
                                            $var->id_anticipo .= ' Compra Factura: '.$expense_anticipo->invoice.' Fecha Anticipo: '.date_format(date_create($anticipos->date),"d-m-Y");
                                        }
                
                                    } 
                                    if(isset($var->id_invoice)){
                                        $quotation = Quotation::on(Auth::user()->database_name)->find($var->id_invoice);
                                         
                                        if (isset($quotation_anticipo->date_billing)){
                                         $var->id_invoice = 'Fact: ('.$quotation->number_invoice.')';
                                        } else{
                                         $var->id_invoice = 'NE: ('.$quotation->number_delivery_note.')';
                                        }
            
                                   }
                                    if(isset($var->id_expense)){
                                        $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->find($var->id_expense);
                                    
                                        if (isset($expense->id)){
                                        $var->id_expense = $expense->invoice;

                                        }

                                    }
                                

                                }
                
                            }                     

            $accounts = Account::on(Auth::user()->database_name)->select('id','description')->where('code_one','<>',0)
                            ->where('code_two','<>',0)
                            ->where('code_three','<>',0)
                            ->where('code_four','<>',0)
                            ->where('code_five', '<>',0)
                            ->get();
            


            return view('admin.accounting_adjustments.index',compact('detailvouchers','datenow','accounts','coin'));
            
        
    }

    public function store(Request $request)
    {
       
        $data = request()->validate([
            'date_begin'        =>'required',
            'date_end'          =>'required',
        ]);
        
        $date_begin = request('date_begin');
        $date_end = request('date_end');
        $coin = request('coin');
        $ver_ajuste = null;
        $ver_ajuste = request('switch');

        if(empty($coin)){
            $coin = "bolivares";
        }
        
        if(isset($ver_ajuste) && ($ver_ajuste == "on")){
            $detailvouchers =  DB::connection(Auth::user()->database_name)->table('detail_vouchers')
            ->join('header_vouchers', 'header_vouchers.id', '=', 'detail_vouchers.id_header_voucher')
            ->join('accounts', 'accounts.id', '=', 'detail_vouchers.id_account')
            ->whereBetween('header_vouchers.date', [$date_begin, $date_end])
            ->where('header_vouchers.description','LIKE','ajuste%')
            ->select('detail_vouchers.*','header_vouchers.*'
            ,'accounts.description as account_description')
            ->orderBy('detail_vouchers.id','desc')->get();
        }else{
            $detailvouchers =  DB::connection(Auth::user()->database_name)->table('detail_vouchers')
            ->join('header_vouchers', 'header_vouchers.id', '=', 'detail_vouchers.id_header_voucher')
            ->join('accounts', 'accounts.id', '=', 'detail_vouchers.id_account')
            ->whereBetween('header_vouchers.date', [$date_begin, $date_end])
            ->select('detail_vouchers.*','header_vouchers.*'
            ,'accounts.description as account_description')
            ->orderBy('detail_vouchers.id','desc')->get();

        }
       
        $accounts = Account::on(Auth::user()->database_name)->select('id','description')->where('code_one','<>',0)
                                ->where('code_two','<>',0)
                                ->where('code_three','<>',0)
                                ->where('code_four','<>',0)
                                ->where('code_five', '<>',0)
                                ->get();
                                
        return view('admin.accounting_adjustments.index',compact('detailvouchers','date_begin','date_end','accounts','coin','ver_ajuste'));
   
    }
}
