<?php

namespace App\Http\Controllers;

use App\ComisionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComisionTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Tipos de Comisión');
    }

   
    public function index(Request $request)
    {

        $agregarmiddleware = $request->get('agregarmiddleware');
        $actualizarmiddleware = $request->get('actualizarmiddleware');
        $eliminarmiddleware = $request->get('eliminarmiddleware');

      
           $comisiontypes      =   ComisionType::on(Auth::user()->database_name)->orderBy('id', 'asc')->get();
     

    
        return view('admin.comisiontypes.index',compact('comisiontypes','agregarmiddleware','actualizarmiddleware','eliminarmiddleware'));
      
    }

    public function create(Request $request)
    {
  
        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){

        return view('admin.comisiontypes.create');
    }else{
        return redirect('/comisiontypes')->withDelete('No Tiene Acceso a Registrar');
        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){
        $data = request()->validate([
           
           
            'description'         =>'required|max:100',
            'status'         =>'required|max:1',
            
           
        ]);

        $users = new Comisiontype();
        $users->setConnection(Auth::user()->database_name);
      
        $users->description = request('description');
        $users->status =  request('status');
       

        $users->save();

        return redirect('/comisiontypes')->withSuccess('Registro Exitoso!');
    }else{
        return redirect('/comisiontypes')->withDelete('No Tiene Acceso a Registrar');
        }
    }




    public function edit(Request $request,$id)
    {

        if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){

        $var = Comisiontype::on(Auth::user()->database_name)->find($id);
        
        return view('admin.comisiontypes.edit',compact('var'));
    }else{
        return redirect('/comisiontypes')->withSuccess('No Tiene Acceso a Editar');
        }
    }

   


    public function update(Request $request,$id)
    {
       
        if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){
        $users =  Comisiontype::on(Auth::user()->database_name)->find($id);
        
        $user_status = $users->status;
      

        $request->validate([
           
            'description'      =>'required|string|max:100',
            'status'     =>'max:2',
        ]);

        

        $user          = comisiontype::on(Auth::user()->database_name)->findOrFail($id);
       
        $user->description        = request('description');
       
        if(request('status') == null){
            $user->status = $user_status;
        }else{
            $user->status = request('status');
        }
       

        $user->save();


        return redirect('/comisiontypes')->withSuccess('Registro Guardado Exitoso!');
    }else{
        return redirect('/comisiontypes')->withSuccess('No Tiene Acceso a Editar');
        }

    }


}

