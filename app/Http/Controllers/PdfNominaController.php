<?php

namespace App\Http\Controllers;

use App;
use App\Employee;
use App\Nomina;
use App\NominaCalculation;
use App\NominaConcept;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PdfNominaController extends Controller
{

    public function create_recibo_vacaciones()
    {
        $employees = Employee::on(Auth::user()->database_name)->where('status','NOT LIKE','X')->orderBY('nombres','asc')->get();

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
        

 
        $nomina = Nomina::on(Auth::user()->database_name)->find($id_nomina);

        if(isset($nomina)){
            $nomina_calculation = NominaCalculation::on(Auth::user()->database_name)->where('id_nomina',$nomina->id)
                                                    ->orderBy('id_employee','asc')->get();                                    
        }else{
            return redirect('/nominas')->withDanger('El empleado no tiene ninguna nomina registrada');
        } 
        
       
        $pdf = $pdf->loadView('pdf.print_calculation_all',compact('datenow','nomina','nomina_calculation'));
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


        if(isset($nomina)){
            
           
            $nomina_calculation_asignacion = NominaCalculation::on(Auth::user()->database_name)
            ->join('nomina_concepts','nomina_concepts.id','nomina_calculations.id_nomina_concept')
            ->join('employees','employees.id','nomina_calculations.id_employee')
            ->where('nomina_concepts.sign','A')
            ->where('id_nomina',$nomina->id)
            ->select('employees.nombres','employees.apellidos','employees.asignacion_general','employees.monto_pago',DB::connection(Auth::user()->database_name)->raw('SUM(nomina_calculations.amount) as total_asignacion'))
            ->groupBy('employees.nombres','employees.apellidos','employees.asignacion_general','employees.monto_pago')
            ->get();           

            $nomina_calculation_deduccion = NominaCalculation::on(Auth::user()->database_name)
                        ->join('nomina_concepts','nomina_concepts.id','nomina_calculations.id_nomina_concept')
                        ->join('employees','employees.id','nomina_calculations.id_employee')
                        ->where('id_nomina',$nomina->id)
                        ->where('nomina_concepts.sign','D')
                        ->select('employees.nombres','employees.apellidos','employees.asignacion_general','employees.monto_pago',DB::connection(Auth::user()->database_name)->raw('SUM(nomina_calculations.amount) as total_deduccion'))
                        ->groupBy('employees.nombres','employees.apellidos','employees.asignacion_general','employees.monto_pago')
                        ->get();     

            $nomina_calculation_sso = NominaCalculation::on(Auth::user()->database_name)
                        ->join('employees','employees.id','nomina_calculations.id_employee')
                        ->where('id_nomina',$nomina->id)
                        ->where('id_nomina_concept',19)
                        ->select('employees.nombres','employees.apellidos','nomina_calculations.amount')
                        ->groupBy('employees.nombres','employees.apellidos','nomina_calculations.amount')
                        ->get();     
            $nomina_calculation_faov = NominaCalculation::on(Auth::user()->database_name)
                        ->join('employees','employees.id','nomina_calculations.id_employee')
                        ->where('id_nomina',$nomina->id)
                        ->where('id_nomina_concept',23)
                        ->select('employees.nombres','employees.apellidos','nomina_calculations.amount')
                        ->groupBy('employees.nombres','employees.apellidos','nomina_calculations.amount')
                        ->get();     
           
        }else{
            return redirect('/nominas')->withDanger('El empleado no tiene ninguna nomina registrada');
        } 
        
        
       
        $pdf = $pdf->loadView('pdf.print_payroll_summary_all',compact('nomina_calculation_sso','nomina_calculation_faov','bcv','datenow','nomina','nomina_calculation_asignacion','nomina_calculation_deduccion'));
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
}
