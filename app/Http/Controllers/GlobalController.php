<?php

namespace App\Http\Controllers;

use App\Anticipo;
use Goutte\Clientg;
use App\ComboProduct;
use App\Company;
use App\Product;
use App\ExpensePayment;
use App\ExpensesDetail;
use App\HeaderVoucher;
use App\Account;
use App\DetailVoucher;
use App\Inventory;
use App\QuotationPayment;
use App\QuotationProduct;
use Carbon\Carbon;
use App\UserAccess;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\TasaBcv;

class GlobalController extends Controller
{


    public function procesar_anticipos($quotation,$total_pay)
    {

        if($total_pay >= 0){
            $anticipos_old = DB::connection(Auth::user()->database_name)->table('anticipos')
                                ->where('id_client', '=', $quotation->id_client)
                                ->where(function ($query) use ($quotation){
                                    $query->where('id_quotation',null)
                                        ->orWhere('id_quotation',$quotation->id);
                                })
                                ->where('status', '=', '1')->get();

            foreach($anticipos_old as $anticipo){
                DB::connection(Auth::user()->database_name)->table('anticipo_quotations')->insert(['id_quotation' => $quotation->id,'id_anticipo' => $anticipo->id]);
            }


            /*Verificamos si el cliente tiene anticipos activos */
            DB::connection(Auth::user()->database_name)->table('anticipos')
                    ->where('id_client', '=', $quotation->id_client)
                    ->where(function ($query) use ($quotation){
                        $query->where('id_quotation',null)
                            ->orWhere('id_quotation',$quotation->id);
                    })
                    ->where('status', '=', '1')
                    ->update(['status' => 'C']);

            //los que quedaron en espera, pasan a estar activos
            DB::connection(Auth::user()->database_name)->table('anticipos')->where('id_client', '=', $quotation->id_client)
            ->where(function ($query) use ($quotation){
                $query->where('id_quotation',null)
                    ->orWhere('id_quotation',$quotation->id);
            })
            ->where('status', '=', 'M')
            ->update(['status' => '1']);
        }
    }

    public function procesar_anticipos_expense($expense,$total_pay)
    {

        if($total_pay >= 0){

            $anticipos_old = DB::connection(Auth::user()->database_name)->table('anticipos')
                                ->where('id_provider', '=', $expense->id_provider)
                                ->where(function ($query) use ($expense){
                                    $query->where('id_expense',null)
                                        ->orWhere('id_expense',$expense->id);
                                })
                                ->where('status', '=', '1')->get();

            foreach($anticipos_old as $anticipo){
                DB::connection(Auth::user()->database_name)->table('anticipo_expenses')->insert(['id_expense' => $expense->id,'id_anticipo' => $anticipo->id]);
            }


            /*Verificamos si el proveedor tiene anticipos activos */
            DB::connection(Auth::user()->database_name)->table('anticipos')
                    ->where('id_provider', '=', $expense->id_provider)
                    ->where(function ($query) use ($expense){
                        $query->where('id_expense',null)
                            ->orWhere('id_expense',$expense->id);
                    })
                    ->where('status', '=', '1')
                    ->update(['status' => 'C']);

            //los que quedaron en espera, pasan a estar activos
            DB::connection(Auth::user()->database_name)->table('anticipos')->where('id_provider', '=', $expense->id_provider)
            ->where(function ($query) use ($expense){
                $query->where('id_expense',null)
                    ->orWhere('id_expense',$expense->id);
            })
            ->where('status', '=', 'M')
            ->update(['status' => '1']);
        }
    }

    public function check_anticipo($quotation,$total_pay)
    {

            $anticipos = DB::connection(Auth::user()->database_name)->table('anticipos')->where('id_client', '=', $quotation->id_client)
                                                                                    ->where(function ($query) use ($quotation){
                                                                                        $query->where('id_quotation',null)
                                                                                            ->orWhere('id_quotation',$quotation->id);
                                                                                    })
                                                                                    ->where('status', '=', '1')->get();

            foreach($anticipos as $anticipo){

                //si el anticipo esta en dolares, multiplico los dolares por la tasa de la cotizacion, para sacar el monto real en bolivares
                if($anticipo->coin != "bolivares"){
                    $anticipo->amount = ($anticipo->amount / $anticipo->rate) * $quotation->bcv;
                }

                if($total_pay >= $anticipo->amount){
                    DB::connection(Auth::user()->database_name)->table('anticipos')
                                                                ->where('id', $anticipo->id)
                                                                ->update(['status' => 'C']);

                    DB::connection(Auth::user()->database_name)->table('anticipo_quotations')->insert(['id_quotation' => $quotation->id,'id_anticipo' => $anticipo->id]);

                    $total_pay -= $anticipo->amount;
                }else{

                    DB::connection(Auth::user()->database_name)->table('anticipos')
                                                                ->where('id', $anticipo->id)
                                                                ->update(['status' => 'C']);

                    DB::connection(Auth::user()->database_name)->table('anticipo_quotations')->insert(['id_quotation' => $quotation->id,'id_anticipo' => $anticipo->id]);


                    $amount_anticipo_new = $anticipo->amount - $total_pay;

                    $var = new Anticipo();
                    $var->setConnection(Auth::user()->database_name);

                    $var->id_anticipo_restante = $anticipo->id;
                    $var->date = $quotation->date_billing;
                    $var->id_client = $quotation->id_client;
                    $user       =   auth()->user();
                    $var->id_user = $user->id;
                    $var->id_account = $anticipo->id_account;
                    $var->coin = $anticipo->coin;
                    $var->amount = $amount_anticipo_new;
                    $var->rate = $quotation->bcv;
                    $var->reference = $anticipo->reference;
                    $var->status = 1;
                    $var->save();
                    break;
                }
            }


    }

    public function checkAnticipoExpense($expense,$total_pay)
    {

            $anticipos = DB::connection(Auth::user()->database_name)->table('anticipos')->where('id_provider', '=', $expense->id_provider)
                                                                                    ->where(function ($query) use ($expense){
                                                                                        $query->where('id_expense',null)
                                                                                            ->orWhere('id_expense',$expense->id);
                                                                                    })
                                                                                    ->where('status', '=', '1')->get();

            foreach($anticipos as $anticipo){

                //si el anticipo esta en dolares, multiplico los dolares por la tasa de la cotizacion, para sacar el monto real en bolivares
                if($anticipo->coin != "bolivares"){
                    $anticipo->amount = ($anticipo->amount / $anticipo->rate) * $expense->rate;
                }

                if($total_pay >= $anticipo->amount){
                    DB::connection(Auth::user()->database_name)->table('anticipos')
                                                                ->where('id', $anticipo->id)
                                                                ->update(['status' => 'C']);

                    DB::connection(Auth::user()->database_name)->table('anticipo_expenses')->insert(['id_expense' => $expense->id,'id_anticipo' => $anticipo->id]);

                    $total_pay -= $anticipo->amount;
                }else{

                    DB::connection(Auth::user()->database_name)->table('anticipos')
                                                                ->where('id', $anticipo->id)
                                                                ->update(['status' => 'C']);

                    DB::connection(Auth::user()->database_name)->table('anticipo_expenses')->insert(['id_expense' => $expense->id,'id_anticipo' => $anticipo->id]);


                    $amount_anticipo_new = $anticipo->amount - $total_pay;

                    $var = new Anticipo();
                    $var->setConnection(Auth::user()->database_name);

                    $var->id_anticipo_restante = $anticipo->id;
                    $var->date = $expense->date;
                    $var->id_provider = $expense->id_provider;
                    $user       =   auth()->user();
                    $var->id_user = $user->id;
                    $var->id_account = $anticipo->id_account;
                    $var->coin = $anticipo->coin;
                    $var->amount = $amount_anticipo_new;
                    $var->rate = $anticipo->rate;
                    $var->reference = $anticipo->reference;
                    $var->status = 1;
                    $var->save();
                    break;
                }
            }
    }

    public function associate_anticipos_quotation($quotation){

        $anticipos = DB::connection(Auth::user()->database_name)->table('anticipos')->where('id_client', '=', $quotation->id_client)
        ->where(function ($query) use ($quotation){
            $query->where('id_quotation',null)
                ->orWhere('id_quotation',$quotation->id);
        })
        ->where('status', '=', '1')->get();

        foreach($anticipos as $anticipo){
            DB::connection(Auth::user()->database_name)->table('anticipo_quotations')->insert(['id_quotation' => $quotation->id,'id_anticipo' => $anticipo->id]);
        }

    }

    public function associate_anticipos_expense($expense){

        $anticipos = DB::connection(Auth::user()->database_name)->table('anticipos')->where('id_provider', '=', $expense->id_provider)
        ->where(function ($query) use ($expense){
            $query->where('id_expense',null)
                ->orWhere('id_expense',$expense->id);
        })
        ->where('status', '=', '1')->get();

        foreach($anticipos as $anticipo){
            DB::connection(Auth::user()->database_name)->table('anticipo_expenses')->insert(['id_expense' => $expense->id,'id_anticipo' => $anticipo->id]);
        }

    }

    public function check_anticipo_multipayment($quotation,$quotations_id,$total_pay)
    {

            $anticipos = DB::connection(Auth::user()->database_name)->table('anticipos')->where('id_client', '=', $quotation->id_client)
                                                                                    ->where(function ($query) use ($quotations_id){
                                                                                        $query->where('id_quotation',null)
                                                                                            ->orWhereIn('id_quotation', $quotations_id);
                                                                                    })
                                                                                    ->where('status', '=', '1')->get();


            foreach($anticipos as $anticipo){

                //si el anticipo esta en dolares, multiplico los dolares por la tasa de la cotizacion, para sacar el monto real en bolivares
                if($anticipo->coin != "bolivares"){
                    $anticipo->amount = ($anticipo->amount / $anticipo->rate) * $quotation->bcv;
                }

                if($total_pay >= $anticipo->amount){
                    DB::connection(Auth::user()->database_name)->table('anticipos')
                                                                ->where('id', $anticipo->id)
                                                                ->update(['status' => 'C']);

                    DB::connection(Auth::user()->database_name)->table('anticipo_quotations')->insert(['id_quotation' => $quotation->id,'id_anticipo' => $anticipo->id]);

                    $total_pay -= $anticipo->amount;
                }else{

                    DB::connection(Auth::user()->database_name)->table('anticipos')
                                                                ->where('id', $anticipo->id)
                                                                ->update(['status' => 'C']);

                    DB::connection(Auth::user()->database_name)->table('anticipo_quotations')->insert(['id_quotation' => $quotation->id,'id_anticipo' => $anticipo->id]);


                    $amount_anticipo_new = $anticipo->amount - $total_pay;

                    $var = new Anticipo();
                    $var->setConnection(Auth::user()->database_name);

                    $var->date = $quotation->date_billing;
                    $var->id_client = $quotation->id_client;
                    $user       =   auth()->user();
                    $var->id_user = $user->id;
                    $var->id_account = $anticipo->id_account;
                    $var->coin = $anticipo->coin;
                    $var->amount = $amount_anticipo_new;
                    $var->rate = $quotation->bcv;
                    $var->reference = $anticipo->reference;
                    $var->status = 1;
                    $var->save();
                    break;
                }
            }


    }


    public function discount_inventory($id_quotation,$sucursal = 1)
    {


        /*Luego, descuenta del Inventario*/
        $inventories_quotations = DB::connection(Auth::user()->database_name)->table('products')
        ->join('quotation_products', 'products.id', '=', 'quotation_products.id_inventory')
        ->where('quotation_products.id_quotation',$id_quotation)
        ->where('quotation_products.status','1')
        ->select('products.*','quotation_products.id as id_quotation','quotation_products.discount as discount',
        'quotation_products.amount as amount_quotation')
        ->get();

        foreach($inventories_quotations as $inventories_quotation){

            $quotation_product = QuotationProduct::on(Auth::user()->database_name)->findOrFail($inventories_quotation->id_quotation);

            if(isset($quotation_product))
            {
                $inventory = Product::on(Auth::user()->database_name)->findOrFail($quotation_product->id_inventory);
                if(isset($inventory)){
                    if(($inventories_quotation->type == 'MERCANCIA') || (($inventories_quotation->type == 'COMBO')) && ($inventory-> amount > 0))
                    {
                        //REVISO QUE SEA MAYOR EL MONTO DEL INVENTARIO Y LUEGO DESCUENTO
                        $global = new GlobalController;
                        $inventory->amount = $global->consul_prod_invt($inventory->id);

                        if($inventory->amount >= $quotation_product->amount){

                        }else{
                            return 'El Inventario de Codigo: '.$inventory->code.' no tiene Cantidad suficiente!';
                        }
                    }else if(($inventories_quotation->type == 'COMBO') && ($inventory-> amount == 0)){
                        $global = new GlobalController;
                        $global->discountCombo($inventory,$quotation_product->amount);
                    }

            }else{
                return 'El Inventario no existe!';
            }
                //CAMBIAMOS EL ESTADO PARA SABER QUE ESE PRODUCTO YA SE COBRO Y SE RESTO DEL INVENTARIO
                $quotation_product->status = 'C';
                $quotation_product->save();
            }else{
            return 'El Inventario de la cotizacion no existe!';
            }

        }

        return "exito";

    }

    public function check_product($id_quotation,$id_inventory,$amount_new){

        $inventories_quotations = DB::connection(Auth::user()->database_name)
        ->table('products')
        ->join('inventories', 'products.id', '=', 'inventories.product_id')
        ->where('inventories.id',$id_inventory)
        ->select('products.*','inventories.amount as amount_inventory')
        ->first();

        if(isset($inventories_quotations) && ($inventories_quotations->type == "MERCANCIA"))
        {
            return $this->check_amount($id_quotation,$inventories_quotations,$amount_new);

        }else if(isset($inventories_quotations) && ($inventories_quotations->type == "COMBO") && ($inventories_quotations->amount_inventory == 0))
        {
            return $this->check_combo_by_zero($id_quotation,$inventories_quotations,$amount_new);

        }else if(isset($inventories_quotations) && ($inventories_quotations->type == "COMBO") ){

            return $this->check_amount($id_quotation,$inventories_quotations,$amount_new);

        }

        return "exito";

    }
    public function check_amount($id_quotation,$inventories_quotations,$amount_new)
    {

        //si es un servicio no se chequea que posea inventario, ni tampoco el combo, el combo se revisa sus componentes si tienen inventario
       /* if(isset($inventories_quotations) && ((($inventories_quotations->type == "MERCANCIA")) || (($inventories_quotations->type == "COMBO")))){
            $inventory = Inventory::on(Auth::user()->database_name)->find($inventories_quotations->id);

            $sum_amount = DB::connection(Auth::user()->database_name)->table('quotation_products')
                            ->where('id_quotation',$id_quotation)
                            ->where('id_inventory',$inventories_quotations->id)
                            ->where("status",'1')
                            ->sum('amount');

            $comboController = new ComboController();

            $suma_en_combos = 0;

            $suma_en_combos = $comboController->check_exist_combo_in_quotation($id_quotation,$inventory->product_id);


            $total_in_quotation = $sum_amount + $amount_new;


            if ($inventory->amount >= ($total_in_quotation + $suma_en_combos)){
                return "exito";
            }else{
                return "El producto ".$inventories_quotations->description." no tiene inventario suficiente";
            }

        }else{*/
            return "exito";
     //   }

    }

    public function check_combo_by_zero($id_quotation,$inventories_quotations,$amount_new){


        $relation_combo = ComboProduct::on(Auth::user()->database_name)->where("id_combo",$inventories_quotations->id)->get();


        if(isset($relation_combo) && (count($relation_combo) > 0)){

            foreach($relation_combo as $relation){
                $inventories_quotations = DB::connection(Auth::user()->database_name)
                                                                    ->table('products')
                                                                    ->where('id',$relation->id_product)
                                                                    ->select('products.*')
                                                                    ->first();

                $value_return = $this->check_amount($id_quotation,$inventories_quotations,$amount_new * $relation->amount_per_product);

                if($value_return != "exito"){
                    return "El producto ".$inventories_quotations->description." del combo no tiene inventario suficiente";
                }
            }
            return "exito";
        }else{

            return "El combo no tiene Productos Asociados";
        }

    }


    public function check_all_products_after_facturar($id_quotation){

        $all_products_quotation = DB::connection(Auth::user()->database_name)->table('inventories')
                                    ->join('quotation_products', 'quotation_products.id_inventory','=','inventories.id')
                                    ->join('products', 'products.id','=','inventories.product_id')
                                    ->where('quotation_products.id_quotation',$id_quotation)
                                    ->where('quotation_products.status','1')
                                    ->where(function ($query){
                                        $query->where('products.type','MERCANCIA');
                                        $query->orWhere('products.type','COMBO');
                                    })
                                    ->select('inventories.code as code','inventories.id as id_inventory','quotation_products.id_quotation as id_quotation','quotation_products.discount as discount',
                                    'quotation_products.amount as amount_quotation')
                                    ->get();


        foreach($all_products_quotation as $product){
            $value_return = $this->check_product($id_quotation,$product->id_inventory,0);

            if($value_return != "exito"){
                return $value_return;
            }
        }

        return "exito";

    }


    public function add_payment($quotation,$id_account,$payment_type,$amount,$bcv){
        $var = new QuotationPayment();
        $var->setConnection(Auth::user()->database_name);

        $var->id_quotation = $quotation->id;
        $var->id_account = $id_account;

        $var->payment_type = $payment_type;
        $var->amount = $amount;


        $var->rate = $bcv;

        $var->status =  1;
        $var->save();

        return $var->id;
    }

    public function add_payment_expense($expense,$id_account,$payment_type,$amount,$bcv){
        $var = new ExpensePayment();
        $var->setConnection(Auth::user()->database_name);

        $var->id_expense = $expense->id;
        $var->id_account = $id_account;

        $var->payment_type = $payment_type;
        $var->amount = $amount;

        $var->status =  1;
        $var->save();

        return $var->id;
    }

    public function aumentCombo($inventory,$amount_discount)
    {
        $product = ComboProduct::on(Auth::user()->database_name)
                    ->join('products','products.id','combo_products.id_product')
                    ->join('inventories','inventories.product_id','products.id')
                    ->where('combo_products.id_combo',$inventory->product_id)
                    ->update(['inventories.amount' => DB::raw('inventories.amount - (combo_products.amount_per_product *'.$amount_discount.')')]);


    }

    public function discountCombo($inventory,$amount_discount)
    {
        $product = ComboProduct::on(Auth::user()->database_name)
                    ->join('products','products.id','combo_products.id_product')
                    ->join('inventories','inventories.product_id','products.id')
                    ->where('combo_products.id_combo',$inventory->product_id)
                    ->update(['inventories.amount' => DB::raw('inventories.amount - (combo_products.amount_per_product *'.$amount_discount.')')]);


    }

    function asignar_payment_type($type){

        if($type == 1){
            return "Cheque";
        }
        if($type == 2){
            return "Contado";
        }
        if($type == 3){
            return "Contra Anticipo";
        }
        if($type == 4){
            return "Crédito";
        }
        if($type == 5){
            return "Depósito Bancario";
        }
        if($type == 6){
            return "Efectivo";
        }
        if($type == 7){
            return "Indeterminado";
        }
        if($type == 8){
            return "Tarjeta Coorporativa";
        }
        if($type == 9){
            return "Tarjeta de Crédito";
        }
        if($type == 10){
            return "Tarjeta de Débito";
        }
        if($type == 11){
            return "Transferencia";
        }
    }

    public function deleteAllProducts($id_quotation)
    {


       $quotation_products = QuotationProduct::on(Auth::user()->database_name)->where('id_quotation',$id_quotation)->first();

        if(isset($quotation_products)){

                    QuotationProduct::on(Auth::user()->database_name)
                        ->where('id_quotation',$id_quotation)
                        ->update(['status' => 'X']);
        }


    }

    public function deleteAllProductsExpense($id_expense)
    {

        $expense_products = ExpensesDetail::on(Auth::user()->database_name)->where('id_expense',$id_expense)->get();


        if(isset($expense_products)){
            foreach($expense_products as $expense_product){

                    ExpensesDetail::on(Auth::user()->database_name)
                        ->where('expenses_details.id',$expense_product->id)
                        ->where('expenses_details.id_expense',$id_expense)
                        ->update(['expenses_details.status' => 'X']);

            }
        }
    }





    public function search_bcv()
    {

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');


        $tasahoy  = TasaBcv::on("logins")->where('fecha_valor',$datenow)->first();

        if($tasahoy == null){ //procedo a guardar la tasa del dia.

        //$url = "https://s3.amazonaws.com/dolartoday/data.json";
        $url = "https://www.aryasoftware.net/apidolarbcv/";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec( $ch );
        $error = curl_error($ch);
        curl_close( $ch );


        $datos = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF] /', '', $data), true);


        if($datos['fechadehoy'] == $datos['fechaoficial']){

            $tasahoy  = TasaBcv::on("logins")->where('fecha_valor',$datenow)->first();


                $dolaroficial = str_replace(array(","),".",$datos['dolaroficial']);

                $var = new TasaBcv();
                $var->setConnection("logins");
                $var->coin = 'dolares';
                $var->valor = $dolaroficial;
                $var->fecha_valor = $datenow;
                $var->save();


                $companies  = Company::on("logins")
                ->update(["rate_bcv" => $dolaroficial, "date_consult_bcv" => $datenow]);



            }else{
                $company = Company::on("logins")->where('login',Auth::user()->database_name)->first();
                $bcv = $company->rate_bcv;

                $companies  = Company::on("logins")
                ->update(["rate_bcv" => $bcv, "date_consult_bcv" => $datenow]);

                return bcdiv($bcv, '1', 2);
            }


    }//fin primer nulll
    else{
        $company = Company::on("logins")->where('login',Auth::user()->database_name)->first();
        $bcv = $company->rate_bcv;
        return bcdiv($bcv, '1', 2);
    }


    }


    public function data_last_month_day() {
        $month = date('m');
        $year = date('Y');
        $day = date("d", mktime(0,0,0, $month+1, 0, $year));

        return date('Y-m-d', mktime(0,0,0, $month, $day, $year));
    }

    /** Actual month first day **/
    public function data_first_month_day() {
        $month = date('m');
        $year = date('Y');
        $dia = date('1');
        return date('Y-m-').'01';
    }


    function consul_cant_combo($id_product,$sucursal = 1){

        $combo_product = ComboProduct::on(Auth::user()->database_name)
        ->where('combo_products.id_combo',$id_product)
        ->get();

        $cantidad_combos = 0;

        if(!empty($combo_product)) {
            foreach ($combo_product as $int_product) {

                    if ($sucursal == 1) {
                        $inventories_quotations = DB::connection(Auth::user()->database_name)
                        ->table('inventory_histories')
                        ->where('id_product','=',$int_product->id_product)
                        ->where('id_branch','=',$sucursal)
                        ->orwhere('id_branch',null)
                        ->select('amount_real')
                        ->get()->last();
                    } else {
                        $inventories_quotations = DB::connection(Auth::user()->database_name)
                        ->table('inventory_histories')
                        ->where('id_product','=',$int_product->id_product)
                        ->where('id_branch','=',$sucursal)
                        ->select('amount_real')
                        ->get()->last();
                    }

                    if (isset($inventories_quotations)){
                        $inventario = $inventories_quotations->amount_real;
                    } else {
                        $inventario = 0;
                    }

                if ($int_product->amount_per_product == 0) {

                   $div = 1;
                } else {
                    $div = $int_product->amount_per_product;
                }

                $disponible = intval($inventario/$div); //validador

                $a_producto[] = array($int_product->id_product,$disponible);

                    if(count($a_producto) > 0) {

                            foreach ($a_producto as $clave => $fila) {
                            $orden[$clave] = $fila[1];
                            }

                            array_multisort($orden, SORT_ASC, $a_producto);

                        //  dd($a_producto);

                            $cantidad_combos = $a_producto[0][1];

                    } else {
                            $cantidad_combos = 0;
                    }
            }



        } else {

            $cantidad_combos = 0;
        }

        return $cantidad_combos;

    }



    function consul_prod_invt($id_product,$sucursal = 1){ // buscar solo la cantidad actual del producto

             //dd($id_product);
             $buscar = DB::connection(Auth::user()->database_name)
             ->table('products')
             ->where('id','=',$id_product)
             ->select('type')->first();

            if (empty($buscar)){
                $amount_real = 0;
            } else {

                        if ($sucursal == 1) {

                            $inventories_quotations = DB::connection(Auth::user()->database_name)
                            ->table('inventory_histories')
                            ->where('id_product','=',$id_product)
                            ->select('amount_real')
                            ->get()->last();

                        } else { // prosuctos normal MATERIAP y MERCANCIA con sucursal

                            $inventories_quotations = DB::connection(Auth::user()->database_name)
                            ->table('inventory_histories')
                            ->where('id_product','=',$id_product)
                            ->where('id_branch','=',$sucursal)
                            ->select('amount_real')
                            ->get()->last();
                        }

                        if (isset($inventories_quotations)) {
                            if ($inventories_quotations->amount_real > 0) {
                            $amount_real = $inventories_quotations->amount_real;
                            } else {
                            $amount_real = 0;
                            }
                        } else {
                            $amount_real =0;
                        }


            }

        return $amount_real;
    }


    function transaction_inv($type,$id_product,$description = '-',$amount = 0,$price = 0,$date,$branch = 1,$centro_cost = 1,$delivery_note = 0,$id_historial_inv = 0,$id,$quotation = 0,$expense = null){

        $msg = 'Sin Registro';
        $global = new GlobalController;
       // $product = Inventory::on(Auth::user()->database_name)->where('id',$id_inventary)->get();

            if ($branch == 1) { // todas las sucurssales
                $inventories_quotations = DB::connection(Auth::user()->database_name)
                ->table('inventory_histories')
                ->where('id_product','=',$id_product)
                ->select('*')
                ->get()->last();
            } else { // sucursal especifica
                $inventories_quotations = DB::connection(Auth::user()->database_name)
                ->table('inventory_histories')
                ->where('id_product','=',$id_product)
                ->where('id_branch','=',$branch)
                ->select('*')
                ->get()->last();
            }



                if (empty($inventories_quotations)) {
                    //$msg = 'El Producto no tiene inventario o no existe.';
                    $amount_real = 0;
                } else {

                    $amount_real = $inventories_quotations->amount_real;

                }


                if ($date == null) {

                $date = Carbon::now();
                $date = $date->format('Y-m-d');

                } else {

                $date = date("Y-m-d",strtotime($date)); // validando date y convirtiendo a formato de la base de datos Y-m-d

                }

            $transaccion = 0;
            $agregar = 'true';
            $validar_combo = 'true';

            if ($amount > 0 ) {

                switch ($type) {
                    case 'compra':

                        if ($id_historial_inv != 0) {

                            $inventories_quotations_hist = DB::connection(Auth::user()->database_name)
                            ->table('inventory_histories')
                            ->where('id','=',$id_historial_inv)
                            ->select('id','amount')
                            ->get()->last();


                            if (!empty($inventories_quotations_hist)) {


                                if ($inventories_quotations_hist->amount == $amount) {

                                    $transaccion = $amount_real;
                                    $agregar = 'false';
                                } else {

                                    $transaccion = ($amount_real+$inventories_quotations_hist->amount)-$amount;
                                    $agregar = 'true';
                                    $type = 'compra';

                                }


                            } else {
                                $transaccion = $amount_real+$amount;
                            }
                        } else {

                            $transaccion = $amount_real+$amount;
                        }

                    break;
                    case 'venta':

                        if ($id_historial_inv != 0) {
                            $inventories_quotations_hist = DB::connection(Auth::user()->database_name)
                            ->table('inventory_histories')
                            ->where('id','=',$id_historial_inv)
                            ->select('id','amount')
                            ->get()->last();

                                if (!empty($inventories_quotations_hist)) {

                                        $amount = 0;
                                        $transaccion = $amount_real;
                                        $description = 'De Nota a Factura';
                                        $validar_combo = 'false';
                                }

                        } else {
                        $transaccion = $amount_real-$amount;
                        }
                    break;
                    case 'entrada':
                    $transaccion = $amount_real+$amount;
                    break;
                    case 'salida':
                    $transaccion = $amount_real-$amount;
                    break;
                    case 'nota':

                        if ($id_historial_inv != 0) {

                            $inventories_quotations_hist = DB::connection(Auth::user()->database_name)
                            ->table('inventory_histories')
                            ->where('id','=',$id_historial_inv)
                            ->select('id','amount')
                            ->get()->last();


                            if (!empty($inventories_quotations_hist)) {


                                if ($inventories_quotations_hist->amount == $amount) {
                                    $amount_nota = 0;
                                    $transaccion = $amount_real;
                                    $agregar = 'false';
                                } else {

                                    $transaccion = ($amount_real+$inventories_quotations_hist->amount)-$amount;
                                    $agregar = 'true';
                                    $type = 'aju_nota';

                                }


                            } else {
                                $transaccion = $amount_real-$amount;
                            }

                        } else {
                                $transaccion = $amount_real-$amount;
                        }

                    break;
                    case 'rev_nota':
                    $transaccion = $amount_real+$amount;
                    break;
                    case 'aju_nota':
                        if ($id_historial_inv != 0) {

                            $inventories_quotations_hist = DB::connection(Auth::user()->database_name)
                            ->table('inventory_histories')
                            ->where('id','=',$id_historial_inv)
                            ->select('id','amount')
                            ->get()->last();


                            if (!empty($inventories_quotations_hist)) {


                                if ($inventories_quotations_hist->amount == $amount) {
                                    $amount_nota = 0;
                                    $transaccion = $amount_real;
                                    $agregar = 'false';
                                } else {

                                    $transaccion = ($amount_real+$inventories_quotations_hist->amount)-$amount;
                                    $agregar = 'true';
                                    $type = 'aju_nota';

                                }


                            } else {
                                $transaccion = $amount_real-$amount;
                            }

                        } else {
                                $transaccion = $amount_real-$amount;
                        }
                    break;

                    case 'aju_compra':
                        if ($id_historial_inv != 0) {

                            $inventories_quotations_hist = DB::connection(Auth::user()->database_name)
                            ->table('inventory_histories')
                            ->where('id','=',$id_historial_inv)
                            ->select('id','amount')
                            ->get()->last();


                            if (!empty($inventories_quotations_hist)) {


                                if ($inventories_quotations_hist->amount == $amount) {
                                    $amount_nota = 0;
                                    $transaccion = $amount_real;
                                    $agregar = 'false';
                                } else {

                                    $transaccion = ($amount_real-$inventories_quotations_hist->amount)+$amount;
                                    $agregar = 'true';
                                    $type = 'aju_compra';

                                }


                            } else {
                                $transaccion = $amount_real;
                                $agregar = 'false';
                            }

                        } else {
                                $transaccion = $amount_real;
                                $agregar = 'false';
                        }
                    break;
                    case 'rev_venta':
                    $transaccion = $amount_real+$amount;
                    break;
                    case 'rev_compra':
                    $transaccion = $amount_real-$amount;
                    break;

                }


                $buscar = Product::on(Auth::user()->database_name)
                ->where('status','!=','X')
                ->select('type')
                ->find($id_product);

                if($buscar == null){
                    $typebuscar = 'SERVICIO';
                }else{
                    $typebuscar = $buscar->type;
                }

                    if ($transaccion < 0 and $typebuscar != 'COMBO') {

                       $msg = "La cantidad es mayor a la disponible en inventario";

                    } else {

                        $user       =   auth()->user();

                        if ($agregar == 'true') {

                             DB::connection(Auth::user()->database_name)->table('inventory_histories')->insert([
                            'id_product' => $id_product,
                            'id_user' => $user->id,
                            'id_branch' => $branch,
                            'id_centro_costo' => $branch,
                            'id_quotation_product' => $quotation,
                            'id_expense_detail' => $expense,
                            'date' => $date,
                            'type' => $type,
                            'price' => $price,
                            'amount' => $amount,
                            'amount_real' => $transaccion,
                            'status' => 'A']);

                            //////CONSULTANDO EL ULTIMO ID DE HISTORIAL///////////////////////

                            $id_last = DB::connection(Auth::user()->database_name)
                            ->table('inventory_histories')
                            ->select('id')
                            ->get()->last();

                            if ($type == 'nota' || $type == 'venta' || $type == 'aju_nota' || $type == 'factura'){
                                DB::connection(Auth::user()->database_name)->table('quotation_products')
                                ->where('id','=',$id)
                                ->update(['id_inventory_histories' => $id_last->id]);
                            }

                            if ($type == 'compra' || $type == 'aju_compra'){
                                DB::connection(Auth::user()->database_name)->table('expenses_details')
                                ->where('id','=',$id)
                                ->update(['id_inventory_histories' => $id_last->id]);
                            }

                            //////FIN CONSULTANDO EL ULTIMO ID DE HISTORIAL///////////////////////

                            //////PRODUCTO COMBO//////////////////////////////////////////////////


                            if($buscar->type == 'COMBO'){ // producto combo

                                $user     =   auth()->user();

                                $invento_combo = $global->consul_prod_invt($id_product);
                                $combos_disponibles = $global->consul_cant_combo($id_product,1);


                                if ($invento_combo <= 0) {

                                    $combo_products = ComboProduct::on(Auth::user()->database_name)
                                    ->where('id_combo',$id_product)
                                    ->orderBy('id' ,'desc')
                                    ->get();


                                    if(!empty($combo_products)) {


                                            foreach ($combo_products as $productwo){
                                                $amount_interno = 0;
                                                $transaccion = 0;
                                                $mov_trans = '';
                                                $type_interno = '';

                                                $amount_interno = $productwo->amount_per_product;

                                                $transaccion_interna = $global->consul_prod_invt($productwo->id_product);

                                                if ($type == 'salida') {
                                                    $type_interno = 'entrada';
                                                    $transaccion_interna = $transaccion_interna + $productwo->amount_per_product;
                                                }

                                                if ($type == 'entrada') {
                                                    $type_interno = 'salida';
                                                    $transaccion_interna = $transaccion_interna - $productwo->amount_per_product;
                                                }

                                                if ($type == 'venta' || $type == 'nota' || $type == 'rev_compra') {
                                                    $type_interno = 'salida';
                                                    $transaccion_interna = $transaccion_interna - $productwo->amount_per_product;
                                                }

                                                if ($type == 'compra' || $type == 'rev_venta' || $type == 'rev_nota') {
                                                    $type_interno = 'entrada';
                                                    $transaccion_interna = $transaccion_interna + $productwo->amount_per_product;
                                                }

                                                if ($agregar == 'true' and $validar_combo == 'true') {

                                                    $mov_trans = DB::connection(Auth::user()->database_name)->table('inventory_histories')->insert([
                                                    'id_product' => $productwo->id_product,
                                                    'id_user' => $user->id,
                                                    'id_branch' => $branch,
                                                    'id_centro_costo' => $branch,
                                                    'id_quotation_product' => $quotation,
                                                    'id_expense_detail' => $expense,
                                                    'id_combo' => $id_product,
                                                    'date' => $date,
                                                    'type' => $type_interno,
                                                    'price' => $price,
                                                    'amount' => $amount_interno,
                                                    'amount_real' => $transaccion_interna,
                                                    'status' => 'A']);

                                                }

                                                    // CREANDO COMPROBANTEE //////////////////////////////////////////////////////
                                                if ($type != 'salida' and $type != 'entrada') {

                                                    $bcv = 1;
                                                    $amount = 0;
                                                    $price_buy = 0;

                                                    $company = Company::on(Auth::user()->database_name)->find(1); // tasa de la compania
                                                    $bcv = $company->rate;

                                                    $productc = Product::on(Auth::user()->database_name)
                                                    ->select('price_buy','description','money')
                                                    ->find($productwo->id_product);

                                                    $headervoucher = new HeaderVoucher(); // Creando cabecera
                                                    $headervoucher->setConnection(Auth::user()->database_name);


                                                    if ($type == 'venta' || $type == 'nota' || $type == 'rev_compra') {
                                                    $headervoucher->description  = 'Materia Prima a Mercancia para la Venta Producto '.$productwo->id_product.' '.$productc->description;
                                                    }
                                                    if ($type == 'compra' || $type == 'rev_venta' || $type == 'rev_nota') {
                                                    $headervoucher->description  = 'Aumento de Materia prima de Producto '.$productwo->id_product.' '.$productc->description;
                                                    }

                                                    $headervoucher->date   = $date;
                                                    $headervoucher->status   = 1;
                                                    $headervoucher->save();

                                                    $account = Account::on(Auth::user()->database_name)
                                                    ->where('description','LIKE','%Materia Prima%')
                                                    ->where('level','5')
                                                    ->first();

                                                    $account_two = Account::on(Auth::user()->database_name)
                                                    ->where('description','LIKE','%Mercancia para la Venta%')
                                                    ->where('level','5')
                                                    ->first();


                                                    $amount = $productc->price_buy * $amount_interno;

                                                    if($productc->money == 'D'){
                                                    $amount = ($productc->price_buy * $bcv) * $amount_interno ;
                                                    }

                                                    if(isset($account) and isset($account_two) ){


                                                        if ($type == 'venta' || $type == 'nota' || $type == 'rev_compra') {
                                                        $this->add_movement($bcv,$headervoucher->id,$account->id,$quotation,$user->id,0,$amount); // incrementa
                                                        $this->add_movement($bcv,$headervoucher->id,$account_two->id,$quotation,$user->id,$amount,0);
                                                        }
                                                        if ($type == 'compra' || $type == 'rev_venta' || $type == 'rev_nota') {
                                                        $this->add_movement($bcv,$headervoucher->id,$account->id,$quotation,$user->id,$amount,0); // disminuye
                                                        $this->add_movement($bcv,$headervoucher->id,$account_two->id,$quotation,$user->id,0,$amount);
                                                        }
                                                    }
                                                }

                                            }

                                    }
                                }

                            }
                            ////fin PRODUCTO COMBO

                        }

                        switch ($type) {
                            case 'compra':
                                $msg = 'La Compra fue registrada con exito';
                                break;
                            case 'venta';
                                $msg = 'La Venta fue registrada con exito';
                                break;
                            case 'nota':
                                $msg = 'exito';//'La Nota fue registrada con exito';
                                break;
                            case 'rev_nota':
                                $msg = 'Reverso de Nota exitoso';
                                break;
                            case 'aju_nota':
                                $msg = 'Eliminacion de producto de la Nota exitoso';
                                break;
                            case 'aju_compra':
                                $msg = 'Ajuste de producto de Compra exitoso';
                                break;
                            case 'rev_venta':
                                $msg = 'Reverso de Factura exitoso';
                                break;
                            case 'entrada':
                                $msg = 'Producto ID '.$id_product.' Agregado a inventario exitosamente';
                                break;
                            case 'salida':
                                $msg = 'Salida de inventario exitoso';
                                break;
                            default:
                                $msg = 'La operacion no es valida';
                                break;
                        }
                    }

            } else { // condicion cantidad 0
                /*if($type == 'creado') {

                    $user       =   auth()->user();

                     DB::connection(Auth::user()->database_name)->table('inventory_histories')->insert([
                    'id_product' => $id_product,
                    'id_user' => $user->id,
                    'id_branch' => 1,
                    'id_centro_costo' => 1,
                    'id_quotation_product' => 0,
                    'id_expense_detail' => 0,
                    'date' => $date,
                    'type' => $type,
                    'price' => $price,
                    'amount' => 0,
                    'amount_real' => 0,
                    'status' => 'A']);

                    $msg = "Producto Creado";

                } else { */

                    $msg = "La cantidad de la oprecion debe ser mayor a cero";
                //}

            }

    return $msg;

    } // fin de funcion transaccion

   // funcion para subir imagenes
    public static function setCaratula($foto,$id = '0',$code_comercial = '0'){

        $fecha_hora = date('dmYhis', time());

        if ($foto) {
            $company = Company::on(Auth::user()->database_name)->find(1);

            $imageName = $id.'-'.$code_comercial.'-'.$fecha_hora.'.jpg';
            $imagen = Image::make($foto)->encode('jpg',90);
            $imagen->resize(600,800, function($constraint) {
                $constraint->upsize();
            });
            Storage::disk('public')->put("img/$company->login/productos/$imageName", $imagen->stream());

            return $imageName;

        }else{
            return 'false';
        }
    }
   // fin funcion para subir imagenes

    // funcion para actualizar imagenes
      public static function setCaratulaup($foto,$id = '0',$code_comercial = '0',$actual = null){

        $fecha_hora = date('dmYhis', time());

        if ($foto) {
            $company = Company::on(Auth::user()->database_name)->find(1);

            Storage::disk('public')->delete("img/$company->login/productos/$actual");

            $imageName = $id.'-'.$code_comercial.'-'.$fecha_hora.'.jpg';
            $imagen = Image::make($foto)->encode('jpg',90);
            $imagen->resize(600,800, function($constraint) {
                $constraint->upsize();
            });
            Storage::disk('public')->put("img/$company->login/productos/$imageName", $imagen->stream());

            return $imageName;

        }else{
            return 'false';
        }
    }
   // fin funcion para subir imagenes

    public function add_movement($tasa,$id_header,$id_account,$id_invoice,$id_user,$debe,$haber){

        $detail = new DetailVoucher();
        $detail->setConnection(Auth::user()->database_name);

        $detail->id_account = $id_account;
        $detail->id_header_voucher = $id_header;
        $detail->id_invoice = $id_invoice;
        $detail->user_id = $id_user;
        $detail->tasa = $tasa;
        $detail->debe = $debe;
        $detail->haber = $haber;
        $detail->status =  "C";
        $detail->save();

        /*Le cambiamos el status a la cuenta a M, para saber que tiene Movimientos en detailVoucher */
        $account = Account::on(Auth::user()->database_name)->findOrFail($detail->id_account);

        if($account->status != "M"){
            $account->status = "M";
            $account->save();
        }


    }

}
