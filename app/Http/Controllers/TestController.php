<?php
namespace App\Http\Controllers;
namespace App\Http\Controllers;

use Goutte\Clientg;

use App\Test;

use App\Http\Controllers\UserAccess\UserAccessController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
 
    public $userAccess;
    public $modulo = 'Cotizacion';

    public function __construct(){

        $this->middleware('auth');
        $this->userAccess = new UserAccessController();
    }

   public function index()
   {

       if($this->userAccess->validate_user_access($this->modulo)){
            $user= auth()->user();

            $msg = 'msg: ';
            
            $global = new GlobalController; 
            
            $bcv = $global->search_bcv();


            dd($bcv);

            return view('admin.test.index',compact('msg'));
        }else{
            return redirect('/home')->withDanger('No tiene Acceso al modulo de '.$this->modulo);
        } 




   }

   /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
  

}
