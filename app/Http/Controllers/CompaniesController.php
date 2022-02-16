<?php

namespace App\Http\Controllers;

use App\Company;
use App\InventaryType;
use App\RateType;
use App\User;
use App\UserCompany;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompaniesController extends Controller
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
        if($users_role == '1'){
           /* $user_companies = UserCompany::on("logins")->where('id_user',Auth::on("logins")->id())->first();
            $users      =   Company::on("logins")->on($user_companies->name_connection)->orderBy('id', 'asc')->get();*/
            $users      =   Company::on("logins")->orderBy('id', 'asc')->get();
        
        }elseif($users_role == '2'){
            return view('admin.index');
        }
        return view('admin.companies.index',compact('users'));
    }

    public function create()
    {
        $bcv = 0;
        $date           = Carbon::now();
        $periodo        = $date->format('Y');

        $tipoinvs       = InventaryType::on("logins")->orderBY('description','asc')->pluck('description','id')->toArray();
        $tiporates      = RateType::on("logins")->orderBY('description','asc')->pluck('description','id')->toArray();

        
        $company = Company::on("logins")->where('login',Auth::user()->database_name)->first();

        return view('admin.companies.create',compact('periodo','tipoinvs','tiporates','bcv','company'));
    }

    public function store(Request $request)
    {

        
        $data = request()->validate([
            'Login'             =>'required|max:191',
            'Email'             =>'required|max:255',
            'Codigo'            =>'required|max:160',
            'Razon_Social'      =>'required|max:160',
            'phone'             =>'required|max:20',
            'Direccion'         =>'required|max:255',
            'Impuesto'          =>'required|max:255',
            'Impuesto_2'        =>'max:10',
            'Impuesto_3'        =>'max:10',
            'Retencion_ISRL'    =>'max:10',
            'Tipo_Inventario'   =>'required|integer|not_in:0',
            'rate_type'         =>'required|integer|not_in:0',
            'Tasa'              =>'required|max:255',
            'Tasa_Petro'        =>'required|max:255',
            'Periodo'           =>'required|max:4',

        ]);

        $razon_social           = strtoupper(request('Razon_Social'));
        $email                  = strtoupper(request('Email'));
        $direccion              = strtoupper(request('Direccion'));

        $company = Company::on("logins")->where('login',Auth::user()->database_name)->first();
        
        $companies  = Company::on("logins")->findOrFail($company->id);
        /*
        $user_companies = UserCompany::on("logins")->where('id_user',Auth::on("logins")->id())->first();
        $companies->setConnection($user_companies->name_connection);*/

        $companies->email           = $email;
        $companies->code_rif        = request('Codigo');
        $companies->razon_social    = $razon_social;
        $companies->phone           = request('phone');
        $companies->franqueo_postal = request('Franqueo_Postal');
        $companies->address         = $direccion;
        $companies->tax_1           = str_replace(',', '.', str_replace('.', '', request('Impuesto')));

        $companies->tax_2           = str_replace(',', '.', str_replace('.', '', request('Impuesto_2')));
        $companies->tax_3           = str_replace(',', '.', str_replace('.', '', request('Impuesto_3')));
        $companies->retention_islr  = str_replace(',', '.', str_replace('.', '', request('Retencion_ISRL')));
        $companies->tipoinv_id      = request('Tipo_Inventario');
        $companies->tiporate_id     = request('rate_type');
        $companies->rate            = str_replace(',', '.', str_replace('.', '', request('Tasa')));
        $companies->rate_petro      = str_replace(',', '.', str_replace('.', '', request('Tasa_Petro')));
        $companies->period          = request('Periodo');

        $companies->status          = '1';
        $companies->pie_pagina     = request('pie_pagina');
        $companies->message_from_email     = request('message_from_email');

        $companies->save();

       // $company = Company::on(Auth::user()->database_name)->where('login',Auth::user()->database_name)->first();
        
        $companies  = Company::on(Auth::user()->database_name)->findOrFail(1);
       

        $companies->email           = $email;
        $companies->code_rif        = request('Codigo');
        $companies->razon_social    = $razon_social;
        $companies->phone           = request('phone');
        $companies->franqueo_postal = request('Franqueo_Postal');
        $companies->address         = $direccion;
        $companies->tax_1           = str_replace(',', '.', str_replace('.', '', request('Impuesto')));

        $companies->tax_2           = str_replace(',', '.', str_replace('.', '', request('Impuesto_2')));
        $companies->tax_3           = str_replace(',', '.', str_replace('.', '', request('Impuesto_3')));
        $companies->retention_islr  = str_replace(',', '.', str_replace('.', '', request('Retencion_ISRL')));
        $companies->tipoinv_id      = request('Tipo_Inventario');
        $companies->tiporate_id     = request('rate_type');
        $companies->rate            = str_replace(',', '.', str_replace('.', '', request('Tasa')));
        $companies->rate_petro      = str_replace(',', '.', str_replace('.', '', request('Tasa_Petro')));
        $companies->period          = request('Periodo');

        $companies->status          = '1';

        $companies->pie_pagina     = request('pie_pagina');
        $companies->message_from_email     = request('message_from_email');

        $companies->save();
        
        return redirect('/companies/register')->withSuccess('Actualizado Exitosamente!');
    }

    public function edit($id)
    {
        $company            = Company::on("logins")->find($id);

        $urlToGet ='http://www.bcv.org.ve/tasas-informativas-sistema-bancario';
        $pageDocument = @file_get_contents($urlToGet);
        preg_match_all('|<div class="col-sm-6 col-xs-6 centrado"><strong> (.*?) </strong> </div>|s', $pageDocument, $cap);

        if ($cap[0] == array()){ // VALIDAR Concidencia
            $titulo = '0,00';
        } else {
            $titulo = $cap[1][4];
        }

        $bcv            = $titulo;
        $date           = Carbon::now();
        $periodo        = $date->format('Y');
        $tipoinvs       = InventaryType::on("logins")->orderBY('description','asc')->pluck('description','id')->toArray();
        $tiporates      = RateType::on("logins")->orderBY('description','asc')->pluck('description','id')->toArray();


        return view('admin.companies.edit',compact('company','bcv','periodo','tipoinvs','tiporates'));
    }

    public function update(Request $request,$id)
    {
        $validar              =  Company::on("logins")->find($id);

        $request->validate([
            'Nombre'         =>'required|max:191,'.$validar->id,
            'Email'          =>'required|max:255,'.$validar->id,
            'Codigo'         =>'required|max:4',
            'Razon_Social'   =>'required|max:160,'.$validar->id,
            'Descripcion'    =>'required|max:255',
            'Estado'         =>'required|max:2',
        ]);

        $nombre              = strtoupper(request('Nombre'));
        $email               = strtoupper(request('Email'));
        $descripcion         = strtoupper(request('Descripcion'));
        $codigo              = strtoupper(request('Codigo'));
        $razon_social        = strtoupper(request('Razon_Social'));
        $resul_social        = $codigo.$razon_social;

        $companies          = Company::on("logins")->findOrFail($id);
        $companies->name                = $nombre;
        $companies->email               = $email;
        $companies->description         = $descripcion;
        $companies->razon_social        = $resul_social;
        $companies->status              = request('Estado');

        $companies->save();
        return redirect('/companies')->withSuccess('Registro Guardado Exitoso!');

    }

    public function destroy(Request $request)
    {
        //find the Division
        $user = User::on("logins")->find($request->user_id);

        //Elimina el Division
        $user->delete();
        return redirect('users')->withDelete('Registro Eliminado Exitoso!');
    }



    public function search_bcv()
    {
        /*Buscar el indice bcv*/
        $urlToGet ='http://www.bcv.org.ve/tasas-informativas-sistema-bancario';
        $pageDocument = @file_get_contents($urlToGet);
        preg_match_all('|<div class="col-sm-6 col-xs-6 centrado"><strong> (.*?) </strong> </div>|s', $pageDocument, $cap);

        if ($cap[0] == array()){ // VALIDAR Concidencia
            $titulo = '0,00';
        }else {
            $titulo = $cap[1][4];
        }

        $bcv_con_formato = $titulo;
        $bcv = str_replace(',', '.', str_replace('.', '',$bcv_con_formato));


        /*-------------------------- */
       return bcdiv($bcv, '1', 2);

    }

    public function bcvlist(Request $request){
        //validar si la peticion es asincrona
        if($request->ajax()){
            try{
                
                $respuesta = $this->search_bcv();
                $respuesta = number_format($respuesta, 2, ',', '.');
                
                return response()->json($respuesta,200);

            }catch(Throwable $th){
                return response()->json(false,500);
            }
        }
        
    }

}
