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
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Pedidos');
    }

    public function index(request $request)
    {
     $agregarmiddleware = $request->get('agregarmiddleware');
     $actualizarmiddleware = $request->get('actualizarmiddleware');
     $eliminarmiddleware = $request->get('eliminarmiddleware');
     $namemodulomiddleware = $request->get('namemodulomiddleware');


                $vendors = Vendor::on(Auth::user()->database_name)->Where('status','1')->orderBy('id' ,'DESC')->get();
                $vendorsinac = Vendor::on(Auth::user()->database_name)->Where('status','0')->orderBy('id' ,'DESC')->get();


            return view('admin.vendors.index',compact('vendors','agregarmiddleware','actualizarmiddleware','vendorsinac'));

   }


   public function create(request $request)
   {

    if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){


       $estados     = Estado::on(Auth::user()->database_name)->orderBY('descripcion','asc')->pluck('descripcion','id')->toArray();
       $municipios  = Municipio::on(Auth::user()->database_name)->get();
       $parroquias  = Parroquia::on(Auth::user()->database_name)->get();

       $comisions   = ComisionType::on(Auth::user()->database_name)->get();
       $employees   = Employee::on(Auth::user()->database_name)->where('status','NOT LIKE','X')->get();


       return view('admin.vendors.create',compact('estados','municipios','parroquias','comisions','employees'));
    }else{
        return redirect('/vendors')->withDanger('No tiene permiso!');

    }

    }


   public function store(Request $request)
    {
        if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){

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

   // $var->direction = request('direction');

    $var->status =  1;

    $var->save();

    if(request('modalactivo') == null){
        return redirect('/vendors')->withSuccess('Registro Exitoso!');

    }else{
        return back()->withSuccess('Registro Exitoso!');

    }

}else{
    return redirect('/vendors')->withDanger('No tiene permiso!');

}
    }


   public function edit(request $request,$id)
   {

    if(Auth::user()->role_id == '1' || $request->get('actualizarmiddleware') == '1'){


        $vendor = vendor::on(Auth::user()->database_name)->find($id);

        $estados            = Estado::on(Auth::user()->database_name)->get();
        $municipios         = Municipio::on(Auth::user()->database_name)->get();
        $parroquias         = Parroquia::on(Auth::user()->database_name)->get();


        $comisions   = ComisionType::on(Auth::user()->database_name)->get();
        $employees   = Employee::on(Auth::user()->database_name)->where('status','NOT LIKE','X')->get();


        return view('admin.vendors.edit',compact('vendor','estados','municipios','parroquias','comisions','employees'));

    }else{
        return redirect('/vendors')->withDanger('No tiene permiso!');

    }

   }


   public function update(Request $request, $id)
   {
    if(Auth::user()->role_id == '1' || $request->get('actualizarmiddleware') == '1'){
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

   // $var->direction = request('direction');

    if(request('status') == null){
        $var->status = $vars_status;
    }else{
        $var->status = request('status');
    }


    $var->save();

    return redirect('/vendors')->withSuccess('Actualizacion Exitosa!');

        }else{
            return redirect('/vendors')->withDanger('No tiene permiso!');

        }
    }



}
