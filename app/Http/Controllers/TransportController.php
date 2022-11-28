<?php

namespace App\Http\Controllers;

use App\Color;
use App\Modelo;
use App\Transport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransportController extends Controller
{
 
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Transportes');
    }

    public function index(Request $request)
    {

        $agregarmiddleware = $request->get('agregarmiddleware');
        $actualizarmiddleware = $request->get('actualizarmiddleware');
        $eliminarmiddleware = $request->get('eliminarmiddleware');

        $transports = Transport::on(Auth::user()->database_name)->orderBy('id' ,'DESC')->get();

       return view('admin.transports.index',compact('transports','agregarmiddleware','actualizarmiddleware','eliminarmiddleware'));
   }


   public function create(Request $request)
    {
  
    if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){

    $modelos     = Modelo::on(Auth::user()->database_name)->get();
    $colors      = Color::on(Auth::user()->database_name)->get();

        return view('admin.transports.create',compact('modelos','colors'));

    }else{
        return redirect('/transports')->withDelete('No Tiene Acceso a Registrar');
        }
   }

   public function store(Request $request)
    {
        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){
    $data = request()->validate([
        
       
        'modelo_id'         =>'required',
        'color_id'         =>'required',
        'user_id'         =>'required',

        'type'         =>'required',
        'placa'         =>'required',
        

        'status'         =>'required',
       
    ]);

    $var = new Transport();
    $var->setConnection(Auth::user()->database_name);

    $var->modelo_id = request('modelo_id');
    $var->color_id = request('color_id');
    $var->user_id = request('user_id');
    $var->type = request('type');
   
    $var->placa = request('placa');
    $var->photo_transport = request('photo_transport');

    $var->status =  request('status');
  
    $var->save();

    return redirect('/transports')->withSuccess('Registro Exitoso!');

    }else{
        return redirect('/transports')->withDelete('No Tiene Acceso a Registrar');
    }
    }



   public function edit(request $request,$id)
   {

    if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){
        $transport = Transport::on(Auth::user()->database_name)->find($id);
       
        $modelos     = Modelo::on(Auth::user()->database_name)->orderBY('description','asc')->get();
      
        $colors     = Color::on(Auth::user()->database_name)->orderBY('description','asc')->get();
     
        return view('admin.transports.edit',compact('transport','modelos','colors'));

    }else{
        return redirect('/transports')->withDelete('No Tiene Acceso a Editar');
    }
  
   }

   public function update(Request $request, $id)
   {
    
    if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){
  
  request()->validate([
        
       
        'modelo_id'         =>'required',
        'color_id'         =>'required',
        'user_id'         =>'required',

        'type'         =>'required',
        'placa'         =>'required',
        //'photo_transport'         =>'required',

        'status'         =>'required',
       
    ]);
 
    $var = Transport::on(Auth::user()->database_name)->findOrFail($id);

    
   
    $var->placa = request('placa');
    $var->photo_transport = request('photo_transport');

    $var->status =  request('status');
  


    $var->modelo_id = request('modelo_id');
    $var->color_id = request('color_id');
    $var->user_id = request('user_id');
    $var->type = request('type');
   
    $var->placa = request('placa');
    $var->photo_transport = request('photo_transport');
   
    $var->save();

    return redirect('/transports')->withSuccess('Actualizacion Exitosa!');

}else{
    return redirect('/transports')->withDelete('No Tiene Acceso a Editar');
}
    }


}
