<?php

namespace App\Http\Controllers\UserAccess;

use App\Http\Controllers\Controller;
use App\UserAccess;
use Illuminate\Http\Request;

class UserAccessController extends Controller
{
    public function validate_user_access($modulo_verification){

        $user       =   auth()->user();
            
        /*if(isset($user->role_id) && ($user->role_id != '1')){
            $validate_user_access = UserAccess::on('logins')
            ->where('modulo',$modulo_verification)
            ->where('id_user',$user->id)
            ->first();

            if(isset($validate_user_access)){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }

        return false;*/
        return true;

    }

}
