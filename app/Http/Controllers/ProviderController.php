<?php

namespace App\Http\Controllers;

use App\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProviderController extends Controller
{
 
    public function __construct(){

       $this->middleware('auth');
   }

   public function index()
   {
       $user= auth()->user();

       $providers = Provider::on(Auth::user()->database_name)->orderBy('id' ,'DESC')->get();
      
       return view('admin.providers.index',compact('providers'));
   }

   /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function create()
   {

      
       return view('admin.providers.create');
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
        'code_provider'         =>'required|max:20',
        'razon_social'         =>'required|max:80',
        'direction'         =>'required|max:100',

        'city'         =>'required|max:20',
        'country'         =>'required|max:20',
        'phone1'         =>'required|max:20',
        'phone2'         =>'required|max:20',

        
        'days_credit'           =>  'required|integer',
        'amount_max_credit'     =>  'required',
        'porc_retencion_iva'    =>  'numeric|min:0|max:100',
        'porc_retencion_islr'    => 'numeric|min:0|max:100',
        
        'balance'         =>'required',
        
       
       
    ]);
    
    $users = new Provider();
    $users->setConnection(Auth::user()->database_name);

    
    $users->code_provider = $request->type_code.request('code_provider');
    $users->razon_social = request('razon_social');
    $users->direction = request('direction');
    $users->city = request('city');
    $users->country = request('country');
    $users->phone1 = request('phone1');
    $users->phone2 = request('phone2');

    $has_credit = request('has_credit');
    if($has_credit == null){
        $users->has_credit = false;
    }else{
        $users->has_credit = true;
    }
   
    $users->days_credit = request('days_credit');

    $sin_formato_amount_max_credit = str_replace(',', '.', str_replace('.', '', request('amount_max_credit')));
    $sin_formato_balance = str_replace(',', '.', str_replace('.', '', request('balance')));
   
    
    $users->amount_max_credit = $sin_formato_amount_max_credit;
    $users->porc_retencion_iva = request('porc_retencion_iva');
    $users->porc_retencion_islr = request('porc_retencion_islr');
    
    $users->balance = $sin_formato_balance;

    $users->status =  1;
   
    $users->save();

    return redirect('/providers')->withSuccess('Registro Exitoso!');
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
        $var = Provider::on(Auth::user()->database_name)->find($id);
        

        return view('admin.providers.edit',compact('var'));
  
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

   
    $data = request()->validate([
        'code_provider'         =>'required|max:20',
        'razon_social'          =>'required|max:80',
        'direction'             =>'required|max:100',

        'city'                  =>'required|max:20',
        'country'               =>'required|max:20',
        'phone1'                =>'required|max:20',
        'phone2'                =>'required|max:20',

        
        'days_credit'           =>'required',
        'amount_max_credit'     =>'required',
        'porc_retencion_iva'    =>  'numeric|min:0|max:100',
        'porc_retencion_islr'   => 'numeric|min:0|max:100',
        
        'balance'               =>'required',
        
       
       
    ]);

    $users = Provider::on(Auth::user()->database_name)->findOrFail($id);

    
   
    $users->code_provider = $request->type_code.request('code_provider');
    $users->razon_social = request('razon_social');
    $users->direction = request('direction');
    $users->city = request('city');
    $users->country = request('country');
    $users->phone1 = request('phone1');
    $users->phone2 = request('phone2');

    $has_credit = request('has_credit');
    if($has_credit == null){
        $users->has_credit = false;
    }else{
        $users->has_credit = true;
    }
    
    $sin_formato_amount_max_credit = str_replace(',', '.', str_replace('.', '', request('amount_max_credit')));
    $sin_formato_balance = str_replace(',', '.', str_replace('.', '', request('balance')));
   
    
    
    $users->days_credit = request('days_credit');
    $users->amount_max_credit = $sin_formato_amount_max_credit;
    $users->porc_retencion_iva = request('porc_retencion_iva');
    $users->porc_retencion_islr = request('porc_retencion_islr');
    
    $users->balance = $sin_formato_balance;
    $users->status =  request('status');

    

    $users->save();

    return redirect('/providers')->withSuccess('Actualizacion Exitosa!');
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
