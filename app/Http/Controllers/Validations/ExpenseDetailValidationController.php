<?php

namespace App\Http\Controllers\Validations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseDetailValidationController extends Controller
{
    public function updateMovement($expense_detail){

        dd($this->checkMovement($expense_detail));
    }

    public function checkMovement($expense_detail){

        $amount = $expense_detail->price * $expense_detail->amount;

        $movement = DB::connection(Auth::user()->database_name)->table('detail_vouchers')
                    ->where('id_expense',$expense_detail->id_expense)
                    ->where('debe',$amount)
                    ->get();

        return $movement;
    }
}
