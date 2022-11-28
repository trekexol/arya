<?php

namespace App\Http\Controllers;

use App\NominaType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NominaTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Tipos de Nóminas');
    }

    public function index(Request $request)
    {

        $agregarmiddleware = $request->get('agregarmiddleware');
        $actualizarmiddleware = $request->get('actualizarmiddleware');
        $eliminarmiddleware = $request->get('eliminarmiddleware');

        

        $nominatypes =NominaType::on(Auth::user()->database_name)->orderBy('id', 'asc')->get();

        return view('admin.nominatypes.index',compact('nominatypes','agregarmiddleware','actualizarmiddleware','eliminarmiddleware'));
      
    }

    public function create(Request $request)
    {
  
        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){

        return view('admin.nominatypes.create');
    }else{
        return redirect('/nominatypes')->withSuccess('No Tiene Acceso a Registrar');
        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){

        $data = request()->validate([
           
            'description'         =>'required|max:255',
            'nomina'         =>'required|max:255',
            'status'         =>'required|max:2',
            
           
        ]);

        $users = new Nominatype();
        $users->setConnection(Auth::user()->database_name);

        $users->name = request('nomina');
        $users->description = request('description');
        $users->status =  request('status');
       

        $users->save();

        return redirect('nominatypes')->withSuccess('Registro Exitoso!');

    }else{
        return redirect('/nominatypes')->withSuccess('No Tiene Acceso a Registrar');
        }
    }


    public function edit(Request $request,$id)
    {

        if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){
        $var = Nominatype::on(Auth::user()->database_name)->find($id);
        
        return view('admin.nominatypes.edit',compact('var'));
    }else{
        return redirect('/nominatypes')->withSuccess('No Tiene Acceso a Editar');
        }
    }

   


    public function update(Request $request,$id)
    {
        if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){
        $vars =  Nominatype::on(Auth::user()->database_name)->find($id);

        $var_status = $vars->status;
      

        $request->validate([
        
            'description'      =>'required|string|max:255',
            'status'     =>'max:2',
        ]);

        

        $var  = Nominatype::on(Auth::user()->database_name)->findOrFail($id);
        $var->description        = request('description');
       
        if(request('status') == null){
            $var->status = $var_status;
        }else{
            $var->status = request('status');
        }
       

        $var->save();


        return redirect('nominatypes')->withSuccess('Registro Guardado Exitoso!');
    }else{
        return redirect('/nominatypes')->withSuccess('No Tiene Acceso a Editar');
        }

    }


}
