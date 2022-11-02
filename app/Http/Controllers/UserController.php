<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Modulo;
use App\Sistemas;
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
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Usuarios');

    }

  
    public function index(Request $request)
    {

        $agregarmiddleware = $request->get('agregarmiddleware');
        $actualizarmiddleware = $request->get('actualizarmiddleware');
        $eliminarmiddleware = $request->get('eliminarmiddleware');
        
        $user       =   auth()->user();
        $users_role =   $user->role_id;
       
        if($users_role == '1'){
            $users      =   User::on(Auth::user()->database_name)->orderBy('id', 'asc')->get();
        }else{
            $users      =   User::on(Auth::user()->database_name)->whereNotIn('role_id',['1'])->orderBy('id', 'asc')->get();
        }
       
         
        return view('admin.users.index',compact('users','agregarmiddleware','actualizarmiddleware','eliminarmiddleware'));
      
    }

    public function create(Request $request)
    {

        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == '1'){
            
            $branches  = Branch::on(Auth::user()->database_name)->orderBY('description','asc')->get();
            return view('admin.users.create',compact('branches'));

        }else{
              return redirect('/users')->withSuccess('No Tiene Acceso a Registrar Usuarios');
        }



     
    }

    public function createAssignModules(Request $request, $id_user)
    {

        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == '1'){

            $user   = User::on(Auth::user()->database_name)->find($id_user);

            if(isset($user->role_id) && $user->role_id == '1'){
                return redirect('/users')->withDelete('Este Usuario es de tipo Administrador, si quiere asignarle modulos debe editarlo a usuario!');
           
           
            }elseif(isset($user) && $user->count() > '0'){
                
              
            $user_access = UserAccess::on($this->conection_logins)->where('id_user',$user->id)
                ->join('modulos', 'modulos.id', '=', 'user_access.id_modulo')
                ->join('sistemas', 'sistemas.id_sistema', '=', 'modulos.id_sistema')
                ->select('user_access.id','user_access.id_user','sistemas.id_sistema','sistemas.sistema', 'modulos.name','user_access.agregar','user_access.actualizar','user_access.eliminar')
                ->get();
    
           return view('admin.users.selectmodulos',compact('user','user_access'));
    
            }else{
    
                return redirect('/users')->withDelete('Usuario No Existe!');
    
            }
            
        }else{

            return redirect('/users')->withDelete('No Tiene Permiso Para Asignar Modulo');

        }


       
      

       
    }

    public function store(Request $request)
    {
        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == '1'){
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
        $user_login->id_branch   = request('id_branch');
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
        $user->id_branch   = request('id_branch');
        $user->status      = request('status');
        $user->id_user_register    = $user_conected->id;

        $user->save();

       
        if($user->role_id != '1'){
       
            return redirect('/users/permisos/'.$user->id.'/'.$user->name.'')->withSuccess('Registro de Usuario Exitoso!');
           
        }else{
            return redirect('/users')->withSuccess('Registro Exitoso!');
        }

    }else{

        return redirect('/users')->withDelete('No Tiene Permiso Para Asignar Modulo');

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

    public function edit(Request $request, $id)
    {
        

        $user   = User::on(Auth::user()->database_name)->find($id);
       
        if(Auth::user()->id == $id){
            $roles   = Role::on(Auth::user()->database_name)->get();
            
            $branches  = Branch::on(Auth::user()->database_name)->orderBY('description','asc')->get();
     
            return view('admin.users.edit',compact('user','roles','branches'));
            
        }
        elseif(Auth::user()->role_id  == '1'){
          
            $roles   = Role::on(Auth::user()->database_name)->get();
            
        
            
            $branches  = Branch::on(Auth::user()->database_name)->orderBY('description','asc')->get();
     
            return view('admin.users.edit',compact('user','roles','branches'));

        }elseif($request->get('actualizarmiddleware') == '1'){

            
            if($user->role_id == '1'){
                return redirect('/users')->withDelete('Solo puede ser editado por un Administrador');

            }else{

                $roles   = Role::on(Auth::user()->database_name)->get();
            
                $branches  = Branch::on(Auth::user()->database_name)->orderBY('description','asc')->get();
         
                return view('admin.users.edit',compact('user','roles','branches'));
            }
            
           

        }

        else{

   
            return redirect('/users')->withDanger('No Tiene Permiso para Editar Usuarios');
       
           
        }

        
    }

   


    public function update(Request $request,$id)
    {
        if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == '1'){
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
        $user->id_branch        = request('id_branch');

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

        $user->id_branch        = request('id_branch');

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

     } else{

   
            return redirect('/users')->withDanger('No Tiene Permiso para Editar Usuarios');
       
           
        }

    }


    public function destroy(Request $request)
    {

        if(Auth::user()->role_id  == '1'){

            $user = User::on(Auth::user()->database_name)->find($request->id_user_modal);
            if(isset($user)){
                $user->delete();
            }
            
            $user = User::on($this->conection_logins)->find($request->id_user_modal);
            if(isset($user)){
                $user->delete();
            }
            return redirect('users')->withDelete('Registro Eliminado Exitoso!');

        }elseif($request->get('eliminarmiddleware') == '1'){

            $user = User::on(Auth::user()->database_name)->find($request->id_user_modal);

            if(isset($user) && $user->role_id == '1'){

             return redirect('/users')->withDanger('Solo Administradores puede Eliminar a un Administrador!');
            }else{
                $user->delete();

                $users = User::on($this->conection_logins)->find($request->id_user_modal);
            if(isset($users)){
                $users->delete();
            }

            return redirect('users')->withDelete('Registro Eliminado Exitoso!');

            }
          
        
        
        }else{

            return redirect('/users')->withDanger('No tiene permiso para Eliminar Usuarios!');
        }

   
    }




    /****VISTA PARA ASIGNAR PERMISO */

    public function indexpermisos(Request $request,$id_user,$name_user)
    {
      

        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == '1'){
            $user = User::on($this->conection_logins)
                            ->where('id',$id_user)
                            ->where('name',$name_user)
                            ->WhereNotIn('role_id',['1'])->first();

            if($user){

                $sistemas = Sistemas::on($this->conection_logins)
                            ->Where('id_companies','like','%'.$user->id_company.'A%')
                            ->orderby('nro_orden','ASC')->get();
          
                return view('admin.users.indexpermisos',compact('id_user','name_user','sistemas'));
              
    
            }else{
    
                return redirect('/users')->withDelete('Usuario No Existe o es Administrador!');
    
            }

        }else{
    
            return redirect('/users')->withDelete('No tienes Permiso Para Asignar Modulos!');

        }

        


      
    }

    
}
