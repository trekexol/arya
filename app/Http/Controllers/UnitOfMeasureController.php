<?php

namespace App\Http\Controllers;

use App\UnitOfMeasure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnitOfMeasureController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Unidades de Medida');
    }

    public function index(Request $request)
    {

        $agregarmiddleware = $request->get('agregarmiddleware');
        $actualizarmiddleware = $request->get('actualizarmiddleware');
        $eliminarmiddleware = $request->get('eliminarmiddleware');

        
        $unitofmeasures      =   UnitOfMeasure::on(Auth::user()->database_name)->orderBy('id', 'asc')->get();
      

        return view('admin.unitofmeasures.index',compact('unitofmeasures','agregarmiddleware','actualizarmiddleware','eliminarmiddleware'));
      
    }

    public function create(Request $request)
    {

        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){

            return view('admin.unitofmeasures.create');
        }else{
            return redirect('/unitofmeasures')->withDelete('No Tiene Acceso a Registrar');
            }

        
    }

    public function store(Request $request)
    {
        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){
        $data = request()->validate([
           
            'code'         =>'required|max:5',
            'description'         =>'required|max:100',
            'status'         =>'required|max:1',
            
           
        ]);

        $users = new UnitOfMeasure();
        $users->setConnection(Auth::user()->database_name);
        
        $users->code = request('code');
        $users->description = request('description');
        $users->status =  request('status');
       

        $users->save();

        return redirect('/unitofmeasures')->withSuccess('Registro Exitoso!');
    }else{
        return redirect('/unitofmeasures')->withDelete('No Tiene Acceso a Registrar');
        }
    }



    public function edit(request $request,$id)
    {
        if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){
        $var = UnitOfMeasure::on(Auth::user()->database_name)->find($id);
        
        return view('admin.unitofmeasures.edit',compact('var'));
        }else{

            return redirect('/unitofmeasures')->withDelete('No Tiene Acceso a Editar');
        }
    }

   


    public function update(Request $request,$id)
    {
        if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){
        $users =  UnitOfMeasure::on(Auth::user()->database_name)->find($id);
       
        $user_status = $users->status;
      

        $request->validate([
            'code'         =>'required|max:5',
            'description'         =>'required|max:100',
            'status'         =>'required|max:1',
        ]);

        

        $user          = UnitOfMeasure::on(Auth::user()->database_name)->findOrFail($id);
        $user->code = request('code');
        $user->description = request('description');
       
       
        if(request('status') == null){
            $user->status = $user_status;
        }else{
            $user->status = request('status');
        }
       

        $user->save();


        return redirect('/unitofmeasures')->withSuccess('Registro Guardado Exitoso!');
    }else{

        return redirect('/unitofmeasures')->withDelete('No Tiene Acceso a Editar');
    }

    }


}
