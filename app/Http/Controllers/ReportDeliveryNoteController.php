<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;


use App;
use App\Client;
use App\Company;
use App\Http\Controllers\UserAccess\UserAccessController;
use App\Quotation;
use App\Vendor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReportDeliveryNoteController extends Controller
{
   
    public $modulo = "Reportes";
   
    //public function index_accounts_receivable_note($typepersone,$id_client_or_vendor)
    public function index_accounts_receivable_note($typepersone = 'todo',$id_client_or_vendor = 'todo',$date_end = 'todo',$date_frist = 'todo',$typeinvoice = 'todo')
    {        
       
        

            $global = new GlobalController();
       
            if($date_frist == 'todo'){
            $date_frist = $global->data_first_month_day();
            }
    
           if($date_end == 'todo'){
            $date_end =  $global->data_last_month_day();
           } 
              
   
        $userAccess = new UserAccessController();

        if($userAccess->validate_user_access($this->modulo)){
            $date = Carbon::now();   
            $client = null; 
            $vendor = null; 
            
                if($typepersone == 'cliente'){
                    
                    $client    = Client::on(Auth::user()->database_name)->find($id_client_or_vendor);
                    $id_client_or_vendor = $client->id;
                }
                
                if ($typepersone == 'vendor'){
                
                        $vendor    = Vendor::on(Auth::user()->database_name)->find($id_client_or_vendor);
                        $id_client_or_vendor = $vendor->id;
                }
                
                if ($typepersone == 'todo'){ 
                    
                    $client = null;    
                    $vendor = null;
                    $id_client_or_vendor = 'todo';
               }
              


            return view('admin.reports.index_accounts_receivable_note',compact('typepersone','client','vendor','date_end','date_frist','typeinvoice','id_client_or_vendor'));
            
        } else{
          
            return redirect('/home')->withDanger('No tiene Acceso al modulo de '.$this->modulo);
        
        } 

        //return view('admin.reports.index_accounts_receivable_note',compact('typepersone','id_client_or_vendor')); 
    }

    


    public function store_accounts_receivable_note(Request $request)
    {
      

        $date_end = request('date_end');
        $date_frist = request('date_begin');
        $typepersone = request('type');
        $id_client = request('id_client');
        $id_vendor = request('id_vendor');
        $typeinvoice = request('typeinvoice');
        $coin = request('coin');
  
        

        if($typepersone == 'cliente'){
            if(isset($id_client)){
                $client    = Client::on(Auth::user()->database_name)->find($id_client);
                $id_client_or_vendor = $client->id;
            }
            $vendor = null;
        }

        if($typepersone == 'vendor'){    
            if(isset($id_vendor)){
                $vendor    = Vendor::on(Auth::user()->database_name)->find($id_vendor);
                $id_client_or_vendor = $vendor->id;
            }
            $client = null;
        }

        if($typepersone == 'todo' || $typepersone == '' || $typepersone == null){
            $typepersone = 'todo';
            $id_client_or_vendor = 'todo';
            $client = null;
            $vendor = null;            
        }

        return view('admin.reports.index_accounts_receivable_note',compact('coin','typeinvoice','date_end','client','vendor','typepersone','date_frist','id_client_or_vendor'));
    }

   
    function accounts_receivable_note_pdf($coin,$date_end,$typeinvoice,$typepersone = 'todo',$id_client_or_vendor = 'todo',$date_frist = '0001-01-01')
    {
       // dd('Moneda: '.$coin.' Hasta: '.$date_end.' ID-Cliente-Vend: '.$id_client_or_vendor.' Tipo: '.$typeinvoice.' Persona: '.$typepersone.' Fecha frist ');
    
        $pdf = App::make('dompdf.wrapper');
        $quotations = null;
        
        $date = Carbon::now();
       // $datenow = $date->format('d-m-Y'); 
        
        $global = new GlobalController();
        
       /* if (empty($date_frist) || $date_frist == null){
            $date_frist = $global->data_first_month_day();   
        } */
        

        $date_consult = $date_end;
    
        $period = $date->format('Y'); 
         

        
        $pdf = $pdf->loadView('admin.reports.accounts_receivable_note',compact('coin','quotations','date_end','date_frist','typepersone','id_client_or_vendor'));
        return $pdf->stream();
                 
    }


    public function select_client_note()
    {

        $clients    = Client::on(Auth::user()->database_name)->get();
        $typepersone = 'cliente';
        return view('admin.reports.selectclient_note',compact('typepersone','clients'));
    }

    
    public function select_vendor_note()
    {
        $vendors    = Vendor::on(Auth::user()->database_name)->get();
        $typepersone = 'vendor';
        return view('admin.reports.selectvendor_note',compact('typepersone','vendors'));
    }

    public function search_bcv()
    {
        /*Buscar el indice bcv*/
        $urlToGet ='http://www.bcv.org.ve/tasas-informativas-sistema-bancario';
        $pageDocument = @file_get_contents($urlToGet);
        preg_match_all('|<div class="col-sm-6 col-xs-6 centrado"><strong> (.*?) </strong> </div>|s', $pageDocument, $cap);

        if ($cap[0] == array()){ // VALIDAR Concidencia
            $titulo = '0,00';
        }else {
            $titulo = $cap[1][4];
        }

        $bcv_con_formato = $titulo;
        $bcv = str_replace(',', '.', str_replace('.', '',$bcv_con_formato));


        /*-------------------------- */
       return bcdiv($bcv, '1', 2);

    }

}
