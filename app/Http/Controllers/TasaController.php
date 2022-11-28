<?php

namespace App\Http\Controllers;

use App\Tasa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TasaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Tasa del Dia');
    }


    public function index(Request $request)
    {

        $agregarmiddleware = $request->get('agregarmiddleware');
        $actualizarmiddleware = $request->get('actualizarmiddleware');
        $eliminarmiddleware = $request->get('eliminarmiddleware');

         $tasas      =   Tasa::on(Auth::user()->database_name)->orderBy('id', 'desc')->get();
       

    
        return view('admin.tasas.index',compact('tasas','agregarmiddleware','actualizarmiddleware','eliminarmiddleware'));
      
    }

    public function create(Request $request)
    {
  
        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');    
        return view('admin.tasas.create',compact('datenow'));
    }else{
        return redirect('/tasas')->withSuccess('No Tiene Acceso a Registrar');
        }
    }

    public function store(Request $request)
    {
     
        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){
        $data = request()->validate([
           
            'date_begin'         =>'required',
            'amount'         =>'required',
            
           
        ]);

        $tasa_old = Tasa::on(Auth::user()->database_name)->where('date_end','=',null)->first();

        if($tasa_old){
            $date = Carbon::now();
        $datenow = $date->format('Y-m-d');  

        $tasa_old->date_end = $datenow;
      
        $tasa_old->save();
        }
        



        $users = new Tasa();
        $users->setConnection(Auth::user()->database_name);

        $users->id_user = request('id_user');

        $users->date_begin = request('date_begin');

        $valor_sin_formato_amount = str_replace(',', '.', str_replace('.', '', request('amount')));

        $users->amount = $valor_sin_formato_amount;
        

        $users->save();

        return redirect('/tasas')->withSuccess('Registro Exitoso!');

    }else{
        return redirect('/tasas')->withSuccess('No Tiene Acceso a Registrar');
        }
    }



    public function edit(request $request,$id)
    {
        if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){
        $user    = Tasa::on(Auth::user()->database_name)->find($id);
        
        return view('admin.tasas.edit',compact('user'));
    }else{
        return redirect('/tasas')->withSuccess('No Tiene Acceso a Editar');
        }
    }

   


    public function update(Request $request,$id)
    {
        if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){
        $request->validate([
          
            'description'      =>'required|string|max:255',
            
        ]);

        

        $user          = Tasa::on(Auth::user()->database_name)->findOrFail($id);
        $user->description        = request('description');
       
     

        $user->save();


        return redirect('/tasas')->withSuccess('Registro Guardado Exitoso!');
    }else{
        return redirect('/tasas')->withSuccess('No Tiene Acceso a Editar');
        }

    }


}

