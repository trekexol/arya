<?php

namespace App\Http\Controllers;

use App\ComisionType;
use App\Employee;
use App\Estado;
use App\Http\Controllers\UserAccess\UserAccessController;
use App\Municipio;
use App\Parroquia;
use App\User;
use App\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
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
            $user       =   auth()->user();
            $users_role =   $user->role_id;
            
                $vendors = Vendor::on(Auth::user()->database_name)->orderBy('id' ,'DESC')->get();
            
            return view('admin.vendors.index',compact('vendors'));
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


     
       $estados     = Estado::on(Auth::user()->database_name)->orderBY('descripcion','asc')->pluck('descripcion','id')->toArray();
       $municipios  = Municipio::on(Auth::user()->database_name)->get();
       $parroquias  = Parroquia::on(Auth::user()->database_name)->get();
     
       $comisions   = ComisionType::on(Auth::user()->database_name)->get();
       $employees   = Employee::on(Auth::user()->database_name)->where('status','NOT LIKE','X')->get();


       return view('admin.vendors.create',compact('estados','municipios','parroquias','comisions','employees'));
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
        
        'Parroquia'         =>'required',
        'comision_id'         =>'required',
        'user_id'         =>'required',
        'cedula_rif'         =>'required',
        'name'         =>'required',
        'surname'         =>'required',
        'comision'         =>'required'
      
       
    ]);

    $var = new Vendor();
    $var->setConnection(Auth::user()->database_name);

    
    $var->parroquia_id = request('Parroquia');
    $var->comision_id = request('comision_id');
    $var->employee_id= request('employee_id');
    $var->user_id = request('user_id');

    $var->code = request('code');
    $var->cedula_rif = $request->type_code.request('cedula_rif');
    $var->name = request('name');
    $var->surname = request('surname');

    $var->email = request('email');
    $var->phone = request('phone');
    $var->phone2 = request('phone2');

    $sin_formato_comision = str_replace(',', '.', str_replace('.', '', request('comision')));

    $var->comision = $sin_formato_comision;
    $var->instagram = request('instagram');

    $var->facebook = request('facebook');


    $var->twitter = request('twitter');
    $var->especification = request('especification');
    $var->observation = request('observation');

    $var->direction = request('direction');
    
    $var->status =  1;
  
    $var->save();

    return redirect('/vendors')->withSuccess('Registro Exitoso!');
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
        $vendor = vendor::on(Auth::user()->database_name)->find($id);
        
        $estados            = Estado::on(Auth::user()->database_name)->get();
        $municipios         = Municipio::on(Auth::user()->database_name)->get();
        $parroquias         = Parroquia::on(Auth::user()->database_name)->get();
      

        $comisions   = ComisionType::on(Auth::user()->database_name)->get();
        $employees   = Employee::on(Auth::user()->database_name)->where('status','NOT LIKE','X')->get();

      
        return view('admin.vendors.edit',compact('vendor','estados','municipios','parroquias','comisions','employees'));
  
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
    $vars =  Vendor::on(Auth::user()->database_name)->find($id);

    $vars_status = $vars->status;
   
    $data = request()->validate([
        
        'Parroquia'         =>'required',
        'comision_id'         =>'required',
        'user_id'         =>'required',
        'cedula_rif'         =>'required',
        'name'         =>'required',
        'phone'         =>'required',
        'comision'         =>'required',
        'status'         =>'required'
       
    ]);

    $var = Vendor::on(Auth::user()->database_name)->findOrFail($id);
    
    $var->parroquia_id = request('Parroquia');
    $var->comision_id = request('comision_id');
    $var->employee_id= request('employee_id');
    $var->user_id = request('user_id');

    $var->code = request('code');
    $var->cedula_rif = $request->type_code.request('cedula_rif');
    $var->name = request('name');
    $var->surname = request('surname');

    $var->email = request('email');
    $var->phone = request('phone');
    $var->phone2 = request('phone2');
    $var->comision = str_replace(',', '.', str_replace('.', '', request('comision')));
    $var->instagram = request('instagram');

    $var->facebook = request('facebook');


    $var->twitter = request('twitter');
    $var->especification = request('especification');
    $var->observation = request('observation');

    $var->direction = request('direction');

    if(request('status') == null){
        $var->status = $vars_status;
    }else{
        $var->status = request('status');
    }
   

    $var->save();

    return redirect('/vendors')->withSuccess('Actualizacion Exitosa!');
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
