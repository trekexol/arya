<?php

namespace App\Http\Controllers;

use App\Provider;
use App\IslrConcept;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProviderController extends Controller
{

    public function __construct(){

        $this->middleware('auth');
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Proveedores');
       }

   public function index(request $request)
   {
    $agregarmiddleware = $request->get('agregarmiddleware');
    $actualizarmiddleware = $request->get('actualizarmiddleware');
    $eliminarmiddleware = $request->get('eliminarmiddleware');

       $providers = Provider::on(Auth::user()->database_name)->orderBy('id' ,'DESC')->get();

       return view('admin.providers.index',compact('providers','agregarmiddleware','actualizarmiddleware','eliminarmiddleware'));
   }


   public function create(Request $request)
   {
    if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){

       $islrconcepts = IslrConcept::on(Auth::user()->database_name)->orderBy('id','asc')->get();
       
       return view('admin.providers.create',compact('islrconcepts'));

    }else{
        return redirect('/providers')->withDanger('No Tiene Permiso!');
    }

   }


   public function store(Request $request)
    {
        $resp = array();
        $resp['error'] = false;
        $resp['msg'] = '';

    if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){


    if($request->ajax()){
        try{

            $providers = Provider::on(Auth::user()->database_name)
            ->Where('code_provider',$request->type_code.request('code_provider'))
            ->orWhere('razon_social',request('razon_social'))
            ->get();

            if($providers->count() > 0){
                $resp['error'] = false;
                $resp['msg'] = 'Codigo de Proveedor o Razon Social Ya se Encuentra Registrado';

                return response()->json($resp);
            }

            if(request('phone1') == null){
                $request->phone1 = 0;
            }
            if(request('phone2') == null){
                $request->phone2 = 0;
            }
            if(request('days_credit') == null){
                $request->days_credit = 0;
            }
            if(request('amount_max_credit') == null){
                $request->amount_max_credit = 0;
            }
            if(request('balance') == null){
                $request->balance = 0;
            }
            if(request('porc_retencion_iva') == null){
                $request->porc_retencion_iva = 0;
            }

            if(request('porc_retencion_islr') == null){
                $request->porc_retencion_islr = 0;
            }


            $users = new Provider();
            $users->setConnection(Auth::user()->database_name);
            $users->code_provider = $request->type_code.request('code_provider');
            $users->razon_social = request('razon_social');
            $users->direction = request('direction');
            $users->city = request('city');
            $users->country = request('country');
            $users->phone1 = $request->phone1;
            $users->phone2 = $request->phone1;
            $has_credit = request('has_credit');
            if($has_credit == null){
                $users->has_credit = false;
            }else{
                $users->has_credit = true;
            }

            $users->days_credit = $request->days_credit;

            $sin_formato_amount_max_credit = str_replace(',', '.', str_replace('.', '', $request->amount_max_credit));
            $sin_formato_balance = str_replace(',', '.', str_replace('.', '', $request->balance));

            $users->amount_max_credit = $sin_formato_amount_max_credit;
            $users->porc_retencion_iva = $request->porc_retencion_iva;
            $users->porc_retencion_islr = $request->porc_retencion_islr;
            $users->concepto_islr = request('islr_concept');
            $users->balance = $sin_formato_balance;
            $users->status =  1;
            $users->save();

            $resp['error'] = true;
	        $resp['msg'] = 'Proveedor Registrado';

            return response()->json($resp);


        }catch(\error $error){
            $resp['error'] = false;
	        $resp['msg'] = 'Error.';

            return response()->json($resp);
        }
    }

        }else{
            return redirect('/providers')->withDanger('No Tiene Permiso!');
        }
    }



   public function edit(request $request,$id)
   {
    if(Auth::user()->role_id == '1' || $request->get('actualizarmiddleware') == '1'){
       
        $islrconcepts = IslrConcept::on(Auth::user()->database_name)->orderBy('id','asc')->get();
       
        $var = Provider::on(Auth::user()->database_name)->find($id);


        return view('admin.providers.edit',compact('var','islrconcepts'));

    }else{
        return redirect('/providers')->withDanger('No Tiene Permiso!');
    }

   }


   public function update(Request $request, $id)
   {

    if(Auth::user()->role_id == '1' || $request->get('actualizarmiddleware') == '1'){
    $data = request()->validate([
        'code_provider'         =>'required|max:20',
        'razon_social'          =>'required|max:80',
        'direction'             =>'required|max:100',

        'city'                  =>'required|max:20',
        'country'               =>'required|max:20',
        'phone1'                =>'required|max:20',
        'phone2'                =>'required|max:20',
        'days_credit'           =>'required',
        'amount_max_credit'     =>'required',
        'porc_retencion_iva'    =>  'numeric|min:0|max:100',
        'balance'               =>'required',



    ]);

    $users = Provider::on(Auth::user()->database_name)->findOrFail($id);



    $users->code_provider = $request->type_code.request('code_provider');
    $users->razon_social = request('razon_social');
    $users->direction = request('direction');
    $users->city = request('city');
    $users->country = request('country');
    $users->phone1 = request('phone1');
    $users->phone2 = request('phone2');

    $has_credit = request('has_credit');
    if($has_credit == null){
        $users->has_credit = false;
    }else{
        $users->has_credit = true;
    }

    $sin_formato_amount_max_credit = str_replace(',', '.', str_replace('.', '', request('amount_max_credit')));
    $sin_formato_balance = str_replace(',', '.', str_replace('.', '', request('balance')));



    $users->days_credit = request('days_credit');
    $users->amount_max_credit = $sin_formato_amount_max_credit;
    $users->porc_retencion_iva = request('porc_retencion_iva');
    $users->porc_retencion_islr = request('porc_retencion_islr');
    $users->concepto_islr = request('islr_concept');

    $users->balance = $sin_formato_balance;
    $users->status =  request('status');



    $users->save();

    return redirect('/providers')->withSuccess('Actualizacion Exitosa!');

}else{
    return redirect('/providers')->withDanger('No Tiene Permiso!');
}
    }





}
