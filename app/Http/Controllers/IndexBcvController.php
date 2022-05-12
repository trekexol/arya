<?php

namespace App\Http\Controllers;

use App\IndexBcv;
use Illuminate\Http\Request;

use Validator;
use Illuminate\Support\Facades\Auth;

class IndexBcvController extends Controller
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
        
        $user       =   auth()->user();
        $users_role =   $user->role_id;
     
           $indexbcvs = IndexBcv::on($this->conection_logins)->orderBy('period', 'desc')
           ->orderBy('month', 'desc')->get();
          // $bases = BasesCalcs::on($this->conection_logins)->find(1);

    
        return view('admin.indexbcvs.index',compact('indexbcvs'));
      
    }

    public function create()
    {

        

        return view('admin.indexbcvs.create');
    }

    public function store(Request $request)
    {
        
        $data = request()->validate([
           
            'period'    =>'required|max:4',
            'month'     =>'required|max:2',
            'rate'      =>'required',
            'status'    =>'required|max:1',
            
           
        ]);

        $var = new IndexBcv();
        $var->setConnection(Auth::user()->database_name);

        $var->period = request('period');
        $var->month = request('month');
        $var->rate =  request('rate');
       
        $var->status =  request('status');
       

        $var->save();

        return redirect('indexbcvs')->withSuccess('Registro Exitoso!');
    }



    public function edit($id)
    {

        $var = IndexBcv::on(Auth::user()->database_name)->find($id);
        
        return view('admin.indexbcvs.edit',compact('var'));
    }

   


    public function update(Request $request,$id)
    {
       
        $vars =  Indexbcv::on(Auth::user()->database_name)->find($id);

        $var_status = $vars->status;
      

        $data = request()->validate([
           
            'period'    =>'required|max:4',
            'month'     =>'required|max:2',
            'rate'      =>'required',
            
            'status'    =>'required|max:1',
            
           
        ]);

        $var  = IndexBcv::on(Auth::user()->database_name)->findOrFail($id);
        $var->period = request('period');
        $var->month = request('month');
        $var->rate =  request('rate');
       
        if(request('status') == null){
            $var->status = $var_status;
        }else{
            $var->status = request('status');
        }
       

        $var->save();


        return redirect('indexbcvs')->withSuccess('Registro Guardado Exitoso!');

    }


}
