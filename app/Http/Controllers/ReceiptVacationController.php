<?php

namespace App\Http\Controllers;

use App\Employee;
use App\ReceiptVacation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceiptVacationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Recibo de Vacaciones');
     
    }



    public function index(Request $request)
    {

        $agregarmiddleware = $request->get('agregarmiddleware');
        $actualizarmiddleware = $request->get('actualizarmiddleware');
        $eliminarmiddleware = $request->get('eliminarmiddleware');


        $receiptvacations = ReceiptVacation::on(Auth::user()->database_name)->orderBy('id', 'asc')->get();
 

        return view('admin.receiptvacations.index',compact('receiptvacations','agregarmiddleware','actualizarmiddleware','eliminarmiddleware'));
      
    }

    public function indexemployees(Request $request)
    {

        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){
        $employees = Employee::on(Auth::user()->database_name)->where('status','NOT LIKE','X')->get();

        return view('admin.receiptvacations.indexemployees',compact('employees'));
        }else{

            return redirect('/receiptvacations')->withSuccess('No Tiene Acceso a Registrar');

        }
    }



    public function create(Request $request,$id)
    {

        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){
        $employee = Employee::on(Auth::user()->database_name)->find($id);

       //HACER VALIDACION DE LAS FECHAS DE INICIO Y FIN

      // ReceiptVacation::on(Auth::user()->database_name)->where('employee_id',$employee->id)->beetween('date_begin',,'date_end');

        //Si el empleado existe
        if($employee){


            return view('admin.receiptvacations.create',compact('employee'));



        }else{
           // return view('admin.receiptvacations.indexemployees');
            return redirect('receiptvacations/indexemployees')->withDanger('El Empleado No Existe');
    
        }
    }else{
        return redirect('/receiptvacations')->withSuccess('No Tiene Acceso a Registrar');

    }

        
    }

    public function store(Request $request)
    {
        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){
        //ACORDARSE DE VALIDAR LOS FOREIGN KEY COMO BIGINTEGER
        $data = request()->validate([
           
            'employee_id'    =>'required',
            'date_begin'     =>'required|date',
            'date_end'      =>'required|date',

            'days_vacations'    =>'required|integer',
            'bono_vacations'     =>'required|integer',
            'days_feriados'      =>'required|integer',

            'lph'    =>'required',
            'sso'     =>'required',
            'seguro_paro_forzoso'   =>'required',

            'ultimo_sueldo'    =>'required',
            'total_pagar'     =>'required',
            

            'status'    =>'required|max:1',
            
           
        ]);

        $var = new Receiptvacation();
        $var->setConnection(Auth::user()->database_name);

        $var->employee_id = request('employee_id');
        $var->date_begin = request('date_begin');
        $var->date_end =  request('date_end');

        $var->days_vacations = request('days_vacations');
        $var->bono_vacations = request('bono_vacations');
        $var->days_feriados =  request('days_feriados');

        $var->lph = request('lph');
        $var->sso = request('sso');
        $var->seguro_paro_forzoso =  request('seguro_paro_forzoso');

        $var->ultimo_sueldo = request('ultimo_sueldo');
        $var->total_pagar = request('total_pagar');
       
        $var->status =  request('status');
       

        $var->save();

        return redirect('receiptvacations')->withSuccess('Registro Exitoso!');

    }else{
        return redirect('/receiptvacations')->withSuccess('No Tiene Acceso a Registrar');

    }

    }



    public function edit(request $request,$id)
    {
        if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == '1'){
        $var = Receiptvacation::on(Auth::user()->database_name)->find($id);
        
        return view('admin.receiptvacations.edit',compact('var'));
        }else{

            return redirect('/receiptvacations')->withSuccess('No Tiene Acceso a Editar');
        }
    }

   


    public function update(Request $request,$id)
    {
        if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == '1'){
        $vars =  Receiptvacation::on(Auth::user()->database_name)->find($id);

        $var_status = $vars->status;
      

        //ACORDARSE DE VALIDAR LOS FOREIGN KEY COMO BIGINTEGER
        $data = request()->validate([
           
            'employee_id'    =>'required',
            'date_begin'     =>'required|date',
            'date_end'      =>'required|date',

            'days_vacations'    =>'required|integer',
            'bono_vacations'     =>'required|integer',
            'days_feriados'      =>'required|integer',

            'lph'    =>'required',
            'sso'     =>'required',
            'seguro_paro_forzoso'   =>'required',

            'ultimo_sueldo'    =>'required',
            'total_pagar'     =>'required',
            

            'status'    =>'required|max:1',
            
           
        ]);

        $var    = ReceiptVacation::on(Auth::user()->database_name)->findOrFail($id);

        $var->employee_id = request('employee_id');
        $var->date_begin = request('date_begin');
        $var->date_end =  request('date_end');

        $var->days_vacations = request('days_vacations');
        $var->bono_vacations = request('bono_vacations');
        $var->days_feriados =  request('days_feriados');

        $var->lph = request('lph');
        $var->sso = request('sso');
        $var->seguro_paro_forzoso =  request('seguro_paro_forzoso');

        $var->ultimo_sueldo = request('ultimo_sueldo');
        $var->total_pagar = request('total_pagar');

     
        if(request('status') == null){
            $var->status = $var_status;
        }else{
            $var->status = request('status');
        }
       

        $var->save();


        return redirect('receiptvacations')->withSuccess('Registro Guardado Exitoso!');

    }else{

        return redirect('/receiptvacations')->withSuccess('No Tiene Acceso a Editar');
    }

    }


}
