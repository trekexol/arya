<?php

namespace App\Http\Controllers;

use App\Branch;
use Illuminate\Http\Request;

use App\Employee;
use App\Estado;                 //IMPORTANTE NOMBRE DE LA CLASE
use App\Municipio;              //IMPORTANTE NOMBRE DE LA CLASE
use App\Parroquia;              //IMPORTANTE NOMBRE DE LA CLASE

use App\Position;                 
use App\SalaryType;              
use App\Profession;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;      


class EmployeeController extends Controller
{
 
    public function __construct(){

       $this->middleware('auth');
   }

   public function index()
   {
       $user= auth()->user();
       $employees = Employee::on(Auth::user()->database_name)->where('status','NOT LIKE','X')->orderBy('id' ,'DESC')->get();
      
       return view('admin.employees.index',compact('employees'));
   }

   /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function create()
   {


       $datetime = date('d/m/Y T08:30');
       $estados            = Estado::on(Auth::user()->database_name)->orderBY('descripcion','asc')->pluck('descripcion','id')->toArray();
       $municipios         = Municipio::on(Auth::user()->database_name)->get();
       $parroquias         = Parroquia::on(Auth::user()->database_name)->get();
     
       $position           = Position::on(Auth::user()->database_name)->get();
       $salarytype         = Salarytype::on(Auth::user()->database_name)->get();
       $profession         = Profession::on(Auth::user()->database_name)->get();
       $centro_costo       = Branch::on(Auth::user()->database_name)->orderBy('description','asc')->get();
       return view('admin.employees.create',compact('estados','municipios','parroquias','position','salarytype','profession','centro_costo'));
   }

   /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
   public function store(Request $request)
    {
   
    $data = request()->validate([
        'nombres'         =>'required|max:160',
        'apellidos'         =>'required|max:160',
        'id_empleado'         =>'required',
        'telefono1'         =>'required',
        'email'         =>'required|max:255|unique:employees,email',

        'amount_utilities'         =>'required',

        'fecha_ingreso'         =>'required',
        'fecha_nacimiento'         =>'required',
        
        'position_id'         =>'required',
        'profession_id'         =>'required',

        'asignacion_general'         =>'required',
        
        'estado'         =>'required',
        'Municipio'         =>'required',
        'Parroquia'         =>'required',
        'direccion'         =>'required',
        'salarytype_id'         =>'required',
        'monto_pago'         =>'required',
        'centro_costo'         =>'required'
        
       
    ]);
    
    $users = new Employee();
    $users->setConnection(Auth::user()->database_name);
    
    $users->nombres = request('nombres');
    $users->apellidos = request('apellidos');
    $users->code_employee = request('code_employee');
    $users->position_id = request('position_id');
    $users->salary_types_id = request('salary_types_id');
    $users->profession_id = request('profession_id');


    $users->id_empleado = $request->type_code.request('id_empleado');
    $users->amount_utilities = request('amount_utilities');


    $users->estado_id = request('estado');
    $users->municipio_id = request('Municipio');
    $users->parroquia_id = request('Parroquia');

    $users->fecha_ingreso = request('fecha_ingreso');
   
    $users->fecha_nacimiento = request('fecha_nacimiento');
    $users->direccion = request('direccion');

    $sin_formato_monto_pago = str_replace(',', '.', str_replace('.', '', request('monto_pago')));
    $sin_formato_asignacion_general = str_replace(',', '.', str_replace('.', '', request('asignacion_general')));

    $users->monto_pago = $sin_formato_monto_pago;
    $users->asignacion_general = $sin_formato_asignacion_general;

    $users->salary_types_id = request('salarytype_id');


    $users->email = request('email');
    $users->telefono1 = request('telefono1');

    $sin_formato_acumulado_prestaciones = str_replace(',', '.', str_replace('.', '', request('acumulado_prestaciones')));
    $sin_formato_acumulado_utilidades = str_replace(',', '.', str_replace('.', '', request('acumulado_utilidades')));


    $users->dias_acumulado_prestaciones = request('dias_pres_acumulado');
    $users->dias_acumulado_vacaciones = request('dias_vaca_acumulado');
    $sin_formato_int_acumulado_prestaciones = str_replace(',', '.', str_replace('.', '', request('intereses_prest_acumulado')));
     
    $users->int_acumulado_prestaciones = $sin_formato_int_acumulado_prestaciones;

    $users->acumulado_prestaciones = $sin_formato_acumulado_prestaciones;
    $users->acumulado_utilidades = $sin_formato_acumulado_utilidades;
    $users->acumulado_prestaciones = $sin_formato_acumulado_prestaciones;



    $users->status =  request('status');
    $users->branch_id = request('centro_costo');

    
   
    $users->save();

    return redirect('/employees')->withSuccess('Registro Exitoso!');
    }

   /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function show($id)
   {
       //
   }

   /**
    * Show the form for editing the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function edit($id)
   {
        $var = Employee::on(Auth::user()->database_name)->find($id);
        
        $estados             = Estado::on(Auth::user()->database_name)->get();
        $municipios          = Municipio::on(Auth::user()->database_name)->get();
        $parroquias          = Parroquia::on(Auth::user()->database_name)->get();
        $positions           = Position::on(Auth::user()->database_name)->get();
        $salarytypes         = Salarytype::on(Auth::user()->database_name)->get();
        $professions         = Profession::on(Auth::user()->database_name)->get();
        $centro_costo        = Branch::on(Auth::user()->database_name)->orderBy('description','asc')->get();

        return view('admin.employees.edit',compact('var','estados','municipios','parroquias','positions','salarytypes','professions','centro_costo'));
  
   }

   /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, $id)
   {

    $users =  Employee::on(Auth::user()->database_name)->find($id);
   
    $data = request()->validate([
        'nombres'         =>'required|max:160',
        'apellidos'         =>'required|max:160',
        'id_empleado'         =>'required',
        'telefono1'         =>'required',
        'amount_utilities'         =>'required|max:2',
        
        'fecha_ingreso'         =>'required',
        'fecha_nacimiento'         =>'required',
        
        'position_id'         =>'required',
        'profession_id'         =>'required',
        'asignacion_general'         =>'required',

        'estado'         =>'required',
        'Municipio'         =>'required',
        'Parroquia'         =>'required',
        'direccion'         =>'required',
        
        'salarytype_id'         =>'required',
       
        'monto_pago'         =>'required',
       
    ]);

    $users = Employee::on(Auth::user()->database_name)->findOrFail($id);
      
    
    $users->nombres = request('nombres');
    $users->apellidos = request('apellidos');

    $users->position_id = request('position_id');
    $users->salary_types_id = request('salary_types_id');
    $users->profession_id = request('profession_id');


    $users->code_employee = request('code_employee');
    $users->amount_utilities = request('amount_utilities');


    $users->estado_id = request('estado');
    $users->municipio_id = request('Municipio');
    $users->parroquia_id = request('Parroquia');

    $users->id_empleado = $request->type_code.request('id_empleado');
    $users->fecha_ingreso = request('fecha_ingreso');
   
    $users->fecha_nacimiento = request('fecha_nacimiento');
    $users->direccion = request('direccion');
    $users->monto_pago = str_replace(',', '.', str_replace('.', '', request('monto_pago')));
    $users->asignacion_general = str_replace(',', '.', str_replace('.', '', request('asignacion_general')));

    $users->salary_types_id = request('salarytype_id');


    $users->email = request('email');
    $users->telefono1 = request('telefono1');
    $users->acumulado_prestaciones = str_replace(',', '.', str_replace('.', '',request('acumulado_prestaciones')));
    $users->acumulado_utilidades = str_replace(',', '.', str_replace('.', '',request('acumulado_utilidades')));

    $users->dias_acumulado_prestaciones = request('dias_pres_acumulado');
    $users->dias_acumulado_vacaciones = request('dias_vaca_acumulado');
    $sin_formato_int_acumulado_prestaciones = str_replace(',', '.', str_replace('.', '', request('intereses_prest_acumulado')));
     
    $users->int_acumulado_prestaciones = $sin_formato_int_acumulado_prestaciones;

    $users->status =  request('status');
     
    $users->branch_id = request('centro_costo');

    $users->save();
     
    return redirect('/employees')->withSuccess('Actualizacion Exitosa!');
    
    }


   /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function destroy(Request $request)
   {
        $employee = Employee::on(Auth::user()->database_name)->findOrFail($request->id_employee_modal);

        if(isset($employee)){
            $employee->status = 'X';

            if(empty($employee->fecha_egreso)){
                $date = Carbon::now();
                $datenow = $date->format('Y-m-d'); 
                $employee->fecha_egreso = $datenow;
            }

            $employee->save();

            return redirect('/employees')->withSuccess('Eliminacion Exitosa!');

        }else{

            return redirect('/employees')->withDanger('No se encontro el empleado!');
        }
   }
}
