<?php

namespace App\Http\Controllers\Checks;

use App\Client;
use App\Branch;
use App\DetailVoucher;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserAccess\UserAccessController;
use App\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckMovementController extends Controller
{
 
    public $userAccess;
    public $modulo = 'Reportes';

    public function __construct(){

        $this->middleware('auth');
        $this->userAccess = new UserAccessController();
    }

   public function index()
   {
        if($this->userAccess->validate_user_access($this->modulo)){
            $user= auth()->user();

            $details = DetailVoucher::on(Auth::user()->database_name)->where('status','C')
                                                                    ->select('id_header_voucher',DB::raw('SUM(debe) As debe'),DB::raw('SUM(haber) As haber'))
                                                                    ->groupBy('id_header_voucher')->get();
          
            $details = $details->filter(function($detail)
            {
                if($detail->debe <> $detail->haber){
                    return $detail;
                    
                }
                
            });

            return view('admin.check_movements.index',compact('details'));
            
        }else{
            return redirect('/home')->withDanger('No tiene Acceso al modulo de '.$this->modulo);
        }
   }

  
}
