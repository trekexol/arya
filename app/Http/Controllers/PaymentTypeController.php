<?php

namespace App\Http\Controllers;

use App\PaymentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentTypeController extends Controller
{
  
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Tipos de Pagos');
    }

    public function index(Request $request)
    {

        $agregarmiddleware = $request->get('agregarmiddleware');
        $actualizarmiddleware = $request->get('actualizarmiddleware');
        $eliminarmiddleware = $request->get('eliminarmiddleware');

        $paymenttypes = PaymentType::on(Auth::user()->database_name)->orderBy('id', 'asc')->get();
       
        return view('admin.paymenttypes.index',compact('paymenttypes','agregarmiddleware','actualizarmiddleware','eliminarmiddleware'));
      
    }

    public function create(Request $request)
    {
  
        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){

        return view('admin.paymenttypes.create');
    }else{
        return redirect('/paymenttypes')->withSuccess('No Tiene Acceso a Registrar');
        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){
        $data = request()->validate([
           
            'description'   =>'required|max:100',
            'type'          =>'required|max:15',
            'credit_days'   =>'required|integer',
            'pide_ref'      =>'required|max:15',
            'small_box'     =>'required|max:15',
            'nature'        =>'required|max:15',
            'point'         =>'required|max:15',


            'status'        =>'required|max:1',
            
           
        ]);

        $users = new Paymenttype();
        $users->setConnection(Auth::user()->database_name);

        $users->description = request('description');
        $users->type = request('type');
        $users->credit_days =  request('credit_days');
        $users->pide_ref = request('pide_ref');
        $users->small_box =  request('small_box');
        $users->nature =  request('nature');
        $users->point =  request('point');

        $users->status =  request('status');
       

        $users->save();

        return redirect('paymenttypes')->withSuccess('Registro Exitoso!');
    }else{
        return redirect('/paymenttypes')->withSuccess('No Tiene Acceso a Registrar');
        }
    }



    public function edit(Request $request,$id)
    {

        if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){
        $var = Paymenttype::on(Auth::user()->database_name)->find($id);
        
        return view('admin.paymenttypes.edit',compact('var'));
    }else{
        return redirect('/paymenttypes')->withSuccess('No Tiene Acceso a Editar');
        }
    }

   


    public function update(Request $request,$id)
    {
        if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){
        $vars =  Paymenttype::on(Auth::user()->database_name)->find($id);

        $var_status = $vars->status;
      

        $data = request()->validate([
           
            'description'   =>'required|max:100',
            'type'          =>'required|max:15',
            'credit_days'   =>'required|integer',
            'pide_ref'      =>'required|max:15',
            'small_box'     =>'required|max:15',
            'nature'        =>'required|max:15',
            'point'         =>'required|max:15',


            'status'        =>'required|max:1',
            
           
        ]);

        $var  = Paymenttype::on(Auth::user()->database_name)->findOrFail($id);
        $var->description        = request('description');
       
        $var->type = request('type');
        $var->credit_days =  request('credit_days');
        $var->pide_ref = request('pide_ref');
        $var->small_box =  request('small_box');
        $var->nature =  request('nature');
        $var->point =  request('point');

        if(request('status') == null){
            $var->status = $var_status;
        }else{
            $var->status = request('status');
        }
       

        $var->save();


        return redirect('paymenttypes')->withSuccess('Registro Guardado Exitoso!');

    }else{
        return redirect('/paymenttypes')->withSuccess('No Tiene Acceso a Editar');
        }

    }


}
