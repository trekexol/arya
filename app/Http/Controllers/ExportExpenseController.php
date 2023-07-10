<?php

namespace App\Http\Controllers;

use App\Company;
use App\ExpensesAndPurchase;
use App\ExpensesDetail;
use App\Exports\ExpensesExport;
use App\Exports\ExpensesExportFromView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ExportExpenseController extends Controller
{
    public function ivaTxt(Request $request) 
    {
        $date_begin = Carbon::parse(request('date_begin'))->format('Y-m-d');
        $date_end = Carbon::parse(request('date_end'))->format('Y-m-d');

        $content = "";

        $total_retiene_iva = 0;
        $date = Carbon::now();
        $company = Company::on(Auth::user()->database_name)->first();
        $expenses = ExpensesAndPurchase::on(Auth::user()->database_name)
                                        ->WhereNotNull('number_iva')
                                        ->whereRaw(
                                            "(DATE_FORMAT(date_payment, '%Y-%m-%d') >= ? AND DATE_FORMAT(date_payment, '%Y-%m-%d') <= ?)", 
                                            [$date_begin, $date_end])
                                        ->where('status','C')
                                        ->get();
        if(!empty($expenses)){

            $expense_amont=0;
            $expense_amont_iva =0;             
            $total_amont = 0;
            $cont = count($expenses);

            foreach ($expenses as  $expense) {
                $expense->date = Carbon::parse($expense->date);
                $total_retiene_iva = $this->calculatarTotalProductosSinIva($expense);
                
                if($expense->amount < 0 || $expense->amount == null || $expense->amount == ''){
                    $expense_amont = 0;  
                } else {
                    $expense_amont = $expense->amount;  
                }
                if($expense->amount_iva < 0 || $expense->amount_iva == null || $expense->amount_iva == ''){
                    $expense_amont_iva = 0; 
                } else {
                    $expense_amont_iva = $expense->amount_iva;  
                }  
                
                $total_amont = $expense_amont + $expense_amont_iva;
                
                $nueva_fecha = substr($expense->date_payment, 0, 4) . substr($expense->date_payment, 5, 2);

                $periodoynum = $nueva_fecha.''.str_pad($expense->number_iva, 8, "0", STR_PAD_LEFT);

                $content .= str_replace('-', '', $company->code_rif)."\t".$expense->date->format('Ym')."\t".$expense->date->format('Y-m-d')."\tC\t01\t".str_replace('-', '', $expense->providers['code_provider'])."\t".$expense->invoice."\t".str_replace('-', '', $expense->serie)."\t".bcdiv($total_amont,'1',2)."\t".bcdiv($expense->base_imponible,'1',2)."\t".bcdiv($expense->retencion_iva,'1',2)."\t0\t".$periodoynum."\t".bcdiv($total_retiene_iva,'1',2)."\t".bcdiv($expense->iva_percentage,'1',2)."\t0";
                
                if($cont > 0){ 
                $content .= "\n";
                }

                $cont++;
            }    
        }
       
        if (count($expenses) == 0){
            $date_begin2 = Carbon::parse(request('date_begin'))->format('Ym');

            $content .= str_replace('-', '', $company->code_rif)."\t".$date_begin2."\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0";

        }
        // file name to download
        $fileName = "retencion-de-iva-provedores.txt";
        // make a response, with the content, a 200 response code and the headers
        return Response::make($content, 200, [
            'Content-type' => 'text/plain', 
            'Content-Disposition' => sprintf('attachment; filename="%s"', $fileName),
            'Content-Length' => strlen($content)
        ]);
   }


   public function islrXml(Request $request) 
   {
        $date = request('date_begin');
       
        $date_new_begin = Carbon::parse($date)->startOfMonth()->format('Y-m-d');

        $date_new_end = Carbon::parse($date)->endOfMonth()->format('Y-m-d');

       
       // $total_retiene_iva = 0;
        //$date = Carbon::now();
        $company = Company::on(Auth::user()->database_name)->first();
        

        $expenses = ExpensesAndPurchase::on(Auth::user()->database_name)
                                        ->where('retencion_islr','<>',0)
                                        ->where('status','C')
                                        ->whereRaw(
                                            "(DATE_FORMAT(date_payment, '%Y-%m-%d') >= ? AND DATE_FORMAT(date_payment, '%Y-%m-%d') <= ?)", 
                                            [$date_new_begin, $date_new_end])
                                        ->get();


        $content = '<?xml version="1.0" encoding="UTF-8"?>
        <RelacionRetencionesISLR RifAgente="'.str_replace("-","",$company->code_rif).'" Periodo="'.date('Ym',strtotime($date)).'">';
                                
                            
        if(isset($expenses)){
            foreach ($expenses as  $expense) {
                  $expense->date = Carbon::parse($expense->date);
               // $total_retiene_iva = $this->calculatarTotalProductosSinIva($expense);
                
                $content .= '<DetalleRetencion>
                  <RifRetenido>'.str_replace("-","",$expense->providers['code_provider']).'</RifRetenido>
                  <NumeroFactura>'.$expense->invoice.'</NumeroFactura>
                  <NumeroControl>'.str_replace('-', '', $expense->serie).'</NumeroControl>
                  <FechaOperacion>'.$expense->date->format('d/m/Y').'</FechaOperacion>
                  <CodigoConcepto>'.str_pad($expense->id_islr_concept, 3, "0", STR_PAD_LEFT).'</CodigoConcepto>
                  <MontoOperacion>'.bcdiv($expense->base_imponible,'1',2) .'</MontoOperacion>
                  <PorcentajeRetencion>'.$expense->islr_concepts['value'].'</PorcentajeRetencion>
                 </DetalleRetencion>';

            }   
            
            $content .= '</RelacionRetencionesISLR>';
        }else{
            $content = 'NO hay retenciones de ISLR para este periodo. Al declarar en el SENIAT solo seleccione la opción (No) cuando le pregunte por las Operaciones en el periodo y listo.';
        }
        
        // file name to download
        $fileName = "retencionislr.xml";

      
        // make a response, with the content, a 200 response code and the headers
        return Response::make($content, 200, [
        'Content-type' => 'text/xml', 
        'Content-Disposition' => sprintf('attachment; filename="%s"', $fileName),
        'Content-Length' => strlen($content)]);
   }

   public function ivaExcel(Request $request) 
   {
        $date_begin = Carbon::parse(request('date_begin'));
        $date_end = Carbon::parse(request('date_end'));

        
        $export = new ExpensesExportFromView($date_begin,$date_end);

        $export->view();       
        
        return Excel::download($export, 'plantilla_compras.xlsx');
   }


   public function calculatarTotalProductosSinIva($expense)
   {
        $request =  ExpensesDetail::on(Auth::user()->database_name)
                        ->where('id_expense',$expense->id)
                        ->where('exento','1')
                        ->select(DB::raw('SUM(price*amount) As total'))
                        ->first();

        return bcdiv($request->total, '1', 2);
   }

   
}
