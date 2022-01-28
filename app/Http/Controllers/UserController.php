<?php

namespace App\Http\Controllers;

use App\Modulo;
use Illuminate\Http\Request;

use App\User;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;



use App\Permission\Models\Role;
use App\UserAccess;

class UserController extends Controller
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
        if($users_role == '1'){
           $users      =   User::on(Auth::user()->database_name)->orderBy('id', 'asc')->get();
        }elseif($users_role == '2'){
            return view('admin.index');
        }

    
        return view('admin.users.index',compact('users'));
      
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function createAssignModules($id_user)
    {
        $user   = User::on(Auth::user()->database_name)->find($id_user);
        if($user->role_id == '1'){
            return redirect('/users')->withDelete('Este Usuario es de tipo Administrador, si quiere asignarle modulos debe editarlo a usuario!');
        }
        $modulos   = Modulo::on($this->conection_logins)->get();

        $user_access   = UserAccess::on($this->conection_logins)->where('id_user',$user->id)->get();


        return view('admin.users.selectmodulos',compact('modulos','user','user_access'));
    }

    public function store(Request $request)
    {
        
        $data = request()->validate([
            'email'         =>'required|max:255|unique:users,email',
            'name'         =>'required|max:160',
            'password'         =>'required|max:20|confirmed|min:6',
            'password_confirmation' => 'required_with:password|same:password|min:6'
           
        ]);

        if($request->password != $request->password_confirmation){
            return redirect('/users')->withDanger('Las contraseñas no coinciden!');
        }

        $user_conected  =   auth()->user();

        $user_login = new User();
        $user_login->setConnection($this->conection_logins);
        $user_login->name        = request('name');
        $user_login->email       = request('email');
        $user_login->password    = Hash::make(request('password'));
        $user_login->role_id     = request('roles_id');
        $user_login->status      = request('status');
        
        $user_login->id_user_register    = $user_conected->id;
        $user_login->id_company      = $user_conected->id_company;
        $user_login->database_name   = $user_conected->database_name;

        $user_login->save();


        $user = new User();
        $user->setConnection(Auth::user()->database_name);
        $user->id          = $user_login->id;
        $user->name        = request('name');
        $user->email       = request('email');
        $user->password    = Hash::make(request('password'));
        $user->role_id     = request('roles_id');
        $user->status      = request('status');
        $user->id_user_register    = $user_conected->id;

        $user->save();

       
        if($user->role_id != '1'){
            
            return redirect('/users/createassignmodules/'.$user->id.'')->withSuccess('Registro de Usuario Exitoso!');
           
        }else{
            return redirect('/users')->withSuccess('Registro Exitoso!');
        }
        
    }

    public function assignModules(Request $request)
    {
       
        //convierte a array
        $modulos_news = explode(",", $request->modulos_news);

        $modulos_olds = explode(",", $request->modulos_olds);

        $diferencias = array_diff($modulos_news,$modulos_olds);
        
        if(empty($diferencias) || (isset($diferencias[0]) && $diferencias[0] == "")){
            $diferencias = array_diff($modulos_olds,$modulos_news);
        }
        
        if(count($diferencias) > 0){
            foreach($diferencias as $diferencia){
                $combo_exist = UserAccess::on($this->conection_logins)->where('id_user',$request->id_user)->where('modulo',$diferencia)->first();
                
                if(isset($combo_exist)){
                    UserAccess::on($this->conection_logins)->where('id_user',$request->id_user)->where('modulo',$diferencia)->delete();
                }else{
                    $var = new UserAccess();
                    $var->setConnection($this->conection_logins);
                    $var->id_user = $request->id_user;
                    $var->modulo = $diferencia;

                    $var->save();
                    
                }
            }
        }

        return redirect('/users')->withSuccess('Registro de Asignaciones Exitosa!');
    }

    public function edit($id)
    {
        $user   = User::on(Auth::user()->database_name)->find($id);
        $roles   = Role::on(Auth::user()->database_name)->get();

        $user_conected  =   auth()->user();

        if($user_conected->role_id != '1'){
            return redirect('/users')->withDanger('Debes ser Administrador!');
        }else{
            return view('admin.users.edit',compact('user','roles'));
        }

        
    }

   


    public function update(Request $request,$id)
    {
       
        $users =  User::on(Auth::user()->database_name)->find($id);
        $user_rol = $users->role_id;
        $user_status = $users->status;
      
        

        $request->validate([
            'name'      =>'required|string|max:255',
            'email'     =>'required|max:120|unique:users,email,'.$users->id,
            'Roles'     =>'max:2',
            'password'  =>'max:255|confirmed',
            'status'     =>'max:2',
        ]);


        if(isset($request->password)){
            if($request->password != $request->password_confirmation){
                return redirect('/users')->withDanger('Las contraseñas no coinciden!');
            }
           
            $password = Hash::make($request->password);
        }else{
            $password = $users->password;
        }
        $user          = User::on(Auth::user()->database_name)->findOrFail($id);
        $user->name         = request('name');
        $user->email        = request('email');
        $user->password     = $password;

        if(request('Roles') == null){
            $user->role_id = $user_rol;
        }else{
            $user->role_id = request('Roles');
        }

        if(request('status') == null){
            $user->status = $user_status;
        }else{
            $user->status = request('status');
        }
    
        $user->save();
        

        //en logins

        $users =  User::on($this->conection_logins)->find($id);

        $user_rol = $users->role_id;
        $user_status = $users->status;

        $user          = User::on($this->conection_logins)->findOrFail($id);
        $user->name         = request('name');
        $user->email        = request('email');
        $user->password     = $password;

        if(request('Roles') == null){
            $user->role_id = $user_rol;
        }else{
            $user->role_id = request('Roles');
        }

        if(request('status') == null){
            $user->status = $user_status;
        }else{
            $user->status = request('status');
        }
    
        $user->save();
      

        return redirect('/users')->withSuccess('Registro Guardado Exitoso!');

    }


    public function destroy(Request $request)
    {
        $user = User::on(Auth::user()->database_name)->find($request->id_user_modal);
        if(isset($user)){
            $user->delete();
        }
        
        $user = User::on($this->conection_logins)->find($request->id_user_modal);
        if(isset($user)){
            $user->delete();
        }
        return redirect('users')->withDelete('Registro Eliminado Exitoso!');
    }


   
    
}
