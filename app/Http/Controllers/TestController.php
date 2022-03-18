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

   public function index(Clientg $clientg)
   {
       if($this->userAccess->validate_user_access($this->modulo)){
            $user= auth()->user();

            $msg = 'msg: ';
            /* $urlToGet ='http://www.bcv.org.ve/bcv/contactos';
            $pageDocument = @file_get_contents($urlToGet);
            preg_match_all('|<div class="col-sm-6 col-xs-6 centrado"><strong> (.*?) </strong> </div>|s', $pageDocument, $cap);
    
            if ($cap[0] == array()){ // VALIDAR Concidencia
                $titulo = '0,00';
            } else {
                $titulo = $cap[1][4];
            }
    
            $bcv_con_formato = $titulo;
            $bcv = str_replace(',', '.', str_replace('.', '',$bcv_con_formato));
    
             $msg .= bcdiv($bcv, '1', 2);   
            -------------------------- */
           
            $crawler = $clientg->request('GET', 'https://www.aryasoftware.net');
           // $msg .= '';
            dd($crawler);
            
          


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
