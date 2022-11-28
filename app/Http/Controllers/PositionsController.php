<?php

namespace App\Http\Controllers;

use App\Position;
use App\Segment;
use App\Subsegment;
use App\UnitOfMeasure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PositionsController extends Controller
{
   
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Cargos');
     
    }



    public function index(Request $request)
    {

     

        $agregarmiddleware = $request->get('agregarmiddleware');
        $actualizarmiddleware = $request->get('actualizarmiddleware');
        $eliminarmiddleware = $request->get('eliminarmiddleware');

      

        $positions  = Position::on(Auth::user()->database_name)->orderBy('id', 'asc')->get();


        return view('admin.positions.index',compact('positions','agregarmiddleware','actualizarmiddleware','eliminarmiddleware'));
      
    }

    public function create(Request $request)
    {
        
        

        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){
   
            return view('admin.positions.create');
        }else{
              return redirect('/positions')->withSuccess('No Tiene Acceso a Registrar Cargos');
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

        $users = new Position();
        $users->setConnection(Auth::user()->database_name);

        $users->name = request('name');
        $users->description = request('description');
        $users->status =  request('status');
       

        $users->save();

        return redirect('/positions')->withSuccess('Registro Exitoso!');

    }else{

        return redirect('/positions')->withSuccess('No tienes permiso para agregar!');
    }
    }



    public function edit(Request $request, $id)
    {

        if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){
            $user  = Position::on(Auth::user()->database_name)->find($id);
    
            return view('admin.positions.edit',compact('user'));
        }else{
              return redirect('/positions')->withSuccess('No Tiene Acceso a Editar Cargos');
        }


     
    }

   


    public function update(Request $request,$id)
    {
        if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){
        $users =  Position::on(Auth::user()->database_name)->find($id);
        $user_rol = $users->role_id;
        $user_status = $users->status;
      

        $request->validate([
            'name'      =>'required|string|max:255',
            'description'      =>'required|string|max:255',
            'status'     =>'max:2',
        ]);

        

        $user          = Position::on(Auth::user()->database_name)->findOrFail($id);
        $user->name         = request('name');
        $user->description        = request('description');
       
        if(request('status') == null){
            $user->status = $user_status;
        }else{
            $user->status = request('status');
        }
       

        $user->save();


        return redirect('/positions')->withSuccess('Registro Guardado Exitoso!');
    }else{
        return redirect('/positions')->withDelete('No tienes permiso para editar');

    }
    }

}

