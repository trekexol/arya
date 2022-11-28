<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Modelo;
use App\Color;
use App\HistoricTransport;
use App\Transport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoricTransportController extends Controller
{
 
    public function __construct(){

        $this->middleware('auth');
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Historial de Transporte');
   }

   public function index(Request $request)
   {

        $agregarmiddleware = $request->get('agregarmiddleware');
        $actualizarmiddleware = $request->get('actualizarmiddleware');
        $eliminarmiddleware = $request->get('eliminarmiddleware');

    
       
        $employees = Employee::on(Auth::user()->database_name)->with('transports')->get();
    
        //dd($employees);
       
       return view('admin.historictransports.index',compact('employees','agregarmiddleware','actualizarmiddleware','eliminarmiddleware'));
   }

  
    public function selecttransport(Request $request)
    {
        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){

            $agregarmiddleware = $request->get('agregarmiddleware');
            $transports = Transport::on(Auth::user()->database_name)->orderBy('id' ,'DESC')->get();

            return view('admin.historictransports.selecttransport',compact('transports','agregarmiddleware'));
        }else{

            return redirect('/historictransports')->withDelete('No tienes permiso para agregar!');

        }
       
    }
    public function selectemployee(Request $request, $transport_id)
    {

        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){
            $agregarmiddleware = $request->get('agregarmiddleware');
        $employees = Employee::on(Auth::user()->database_name)->orderBy('nombres' ,'DESC')->get();

 
        return view('admin.historictransports.selectemployee',compact('employees','transport_id','agregarmiddleware'));
   
        }else{

            return redirect('/historictransports')->withDelete('No tienes permiso para agregar!');

        }
    }
  

   public function create(Request $request, $transport_id,$employee_id)
   {
    if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');    

        return view('admin.historictransports.create',compact('datenow','transport_id','employee_id'));
    }else{

        return redirect('/historictransports')->withDelete('No tienes permiso para agregar!');

    }
   
    }


   public function store(Request $request)
    {
        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){
    $data = request()->validate([
        
       
        'employee_id'         =>'required',
        'transport_id'         =>'required',
        'user_id'         =>'required',
        'date_begin'         =>'required',

    ]);

    $var = new HistoricTransport();
    $var->setConnection(Auth::user()->database_name);

    $var->employee_id = request('employee_id');
    $var->transport_id = request('transport_id');
    $var->user_id = request('user_id');
    $var->date_begin = request('date_begin');
    $var->date_end = null;
   
    $var->save();

    return redirect('/historictransports')->withSuccess('Registro Exitoso!');
    }else{

        return redirect('/historictransports')->withDelete('No tienes permiso para agregar!');

    }

    }



   public function edit(request $request,$id)
   {

    if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){
        $historictransport = HistoricTransport::on(Auth::user()->database_name)->find($id);
     
        $modelos     = Modelo::on(Auth::user()->database_name)->orderBY('description','asc')->pluck('description','id')->toArray();
      
        $colors     = Color::on(Auth::user()->database_name)->orderBY('description','asc')->pluck('description','id')->toArray();
     
        return view('admin.historictransports.edit',compact('historictransport','modelos','colors'));
    }else{

        return redirect('/historictransports')->withDelete('No tienes permiso para Editar!');
    }
   }

  
   public function update(Request $request, $id)
   {

    if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){
    $vars =  HistoricTransport::on(Auth::user()->database_name)->find($id);

    $vars_status = $vars->status;
    $vars_exento = $vars->exento;
    $vars_islr = $vars->islr;
  
    $data = request()->validate([
        
       
        'modelo_id'         =>'required',
        'color_id'         =>'required',
        'user_id'         =>'required',

        'type'         =>'required',
        'placa'         =>'required',
        'photo_historictransport'         =>'required',

        'status'         =>'required',
       
    ]);

    $var = HistoricTransport::on(Auth::user()->database_name)->findOrFail($id);

    $var->modelo_id = request('modelo_id');
    $var->color_id = request('color_id');
    $var->user_id = request('user_id');
    $var->type = request('type');
   
    $var->placa = request('placa');
    $var->photo_historictransport = request('photo_historictransport');

    if(request('status') == null){
        $var->status = $vars_status;
    }else{
        $var->status = request('status');
    }
   
    $var->save();

    return redirect('/historictransports')->withSuccess('Actualizacion Exitosa!');
}
else{

    return redirect('/historictransports')->withDelete('No tienes permiso para Editar!');
}

    }




    public function destroy(Request $request)
    {
       

        if(Auth::user()->role_id  == '1' || $request->get('eliminarmiddleware') == '1'){

            $var = HistoricTransport::on(Auth::user()->database_name)->findOrFail($request->id_user_modal);
            if(isset($var)){
                $var->delete();
                return redirect('historictransports')->withDelete('Registro Eliminado Exitoso!');  
            }else{

                return redirect('/historictransports')->withDanger('No Encuenta el transporte!');

            }
            

           
        
        }else{

            return redirect('/historictransports')->withDanger('No tiene permiso para Eliminar!');
        }

   
    }



   
}
