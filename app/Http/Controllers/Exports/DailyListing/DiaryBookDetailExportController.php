<?php

namespace App\Http\Controllers\Exports\DailyListing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App;
use App\Account;
use App\Client;
use App\Quotation;
use App\Anticipo;
use App\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exports\DailyListing\DiaryBookDetailExportFromView;
use App\Http\Controllers\Calculations\AccountCalculationController;
use App\Http\Controllers\GlobalController;
use App\Provider;
use App\Vendor;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class DiaryBookDetailExportController extends Controller
{
    public function exportExcel(Request $request) 
    {
        
        $export = new DiaryBookDetailExportFromView($request);

        $export->setter($request);

        $export->view();       
        
        return Excel::download($export, 'Libro Diario Detalles.xlsx');
    }

    public function print_diary_book_detail(Request $request)
    {
        
        
        $id_account = request('id_account');
        $coin = request('coin');
        
        $date_begin = request('date_begin');
        $date_end = request('date_end');

        $date = Carbon::now();
        $datenow = $date->format('d-m-Y');

        $pdf = App::make('dompdf.wrapper');

        $company = Company::on(Auth::user()->database_name)->find(1);

        if($coin != "bolivares")
        {
            $detailvouchers =  DB::connection(Auth::user()->database_name)->table('detail_vouchers')
            ->join('header_vouchers', 'header_vouchers.id', '=', 'detail_vouchers.id_header_voucher')
            ->join('accounts', 'accounts.id', '=', 'detail_vouchers.id_account')
            ->whereBetween('header_vouchers.date', [$date_begin, $date_end])
            ->whereIn('header_vouchers.id', function($query) use ($id_account){
                $query->select('id_header_voucher')
                ->from('detail_vouchers')
                ->where('id_account',$id_account);
            })
            ->whereIn('detail_vouchers.status', ['F','C'])
            ->select('detail_vouchers.*','header_vouchers.*'
            ,'accounts.description as account_description'
            ,'header_vouchers.id as id_header'
            ,'header_vouchers.description as header_description')
            ->orderBy('header_vouchers.date','asc')
            ->orderBy('header_vouchers.id','asc')->get();

            $total_debe = DB::connection(Auth::user()->database_name)->table('accounts')
                        ->join('detail_vouchers', 'detail_vouchers.id_account', '=', 'accounts.id')
                        ->join('header_vouchers', 'header_vouchers.id', '=', 'detail_vouchers.id_header_voucher')
                        ->where('detail_vouchers.id_account',$id_account)
                        ->whereIn('detail_vouchers.status', ['F','C'])
                        ->where('header_vouchers.date','<' ,$date_begin)
                        ->select(DB::connection(Auth::user()->database_name)->raw('SUM(detail_vouchers.debe/detail_vouchers.tasa) as debe'))->first();

           
           
            if(isset($total_debe->debe)){
                $detailvouchers_saldo_debe = $total_debe->debe;
            }else{
                $detailvouchers_saldo_debe = 0;
            }
           
                
            $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
                FROM accounts a
                INNER JOIN detail_vouchers d 
                    ON d.id_account = a.id
                INNER JOIN header_vouchers h 
                    ON h.id = d.id_header_voucher
                WHERE h.date < ? AND
                a.id = ? AND
                (d.status = ? OR
                d.status = ?)'
                , [$date_begin,$id_account,'C','F']);
                
                if(isset($total_haber[0]->haber)){
                    $detailvouchers_saldo_haber = $total_haber[0]->haber;
                }else{
                    $detailvouchers_saldo_haber = 0;
                }
     
            
        }else{ // dolares-----------------------------------------------
            

 
            $detailvouchers =  DB::connection(Auth::user()->database_name)->table('detail_vouchers')
            ->join('header_vouchers', 'header_vouchers.id', '=', 'detail_vouchers.id_header_voucher')
            ->join('accounts', 'accounts.id', '=', 'detail_vouchers.id_account')
            ->whereRaw(
                "(DATE_FORMAT(header_vouchers.date, '%Y-%m-%d') >= ? AND DATE_FORMAT(header_vouchers.date, '%Y-%m-%d') <= ?)",  
               [$date_begin, $date_end])
            ->whereIn('header_vouchers.id', function($query) use ($id_account){
                $query->select('id_header_voucher')
                ->from('detail_vouchers')
                ->where('id_account',$id_account);
            })
            ->whereIn('detail_vouchers.status', ['F','C'])
            ->select('detail_vouchers.*','header_vouchers.*'
            ,'accounts.description as account_description'
            ,'header_vouchers.id as id_header'
            ,'header_vouchers.description as header_description')
            ->orderBy('header_vouchers.date','asc')
            ->orderBy('header_vouchers.id','asc')->get();

           
            //busca los saldos previos de la cuenta                    
            $detailvouchers_saldo_debe =  DB::connection(Auth::user()->database_name)->table('detail_vouchers')
                        ->join('header_vouchers', 'header_vouchers.id', '=', 'detail_vouchers.id_header_voucher')
                        ->join('accounts', 'accounts.id', '=', 'detail_vouchers.id_account')
                        ->where('header_vouchers.date','<' ,$date_begin)
                        ->where('accounts.id',$id_account)
                        ->whereIn('detail_vouchers.status', ['F','C'])
                        ->sum('detail_vouchers.debe');

            
            $detailvouchers_saldo_haber =  DB::connection(Auth::user()->database_name)->table('detail_vouchers')
                        ->join('header_vouchers', 'header_vouchers.id', '=', 'detail_vouchers.id_header_voucher')
                        ->join('accounts', 'accounts.id', '=', 'detail_vouchers.id_account')
                        ->where('header_vouchers.date','<' ,$date_begin)
                        ->where('accounts.id',$id_account)
                        ->whereIn('detail_vouchers.status', ['F','C'])
                        ->sum('detail_vouchers.haber');       
        }

        
        $date_begin = Carbon::parse($date_begin)->format('d-m-Y');

        $date_end = Carbon::parse($date_end)->format('d-m-Y');

        $account = Account::on(Auth::user()->database_name)->find($id_account);

        $account_calculate = new AccountCalculationController();

        $account_historial = $account_calculate->calculateBalance($account,$date_begin);

        
        if($coin !="bolivares"){

            if(empty($account_historial->rate) || ($account_historial->rate == 0)){
                $account_historial->rate = 1;
            }
            $account_historial->balance_previous = $account_historial->balance_previous / $account_historial->rate;
        }

        $saldo_anterior = ($account_historial->balance_previous ?? 0) + ($detailvouchers_saldo_debe ?? 0) - ($detailvouchers_saldo_haber ?? 0);
        $primer_movimiento = true;
        $saldo = 0;
        $counterpart = "";


        foreach($detailvouchers as $detail){
            
            //$detailvouchers->account_counterpart = '';

            
            

            $quotation = Quotation::on(Auth::user()->database_name) // buscar factura
            ->where('id','=',$detail->id_invoice)
            ->where('date_billing','!=',null)
            ->get()->first();     
        

            $anticipo = Anticipo::on(Auth::user()->database_name) // buscar anticipo
                ->where('id','=',$detail->id_anticipo)
                ->get()->first();  
                

   


          

            if (isset($quotation)) {

                $detail->header_description .= ' FAC: '.$quotation->number_invoice;
                $client = Client::on(Auth::user()->database_name) // buscar factura
                ->where('id','=',$quotation->id_client)
                ->get()->first();
                
                $detail->header_description .= '. '.$client->name.'. '.$quotation->coin;
                

            } else {


                if (isset($anticipo)) {
                    $id_client = '';
                    $coin_mov = '';
                   if ($anticipo->id_quotation != null){
                        
   
                        $quotation = Quotation::on(Auth::user()->database_name) // buscar factura
                        ->where('id','=',$anticipo->id_quotation)
                        ->where('date_billing','!=',null)
                        ->get()->first();

                        $quotation_delivery = Quotation::on(Auth::user()->database_name) // buscar Nota de entrega
                        ->where('id','=',$anticipo->id_quotation)
                        ->where('date_billing','=',null)
                        ->where('number_invoice','=',null)
                        ->get()->first();
                        

                        
                        if (isset($quotation)) {
                        $detail->header_description .= ' FAC: '.$quotation->number_invoice;
                        $id_client = $quotation->id_client;
                        $coin_mov = $quotation->coin;
                        }
                        if (isset($quotation_delivery)) {
                        $detail->header_description .= ' NE: '.$quotation_delivery->number_delivery_note;
                        $id_client = $quotation_delivery->id_client;
                        $coin_mov = $quotation_delivery->coin;    
                        }
                        
                        if(isset($id_client)) {
                            $client = Client::on(Auth::user()->database_name) // buscar factura
                            ->where('id','=',$id_client)
                            ->get()->first();
                            
                            if(!empty($client)) {
                            $detail->header_description .= $client->name;
                            }
                       }


                        $detail->header_description .= '. '.$coin_mov;
                        
                        

                   } else {




                        if (isset($anticipo->id_client)) {
                                                    
                            $client = Client::on(Auth::user()->database_name) // buscar factura
                            ->where('id','=',$anticipo->id_client)
                            ->get()->first();
                                 if (isset($client)) {
                                 $detail->header_description .= '. '.$client->name;
                                 }
                        }

                        if (isset($anticipo->id_provider)) {
                        
                            $proveedor = Provider::on(Auth::user()->database_name) // buscar factura
                            ->where('id','=',$anticipo->id_provider)
                            ->get()->first();
                                 if (isset($proveedor)) {
                                 $detail->header_description .= '. '.$proveedor->razon_social;
                                 }
                        } 

                        $detail->header_description .= '. '.$anticipo->coin;
                   }
                    

                }

            }
            

                if($coin != "bolivares"){
                    
                    if((isset($detail->debe)) && ($detail->debe != 0)){
                    $detail->debe = $detail->debe / ($detail->tasa ?? 1);
                    }

                    if((isset($detail->haber)) && ($detail->haber != 0)){
                    $detail->haber = $detail->haber / ($detail->tasa ?? 1);
                    }

                }


                if($detail->id_account == $id_account){
                    /*esta parte convierte los saldos a dolares */
                    /*if($coin != "bolivares"){
                        
                        if((isset($detail->debe)) && ($detail->debe != 0)){
                        $detail->debe = $detail->debe / ($detail->tasa ?? 1);
                        }
    
                        if((isset($detail->haber)) && ($detail->haber != 0)){
                        $detail->haber = $detail->haber / ($detail->tasa ?? 1);
                        }
                    } */
                    /*----------------------------- */
                    if($primer_movimiento){
                        
                        $detail->saldo = $detail->debe - $detail->haber + $saldo_anterior;
                     
                        $saldo += $detail->saldo;
    
                        $primer_movimiento = false;

                    }else{
    
                        $detail->saldo = $detail->debe - $detail->haber + $saldo;                 
                    
                        $saldo = $detail->saldo;   
                    }
                    
                   /* if($counterpart == ""){
                        $last_detail = $detail;
                    }else{
                        $detail->account_counterpart = $counterpart;
                    }*/
                  
                    $detail->account_counterpart = '';
    
                }else{
                   /*if(isset($last_detail)){
                        $last_detail->account_counterpart = $detail->account_description;
                       
                    }else{
                        $counterpart = $detail->account_description;
                    }*/
    
                    
                   // $account = Account::on(Auth::user()->database_name)->find($detail->id_account);
                    
    
                   $detail->account_counterpart = '';
     
                }

        }

        //voltea los movimientos para mostrarlos del mas actual al mas antiguo
        $detailvouchers = array_reverse($detailvouchers->toArray());

        
        return view('export_excel.daily_listing.diary_book_detail',compact('coin','company','detailvouchers'
                                ,'datenow','date_begin','date_end','account'
                                ,'detailvouchers_saldo_debe','detailvouchers_saldo_haber','saldo','id_account'));
    }
   
}
