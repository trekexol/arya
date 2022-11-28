<?php

namespace App\Http\Controllers;

use App\Company;
use Illuminate\Http\Request;
use App\InventaryType;
use App\User;
use Illuminate\Support\Facades\Auth;

class InventaryTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Tipos de Inventario');
    }

    public function index(Request $request)
    {

        $agregarmiddleware = $request->get('agregarmiddleware');
        $actualizarmiddleware = $request->get('actualizarmiddleware');
        $eliminarmiddleware = $request->get('eliminarmiddleware');

        
        $user       =   auth()->user();
        $users_role =   $user->role_id;

        $inventarytypes      =   InventaryType::on(Auth::user()->database_name)->orderBy('id', 'asc')->get();
                return view('admin.inventarytypes.index',compact('inventarytypes','agregarmiddleware'));
  
    }

    public function create(Request $request)
    {
  
        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){
        return view('admin.inventarytypes.create');
    }else{
        return redirect('/inventarytypes')->withSuccess('No Tiene Acceso a Registrar');
        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){
        $data = request()->validate([
            'Descripcion'    =>'required|max:255',
        ]);

        $descripcion     = strtoupper(trim(request('Descripcion')));
        $inventaryTypes  = new InventaryType();
        $inventaryTypes->setConnection(Auth::user()->database_name);

        $inventaryTypes->description      = $descripcion;
        $inventaryTypes->status          = '1';

        $inventaryTypes->save();
        return redirect('/inventarytypes')->withSuccess('Registro Exitoso!');
    }else{
        return redirect('/inventarytypes')->withSuccess('No Tiene Acceso a Registrar');
        }
    }

    public function edit(Request $request,$id)
    {

        if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){
        $company            = Company::on(Auth::user()->database_name)->find($id);
        $codigo             = substr($company->razon_social,0,2);
        $razon_social       = substr($company->razon_social,2);

        return view('admin.companies.edit',compact('company','codigo','razon_social'));
    }else{
        return redirect('/inventarytypes')->withSuccess('No Tiene Acceso a Editar');
        }
    }

    public function update(Request $request,$id)
    {

        if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){
        $validar              =  Company::on(Auth::user()->database_name)->find($id);

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

        $companies          = Company::on(Auth::user()->database_name)->findOrFail($id);
        $companies->name                = $nombre;
        $companies->email               = $email;
        $companies->description         = $descripcion;
        $companies->razon_social        = $resul_social;
        $companies->status              = request('Estado');

        $companies->save();
        return redirect('/companies')->withSuccess('Registro Guardado Exitoso!');
    }else{
        return redirect('/inventarytypes')->withSuccess('No Tiene Acceso a Editar');
        }

    }

    public function destroy(Request $request)
    {
        //find the Division
        $user = User::on(Auth::user()->database_name)->find($request->user_id);

        //Elimina el Division
        $user->delete();
        return redirect('users')->withDelete('Registro Eliminado Exitoso!');
    }
}
