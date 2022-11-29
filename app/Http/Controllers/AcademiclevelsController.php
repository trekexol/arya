<?php

namespace App\Http\Controllers;

use App\Academiclevel;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AcademiclevelsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Niveles Académicos');
    }


 
    public function index(Request $request)
    {

    
        $agregarmiddleware = $request->get('agregarmiddleware');
        $actualizarmiddleware = $request->get('actualizarmiddleware');
        $eliminarmiddleware = $request->get('eliminarmiddleware');
        
        $academiclevels   =  Academiclevel::on(Auth::user()->database_name)->orderBy('id', 'asc')->get();
    
    
        return view('admin.academiclevels.index',compact('academiclevels','agregarmiddleware','actualizarmiddleware','eliminarmiddleware'));
      
    }

    public function create(Request $request)
    {
        
    
        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){
        return view('admin.academiclevels.create');
        }else{

            return redirect('/academiclevels')->withSuccess('No Tiene Acceso a Registrar');


        }
    }

    public function store(Request $request)
    {

        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){

        $data = request()->validate([
           
            'name'         =>'required|max:160',
            'description'         =>'required|max:255',
            'status'         =>'required|max:2',
            
           
        ]);

        $users = new Academiclevel();
        $users->setConnection(Auth::user()->database_name);
        $users->name = request('name');
        $users->description = request('description');
        $users->status =  request('status');
       

        $users->save();

        return redirect('/academiclevels')->withSuccess('Registro Exitoso!');


    }else{

        return redirect('/academiclevels')->withDelete('No Tiene Permiso a Registrar');


    }

    }




    public function edit(Request $request,$id)
    {
        if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){
        $user = Academiclevel::on(Auth::user()->database_name)->find($id);
        
        return view('admin.academiclevels.edit',compact('user'));
        }else{

            return redirect('/academiclevels')->withDelete('No Tiene Permiso a Editar');
        }
    }

   


    public function update(Request $request,$id)
    {

        if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){
            $users =  Academiclevel::on(Auth::user()->database_name)->find($id);
            $user_rol = $users->role_id;
            $user_status = $users->status;
        

            $request->validate([
                'name'      =>'required|string|max:255',
                'description'      =>'required|string|max:255',
                'status'     =>'max:2',
            ]);

        

            $user          = Academiclevel::on(Auth::user()->database_name)->findOrFail($id);
            $user->name         = request('name');
            $user->description        = request('description');
        
            if(request('status') == null){
                $user->status = $user_status;
            }else{
                $user->status = request('status');
            }
       

             $user->save();


              return redirect('/academiclevels')->withSuccess('Registro Guardado Exitoso!');

            /* }


                public function destroy(Request $request)
                {
                    //find the Division
                    $user = User::on(Auth::user()->database_name)->find($request->user_id);

                    //Elimina el Division
                    $user->delete();
                    return redirect('users')->withDelete('Registro Eliminado Exitoso!');
                } */

        }else{

            return redirect('/academiclevels')->withDelete('No Tiene Permiso a Editar');
        }

    }


}
