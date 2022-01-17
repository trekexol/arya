<?php

namespace App\Http\Controllers;

use App\Client;
use App\Http\Controllers\UserAccess\UserAccessController;
use App\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
 
    public $userAccess;
    public $modulo = 'Cotizacion';

    public function __construct(){

        $this->middleware('auth');
        $this->userAccess = new UserAccessController();
    }

   public function index()
   {
        if($this->userAccess->validate_user_access($this->modulo)){
            $user= auth()->user();

            $clients = Client::on(Auth::user()->database_name)->orderBy('id' ,'DESC')->get();
            
            return view('admin.clients.index',compact('clients'));
        }else{
            return redirect('/home')->withDanger('No tiene Acceso al modulo de '.$this->modulo);
        }
   }

   /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function create()
   {
        $vendors = Vendor::on(Auth::user()->database_name)->orderBy('name','asc')->get();
      
       return view('admin.clients.create',compact('vendors'));
   }

   /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
   public function store(Request $request)
    {
   
        $data = request()->validate([
            'type_code'         =>'required|max:20',
            'id_user'         =>'required',
            'name'         =>'required|max:80',
            'cedula_rif'         =>'required|max:20',
            'direction'         =>'required|max:100',
    
            'city'         =>'required|max:20',
            'country'         =>'required|max:20',
            'phone1'         =>'required|max:20',
            'days_credit'         =>'required|integer',
            
    
           
        ]);

    $users = new client();
    $users->setConnection(Auth::user()->database_name);

    $users->id_vendor = request('id_vendor');
    $users->id_user = request('id_user');
    $users->type_code = request('type_code');
   
    $users->name = request('name');
    //$users->name = request('name_ref');
    $users->cedula_rif = request('cedula_rif');
    $users->direction = request('direction');
    $users->city = request('city');
    $users->country = request('country');
    $users->phone1 = request('phone1');
    $users->phone2 = request('phone2');
    $users->email = request('email');
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
    }

   /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function show($id)
   {
       //
   }

   /**
    * Show the form for editing the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function edit($id)
   {
        $var = client::on(Auth::user()->database_name)->find($id);
        
        $vendors = Vendor::on(Auth::user()->database_name)->orderBy('name','asc')->get();

        return view('admin.clients.edit',compact('var','vendors'));
  
   }

   /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, $id)
   {

    $vars =  client::on(Auth::user()->database_name)->find($id);
    $vars_status = $vars->status;
   
    $data = request()->validate([
        'type_code'         =>'required|max:20',
        
        'razon_social'         =>'required|max:80',
        'namecomercial'         =>'required|max:100',
        'cedula_rif'         =>'required|max:20',
        'direction'         =>'required|max:100',

        'city'         =>'required|max:20',
        'country'         =>'required|max:20',
        'phone1'         =>'required|max:20',
        

        
        'days_credit'         =>'required|integer',
        

       
    ]);
    

    $users = client::on(Auth::user()->database_name)->findOrFail($id);
    
    $users->id_vendor = request('id_vendor');

    $users->type_code = request('type_code');
   
    $users->name = request('razon_social');
    $users->name_ref = request('namecomercial');

    $users->cedula_rif = request('cedula_rif');
    $users->direction = request('direction');
    $users->city = request('city');
    $users->country = request('country');
    $users->phone1 = request('phone1');
    $users->phone2 = request('phone2');
    $users->email = request('email');
    $users->personcontact = request('personcontact');   
    $users->days_credit = request('days_credit');

    $sin_formato_amount_max_credit = str_replace(',', '.', str_replace('.', '', request('amount_max_credit')));


    $users->amount_max_credit = $sin_formato_amount_max_credit;
    
    $users->percentage_retencion_iva = request('retencion_iva');
    $users->percentage_retencion_islr = request('retencion_islr');

    if(request('status') == null){
        $users->status = $vars_status;
    }else{
        $users->status = request('status');
    }
   
    $users->save();

    return redirect('/clients')->withSuccess('Actualizacion Exitosa!');
    }


   /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function destroy($id)
   {
       //
   }
}
