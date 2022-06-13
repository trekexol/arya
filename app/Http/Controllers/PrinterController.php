<?php

namespace App\Http\Controllers;

use App\Client;
use App\Branch;
use App\Http\Controllers\UserAccess\UserAccessController;
use App\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

//use App\Http\Controllers\Mike42\Escpos\Printer;
//use App\Http\Controllers\Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

use App\Mike42\Escpos\Printer;
use App\Mike42\Escpos\PrintConnectors\WindowsPrintConnector;


class PrinterController extends Controller
{


    public function index(){

        return view('admin.printer.index');
   
    }

    public function printer(){
        try {
            // Enter the share name for your USB printer here
        // $connector = null;
            $connector = new WindowsPrintConnector("XP-58");
        
        // Print a "Hello world" receipt"
            $printer = new Printer($connector);
            $printer -> text("Hello World!\n");

            //$printer -> cut();
            
           // Close printer 
            $printer -> close();
        } catch (Exception $e) {
            echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
        }

   }



   /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
  /* public function store(Request $request)
    {
   
        $data = request()->validate([
            'type_code'         =>'required|max:20',
            'id_user'         =>'required',
            'direction'         =>'required|max:300',
            'city'         =>'required',
            'country'         =>'required',
            'phone1'         =>'required',
            'days_credit'         =>'required|integer',
           
        ]);

    $users = new client();
    $users->setConnection(Auth::user()->database_name);

    $users->id_vendor = request('id_vendor');
    $users->id_user = request('id_user');
    $users->type_code = request('type_code');
   
    $users->name = request('name');
    $users->name_ref = request('namecomercial');
    $users->cedula_rif = request('cedula_rif');
    $users->direction = request('direction');
    $users->city = request('city');
    $users->country = request('country');
    $users->phone1 = request('phone1');
    $users->phone2 = request('phone2');
    $users->email = request('email');
    $users->aliquot = request('aliquot');
    $users->id_cost_center = request('id_cost_center');
    $users->personcontact = request('personcontact');
    
    $users->days_credit = request('days_credit');

    if(request('amount_max_credit') != null){
        $sin_formato_amount_max_credit = str_replace(',', '.', str_replace('.', '', request('amount_max_credit')));
    }
    if(request('percentage_retencion_iva') != null){
        $sin_formato_percentage_retencion_iva = str_replace(',', '.', str_replace('.', '', request('percentage_retencion_iva')));
    }
    if(request('percentage_retencion_islr') != null){
        $sin_formato_percentage_retencion_islr = str_replace(',', '.', str_replace('.', '', request('percentage_retencion_islr')));
    }

    $users->amount_max_credit = $sin_formato_amount_max_credit ?? 0;
    
    $users->percentage_retencion_iva = $sin_formato_percentage_retencion_iva ?? 0;
    $users->percentage_retencion_islr = $sin_formato_percentage_retencion_islr ?? 0;
   
    $users->status =  1;
   
    $users->save();

    return redirect('/clients')->withSuccess('Registro Exitoso!');
    }*/

   
}
