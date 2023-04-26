<?php

namespace App\Http\Controllers;

use App\Client;
use App\Branch;
use App\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{


    public function __construct(){

        $this->middleware('auth');
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Clientes');
       }

       public function index(Request $request)
       {

        $agregarmiddleware = $request->get('agregarmiddleware');
        $actualizarmiddleware = $request->get('actualizarmiddleware');
        $eliminarmiddleware = $request->get('eliminarmiddleware');


            $user= auth()->user();

            $clients = Client::on(Auth::user()->database_name)->where('status',1)->orderBy('id' ,'DESC')->get();


            return view('admin.clients.index',compact('actualizarmiddleware','agregarmiddleware','clients'));

   }

   public function create(Request $request)
   {

    if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){
        $vendors = Vendor::on(Auth::user()->database_name)->orderBy('name','asc')->get();
        $branches = Branch::on(Auth::user()->database_name)->orderBy('description','desc')->get();

       return view('admin.clients.create',compact('vendors','branches'));


    }else{

        return redirect('/clients')->withDelete('No Tienes Permiso!');
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

                $clientes = client::on(Auth::user()->database_name)
                ->Where('cedula_rif',request('cedula_rif'))
                ->orWhere('name',request('name'))
                ->get();

                if($clientes->count() > 0){
                    $resp['error'] = false;
                    $resp['msg'] = 'Codigo del Cliente o Razon Social Ya se Encuentra Registrado';

                    return response()->json($resp);
                }


                $users = new client();
                $users->setConnection(Auth::user()->database_name);

                $users->id_vendor = request('id_vendor');
                $users->id_user = request('id_user');
                $users->type_code = request('type_code');

                $users->name = request('name');
                $users->name_ref = request('namecomercial');
                $users->cedula_rif = request('cedula_rif');
                $users->direction = request('direction');
                $users->city = request('city');
                $users->country = request('country');
                $users->phone1 = request('phone1');
                $users->phone2 = request('phone2');
                $users->email = request('email');
                $users->aliquot = request('aliquot');
                $users->id_cost_center = request('id_cost_center');
                $users->personcontact = request('personcontact');

                $users->days_credit = request('days_credit');

                if(request('amount_max_credit') != null){
                    $sin_formato_amount_max_credit = str_replace(',', '.', str_replace('.', '', request('amount_max_credit')));
                }
                if(request('percentage_retencion_iva') != null){
                    $sin_formato_percentage_retencion_iva = str_replace(',', '.', str_replace('.', '', request('percentage_retencion_iva')));
                }
                if(request('percentage_retencion_islr') != null){
                    $sin_formato_percentage_retencion_islr = str_replace(',', '.', str_replace('.', '', request('percentage_retencion_islr')));
                }

                $users->amount_max_credit = $sin_formato_amount_max_credit ?? 0;

                $users->percentage_retencion_iva = $sin_formato_percentage_retencion_iva ?? 0;
                $users->percentage_retencion_islr = $sin_formato_percentage_retencion_islr ?? 0;

                $users->status =  1;

                $users->save();


                $resp['error'] = true;
                $resp['msg'] = 'Cliente Registrado';

                return response()->json($resp);


            }catch(\error $error){
                $resp['error'] = false;
                $resp['msg'] = 'Error.';

                return response()->json($resp);
            }
        }



    }else{

        return redirect('/clients')->withDelete('No Tienes Permiso!');
    }
}



   public function edit(request $request,$id)
   {

    if(Auth::user()->role_id == '1' || $request->get('actualizarmiddleware') == '1'){

        $var = client::on(Auth::user()->database_name)->find($id);

        $vendors = Vendor::on(Auth::user()->database_name)->orderBy('name','asc')->get();

        $branches = Branch::on(Auth::user()->database_name)->orderBy('description','desc')->get();


        return view('admin.clients.edit',compact('var','vendors','branches'));



    }else{

        return redirect('/clients')->withDelete('No Tienes Permiso!');
    }

   }


   public function update(Request $request, $id)
   {
    if(Auth::user()->role_id == '1' || $request->get('actualizarmiddleware') == '1'){

    $vars =  client::on(Auth::user()->database_name)->find($id);
    $vars_status = $vars->status;

    $data = request()->validate([
        'type_code'         =>'required|max:20',

        'razon_social'         =>'required|max:100',
        'cedula_rif'         =>'required',
        'direction'         =>'required|max:300',
        'city'         =>'required',
        'country'         =>'required',
        'phone1'         =>'required',
        'days_credit'         =>'required|integer',



    ]);


    $users = client::on(Auth::user()->database_name)->findOrFail($id);

    $users->id_vendor = request('id_vendor');

    $users->type_code = request('type_code');

    $users->name = request('razon_social');
    $users->name_ref = request('namecomercial');

    $users->cedula_rif = request('cedula_rif');
    $users->direction = request('direction');
    $users->city = request('city');
    $users->country = request('country');
    $users->phone1 = request('phone1');
    $users->phone2 = request('phone2');
    $users->email = request('email');
    $users->aliquot = request('aliquot');
    $users->id_cost_center = request('id_cost_center');
    $users->personcontact = request('personcontact');
    $users->days_credit = request('days_credit');

    $sin_formato_amount_max_credit = str_replace(',', '.', str_replace('.', '', request('amount_max_credit')));


    $users->amount_max_credit = $sin_formato_amount_max_credit;

    $users->percentage_retencion_iva = request('retencion_iva');
    $users->percentage_retencion_islr = request('retencion_islr');

    if(request('status') == null){
        $users->status = $vars_status;
    }else{
        $users->status = request('status');
    }

    $users->save();

    return redirect('/clients')->withSuccess('Actualizacion Exitosa!');

        }else{

            return redirect('/clients')->withDelete('No Tienes Permiso!');
        }
    }


}
