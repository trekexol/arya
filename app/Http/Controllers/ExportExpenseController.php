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
                                        ->where('retencion_iva','<>',0)
                                        ->whereRaw(
                                            "(DATE_FORMAT(date, '%Y-%m-%d') >= ? AND DATE_FORMAT(date, '%Y-%m-%d') <= ?)", 
                                            [$date_begin, $date_end])
                                        ->where('status','C')
                                        ->get();
        if(isset($expenses)){

            $expense_amont=0;
            $expense_amont_iva =0;             
            $total_amont = 0;
            $cont = 0;

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
                  

                $content .= str_replace('-', '', $company->code_rif).'  '.$expense->date->format('Ym').'    '.$expense->date->format('Y-m-d').' C   01  '.str_replace('-', '', $expense->providers['code_provider']).'  '.$expense->invoice.'   '.$expense->serie.' '.bcdiv($total_amont,'1',2).'   '.bcdiv($expense->base_imponible,'1',2).'   '.bcdiv($expense->retencion_iva,'1',2).'    0   '.$expense->date->format('Ym').str_pad($expense->id, 8, "0", STR_PAD_LEFT).'    '.bcdiv($total_retiene_iva,'1',2).' '.bcdiv($expense->iva_percentage,'1',2).'   0';
                
                if($cont > 0){ 
                $content .= "\n";
                }

                $cont++;
            }    
        }else{
 
           
            $content = str_replace('-', '', $company->code_rif).'   '.$expense->date->format('Ym').'    0   0   0   0   0   0   0   0   0   0   0   0   0   0';

        }
        
        // file name to download
        $fileName = "retencion-de-iva-provedores.txt";

      
        // make a response, with the content, a 200 response code and the headers
        return Response::make($content, 200, [
        'Content-type' => 'text/plain', 
        'Content-Disposition' => sprintf('attachment; filename="%s"', $fileName),
        'Content-Length' => strlen($content)]);
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
                                            "(DATE_FORMAT(date, '%Y-%m-%d') >= ? AND DATE_FORMAT(date, '%Y-%m-%d') <= ?)", 
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
