<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;


use App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Company;
use App\DetailVoucher;
use App\Multipayment;
use App\Quotation;
use App\QuotationPayment;

class PaymentController extends Controller
{

    public function __construct(){

        $this->middleware('auth');
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Cobros');
       }


    public function index(Request $request)
       {
    
        $agregarmiddleware = $request->get('agregarmiddleware');
        $actualizarmiddleware = $request->get('actualizarmiddleware');
        $eliminarmiddleware = $request->get('eliminarmiddleware');
        $payment_quotations = null;
        
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');    
        $datebeginyear = $date->firstOfYear()->format('Y-m-d');
        
        $payment_quotations = QuotationPayment::on(Auth::user()->database_name)
                                ->join('quotations','quotations.id','quotation_payments.id_quotation')
                                ->join('clients','clients.id','quotations.id_client')
                                ->join('accounts','accounts.id','quotation_payments.id_account')
                                ->whereRaw(
                                    "(DATE_FORMAT(quotation_payments.created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotation_payments.created_at, '%Y-%m-%d') <= ?)", 
                                    [$datebeginyear, $datenow])
                                ->where('quotation_payments.status','1')
                                ->select('quotation_payments.*','quotations.number_invoice as number_invoice',
                                        'clients.name as name_client','accounts.description as description_account')
                                ->orderBy('quotation_payments.created_at','desc')
                                ->get();

        foreach($payment_quotations as $payment_quotation){

            $type = $this->asignar_payment_type($payment_quotation->payment_type);

            $payment_quotation->type = $type;
        }
            
        
        return view('admin.payments.index',compact('eliminarmiddleware','actualizarmiddleware','agregarmiddleware','datenow','payment_quotations'));
      
    }


    public function movements($id_invoice)
    {
        
        if(isset($id_invoice) && $id_invoice != -1){

            $user       =   auth()->user();
            $users_role =   $user->role_id;
            
                $quotation = Quotation::on(Auth::user()->database_name)->find($id_invoice);
                $detailvouchers = DetailVoucher::on(Auth::user()->database_name)
                                                ->join('header_vouchers','header_vouchers.id','detail_vouchers.id_header_voucher')
                                                ->where('id_invoice',$id_invoice)
                                                ->where('header_vouchers.description','LIKE','Cobro%')
                                                ->where('detail_vouchers.status','C')
                                                ->get();

                $multipayments_detail = null;
                $invoices = null;
                $coin = $quotation->coin;
                $return = "payments";

                //Buscamos a la factura para luego buscar atraves del header a la otras facturas
                $multipayment = Multipayment::on(Auth::user()->database_name)->where('id_quotation',$id_invoice)->first();
                if(isset($multipayment)){
                $invoices = Multipayment::on(Auth::user()->database_name)->where('id_header',$multipayment->id_header)->get();
                $multipayments_detail = DetailVoucher::on(Auth::user()->database_name)->where('id_header_voucher',$multipayment->id_header)->get();
                }

                if(!isset($coin)){
                    $coin = 'bolivares';
                }
            
            
            return view('admin.invoices.index_detail_movement',compact('return','detailvouchers','quotation','coin','invoices','multipayments_detail'));
        }else{
            return redirect('payments/index')->withDanger('No se encontro el movimiento de este pago!');
        }
    
    }


    public function pdf($id_payment,$coin)
    {
       
        $pdf = App::make('dompdf.wrapper');

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');    

        $payment = QuotationPayment::on(Auth::user()->database_name)->find($id_payment);
       
        $movements = Quotation::on(Auth::user()->database_name)
            ->join('detail_vouchers', 'detail_vouchers.id_invoice', '=', 'quotations.id')
            ->join('header_vouchers','header_vouchers.id','detail_vouchers.id_header_voucher')
            ->join('accounts','accounts.id','detail_vouchers.id_account')
            ->join('clients','clients.id','quotations.id_client')
            ->where('quotations.id',$payment->id_quotation)
            ->where('header_vouchers.description','LIKE','Cobro%')
            ->where('detail_vouchers.status','C')
            ->select('header_vouchers.description', 'header_vouchers.id as header_id',
            'detail_vouchers.debe', 'detail_vouchers.haber', 'detail_vouchers.haber', 'detail_vouchers.tasa',
            'accounts.code_one','accounts.code_two','accounts.code_three','accounts.code_four','accounts.code_five','accounts.description as account_description',
            'clients.name as client_name','clients.cedula_rif as client_cedula_rif','clients.type_code as client_type_code',
            'quotations.id as quotation_id')
            ->get();

        

        
        $type = $this->asignar_payment_type($payment->payment_type);

        $payment->type = $type;
            
        $pdf = $pdf->loadView('admin.payments.pdf',compact('payment','coin','movements','datenow'));
        return $pdf->stream();
                 
    }


    public function deleteAllPayments(Request $request){

    if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){
        $id_quotation = request('id_quotation_modal');
        //dd($request);
        $quotation = Quotation::on(Auth::user()->database_name)->findOrFail($id_quotation);
        
        if($quotation->status != 'X'){
            
            DetailVoucher::on(Auth::user()->database_name)
                    ->join('header_vouchers','header_vouchers.id','detail_vouchers.id_header_voucher')
                    ->where('id_invoice',$id_quotation)
                    ->where('header_vouchers.description','LIKE','Cobro%')
                    ->update(['detail_vouchers.status' => 'X','header_vouchers.status' => 'X']);

                    
            QuotationPayment::on(Auth::user()->database_name)
                            ->where('id_quotation',$quotation->id)
                            ->update(['status' => 'X']);

            $quotation->status = 'P';
            $quotation->save();
        }
        
        
        return redirect('payments/index')->withSuccess('Reverso de Pagos Exitoso!');
    }else{
        return redirect('payments/index')->withDanger('No Tiene Permiso!');

    }
    }

    public function check_quotation_in_multipayment($id_quotation){

        Multipayment::on(Auth::user()->database_name)
                    ->where('id_quotation',$id_quotation)
                    ->first();
    }
  

    function asignar_payment_type($type){
      
        if($type == 1){
            return "Cheque";
        }
        if($type == 2){
            return "Contado";
        }
        if($type == 3){
            return "Contra Anticipo";
        }
        if($type == 4){
            return "Crédito";
        }
        if($type == 5){
            return "Depósito Bancario";
        }
        if($type == 6){
            return "Efectivo";
        }
        if($type == 7){
            return "Indeterminado";
        }
        if($type == 8){
            return "Tarjeta Coorporativa";
        }
        if($type == 9){
            return "Tarjeta de Crédito";
        }
        if($type == 10){
            return "Tarjeta de Débito";
        }
        if($type == 11){
            return "Transferencia";
        }
    }


   
}
