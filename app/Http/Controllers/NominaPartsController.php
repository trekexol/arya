<?php

namespace App\Http\Controllers;
use App;
use App\Employee;
use App\Company;
use App\BasesCalcs;
use App\Nomina;
use App\NominaCalculation;
use App\NominaBasesCalcs;
use App\BvcRatesSocialBenefits;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        }elseif($type == 'utilidades' OR $type == 'prestaciones'){
            $employees = Employee::on(Auth::user()->database_name)->whereNotIn('status', ['5','X','0'])->orderBy('id' ,'DESC')->get();

        }else{
            $employees = Employee::on(Auth::user()->database_name)->where('status','NOT LIKE','X')->where('status','NOT LIKE','5')->orderBy('id' ,'DESC')->get();

        }


        $datospresta = DB::connection(Auth::user()->database_name)
            ->table('nomina_calculations AS a')
            ->join('nominas as b', 'a.id_nomina','b.id')
            ->wherein('a.id_nomina_concept', ['2','3','4'])
            ->select(DB::raw('SUBSTR(b.date_end,1,4) AS año'))
            ->groupBy(DB::raw('SUBSTR(b.date_end,1,4)'))
            ->get();


        return view('admin.nominaparts.index',compact('employees','type','datospresta'));
    }



    function completcalcs($employee = null, $tipo = null , $year = null)
    {


        if($tipo == 'prestacion'){
            $idempleado = $employee;
            $pdf = App::make('dompdf.wrapper');
            $company = Company::on(Auth::user()->database_name)->find(1);

            $employee = Employee::on(Auth::user()->database_name) // Buscamos el empleado
            ->where('id','=',$idempleado)->first();

           $datospresta = DB::connection(Auth::user()->database_name)
            ->table('nomina_calculations AS a')
            ->join('nominas as b', 'a.id_nomina','b.id')
            ->where('a.id_employee',$idempleado)
            ->wherein('a.id_nomina_concept', ['2','3','4'])
            ->select(DB::raw('SUBSTR(b.date_end,1,4) AS año'), DB::raw('SUBSTR(b.date_end,6,2) AS mes'), DB::raw('sum(a.amount) as monto'), 'a.id_nomina_concept')
            ->groupBy(DB::raw('SUBSTR(b.date_end,1,4)') ,  DB::raw('SUBSTR(b.date_end,6,2)'),  'a.id_nomina_concept')
            ->get();


            foreach($datospresta as $datosprestaciones){
                $bcvtasa   = DB::connection($this->conection_logins)
                ->table('bvc_rates_social_benefits')
                ->where('period',$datosprestaciones->año)
                ->where('month',$datosprestaciones->mes)
                ->first();

                if($bcvtasa){

                $datosprestaciones->tasaaver = $bcvtasa->rate_average_a_p;

                }else{

                    $bcvtasa   = DB::connection($this->conection_logins)
                    ->table('bvc_rates_social_benefits')
                    ->orderBy('id','DESC')
                    ->first();

                    $datosprestaciones->tasaaver = $bcvtasa->rate_average_a_p;

                }



            }




          $pdf = $pdf->loadView('pdf.prestations',compact('employee','company','tipo','datospresta'))->setPaper('a4', 'landscape');

          return $pdf->stream();

        }

        if($tipo == 'liquidacion'){

            $idempleado = $employee;

            $pdf = App::make('dompdf.wrapper');

            $company = Company::on(Auth::user()->database_name)->find(1);

            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');

            $diasextras = 0;
            $diasvaca = '';
            $acumulado = 0;
            $interesesacumulado = 0;
            $cuotautilidad = 0;
            $cuotavaca = 0;
            $diasutilidades = 0;

            $employee = Employee::on(Auth::user()->database_name)
            ->join('positions','positions.id','position_id') // Buscamos el empleado
            ->where('employees.status','5')
            ->where('employees.id','=',$idempleado)
            ->first();

            $datospresta = DB::connection(Auth::user()->database_name)
            ->table('nomina_calculations AS a')
            ->join('nominas as b', 'a.id_nomina','b.id')
            ->where('a.id_employee',$idempleado)
            ->wherein('a.id_nomina_concept', ['2','3','4'])
            ->select(DB::raw('SUBSTR(b.date_end,1,4) AS año'), DB::raw('SUBSTR(b.date_end,6,2) AS mes'), DB::raw('sum(a.amount) as monto'), 'a.id_nomina_concept')
            ->groupBy(DB::raw('SUBSTR(b.date_end,1,4)') ,  DB::raw('SUBSTR(b.date_end,6,2)'),  'a.id_nomina_concept')
            ->get();


            $ultimopago = DB::connection(Auth::user()->database_name)
            ->table('nomina_calculations AS a')
            ->join('nominas as b', 'a.id_nomina','b.id')
            ->where('a.id_employee',$idempleado)
            ->wherein('a.id_nomina_concept', ['2','3','4'])
            ->select(DB::raw('MAX(b.date_end) AS ultimopago'))
            ->first();

            if (empty($ultimopago)){
                $ultimopago_e = '';

            } else {

            $ultimopago_e = $ultimopago->ultimopago;

            }



            if($employee->amount_utilities == 'Ma'){
                $diasutilidades = 120;

            }else{
                $diasutilidades = 30;
            }

                $i = 1;
                $o = 1;
                $cantidadmeses = 1;
                $diasvacaciones = 15;



            foreach($datospresta as $datosprestaciones){



                $bcvtasa   = DB::connection($this->conection_logins)
                ->table('bvc_rates_social_benefits')
                ->where('period',$datosprestaciones->año)
                ->where('month',$datosprestaciones->mes)
                ->first();

                if($bcvtasa){

                $tasaaver = $bcvtasa->rate_average_a_p;

                }else{

                    $bcvtasa   = DB::connection($this->conection_logins)
                    ->table('bvc_rates_social_benefits')
                    ->orderBy('id','DESC')
                    ->first();

                    $tasaaver = $bcvtasa->rate_average_a_p;

                }


                $sueldodiario = $datosprestaciones->monto/30;
                $cuotautilidad = $sueldodiario*$diasutilidades/360;



                if($o == 24){
                    $diasvacaciones = $diasvacaciones + 1;
                    $diasextras = $diasextras + 1;
                    $os = 1;
                }

                elseif(isset($os) AND $os == 12){
                    $diasvacaciones = $diasvacaciones + 1;

                    $os = 1;
                }elseif(isset($os)){
                    $os++;
                }

                $cuotavaca = $sueldodiario*$diasvacaciones/360;

                $salariointegral = $sueldodiario + $cuotautilidad + $cuotavaca;




            if($cantidadmeses == 3)
            {
                $asig =   $salariointegral * $diasvacaciones;
                $diasvaca = 15;
                $diasextrass = $diasextras;
                $cantidadmeses = 0;
                $ultimodia = 15;
                $acumulado += $asig;
                $interes = $acumulado * $tasaaver / 1200;
                $interesesacumulado += $interes;

            }else{

                $diasvaca = '';
                $diasextrass = '';
                $asig = 0;
                $acumulado += $asig;

                }





                $cantidadmeses++;
                $o++;
                $i++;

            }




          $pdf = $pdf->loadView('pdf.prestations',compact('diasutilidades','diasvacaciones','company','tipo','employee','datenow','cuotautilidad','cuotavaca','acumulado','ultimopago_e','interesesacumulado'))->setPaper('a4');



          return $pdf->stream();

        }




        if($tipo == 'utilidades'){
            $idempleado = $employee;
            $pdf = App::make('dompdf.wrapper');
            $company = Company::on(Auth::user()->database_name)->find(1);

            $employee = Employee::on(Auth::user()->database_name)
            ->join('positions','positions.id','position_id') // Buscamos el empleado
            ->where('employees.id','=',$idempleado)
            ->first();


            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');

           $datospresta = DB::connection(Auth::user()->database_name)
            ->table('nomina_calculations AS a')
            ->join('nominas as b', 'a.id_nomina','b.id')
            ->where('a.id_employee',$idempleado)
            ->where(DB::raw('SUBSTR(b.date_end,1,4)'),$year)
            ->wherein('a.id_nomina_concept', ['2','3','4'])
            ->select(DB::raw('SUBSTR(b.date_end,1,4) AS año'), DB::raw('SUBSTR(b.date_end,6,2) AS mes'), DB::raw('sum(a.amount) as monto'), 'a.id_nomina_concept')
            ->groupBy(DB::raw('SUBSTR(b.date_end,1,4)') ,  DB::raw('SUBSTR(b.date_end,6,2)'),  'a.id_nomina_concept')
            ->get();



            if($datospresta->count() > 0){

            foreach($datospresta as $datosprestaciones){
                $bcvtasa   = DB::connection($this->conection_logins)
                ->table('bvc_rates_social_benefits')
                ->where('period',$datosprestaciones->año)
                ->where('month',$datosprestaciones->mes)
                ->first();

                if($bcvtasa){

                $datosprestaciones->tasaaver = $bcvtasa->rate_average_a_p;

                }else{

                    $bcvtasa   = DB::connection($this->conection_logins)
                    ->table('bvc_rates_social_benefits')
                    ->orderBy('id','DESC')
                    ->first();

                    $datosprestaciones->tasaaver = $bcvtasa->rate_average_a_p;

                }



            }


            $pdf = $pdf->loadView('pdf.prestations',compact('employee','company','tipo','datospresta','datenow'))->setPaper('a4');

            return $pdf->stream();

        }else{

            return redirect('nominaparts/utilidades')->withDelete('No Posee Registro de ese año!');

        }



        }


        if($tipo == 'ARC'){
            $idempleado = $employee;
            $pdf = App::make('dompdf.wrapper');
            $company = Company::on(Auth::user()->database_name)->find(1);

            $employee = Employee::on(Auth::user()->database_name)
            ->join('positions','positions.id','position_id') // Buscamos el empleado
            ->where('employees.id','=',$idempleado)
            ->first();


            $date = Carbon::now();
            $years = $date->format('Y');

            $años = $years - 1;


           $datospresta = DB::connection(Auth::user()->database_name)
            ->table('nomina_calculations AS a')
            ->join('nominas as b', 'a.id_nomina','b.id')
            ->where('a.id_employee',$idempleado)
            ->where(DB::raw('SUBSTR(b.date_end,1,4)'), $años)
            ->wherein('a.id_nomina_concept', ['2','3','4'])
            ->select(DB::raw('SUBSTR(b.date_end,1,4) AS año'), DB::raw('SUBSTR(b.date_end,6,2) AS mes'), DB::raw('sum(a.amount) as monto'), 'a.id_nomina_concept')
            ->groupBy(DB::raw('SUBSTR(b.date_end,1,4)') ,  DB::raw('SUBSTR(b.date_end,6,2)'),  'a.id_nomina_concept')
            ->get();



          $pdf = $pdf->loadView('pdf.prestations',compact('employee','company','tipo','datospresta','años'))->setPaper('a4');

          return $pdf->stream();

        }



    }




    function balancecomprobacion(Request $request)
    {


        if($request->tipo == 'balancecomprobacion'){

        $tipo = $request->tipo;
        $ini = $request->ini;
        $fin = $request->fin;

        $arreglo = decrypt($request->employee);

        $pdf = App::make('dompdf.wrapper');

           $company = Company::on(Auth::user()->database_name)->find(1);

           $date = Carbon::now();
           $datenow = $date->format('Y-m-d');

          $pdf = $pdf->loadView('pdf.prestations',compact('company','tipo','arreglo','datenow','ini','fin'));

          return $pdf->stream();

        }



    }




}
