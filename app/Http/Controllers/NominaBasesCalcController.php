<?php

namespace App\Http\Controllers;


use App\NominaBasesCalc;

use App\Profession;
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
        
        $nominaconcepts  =  NominaBasesCalc::on(Auth::user()->database_name)->orderBy('id', 'asc')->get();


    
        return view('admin.nominabasescalc.index',compact('nominabasescalc'));
      
    }

    /*public function create()
    {
       
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');
        $formulas = NominaFormula::on(Auth::user()->database_name)->orderBy('id','asc')->get();


        return view('admin.nominaconcepts.create',compact('datenow','formulas'));
    }

    public function store(Request $request)
    {
       
        $data = request()->validate([
           
            'order'         =>'required',
            'abbreviation'  =>'required',
            'description'   =>'required|max:60',
            'type'          =>'required',
            'sign'          =>'required',

            'calculate'     =>'required',
           

            'minimum'     =>'required',
            'maximum'     =>'required',
            
            
           
        ]);

        $users = new NominaConcept();
        $users->setConnection(Auth::user()->database_name);

        $users->order = request('order');
        $users->abbreviation = request('abbreviation');
        $users->description = request('description');
        $users->type = request('type');
       
        $users->sign = request('sign');
        
        $users->calculate = request('calculate');
        $users->id_formula_m = request('formula_m');
        $users->id_formula_s = request('formula_s');
        $users->id_formula_q = request('formula_q');

        $valor_sin_formato_minimum = str_replace(',', '.', str_replace('.', '', request('minimum')));
        $valor_sin_formato_maximum = str_replace(',', '.', str_replace('.', '', request('maximum')));


        $users->minimum = $valor_sin_formato_minimum;
        $users->maximum = $valor_sin_formato_maximum;


        $users->status =  "1";
       
       

        $users->save();

        return redirect('/nominaconcepts')->withSuccess('Registro Exitoso!');
    }



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
