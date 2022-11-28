<?php

namespace App\Http\Controllers;

use App\Modelo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModeloController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Modelos');
    }


    public function index(Request $request)
    {

        $agregarmiddleware = $request->get('agregarmiddleware');
        $actualizarmiddleware = $request->get('actualizarmiddleware');
        $eliminarmiddleware = $request->get('eliminarmiddleware');

        
    
           $modelos      =   Modelo::on(Auth::user()->database_name)->orderBy('id', 'asc')->get();
       

    
        return view('admin.modelos.index',compact('modelos','agregarmiddleware','actualizarmiddleware','eliminarmiddleware'));
      
    }

    public function create(Request $request)
    {

        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){

        return view('admin.modelos.create');
        }else{

            return redirect('/modelos')->withSuccess('No Tiene Permiso para Agregar!');

        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){
        $data = request()->validate([
           
          
            'description'         =>'required|max:255',
            'status'         =>'required|max:1',
           
        ]);

        $users = new Modelo();
        $users->setConnection(Auth::user()->database_name);

        $users->description = request('description');
        $users->status = request('status');
        

        $users->save();

        return redirect('/modelos')->withSuccess('Registro Exitoso!');
        }else{

            return redirect('/modelos')->withSuccess('No Tiene Permiso para Agregar!');

        }
   

    }



    public function edit(request $request,$id)
    {
        if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){
        $var   = Modelo::on(Auth::user()->database_name)->find($id);
        
        return view('admin.modelos.edit',compact('var'));

        }else{
            return redirect('/modelos')->withSuccess('No Tiene Permiso para Editar!');
        }
    }

   


    public function update(Request $request,$id)
    {
        if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){
        $vars =  Modelo::on(Auth::user()->database_name)->find($id);

        $var_status = $vars->status;

        $request->validate([
          
            'description'      =>'required|string|max:100',
            'status'    =>'required|max:1',
        ]);

        

        $var          = Modelo::on(Auth::user()->database_name)->findOrFail($id);
        $var->description        = request('description');
       
        if(request('status') == null){
            $var->status = $var_status;
        }else{
            $var->status = request('status');
        }
       

        $var->save();


        return redirect('/modelos')->withSuccess('Registro Guardado Exitoso!');

    }else{
        return redirect('/modelos')->withSuccess('No Tiene Permiso para Editar!');

    }

    }


}

