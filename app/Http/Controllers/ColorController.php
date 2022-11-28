<?php

namespace App\Http\Controllers;

use App\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ColorController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth');
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Colores');
      
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index(Request $request)
    {
       
        $agregarmiddleware = $request->get('agregarmiddleware');
        $actualizarmiddleware = $request->get('actualizarmiddleware');
        $eliminarmiddleware = $request->get('eliminarmiddleware');


           $colors =  Color::on(Auth::user()->database_name)->orderBy('id', 'asc')->get();
      

    
        return view('admin.colors.index',compact('colors','agregarmiddleware','actualizarmiddleware','eliminarmiddleware'));
      
    }

    public function create(Request $request)
    {

        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){
           
            return view('admin.colors.create');
        }else{
              return redirect('/colors')->withSuccess('No Tiene Acceso a Registrar Colores');
        }


        

       

    }

    public function store(Request $request)
    {

        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){
        
        $data = request()->validate([
           
          
            'description'         =>'required|max:255',
            'status'         =>'required|max:1',
           
        ]);

        $users = new Color();
        $users->setConnection(Auth::user()->database_name);

        $users->description = request('description');
        $users->status = request('status');
        

        $users->save();

        return redirect('/colors')->withSuccess('Registro Exitoso!');


    }else{
        return redirect('/colors')->withSuccess('No Tiene Acceso a Registrar Colores');
  }



    }




    public function edit(Request $request, $id)
    {

        if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){
            $var   = Color::on(Auth::user()->database_name)->find($id);
        
            return view('admin.colors.edit',compact('var'));
        }else{
              return redirect('/colors')->withSuccess('No Tiene Acceso a Editar Colores');
        }

       

    }

   


    public function update(Request $request,$id)
    {

        if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){

        $vars =  Color::on(Auth::user()->database_name)->find($id);

        $var_status = $vars->status;

        $request->validate([
          
            'description'      =>'required|string|max:100',
            'status'    =>'required|max:1',
        ]);

        

        $var          = Color::on(Auth::user()->database_name)->findOrFail($id);
        $var->description        = request('description');
       
        if(request('status') == null){
            $var->status = $var_status;
        }else{
            $var->status = request('status');
        }
       

        $var->save();


        return redirect('/colors')->withSuccess('Registro Guardado Exitoso!');


    }else{
        return redirect('/colors')->withSuccess('No Tiene Acceso a Editar Colores');
         }


    }


}
