<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Nomina;
use App\NominaCalculation;
use App\NominaConcept;
use App\NominaFormula;
use App\NominaBasesCalcs;
use App\Profession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NominaCalculationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($id_nomina,$id_employee)
    {
        
        $user       =   auth()->user();
        $users_role =   $user->role_id;
        if($users_role == '1'){
            $nomina      =   Nomina::on(Auth::user()->database_name)->find($id_nomina);
            $employee    =   Employee::on(Auth::user()->database_name)->find($id_employee);
            if(isset($nomina)){
                if(isset($employee)){

                        $nominacalculations   =   NominaCalculation::on(Auth::user()->database_name)
                                                                    ->join('nomina_concepts','nomina_concepts.id','nomina_calculations.id_nomina_concept')
                                                                    ->where('id_nomina', $id_nomina)
                                                                    ->where('id_employee', $id_employee)
                                                                    ->orderby('nomina_concepts.sign','asc')
                                                                    ->select('nomina_calculations.*')
                                                                    ->get();

                        $new_format = Carbon::parse($nomina->date_begin);
                        
                        
                        $nomina->date_format = $new_format->format('M Y');    
                        
                        $nomina->date_begin = $new_format->format('d-m-Y');   

                    return view('admin.nominacalculations.index',compact('nominacalculations','nomina','employee'));

                }else{
                    return redirect('/nominacalculations')->withDanger('No se encuentra al Empleado!');
                }
            }else{
                return redirect('/nominacalculations')->withDanger('No se encuentra la Nomina!');
            }
           
        }elseif($users_role == '2'){
            return view('admin.index');
        }

    
        
      
    }

    

    public function create($id_nomina,$id_employee)
    {
       
        $nomina      =   Nomina::on(Auth::user()->database_name)->find($id_nomina);
        $employee    =   Employee::on(Auth::user()->database_name)->find($id_employee);

        if(isset($nomina)){
            if(isset($employee)){

                $nominaconcepts   =   NominaConcept::on(Auth::user()->database_name)->orderBy('description','asc')->get();
               
                return view('admin.nominacalculations.create',compact('nominaconcepts','nomina','employee'));

            }else{
                return redirect('/nominacalculations')->withDanger('No se encuentra al Empleado!');
            }
        }else{
            return redirect('/nominacalculations')->withDanger('No se encuentra la Nomina!');
        }
    }

    public function selectemployee($id)
    {

        $var  = NominaCalculation::on(Auth::user()->database_name)->find($id);

        $employees = Employee::on(Auth::user()->database_name)->where('status','NOT LIKE','X')->where('profession_id',$var->id_profession)->get();

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');

       // dd($var);
        return view('admin.nominacalculations.selectemployee',compact('var','employees','datenow'));
        
    }

    public function store(Request $request)
    {
       // dd($request);
       
        $data = request()->validate([
           
            'id_nomina'     =>'required',
            'id_nomina_concept'       =>'required|max:60',
            'id_employee'              =>'required',
            
        ]);

        $nomina_calculation = new NominaCalculation();
        $nomina_calculation->setConnection(Auth::user()->database_name);
        $amount = request('monto');

        $nomina_calculation->id_nomina = request('id_nomina');
        $nomina_calculation->id_nomina_concept = request('id_nomina_concept');
        $nomina_calculation->id_employee = request('id_employee');

       
        $nomina_calculation->number_receipt = 0;
        
        $nomina_calculation->type = 'No';

        $nomina = Nomina::on(Auth::user()->database_name)->find($nomina_calculation->id_nomina);
        $employee = Employee::on(Auth::user()->database_name)->find($nomina_calculation->id_employee);
        $nomina_concept = NominaConcept::on(Auth::user()->database_name)->find($nomina_calculation->id_nomina_concept);
        $nominabases  =  NominaBasesCalcs::on(Auth::user()->database_name)->find(1);
        
        
        $days = request('days');
        $hours = request('hours');
        $cantidad = str_replace(',', '.', str_replace('.', '', request('cantidad')));
         
        if(isset($days)){
            if($days != 0){
                $nomina_calculation->days = $days;
            }else{
                $nomina_calculation->days = 0;
            }
        }else{
            $nomina_calculation->days = 0;
        }

        if(isset($hours)){
            if($hours != 0){
                $nomina_calculation->hours = $hours;
            }else{
                $nomina_calculation->hours = 0;
            }
        }else{
            $nomina_calculation->hours = 0;
        }

        if(isset($cantidad)){
            if($cantidad != 0){
                $nomina_calculation->cantidad = $cantidad;
            }else{
                $nomina_calculation->cantidad = 1;
            }
        }else{
            $nomina_calculation->cantidad = 1;
        }

        
        $nomina_calculation->voucher = 0;


        $nomina_calculation->status =  "1";


        if(isset($amount)){
            $nomina_calculation->amount = $amount * $cantidad;
        }else{
            $nomina_calculation->amount = 0;
        }
       
        $nomina_calculation->save();

        return redirect('/nominacalculations/index/'.$nomina_calculation->id_nomina.'/'.$nomina_calculation->id_employee.'')->withSuccess('Registro Exitoso!');
    }

   

    public function calcular_cantidad_de_lunes($nomina)
    {
        $fechaInicio= strtotime($nomina->date_begin);
        $fechaFin= strtotime($nomina->date_end);
       
        $cantidad_de_dias_lunes = 0;
        //Recorro las fechas y con la función strotime obtengo los lunes
        for($i=$fechaInicio; $i<=$fechaFin; $i+=86400){
            //Sacar el dia de la semana con el modificador N de la funcion date
            
            $dia = date('N', $i);
            if($dia==1){
                $cantidad_de_dias_lunes += 1;
            }
        }

        return $cantidad_de_dias_lunes;
    }

    public function calcularopracion($operador1,$operador2,$operacion = '+') {
        switch ($operacion) {
            case '**':
                $result = $operador1*$operador2;
                break;
            case '*':
                $result = $operador1*$operador2;
                break;
            case '/':
                if ($operador2 != 0) {
                    $result = $operador1/$operador2;
                } else {
                    $result = 0;
                }
                break;
            case '+':
                $result = $operador1+$operador2;
                break;
            case '-':
                $result = $operador1-$operador2;
                break;
            default:
                $result = $operador1+$operador2;
                break;
        }
        return $result;
    }

    public function resolver($operacion,$a_variables) {
        $a_param2 = ['',''];
        $return = 0;
        foreach ($a_variables as $key => $value) {
            $sustituir = '{{'.$key.'}}';
            $operacion = str_replace($sustituir, $value,$operacion);
        }
        $a_param = explode(' ', $operacion);
        if (count($a_param) <3 ) {
            $return = $a_param[0];
        } else {
            $return = $this->calcularopracion($a_param[0],$a_param[2],$a_param[1]);
    
            $j = 0;
            $a_param2 = ['',''];
            $tope = count($a_param);
            for ($i=3; $i < $tope ; $i++) { 
                if ($j > 1) {
                    $return = $this->calcularopracion($return,$a_param2[1],$a_param2[0]);
                    $a_param2 = ['',''];
                    $j=0;
                }
                $a_param2[$j] = $a_param[$i];
                $j += 1; 
            }
        }
        if ($a_param2[1] != '') {
            $return = $this->calcularopracion($return,$a_param2[1],$a_param2[0]);
        }
        return $return;
    }

    public function addNominaCalculation($nomina,$nominaconcept,$employee,$nomina_calculation,$nominabases)
    {
        
        $amount = -1;

            if(($nomina->type == "Primera Quincena") || ($nomina->type == "Segunda Quincena")){
                if(isset($nominaconcept->id_formula_q)){
                    $amount = $this->formula($nominaconcept->id_formula_q,$employee,$nomina,$nomina_calculation,$nominabases);
                }
                
            }else if($nomina->type == "Mensual"){
                if(isset($nominaconcept->id_formula_m)){
                    $amount = $this->formula($nominaconcept->id_formula_m,$employee,$nomina,$nomina_calculation,$nominabases);
                }

            }else if($nomina->type == "Especial"){

                    if(isset($nominaconcept->id_formula_e)){
                        $amount = $this->formula($nominaconcept->id_formula_m,$employee,$nomina,$nomina_calculation,$nominabases);
                    }

            }else if($nomina->type == "Semanal"){
                if(isset($nominaconcept->id_formula_s)){
                    $amount = $this->formula($nominaconcept->id_formula_s,$employee,$nomina,$nomina_calculation,$nominabases);
                }
            }
           
            if ($nomina->asignation == 'S') {
                
                $amount = $this->formula($nominaconcept->id_formula_a,$employee,$nomina,$nomina_calculation,$nominabases);
            }


           return $amount;
        
        
    }

    


    public function formula($id_formula,$employee,$nomina,$nomina_calculation,$nominabases)
    {

        
        $lunes = 0;
        $hours = 0;
        $days = 0;
        $cestaticket = 0;
        

        if(isset($nomina_calculation->days)){
            if($nomina_calculation->days != 0){
                $days = $nomina_calculation->days;
            }
        }

        if(isset($nomina_calculation->hours)){
            if($nomina_calculation->hours != 0){
                $hours = $nomina_calculation->hours;
            }
        }

        if(isset($nomina_calculation->cantidad)){
            if($nomina_calculation->cantidad != 0){
                $cestaticket = $nomina_calculation->cantidad;
            }
        }

        $nominaconcepts = NominaFormula::on(Auth::user()->database_name)->find($id_formula);
        
        $operacion = $nominaconcepts->description;


        $lunes = $this->calcular_cantidad_de_lunes($nomina);

		$variables = ["sueldo"=>$employee->monto_pago,"lunes"=>$lunes,"tasa"=>$nomina->rate,"asignacion"=>$employee->asignacion_general,"cestatickets"=>$nominabases->amount_cestatickets];
        //$variables = ["sueldo"=>$monto_pago, "horas"=>0, "dias"=>0, "horas_trabajadas"=>$horas_trabajadas, "horas_faltadas"=>$horas_faltadas, "dias_trabajados"=>$dias_trabajados, "dias_faltados"=>$dias_faltados,  "lunes"=>$lunes];
		$total = $this->resolver($operacion,$variables);


        if($total){
            $total = $total;
        } else {
            $total = 0; 
        }

        return $total;
    }



    public function edit($id)
    {

        $nomina_calculation  = NominaCalculation::on(Auth::user()->database_name)->find($id);

        $nomina      =   Nomina::on(Auth::user()->database_name)->find($nomina_calculation->id_nomina);
        $employee    =   Employee::on(Auth::user()->database_name)->find($nomina_calculation->id_employee);
        $nomina_concept      =   NominaConcept::on(Auth::user()->database_name)->find($nomina_calculation->id_nomina_concept);

        $nominaconcepts   =   NominaConcept::on(Auth::user()->database_name)->orderBy('description','asc')->get();

       
        return view('admin.nominacalculations.edit',compact('nomina_calculation','nomina','employee','nomina_concept','nominaconcepts'));
    }

   


    public function update(Request $request,$id)
    {
       
        $vars =  NominaCalculation::on(Auth::user()->database_name)->find($id);
        $var_status = $vars->status;
      
        $data = request()->validate([
           
            'id_nomina'     =>'required',
            'id_nomina_concept'       =>'required|max:60',
            'id_employee'              =>'required',
            
        ]);

        $nomina_calculation = NominaCalculation::on(Auth::user()->database_name)->findOrFail($id);


        $nomina_calculation->id_nomina = request('id_nomina');
        $nomina_calculation->id_nomina_concept = request('id_nomina_concept');
        $nomina_calculation->id_employee = request('id_employee');
       
        $nomina_calculation->number_receipt = 0;
        $nomina_calculation->status = 1;
       // $nomina_calculation->type = 'No';

        $nomina = Nomina::on(Auth::user()->database_name)->find($nomina_calculation->id_nomina);
        $employee = Employee::on(Auth::user()->database_name)->find($nomina_calculation->id_employee);
        $nomina_concept = NominaConcept::on(Auth::user()->database_name)->find($nomina_calculation->id_nomina_concept);
        $nominabases  =  NominaBasesCalcs::on(Auth::user()->database_name)->find(1);  
        
        $days = request('days');
        $hours = request('hours');
        $cantidad = str_replace(',', '.', str_replace('.', '', request('cantidad')));
         
        if(isset($days)){
            if($days != 0){
                $nomina_calculation->days = $days;
            }else{
                $nomina_calculation->days = 0;
            }
        }else{
            $nomina_calculation->days = 0;
        }

        if(isset($hours)){
            if($hours != 0){
                $nomina_calculation->hours = $hours;
            }else{
                $nomina_calculation->hours = 0;
            }
        }else{
            $nomina_calculation->hours = 0;
        }

        if(isset($cantidad)){
            if($cantidad != 0){
                $nomina_calculation->cantidad = $cantidad;
            }else{
                $nomina_calculation->cantidad = 0;
            }
        }else{
            $nomina_calculation->cantidad = 0;
        }

        
        $nomina_calculation->voucher = 0;


        $nomina_calculation->status =  "1";

        $amount = $this->addNominaCalculation($nomina,$nomina_concept,$employee,$nomina_calculation,$nominabases);

        if(isset($amount)){
            $nomina_calculation->amount = $amount;
        }else{
            $nomina_calculation->amount = 0;
        }
       
       
        $nomina_calculation->save();


        return redirect('/nominacalculations/index/'.$nomina_calculation->id_nomina.'/'.$nomina_calculation->id_employee.'')->withSuccess('Actualización Exitosa!');
  
    }



    public function listformula(Request $request, $id_concept = null,$id_nomina = null,$id_employe = null){
        //validar si la peticion es asincrona
        if($request->ajax()){
            try{
                $formula_q = DB::connection(Auth::user()->database_name)->table('nomina_concepts')
                                                        ->join('nomina_formulas', 'nomina_formulas.id', '=', 'nomina_concepts.id_formula_q')
                                                        ->where('nomina_concepts.id', $id_concept)
                                                        ->select('nomina_formulas.description as description')
                                                        ->get(); 
                
  
                $nomina = Nomina::on(Auth::user()->database_name)->find($id_nomina);
                $employee = Employee::on(Auth::user()->database_name)->find($id_employe);
                $nominabases  =  NominaBasesCalcs::on(Auth::user()->database_name)->find(1);
                $nomina_concept = NominaConcept::on(Auth::user()->database_name)->find($id_concept);
                $nominaconcepts = NominaFormula::on(Auth::user()->database_name)->find($nomina_concept->id_formula_q);
                
                $operacion = $nominaconcepts->description;

                $lunes = $this->calcular_cantidad_de_lunes($nomina);

                $variables = ["sueldo"=>$employee->monto_pago,"lunes"=>$lunes,"tasa"=>$nomina->rate,"asignacion"=>$employee->asignacion_general,"cestatickets"=>$nominabases->amount_cestatickets];
                //$variables = ["sueldo"=>$monto_pago, "horas"=>0, "dias"=>0, "horas_trabajadas"=>$horas_trabajadas, "horas_faltadas"=>$horas_faltadas, "dias_trabajados"=>$dias_trabajados, "dias_faltados"=>$dias_faltados,  "lunes"=>$lunes];
                $total = $this->resolver($operacion,$variables);


                if($total){
                    $total = $total;
                } else {
                    $total = 0; 
                }
                
                $formula_q[0]->amount = $total;
  

                return response()->json($formula_q,200);
            }catch(Throwable $th){
                return response()->json(false,500);
            }
        }
        
    }
    public function listformulamensual(Request $request, $id_concept = null,$id_nomina = null,$id_employe = null){
        //validar si la peticion es asincrona
        if($request->ajax()){
            try{
                $formula_q = DB::connection(Auth::user()->database_name)->table('nomina_concepts')
                                                        ->join('nomina_formulas', 'nomina_formulas.id', '=', 'nomina_concepts.id_formula_m')
                                                        ->where('nomina_concepts.id', $id_concept)
                                                        ->select('nomina_formulas.description as description')
                                                        ->get(); 

                $nomina = Nomina::on(Auth::user()->database_name)->find($id_nomina);
                $employee = Employee::on(Auth::user()->database_name)->find($id_employe);
                $nominabases  =  NominaBasesCalcs::on(Auth::user()->database_name)->find(1);
                $nomina_concept = NominaConcept::on(Auth::user()->database_name)->find($id_concept);
                $nominaconcepts = NominaFormula::on(Auth::user()->database_name)->find($nomina_concept->id_formula_m);
                
                $operacion = $nominaconcepts->description;

                $lunes = $this->calcular_cantidad_de_lunes($nomina);

                $variables = ["sueldo"=>$employee->monto_pago,"lunes"=>$lunes,"tasa"=>$nomina->rate,"asignacion"=>$employee->asignacion_general,"cestatickets"=>$nominabases->amount_cestatickets];
                //$variables = ["sueldo"=>$monto_pago, "horas"=>0, "dias"=>0, "horas_trabajadas"=>$horas_trabajadas, "horas_faltadas"=>$horas_faltadas, "dias_trabajados"=>$dias_trabajados, "dias_faltados"=>$dias_faltados,  "lunes"=>$lunes];
                $total = $this->resolver($operacion,$variables);


                if($total){
                    $total = $total;
                } else {
                    $total = 0; 
                }
                
                $formula_q[0]->amount = $total;
                                                          
                                                   
                return response()->json($formula_q,200);
            }catch(Throwable $th){
                return response()->json(false,500);
            }
        }
        
    }
    public function listformulasemanal(Request $request, $id_concept = null,$id_nomina = null,$id_employe = null){
        //validar si la peticion es asincrona
        if($request->ajax()){
            try{
                $formula_q = DB::connection(Auth::user()->database_name)->table('nomina_concepts')
                                                        ->join('nomina_formulas', 'nomina_formulas.id', '=', 'nomina_concepts.id_formula_s')
                                                        ->where('nomina_concepts.id', $id_concept)
                                                        ->select('nomina_formulas.description as description')
                                                        ->get(); 
                
                $nomina = Nomina::on(Auth::user()->database_name)->find($id_nomina);
                $employee = Employee::on(Auth::user()->database_name)->find($id_employe);
                $nominabases  =  NominaBasesCalcs::on(Auth::user()->database_name)->find(1);
                $nomina_concept = NominaConcept::on(Auth::user()->database_name)->find($id_concept);
                $nominaconcepts = NominaFormula::on(Auth::user()->database_name)->find($nomina_concept->id_formula_s);
                
                $operacion = $nominaconcepts->description;

                $lunes = $this->calcular_cantidad_de_lunes($nomina);

                $variables = ["sueldo"=>$employee->monto_pago,"lunes"=>$lunes,"tasa"=>$nomina->rate,"asignacion"=>$employee->asignacion_general,"cestatickets"=>$nominabases->amount_cestatickets];
                //$variables = ["sueldo"=>$monto_pago, "horas"=>0, "dias"=>0, "horas_trabajadas"=>$horas_trabajadas, "horas_faltadas"=>$horas_faltadas, "dias_trabajados"=>$dias_trabajados, "dias_faltados"=>$dias_faltados,  "lunes"=>$lunes];
                $total = $this->resolver($operacion,$variables);


                if($total){
                    $total = $total;
                } else {
                    $total = 0; 
                }
                
                $formula_q[0]->amount = $total;  

                return response()->json($formula_q,200);
            }catch(Throwable $th){
                return response()->json(false,500);
            }
        }
        
    }


    public function listformulaespecial(Request $request, $id_concept = null,$id_nomina = null,$id_employe = null){
        //validar si la peticion es asincrona
        if($request->ajax()){
            try{
                $formula_e = DB::connection(Auth::user()->database_name)->table('nomina_concepts')
                                                        ->join('nomina_formulas', 'nomina_formulas.id', '=', 'nomina_concepts.id_formula_e')
                                                        ->where('nomina_concepts.id', $id_concept)
                                                        ->select('nomina_formulas.description as description')
                                                        ->get(); 
                
                $nomina = Nomina::on(Auth::user()->database_name)->find($id_nomina);
                $employee = Employee::on(Auth::user()->database_name)->find($id_employe);
                $nominabases  =  NominaBasesCalcs::on(Auth::user()->database_name)->find(1);
                $nomina_concept = NominaConcept::on(Auth::user()->database_name)->find($id_concept);
                $nominaconcepts = NominaFormula::on(Auth::user()->database_name)->find($nomina_concept->id_formula_e);
                
                $operacion = $nominaconcepts->description;

                $lunes = $this->calcular_cantidad_de_lunes($nomina);

                $variables = ["sueldo"=>$employee->monto_pago,"lunes"=>$lunes,"tasa"=>$nomina->rate,"asignacion"=>$employee->asignacion_general,"cestatickets"=>$nominabases->amount_cestatickets];
                //$variables = ["sueldo"=>$monto_pago, "horas"=>0, "dias"=>0, "horas_trabajadas"=>$horas_trabajadas, "horas_faltadas"=>$horas_faltadas, "dias_trabajados"=>$dias_trabajados, "dias_faltados"=>$dias_faltados,  "lunes"=>$lunes];
                $total = $this->resolver($operacion,$variables);


                if($total){
                    $total = $total;
                } else {
                    $total = 0; 
                }
                
                $formula_e[0]->amount = $total;               

                return response()->json($formula_e,200);
            }catch(Throwable $th){
                return response()->json(false,500);
            }
        }
        
    }



    public function destroy(Request $request,$id)
    {
       
        $nomina_calculation = NominaCalculation::on(Auth::user()->database_name)->find($id);
        
        $nomina_calculation->delete();
        return redirect('/nominacalculations/index/'.$nomina_calculation->id_nomina.'/'.$nomina_calculation->id_employee.'')->withDanger('Se ha Eliminado Correctamente!');
  
    }
}
