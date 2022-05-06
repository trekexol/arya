<?php

namespace App\Http\Controllers;


use App\NominaBasesCalcs;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NominaBasesCalcController extends Controller
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
        
        $nominabases  =  NominaBasesCalcs::on(Auth::user()->database_name)->find(1);

    
        return view('admin.nominabasescalc.index',compact('nominabases'));
      
    }

    /*public function create()
    {
       
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');
        $formulas = NominaFormula::on(Auth::user()->database_name)->orderBy('id','asc')->get();


        return view('admin.nominaconcepts.create',compact('datenow','formulas'));
    } */

    public function store(Request $request)
    {
       
        $data = request()->validate([
           /*
            'abbreviation'  =>'required',
            'description'   =>'required|max:60',*/

            
           
        ]);

        dd($request);

        $datos = new NominaBasesCalcs();
        $datos->setConnection(Auth::user()->database_name);

        $datos->salary_min = request('salary_min');
        $datos->salary_min_USD = request('salary_min_USD');
        $datos->salary_max = request('salary_max');
        $datos->salary_max_USD = request('salary_max_USD');
        $datos->amount_cestatickets = request('amount_cestatickets');
        $datos->days_vacations = request('days_vacations');
        $datos->days_bond_vacations = request('days_bond_vacations');
        $datos->days_utility_min = request('days_utility_min');
        $datos->days_utility_max = request('days_utility_max');
        $datos->days_social_benefits = request('days_social_benefits');
        $datos->rate_social_benefits = request('rate_social_benefits');
        $datos->sso = request('sso');
        $datos->faov = request('faov');
        $datos->pie = request('pie');
        $datos->sso_company = request('sso_company');
        $datos->faov_company = request('faov_company');
        $datos->pie_company = request('pie_company');


        $valor_sin_formato_minimum = str_replace(',', '.', str_replace('.', '', request('minimum')));
        $valor_sin_formato_maximum = str_replace(',', '.', str_replace('.', '', request('maximum')));


        $datos->minimum = $valor_sin_formato_minimum;
        $datos->maximum = $valor_sin_formato_maximum;


        $datos->status =  "1";
       
       

        $datos->save();

        return redirect('/nominaconcepts')->withSuccess('Registro Exitoso!');
    }


/*
    public function edit($id)
    {

        $var  = NominaConcept::on(Auth::user()->database_name)->find($id);

        $formulas  = NominaFormula::on(Auth::user()->database_name)->orderBy('description','asc')->get();

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');

       // dd($var);
        return view('admin.nominaconcepts.edit',compact('var','datenow','formulas'));
        
    }

   



    public function update(Request $request,$id)
    {
       
        $vars =  NominaConcept::on(Auth::user()->database_name)->find($id);
        $var_status = $vars->status;
      

        $data = request()->validate([
           
            'order'         =>'required',
            'abbreviation'         =>'required',
            'description'   =>'required|max:60',
            'type'          =>'required',
            'sign'          =>'required',

            'calculate'     =>'required',
           

            'minimum'     =>'required',
            'maximum'     =>'required',
            
            
           
        ]);

        $var = NominaConcept::on(Auth::user()->database_name)->findOrFail($id);

        $var->order = request('order');
        $var->abbreviation = request('abbreviation');
        $var->description = request('description');
        $var->type = request('type');
       
        $var->sign = request('sign');
        
        $var->calculate = request('calculate');
        $var->formula_m = request('formula_m');
        $var->formula_s = request('formula_s');
        $var->formula_q = request('formula_q');

        $valor_sin_formato_minimum = str_replace(',', '.', str_replace('.', '', request('minimum')));
        $valor_sin_formato_maximum = str_replace(',', '.', str_replace('.', '', request('maximum')));


        $var->minimum = $valor_sin_formato_minimum;
        $var->maximum = $valor_sin_formato_maximum;
       
        if(request('status') == null){
            $var->status = $var_status;
        }else{
            $var->status = request('status');
        }
       

        $var->save();


        return redirect('/nominaconcepts')->withSuccess('Registro Guardado Exitoso!');

    } */


}
