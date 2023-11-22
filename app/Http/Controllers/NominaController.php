<?php

namespace App\Http\Controllers;

use App\Account;
use App\DetailVoucher;
use App\Employee;
use App\HeaderVoucher;
use App\Nomina;
use App\NominaType;
use App\NominaBasesCalcs;
use App\NominaCalculation;
use App\NominaConcept;
use App\NominaFormula;
use App\Profession;
use App\Company;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;


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

      //  if($users_role == '1'){
           $nominas      =   Nomina::on(Auth::user()->database_name)->where('status','!=','X')->orderBy('id', 'desc')->get();

           $datospresta = DB::connection(Auth::user()->database_name)
           ->table('nomina_calculations AS a')
           ->join('nominas as b', 'a.id_nomina','b.id')
           ->wherein('a.id_nomina_concept', ['2','3','4'])
           ->select(DB::raw('SUBSTR(b.date_end,1,4) AS año'), DB::raw('SUBSTR(b.date_end,6,2) AS mes'))
           ->groupBy(DB::raw('SUBSTR(b.date_end,1,4)') ,  DB::raw('SUBSTR(b.date_end,6,2)'))
           ->get();

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

    /*   }elseif($users_role == '2'){
            return view('admin.index');
        }*/


        return view('admin.nominas.index',compact('nominas','nomina_type','datospresta'));

    }

    public function searchMovementNomina($id_nomina){
        $header = HeaderVoucher::on(Auth::user()->database_name)
        ->where('id_nomina',$id_nomina)
        ->where('status','!=','X')
        ->orderBy('id','desc')->first();

        if(isset($header)){
            $detail = new DetailVoucherController();


            return  redirect('/detailvouchers/register/bolivares/'.$header->id);

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

        if ($var->nomina_type_id == '1') {
            $employees = Employee::on(Auth::user()->database_name)
            ->where('status','!=','X')
            ->where('status','!=','0')
            ->where('status','!=','5')->get();
        } else {
            $employees = Employee::on(Auth::user()->database_name)
            ->where('status','!=','X')
            ->where('status','!=','0')
            ->where('status','!=','5')
            ->where('nomina_type_id',$var->nomina_type_id)->get();
        }

        $nomina_type = NominaType::on(Auth::user()->database_name)->find($var->nomina_type_id);
        $nomina_type_id_name = $nomina_type->name;

        $amount_total_asignacion_m_deducciones = 0;

        foreach ($employees as $employee) {

            $amount_total_otras_asignaciones = 0;
            $amount_total_otras_deducciones = 0;

            $amount_salary = 0;

            $calculos_nomina = DB::connection(Auth::user()->database_name)->table('nomina_calculations')
            ->where('id_nomina',$id)
            ->where('id_employee',$employee->id)
            ->get();

            foreach($calculos_nomina as $calculos) {

                    $concepto = DB::connection(Auth::user()->database_name)->table('nomina_concepts')
                    ->find($calculos->id_nomina_concept);

                    // Total Asignaciones
                    if (($concepto->abbreviation != 'SUEM' or $concepto->abbreviation != 'SUES' or $concepto->abbreviation != 'SUEQ') and $concepto->sign == 'A'){
                        $amount_total_otras_asignaciones += $calculos->amount;
                    } else {
                        $amount_total_otras_asignaciones += 0;
                    }

                    // Total Asignaciones
                    if (($concepto->abbreviation == 'SUEM' or $concepto->abbreviation == 'SUES' or $concepto->abbreviation == 'SUEQ') and $concepto->sign == 'A'){
                        $amount_salary += $calculos->amount;
                    } else {
                        $amount_salary += 0;
                    }


                    // total Deducciones
                    if ($concepto->sign == 'D') {
                        $amount_total_otras_deducciones += $calculos->amount;
                    } else {
                        $amount_total_otras_deducciones += 0;
                    }

            }

            $amount_total_asignacion_m_deducciones = $amount_total_otras_asignaciones - $amount_total_otras_deducciones;

            $employee->asignaciones = $amount_total_otras_asignaciones;
            $employee->deducciones = $amount_total_otras_deducciones;
            $employee->monto_pago = $amount_total_asignacion_m_deducciones;
            $employee->amount_salary = $amount_salary;

        }


        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');

       // dd($var);
        return view('admin.nominas.selectemployee',compact('var','employees','datenow','nomina_type_id_name'));

    }



    public function calculate($id_nomina)
    {

        $check_exist_calculation = NominaCalculation::on(Auth::user()->database_name)->where('id_nomina',$id_nomina)->first();

        $nomina_actual = Nomina::on(Auth::user()->database_name)->find($id_nomina);

            //Chequea si hay calculos previos y pregunta si se desea recalcular la nomina
            if(!empty($check_exist_calculation)){

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

                $exist_nomina_calculation = $nomina_actual;


                $datospresta = DB::connection(Auth::user()->database_name)
                ->table('nomina_calculations AS a')
                ->join('nominas as b', 'a.id_nomina','b.id')
                ->whereIn('a.id_nomina_concept', ['2','3','4'])
                ->select(DB::raw('SUBSTR(b.date_end,1,4) AS año'), DB::raw('SUBSTR(b.date_end,6,2) AS mes'))
                ->groupBy(DB::raw('SUBSTR(b.date_end,1,4)') ,  DB::raw('SUBSTR(b.date_end,6,2)'))
                ->get();

                /*$datospresta = DB::connection(Auth::user()->database_name)
                ->table('nomina_calculations AS a')
                ->join('nominas as b', 'a.id_nomina','b.id')
                ->wherein('a.id_nomina_concept', ['2','3','4'])
                ->select(DB::raw('SUBSTR(b.date_end,1,4) AS año'), DB::raw('SUBSTR(b.date_end,6,2) AS mes'))
                ->groupBy(DB::raw('SUBSTR(b.date_end,1,4)') ,  DB::raw('SUBSTR(b.date_end,6,2)'))
                ->get();*/


                return view('admin.nominas.index',compact('nominas','exist_nomina_calculation','nomina_type','datospresta'));

            }

        if ($nomina_actual->nomina_type_id == '1'){

            $employees = Employee::on(Auth::user()->database_name)
            ->where('status','!=','X')
            ->where('status','!=','0')
            ->where('status','!=','5')->get();

        } else {

            $employees = Employee::on(Auth::user()->database_name)
            ->where('status','!=','X')
            ->where('status','!=','0')
            ->where('status','!=','5')
            ->where('nomina_type_id',$nomina_actual->nomina_type_id)->get();
        }



        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');
        $sum_employees = 0;
        $sum_employees_asignacion_general = 0;
        $sum_sso_patronal = 0;
        $global = new GlobalController();
        $bcv = floatval($global->search_bcv());

        if(!isset($nomina_actual->rate) or $nomina_actual->rate == 0){
            $nomina_actual->rate = $bcv;
        }

        foreach($employees as $employee){
            $this->addNominaCalculation($nomina_actual,$employee);

            // Calcular conceptos de Asignacion
            $amount_total_otras_asignaciones = 0;
            $amount_total_otras_deducciones = 0;
            $amount_total_asignacion_m_deducciones = 0;
            $monto_total_asignacion = 0;
            $asignacion_general = 0;
            $conteo = 0;
            $asignacion_general_calculate = 0;


                    $calculos_nomina = DB::connection(Auth::user()->database_name)->table('nomina_calculations')
                    ->where('id_nomina',$nomina_actual->id)
                    ->where('id_employee',$employee->id)
                    ->get();

                    foreach($calculos_nomina as $calculos) {

                            $concepto = DB::connection(Auth::user()->database_name)->table('nomina_concepts')
                            ->find($calculos->id_nomina_concept);

                            // Total Asignaciones
                            if ($concepto->sign == 'A'){
                                $amount_total_otras_asignaciones += $calculos->amount;
                            } else {
                                $amount_total_otras_asignaciones += 0;
                            }

                            // total Deducciones
                            if ($concepto->sign == 'D') {
                                $amount_total_otras_deducciones += $calculos->amount;
                            } else {
                                $amount_total_otras_deducciones += 0;
                            }

                            if($concepto->asignation == 'S'){
                                $conceptos_asignacion[] = array($concepto->id,$calculos->id,$concepto->type);
                            }
                    }


                    if (isset($conceptos_asignacion)){
                        for ($q=0;$q<count($conceptos_asignacion);$q++) {

                            if ($conceptos_asignacion[$q][2] == 'Quincenal'){
                            $asignacion_general_calculate = $employee->asignacion_general/2;
                            }

                            if ($conceptos_asignacion[$q][2] == 'Mensual' or $conceptos_asignacion[$q][2] == 'Especial' or $conceptos_asignacion[$q][2] == 'Asignacion'){
                            $asignacion_general_calculate = $employee->asignacion_general;
                            }

                            if ($conceptos_asignacion[$q][2] == 'Semanal'){
                            $asignacion_general_calculate = $employee->asignacion_general/4;
                            }

                        }
                    }

                   // cuadrar los calculos para otras asignaciones generales

                    $amount_total_asignacion_m_deducciones = $amount_total_otras_asignaciones - $amount_total_otras_deducciones;
                    $asignacion_general = $asignacion_general_calculate * $nomina_actual->rate;
                    $monto_total_asignacion = $asignacion_general - $amount_total_asignacion_m_deducciones;


                    if (isset($conceptos_asignacion)){

                        for ($q=0;$q<count($conceptos_asignacion);$q++) {
                            if($employee->asignacion_general > 0) {

                            $agrega = NominaCalculation::on(Auth::user()->database_name)
                            ->where('id',$conceptos_asignacion[$q][1])
                            ->where('id_employee',$employee->id)
                            ->update(['amount' => $monto_total_asignacion]);

                            } else {

                                $elimina = NominaCalculation::on(Auth::user()->database_name)
                                ->where('id',$conceptos_asignacion[$q][1])
                                ->where('id_employee',$employee->id)
                                ->delete();

                            }

                        }
                    }

        }




        return redirect('/nominas')->withSuccess('El calculo de la Nomina '.$nomina_actual->description.' fue Exitoso!');

    }

    public function calculatecont($id_nomina)
    {
        $header_voucher = '';

        $nomina = Nomina::on(Auth::user()->database_name)
        ->where('id',$id_nomina)
        ->first();

        //Chequea si hay comprovante  y pregunta si se desea recrearlos

        $header_search = HeaderVoucher::on(Auth::user()->database_name)->where('id_nomina',$id_nomina)->where('status','!=','X')->first();

        if(!empty($header_search)){

            $nomina_type = '';

            $nominas      =   Nomina::on(Auth::user()->database_name)
            ->where('status','!=','X')
            ->orderBy('id', 'desc')->get();


            foreach ($nominas as $key => $nominai) {
                    $nomina_type = NominaType::on(Auth::user()->database_name)->find($nominai->nomina_type_id);
                    $nominai->nomina_type_id_name = $nomina_type->name;

                    $header_search = HeaderVoucher::on(Auth::user()->database_name)->where('id_nomina',$nominai->id)->where('status','!=','X')->first();

                    if (!empty($header_search)) {
                        $check_exist = 'Existe';
                    } else {
                        $check_exist = 'no existe';
                    }

                    $nominai->check_exist = $check_exist;
            }

                $exist_nomina_calculationcont = $nomina;

                $datospresta = DB::connection(Auth::user()->database_name)
                ->table('nomina_calculations AS a')
                ->join('nominas as b', 'a.id_nomina','b.id')
                ->wherein('a.id_nomina_concept', ['2','3','4'])
                ->select(DB::raw('SUBSTR(b.date_end,1,4) AS año'), DB::raw('SUBSTR(b.date_end,6,2) AS mes'))
                ->groupBy(DB::raw('SUBSTR(b.date_end,1,4)') ,  DB::raw('SUBSTR(b.date_end,6,2)'))
                ->get();

            return view('admin.nominas.index',compact('nominas','exist_nomina_calculationcont','nomina_type','datospresta'));
        }


        $header_voucher = $this->calculateAmountTotalNomina($nomina);

        return redirect('/nominas')->withSuccess('Los comprobantes de la Nómina '.$nomina->description.' fueron creados exiosamente. Comprobante contable: '.$header_voucher);

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


    public function calculateAmountTotalNomina($nomina){

        $amount_total_asignacion = 0;

        $amount_total_asignacion1 = 0;
        $amount_total_asignacion2 = 0;
        $amount_total_asignacion3 = 0;
        $amount_total_asignacion4 = 0;
        $amount_total_asignacion5 = 0;

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
        $amount_total_anticipo = 0;

        $amount_total_bono_transporte1 = 0;
        $amount_total_bono_transporte2 = 0;
        $amount_total_bono_transporte3 = 0;
        $amount_total_bono_transporte4 = 0;
        $amount_total_bono_transporte5 = 0;

        $amount_total_bono_alim1 = 0;
        $amount_total_bono_alim2 = 0;
        $amount_total_bono_alim3 = 0;
        $amount_total_bono_alim4 = 0;
        $amount_total_bono_alim5 = 0;

        $amount_total_anticipo1 = 0;
        $amount_total_anticipo2 = 0;
        $amount_total_anticipo3 = 0;
        $amount_total_anticipo4 = 0;
        $amount_total_anticipo5 = 0;

        $amount_total_des_anticipo1 = 0;
        $amount_total_des_anticipo2 = 0;
        $amount_total_des_anticipo3 = 0;
        $amount_total_des_anticipo4 = 0;
        $amount_total_des_anticipo5 = 0;

        $amount_total_des_anticipo = 0;
        $amount_total_otras_asignaciones = 0;
        $amount_total_otras_deducciones = 0;

        $amount_total_asignacion_m_deducciones = 0;

        $account_sueldos = '';
        $account_alim = '';
        $account_ant = '';
        $account_bmedic = '';
        $account_transp = '';
        $account_sso = '';
        $account_faov = '';
        $account_pie = '';
        $account_inces = '';
        $account_sueldos = '';

        $c_sueldo = '';
        $c_balim = '';
        $c_btrn = '';
        $c_bmed = '';
        $c_antc = '';

        $c_sueldo1 = '';
        $c_balim1 = '';
        $c_btrn1 = '';
        $c_bmed1 = '';
        $c_antc1 = '';
        $c_des_antc1 = '';

        $c_sueldo2 = '';
        $c_balim2 = '';
        $c_btrn2 = '';
        $c_bmed2 = '';
        $c_antc2 = '';
        $c_des_antc2 = '';

        $c_sueldo3 = '';
        $c_balim3 = '';
        $c_btrn3 = '';
        $c_bmed3 = '';
        $c_antc3 = '';
        $c_des_antc3 = '';

        $c_sueldo4 = '';
        $c_balim4 = '';
        $c_btrn4 = '';
        $c_bmed4 = '';
        $c_antc4 = '';
        $c_des_antc4 = '';

        $c_sueldo5 = '';
        $c_balim5 = '';
        $c_btrn5 = '';
        $c_bmed5 = '';
        $c_antc5 = '';
        $c_des_antc5 = '';


        $c_sso = '';
        $c_faov = '';
        $c_inces = '';
        $c_des_antc = '';

        $calculos_nomina = DB::connection(Auth::user()->database_name)->table('nomina_calculations')
        ->where('id_nomina',$nomina->id)
        ->get();


        $date = Carbon::now();
        $datenow = $date->format('Y-m-d'); //fecha actual

        $global = new GlobalController();
        $bcv = floatval($global->search_bcv());
        $nominabases  =  NominaBasesCalcs::on(Auth::user()->database_name)->find(1);
        $lunes = $this->calcular_cantidad_de_lunes($nomina);



        foreach($calculos_nomina as $calculos) {
                $concepto = '';

                $concepto = DB::connection(Auth::user()->database_name)->table('nomina_concepts')
                ->find($calculos->id_nomina_concept);

                if (!empty($concepto)){

                    // Sueldo
                    if(($concepto->abbreviation == 'SUEM' or $concepto->abbreviation == 'SUES' or $concepto->abbreviation == 'SUEQ') and $concepto->sign == 'A'){


                           if (isset($concepto->account_name)){

                            $account = DB::connection(Auth::user()->database_name)->table('accounts')
                            ->where('description','LIKE','%'.$concepto->account_name.'%')
                            ->first();
                            $c_sueldo = $account->id;

                            $amount_total_asignacion += $calculos->amount;


                           } else {

                                if (isset($concepto->account_name1)){

                                    $tipo_empleado =  Employee::on(Auth::user()->database_name)->where('id',$calculos->id_employee)->select('nomina_type_id')->first();

                                    if ($tipo_empleado->nomina_type_id == '1'){

                                        $account = DB::connection(Auth::user()->database_name)->table('accounts')
                                        ->where('description','LIKE','%'.$concepto->account_name1.'%')
                                        ->first();

                                        $c_sueldo1 = $account->id;
                                        $amount_total_asignacion1 += $calculos->amount;
                                    }

                                }

                                if (isset($concepto->account_name2)){

                                    $tipo_empleado =  Employee::on(Auth::user()->database_name)->where('id',$calculos->id_employee)->select('nomina_type_id')->first();

                                    if ($tipo_empleado->nomina_type_id == '2'){

                                        $account = DB::connection(Auth::user()->database_name)->table('accounts')
                                        ->where('description','LIKE','%'.$concepto->account_name2.'%')
                                        ->first();

                                        $c_sueldo2 = $account->id;
                                        $amount_total_asignacion2 += $calculos->amount;
                                    }
                                }

                                if (isset($concepto->account_name3)){

                                    $tipo_empleado =  Employee::on(Auth::user()->database_name)->where('id',$calculos->id_employee)->select('nomina_type_id')->first();

                                    if ($tipo_empleado->nomina_type_id == '3'){

                                        $account = DB::connection(Auth::user()->database_name)->table('accounts')
                                        ->where('description','LIKE','%'.$concepto->account_name3.'%')
                                        ->first();

                                        $c_sueldo3 = $account->id;
                                        $amount_total_asignacion3 += $calculos->amount;
                                    }
                                }

                                if (isset($concepto->account_name4)){

                                    $tipo_empleado =  Employee::on(Auth::user()->database_name)->where('id',$calculos->id_employee)->select('nomina_type_id')->first();

                                    if ($tipo_empleado->nomina_type_id == '4'){

                                        $account = DB::connection(Auth::user()->database_name)->table('accounts')
                                        ->where('description','LIKE','%'.$concepto->account_name4.'%')
                                        ->first();

                                        $c_sueldo4 = $account->id;
                                        $amount_total_asignacion4 += $calculos->amount;
                                    }
                                }

                                if (isset($concepto->account_name5)){

                                    $tipo_empleado =  Employee::on(Auth::user()->database_name)->where('id',$calculos->id_employee)->select('nomina_type_id')->first();

                                    if ($tipo_empleado->nomina_type_id == '5'){

                                        $account = DB::connection(Auth::user()->database_name)->table('accounts')
                                        ->where('description','LIKE','%'.$concepto->account_name5.'%')
                                        ->first();

                                        $c_sueldo5 = $account->id;
                                        $amount_total_asignacion5 += $calculos->amount;
                                    }
                                }


                           }



                    } else {
                        $amount_total_asignacion += 0;

                    }

                    if($concepto->abbreviation == 'BALIM' and $concepto->sign == 'A'){

                        if (isset($concepto->account_name)){

                            $account = DB::connection(Auth::user()->database_name)->table('accounts')
                            ->where('description','LIKE','%'.$concepto->account_name.'%')
                            ->first();
                            $c_balim = $account->id;

                            $amount_total_bono_alim += $calculos->amount;


                           } else {

                                if (isset($concepto->account_name1)){

                                    $tipo_empleado =  Employee::on(Auth::user()->database_name)->where('id',$calculos->id_employee)->select('nomina_type_id')->first();

                                    if ($tipo_empleado->nomina_type_id == '1'){

                                        $account = DB::connection(Auth::user()->database_name)->table('accounts')
                                        ->where('description','LIKE','%'.$concepto->account_name1.'%')
                                        ->first();

                                        $c_balim1 = $account->id;
                                        $amount_total_bono_alim1 += $calculos->amount;
                                    }

                                }

                                if (isset($concepto->account_name2)){

                                    $tipo_empleado =  Employee::on(Auth::user()->database_name)->where('id',$calculos->id_employee)->select('nomina_type_id')->first();

                                    if ($tipo_empleado->nomina_type_id == '2'){

                                        $account = DB::connection(Auth::user()->database_name)->table('accounts')
                                        ->where('description','LIKE','%'.$concepto->account_name2.'%')
                                        ->first();

                                        $c_balim2 = $account->id;
                                        $amount_total_bono_alim2 += $calculos->amount;
                                    }
                                }

                                if (isset($concepto->account_name3)){

                                    $tipo_empleado =  Employee::on(Auth::user()->database_name)->where('id',$calculos->id_employee)->select('nomina_type_id')->first();

                                    if ($tipo_empleado->nomina_type_id == '3'){

                                        $account = DB::connection(Auth::user()->database_name)->table('accounts')
                                        ->where('description','LIKE','%'.$concepto->account_name3.'%')
                                        ->first();

                                        $c_balim3 = $account->id;
                                        $amount_total_bono_alim3 += $calculos->amount;
                                    }
                                }

                                if (isset($concepto->account_name4)){

                                    $tipo_empleado =  Employee::on(Auth::user()->database_name)->where('id',$calculos->id_employee)->select('nomina_type_id')->first();

                                    if ($tipo_empleado->nomina_type_id == '4'){

                                        $account = DB::connection(Auth::user()->database_name)->table('accounts')
                                        ->where('description','LIKE','%'.$concepto->account_name4.'%')
                                        ->first();

                                        $c_balim4 = $account->id;
                                        $amount_total_bono_alim4 += $calculos->amount;
                                    }
                                }

                                if (isset($concepto->account_name5)){

                                    $tipo_empleado =  Employee::on(Auth::user()->database_name)->where('id',$calculos->id_employee)->select('nomina_type_id')->first();

                                    if ($tipo_empleado->nomina_type_id == '5'){

                                        $account = DB::connection(Auth::user()->database_name)->table('accounts')
                                        ->where('description','LIKE','%'.$concepto->account_name5.'%')
                                        ->first();

                                        $c_balim5 = $account->id;
                                        $amount_total_bono_alim5 += $calculos->amount;
                                    }
                                }


                           }


                    } else {
                        $amount_total_bono_alim += 0;
                    }

                    if($concepto->abbreviation == 'ANTC' and $concepto->sign == 'A'){

                        if (isset($concepto->account_name)){

                            $account = DB::connection(Auth::user()->database_name)->table('accounts')
                            ->where('description','LIKE','%'.$concepto->account_name.'%')
                            ->first();
                            $c_antc = $account->id;

                            $amount_total_anticipo += $calculos->amount;


                           } else {

                                if (isset($concepto->account_name1)){

                                    $tipo_empleado =  Employee::on(Auth::user()->database_name)->where('id',$calculos->id_employee)->select('nomina_type_id')->first();

                                    if ($tipo_empleado->nomina_type_id == '1'){

                                        $account = DB::connection(Auth::user()->database_name)->table('accounts')
                                        ->where('description','LIKE','%'.$concepto->account_name1.'%')
                                        ->first();

                                        $c_antc1 = $account->id;
                                        $amount_total_anticipo1 += $calculos->amount;
                                    }

                                }

                                if (isset($concepto->account_name2)){

                                    $tipo_empleado =  Employee::on(Auth::user()->database_name)->where('id',$calculos->id_employee)->select('nomina_type_id')->first();

                                    if ($tipo_empleado->nomina_type_id == '2'){

                                        $account = DB::connection(Auth::user()->database_name)->table('accounts')
                                        ->where('description','LIKE','%'.$concepto->account_name2.'%')
                                        ->first();

                                        $c_antc2 = $account->id;
                                        $amount_total_anticipo2 += $calculos->amount;
                                    }
                                }

                                if (isset($concepto->account_name3)){

                                    $tipo_empleado =  Employee::on(Auth::user()->database_name)->where('id',$calculos->id_employee)->select('nomina_type_id')->first();

                                    if ($tipo_empleado->nomina_type_id == '3'){

                                        $account = DB::connection(Auth::user()->database_name)->table('accounts')
                                        ->where('description','LIKE','%'.$concepto->account_name3.'%')
                                        ->first();

                                        $c_antc3 = $account->id;
                                        $amount_total_anticipo3 += $calculos->amount;
                                    }
                                }

                                if (isset($concepto->account_name4)){

                                    $tipo_empleado =  Employee::on(Auth::user()->database_name)->where('id',$calculos->id_employee)->select('nomina_type_id')->first();

                                    if ($tipo_empleado->nomina_type_id == '4'){

                                        $account = DB::connection(Auth::user()->database_name)->table('accounts')
                                        ->where('description','LIKE','%'.$concepto->account_name4.'%')
                                        ->first();

                                        $c_antc4 = $account->id;
                                        $amount_total_anticipo4 += $calculos->amount;
                                    }
                                }

                                if (isset($concepto->account_name5)){

                                    $tipo_empleado =  Employee::on(Auth::user()->database_name)->where('id',$calculos->id_employee)->select('nomina_type_id')->first();

                                    if ($tipo_empleado->nomina_type_id == '5'){

                                        $account = DB::connection(Auth::user()->database_name)->table('accounts')
                                        ->where('description','LIKE','%'.$concepto->account_name5.'%')
                                        ->first();

                                        $c_antc5 = $account->id;
                                        $amount_total_anticipo5 += $calculos->amount;
                                    }
                                }


                           }

                    } else {
                        $amount_total_anticipo += 0;
                    }

                    // Asignaciones Generales
                    if($concepto->abbreviation == 'BMED' and $concepto->sign == 'A'){
                        $amount_total_bono_medico += $calculos->amount;
                        $account_bmedic = $concepto->account_name;

                            $account = DB::connection(Auth::user()->database_name)->table('accounts')
                            ->where('description','LIKE','%'.$concepto->account_name.'%')
                            ->first();



                            $c_bmed = $account->id;


                    } else {
                        $amount_total_bono_medico += 0;

                    }

                    if($concepto->abbreviation == 'BTRN' and $concepto->sign == 'A'){

                        if (isset($concepto->account_name)){

                            $account = DB::connection(Auth::user()->database_name)->table('accounts')
                            ->where('description','LIKE','%'.$concepto->account_name.'%')
                            ->first();
                            $c_btrn = $account->id;

                            $amount_total_bono_transporte += $calculos->amount;


                           } else {

                                if (isset($concepto->account_name1)){

                                    $tipo_empleado =  Employee::on(Auth::user()->database_name)->where('id',$calculos->id_employee)->select('nomina_type_id')->first();

                                    if ($tipo_empleado->nomina_type_id == '1'){

                                        $account = DB::connection(Auth::user()->database_name)->table('accounts')
                                        ->where('description','LIKE','%'.$concepto->account_name1.'%')
                                        ->first();

                                        $c_btrn1 = $account->id;
                                        $amount_total_bono_transporte1 += $calculos->amount;
                                    }

                                }

                                if (isset($concepto->account_name2)){

                                    $tipo_empleado =  Employee::on(Auth::user()->database_name)->where('id',$calculos->id_employee)->select('nomina_type_id')->first();

                                    if ($tipo_empleado->nomina_type_id == '2'){

                                        $account = DB::connection(Auth::user()->database_name)->table('accounts')
                                        ->where('description','LIKE','%'.$concepto->account_name2.'%')
                                        ->first();

                                        $c_btrn2 = $account->id;
                                        $amount_total_bono_transporte2 += $calculos->amount;
                                    }
                                }

                                if (isset($concepto->account_name3)){

                                    $tipo_empleado =  Employee::on(Auth::user()->database_name)->where('id',$calculos->id_employee)->select('nomina_type_id')->first();

                                    if ($tipo_empleado->nomina_type_id == '3'){

                                        $account = DB::connection(Auth::user()->database_name)->table('accounts')
                                        ->where('description','LIKE','%'.$concepto->account_name3.'%')
                                        ->first();

                                        $c_btrn3 = $account->id;
                                        $amount_total_bono_transporte3 += $calculos->amount;
                                    }
                                }

                                if (isset($concepto->account_name4)){

                                    $tipo_empleado =  Employee::on(Auth::user()->database_name)->where('id',$calculos->id_employee)->select('nomina_type_id')->first();

                                    if ($tipo_empleado->nomina_type_id == '4'){

                                        $account = DB::connection(Auth::user()->database_name)->table('accounts')
                                        ->where('description','LIKE','%'.$concepto->account_name4.'%')
                                        ->first();

                                        $c_btrn4 = $account->id;
                                        $amount_total_bono_transporte4 += $calculos->amount;
                                    }
                                }

                                if (isset($concepto->account_name5)){

                                    $tipo_empleado =  Employee::on(Auth::user()->database_name)->where('id',$calculos->id_employee)->select('nomina_type_id')->first();

                                    if ($tipo_empleado->nomina_type_id == '5'){

                                        $account = DB::connection(Auth::user()->database_name)->table('accounts')
                                        ->where('description','LIKE','%'.$concepto->account_name5.'%')
                                        ->first();

                                        $c_btrn5 = $account->id;
                                        $amount_total_bono_transporte5 += $calculos->amount;
                                    }
                                }


                           }


                    } else {
                        $amount_total_bono_transporte += 0;

                    }

                    // retenciones
                    if($concepto->abbreviation == 'SSO' and $concepto->sign == 'D'){
                        $amount_total_deduccion_sso += $calculos->amount;
                        $account_sso = $concepto->account_name;

                            $account = DB::connection(Auth::user()->database_name)->table('accounts')
                            ->where('description','LIKE','%'.$concepto->account_name.'%')
                            ->first();

                            $c_sso =   $account->id;


                    } else {
                        $amount_total_deduccion_sso += 0;

                    }

                    if($concepto->abbreviation == 'FAOV' and $concepto->sign == 'D'){
                        $amount_total_deduccion_faov += $calculos->amount;

                        $account = DB::connection(Auth::user()->database_name)->table('accounts')
                        ->where('description','LIKE','%'.$concepto->account_name.'%')
                        ->first();

                        $c_faov =   $account->id;

                    } else {
                        $amount_total_deduccion_faov += 0;

                    }

                    if($concepto->abbreviation == 'PIE' and $concepto->sign == 'D'){
                        $amount_total_deduccion_pie += $calculos->amount;
                        $account_pie = $concepto->account_name;

                            $account = DB::connection(Auth::user()->database_name)->table('accounts')
                            ->where('description','LIKE','%'.$concepto->account_name.'%')
                            ->first();


                            $c_pie = $account->id;

                    } else {
                        $amount_total_deduccion_pie += 0;

                    }

                    if($concepto->abbreviation == 'INCES' and $concepto->sign == 'D'){
                        $amount_total_deduccion_ince += $calculos->amount;
                        $account_inces = $concepto->account_name;

                            $account = DB::connection(Auth::user()->database_name)->table('accounts')
                            ->where('description','LIKE','%'.$concepto->account_name.'%')
                            ->first();



                            $c_inces = $account->id;


                    } else {
                        $amount_total_deduccion_ince += 0;

                    }

                    if($concepto->abbreviation == 'DAPQ' and $concepto->sign == 'D'){

                        if (isset($concepto->account_name)){

                            $account = DB::connection(Auth::user()->database_name)->table('accounts')
                            ->where('description','LIKE','%'.$concepto->account_name.'%')
                            ->first();
                            $c_des_antc = $account->id;

                            $amount_total_des_anticipo += $calculos->amount;


                           } else {

                                if (isset($concepto->account_name1)){

                                    $tipo_empleado =  Employee::on(Auth::user()->database_name)->where('id',$calculos->id_employee)->select('nomina_type_id')->first();

                                    if ($tipo_empleado->nomina_type_id == '1'){

                                        $account = DB::connection(Auth::user()->database_name)->table('accounts')
                                        ->where('description','LIKE','%'.$concepto->account_name1.'%')
                                        ->first();

                                        $c_des_antc1 = $account->id;
                                        $amount_total_des_anticipo1 += $calculos->amount;
                                    }

                                }

                                if (isset($concepto->account_name2)){

                                    $tipo_empleado =  Employee::on(Auth::user()->database_name)->where('id',$calculos->id_employee)->select('nomina_type_id')->first();

                                    if ($tipo_empleado->nomina_type_id == '2'){

                                        $account = DB::connection(Auth::user()->database_name)->table('accounts')
                                        ->where('description','LIKE','%'.$concepto->account_name2.'%')
                                        ->first();

                                        $c_des_antc2 = $account->id;
                                        $amount_total_des_anticipo2 += $calculos->amount;
                                    }
                                }

                                if (isset($concepto->account_name3)){

                                    $tipo_empleado =  Employee::on(Auth::user()->database_name)->where('id',$calculos->id_employee)->select('nomina_type_id')->first();

                                    if ($tipo_empleado->nomina_type_id == '3'){

                                        $account = DB::connection(Auth::user()->database_name)->table('accounts')
                                        ->where('description','LIKE','%'.$concepto->account_name3.'%')
                                        ->first();

                                        $c_des_antc3 = $account->id;
                                        $amount_total_des_anticipo3 += $calculos->amount;
                                    }
                                }

                                if (isset($concepto->account_name4)){

                                    $tipo_empleado =  Employee::on(Auth::user()->database_name)->where('id',$calculos->id_employee)->select('nomina_type_id')->first();

                                    if ($tipo_empleado->nomina_type_id == '4'){

                                        $account = DB::connection(Auth::user()->database_name)->table('accounts')
                                        ->where('description','LIKE','%'.$concepto->account_name4.'%')
                                        ->first();

                                        $c_des_antc4 = $account->id;
                                        $amount_total_des_anticipo4 += $calculos->amount;
                                    }
                                }

                                if (isset($concepto->account_name5)){

                                    $tipo_empleado =  Employee::on(Auth::user()->database_name)->where('id',$calculos->id_employee)->select('nomina_type_id')->first();

                                    if ($tipo_empleado->nomina_type_id == '5'){

                                        $account = DB::connection(Auth::user()->database_name)->table('accounts')
                                        ->where('description','LIKE','%'.$concepto->account_name5.'%')
                                        ->first();

                                        $c_des_antc5 = $account->id;
                                        $amount_total_des_anticipo5 += $calculos->amount;
                                    }
                                }


                           }

                    } else {
                        $amount_total_des_anticipo += 0;
                    }



                    // Otras Asignaciones
                    if (($concepto->abbreviation != 'SSO' and $concepto->abbreviation != 'FAOV' and $concepto->abbreviation != 'PIE' and $concepto->abbreviation != 'INCES') and $concepto->sign == 'A'){
                       $amount_total_otras_asignaciones += $calculos->amount;

                       $account_sueldos = $concepto->account_name;


                    } else {
                       $amount_total_otras_asignaciones = 0;

                    }

                    // Deducciones diferentes
                    if (($concepto->abbreviation != 'SSO' and $concepto->abbreviation != 'FAOV' and $concepto->abbreviation != 'INCES') and $concepto->sign == 'D') {
                        $amount_total_otras_deducciones += $calculos->amount;
                    } else {
                        $amount_total_otras_deducciones += 0;

                    }



                } else {

                    $amount_total_asignacion += 0;
                    $amount_total_otras_deducciones += 0;
                    $amount_total_otras_asignaciones += 0;
                    $amount_total_deduccion_ince += 0;
                    $amount_total_deduccion_pie += 0;
                    $amount_total_deduccion_faov += 0;
                    $amount_total_deduccion_sso += 0;
                    $amount_total_bono_transporte += 0;
                    $amount_total_bono_medico += 0;
                    $amount_total_bono_alim += 0;
                    $amount_total_anticipo += 0;
                    $amount_total_des_anticipo += 0;

                }

        }


        $user =   auth()->user();


        if ($user->id_company == 1) {
            $amount_total_asignacion = $amount_total_asignacion;
        } else {
            $amount_total_asignacion = $amount_total_asignacion + $amount_total_otras_asignaciones;
        }

        $amount_total_asignacion_m_deducciones = ($amount_total_asignacion + $amount_total_otras_asignaciones) - ($amount_total_deduccion_sso + $amount_total_deduccion_faov + $amount_total_deduccion_ince + $amount_total_deduccion_pie + $amount_total_otras_deducciones );





        if($nomina->rate == 0 or $nomina->rate == null){
            $nomina->rate = $bcv;
        }

        $header_voucher  = new HeaderVoucher();
        $header_voucher->setConnection(Auth::user()->database_name);
        $header_voucher->id_nomina = $nomina->id;
        $header_voucher->description = "Nomina ".$nomina->description ?? '';
        $header_voucher->date = $nomina->date_end;
        $header_voucher->status =  "1";

        $header_voucher->save();


        if ($user->id_company == 1) { /////////////CONDICION COMPANY//////////////////

            $total_a_pagar_banco = 0;
            //MOVIMIENTO DE SUELDOS
            if($amount_total_asignacion > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_sueldo,$nomina->id,$amount_total_asignacion,0);
            }
            if($amount_total_asignacion1 > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_sueldo1,$nomina->id,$amount_total_asignacion1,0);
            }
            if($amount_total_asignacion2 > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_sueldo2,$nomina->id,$amount_total_asignacion2,0);
            }
            if($amount_total_asignacion3 > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_sueldo3,$nomina->id,$amount_total_asignacion3,0);
            }
            if($amount_total_asignacion4 > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_sueldo4,$nomina->id,$amount_total_asignacion4,0);
            }
            if($amount_total_asignacion5 > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_sueldo5,$nomina->id,$amount_total_asignacion5,0);
            }



            //bono alimentacion
            if($amount_total_bono_alim > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_balim,$nomina->id,$amount_total_bono_alim,0);
            }
            if($amount_total_bono_alim1 > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_balim1,$nomina->id,$amount_total_bono_alim1,0);
            }
            if($amount_total_bono_alim2 > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_balim2,$nomina->id,$amount_total_bono_alim2,0);
            }
            if($amount_total_bono_alim3 > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_balim3,$nomina->id,$amount_total_bono_alim3,0);
            }
            if($amount_total_bono_alim4 > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_balim4,$nomina->id,$amount_total_bono_alim4,0);
            }
            if($amount_total_bono_alim5 > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_balim5,$nomina->id,$amount_total_bono_alim5,0);
            }


                    //MOVIMIENTO DE Bono Medico
            if($amount_total_bono_medico > 0){
                    $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_bmed,$nomina->id,$amount_total_bono_medico,0);
            }

           //BONO DE TRANSPORTE
            if($amount_total_bono_transporte > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_btrn,$nomina->id,$amount_total_bono_transporte,0);
            }
            if($amount_total_bono_transporte1 > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_btrn1,$nomina->id,$amount_total_bono_transporte1,0);
            }
            if($amount_total_bono_transporte2 > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_btrn2,$nomina->id,$amount_total_bono_transporte2,0);
            }
            if($amount_total_bono_transporte3 > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_btrn3,$nomina->id,$amount_total_bono_transporte3,0);
            }
            if($amount_total_bono_transporte4 > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_btrn4,$nomina->id,$amount_total_bono_transporte4,0);
            }
            if($amount_total_bono_transporte5 > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_btrn5,$nomina->id,$amount_total_bono_transporte5,0);
            }


            //RETENCIONES
            if ($amount_total_deduccion_sso > 0) {
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_sso,$nomina->id,0,$amount_total_deduccion_sso);
            }
            if ($amount_total_deduccion_faov > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_faov,$nomina->id,0,$amount_total_deduccion_faov);
            }
            if ($amount_total_deduccion_pie > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_pie,$nomina->id,0,$amount_total_deduccion_pie);
            }
            if ($amount_total_deduccion_ince > 0) {
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_inces,$nomina->id,0,$amount_total_deduccion_ince);
            }

            //ANTICIPOS
            if($amount_total_anticipo > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_antc,$nomina->id,$amount_total_anticipo,0);
            }
            if($amount_total_anticipo1 > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_antc1,$nomina->id,$amount_total_anticipo1,0);
            }
            if($amount_total_anticipo2 > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_antc2,$nomina->id,$amount_total_anticipo2,0);
            }
            if($amount_total_anticipo3 > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_antc3,$nomina->id,$amount_total_anticipo3,0);
            }
            if($amount_total_anticipo4 > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_antc4,$nomina->id,$amount_total_anticipo4,0);
            }
            if($amount_total_anticipo5 > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_antc5,$nomina->id,$amount_total_anticipo5,0);
            }

            //DESCUENTO DE ANTICIPO
            if($amount_total_des_anticipo > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_des_antc,$nomina->id,0,$amount_total_des_anticipo);
            }
            if($amount_total_des_anticipo1 > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_des_antc1,$nomina->id,0,$amount_total_des_anticipo1);
            }
            if($amount_total_des_anticipo2 > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_des_antc2,$nomina->id,0,$amount_total_des_anticipo2);
            }
            if($amount_total_des_anticipo3 > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_des_antc3,$nomina->id,0,$amount_total_des_anticipo3);
            }
            if($amount_total_des_anticipo4 > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_des_antc4,$nomina->id,0,$amount_total_des_anticipo4);
            }
            if($amount_total_des_anticipo5 > 0){
                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$c_des_antc5,$nomina->id,0,$amount_total_des_anticipo5);
            }



            //MOVIMIENTO BANCO


            $total_a_pagar_banco = (($amount_total_asignacion + $amount_total_asignacion1 + $amount_total_asignacion2 + $amount_total_asignacion3 + $amount_total_asignacion4 + $amount_total_asignacion5) +
            ($amount_total_anticipo + $amount_total_anticipo1 + $amount_total_anticipo2 + $amount_total_anticipo3 + $amount_total_anticipo4 + $amount_total_anticipo5) +
            ($amount_total_bono_alim + $amount_total_bono_alim1 + $amount_total_bono_alim2 + $amount_total_bono_alim3 + $amount_total_bono_alim4 + $amount_total_bono_alim5) +
            ($amount_total_bono_medico) +
            ($amount_total_bono_transporte + $amount_total_bono_transporte1 + $amount_total_bono_transporte2 + $amount_total_bono_transporte3 + $amount_total_bono_transporte4 + $amount_total_bono_transporte5)) -
            (($amount_total_des_anticipo + $amount_total_des_anticipo1 + $amount_total_des_anticipo2 + $amount_total_des_anticipo3 + $amount_total_des_anticipo4 + $amount_total_des_anticipo5 ) +
            $amount_total_deduccion_ince +
            $amount_total_deduccion_pie +
            $amount_total_deduccion_faov +
            $amount_total_deduccion_sso);

            $accounts_banco = DB::connection(Auth::user()->database_name)->table('accounts')
            ->where('id','=','13')
            ->first();

            $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_banco->id,$nomina->id,0,$total_a_pagar_banco);



        //MOVIMIENTO DE aporte patronal
        if ($amount_total_deduccion_sso > 0) {

             $amount_total_asignacion = ($amount_total_asignacion + $amount_total_asignacion1 + $amount_total_asignacion2 + $amount_total_asignacion3 + $amount_total_asignacion4 + $amount_total_asignacion5);

             $total_sso_patronal = (($amount_total_asignacion * 12)/52) * $lunes * ($nominabases->sso_company/100);

             $accounts_sso_patronal = DB::connection(Auth::user()->database_name)->table('accounts')
             ->where('description','LIKE', '%Gasto por Aporte al SSO Patronal%')
             ->first();
             $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_sso_patronal->id,$nomina->id,$total_sso_patronal,0);


             $accounts_sso_patronal = DB::connection(Auth::user()->database_name)->table('accounts')
             ->where('description','LIKE', '%Aportes por Pagar al SSO Patronal%')
             ->first();

             $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_sso_patronal->id,$nomina->id,0,$total_sso_patronal);
         }

         if ($amount_total_deduccion_faov > 0){

            $amount_total_asignacion = ($amount_total_asignacion + $amount_total_asignacion1 + $amount_total_asignacion2 + $amount_total_asignacion3 + $amount_total_asignacion4 + $amount_total_asignacion5);


             $total_faov_patronal = $amount_total_asignacion * ($nominabases->faov_company/100);

             $accounts_aporte_patronal = DB::connection(Auth::user()->database_name)->table('accounts')
             ->where('description','LIKE', '%Gasto por Aporte al FAOV Patronal%')
             ->first();
             $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_aporte_patronal->id,$nomina->id,$total_faov_patronal,0);

             $accounts_aporte_patronal = DB::connection(Auth::user()->database_name)->table('accounts')
             ->where('description','LIKE', '%Aportes por Pagar al FAOV Patronal%')
             ->first();

             $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_aporte_patronal->id,$nomina->id,0,$total_faov_patronal);
         }

         if ($amount_total_deduccion_pie > 0){

            $amount_total_asignacion = ($amount_total_asignacion + $amount_total_asignacion1 + $amount_total_asignacion2 + $amount_total_asignacion3 + $amount_total_asignacion4 + $amount_total_asignacion5);


             $total_pie_patronal =  (($amount_total_asignacion * 12)/52) * $lunes * ($nominabases->pie_company/100);

             $accounts_sso_patronal = DB::connection(Auth::user()->database_name)->table('accounts')
             ->where('description','LIKE', '%Gasto por Aporte al PIE Patronal%')
             ->first();

             $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_sso_patronal->id,$nomina->id,$total_pie_patronal,0);


             $accounts_pie_patronal = DB::connection(Auth::user()->database_name)->table('accounts')
             ->where('description','LIKE', '%Aportes por Pagar al PIE Patronal%')
             ->first();

             $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_pie_patronal->id,$nomina->id,0,$total_pie_patronal);
         }


         if ($amount_total_deduccion_ince > 0){

            $amount_total_asignacion = ($amount_total_asignacion + $amount_total_asignacion1 + $amount_total_asignacion2 + $amount_total_asignacion3 + $amount_total_asignacion4 + $amount_total_asignacion5);


             $total_pie_patronal =  ($amount_total_asignacion * 12)/52 * $lunes * (1/100);

             $accounts_sso_patronal = DB::connection(Auth::user()->database_name)->table('accounts')
             ->where('description','LIKE', '%Gasto por Aporte al INCES Patronal%')
             ->first();
             $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_sso_patronal->id,$nomina->id,$total_pie_patronal,0);


             $accounts_pie_patronal = DB::connection(Auth::user()->database_name)->table('accounts')
             ->where('description','LIKE', '%Aportes por Pagar al INCES Patronal%')
             ->first();

             $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_pie_patronal->id,$nomina->id,0,$total_pie_patronal);
         }


        } else { //////NORMAL////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        //MOVIMIENTO DE SUELDOS
        if($amount_total_asignacion > 0){
            $accounts_sueldos = DB::connection(Auth::user()->database_name)->table('accounts')
                ->where('id',$c_sueldo)
                ->first();

            $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_sueldos->id,$nomina->id,$amount_total_asignacion,0);


            //AHORA LOS MOVIMIENTOS POR PAGAR
            $accounts_sueldos_por_pagar = DB::connection(Auth::user()->database_name)->table('accounts')
            ->where('code_one','=','2')
            ->where('description','LIKE', 'Sueldos por Pagar')
            ->first();



            $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_sueldos_por_pagar->id,$nomina->id,0,$amount_total_asignacion_m_deducciones);


        }

         //bono alimentacion
         if($amount_total_bono_alim > 0){
            $accounts_alimentacion = DB::connection(Auth::user()->database_name)->table('accounts')
            ->where('code_one','=','6')
            ->where('description','LIKE', 'Bono de Alimentacion')
            ->first();

            $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_alimentacion->id,$nomina->id,$amount_total_bono_alim,0);


            $accounts_alimentacion_por_pagar = DB::connection(Auth::user()->database_name)->table('accounts')
            ->where('code_one','=','2')
            ->where('description','LIKE', 'Bono de Alimentacion por Pagar')
            ->first();

            $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_alimentacion_por_pagar->id,$nomina->id,0,$amount_total_bono_alim);
         }


                //MOVIMIENTO DE Bono Medico
           if($amount_total_bono_medico > 0){
                $accounts_bono_medico = DB::connection(Auth::user()->database_name)->table('accounts')
                ->where('code_one','=','6')
                ->where('description','LIKE', 'Bono Medico')
                ->first();

                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_bono_medico->id,$nomina->id,$amount_total_bono_medico,0);

              // bono medico

                $accounts_bono_medico_por_pagar = DB::connection(Auth::user()->database_name)->table('accounts')
                ->where('code_one','=','2')
                ->where('description','LIKE', 'Bono Medico por Pagar')
                ->first();

                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_bono_medico_por_pagar->id,$nomina->id,0,$amount_total_bono_medico);
           }


           if($amount_total_bono_transporte > 0){
                $accounts_bono_medico = DB::connection(Auth::user()->database_name)->table('accounts')
                ->where('code_one','=','6')
                ->where('description','LIKE', 'Bono de Transporte')
                ->first();

                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_bono_medico->id,$nomina->id,$amount_total_bono_transporte,0);

            // bono medico

                $accounts_bono_medico_por_pagar = DB::connection(Auth::user()->database_name)->table('accounts')
                ->where('code_one','=','2')
                ->where('description','LIKE', 'Bono de Transporte por Pagar')
                ->first();

                $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_bono_medico_por_pagar->id,$nomina->id,0,$amount_total_bono_transporte);

        }


       //RETENCIONES
        if ($amount_total_deduccion_sso > 0) {
            $accounts_sso_por_pagar = DB::connection(Auth::user()->database_name)->table('accounts')
            ->where('code_one','=','2')
            ->where('description','LIKE', 'Retencion por Aporte al SSO empleados por Pagar')
            ->first();

            $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_sso_por_pagar->id,$nomina->id,0,$amount_total_deduccion_sso);

        }

        if ($amount_total_deduccion_faov > 0){
            $accounts_faov_por_pagar = DB::connection(Auth::user()->database_name)->table('accounts')
            ->where('code_one','=','2')
            ->where('description','LIKE', 'Retencion por Aporte al FAOV empleados por Pagar')
            ->first();
            $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_faov_por_pagar->id,$nomina->id,0,$amount_total_deduccion_faov);

        }

        if ($amount_total_deduccion_pie > 0){
            $accounts_faov_por_pagar = DB::connection(Auth::user()->database_name)->table('accounts')
            ->where('code_one','=','2')
            ->where('description','LIKE', 'Retencion por Aporte al PIE por Pagar')
            ->first();
            $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_faov_por_pagar->id,$nomina->id,0,$amount_total_deduccion_pie);

        }

        if ($amount_total_deduccion_ince > 0) {
            $accounts_sso_por_pagar = DB::connection(Auth::user()->database_name)->table('accounts')
            ->where('code_one','=','2')
            ->where('description','LIKE', 'Retencion por Aporte al INCES por Pagar')
            ->first();

            $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_sso_por_pagar->id,$nomina->id,0,$amount_total_deduccion_ince);

        }

        //MOVIMIENTO DE aporte patronal
        if ($amount_total_deduccion_sso > 0) {

           /* $total_sso_patronal = (($amount_total_asignacion * 12)/52) * $lunes * ($nominabases->sso_company/100);
            */

            $accounts_sso_patronal = DB::connection(Auth::user()->database_name)->table('accounts')
            ->where('code_one','=','6')
            ->where('description','LIKE', 'Gasto por Aporte al SSO Patronal')
            ->first();
            $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_sso_patronal->id,$nomina->id,$total_sso_patronal,0);


            $accounts_sso_patronal = DB::connection(Auth::user()->database_name)->table('accounts')
            ->where('code_one','=','2')
            ->where('description','LIKE', 'Aportes por Pagar al SSO Patronal')
            ->first();

            $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_sso_patronal->id,$nomina->id,0,$total_sso_patronal);
        }

        if ($amount_total_deduccion_faov > 0){

            $total_faov_patronal = $amount_total_asignacion * ($nominabases->faov_company/100);

            $accounts_aporte_patronal = DB::connection(Auth::user()->database_name)->table('accounts')
            ->where('code_one','=','6')
            ->where('description','LIKE', 'Gasto por Aporte al FAOV Patronal')
            ->first();
            $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_aporte_patronal->id,$nomina->id,$total_faov_patronal,0);

            $accounts_aporte_patronal = DB::connection(Auth::user()->database_name)->table('accounts')
            ->where('code_one','=','2')
            ->where('description','LIKE', 'Aportes por Pagar al FAOV Patronal')
            ->first();

            $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_aporte_patronal->id,$nomina->id,0,$total_faov_patronal);
        }

        if ($amount_total_deduccion_pie > 0){

            $total_pie_patronal =  (($amount_total_asignacion * 12)/52) * $lunes * ($nominabases->pie_company/100);

            $accounts_sso_patronal = DB::connection(Auth::user()->database_name)->table('accounts')
            ->where('code_one','=','6')
            ->where('description','LIKE', 'Gasto por Aporte al PIE Patronal')
            ->first();
            $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_sso_patronal->id,$nomina->id,$total_pie_patronal,0);


            $accounts_pie_patronal = DB::connection(Auth::user()->database_name)->table('accounts')
            ->where('code_one','=','2')
            ->where('description','LIKE', 'Aportes por Pagar al PIE Patronal')
            ->first();

            $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_pie_patronal->id,$nomina->id,0,$total_pie_patronal);
        }


        if ($amount_total_deduccion_ince > 0){

            $total_pie_patronal =  ($amount_total_asignacion * 12)/52 * $lunes * (1/100);

            $accounts_sso_patronal = DB::connection(Auth::user()->database_name)->table('accounts')
            ->where('code_one','=','6')
            ->where('description','LIKE', 'Gasto por Aporte al INCES Patronal')
            ->first();
            $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_sso_patronal->id,$nomina->id,$total_pie_patronal,0);


            $accounts_pie_patronal = DB::connection(Auth::user()->database_name)->table('accounts')
            ->where('code_one','=','2')
            ->where('description','LIKE', 'Aportes por Pagar al INCES Patronal')
            ->first();

            $this->add_movement($nomina->rate ?? $bcv,$header_voucher->id,$accounts_pie_patronal->id,$nomina->id,0,$total_pie_patronal);
        }


    }




         return $header_voucher->id;
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

        if(($nomina->type == "Primera Quincena")){

            $nominaconcepts = NominaConcept::on(Auth::user()->database_name)
            ->where('calculate','S')
            ->where('type','LIKE','%Primera Quincena%')
            ->Orwhere('type','LIKE','%Quincenal%')
            ->get();

        }else if(($nomina->type == "Segunda Quincena")){

            $nominaconcepts = NominaConcept::on(Auth::user()->database_name)
            ->where('calculate','S')
            ->where('type','LIKE','%Segunda Quincena%')
            ->Orwhere('type','LIKE','%Quincenal%')
            ->get();

        }else if(($nomina->type == "Quincenal")){
            $nominaconcepts = NominaConcept::on(Auth::user()->database_name)
            ->where('calculate','S')
            ->where('type','LIKE','%Quincenal%')
            ->get();

        }else if(($nomina->type == "Mensual")){
            $nominaconcepts = NominaConcept::on(Auth::user()->database_name)
            ->where('calculate','S')
            ->where('type','LIKE','%Mensual%')
            ->get();

        }else if(($nomina->type == "Semanal")){
            $nominaconcepts = NominaConcept::on(Auth::user()->database_name)
            ->where('calculate','S')
            ->where('type','LIKE','%Semanal%')
            ->get();

        }else if(($nomina->type == "Especial")){
            $nominaconcepts = NominaConcept::on(Auth::user()->database_name)
            ->where('calculate','S')
            ->where('type','LIKE','Especial')
            ->get();
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
                }else if(($nomina->type == "Asignacion")){ //crear un id_formula_t para la asignacion

                    if(isset($nominaconcept->id_formula_a)){
                        $tiene_calculo = true;
                        $amount = $this->formula($nominaconcept->id_formula_a,$employee,$nomina,$vars);
                    }
                }

                $vars->amount = $amount;
                $vars->status =  "1";
/*
                if ($nominaconcept->asignation == 'S' and $employee->asignacion_general <= 0) {
                    $tiene_calculo = false;
                } */

                if($tiene_calculo == true){
                    $vars->save();


                }
            }


        }

    }

    public function formula($id_formula,$employee,$nomina,$nomina_calculation)
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
        $nominabases  =  NominaBasesCalcs::on(Auth::user()->database_name)->find(1);

        $operacion = $nominaconcepts->description;


        $lunes = $this->calcular_cantidad_de_lunes($nomina);

        //$tasa = $global->search_bcv();

		$variables = [
            "sueldo"=>$employee->monto_pago,
            "lunes"=>$lunes,
            "tasa"=>$nomina->rate,
            "asignaciong"=>0,
            "asig"=>intval($employee->asignacion_general),
            "cestatickets"=>$nominabases->amount_cestatickets,
            "sueldoq" => $employee->monto_pago/2
            /*
            "ssoq"=> (($employee->monto_pago/2) * 12) / 52 * $lunes * 0.04,
            "ssom"=> (($employee->monto_pago) * 12) / 52 * $lunes * 0.04,
            "ssos"=> (($employee->monto_pago/4) * 12) / 52 * $lunes * 0.04,

            "faovq"=> ($employee->monto_pago/2) * 0.01,
            "faovm"=> ($employee->monto_pago) * 0.01,
            "faovs"=> ($employee->monto_pago/4) * 0.01,

            "pieq"=> (($employee->monto_pago/2) * 12) / 52 * $lunes * 0.05,
            "piem"=> (($employee->monto_pago) * 12) / 52 * $lunes * 0.05,
            "pies"=> (($employee->monto_pago/4) * 12) / 52 * $lunes * 0.05,

            "incesq"=> ($employee->monto_pago/2) * 0.01,
            "incesm"=> ($employee->monto_pago) * 0.01,
            "incess"=> ($employee->monto_pago/4) * 0.01,*/
        ];

        //$variables = ["sueldo"=>$monto_pago, "horas"=>0, "dias"=>0, "horas_trabajadas"=>$horas_trabajadas, "horas_faltadas"=>$horas_faltadas, "dias_trabajados"=>$dias_trabajados, "dias_faltados"=>$dias_faltados];
		$total = $this->resolver($operacion,$variables);


        if($total){
            $total = $total;
        } else {
            $total = 0;
        }


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
        if (count($a_param) < 3 ) {
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

        return redirect('/nominas')->withSuccess('Nómina creada con exito!.. Ingrese en Ver Detalles para verificar los conceptos creados, luego de confirmar puede terminar el proceso en Crear el Asiento Contable para contabilizar los montos.');
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
            $this->deleteNominacont($nomina->id);

            $nomina->status = 'X';

            $nomina->save();

            return redirect('/nominas')->withSuccess('Eliminación de Nómina '.$nomina->id.' Exitosa, y comprobantes contables eliminados!');

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
        ->where('status','!=','X')
        ->first();

        if($header_id){

            $detail = DetailVoucher::on(Auth::user()->database_name)
        ->where('id_header_voucher',$header_id->id)
        ->update(['status' => 'X']);

        $header = HeaderVoucher::on(Auth::user()->database_name)
        ->where('id_nomina',$id_nomina)
        ->update(['status' => 'X']);

        }





    }




    public function islrXmlempleado(Request $request)
    {

        $fe = explode("/",$request->per);
        $año = $fe[0];
        $mes = $fe[1];


        $company = Company::on(Auth::user()->database_name)->first();


        $content = '<?xml version="1.0" encoding="UTF-8"?>
         <RelacionRetencionesISLR RifAgente="'.str_replace("-","",$company->code_rif).'" Periodo="'.trim($año.$mes).'">';



        $datospresta = DB::connection(Auth::user()->database_name)
         ->table('nomina_calculations AS a')
         ->join('nominas as b', 'a.id_nomina','b.id')
         ->join('employees as c', 'a.id_employee','c.id')
         ->where(DB::raw('SUBSTR(b.date_end,1,4)'),$año)
         ->where(DB::raw('SUBSTR(b.date_end,6,2)'),$mes)
         ->wherein('a.id_nomina_concept', ['2','3','4'])
         ->select('id_empleado',DB::raw('SUBSTR(b.date_end,1,4) AS año'), DB::raw('SUBSTR(b.date_end,6,2) AS mes'), DB::raw('sum(a.amount) as monto'), 'a.id_nomina_concept',DB::raw('max(b.date_end) as fin'))
         ->groupBy('id_empleado',DB::raw('SUBSTR(b.date_end,1,4)') ,  DB::raw('SUBSTR(b.date_end,6,2)'),  'a.id_nomina_concept')
         ->get();

             foreach ($datospresta as  $datospresta) {

                $newDate = date("d/m/Y", strtotime($datospresta->fin));

                 $content .= '<DetalleRetencion>
                   <RifRetenido>'.$datospresta->id_empleado.'</RifRetenido>
                   <NumeroFactura>'.$mes.$año.'</NumeroFactura>
                   <NumeroControl>'.$mes.$año.'</NumeroControl>
                   <FechaOperacion>'.$newDate.'</FechaOperacion>
                   <CodigoConcepto>001</CodigoConcepto>
                   <MontoOperacion>'.bcdiv($datospresta->monto,'1',2) .'</MontoOperacion>
                   <PorcentajeRetencion>0.00</PorcentajeRetencion>
                  </DetalleRetencion>';

             }

             $content .= '</RelacionRetencionesISLR>';


         // file name to download
         $fileName = "retencionislrempleado.xml";


         // make a response, with the content, a 200 response code and the headers
         return Response::make($content, 200, [
         'Content-type' => 'text/xml',
         'Content-Disposition' => sprintf('attachment; filename="%s"', $fileName),
         'Content-Length' => strlen($content)]);
    }




}
