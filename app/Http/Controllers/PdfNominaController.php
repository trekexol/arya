<?php

namespace App\Http\Controllers;

use App;
use App\Employee;
use App\Nomina;
use App\NominaCalculation;
use App\NominaBasesCalcs;
use App\NominaConcept;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class PdfNominaController extends Controller
{

    public function create_recibo_vacaciones()
    {
        $employees = Employee::on(Auth::user()->database_name)->where('status','NOT LIKE','X')->where('status','NOT LIKE','5')->orderBY('nombres','asc')->get();

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');


        $dateend =  date("Y-m-d",strtotime($date."+ 15 days"));


        return view('admin.nominas.create_recibo_vacaciones',compact('employees','datenow','dateend'));
    }


    public function create_recibo_prestaciones()
    {
        $employees = Employee::on(Auth::user()->database_name)->where('status','NOT LIKE','X')->orderBY('nombres','asc')->get();

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');

        return view('admin.nominas.create_recibo_prestaciones',compact('employees','datenow'));
    }

    public function create_recibo_utilidades()
    {
        $employees = Employee::on(Auth::user()->database_name)->where('status','NOT LIKE','X')->orderBY('nombres','asc')->get();

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');

        $dateend =  date("Y-m-d",strtotime($date."+ 15 days"));

        return view('admin.nominas.create_recibo_utilidades',compact('employees','datenow','dateend'));
    }


    public function create_recibo_liquidacion_auto()
    {
        $employees = Employee::on(Auth::user()->database_name)->where('status','NOT LIKE','X')->orderBY('nombres','asc')->get();

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');

        $dateend =  date("Y-m-d",strtotime($date."+ 15 days"));

        return view('admin.nominas.create_recibo_liquidacion_auto',compact('employees','datenow','dateend'));
    }




    function imprimirVacaciones(Request $request){

        $guardar = request('guardar');

        $pdf = App::make('dompdf.wrapper');

        $employee = Employee::on(Auth::user()->database_name)->find(request('id_employee'));

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');


        if(isset($employee)){

            $employee->date_begin = request('date_begin');
            $employee->date_end = request('date_end');
            $employee->days = request('days');
            $employee->bono = request('bono');
            //SE CALCULA LA CANTIDAD DE DIAS SABADOS, DOMINGOS Y FERIADOS

            $enable_holidays = request('enable_holidays');

            if(isset($enable_holidays)){
                $total_feriados = $this->calcular_cantidad_de_feriados($employee->date_begin,$employee->date_end);
                $employee->holidays = $total_feriados;
            }

            //---------------------------------
            $employee->mondays = request('monday');


            $sin_formato_lph = str_replace(',', '.', str_replace('.', '', request('lph')));

            $employee->lph = $sin_formato_lph;

            $pdf = $pdf->loadView('pdf.bono_vacaciones',compact('employee','datenow'));

            if(isset($guardar)){
                return $pdf->download('vacaciones.pdf');
            }

            return $pdf->stream();

        }else{
            return redirect('/nominas')->withDanger('El empleado no existe');
        }

    }

    function imprimirPrestaciones(Request $request){

        $pdf = App::make('dompdf.wrapper');

        $employee = Employee::on(Auth::user()->database_name)->find(request('id_employee'));

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');


        if(isset($employee)){

            $employee->date_begin = request('date_begin');

            $ultima_nomina = Nomina::on(Auth::user()->database_name)->where('status','NOT LIKE','X')->where('id_profession',$employee->profession_id)
                                                ->latest()->first();

            if(isset($ultima_nomina)){
                $nomina_calculation = NominaCalculation::on(Auth::user()->database_name)->where('id_nomina',$ultima_nomina->id)->get();
            }else{
                return redirect('/nominas')->withDanger('El empleado no tiene ninguna nomina registrada');
            }

            $pdf = $pdf->loadView('pdf.prestaciones',compact('employee','datenow','ultima_nomina','nomina_calculation'));
            return $pdf->stream();

        }else{
            return redirect('/nominas')->withDanger('El empleado no existe');
        }

    }

    function print_nomina_calculation($id_nomina,$id_employee){

        $pdf = App::make('dompdf.wrapper');

        $employee = Employee::on(Auth::user()->database_name)->find($id_employee);

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');


        if(isset($employee)){

            $nomina = Nomina::on(Auth::user()->database_name)->find($id_nomina);

            if(isset($nomina)){
                $nomina_calculation = NominaCalculation::on(Auth::user()->database_name)->where('id_nomina',$nomina->id)
                                                        ->where('id_employee',$employee->id)->get();
            }else{
                return redirect('/nominas')->withDanger('El empleado no tiene ninguna nomina registrada');
            }

            $pdf = $pdf->loadView('pdf.print_calculation',compact('employee','datenow','nomina','nomina_calculation'));
            return $pdf->stream();

        }else{
            return redirect('/nominas')->withDanger('El empleado no existe');
        }


    }
    function print_nomina_calculation_all($id_nomina){

        $pdf = App::make('dompdf.wrapper');

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');

        $datos = array();

        $nomina = Nomina::on(Auth::user()->database_name)->find($id_nomina);

        if(isset($nomina)){
            $datosempleados = NominaCalculation::on(Auth::user()->database_name)
                                    ->join('employees','employees.id','id_employee')
                                    ->join('positions','positions.id','position_id')
                                    ->where('id_nomina',$nomina->id)
                                    ->select('fecha_ingreso','id_employee','id_empleado','apellidos','nombres','name')
                                    ->groupby('fecha_ingreso','id_employee','id_empleado','apellidos','nombres','name')
                                    ->orderby('id_empleado', 'DESC')
                                    ->get();



            foreach($datosempleados as $datosempleados){

            $datosdenomina = NominaCalculation::on(Auth::user()->database_name)
                ->join('nomina_concepts','nomina_concepts.id','id_nomina_concept')
                ->where('id_nomina',$nomina->id)
                ->select('id_employee','id_nomina_concept','amount','nomina_concepts.description','nomina_concepts.sign')
                ->groupby('id_employee','id_nomina_concept','amount','nomina_concepts.description','nomina_concepts.sign')
                ->where('id_employee',$datosempleados->id_employee)
                ->wherenotin('nomina_concepts.description',['Bono Medico'])
                ->orderby('nomina_concepts.sign','ASC')
                ->orderby('nomina_concepts.description','ASC')
                ->get();

                $nominaarreglo = array();

            foreach($datosdenomina as  $datosdenomina){

            $nominaarreglo[] = ['idcon' => $datosdenomina->id_nomina_concept,
                                'monto' => $datosdenomina->amount,
                                'description' => $datosdenomina->description,
                                'sign' => $datosdenomina->sign ];




             }


             $datos[] = ['cedula' => $datosempleados->id_empleado,
                                'nombres' => $datosempleados->apellidos.' '.$datosempleados->nombres,
                                'cargo' => $datosempleados->name,
                                'fecha' => $datosempleados->fecha_ingreso,
                            'datos' => $nominaarreglo];



            }







        }else{
            return redirect('/nominas')->withDanger('El empleado no tiene ninguna nomina registrada');
        }


        $pdf = $pdf->loadView('pdf.print_calculation_all',compact('datenow','nomina','datos'));
        return $pdf->stream();


    }

    function print_payrool_summary($id_nomina){

        $pdf = App::make('dompdf.wrapper');

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');



        $nomina = Nomina::on(Auth::user()->database_name)->find($id_nomina);


        if(isset($nomina)){
            $nomina_calculation_asignacion = NominaCalculation::on(Auth::user()->database_name)
                                                    ->join('nomina_concepts','nomina_concepts.id','nomina_calculations.id_nomina_concept')
                                                    ->join('employees','employees.id','nomina_calculations.id_employee')
                                                    ->where('nomina_concepts.sign','A')
                                                    ->where('id_nomina',$nomina->id)
                                                    ->select('employees.nombres','employees.apellidos',DB::connection(Auth::user()->database_name)->raw('SUM(nomina_calculations.amount) as total_asignacion'))
                                                    ->groupBy('employees.nombres','employees.apellidos')
                                                    ->get();
            $nomina_calculation_deduccion = NominaCalculation::on(Auth::user()->database_name)
                                                    ->join('nomina_concepts','nomina_concepts.id','nomina_calculations.id_nomina_concept')
                                                    ->join('employees','employees.id','nomina_calculations.id_employee')
                                                    ->where('id_nomina',$nomina->id)
                                                    ->where('nomina_concepts.sign','D')
                                                    ->select('employees.nombres','employees.apellidos',DB::connection(Auth::user()->database_name)->raw('SUM(nomina_calculations.amount) as total_deduccion'))
                                                    ->groupBy('employees.nombres','employees.apellidos')
                                                    ->get();


        }else{
            return redirect('/nominas')->withDanger('El empleado no tiene ninguna nomina registrada');
        }



        $pdf = $pdf->loadView('pdf.print_payroll_summary',compact('datenow','nomina','nomina_calculation_asignacion','nomina_calculation_deduccion'));
        return $pdf->stream();


    }

    function print_payrool_summary_all($id_nomina){

        $pdf = App::make('dompdf.wrapper');

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');
        $global = new GlobalController();
        $bcv = $global->search_bcv();


        $nomina = Nomina::on(Auth::user()->database_name)->find($id_nomina);

        if(isset($nomina->rate)&& $nomina->rate != 0){
            $bcv = $nomina->rate;
        }

        if(isset($nomina)){


            if ($nomina->nomina_type_id == '1'){
                $employees = Employee::on(Auth::user()->database_name)
                ->where('status','!=','X')
                ->where('status','!=','0')
                ->where('status','!=','5')
                ->orderby('nombres', 'asc')
                ->get();

            } else {
                $employees = Employee::on(Auth::user()->database_name)
                ->where('status','!=','X')
                ->where('status','!=','0')
                ->where('status','!=','5')
                ->where('nomina_type_id',$nomina->nomina_type_id)
                ->orderby('nombres', 'asc')
                ->get();

            }


            $nominaController = new NominaController();
            $lunes = $nominaController->calcular_cantidad_de_lunes($nomina);
            $nominabases  =  NominaBasesCalcs::on(Auth::user()->database_name)->find(1);

            //// nuevo metodo
            foreach($employees as $employee){

                $amount_total_asignacion = 0;

                $amount_total_deduccion_sso = 0;
                $amount_total_deduccion_faov = 0;
                $amount_total_deduccion_pie = 0;
                $amount_total_deduccion_ince = 0;

                $total_sso_patronal = 0;
                $total_faov_patronal = 0;
                $total_pie_patronal = 0;

                $amount_total_bono_medico = 0;
                $amount_total_bono_alim = 0;
                $amount_total_bono_transporte = 0;

                $amount_total_antc = 0;

                $amount_total_otras_asignaciones = 0;
                $amount_total_otras_deducciones = 0;

                $amount_total_asignacion_general = 0;
                $amount_total_asignacion_m_deducciones = 0;


                $calculos_nomina = DB::connection(Auth::user()->database_name)->table('nomina_calculations')
                ->where('id_nomina',$nomina->id)
                ->where('id_employee',$employee->id)
                ->get();


                foreach($calculos_nomina as $calculos) {
                        $concepto = '';

                        $concepto = DB::connection(Auth::user()->database_name)->table('nomina_concepts')
                        ->find($calculos->id_nomina_concept);


                        if (!empty($concepto)){

                            // Sueldo
                            if(($concepto->abbreviation == 'SUEM' or $concepto->abbreviation == 'SUES' or $concepto->abbreviation == 'SUEQ') and $concepto->sign == 'A'){
                                $amount_total_asignacion += $calculos->amount;
                            } else {
                                $amount_total_asignacion += 0;
                            }

                            if($concepto->abbreviation == 'ANTC' and $concepto->sign == 'A'){
                                $amount_total_antc += $calculos->amount;
                            } else {
                                $amount_total_antc += 0;
                            }

                            if($concepto->abbreviation == 'BALIM' and $concepto->sign == 'A'){
                                $amount_total_bono_alim += $calculos->amount;
                            } else {
                                $amount_total_bono_alim += 0;
                            }

                            // Asignaciones Generales
                            if($concepto->abbreviation == 'BMED' and $concepto->sign == 'A'){
                                $amount_total_bono_medico += $calculos->amount;
                            } else {
                                $amount_total_bono_medico += 0;

                            }

                            if($concepto->abbreviation == 'BTRN' and $concepto->sign == 'A'){
                                $amount_total_bono_transporte += $calculos->amount;
                            } else {
                                $amount_total_bono_transporte += 0;

                            }

                            // retenciones
                            if($concepto->abbreviation == 'SSO' and $concepto->sign == 'D'){
                                $amount_total_deduccion_sso += $calculos->amount;
                            } else {
                                $amount_total_deduccion_sso += 0;

                            }

                            if($concepto->abbreviation == 'FAOV' and $concepto->sign == 'D'){
                                $amount_total_deduccion_faov += $calculos->amount;
                            } else {
                                $amount_total_deduccion_faov += 0;

                            }

                            if($concepto->abbreviation == 'PIE' and $concepto->sign == 'D'){
                                $amount_total_deduccion_pie += $calculos->amount;
                            } else {
                                $amount_total_deduccion_pie += 0;

                            }

                            if($concepto->abbreviation == 'INCES' and $concepto->sign == 'D'){
                                $amount_total_deduccion_ince += $calculos->amount;
                            } else {
                                $amount_total_deduccion_ince += 0;

                            }

                            // Otras Asignaciones
                            if (($concepto->abbreviation != 'SUEM' or $concepto->abbreviation != 'SUES' or $concepto->abbreviation != 'SUEQ') and  $concepto->abbreviation != 'BALIM' and  $concepto->abbreviation != 'BMED' and  $concepto->abbreviation != 'BTRN' and $concepto->sign == 'A'){
                            $amount_total_otras_asignaciones += $calculos->amount;
                            } else {
                            $amount_total_otras_asignaciones += 0;

                            }

                            // Deducciones diferentes
                            if (($concepto->abbreviation != 'SSO' and $concepto->abbreviation != 'FAOV' and $concepto->abbreviation != 'PIE' and $concepto->abbreviation != 'INCES') and $concepto->sign == 'D') {
                                $amount_total_otras_deducciones += $calculos->amount;
                            } else {
                                $amount_total_otras_deducciones += 0;

                            }



                        } else {

                            $amount_total_asignacion += 0;
                            $amount_total_antc += 0;
                            $amount_total_otras_deducciones += 0;
                            $amount_total_otras_asignaciones += 0;
                            $amount_total_deduccion_ince += 0;
                            $amount_total_deduccion_pie += 0;
                            $amount_total_deduccion_faov += 0;
                            $amount_total_deduccion_sso += 0;
                            $amount_total_bono_transporte += 0;
                            $amount_total_bono_medico += 0;
                            $amount_total_bono_alim += 0;


                        }

                    }

                    //$amount_total_asignacion_general = $amount_total_asignacion + $amount_total_otras_asignaciones;
                    $amount_total_asignacion_m_deducciones = ($amount_total_asignacion + $amount_total_antc) - ($amount_total_deduccion_sso + $amount_total_deduccion_faov + $amount_total_deduccion_ince + $amount_total_deduccion_pie + $amount_total_otras_deducciones);


                    $employee->asignacion = $amount_total_asignacion;
                    $employee->otras_deducciones = $amount_total_otras_deducciones;
                    $employee->otras_asignaciones = $amount_total_otras_asignaciones;
                    $employee->deduccion_ince = $amount_total_deduccion_ince;
                    $employee->deduccion_pie = $amount_total_deduccion_pie;
                    $employee->deduccion_faov = $amount_total_deduccion_faov;
                    $employee->deduccion_sso = $amount_total_deduccion_sso;
                    $employee->bono_transporte = $amount_total_bono_transporte;
                    $employee->bono_medico = $amount_total_bono_medico;
                    $employee->bono_alim = $amount_total_bono_alim;
                    //$employee->total_asignacion_general = $amount_total_asignacion_general;
                    $employee->total_asignacion_m_deducciones = $amount_total_asignacion_m_deducciones;
            }

        }else{
            return redirect('/nominas')->withDanger('El empleado no tiene ninguna nomina registrada');
        }



        $pdf = $pdf->loadView('pdf.print_payroll_summary_all',compact('lunes','bcv','datenow','nomina','nominabases','employees'))->setPaper('letter', 'landscape');
        return $pdf->stream();


    }

    function imprimirUtilidades(Request $request){

        $guardar = request('guardar');

        $pdf = App::make('dompdf.wrapper');

        $employee = Employee::on(Auth::user()->database_name)->find(request('id_employee'));

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');


        if(isset($employee)){

            $employee->date_end = request('date_end');
            $employee->days = request('days');



            $pdf = $pdf->loadView('pdf.utilidades',compact('employee','datenow'));

            if(isset($guardar)){
                return $pdf->download('utilidades.pdf');
            }

            return $pdf->stream();

        }else{
            return redirect('/nominas')->withDanger('El empleado no existe');
        }

    }

    function imprimirLiquidacionAuto(Request $request){


        $guardar = request('guardar');

        $pdf = App::make('dompdf.wrapper');

        $employee = Employee::on(Auth::user()->database_name)->find(request('id_employee'));

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');


        if(isset($employee)){

            $employee->date_begin = request('date_begin');

            $employee->motivo = request('motivo');
            $employee->utilidad = request('utilidad');

            $employee->faov = request('faov');
            $employee->inces = request('inces');
            $employee->adicionales = request('adicionales');
            $employee->bono_alimenticio = request('bono_alimenticio');

            $employee->lunes = request('lunes');
            $employee->dias_no_laborados = request('dias_no_laborados');
            $employee->meses_utilidades = request('meses_utilidades');

            $sin_formato_otras_asignaciones = str_replace(',', '.', str_replace('.', '', request('otras_asignaciones')));
            $sin_formato_otras_deducciones = str_replace(',', '.', str_replace('.', '', request('otras_deducciones')));

            $employee->otras_asignaciones = $sin_formato_otras_asignaciones;
            $employee->otras_deducciones = $sin_formato_otras_deducciones;


            $ultima_nomina = Nomina::on(Auth::user()->database_name)->where('status','NOT LIKE','X')->where('id_profession',$employee->profession_id)
                                    ->latest()->first();

            if(!empty($ultima_nomina)) {

                $nomina_calculation = NominaCalculation::on(Auth::user()->database_name)->where('id_nomina',$ultima_nomina->id)->get();

            } else {

                $ultima_nomina = null;
                $nomina_calculation = null;
            }






            $pdf = $pdf->loadView('pdf.liquidacion',compact('employee','datenow','ultima_nomina','nomina_calculation'));

            if(isset($guardar)){
                return $pdf->download('liquidacion.pdf');
            }

            return $pdf->stream();

        }else{
            return redirect('/nominas')->withDanger('El empleado no existe');
        }

    }


    public function calcular_cantidad_de_feriados($date_begin,$date_end)
    {
        $fechaInicio= strtotime($date_begin);
        $fechaFin= strtotime($date_end);

        $cantidad_de_dias_lunes = 0;
        //Recorro las fechas y con la función strotime obtengo los lunes
        for($i=$fechaInicio; $i<=$fechaFin; $i+=86400){
            //Sacar el dia de la semana con el modificador N de la funcion date

            $dia = date('N', $i);
            if($dia==7){
                $cantidad_de_dias_lunes += 1;
            }
            if($dia==6){
                $cantidad_de_dias_lunes += 1;
            }
        }

        return $cantidad_de_dias_lunes;
    }




    public function diavacaciones(Request $request){

        if($request->ajax()){
            try{

            $employee = Employee::on(Auth::user()->database_name)->find($request->id_employee);

            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');

            $fechaex = explode('-',$employee->fecha_ingreso);
            $fechaexplode = explode('-',$datenow);

            return response()->json(View::make('admin.nominas.diavacaciones',compact('employee','fechaex','fechaexplode'))->render());



            }catch(\Throwable $th){
                return response()->json(false,500);
            }
        }


    }
}
