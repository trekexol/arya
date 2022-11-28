<?php

namespace App\Http\Controllers;

use App\Branch;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Permission\Models\Role;


class MyUserController extends Controller
{
    
    public $conection_logins = "logins"; 

    public function __construct()
    {
        $this->middleware('auth');


    }

  

    public function edit()
    {
        

        $user   = User::on(Auth::user()->database_name)->find(Auth::user()->id);
       
            $roles   = Role::on(Auth::user()->database_name)->get();
            
            $branches  = Branch::on(Auth::user()->database_name)->orderBY('description','asc')->get();
     
            return view('admin.users.myedit',compact('user','roles','branches'));
            
      

      
        
    }

   


    public function update(Request $request)
    {

        $rules = [
            'name'      =>'required|string|between:2,30|regex:/^[\pL\s\-]+$/u',
            'email'     =>'required|between:3,50|email|unique:users,email,'.Auth::user()->id,
            'password'  =>'required|between:4,30',
            'password_confirmation' =>'required|between:4,30|same:password',
        ];
        $messages = [
            'name.required' => 'Debe Agregar el Nombre.',
            'name.string' =>'Solo Permite Letras.',
            'name.between' => 'Minimo :min y Maximo :max Caracteres en el Nombre.',

            'email.required' => 'Debe Ingresar Correo Electronico.',
            'email.email' => 'Debe Ingresar Correo Electronico. ejemplo@ejemplo.com',
            'email.unique' => 'Correo Electronico ya existe',
            'email.between' => 'Minimo :min y Maximo :max el correo electronico',


            'password.required' => 'Debe Ingresar la contraseña.',
            'password.between' => 'debe ingresar Minimo :min y Maximo :max para la contraseña.',

            'password_confirmation.required' => 'Debe Ingresar la contraseña.',
            'password_confirmation.between' => 'debe ingresar Minimo :min y Maximo :max para la contraseña.',
            'password_confirmation.same' => 'La contraseña no coinciden',

        ];
        $this->validate($request, $rules, $messages);
 

        if(isset($request->password)){
            if($request->password != $request->password_confirmation){
                return redirect('/users')->withDanger('Las contraseñas no coinciden!');
            }
           
            $password = Hash::make($request->password);
        }else{
            $password = Auth::user()->password;
        }
        $user          = User::on(Auth::user()->database_name)->findOrFail(Auth::user()->id);
        $user->name         = request('name');
        $user->email        = request('email');
        $user->password     = $password;


        $user->save();
        

        //en logins

        $user          = User::on($this->conection_logins)->findOrFail(Auth::user()->id);
        $user->name         = request('name');
        $user->email        = request('email');
        $user->password     = $password;
     
    
        $user->save();
      

        return redirect('/users/edit')->withSuccess('Actualizado Con Exito!');

    

    }









   
    
}
