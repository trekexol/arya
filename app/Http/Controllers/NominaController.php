<?php

namespace App\Http\Controllers;

use App\Account;
use App\DetailVoucher;
use App\Employee;
use App\HeaderVoucher;
use App\Nomina;
use App\NominaType;
use App\NominaCalculation;
use App\NominaConcept;
use App\NominaFormula;
use App\Profession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NominaController extends Controller
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
    public function index()
    {
        
        $user       =   auth()->user();
        $users_role =   $user->role_id;
        $nomina_type = '';

        if($users_role == '1'){
           $nominas      =   Nomina::on(Auth::user()->database_name)->where('status','!=','X')->orderBy('id', 'desc')->get();
           

           foreach ($nominas as $key => $nomina) {
                $nomina_type = NominaType::on(Auth::user()->database_name)->find($nomina->nomina_type_id);
                $nomina->nomina_type_id_name = $nomina_type->name;
                
                $header_search = HeaderVoucher::on(Auth::user()->database_name)->where('id_nomina',$nomina->id)->where('status','!=','X')->first();

                if (!empty($header_search)) {
                    $check_exist = 'Existe';
                } else {
                    $check_exist = 'no existe';
                }
               
                $nomina->check_exist = $check_exist; 
           }

        }elseif($users_role == '2'){
            return view('admin.index');
        }

    
        return view('admin.nominas.index',compact('nominas','nomina_type'));
      
    }

    public function searchMovementNomina($id_nomina){
        $header = HeaderVoucher::on(Auth::user()->database_name)->where('id_nomina',$id_nomina)->where('status',1)->orderBy('id','desc')->first();
        
        if(isset($header)){
            $detail = new DetailVoucherController();
            return $detail->create("bolivares",$header->id);
        }


        return redirect('/nominas')->withDanger('No posee movimientos la Nomina !!');
    }

    public function create()
    {
        /*$professions = Profession::on(Auth::user()->database_name)->orderBY('id','asc')->get();*/
        $nomina_type = NominaType::on(Auth::user()->database_name)->get();
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');
        $global = new GlobalController();
        $bcv = $global->search_bcv();

        return view('admin.nominas.create',compact('nomina_type','datenow','bcv'));
    }

   
    public function selectemployee($id)
    {

        $var  = Nomina::on(Auth::user()->database_name)->find($id);

        $employees = Employee::on(Auth::user()->database_name)
        ->where('status','!=','X')
        ->where('status','!=','0')
        ->where('nomina_type_id',$var->nomina_type_id)->get();


        $nomina_type = NominaType::on(Auth::user()->database_name)->find($var->nomina_type_id);
        $nomina_type_id_name = $nomina_type->name;
    

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');

       // dd($var);
        return view('admin.nominas.selectemployee',compact('var','employees','datenow','nomina_type_id_name'));
        
    }

    public function recalculate($id_nomina)
    {
        $this->deleteNomina($id_nomina);

       return $this->calculate($id_nomina);
    }

    public function recalculatecont($id_nomina)
    {
        $this->deleteNominacont($id_nomina);

       return $this->calculatecont($id_nomina);
    }


    public function calculate($id_nomina)
    {

        $check_exist_calculation = NominaCalculation::on(Auth::user()->database_name)->where('id_nomina',$id_nomina)->first();
        $nomina = Nomina::on(Auth::user()->database_name)->find($id_nomina);
       
        //Chequea si hay calculos previos y pregunta si se desea recalcular la nomina
        if(isset($check_exist_calculation)){

            $nomina_type = '';

            $nominas      =   Nomina::on(Auth::user()->database_name)
            ->where('status','!=','X')
            ->orderBy('id', 'desc')->get();


            foreach ($nominas as $key => $nomina) {
                $nomina_type = NominaType::on(Auth::user()->database_name)->find($nomina->nomina_type_id);
                $nomina->nomina_type_id_name = $nomina_type->name;

                $header_search = HeaderVoucher::on(Auth::user()->database_name)->where('id_nomina',$nomina->id)->where('status','!=','X')->first();

                if (!empty($header_search)) {
                    $check_exist = 'Existe';
                } else {
                    $check_exist = 'no existe';
                }
               
                $nomina->check_exist = $check_exist; 
           }

           $exist_nomina_calculation = $nomina;

            return view('admin.nominas.index',compact('nominas','exist_nomina_calculation','nomina_type'));

         
        }


        
        
        $employees = Employee::on(Auth::user()->database_name)
        ->where('status','!=','X')
        ->where('status','!=','0')
        ->where('nomina_type_id',$nomina->nomina_type_id)->get();

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');

       
        $sum_employees = 0;
        $sum_employees_asignacion_general = 0;
        $sum_sso_patronal = 0;




        $lunes = $this->calcular_cantidad_de_lunes($nomina);

        $global = new GlobalController();
        $bcv = floatval($global->search_bcv());
        
        $nomina = Nomina::on(Auth::user()->database_name)->find($id_nomina);

        if(isset($nomina->rate) && $nomina->rate == 0){
            $nomina->rate = $bcv;
        }
       
        foreach($employees as $employee){
            $this->addNominaCalculation($nomina,$employee);
            $sum_employees ++;
            $sum_employees_asignacion_general += $employee->asignacion_general;
            $sum_sso_patronal += ($employee->monto_pago * 12)/52 * ($lunes * 0.10);
        }    

        
        return redirect('/nominas')->withSuccess('El calculo de la Nomina '.$nomina->description.' fue Exitoso!');
        
    }

    public function calculatecont($id_nomina)
    {

        
        $nomina = Nomina::on(Auth::user()->database_name)->find($id_nomina);
        
        //Chequea si hay comprovante  y pregunta si se desea recrearlos

        $header_search = HeaderVoucher::on(Auth::user()->database_name)->where('id_nomina',$id_nomina)->where('status','!=','X')->first();

        if(isset($header_search)){
            
            $nomina_type = '';

            $nominas      =   Nomina::on(Auth::user()->database_name)
            ->where('status','!=','X')
            ->orderBy('id', 'desc')->get();


            foreach ($nominas as $key => $nomina) {
                $nomina_type = NominaType::on(Auth::user()->database_name)->find($nomina->nomina_type_id);
                $nomina->nomina_type_id_name = $nomina_type->name;
                
                $header_search = HeaderVoucher::on(Auth::user()->database_name)->where('id_nomina',$nomina->id)->where('status','!=','X')->first();

                if (!empty($header_search)) {
                    $check_exist = 'Existe';
                } else {
                    $check_exist = 'no existe';
                }
               
                $nomina->check_exist = $check_exist; 
           }

            $exist_nomina_calculationcont = $header_search;

            return view('admin.nominas.index',compact('nominas','exist_nomina_calculationcont','nomina_type'));
        }

        
        $employees = Employee::on(Auth::user()->database_name)
        ->where('status','!=','X')
        ->where('status','!=','0')
        ->where('nomina_type_id',$nomina->nomina_type_id)->get();

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');

       
        $sum_employees = 0;
        $sum_employees_asignacion_general = 0;
        $sum_sso_patronal = 0;


        $lunes = $this->calcular_cantidad_de_lunes($nomina);

        $global = new GlobalController();
        $bcv = floatval($global->search_bcv());
        
        $nomina = Nomina::on(Auth::user()->database_name)->find($id_nomina);

        if(isset($nomina->rate) && $nomina->rate == 0){
            $nomina->rate = $bcv;
        }
       
        foreach($employees as $employee){
            $sum_employees ++;
            $sum_employees_asignacion_general += $employee->asignacion_general;
            $sum_sso_patronal += ($employee->monto_pago * 12)/52 * ($lunes * 0.10);
        }    

        $amount_total_nomina = $this->calculateAmountTotalNomina($nomina);

        $header_voucher  = new HeaderVoucher();
        $header_voucher->setConnection(Auth::user()->database_name);
        
        $header_voucher->id_nomina = $id_nomina;
        $header_voucher->description = "Nomina ".$nomina->description ?? '';
        $header_voucher->date = $datenow;
       
    
        $header_voucher->status =  "1";
    
        $header_voucher->save();


        /*MOVIMIENTO DE SUELDOS */
        
        $accounts_sueldos = DB::connection(Auth::user()->database_name)->table('accounts')
            ->where('code_one','=','6')
            ->where('description','LIKE', 'Sueldos y Salarios')
            ->first();
        

        $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_sueldos->id,$nomina->id,$amount_total_nomina,0);
        
        if($sum_employees_asignacion_general > 0) {

        if($nomina->type == "Segunda Quincena"){
            /*MOVIMIENTO DE BONO ALIMENTACION */
            $total_bono_alimentacion = 45 * $sum_employees;

            $accounts_alimentacion = DB::connection(Auth::user()->database_name)->table('accounts')
            ->where('code_one','=','6')
            ->where('description','LIKE', 'Bono de Alimentacion')
            ->first();

            $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_alimentacion->id,$nomina->id,$total_bono_alimentacion,0);
        }
       } else {
        $total_bono_alimentacion = 0;
       }
 
        $total_sso = $this->calculateAmountTotalSSO($nomina);
        $total_faov = $this->calculateAmountTotalFAOV($nomina);

        /*MOVIMIENTO DE Bono Medico */
        if($sum_employees_asignacion_general > 0) {
            $total_bono_medico = ($sum_employees_asignacion_general * $nomina->rate) - $amount_total_nomina - ($total_bono_alimentacion ?? 0) + $total_faov + $total_sso;
        
            $accounts_bono_medico = DB::connection(Auth::user()->database_name)->table('accounts')
            ->where('code_one','=','6')
            ->where('description','LIKE', 'Bono Medico')
            ->first();

            $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_bono_medico->id,$nomina->id,$total_bono_medico,0);

        } else {
            $total_bono_medico = 0;
        }
        /*MOVIMIENTO DE aporte patronal*/
                        
       
        $accounts_aporte_patronal = DB::connection(Auth::user()->database_name)->table('accounts')
        ->where('code_one','=','6')
        ->where('description','LIKE', 'Gasto por Aporte al FAOV Patronal')

        ->first();

        $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_aporte_patronal->id,$nomina->id,$amount_total_nomina * 0.02,0);


        $accounts_sso_patronal = DB::connection(Auth::user()->database_name)->table('accounts')
        ->where('code_one','=','6')
        ->where('description','LIKE', 'Gasto por Aporte al SSO Patronal')

        ->first();

        $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_sso_patronal->id,$nomina->id,$sum_sso_patronal,0);



        /*AHORA LOS MOVIMIENTOS POR PAGAR */

        $accounts_sueldos_por_pagar = DB::connection(Auth::user()->database_name)->table('accounts')
        ->where('code_one','=','2')
        ->where('description','LIKE', 'Sueldos por Pagar')
        ->first();

        $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_sueldos_por_pagar->id,$nomina->id,0,$amount_total_nomina - $total_faov - $total_sso);
        /*------------------------ */
        
        if($sum_employees_asignacion_general > 0) { 

            if($nomina->type == "Segunda Quincena"){
                $accounts_alimentacion_por_pagar = DB::connection(Auth::user()->database_name)->table('accounts')
                ->where('code_one','=','2')
                ->where('description','LIKE', 'Bono Alimentacion por Pagar')
                ->first();

                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_alimentacion_por_pagar->id,$nomina->id,0,$total_bono_alimentacion);
                /*------------------------ */
            }
        }

        $accounts_sso_por_pagar = DB::connection(Auth::user()->database_name)->table('accounts')
        ->where('code_one','=','2')
        ->where('description','LIKE', 'Retencion por Aporte al SSO empleados por Pagar')
        ->first();

        $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_sso_por_pagar->id,$nomina->id,0,$total_sso);
        /*------------------------ */
      

        $accounts_faov_por_pagar = DB::connection(Auth::user()->database_name)->table('accounts')
        ->where('code_one','=','2')
        ->where('description','LIKE', 'Retencion por Aporte al FAOV empleados por Pagar')
        ->first();

        $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_faov_por_pagar->id,$nomina->id,0,$total_faov);
        /*------------------------ */
        
        if($sum_employees_asignacion_general > 0) {
         $accounts_bono_medico_por_pagar = DB::connection(Auth::user()->database_name)->table('accounts')
         ->where('code_one','=','2')
         ->where('description','LIKE', 'Bono Medico por Pagar')
         ->first();
 
         $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_bono_medico_por_pagar->id,$nomina->id,0,$total_bono_medico);
         /*------------------------ */
        } else {

            $accounts_bono_medico_por_pagar = 0;  
        }
         $accounts_aporte_patronal = DB::connection(Auth::user()->database_name)->table('accounts')
         ->where('code_one','=','2')
         ->where('description','LIKE', 'Aportes por Pagar al FAOV Patronal')
         ->first();

         $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_aporte_patronal->id,$nomina->id,0,$amount_total_nomina * 0.02);


         $accounts_sso_patronal = DB::connection(Auth::user()->database_name)->table('accounts')
         ->where('code_one','=','2')
         ->where('description','LIKE', 'Aportes por Pagar al SSO Patronal')
         ->first();

         $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_sso_patronal->id,$nomina->id,0,$sum_sso_patronal);

        
        return redirect('/nominas')->withSuccess('El calculo de la Nomina '.$nomina->description.' fue Exitoso!');
        
    }
    


    public function calculateAmountTotalNomina($nomina){

       
        $amount_total_asignacion = DB::connection(Auth::user()->database_name)->table('nomina_calculations')
        ->where('id_nomina',$nomina->id)
        ->whereIn('id_nomina_concept',[2,3,4])
        ->sum('amount');
        

       /* $amount_total_deduccion = NominaCalculation::join('nomina_concepts','nomina_concepts.id','nomina_calculations.id_nomina_concept')
                                                    ->where('id_nomina',$nomina->id)
                                                    ->where('nomina_concepts.sign',"D")
                                                    ->sum('nomina_calculations.amount');*/
                                     
        return $amount_total_asignacion;/* - $amount_total_deduccion;*/

    }

    public function calculateAmountTotalSSO($nomina){

       
        $amount_total_sso =  DB::connection(Auth::user()->database_name)->table('nomina_calculations')
                                            ->where('id_nomina',$nomina->id)
                                            ->where('id_nomina_concept',19)
                                            ->sum('amount');

        return $amount_total_sso;

    }
   
    public function calculateAmountTotalFAOV($nomina){

       
        $amount_total_faov = DB::connection(Auth::user()->database_name)->table('nomina_calculations')
                                            ->where('id_nomina',$nomina->id)
                                            ->where('id_nomina_concept',23)
                                            ->sum('amount');

   
        return $amount_total_faov;

    }
  
    public function add_movement($bcv,$id_header,$id_account,$id_nomina,$debe,$haber){

        $detail = new DetailVoucher();
        $detail->setConnection(Auth::user()->database_name);
        $user       =   auth()->user();

        $detail->id_account = $id_account;
        $detail->id_header_voucher = $id_header;
        $detail->user_id = $user->id;
        $detail->tasa = $bcv;
       

      /*  $valor_sin_formato_debe = str_replace(',', '.', str_replace('.', '', $debe));
        $valor_sin_formato_haber = str_replace(',', '.', str_replace('.', '', $haber));*/


        $detail->debe = $debe;
        $detail->haber = $haber;
       
      
        $detail->status =  "C";

         /*Le cambiamos el status a la cuenta a M, para saber que tiene Movimientos en detailVoucher */
         
            $account = Account::on(Auth::user()->database_name)->findOrFail($detail->id_account);

            if($account->status != "M"){
                $account->status = "M";
                $account->save();
            }
         
    
        $detail->save();

    }

    public function addNominaCalculation($nomina,$employee)
    {
        
        if(($nomina->type == "Primera Quincena") || ($nomina->type == "Segunda Quincena")){
            
            $nominaconcepts = NominaConcept::on(Auth::user()->database_name)->where('type','LIKE','%Quincenal%')
                                                ->where('calculate','S')->get();
            /*$nominaconcepts_comun = NominaConcept::on(Auth::user()->database_name)->where('type','LIKE','%Quincenal%')
                                                ->where('calculate','S')->get();*/
        }

        if(($nomina->type == "Primera Quincena")){
            
            $nominaconcepts = NominaConcept::on(Auth::user()->database_name)
            ->where('type','LIKE','%Primera Quincena%')
            ->Orwhere('type','LIKE','%Quincenal%')
            ->where('calculate','S')->get();

        }else if(($nomina->type == "Segunda Quincena")){
            
            $nominaconcepts = NominaConcept::on(Auth::user()->database_name)
            ->where('type','LIKE','%Segunda Quincena%')
            ->Orwhere('type','LIKE','%Quincenal%')                       
            ->where('calculate','S')->get();
            
        }else if(($nomina->type == "Quincenal")){
            $nominaconcepts = NominaConcept::on(Auth::user()->database_name)->where('type','LIKE','%Quincenal%')
                                                ->where('calculate','S')->get();

        }else if(($nomina->type == "Mensual")){
            $nominaconcepts = NominaConcept::on(Auth::user()->database_name)->where('type','LIKE','%Mensual%')
                                                ->where('calculate','S')->get();

        }else if(($nomina->type == "Semanal")){
            $nominaconcepts = NominaConcept::on(Auth::user()->database_name)->where('type','LIKE','%Semanal%')
                                                ->where('calculate','S')->get();

        }else if(($nomina->type == "Especial")){
            $nominaconcepts = NominaConcept::on(Auth::user()->database_name)->where('type','LIKE','Especial')
                                                ->where('calculate','S')->get();
        }else{
            $nominaconcepts = NominaConcept::on(Auth::user()->database_name)->where('type','LIKE','%'.$nomina->type.'%')
                                                ->where('calculate','S')->get();
        }
       
        if(isset($nominaconcepts))
        {
            foreach($nominaconcepts as $nominaconcept){

                $vars = new NominaCalculation();
                $vars->setConnection(Auth::user()->database_name);

                $vars->id_nomina = $nomina->id;
                $vars->id_nomina_concept = $nominaconcept->id;
                $vars->id_employee = $employee->id;
            
                $vars->number_receipt = 0;
                
                $vars->type = 'No';

                $vars->days = 0;
                $vars->hours = 0;
                $vars->cantidad = 0;
        
                $amount = 0;
                $tiene_calculo = false;

                if(($nomina->type == "Primera Quincena") || ($nomina->type == "Segunda Quincena")){
                    if(isset($nominaconcept->id_formula_q)){
                        $tiene_calculo = true;
                        $amount = $this->formula($nominaconcept->id_formula_q,$employee,$nomina,$vars);
                    }
                    
                }else if(($nomina->type == "Mensual")){
                    if(isset($nominaconcept->id_formula_m)){
                        $tiene_calculo = true;
                        $amount = $this->formula($nominaconcept->id_formula_m,$employee,$nomina,$vars);
                    }

                }else if(($nomina->type == "Semanal")){
                    if(isset($nominaconcept->id_formula_s)){
                        $tiene_calculo = true;
                        $amount = $this->formula($nominaconcept->id_formula_s,$employee,$nomina,$vars);
                    }

                }else if(($nomina->type == "Especial")){ //crear un id_formula_t para la especial
                    if(isset($nominaconcept->id_formula_e)){
                        $tiene_calculo = true;
                        $amount = $this->formula($nominaconcept->id_formula_e,$employee,$nomina,$vars);
                    }
                }/*else if(($nomina->type == "Asignacion")){ //crear un id_formula_t para la especial
                    if(isset($nominaconcept->id_formula_a)){
                        $tiene_calculo = true;
                        $amount = $this->formula($nominaconcept->id_formula_m,$employee,$nomina,$vars);
                    }
                }*/

                $vars->amount = $amount;
                $vars->status =  "1";
            
                if($tiene_calculo == true){
                    $vars->save();
                  
                }
            }

           
        }


        /*if(isset($nominaconcepts_comun))
        {
            foreach($nominaconcepts_comun as $nominaconcept){

                $vars = new NominaCalculation();
                $vars->setConnection(Auth::user()->database_name);
    
                $vars->id_nomina = $nomina->id;
                $vars->id_nomina_concept = $nominaconcept->id;
                $vars->id_employee = $employee->id;
               
                $vars->number_receipt = 0;
                
                $vars->type = 'No';

                $vars->days = 0;
                $vars->hours = 0;
                $vars->cantidad = 0;
        
                $amount = 0;
                $tiene_calculo = false;
    
                if(($nomina->type == "Primera Quincena") || ($nomina->type == "Segunda Quincena")){
                    if(isset($nominaconcept->id_formula_q)){
                        $tiene_calculo = true;
                        $amount = $this->formula($nominaconcept->id_formula_q,$employee,$nomina,$vars);
                    }
    
                }else if(($nomina->type == "Mensual")){
                    if(isset($nominaconcept->id_formula_m)){
                        $tiene_calculo = true;
                        $amount = $this->formula($nominaconcept->id_formula_m,$employee,$nomina,$vars);
                    }
    
                }else if(($nomina->type == "Semanal")){
                    if(isset($nominaconcept->id_formula_s)){
                        $tiene_calculo = true;
                        $amount = $this->formula($nominaconcept->id_formula_s,$employee,$nomina,$vars);
                    }
                }
    
                $vars->amount = $amount;
                $vars->status =  "1";
               
               
                if($tiene_calculo == true){
                    $vars->save();
                   
             
                }
                
            }
            
           
        }    */
        

        
        
    }

    public function formula($id_formula,$employee,$nomina,$nomina_calculation)
    {

       // $global = new GlobalController();
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
        
        //$tasa = $global->search_bcv();

		$variables = ["sueldo"=>$employee->monto_pago,"lunes"=>$lunes,"tasa"=>$nomina->rate,"asignacion"=>$employee->asignacion_general];
        //$variables = ["sueldo"=>$monto_pago, "horas"=>0, "dias"=>0, "horas_trabajadas"=>$horas_trabajadas, "horas_faltadas"=>$horas_faltadas, "dias_trabajados"=>$dias_trabajados, "dias_faltados"=>$dias_faltados];
		$total = $this->resolver($operacion,$variables);


        if($total){
            $total = $total;
        } else {
            $total = 0; 
        }

        /*if($id_formula == 1){
            //{{sueldo}} * 12 / 52 * {{lunes}} * 0.04
            $lunes = $this->calcular_cantidad_de_lunes($nomina);
            $total = ($employee->monto_pago * 12)/52 * ($lunes * 0.04);
            $total = $this->resolver($operacion,$variables);
            
        }else if($id_formula == 2){
            //{{sueldo}} * 12 / 52 * {{lunes}} * 0.04 * 5 / 5
            $lunes = $this->calcular_cantidad_de_lunes($nomina);
            $total = (($employee->monto_pago * 12)/52) * (($lunes * 0.04) * 5)/5 ;
            
        }else if($id_formula == 3){
            //{{sueldo}} / 30 * 7.5
            $total = ($employee->monto_pago * 30) * 7.5 ;
            
        }else if($id_formula == 4){
            //{{sueldo}} * 0.01 / 2
            $total = ($employee->monto_pago * 0.01)/2 ;
            
        }else if($id_formula == 5){
            //{{sueldo}} * 0.01 / 4
            $total = ($employee->monto_pago * 0.01) / 4 ;
            
        }else if($id_formula == 6){
            //{{sueldo}} / 2
            $total = ($employee->monto_pago)/2 ;
            
        }else if($id_formula == 7){
            //{{sueldo}} 
            $total = ($employee->monto_pago) ;
            
        }else if($id_formula == 8){
            //{{sueldo}} / 30 / 8 * 1.6 / {{horas}} 
            $total = (($employee->monto_pago * 30)/8 * 1.6) * $hours ;
            
        }else if($id_formula == 9){
            //{{sueldo}} / 30 / 8 * 1.8 / {{horas}}
            $total = (($employee->monto_pago * 30)/8 * 1.8) * $hours ;
            
        }else if($id_formula == 10){
            //{{sueldo}} / 30*1.5 *{{dias}}
            $total = ($employee->monto_pago / 30) * 1.5 * $days;
            
        }else if($id_formula == 11){
            //{{sueldo}} / 30 * 1.5 * {{diasferiados}}
            $total = ($employee->monto_pago / 30) * 1.5 * $days;
            
        }else if($id_formula == 12){
            //{{cestaticket}} / 2
            $total = $cestaticket / 2;
            
        }else if($id_formula == 13){
            //{{sueldo}} * 0.03
            $total = $employee->monto_pago * 0.03;
            
        }else if($id_formula == 14){
            //{{sueldo}} * 12 / 52 * {{lunes}} * 0.005
            $lunes = $this->calcular_cantidad_de_lunes($nomina);
            $total = ($employee->monto_pago * 12)/52 * $lunes * 0.05;
            
        }else if($id_formula == 15){
            //{{sueldo}} * 12 / 52 * {{lunes}} * 0.004
            $lunes = $this->calcular_cantidad_de_lunes($nomina);
            $total = ($employee->monto_pago * 12)/52 * $lunes * 0.04;
            
        }else if($id_formula == 16){
            //{{sueldo}} / 30 * {{dias_faltados}}
            
            $total = ($employee->monto_pago / 30) * $days;
            
        }else if($id_formula == 17){
            //{{sueldo}} /4
            $total = ($employee->monto_pago) /4;
            
        }else{
            return -1;
        }*/
        
        return $total;
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
    
    public function store(Request $request)
    {
       
        $data = request()->validate([
           
            'nomina_type'     =>'required',
            'description'       =>'required|max:60',
            'type'              =>'required',
            'date_begin'        =>'required',
            
            
           
        ]);

        $nomina = new Nomina();
        $nomina->setConnection(Auth::user()->database_name);

        $nomina->nomina_type_id = request('nomina_type');
        $nomina->description = request('description');
        $nomina->type = request('type');
       
        $nomina->date_begin = request('date_begin');
        
        $nomina->date_end = request('date_end');
        $nomina->status =  "1";
       
        $nomina->rate = str_replace(',', '.', str_replace('.', '', request('rate')));

        $nomina->save();

        $this->calculate($nomina->id);
        
        return redirect('/nominas')->withSuccess('Registro Exitoso!');
    }



    public function edit($id)
    {

        $var  = Nomina::on(Auth::user()->database_name)->find($id);

        /*$professions = Profession::on(Auth::user()->database_name)->orderBY('name','asc')->get();*/
        $nomina_type = NominaType::on(Auth::user()->database_name)->get();
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');

        $global = new GlobalController();
        $bcv = $global->search_bcv();

        
        return view('admin.nominas.edit',compact('var','nomina_type','datenow','bcv'));
        
    }

   


    public function update(Request $request,$id)
    {
       
        $vars =  Nomina::on(Auth::user()->database_name)->find($id);
        $var_status = $vars->status;
      

        $data = request()->validate([

            'nomina_type'     =>'required',
            'description'         =>'required|max:255',
            'type'         =>'required',
            'date_begin'         =>'required|max:255',
            
            
           
        ]);

        $var          = Nomina::on(Auth::user()->database_name)->findOrFail($id);

        $var->nomina_type_id = request('nomina_type');
        $var->description = request('description');
        $var->type = request('type');
        $var->date_begin = request('date_begin');
        $var->date_end = request('date_end');
        $var->rate = str_replace(',', '.', str_replace('.', '', request('rate')));
       
        if(request('status') == null){
            $var->status = $var_status;
        }else{
            $var->status = request('status');
        }
       

        $var->save();


        return redirect('/nominas')->withSuccess('Registro Guardado Exitoso!');

    }

    public function destroy(Request $request)
   {
        $nomina = Nomina::on(Auth::user()->database_name)->findOrFail($request->id_nomina_modal);

        if(isset($nomina)){

            $this->deleteNomina($nomina->id);

            $nomina->status = 'X';

            $nomina->save();

            return redirect('/nominas')->withSuccess('Eliminacion Exitosa!');

        }else{

            return redirect('/nominas')->withDanger('No se encontro el empleado!');
        }
   }

    public function deleteNomina($id_nomina){
        
        NominaCalculation::on(Auth::user()->database_name)->where('id_nomina',$id_nomina)->delete();


    }

    public function deleteNominacont($id_nomina){
        
        $header_id = HeaderVoucher::on(Auth::user()->database_name)
        ->where('id_nomina',$id_nomina)
        ->first();

        $header = HeaderVoucher::on(Auth::user()->database_name)
        ->where('id_nomina',$id_nomina)
        ->update(['status' => 'X']);

        $detail = DetailVoucher::on(Auth::user()->database_name)
        ->where('id_header_voucher',$header_id->id)
        ->update(['status' => 'X']);
    
    }




}
