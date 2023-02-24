<?php

namespace App\Http\Controllers;
use App;
use App\Employee;
use App\Company;
use App\BasesCalcs;
use App\Nomina;
use App\NominaCalculation;
use App\NominaBasesCalcs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NominaPartsController extends Controller
{
    public $conection_logins = "logins";

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index($type = null)
    {
        $user= auth()->user();

        if($type == 'liquidaciones'){
            $employees = Employee::on(Auth::user()->database_name)->where('status','5')->orderBy('id' ,'DESC')->get();

        }else{
            $employees = Employee::on(Auth::user()->database_name)->where('status','NOT LIKE','X')->orderBy('id' ,'DESC')->get();

        }

        return view('admin.nominaparts.index',compact('employees','type'));
    }



    function completcalcs($employee = null, $tipo = null)
    {


        if($tipo == 'prestacion'){

            $pdf = App::make('dompdf.wrapper');
            $company = Company::on(Auth::user()->database_name)->find(1);

            $employee = Employee::on(Auth::user()->database_name) // Buscamos el empleado
            ->where('status','NOT LIKE','X')
            ->where('id','=',$employee)
            ->orderBy('id' ,'DESC')->get()->first();



          $pdf = $pdf->loadView('pdf.prestations',compact('company','tipo'))->setPaper('a4', 'landscape');

          return $pdf->stream();

        }

        if($tipo == 'liquidacion'){
            $idempleado = $employee;

            $pdf = App::make('dompdf.wrapper');

            $company = Company::on(Auth::user()->database_name)->find(1);

            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');

            $employee = Employee::on(Auth::user()->database_name)
            ->join('positions','positions.id','position_id') // Buscamos el empleado
            ->where('employees.status','5')
            ->where('employees.id','=',$idempleado)
            ->first();

            $ultima_nomina = NominaCalculation::on(Auth::user()->database_name)
            ->join('nominas','nominas.id','id_nomina')
            ->where('nominas.status','NOT LIKE','X')
            ->where('id_employee',$idempleado)
            ->latest('nominas.created_at')->first();


          $pdf = $pdf->loadView('pdf.prestations',compact('company','tipo','employee','datenow','ultima_nomina'))->setPaper('a4');

          return $pdf->stream();

        }



    }




    function balancecomprobacion(Request $request)
    {


        if($request->tipo == 'balancecomprobacion'){

        $tipo = $request->tipo;
        $arreglo = decrypt($request->employee);

        $pdf = App::make('dompdf.wrapper');

           $company = Company::on(Auth::user()->database_name)->find(1);

           $date = Carbon::now();
           $datenow = $date->format('Y-m-d');

          $pdf = $pdf->loadView('pdf.prestations',compact('company','tipo','arreglo','datenow'))->setPaper('a4');

          return $pdf->stream();

        }



    }




}
