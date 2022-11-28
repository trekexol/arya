<?php

namespace App\Http\Controllers;

use App\SalaryType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalarytypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Tipos de Salarios');
    }

    public function index(Request $request)
    {

        $agregarmiddleware = $request->get('agregarmiddleware');
        $actualizarmiddleware = $request->get('actualizarmiddleware');
        $eliminarmiddleware = $request->get('eliminarmiddleware');

        $salarytypes     =   SalaryType::on(Auth::user()->database_name)->orderBy('id', 'asc')->get();
   
    
        return view('admin.salarytypes.index',compact('salarytypes','agregarmiddleware','actualizarmiddleware','eliminarmiddleware'));
      
    }

    public function create(Request $request)
    {
  
        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){
        return view('admin.salarytypes.create');
    }else{
        return redirect('/salarytypes')->withDelete('No Tiene Acceso a Registrar');
        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){
        $data = request()->validate([
           
            'name'         =>'required|max:160',
            'description'         =>'required|max:255',
            'status'         =>'required|max:2',
            
           
        ]);

        $users = new Salarytype();
        $users->setConnection(Auth::user()->database_name);

        $users->name = request('name');
        $users->description = request('description');
        $users->status =  request('status');
       

        $users->save();

        return redirect('/salarytypes')->withSuccess('Registro Exitoso!');

    }else{
        return redirect('/salarytypes')->withDelete('No Tiene Acceso a Registrar');
        }
    }



    public function edit(Request $request,$id)
    {

        if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){
        $user  = Salarytype::on(Auth::user()->database_name)->find($id);
        
        return view('admin.salarytypes.edit',compact('user'));
    }else{
        return redirect('/salarytypes')->withDelete('No Tiene Acceso a Editar');
        }
    }

   


    public function update(Request $request,$id)
    {
        if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){
        $users =  Salarytype::on(Auth::user()->database_name)->find($id);
        $user_rol = $users->role_id;
        $user_status = $users->status;
      

        $request->validate([
            'name'      =>'required|string|max:255',
            'description'      =>'required|string|max:255',
            'status'     =>'max:2',
        ]);

        

        $user          = Salarytype::on(Auth::user()->database_name)->findOrFail($id);
        $user->name         = request('name');
        $user->description        = request('description');
       
        if(request('status') == null){
            $user->status = $user_status;
        }else{
            $user->status = request('status');
        }
       

        $user->save();


        return redirect('/salarytypes')->withSuccess('Registro Guardado Exitoso!');
    }else{
        return redirect('/salarytypes')->withDelete('No Tiene Acceso a Editar');
        }

    }


}
