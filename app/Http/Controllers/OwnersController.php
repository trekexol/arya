<?php

namespace App\Http\Controllers;

use App\Owners;
use App\Branch;
use App\Http\Controllers\UserAccess\UserAccessController;
use App\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnersController extends Controller
{
 
    public $userAccess;
    public $modulo = 'Propietarios';

    public function __construct(){

        $this->middleware('auth');
        $this->userAccess = new UserAccessController();
    }

   public function index()
   {

            $clients = Owners::on(Auth::user()->database_name)->orderBy('id' ,'DESC')->get(); 
            return view('admin.owners.index',compact('clients'));
   
   }

   /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function create()
   {
        $vendors = Vendor::on(Auth::user()->database_name)->orderBy('name','asc')->get();
        $branches = Branch::on(Auth::user()->database_name)->orderBy('description','desc')->get();

       return view('admin.owners.create',compact('vendors','branches'));
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
            'direction'         =>'required|max:200',
            'city'         =>'required',
            'country'         =>'required',
            'phone1'         =>'required',
            'days_credit'         =>'required|integer',
           
        ]);

    $users = new Owners();
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

    return redirect('/owners')->withSuccess('Registro Exitoso!');
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
        $var = Owners::on(Auth::user()->database_name)->find($id);
        
        $vendors = Vendor::on(Auth::user()->database_name)->orderBy('name','asc')->get();

        $branches = Branch::on(Auth::user()->database_name)->orderBy('description','desc')->get();


        return view('admin.owners.edit',compact('var','vendors','branches'));
  
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

    $vars =  Owners::on(Auth::user()->database_name)->find($id);
    $vars_status = $vars->status;
   
    $data = request()->validate([
        'type_code'         =>'required|max:20',
        
        'razon_social'         =>'required|max:100',
        'cedula_rif'         =>'required',
        'direction'         =>'required|max:200',
        'city'         =>'required',
        'country'         =>'required',
        'phone1'         =>'required',
        'days_credit'         =>'required|integer',
        

       
    ]);
    

    $users = Owners::on(Auth::user()->database_name)->findOrFail($id);
    
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
    $users->aliquot = request('aliquot');
    $users->id_cost_center = request('id_cost_center');
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

    return redirect('/owners')->withSuccess('Actualizacion Exitosa!');
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
