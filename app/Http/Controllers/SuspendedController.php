<?php

namespace App\Http\Controllers;


use App\Company;
use App\Http\Controllers\UserAccess\UserAccessController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SuspendedController extends Controller
{

   public function index()
   {      

    return view('suspended'); 

   }


}
