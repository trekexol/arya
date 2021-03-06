<?php

namespace App\Http\Controllers\Calculations;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountCalculationController extends Controller
{
    public function calculateBalance($account,$date_begin){

        $date_begin = Carbon::parse($date_begin)->format('Y-m-d');
       
        $cierre = DB::connection(Auth::user()->database_name)->table('account_historials')
        ->where('id_account',$account->id)
        ->whereRaw(
            "(DATE_FORMAT(account_historials.date_begin, '%Y-%m-%d') <= ? AND DATE_FORMAT(account_historials.date_end, '%Y-%m-%d') >= ?)", 
            [$date_begin, $date_begin])
        ->select('*')->first();
        
       
        if(isset($cierre)){
            return $cierre;
        }else{
            return $account;
        }
        
        
    }
}
