<?php

namespace App\Http\Controllers;

use App\Employee;
use App\NominaBasesCalcs;
use App\BasesCalcs;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NominaBasesCalcController extends Controller
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

    public function index()
    {
        $user= auth()->user();
        $employees = Employee::on(Auth::user()->database_name)->where('status','NOT LIKE','X')->orderBy('id' ,'DESC')->get();
       
        return view('admin.employees.index',compact('employees'));
    }
   /* public function index()
    {

        $user       =   auth()->user();
        $users_role =   $user->role_id;
        
        $nominabases  =  NominaBasesCalcs::on(Auth::user()->database_name)->find(1);
        $bases = BasesCalcs::on($this->conection_logins)->find(1);


        return view('admin.nominabasescalc.index',compact('nominabases','bases'));
      
    }


    public function store(Request $request)
    {
       
        $data = request()->validate([
           
        ]);

        $datos = NominaBasesCalcs::on(Auth::user()->database_name)->findOrFail(1);

        $datos->salary_min = str_replace(',', '.', str_replace('.', '', request('salary_min')));
        $datos->salary_min_USD = str_replace(',', '.', str_replace('.', '', request('salary_min_USD')));
        $datos->salary_max = str_replace(',', '.', str_replace('.', '', request('salary_max')));
        $datos->salary_max_USD = str_replace(',', '.', str_replace('.', '', request('salary_max_USD')));
        $datos->amount_cestatickets = str_replace(',', '.', str_replace('.', '', request('amount_cesta')));
        $datos->amount_cestatickets_USD = str_replace(',', '.', str_replace('.', '', request('amount_cesta_USD')));
        $datos->days_vacations = request('days_vacations');
        $datos->days_bond_vacations = request('days_bond_vacations');
        $datos->days_utility_min = request('days_utility_min');
        $datos->days_utility_max = request('days_utility_max');
        $datos->days_social_benefits = request('days_social_benefits');
        $datos->rate_social_benefits = request('rate_social_benefits');
        $datos->sso = str_replace(',', '.', str_replace('.', '', request('sso')));
        $datos->faov = str_replace(',', '.', str_replace('.', '', request('faov')));
        $datos->pie = str_replace(',', '.', str_replace('.', '', request('pie')));
        $datos->sso_company = str_replace(',', '.', str_replace('.', '', request('sso_company')));
        $datos->faov_company = str_replace(',', '.', str_replace('.', '', request('faov_company')));
        $datos->pie_company = str_replace(',', '.', str_replace('.', '', request('pie_company')));

        $datos->save();

        return redirect('/nominabasescalc')->withSuccess('Datos Actualizados Exitosamente!');
    }*/




}
