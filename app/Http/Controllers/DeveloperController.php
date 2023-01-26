<?php

namespace App\Http\Controllers;

use App\QuotationProduct;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DeveloperController extends Controller
{

    public function index()
    {
        $quotation_products = QuotationProduct::on(Auth::user()->database_name)
        ->join('products','products.id','quotation_products.id_inventory')
        ->where('id_quotation','3893')
->Orwhere('quotation_products.id_quotation','3894')
->Orwhere('quotation_products.id_quotation','3895')
->Orwhere('quotation_products.id_quotation','3896')
->Orwhere('quotation_products.id_quotation','3897')
->Orwhere('quotation_products.id_quotation','3898')
->Orwhere('quotation_products.id_quotation','3899')
->Orwhere('quotation_products.id_quotation','3900')
->Orwhere('quotation_products.id_quotation','3901')
->Orwhere('quotation_products.id_quotation','3902')
->Orwhere('quotation_products.id_quotation','3904')
->Orwhere('quotation_products.id_quotation','3905')
->Orwhere('quotation_products.id_quotation','3906')
->Orwhere('quotation_products.id_quotation','3907')
->Orwhere('quotation_products.id_quotation','3908')
->Orwhere('quotation_products.id_quotation','3909')
->Orwhere('quotation_products.id_quotation','3910')
->Orwhere('quotation_products.id_quotation','3911')
->Orwhere('quotation_products.id_quotation','3912')
->Orwhere('quotation_products.id_quotation','3913')
->Orwhere('quotation_products.id_quotation','3914')
->Orwhere('quotation_products.id_quotation','3915')
->Orwhere('quotation_products.id_quotation','3916')
->Orwhere('quotation_products.id_quotation','3917')
->Orwhere('quotation_products.id_quotation','3918')
->Orwhere('quotation_products.id_quotation','3919')
->get();

        foreach($quotation_products as  $quotation_pro){
            
            $suma = 0;
 
            $suma += ($quotation_pro->price_buy*$quotation_pro->rate) * $quotation_pro->amount;
            $quotation_pro->suma_total = $suma;

            $a_quotation[] = array($quotation_pro->id_quotation,$quotation_pro->suma_total,$quotation_pro->rate,$quotation_pro->amount,$quotation_pro->price_buy);
        }


        for ($q=0;$q<count($a_quotation);$q++) {
            for ($k=$q+1; $k<count($a_quotation);$k++) {
               if ($a_quotation[$q][0] == $a_quotation[$k][0]) {
                  $a_quotation[$q][1] = $a_quotation[$q][1]+$a_quotation[$k][1];
                  $a_quotation[$k][0]=0; 
                }
    
            }

        }


        for ($q=0;$q<count($a_quotation);$q++) {
        
            if ($a_quotation[$q][0] != 0) {
            
                $affected_debe = DB::connection(Auth::user()->database_name)
                ->table('detail_vouchers')
                ->where('id_invoice', '=', $a_quotation[$q][0])
                ->where('id_account','302')
                ->update(array('debe' => $a_quotation[$q][1]));

                $affected_haber = DB::connection(Auth::user()->database_name)
                ->table('detail_vouchers')
                ->where('id_invoice', '=', $a_quotation[$q][0])
                ->where('id_account','32')
                ->update(array('haber' => $a_quotation[$q][1]));
                    
            } 

        }

        dd('listo');

        return view('admin.developer.index',compact('quotation_products'));
      
    }

    

}
