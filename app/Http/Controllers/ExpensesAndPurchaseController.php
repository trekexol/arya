<?php

namespace App\Http\Controllers;


use App;
use App\Account;
use App\Branch;
use App\DetailVoucher;
use App\ExpensePayment;
use App\MultipaymentExpense;
use App\ExpensesAndPurchase;
use App\ExpensesDetail;
use App\HeaderVoucher;
use App\Product;
use App\Provider;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Anticipo;
use App\Company;
use App\Http\Controllers\Historial\HistorialExpenseController;
use App\Http\Controllers\Validations\ExpenseDetailValidationController;
use App\Inventory;
use App\IslrConcept;
use Illuminate\Support\Facades\Auth;
use App\DebitNoteExpense;
use App\DebitNoteDetailExpense;
use App\InventoryHistories;
use App\FacturasCour;
use Faker\Core\Number;

class ExpensesAndPurchaseController extends Controller
{

    public function __construct(){

        $this->middleware('auth');
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Gastos y Compras');
       }


       public function index(request $request)
       {
        $agregarmiddleware = $request->get('agregarmiddleware');
        $actualizarmiddleware = $request->get('actualizarmiddleware');
        $eliminarmiddleware = $request->get('eliminarmiddleware');
        $namemodulomiddleware = $request->get('namemodulomiddleware');


    $expensesandpurchases = ExpensesAndPurchase::on(Auth::user()->database_name)->orderBy('id' ,'DESC')
                                                            ->where('amount_with_iva','=',null)
                                                            ->where('status',1)
                                                            ->get();

            return view('admin.expensesandpurchases.index',compact('namemodulomiddleware','expensesandpurchases','agregarmiddleware','eliminarmiddleware'));

   }


   public function index_historial(request $request)
   {


    if(Auth::user()->role_id == '1' || $request->get('namemodulomiddleware') == 'Gastos y Compras'){


        $agregarmiddleware = $request->get('agregarmiddleware');
        $actualizarmiddleware = $request->get('actualizarmiddleware');
        $eliminarmiddleware = $request->get('eliminarmiddleware');

       $user       =   auth()->user();
       $users_role =   $user->role_id;


        $expensesandpurchases = ExpensesAndPurchase::on(Auth::user()->database_name)->orderBy('id' ,'DESC')
                                                    ->whereIn('status',['C','X','P']) // 0 es eliminada
                                                    ->orderBy(DB::raw('SUBSTR(date,0,4)'),'DESC')
                                                    ->get();

        foreach($expensesandpurchases as $expensesandpurchasesr){

            if(Auth::user()->id_company == '26'){


           $validarfact = FacturasCour::on(Auth::user()->database_name)
            ->where('id_expense',$expensesandpurchasesr->id)
            ->first();

            if($validarfact){

                if($validarfact->tipo_fac == 1){
                    $nombre = 'ADUANA';
                }
                elseif($validarfact->tipo_fac == 2){
                    $nombre = 'INTERNACIONAL';
                }elseif($validarfact->tipo_fac == 3){
                    $nombre = 'SEGURO';
                }elseif($validarfact->tipo_fac == 4){
                    $nombre = 'PICK UP';
                }elseif($validarfact->tipo_fac == 5){
                    $nombre = 'MANEJO';
                }elseif($validarfact->tipo_fac == 6){
                    $nombre = 'IMPUESTOS';
                }


                if($validarfact->tipo_movimiento == 1){
                    $movimiento = 'PALETA';
                }
                elseif($validarfact->tipo_movimiento == 2){
                    $movimiento = 'CONTENEDOR';
                }elseif($validarfact->tipo_movimiento == 3){
                    $movimiento = 'GUIA MASTER';
                }elseif($validarfact->tipo_movimiento == 4){
                    $movimiento = 'TULA';
                }elseif($validarfact->tipo_movimiento == 5){
                    $movimiento = 'GUIA TERRESTRE';
                }


                $expensesandpurchasesr->validar = true;
                $expensesandpurchasesr->nombrefac =  $nombre;
                $expensesandpurchasesr->movimientofac =  $movimiento;
                $expensesandpurchasesr->numerofac =  $validarfact->numero;

            }else{

                $expensesandpurchasesr->validar = false;

            }

            }else{
                $expensesandpurchasesr->validar = false;
            }


        }


        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');


       return view('admin.expensesandpurchases.index_historial',compact('agregarmiddleware','expensesandpurchases','datenow'));

    }else{

        return redirect('/expensesandpurchases')->withDanger('No Tiene Permiso');

     }
   }


   public function movements_expense($id_expense,$coin)
   {


       $user       =   auth()->user();
       $users_role =   $user->role_id;

           $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->find($id_expense);
           $detailvouchers = DetailVoucher::on(Auth::user()->database_name)
           ->join('accounts','accounts.id','id_account')
           ->where('detail_vouchers.status','!=','X')
           ->where('detail_vouchers.id_expense',$id_expense)
           ->whereIn('detail_vouchers.status',['C','F'])
           ->orderBy('detail_vouchers.debe','desc')
           ->orderBy('accounts.code_one','desc')
           ->orderBy('accounts.code_two','asc')
           ->orderBy('accounts.code_three','asc')
           ->orderBy('accounts.code_four','asc')
           ->orderBy('accounts.code_five','asc')
           ->get();
           

            $multipayments_detail = null;
            $expenses = null;

            //Buscamos a la factura para luego buscar atraves del header a la otras facturas
            $multipayment = MultipaymentExpense::on(Auth::user()->database_name)->where('id_expense',$id_expense)->first();
            if(isset($multipayment)){
                $expenses = MultipaymentExpense::on(Auth::user()->database_name)->where('id_header',$multipayment->id_header)->get();
                /*$multipayments_detail = DetailVoucher::on(Auth::user()->database_name)->where('id_header_voucher',$multipayment->id_header)
                ->orderBy('debe','DESC')
                ->get();*/

                $multipayments_detail = DetailVoucher::on(Auth::user()->database_name)->where('id_header_voucher',$multipayment->id_header)
                ->join('accounts','accounts.id','id_account')
                ->where('detail_vouchers.status','!=','X')
                ->orderBy('detail_vouchers.debe','desc')
                ->orderBy('accounts.code_one','desc')
                ->orderBy('accounts.code_two','asc')
                ->orderBy('accounts.code_three','asc')
                ->orderBy('accounts.code_four','asc')
                ->orderBy('accounts.code_five','asc')
                ->get();

            }




       return view('admin.expensesandpurchases.index_movement',compact('coin','detailvouchers','expense','expenses','multipayments_detail'));
   }


   public function index_delivery_note(request $request)
   {

     if(Auth::user()->role_id == '1' || $request->get('namemodulomiddleware') == 'Gastos y Compras'){

        $agregarmiddleware = $request->get('agregarmiddleware');
        $actualizarmiddleware = $request->get('actualizarmiddleware');
        $eliminarmiddleware = $request->get('eliminarmiddleware');
        $namemodulomiddleware = $request->get('namemodulomiddleware');


        $expenses = ExpensesAndPurchase::on(Auth::user()->database_name)->orderBy('id' ,'DESC')
                                ->where('date_delivery_note','<>',null)
                                ->where('status',1)
                                ->get();


       return view('admin.expensesandpurchases.indexdeliverynote',compact('eliminarmiddleware','actualizarmiddleware','agregarmiddleware','expenses'));

     }else{

        return redirect('/expensesandpurchases')->withDanger('No Tiene Permiso');

     }
   }





   public function createdeliverynote(request $request,$id_expense,$coin)
   {


      if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){
        $expense = null;

        if(isset($id_expense)){
           $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->findOrFail($id_expense);
           $expense->coin = $coin;
           $expense->save();
        }

        if(isset($expense)){

            $inventories_expenses = DB::connection(Auth::user()->database_name)->table('products')
                                                            ->join('inventories', 'products.id', '=', 'inventories.product_id')
                                                           ->rightJoin('expenses_details', 'inventories.id', '=', 'expenses_details.id_inventory')
                                                           ->where('expenses_details.id_expense',$expense->id)
                                                           ->where('expenses_details.status',['1','C'])
                                                           ->select('products.*','expenses_details.price as price','expenses_details.rate as rate',
                                                           'expenses_details.amount as amount_expense','expenses_details.exento as retiene_iva_expense'
                                                           ,'expenses_details.islr as retiene_islr_expense')
                                                           ->get();

           $total= 0;
           $base_imponible= 0;

           //este es el total que se usa para guardar el monto de todos los productos que estan exentos de iva, osea retienen iva
           $total_retiene_iva = 0;
           $retiene_iva = 0;

           $total_retiene_islr = 0;
           $retiene_islr = 0;

           foreach($inventories_expenses as $var){
               //Se calcula restandole el porcentaje de descuento (discount)

                   $percentage = ($var->price * $var->amount) * ($var->porc_discount/100);

                   $total_less_percentage = ($var->price * $var->amount) - $percentage;

                   $total += number_format($var->price * $var->amount_expense, 2, '.', '');

               //-----------------------------

               if($var->retiene_iva_expense == 0){

                   $base_imponible += ($var->price * $var->amount_expense);

               }else{
                   $retiene_iva += ($var->price * $var->amount_expense);
               }

               if($var->retiene_islr_expense == 1){

                   $retiene_islr += ($var->price * $var->amount_expense);

               }

           }

           $expense->total_factura = $total;
           $expense->base_imponible = $base_imponible;

           $date = Carbon::now();
           $datenow = $date->format('Y-m-d');

           if($coin == 'bolivares'){
               $bcv = null;

           }else{
               $bcv = $expense->rate;
           }


            return view('admin.expensesandpurchases.createdeliverynote',compact('coin','expense','datenow','bcv','total_retiene_iva','total_retiene_islr'));
        }else{
            return redirect('/expensesandpurchases')->withDanger('La compra no existe');
        }
    }else{

        return redirect('/expensesandpurchases')->withDanger('No Tiene Permiso');

     }
   }

   public function deletedeliverynote(Request $request)
   {
       if(Auth::user()->role_id == '1' || $request->get('eliminarmiddleware') == '1'){

       $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->findOrFail(request('id_expense_modal'));
       $expense->status = 'X';
       $expense->save();

       $global = new GlobalController();
       $global->deleteAllProductsExpense($expense->id);

       $historial_expense = new HistorialExpenseController();

       $historial_expense->registerAction($expense,"expense","Se elimino la Orden de compra");

       return redirect('expensesandpurchases/indexnotasdeentrega')->withDanger('Se elimino la Orden de Compra!!');
       }else{

           return redirect('/expensesandpurchases')->withDanger('No Tiene Permiso!');
       }
   }


    public function create_expense($id_provider = null)
    {

        $provider = null;

        if(isset($id_provider)){
            $provider = Provider::on(Auth::user()->database_name)->find($id_provider);
        }

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');

        return view('admin.expensesandpurchases.createexpense',compact('datenow','provider'));
    }

    public function retencion_iva($id_expense,$coin)
    {
        $pdf = App::make('dompdf.wrapper');
        $expense = null;
        $provider = null;

        if(isset($id_expense)){
            $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->find($id_expense);
            $provider = Provider::on(Auth::user()->database_name)->find($expense->id_provider);
            $pago = ExpensePayment::on(Auth::user()->database_name)
                        ->where('id_expense',$id_expense)
                        ->select('created_at')
                        ->get()->last();
        }

        if(isset($pago)){

            $periodo_pago = substr($expense->date_payment,0,4);
            $mes_pago = substr($expense->date_payment,5,2);

        } else {

            $periodo_pago = substr($expense->dateregistro,0,4);
            $mes_pago = substr($expense->dateregistro,5,2);
        }

        if((isset($expense)) && ($expense->retencion_iva != 0)){
            $date = Carbon::now();
            $datenow = $date->format('d-m-Y');
            $period = $date->format('Y-m');

            $company = Company::on(Auth::user()->database_name)->find(1);


            $pdf = $pdf->loadView('admin.expensesandpurchases.retencion_iva',compact('pago','company','expense','datenow','period','provider','periodo_pago','mes_pago'))->setPaper('letter', 'landscape');
            return $pdf->stream();
        }else{
            return redirect('/expensesandpurchases/expensevoucher/'.$id_expense.'/bolivares')->withDanger('Esta factura no retiene IVA!');
        }

    }


    public function updateexpense(request $request)
    {

        if(Auth::user()->role_id == '1' || $request->get('actualizarmiddleware') == '1'){

                    if(isset($request->centro_costo)){
                        $centro_costo = $request->centro_costo;
                    } else {
                        $centro_costo = 1;
                    }

                    if ($request->observation == '-1'){
                        $observation = '';
                    }else{

                        $observation = $request->observation;
                    }
                    if ($request->invoice == '-1'){
                        $invoice = '';
                    }else{
                        $invoice = $request->invoice;
                    }
                    if ($request->serie == '-1'){
                        $serie = '';
                    }else{
                        $serie = $request->serie;
                    }

                    $update =  ExpensesAndPurchase::on(Auth::user()->database_name)->where('id',$request->id_quotation)
                                    ->update(['coin'=>$request->coin,'observation' => $observation,'invoice' => $invoice,'serie' => $serie,'date'=>$request->date, 'id_branch' => $centro_costo,'dateregistro'=>$request->dateregistro]);


            return redirect('/expensesandpurchases/register/'.$request->id_quotation.'/'.$request->coin)->withSuccess('Actualizacion Exitosa!');


        /*$historial_quotation = new HistorialQuotationController();

        $historial_quotation->registerAction($var,"quotation","Actualizó la Compra");*/

       // return view('admin.expensesandpurchases.createexpense',compact('datenow','provider'));


    }else{
        return redirect('/expensesandpurchases')->withDanger('No Tiene Permiso');
    }

    }

    public function retencion_islr($id_expense,$coin)
    {
        $pdf = App::make('dompdf.wrapper');
        $expense = null;
        $provider = null;

        if(isset($id_expense)){
            $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->find($id_expense);
            $provider = Provider::on(Auth::user()->database_name)->find($expense->id_provider);
        }

        if((isset($expense)) && ($expense->retencion_islr != 0)){
            $date = Carbon::now();
            $datenow = $date->format('d-m-Y');
            $period = $date->format('Y-m');

            $company = Company::on(Auth::user()->database_name)->find(1);

            $expense_details =  DB::connection(Auth::user()->database_name)->select('SELECT SUM(amount * price) AS total
                                FROM expenses_details
                                WHERE id_expense = ? AND
                                islr = 1 AND
                                status = ?
                                '
                                , [$expense->id,'C']);


            $total_islr_details = $expense_details[0]->total;



            $pdf = $pdf->loadView('admin.expensesandpurchases.retencion_islr',compact('total_islr_details','company','expense','datenow','period','provider'))->setPaper('a4', 'landscape');
            return $pdf->stream();
        }else{
            return redirect('/expensesandpurchases/expensevoucher/'.$id_expense.'/bolivares')->withDanger('Esta factura no retiene ISLR!');
        }

    }

    public function create_expense_detail(request $request,$id_expense,$coin,$type = null,$id_product = null,$account = null,$subaccount = null)
    {


        if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){

            $agregarmiddleware = $request->get('agregarmiddleware');
            $actualizarmiddleware = $request->get('actualizarmiddleware');
            $eliminarmiddleware = $request->get('eliminarmiddleware');

        $expense = null;
        $provider = null;
        $expense_details = null;
        $inventory = null;

        $accounts_inventory = null;

        if(isset($id_expense)){
            $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->find($id_expense);

            $provider = Provider::on(Auth::user()->database_name)->find($expense->id_provider);

            $expense_details = ExpensesDetail::on(Auth::user()->database_name)->where('id_expense',$expense->id)->get();

           // dd($id_inventory);

            if(isset($id_product)){
                $inventory = Product::on(Auth::user()->database_name)->find($id_product);

                if(($inventory->type == 'MERCANCIA') || ($inventory->type == 'COMBO') || ($inventory->type == 'MATERIAP')){
                    $accounts_inventory = Account::on(Auth::user()->database_name)->select('id','description')->where('code_one',1)
                    ->where('code_two', 1)
                    ->where('code_three', 3)
                    ->where('code_four',1)
                    ->where('code_five', '<>',0)
                    ->orderBy('description','asc')
                    ->get();
                }else{
                    $accounts_inventory = Account::on(Auth::user()->database_name)->select('id','description')
                    ->where('code_one',5)
                    ->where('code_two', '<>',0)
                    ->where('code_three', '<>',0)
                    ->where('code_four', '<>',0)
                    ->orderBy('description','asc')
                    ->get();
                }

            }
        }

        $contrapartidas     = Account::on(Auth::user()->database_name)
        ->where('code_one', '<>',0)
        ->where('code_one', '<>',4)
        ->where('code_one', '<>',3)
        ->where('code_one', '<>',2)
        ->where('code_two', '<>',0)
        ->where('code_three', '<>',0)
        ->where('code_four', '<>',0)
        ->where('code_five', '=',0)
        ->orderBY('description','asc')->pluck('description','id')->toArray();




        $branches = Branch::on(Auth::user()->database_name)->orderBy('description','desc')->get();

        $company = Company::on(Auth::user()->database_name)->find(1);
        $global = new GlobalController();

        //Si la taza es automatica
        if($company->tiporate_id == 1){
            $bcv = $global->search_bcv();
        }else{
            //si la tasa es fija
            $bcv = $company->rate;
        }

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');

        if(($coin == 'bolivares') || (!isset($coin)) ){

            if(isset($id_product)){
                $inventory = Product::on(Auth::user()->database_name)->find($id_product);

                if( $inventory->money!= 'Bs'){
                    $inventory->price_buy = $inventory->price_buy * $expense->rate;
                }
            }
            $coin = 'bolivares';
        }else{
            if(isset($id_product)){
                $inventory = Product::on(Auth::user()->database_name)->find($id_product);

                if( $inventory->money == 'Bs'){
                    $inventory->price_buy = $inventory->price_buy / $expense->rate;
                }
            }
            $coin = 'dolares';
        }

        return view('admin.expensesandpurchases.create',compact('eliminarmiddleware','actualizarmiddleware','agregarmiddleware','type','coin','bcv','datenow','provider','expense','expense_details','branches','inventory','accounts_inventory','contrapartidas','account','subaccount'));

    }else{
        return redirect('/expensesandpurchases')->withDanger('No Tienes Permiso!');
    }

    }

    public function create_expense_voucher($id_expense,$coin)
    {

        $expense = null;
        $provider = null;
        $expense_details = null;

        if(isset($id_expense)){
            $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->find($id_expense);


        }else{
            return redirect('/expensesandpurchases')->withDanger('El Pago no existe');
        }

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');

        if($coin != 'bolivares'){
            $bcv = $expense->rate;
        }else{
            $bcv = 1;
        }

        return view('admin.expensesandpurchases.create_payment_voucher',compact('coin','expense','datenow','bcv'));

    }



    public function create_payment(request $request,$id_expense,$coin)
    {

        if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){
        $expense = null;
        $provider = null;
        $expense_details = null;

        if(isset($id_expense)){
            $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->find($id_expense);

            $provider = Provider::on(Auth::user()->database_name)->find($expense->id_provider);

            $expense_details = ExpensesDetail::on(Auth::user()->database_name)->where('id_expense',$expense->id)->get();
        }else{
            return redirect('/expensesandpurchases')->withDanger('El Pago no existe');
        }

            $anticipos_sum_bolivares = Anticipo::on(Auth::user()->database_name)->where('status',1)
                                                ->where('id_provider',$expense->id_provider)
                                                ->where(function ($query) use ($expense){
                                                    $query->where('id_expense',null)
                                                        ->orWhere('id_expense',$expense->id);
                                                })
                                                ->where('coin','like','bolivares')
                                                ->sum('amount');

            $total_dolar_anticipo = Anticipo::on(Auth::user()->database_name)->where('status',1)
                                ->where('id_provider',$expense->id_provider)
                                ->where(function ($query) use ($expense){
                                    $query->where('id_expense',null)
                                        ->orWhere('id_expense',$expense->id);
                                })
                                ->where('coin','like','dolares')
                                ->select(DB::connection(Auth::user()->database_name)->raw('SUM(amount/rate) as dolar'))->first();



            $anticipos_sum_dolares = 0;
            if(isset($total_dolar_anticipo->dolar)){
                $anticipos_sum_dolares = $total_dolar_anticipo->dolar;

            }

            $accounts_bank = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
                        ->where('code_two', 1)
                        ->where('code_three', 1)
                        ->where('code_four', 2)
                        ->where('code_five', '<>',0)
                        ->where('description','not like', 'Punto de Venta%')
                        ->orderBy('description','asc')
                        ->get();

            $accounts_efectivo = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
                        ->where('code_two', 1)
                        ->where('code_three', 1)
                        ->where('code_four', 1)
                        ->where('code_five', '<>',0)
                        ->orderBy('description','asc')
                        ->get();

            $accounts_punto_de_venta = DB::connection(Auth::user()->database_name)->table('accounts')->where('description','LIKE', 'Punto de Venta%')
                        ->orderBy('description','asc')
                        ->get();

             $total= 0;
             $base_imponible= 0;

             $retiene_iva = 0;

             $total_retiene_iva = 0;
             $total_retiene_islr = 0;

             foreach($expense_details as $var){

                $percentage = ($var->price * $var->amount) * ($var->porc_discount/100);

                $total_less_percentage = ($var->price * $var->amount) - $percentage;

                if($coin == 'bolivares'){
                    $total += number_format($total_less_percentage,2,'.','');
                }else {
                    $total += number_format($total_less_percentage / $expense->rate,2,'.','');
                }

                  //  $total += ($var->price * $var->amount);

                if($var->exento == 0){
                    $base_imponible += $total_less_percentage;
                }

                if($var->islr == 1){
                    $total_retiene_islr += $total_less_percentage;
                }
             }



             $date = Carbon::now();
             $datenow = $date->format('Y-m-d');

             if($coin == 'bolivares'){
                $bcv = null;

             }else{
                $bcv = $expense->rate;
               //$total = number_format($total / $expense->rate,2,'.','');
                $base_imponible = $base_imponible / $expense->rate;
             }

             $expense->total_factura = number_format($total, 2, '.', '');
             $expense->base_imponible = $base_imponible;

             $anticipos_sum = 0;
             if(isset($coin)){
                 if($coin == 'bolivares'){
                    $bcv = null;
                    //Si la factura es en BS, y tengo anticipos en dolares, los multiplico los dolares por la tasa a la que estoy facturando
                    $anticipos_sum_dolares =  $anticipos_sum_dolares * $expense->rate;
                    $anticipos_sum = $anticipos_sum_bolivares + $anticipos_sum_dolares;
                 }else{
                    $bcv = $expense->rate;
                     //Si la factura es en Dolares, y tengo anticipos en bolivares, divido los bolivares por la tasa a la que estoy facturando
                    $anticipos_sum_bolivares =  $anticipos_sum_bolivares / $expense->rate;
                    $anticipos_sum = $anticipos_sum_bolivares + $anticipos_sum_dolares;
                 }
             }else{
                $bcv = null;
             }


             /*Aqui revisamos el porcentaje de retencion de iva que tiene el proveedor, para aplicarlo a productos que retengan iva */
             $provider = Provider::on(Auth::user()->database_name)->find($expense->id_provider);

             $company = Company::on(Auth::user()->database_name)->find(1);
             $igtfporc = $company->IGTF_porc ?? 3;
             $impuesto = $company->tax_1 ?? 1;
             $impuesto2 = $company->tax_2 ?? 1;
             $impuesto3 = $company->tax_3 ?? 1;


             if (isset($provider->concepto_islr)){

                if ($provider->concepto_islr > 0){
                    $islrconcept_ps = IslrConcept::on(Auth::user()->database_name)->find($provider->concepto_islr);
                    $islr_pagos_mayores = $islrconcept_ps->pagos_mayores;
                    $islr_sustraendo = $islrconcept_ps->sustraendo;
                } else {
                    $islr_pagos_mayores = 0;
                    $islr_sustraendo = 0;
                }
             } else {
                    $islr_pagos_mayores = 0;
                    $islr_sustraendo = 0;
             }


            $islrconcepts = IslrConcept::on(Auth::user()->database_name)->orderBy('id','asc')->get();

             return view('admin.expensesandpurchases.create_payment',compact('coin','expense','datenow'
                                ,'expense_details','accounts_bank', 'accounts_efectivo'
                                ,'accounts_punto_de_venta','anticipos_sum'
                                ,'total_retiene_iva','total_retiene_islr','bcv','provider'
                                ,'islrconcepts','igtfporc','impuesto','impuesto2','impuesto3','islr_pagos_mayores','islr_sustraendo'));

                            }else{
                                return redirect('/expensesandpurchases')->withDanger('No Tiene Permiso');
                            }


    }

    public function create_payment_after(request $request,$id_expense,$coin)
    {

        if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){
        $expense = null;
        $provider = null;
        $expense_details = null;

        if(isset($id_expense)){
            $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->find($id_expense);

            $provider = Provider::on(Auth::user()->database_name)->find($expense->id_provider);

            $expense_details = ExpensesDetail::on(Auth::user()->database_name)->where('id_expense',$expense->id)->get();

            $debitnoteexpense = DebitNoteExpense::on(Auth::user()->database_name)
            ->Where('id_expense',$id_expense)
            ->first();

            if($debitnoteexpense){
                if($debitnoteexpense->percentage > 0){
                    if($coin == 'dolares'){
                        $montodebito =  $debitnoteexpense->monto_perc / $debitnoteexpense->rate;
                      } else{
                          $montodebito =  $debitnoteexpense->monto_perc;
                      }
                      $creditrue = false;
                }elseif($debitnoteexpense->percentage == 0){
                    if($coin == 'dolares'){
                        $montodebito =  $debitnoteexpense->monto_perc / $debitnoteexpense->rate;
                      } else{
                          $montodebito =  $debitnoteexpense->monto_perc;
                      }
                      $creditrue = true;
                }else{
                    $creditrue = false;
                    $montodebito = 0;
                }
            }else{
                $creditrue = false;
                $montodebito = 0;
            }

        }else{
            return redirect('/expensesandpurchases')->withDanger('El Pago no existe');
        }

            $anticipos_sum_bolivares = Anticipo::on(Auth::user()->database_name)->where('status',1)
                                        ->where('id_provider',$expense->id_provider)
                                        ->where(function ($query) use ($expense){
                                            $query->where('id_expense',null)
                                                ->orWhere('id_expense',$expense->id);
                                        })
                                        ->where('coin','like','bolivares')
                                        ->sum('amount');

            $total_dolar_anticipo = Anticipo::on(Auth::user()->database_name)->where('status',1)
                                        ->where('id_provider',$expense->id_provider)
                                        ->where(function ($query) use ($expense){
                                            $query->where('id_expense',null)
                                                ->orWhere('id_expense',$expense->id);
                                        })
                                        ->where('coin','like','dolares')
                                        ->select(DB::connection(Auth::user()->database_name)->raw('SUM(amount/rate) as dolar'))
                                        ->first();


            $anticipos_sum_dolares = 0;
            if(isset($total_dolar_anticipo->dolar)){
                $anticipos_sum_dolares = $total_dolar_anticipo->dolar;

            }

            $accounts_bank = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
                        ->where('code_two', 1)
                        ->where('code_three', 1)
                        ->where('code_four', 2)
                        ->where('code_five', '<>',0)
                        ->where('description','not like', 'Punto de Venta%')
                        ->orderBy('description','asc')
                        ->get();
            $accounts_efectivo = DB::connection(Auth::user()->database_name)->table('accounts')->where('code_one', 1)
                        ->where('code_two', 1)
                        ->where('code_three', 1)
                        ->where('code_four', 1)
                        ->where('code_five', '<>',0)
                        ->orderBy('description','asc')
                        ->get();
            $accounts_punto_de_venta = DB::connection(Auth::user()->database_name)->table('accounts')->where('description','LIKE', 'Punto de Venta%')
                        ->orderBy('description','asc')
                        ->get();

             $total= 0;
             $base_imponible= 0;

             $retiene_iva = 0;
            // $retiene_islr = 0;

             $total_retiene_iva = 0;
             $total_retiene_islr = 0;

             foreach($expense_details as $var){

                    $total += number_format($var->price * $var->amount, 2, '.', '');

                if($var->exento == 0){
                    $base_imponible += ($var->price * $var->amount);
                }

                if($var->islr == 1){
                    $total_retiene_islr += ($var->price * $var->amount);
                }
             }



             $date = Carbon::now();
             $datenow = $date->format('Y-m-d');

             if($coin == 'bolivares'){
                $bcv = null;

               /* if($creditrue == TRUE){
                    $total = $total - $montodebito;

                }else{
                    $total = $total + $montodebito;

                }*/


            }else{

                $bcv = $expense->rate;
                $total = $total / $expense->rate;

              /*  if($creditrue == TRUE){
                    $total = $total - $montodebito;

                }else{
                    $total = $total + $montodebito;

                }*/


                $base_imponible = $base_imponible / $expense->rate;


             }
            if($base_imponible == 0){
                $expense->total_factura = number_format($total,2,'.','');
                $expense->base_imponible = $base_imponible;
            }else{
                $expense->total_factura = number_format($total,2,'.','');
                $expense->base_imponible = $base_imponible;
            }


             $anticipos_sum = 0;
             if(isset($coin)){
                 if($coin == 'bolivares'){
                    $bcv = null;
                    //Si la factura es en BS, y tengo anticipos en dolares, los multiplico los dolares por la tasa a la que estoy facturando
                    $anticipos_sum_dolares =  $anticipos_sum_dolares * $expense->rate;
                    $anticipos_sum = $anticipos_sum_bolivares + $anticipos_sum_dolares;
                 }else{
                    $bcv = $expense->rate;
                     //Si la factura es en Dolares, y tengo anticipos en bolivares, divido los bolivares por la tasa a la que estoy facturando
                    $anticipos_sum_bolivares =  $anticipos_sum_bolivares / $expense->rate;
                    $anticipos_sum = $anticipos_sum_bolivares + $anticipos_sum_dolares;
                 }
             }else{
                $bcv = null;
             }

             /*Aqui revisamos el porcentaje de retencion de iva que tiene el proveedor, para aplicarlo a productos que retengan iva */
             $provider = Provider::on(Auth::user()->database_name)->find($expense->id_provider);

             if (isset($provider->concepto_islr)){

                if ($provider->concepto_islr > 0){
                    $islrconcept_ps = IslrConcept::on(Auth::user()->database_name)->find($provider->concepto_islr);
                    $islr_pagos_mayores = $islrconcept_ps->islr_pagos_mayores;
                    $islr_sustraendo = $islrconcept_ps->islr_sustraendo;
                } else {
                    $islr_pagos_mayores = 0;
                    $islr_sustraendo = 0;
                }
             } else {
                    $islr_pagos_mayores = 0;
                    $islr_sustraendo = 0;
             }


            $islrconcepts = IslrConcept::on(Auth::user()->database_name)->orderBy('id','asc')->get();


            $company = Company::on(Auth::user()->database_name)->find(1);
            $igtfporc = $company->IGTF_porc ?? 3;
            $impuesto = $company->tax_1 ?? 1;
            $impuesto2 = $company->tax_2 ?? 1;
            $impuesto3 = $company->tax_3 ?? 1;

             return view('admin.expensesandpurchases.create_payment_after',compact('coin','expense','datenow'
                                ,'expense_details','accounts_bank', 'accounts_efectivo'
                                ,'accounts_punto_de_venta','anticipos_sum'
                                ,'total_retiene_iva','total_retiene_islr','bcv','provider'
                                ,'islrconcepts','debitnoteexpense','igtfporc','impuesto','impuesto2','impuesto3','islr_pagos_mayores','islr_sustraendo'));

                            }else{
                                return redirect('/expensesandpurchases')->withDanger('No Tiene Permiso');
                            }

    }


    public function selectprovider(request $request)
    {

        if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){
            $providers     = Provider::on(Auth::user()->database_name)->get();

            return view('admin.expensesandpurchases.selectprovider',compact('providers'));
        }else{
            return redirect('/expensesandpurchases')->withDanger('No Tiene Permiso!');

        }
    }

    public function selectproviderexpense(Request $request,$id)
    {
            $providers = Provider::on(Auth::user()->database_name)->get();

            $coin = $request->coin_hidde;
            $id_expense = $id;

            return view('admin.expensesandpurchases.selectproviderexpense',compact('providers','id_expense','coin'));
    }

    public function updateproviderexpense($id_expense,$id_provider,$coin)
    {
        $var = ExpensesAndPurchase::on(Auth::user()->database_name)->findOrFail($id_expense);

        $var->id_provider = $id_provider;

        $var->save();

        return redirect('/expensesandpurchases/register/'.$id_expense.'/'.$coin.'')->withSuccess('Proveedor Actualizado Con Exito !!');

    }

    public function selectinventary($id_expense,$coin,$type,$account = null ,$subaccount = null)
    {
        if($type == 'mercancia' || $type == 'MERCANCIA'){
            $type = 'MERCANCIA';
        }

        if($type == 'servicio' || $type == 'SERVICIO'){
            $type = 'SERVICIO';
        }

        if($type == 'materiap' || $type == 'MATERIAP'){
            $type = 'MATERIAP';
        }



        $user       =   auth()->user();
        $users_role =   $user->role_id;

            $global = new GlobalController();




            $inventories = Product::on(Auth::user()->database_name)

            ->where(function ($query){
                $query->where('type','MERCANCIA')
                    ->orWhere('type','COMBO')
                    ->orWhere('type','MATERIAP')
                    ->orWhere('type','SERVICIO');
            })


            ->where('products.status',1)
            ->select('products.id as id_inventory','products.*')
            ->get();

            foreach ($inventories as $inventorie) {

                $inventories_quotations = DB::connection(Auth::user()->database_name)
                ->table('inventory_histories')
                ->where('id_product','=',$inventorie->id_inventory)
                ->select('amount_real')
                ->get()->last();

                if($inventories_quotations){
                    $inventorie->amount = $inventories_quotations->amount_real;
                }else{

                    $inventorie->amount = 11;
                }



            }


            return view('admin.expensesandpurchases.selectinventary',compact('type','coin','inventories','id_expense','account','subaccount'));
    }



    public function store(Request $request)
    {
        if(Auth::user()->role_id == '1' || $request->get('agregarmiddleware') == '1'){
          //validador de formulario
            $rules = [
                'id_provider' => 'required'
               // 'invoice' => 'required',
            ];
            $messages = [
                'id_provider.required' => 'Seleccione un Proveedor.',
              //  'invoice.required' => 'Ingrese Numero de factura.',

            ];
            $this->validate($request, $rules, $messages);


        $idprovider = request('id_provider');
        $invoice = request('invoice');

        $validar = ExpensesAndPurchase::on(Auth::user()->database_name)
                    ->where('id_provider',$idprovider)
                    ->where('invoice',$invoice)
                    ->wherein('status',['C','P','1'])
                    ->get();


        if(!isset($idprovider)){
            return redirect('expensesandpurchases/registerexpense')->withDelete('Debe seleccionar un proveedor!');
       /* }
        elseif(!isset($invoice)){
            return redirect('expensesandpurchases/registerexpense/'.request('id_provider'))->withDelete('Debe Registrar Factura de Compra!');*/
        } elseif($validar->count() > 0){
            return redirect('expensesandpurchases/registerexpense/'.request('id_provider'))->withDelete('El Proveedor ya posee una factura con el numero '.$invoice);
        }else{

            $var = new ExpensesAndPurchase();
            $var->setConnection(Auth::user()->database_name);
            $var->id_provider = request('id_provider');
            $var->invoice = request('invoice');
            $var->id_branch = 1;
            $var->id_user = request('id_user');
            $var->serie = request('serie');
            $var->observation = request('observation');
            $var->date = request('date-begin');
            $var->dateregistro = request('date-registro');
            $var->coin = 'bolivares';

            $company = Company::on(Auth::user()->database_name)->find(1);
            $global = new GlobalController();

            //Si la taza es automatica
            if($company->tiporate_id == 1){
                $bcv = $global->search_bcv();
            }else{
                //si la tasa es fija
                $bcv = $company->rate;
            }

            $var->rate = $bcv;
            $var->status =  "1";
            $var->save();

            $historial_expense = new HistorialExpenseController();
            $historial_expense->registerAction($var,"expense","Creó la Compra");

            return redirect('expensesandpurchases/register/'.$var->id.'/bolivares')->withSuccess('Gasto o Compra Resgistrada Correctamente!');

        }




    }else{
        return redirect('/expensesandpurchases')->withDanger('No Tiene Permiso!');

    }
    }


    public function store_detail(Request $request)
    {


        $rules = [
            'id_expense'    =>'required',
            'id_user'  =>'required',
            'amount'        =>'required',
            'description'   =>'required',
            'price'         =>'required',
            'Account'       =>'required'
        ];
        $messages = [
            'id_provider.required' => 'Seleccione un Proveedor.',
            'invoice.required' => 'Ingrese Numero de factura.',

        ];
        $this->validate($request, $rules, $messages);


        $var = new ExpensesDetail();
        $var->setConnection(Auth::user()->database_name);

        $var->id_expense = request('id_expense');
        $var->rate = request('rate_expense');
        $coin = request('coin_hidde');
        $discount = request('discount');

        $var->id_user = request('id_user');
        $var->id_account = request('Account');
        $var->porc_discount = $discount;
        $sin_formato_amount = request('amount');

        $var->amount = $sin_formato_amount;

        $var->description = request('description');

        $sin_formato_price = request('price');

        if($coin != 'bolivares'){
            $sin_formato_price = ($sin_formato_price) * $var->rate;
        }else{
            $sin_formato_price = $sin_formato_price;
        }

        $var->price = $sin_formato_price;


        $branch = request('centro_costo');


        if(isset($branch) or $branch != NULL){
            $centro_costo = $branch;
        } else {
            $centro_costo = 1;
        }

        $var->id_branch = $centro_costo;


        $percentage = (($sin_formato_price * $sin_formato_amount) * $discount)/100;

        $total_less_percentage = ($sin_formato_price *  $sin_formato_amount) - $percentage;

        $var->discount = $total_less_percentage;

        $exento = request('exento');
        if($exento == null){
            $var->exento = false;
        }else{
            $var->exento = true;
        }

        $islr = request('islr');
        if($islr == null){
            $var->islr = false;
        }else{
            $var->islr = true;
        }

        $id_inventory = request('id_inventory');

        if($id_inventory != -1){
            $var->id_inventory = $id_inventory;
        }

        $var->status =  1;

        $var->save();

        $validation = new ExpenseDetailValidationController();

        if($var->expenses['status'] == 'P'){
            $validation->calculateExpenseModify($var->id_expense);

            $date = Carbon::now();
            $date = $date->format('Y-m-d');

            $expense_detail = ExpensesDetail::on(Auth::user()->database_name)->where('id_expense',$var->id_expense)->get();

            if(isset($expense_detail)){

               foreach($expense_detail as $var){

                    if(isset($var->id_inventory)){

                        $product = Product::on(Auth::user()->database_name)->find($var->id_inventory);

                        if(isset($product)){

                            if(($product->type == 'MERCANCIA') || ($product->type == 'COMBO') || ($product->type == 'MATERIAP')){

                                $global = new GlobalController;
                                $global->transaction_inv('compra',$var->id_inventory,'compra_n',$var->amount,$var->price,$date,$var->id_branch,$var->id_branch,0,$var->id_inventory_histories,$var->id,0,$var->id_expense);

                            }

                        }
                    }
               }

            }

        }

        $historial_expense = new HistorialExpenseController();

        $historial_expense->registerAction($var,"expense_product","Registró un Producto o Servicio");

        return redirect('expensesandpurchases/register/'.$var->id_expense.'/'.$coin.'')->withSuccess('Agregado Exitosamente!');
    }


    public function store_expense_payment(Request $request)
    {

        /************PARA LO DE COURIERTOOL NO TOCAR ********/
        $montocour = str_replace(',', '.', str_replace('.', '', request('grandtotal_form')));
        /***************************************************************/
        $igftmonto = request('igtfvalor');
        $IGTF_porc = request('IGTF_porc');
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');

        $total_pay = 0;

        //Saber cuantos pagos vienen
        $come_pay = request('amount_of_payments');
        $global = new GlobalController();
        $user_id = request('user_id');

        /*Validar cuales son los pagos a guardar */
            $validate_boolean1 = false;
            $validate_boolean2 = false;
            $validate_boolean3 = false;
            $validate_boolean4 = false;
            $validate_boolean5 = false;
            $validate_boolean6 = false;
            $validate_boolean7 = false;

        //-----------------------
        $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->findOrFail(request('id_expense'));

        $bcv = $expense->rate;
        $coin = request('coin');

        $anticipo = request('anticipo_form');
        $retencion_iva = request('total_retiene_iva');
        $retencion_islr = request('total_retiene_islr');

        $sub_total = request('sub_total_form');
        $base_imponible = request('base_imponible_form');
        $sin_formato_amount = request('sub_total_form');
        $iva_percentage = request('iva_form');

        //$sin_formato_total_pay = request('total_pay_form');
        //$total_pay_form = request('total_pay_form');

        $sin_formato_total_pay = str_replace(',', '.', str_replace('.', '', request('total_pay_form')));
        $total_pay_form = str_replace(',', '.', str_replace('.', '', request('total_pay_form')));
        $porc_descuento = request('porc_descuento_form');
        $descuento = request('descuento_form');
        $date_payment = request('date_payment_form');
        $date_payment_expense = request('date_payment_expense');
        $sin_formato_grandtotal = str_replace(',', '.', str_replace('.', '', request('grandtotal_form')));
        $sin_formato_amount_iva = str_replace(',', '.', str_replace('.', '', request('iva_amount_form')));

        $total_iva = 0;

        if($base_imponible != 0){
            $total_iva = ($base_imponible * $iva_percentage)/100;

        }

        //Verifica el status del pago, si esta en C significa Cobrado y por tanto no se debe cobrar de nuevo
        if($expense->status != "C"){
            //si el monto es menor o igual a cero, quiere decir que el anticipo cubre el total de la factura, por tanto no hay pagos
            if($sin_formato_total_pay > 0)
            {
                $payment_type = request('payment_type');
                if($come_pay >= 1){

                    /*-------------PAGO NUMERO 1----------------------*/
                    $var = new ExpensePayment();
                    $var->setConnection(Auth::user()->database_name);

                    $amount_pay = request('amount_pay');

                    if(isset($amount_pay)){

                        $valor_sin_formato_amount_pay = str_replace(',', '.', str_replace('.', '', $amount_pay));
                    }else{
                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar un monto de pago 1!');
                    }

                    $account_bank = request('account_bank');
                    $account_efectivo = request('account_efectivo');
                    $account_punto_de_venta = request('account_punto_de_venta');
                    $credit_days = request('credit_days');
                    $reference = request('reference');

                    if($valor_sin_formato_amount_pay != 0){

                        if($payment_type != 0){

                            $var->id_expense = request('id_expense');

                            //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                            if($payment_type == 1 || $payment_type == 11 || $payment_type == 5 ){
                                //CUENTAS BANCARIAS
                                if(($account_bank != 0)){
                                    if(isset($reference)){

                                        $var->id_account = $account_bank;

                                        $var->reference = $reference;

                                    }else{
                                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar una Referencia Bancaria!');
                                    }
                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria!');
                                }
                            }if($payment_type == 2){

                                $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first();

                                $var->id_account = $account_contado->id;
                            }
                            if($payment_type == 4){
                                //DIAS DE CREDITO
                                if(isset($credit_days)){

                                    $var->credit_days = $credit_days;

                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar los Dias de Credito!');
                                }
                            }

                            if($payment_type == 6){
                                //DIAS DE CREDITO
                                if(($account_efectivo != 0)){

                                    $var->id_account = $account_efectivo;

                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo!');
                                }
                            }

                            if($payment_type == 9 || $payment_type == 10){
                                //CUENTAS PUNTO DE VENTA
                                if(($account_punto_de_venta != 0)){
                                    $var->id_account = $account_punto_de_venta;
                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta!');
                                }
                            }

                                $var->payment_type = request('payment_type');
                                $var->amount = $valor_sin_formato_amount_pay;

                                if($coin != 'bolivares'){
                                    $var->amount = $var->amount * $bcv;
                                }

                                $var->status =  1;
                                $total_pay += $valor_sin_formato_amount_pay;
                                $validate_boolean1 = true;

                        }else{
                            return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar un Tipo de Pago 1!');
                        }

                    }else{
                            return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('El pago debe ser distinto de Cero!');
                        }
                    /*--------------------------------------------*/
                }
                $payment_type2 = request('payment_type2');
                if($come_pay >= 2){

                    /*-------------PAGO NUMERO 2----------------------*/
                    $var2 = new ExpensePayment();
                    $var2->setConnection(Auth::user()->database_name);
                    $amount_pay2 = request('amount_pay2');

                    if(isset($amount_pay2)){
                        $valor_sin_formato_amount_pay2 = str_replace(',', '.', str_replace('.', '', $amount_pay2));
                    }else{
                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar un monto de pago 2!');
                    }


                    $account_bank2 = request('account_bank2');
                    $account_efectivo2 = request('account_efectivo2');
                    $account_punto_de_venta2 = request('account_punto_de_venta2');
                    $credit_days2 = request('credit_days2');
                    $reference2 = request('reference2');

                    if($valor_sin_formato_amount_pay2 != 0){

                    if($payment_type2 != 0){

                        $var2->id_expense = request('id_expense');

                        //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                        if($payment_type2 == 1 || $payment_type2 == 11 || $payment_type2 == 5 ){
                            //CUENTAS BANCARIAS
                            if(($account_bank2 != 0)){
                                if(isset($reference2)){

                                    $var2->id_account = $account_bank2;

                                    $var2->reference = $reference2;

                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 2!');
                                }
                            }else{
                                return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 2!');
                            }
                        }if($payment_type2 == 2){

                            $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first();

                            $var2->id_account = $account_contado->id;
                        }
                        if($payment_type2 == 4){
                            //DIAS DE CREDITO
                            if(isset($credit_days2)){

                                $var2->credit_days = $credit_days2;

                            }else{
                                return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 2!');
                            }
                        }

                        if($payment_type2 == 6){
                            //DIAS DE CREDITO
                            if(($account_efectivo2 != 0)){

                                $var2->id_account = $account_efectivo2;

                            }else{
                                return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 2!');
                            }
                        }

                        if($payment_type2 == 9 || $payment_type2 == 10){
                                //CUENTAS PUNTO DE VENTA
                            if(($account_punto_de_venta2 != 0)){
                                $var2->id_account = $account_punto_de_venta2;
                            }else{
                                return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 2!');
                            }
                        }
                            $var2->payment_type = request('payment_type2');
                            $var2->amount = $valor_sin_formato_amount_pay2;

                            if($coin != 'bolivares'){
                                $var2->amount = $var2->amount * $bcv;
                            }

                            $var2->status =  1;
                            $total_pay += $valor_sin_formato_amount_pay2;
                            $validate_boolean2 = true;
                    }else{
                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar un Tipo de Pago 2!');
                    }


                    }else{
                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('El pago 2 debe ser distinto de Cero!');
                    }
                    /*--------------------------------------------*/
                }
                $payment_type3 = request('payment_type3');
                if($come_pay >= 3){

                        /*-------------PAGO NUMERO 3----------------------*/
                        $var3 = new ExpensePayment();
                        $var3->setConnection(Auth::user()->database_name);

                        $amount_pay3 = request('amount_pay3');

                        if(isset($amount_pay3)){

                            $valor_sin_formato_amount_pay3 = str_replace(',', '.', str_replace('.', '', $amount_pay3));
                        }else{
                            return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar un monto de pago 3!');
                        }

                        $account_bank3 = request('account_bank3');
                        $account_efectivo3 = request('account_efectivo3');
                        $account_punto_de_venta3 = request('account_punto_de_venta3');
                        $credit_days3 = request('credit_days3');
                        $reference3 = request('reference3');

                        if($valor_sin_formato_amount_pay3 != 0){

                            if($payment_type3 != 0){

                                $var3->id_expense = request('id_expense');

                                //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                                if($payment_type3 == 1 || $payment_type3 == 11 || $payment_type3 == 5 ){
                                    //CUENTAS BANCARIAS
                                    if(($account_bank3 != 0)){
                                        if(isset($reference3)){

                                            $var3->id_account = $account_bank3;

                                            $var3->reference = $reference3;

                                        }else{
                                            return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 3!');
                                        }
                                    }else{
                                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 3!');
                                    }
                                }if($payment_type3 == 2){

                                    $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first();

                                    $var3->id_account = $account_contado->id;
                                }
                                if($payment_type3 == 4){
                                    //DIAS DE CREDITO
                                    if(isset($credit_days3)){

                                        $var3->credit_days = $credit_days3;

                                    }else{
                                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 3!');
                                    }
                                }

                                if($payment_type3 == 6){
                                    //DIAS DE CREDITO
                                    if(($account_efectivo3 != 0)){

                                        $var3->id_account = $account_efectivo3;

                                    }else{
                                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 3!');
                                    }
                                }

                                if($payment_type3 == 9 || $payment_type3 == 10){
                                    //CUENTAS PUNTO DE VENTA
                                    if(($account_punto_de_venta3 != 0)){
                                        $var3->id_account = $account_punto_de_venta3;
                                    }else{
                                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 3!');
                                    }
                                }

                                    $var3->payment_type = request('payment_type3');
                                    $var3->amount = $valor_sin_formato_amount_pay3;

                                    if($coin != 'bolivares'){
                                        $var3->amount = $var3->amount * $bcv;
                                    }
                                    $var3->status =  1;
                                    $total_pay += $valor_sin_formato_amount_pay3;
                                    $validate_boolean3 = true;


                            }else{
                                return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar un Tipo de Pago 3!');
                            }


                        }else{
                                return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('El pago 3 debe ser distinto de Cero!');
                            }
                        /*--------------------------------------------*/
                }
                $payment_type4 = request('payment_type4');
                if($come_pay >= 4){

                        /*-------------PAGO NUMERO 4----------------------*/
                        $var4 = new expensePayment();
                        $var4->setConnection(Auth::user()->database_name);
                        $amount_pay4 = request('amount_pay4');

                        if(isset($amount_pay4)){

                            $valor_sin_formato_amount_pay4 = str_replace(',', '.', str_replace('.', '', $amount_pay4));
                        }else{
                            return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar un monto de pago 4!');
                        }

                        $account_bank4 = request('account_bank4');
                        $account_efectivo4 = request('account_efectivo4');
                        $account_punto_de_venta4 = request('account_punto_de_venta4');
                        $credit_days4 = request('credit_days4');
                        $reference4 = request('reference4');

                        if($valor_sin_formato_amount_pay4 != 0){

                            if($payment_type4 != 0){

                                $var4->id_expense = request('id_expense');

                                //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                                if($payment_type4 == 1 || $payment_type4 == 11 || $payment_type4 == 5 ){
                                    //CUENTAS BANCARIAS
                                    if(($account_bank4 != 0)){
                                        if(isset($reference4)){

                                            $var4->id_account = $account_bank4;

                                            $var4->reference = $reference4;

                                        }else{
                                            return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 4!');
                                        }
                                    }else{
                                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 4!');
                                    }
                                }if($payment_type4 == 2){

                                    $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first();

                                    $var4->id_account = $account_contado->id;
                                }
                                if($payment_type4 == 4){
                                    //DIAS DE CREDITO
                                    if(isset($credit_days4)){

                                        $var4->credit_days = $credit_days4;

                                    }else{
                                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 4!');
                                    }
                                }

                                if($payment_type4 == 6){
                                    //DIAS DE CREDITO
                                    if(($account_efectivo4 != 0)){

                                        $var4->id_account = $account_efectivo4;

                                    }else{
                                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 4!');
                                    }
                                }

                                if($payment_type4 == 9 || $payment_type4 == 10){
                                    //CUENTAS PUNTO DE VENTA
                                    if(($account_punto_de_venta4 != 0)){
                                        $var4->id_account = $account_punto_de_venta4;
                                    }else{
                                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 4!');
                                    }
                                }

                                    $var4->payment_type = request('payment_type4');
                                    $var4->amount = $valor_sin_formato_amount_pay4;

                                    if($coin != 'bolivares'){
                                        $var4->amount = $var4->amount * $bcv;
                                    }
                                    $var4->status =  1;
                                    $total_pay += $valor_sin_formato_amount_pay4;
                                    $validate_boolean4 = true;

                            }else{
                                return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar un Tipo de Pago 4!');
                            }


                        }else{
                                return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('El pago 4 debe ser distinto de Cero!');
                            }
                        /*--------------------------------------------*/
                }
                $payment_type5 = request('payment_type5');
                if($come_pay >= 5){

                    /*-------------PAGO NUMERO 5----------------------*/

                    $var5 = new expensePayment();
                    $var5->setConnection(Auth::user()->database_name);
                    $amount_pay5 = request('amount_pay5');

                    if(isset($amount_pay5)){
                        $valor_sin_formato_amount_pay5 = str_replace(',', '.', str_replace('.', '', $amount_pay5));
                    }else{
                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar un monto de pago 5!');
                    }

                    $account_bank5 = request('account_bank5');
                    $account_efectivo5 = request('account_efectivo5');
                    $account_punto_de_venta5 = request('account_punto_de_venta5');
                    $credit_days5 = request('credit_days5');
                    $reference5 = request('reference5');

                    if($valor_sin_formato_amount_pay5 != 0){

                        if($payment_type5 != 0){

                            $var5->id_expense = request('id_expense');

                            //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                            if($payment_type5 == 1 || $payment_type5 == 11 || $payment_type5 == 5 ){
                                //CUENTAS BANCARIAS
                                if(($account_bank5 != 0)){
                                    if(isset($reference5)){

                                        $var5->id_account = $account_bank5;

                                        $var5->reference = $reference5;

                                    }else{
                                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 5!');
                                    }
                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 5!');
                                }
                            }if($payment_type5 == 2){

                                $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first();

                                $var5->id_account = $account_contado->id;
                            }
                            if($payment_type5 == 4){
                                //DIAS DE CREDITO
                                if(isset($credit_days5)){

                                    $var5->credit_days = $credit_days5;

                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 5!');
                                }
                            }

                            if($payment_type5 == 6){
                                //DIAS DE CREDITO
                                if(($account_efectivo5 != 0)){

                                    $var5->id_account = $account_efectivo5;

                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 5!');
                                }
                            }

                            if($payment_type5 == 9 || $payment_type5 == 10){
                                //CUENTAS PUNTO DE VENTA
                                if(($account_punto_de_venta5 != 0)){
                                    $var5->id_account = $account_punto_de_venta5;
                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 5!');
                                }
                            }

                                $var5->payment_type = request('payment_type5');
                                $var5->amount = $valor_sin_formato_amount_pay5;

                                if($coin != 'bolivares'){
                                    $var5->amount = $var5->amount * $bcv;
                                }

                                $var5->status =  1;
                                $total_pay += $valor_sin_formato_amount_pay5;
                                $validate_boolean5 = true;

                        }else{
                            return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar un Tipo de Pago 5!');
                        }


                    }else{
                            return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('El pago 5 debe ser distinto de Cero!');
                        }
                    /*--------------------------------------------*/
                }
                $payment_type6 = request('payment_type6');
                if($come_pay >= 6){

                    /*-------------PAGO NUMERO 6----------------------*/
                    $var6 = new expensePayment();
                    $var6->setConnection(Auth::user()->database_name);
                    $amount_pay6 = request('amount_pay6');

                    if(isset($amount_pay6)){
                        $valor_sin_formato_amount_pay6 = str_replace(',', '.', str_replace('.', '', $amount_pay6));
                    }else{
                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar un monto de pago 6!');
                    }

                    $account_bank6 = request('account_bank6');
                    $account_efectivo6 = request('account_efectivo6');
                    $account_punto_de_venta6 = request('account_punto_de_venta6');
                    $credit_days6 = request('credit_days6');
                    $reference6 = request('reference6');

                    if($valor_sin_formato_amount_pay6 != 0){

                        if($payment_type6 != 0){

                            $var6->id_expense = request('id_expense');

                            //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                            if($payment_type6 == 1 || $payment_type6 == 11 || $payment_type6 == 5 ){
                                //CUENTAS BANCARIAS
                                if(($account_bank6 != 0)){
                                    if(isset($reference6)){

                                        $var6->id_account = $account_bank6;

                                        $var6->reference = $reference6;

                                    }else{
                                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 6!');
                                    }
                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 6!');
                                }
                            }if($payment_type6 == 2){

                                $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first();

                                $var6->id_account = $account_contado->id;
                            }
                            if($payment_type6 == 4){
                                //DIAS DE CREDITO
                                if(isset($credit_days6)){

                                    $var6->credit_days = $credit_days6;

                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 6!');
                                }
                            }

                            if($payment_type6 == 6){
                                //DIAS DE CREDITO
                                if(($account_efectivo6 != 0)){

                                    $var6->id_account = $account_efectivo6;

                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 6!');
                                }
                            }

                            if($payment_type6 == 9 || $payment_type6 == 10){
                                //CUENTAS PUNTO DE VENTA
                                if(($account_punto_de_venta6 != 0)){
                                    $var6->id_account = $account_punto_de_venta6;
                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 6!');
                                }
                            }

                                $var6->payment_type = request('payment_type6');
                                $var6->amount = $valor_sin_formato_amount_pay6;

                                if($coin != 'bolivares'){
                                    $var6->amount = $var6->amount * $bcv;
                                }

                                $var6->status =  1;
                                $total_pay += $valor_sin_formato_amount_pay6;
                                $validate_boolean6 = true;


                        }else{
                            return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar un Tipo de Pago 6!');
                        }


                    }else{
                            return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('El pago 6 debe ser distinto de Cero!');
                        }
                    /*--------------------------------------------*/
                }
                $payment_type7 = request('payment_type7');
                if($come_pay >= 7){

                    /*-------------PAGO NUMERO 7----------------------*/

                    $var7 = new expensePayment();
                    $var7->setConnection(Auth::user()->database_name);

                    $amount_pay7 = request('amount_pay7');

                    if(isset($amount_pay7)){

                        $valor_sin_formato_amount_pay7 = str_replace(',', '.', str_replace('.', '', $amount_pay7));
                    }else{
                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar un monto de pago 7!');
                    }


                    $account_bank7 = request('account_bank7');
                    $account_efectivo7 = request('account_efectivo7');
                    $account_punto_de_venta7 = request('account_punto_de_venta7');

                    $credit_days7 = request('credit_days7');



                    $reference7 = request('reference7');

                    if($valor_sin_formato_amount_pay7 != 0){

                        if($payment_type7 != 0){

                            $var7->id_expense = request('id_expense');

                            //SELECCIONA LA CUENTA QUE SE REGISTRA EN EL TIPO DE PAGO
                            if($payment_type7 == 1 || $payment_type7 == 11 || $payment_type7 == 5 ){
                                //CUENTAS BANCARIAS
                                if(($account_bank7 != 0)){
                                    if(isset($reference7)){

                                        $var7->id_account = $account_bank7;

                                        $var7->reference = $reference7;

                                    }else{
                                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar una Referencia Bancaria en pago numero 7!');
                                    }
                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta Bancaria en pago numero 7!');
                                }
                            }if($payment_type7 == 2){

                                $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first();

                                $var7->id_account = $account_contado->id;
                            }
                            if($payment_type7 == 4){
                                //DIAS DE CREDITO
                                if(isset($credit_days7)){

                                    $var7->credit_days = $credit_days7;

                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe ingresar los Dias de Credito en pago numero 7!');
                                }
                            }

                            if($payment_type7 == 6){
                                //DIAS DE CREDITO
                                if(($account_efectivo7 != 0)){

                                    $var7->id_account = $account_efectivo7;

                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta de Efectivo en pago numero 7!');
                                }
                            }

                            if($payment_type7 == 9 || $payment_type7 == 10){
                                //CUENTAS PUNTO DE VENTA
                                if(($account_punto_de_venta7 != 0)){
                                    $var7->id_account = $account_punto_de_venta7;
                                }else{
                                    return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar una Cuenta de Punto de Venta en pago numero 7!');
                                }
                            }

                                $var7->payment_type = request('payment_type7');
                                $var7->amount = $valor_sin_formato_amount_pay7;

                                if($coin != 'bolivares'){
                                    $var7->amount = $var7->amount * $bcv;
                                }

                                $var7->status =  1;

                                $total_pay += $valor_sin_formato_amount_pay7;

                                $validate_boolean7 = true;


                        }else{
                            return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Debe seleccionar un Tipo de Pago 7!');
                        }


                    }else{
                            return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('El pago 7 debe ser distinto de Cero!');
                        }
                    /*--------------------------------------------*/
                }
            }

            $total_pay = (string) $total_pay;

            ////////////////////////////////////COMPROBANTE DE COMPRA////////////////////////////////////////////////////////////
            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');

            if(($expense->status != 'C') && ($expense->status != 'P')){

                $header_voucher  = new HeaderVoucher();
                $header_voucher->setConnection(Auth::user()->database_name);


                $header_voucher->description = "Compras de Bienes o servicios.";
                $header_voucher->date = $date_payment ?? $datenow;


                $header_voucher->status =  "1";

                $header_voucher->save();

                $expense_details = ExpensesDetail::on(Auth::user()->database_name)->where('id_expense',$expense->id)->get();

                foreach($expense_details as $exp){ // CUENTAS USADAS EN LA FACTURA DE COMPRA por producto
                    $account = Account::on(Auth::user()->database_name)->find($exp->id_account);

                    if(isset($account)){
                        $this->add_movement($bcv,$header_voucher->id,$account->id,$expense->id,$user_id,$exp->price * $exp->amount,0);
                    }
                }

                //Credito Fiscal IVA por Pagar
                $account_credito_iva_fiscal = Account::on(Auth::user()->database_name)->where('description', 'like', 'IVA (Credito Fiscal)')->first();
                if(isset($account_credito_iva_fiscal)){
                    if($sin_formato_amount_iva != 0){
                        $this->add_movement($bcv,$header_voucher->id,$account_credito_iva_fiscal->id,$expense->id,$user_id,$sin_formato_amount_iva,0);
                    }
                }
                //Al final de agregar los movimientos de los pagos, agregamos el monto total de los pagos a cuentas por cobrar clientes
                $account_cuentas_por_pagar_proveedores = Account::on(Auth::user()->database_name)->where('description', 'like', 'Cuentas por Pagar Proveedores')->first();

                if(isset($account_cuentas_por_pagar_proveedores)){
                    $this->add_movement($bcv,$header_voucher->id,$account_cuentas_por_pagar_proveedores->id,$expense->id,$user_id,0,$sin_formato_grandtotal);
                } 

                if($descuento > 0){
                    $account_descount = Account::on(Auth::user()->database_name)->where('description', 'like', 'Descuentos en Compras')->first();

                    if(isset($account_descount)){
                        $this->add_movement($bcv,$header_voucher->id,$account_descount->id,$expense->id,$user_id,0,$descuento);
                    }
                }
            }
////////////////////////////////////FINCOMPROBANTE DE COMPRA///////////////////////////////////////////////////////
////////////////////////////////////COMPROBANTE DE PAGO////////////////////////////////////////////////////////////
            //VALIDA QUE LA SUMA MONTOS INGRESADOS SEAN IGUALES AL MONTO TOTAL DEL PAGO
            if(($total_pay - $total_pay_form) < "1" || ($sin_formato_total_pay <= "0"))
            {

                $header_voucher  = new HeaderVoucher();
                $header_voucher->setConnection(Auth::user()->database_name);
                $header_voucher->description = "Pago de Bienes o servicios.";
                $header_voucher->date = $date_payment_expense ?? $datenow;
                $header_voucher->status =  "1";
                $header_voucher->save();

                 if($coin != 'bolivares'){
                    $anticipo =  $anticipo * $bcv;
                    $retencion_iva = $retencion_iva * $bcv;
                    $retencion_islr = $retencion_islr * $bcv;
                    $sin_formato_amount_iva = $sin_formato_amount_iva * $bcv;
                    $base_imponible = $base_imponible * $bcv;
                    $sin_formato_amount = $sin_formato_amount * $bcv;
                    $sin_formato_total_pay = $sin_formato_total_pay * $bcv;
                    $sin_formato_grandtotal = $sin_formato_grandtotal * $bcv;
                    $sub_total = $sub_total * $bcv;
                    $igftmonto = $igftmonto * $bcv;

                }

                if($validate_boolean1 == true){
                    $var->save();
                    $this->add_pay_movement($bcv,$payment_type,$header_voucher->id,$var->id_account,$expense->id,$user_id,0,$var->amount);
                    $historial_expense = new HistorialExpenseController();
                    $historial_expense->registerAction($var,"expense_payment","Se registró un Pago");

                }

                if($validate_boolean2 == true){
                    $var2->save();
                    $this->add_pay_movement($bcv,$payment_type2,$header_voucher->id,$var2->id_account,$expense->id,$user_id,0,$var2->amount);
                    $historial_expense = new HistorialExpenseController();
                    $historial_expense->registerAction($var2,"expense_payment","Se registró un Pago");
                }

                if($validate_boolean3 == true){
                    $var3->save();
                    $this->add_pay_movement($bcv,$payment_type3,$header_voucher->id,$var3->id_account,$expense->id,$user_id,0,$var3->amount);
                    $historial_expense = new HistorialExpenseController();
                    $historial_expense->registerAction($var3,"expense_payment","Se registró un Pago");
                }
                if($validate_boolean4 == true){
                    $var4->save();
                    $this->add_pay_movement($bcv,$payment_type4,$header_voucher->id,$var4->id_account,$expense->id,$user_id,0,$var4->amount);
                    $historial_expense = new HistorialExpenseController();
                    $historial_expense->registerAction($var4,"expense_payment","Se registró un Pago");
                }
                if($validate_boolean5 == true){
                    $var5->save();
                    $this->add_pay_movement($bcv,$payment_type5,$header_voucher->id,$var5->id_account,$expense->id,$user_id,0,$var5->amount);
                    $historial_expense = new HistorialExpenseController();
                    $historial_expense->registerAction($var5,"expense_payment","Se registró un Pago");
                }
                if($validate_boolean6 == true){
                    $var6->save();
                    $this->add_pay_movement($bcv,$payment_type6,$header_voucher->id,$var6->id_account,$expense->id,$user_id,0,$var6->amount);
                    $historial_expense = new HistorialExpenseController();
                    $historial_expense->registerAction($var6,"expense_payment","Se registró un Pago");
                }
                if($validate_boolean7 == true){
                    $var7->save();
                    $this->add_pay_movement($bcv,$payment_type7,$header_voucher->id,$var7->id_account,$expense->id,$user_id,0,$var7->amount);
                    $historial_expense = new HistorialExpenseController();
                    $historial_expense->registerAction($var7,"expense_payment","Se registró un Pago");
                }


                /*Se agregan los movimientos de las retenciones si son diferentes a cero */

                if($retencion_iva !=0 and $expense->number_iva == NULL){

                    $account_iva_retenido = Account::on(Auth::user()->database_name)->where('description', 'like', '%IVA Retenido a Terceros%')->first();
                    if(isset($account_iva_retenido)){
                        $this->add_movement($bcv,$header_voucher->id,$account_iva_retenido->id,$expense->id,$user_id,0,$retencion_iva);
                    }
                    $last_number = ExpensesAndPurchase::on(Auth::user()->database_name)->where('number_iva','<>',NULL)->whereIn('status',['C','P'])->orderBy('number_iva','desc')->first();

                    //Asigno un numero incrementando en 1
                    if(isset($last_number)){
                        $expense->number_iva = $last_number->number_iva + 1;
                    }else{
                        $expense->number_iva = 1;
                    }
                }


                if($retencion_islr != 0 and $expense->number_islr == NULL){

                    $account_islr_pagago = Account::on(Auth::user()->database_name)->where('description', 'like', '%ISLR Retenido a Terceros%')->first();

                    if(isset($account_islr_pagago)){
                        $this->add_movement($bcv,$header_voucher->id,$account_islr_pagago->id,$expense->id,$user_id,0,$retencion_islr);
                    }
                    $last_number = ExpensesAndPurchase::on(Auth::user()->database_name)->where('number_islr','<>',NULL)->whereIn('status',['C','P'])->orderBy('number_islr','desc')->first();

                    //Asigno un numero incrementando en 1
                    if(isset($last_number)){
                        $expense->number_islr = $last_number->number_islr + 1;
                    }else{
                        $expense->number_islr = 1;
                    }

                }
                /*------------------------------- */


                if(isset($anticipo) && ($anticipo != 0)){


                    $account_anticipo_proveedor = Account::on(Auth::user()->database_name)->where('description', 'like', 'Anticipos a Proveedores Nacionales')->first();
                    if($sin_formato_total_pay < 0){
                        $global->checkAnticipoExpense($expense,$sin_formato_grandtotal);
                        $expense->anticipo =  $sin_formato_grandtotal;


                    }else{
                        $expense->anticipo =  $anticipo;
                        $global->associate_anticipos_expense($expense);

                    }

                    if(isset($account_anticipo_proveedor)){
                        $this->add_movement($bcv,$header_voucher->id,$account_anticipo_proveedor->id,$expense->id,$user_id,0,$expense->anticipo);
                        $global->add_payment_expense($expense,$account_anticipo_proveedor->id,3,$expense->anticipo,$bcv);
                    }
                }else{
                    $expense->anticipo = 0;
                }


                //Al final de agregar los movimientos de los pagos, agregamos el monto total de los pagos a cuentas por cobrar clientes
                $account_cuentas_por_pagar_proveedores = Account::on(Auth::user()->database_name)->where('description', 'like', 'Cuentas por Pagar Proveedores')->first();

                if(isset($account_cuentas_por_pagar_proveedores)){
                    $this->add_movement($bcv,$header_voucher->id,$account_cuentas_por_pagar_proveedores->id,$expense->id,$user_id,$sin_formato_grandtotal,0);
                }

               

                /*Modifica la cotizacion */
                    $expense->date_payment = $date_payment_expense ?? $datenow;

                    $expense->iva_percentage = $iva_percentage;



                    $id_islr_concept = request('id_islr_concept');

                    if(isset($id_islr_concept) && ($id_islr_concept > 0)){
                        $expense->id_islr_concept = $id_islr_concept;
                    }

                    $expense->base_imponible = $base_imponible;
                    $expense->amount =  $sin_formato_amount;
                    $expense->amount_iva =  $sin_formato_amount_iva;
                    $expense->amount_with_iva =  $sin_formato_grandtotal;
                    $iva_percentage = $iva_percentage;
                    $expense->porc_discount = $porc_descuento;
                    $expense->discount = $descuento;
                    $expense->IGTF_percentage = $IGTF_porc;
                    $expense->IGTF_amount = $igftmonto;
                    $expense->status = "C";

                    $expense->coin = $coin;

                    $expense->retencion_iva = $retencion_iva;
                    $expense->retencion_islr = $retencion_islr;

                    $expense->save();


                    //aumentamos el inventario
                    $retorno = $this->increase_inventory($expense->id,$expense->date);


                    if($retorno != "exito"){
                        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'');
                    }


                    $debinot = DebitNoteExpense::on(Auth::user()->database_name)
                    ->where('id_expense',$expense->id)
                    ->get();

                    if($debinot->count() > 0){

                        foreach($debinot as $debinot){
                            $arreglo[] = ['iddebit' => $debinot->id];
                        }

                        DebitNoteDetailExpense::on(Auth::user()->database_name)
                            ->WhereIn('id_debit_note_expenses',$arreglo)
                            ->update(['status' => 'X']);
                    DebitNoteExpense::on(Auth::user()->database_name)
                            ->where('id_expense',$expense->id)
                        ->update(['status' => 'X']);
                    }




        /////////////////////////////**************LO DE COURIERTOOL**************/////////////////
        if($request->court != null AND  $request->tifac != null AND $request->nrofactcou != null AND Auth::user()->company['id'] == '26'){

            $factcour  = new FacturasCour();
            $factcour->setConnection(Auth::user()->database_name);
            $factcour->id_expense = $expense->id;
            $factcour->tipo_fac = $request->tifac;
            $factcour->tipo_movimiento = $request->court;
            $factcour->numero =  $request->nrofactcou;
            $factcour->monto =  $montocour;
            $factcour->save();

        }
    /////////////////////////////**************LO DE COURIERTOOL**************/////////////////

        //Aqui pasa los quotation_products a status C de Cobrado
        DB::connection(Auth::user()->database_name)->table('expenses_details')
        ->where('id_expense', '=', $expense->id)
        ->update(['status' => 'C']);

        $global = new GlobalController;
        $global->procesar_anticipos_expense($expense,$sin_formato_total_pay);


    }else{
        return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('La suma de los pagos es diferente al monto Total a Pagar!');
    }

            $historial_expense = new HistorialExpenseController();

            $historial_expense->registerAction($expense,"expense","Se pagó la Compra");

            return redirect('expensesandpurchases/expensevoucher/'.$expense->id.'/'.$coin.'')->withSuccess('Factura Guardada con Exito!');

        }else{
            return redirect('expensesandpurchases/registerpaymentafter/'.$expense->id.'/'.$coin.'')->withDanger('Este pago ya ha sido realizado!');
        }

    }


    public function store_expense_credit(Request $request)
    {

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');

        $igftmonto = request('igtfvalor');
        $IGTF_porc = request('IGTF_porc');
        $valida_com = request('valida_com');

        $sin_formato_amount = str_replace(',', '.', str_replace('.', '', request('total_factura')));
        $sin_formato_base_imponible = str_replace(',', '.', str_replace('.', '', request('base_imponible')));
        $sin_formato_amount_iva = str_replace(',', '.', str_replace('.', '', request('iva_amount')));
        $sin_formato_amount_with_iva = str_replace(',', '.', str_replace('.', '', request('grand_total')));

        $retencion_iva_check = request('retencion_iva_check');
        $porc_descuento_general = request('porc_descuento_general');
        $sin_formato_descuento_general = str_replace(',', '.', str_replace('.', '', request('descuento_general')));

        if(isset($retencion_iva_check)){
            $sin_formato_iva_retencion = str_replace(',', '.', str_replace('.', '', request('iva_retencion')));
        }else{
            $sin_formato_iva_retencion = 0;
        }

        $retencion_islr_check = request('retencion_islr_check');

        if(isset($retencion_islr_check)){
            $sin_formato_islr_retencion = str_replace(',', '.', str_replace('.', '', request('islr_retencion')));
        }else{
            $sin_formato_islr_retencion = 0;
        }

        $sin_formato_anticipo = str_replace(',', '.', str_replace('.', '', request('anticipo')));
        $sin_formato_total_pay = str_replace(',', '.', str_replace('.', '', request('total_pay')));

        /************PARA LO DE COURIERTOOL NO TOCAR ********/
        $montocour = str_replace(',', '.', str_replace('.', '', request('total_pay')));
        /***************************************************************/

        $id_expense = request('id_expense');
        $user_id = request('user_id');

        $coin = request('coin');

        $date_payment = request('date_payment');

        $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->findOrFail($id_expense);

        if($coin != 'bolivares'){
            $sin_formato_amount_iva = $sin_formato_amount_iva * $expense->rate;
            $sin_formato_amount_with_iva = $sin_formato_amount_with_iva * $expense->rate;
            $sin_formato_base_imponible = $sin_formato_base_imponible * $expense->rate;
            $sin_formato_amount = $sin_formato_amount * $expense->rate;

            $sin_formato_iva_retencion = $sin_formato_iva_retencion * $expense->rate;
            $sin_formato_islr_retencion = $sin_formato_islr_retencion * $expense->rate;
            $sin_formato_anticipo = $sin_formato_anticipo * $expense->rate;
            $sin_formato_total_pay = $sin_formato_total_pay * $expense->rate;
            $sin_formato_descuento_general = $sin_formato_descuento_general * $expense->rate;


            $igftmonto = $igftmonto * $expense->rate;
        }

        $id_islr_concept = request('id_islr_concept_credit');

        if(isset($id_islr_concept) && ($id_islr_concept > 0)){
            $expense->id_islr_concept = $id_islr_concept;
        }

        $expense->base_imponible = $sin_formato_base_imponible;
        $expense->amount =  $sin_formato_amount;
        $expense->amount_iva =  $sin_formato_amount_iva;
        $expense->amount_with_iva =  $sin_formato_total_pay;

        $expense->retencion_iva =  $sin_formato_iva_retencion;
        $expense->retencion_islr =  $sin_formato_islr_retencion;
        $expense->anticipo =  $sin_formato_anticipo;
        $expense->porc_discount =$porc_descuento_general;
        $expense->discount = $sin_formato_descuento_general;

        $expense->IGTF_percentage = $IGTF_porc;
        $expense->IGTF_amount = $igftmonto;

        $credit = request('credit');

        $expense->iva_percentage = request('iva');

        $expense->credit_days = $credit;

        $expense->status = "P";



         //preparando para guardar historial
        $expense_detail = ExpensesDetail::on(Auth::user()->database_name)->where('id_expense',$id_expense)->get();

        /////////////////////////////**************LO DE COURIERTOOL**************/////////////////
        if($request->court != null AND  $request->tifac != null AND $request->nrofactcou != null AND Auth::user()->company['id'] == '26'){

            $factcour  = new FacturasCour();
            $factcour->setConnection(Auth::user()->database_name);
            $factcour->id_expense = $id_expense;
            $factcour->tipo_fac = $request->tifac;
            $factcour->tipo_movimiento = $request->court;
            $factcour->numero =  $request->nrofactcou;
            $factcour->monto =  $montocour;
            $factcour->save();

        }
    /////////////////////////////**************LO DE COURIERTOOL**************/////////////////



        if(isset($expense_detail)){

           foreach($expense_detail as $var){

                if(isset($var->id_inventory)){

                    $product = Product::on(Auth::user()->database_name)->find($var->id_inventory);

                    if(isset($product)){

                        if(($product->type == 'MERCANCIA') || ($product->type == 'COMBO') || ($product->type == 'MATERIAP')){

                            $global = new GlobalController;

                            $global->transaction_inv('compra',$var->id_inventory,'compra_n',$var->amount,$var->price,$date,$var->id_branch,$var->id_branch,0,$var->id_inventory_histories,$var->id,0,$var->id_expense);

                        }

                    }else{
                        return redirect('expensesandpurchases/registerpaymentafter/'.$id_expense.'')->withDanger('El Inventario no existe!');
                    }
                }
           }


        }

        $header_voucher  = new HeaderVoucher();
        $header_voucher->setConnection(Auth::user()->database_name);


        $header_voucher->description = "Compras de Bienes o servicios.";
        $header_voucher->date = $date_payment ?? $datenow;


        $header_voucher->status =  "1";

        $header_voucher->save();

        if ($valida_com == 1){

            if($sin_formato_iva_retencion > 0){

                $account_iva_retenido = Account::on(Auth::user()->database_name)->where('description', 'like', 'IVA Retenido a Terceros')->first();
                if(isset($account_iva_retenido)){
                    $this->add_movement($expense->rate,$header_voucher->id,$account_iva_retenido->id,$expense->id,$user_id,0,$sin_formato_iva_retencion);
                }
                $last_number = ExpensesAndPurchase::on(Auth::user()->database_name)->where('number_iva','<>',NULL)->whereIn('status',['C','P'])->orderBy('number_iva','desc')->first();
                //Asigno un numero incrementando en 1
                if(isset($last_number)){
                    $expense->number_iva = $last_number->number_iva + 1;
                }else{
                    $expense->number_iva = 1;
                }
            } else {
                $sin_formato_iva_retencion = 0;
            }


            if($sin_formato_islr_retencion > 0){

                
                $account_islr_pagago = Account::on(Auth::user()->database_name)->where('description', 'like', '%ISLR Retenido a Terceros%')->first();
                if(isset($account_islr_pagago)){
                    $this->add_movement($expense->rate,$header_voucher->id,$account_islr_pagago->id,$expense->id,$user_id,0,$sin_formato_islr_retencion);
                }
                $last_number = ExpensesAndPurchase::on(Auth::user()->database_name)->where('number_islr','<>',NULL)->whereIn('status',['C','P'])->orderBy('number_islr','desc')->first();

                //Asigno un numero incrementando en 1
                if(isset($last_number)){
                    $expense->number_islr = $last_number->number_islr + 1;

                }else{
                    $expense->number_islr = 1;
                }

            } else {
                $sin_formato_islr_retencion = 0;
            }

            $expense->date_payment = $expense->date;

            $sin_formato_amount_with_iva = $sin_formato_amount_with_iva - ($sin_formato_iva_retencion + $sin_formato_islr_retencion);
        }

        $expense->save();






        $expense_details = ExpensesDetail::on(Auth::user()->database_name)->where('id_expense',$expense->id)->get();

        foreach($expense_details as $var){
            $account = Account::on(Auth::user()->database_name)->find($var->id_account);

            if(isset($account)){
                $this->add_movement($expense->rate,$header_voucher->id,$account->id,$expense->id,$user_id,$var->price * $var->amount,0);
            }
        }

        //IVA credito Fiscal

        $account_credito_iva_fiscal = Account::on(Auth::user()->database_name)->where('description', 'like', 'IVA (Credito Fiscal)')->first();

        if(isset($account_credito_iva_fiscal)){
            if($sin_formato_amount_iva != 0){
                $this->add_movement($expense->rate,$header_voucher->id,$account_credito_iva_fiscal->id,$expense->id,$user_id,$sin_formato_amount_iva,0);
            }
        }

        //Al final de agregar los movimientos de los pagos, agregamos el monto total de los pagos a cuentas por cobrar clientes
        $account_cuentas_por_pagar_proveedores = Account::on(Auth::user()->database_name)->where('description', 'like', 'Cuentas por Pagar Proveedores')->first();

        if(isset($account_cuentas_por_pagar_proveedores)){
            $this->add_movement($expense->rate,$header_voucher->id,$account_cuentas_por_pagar_proveedores->id,$expense->id,$user_id,0,$sin_formato_amount_with_iva);
        }

        if($sin_formato_descuento_general > 0){
            $account_descount = Account::on(Auth::user()->database_name)->where('description', 'like', 'Descuentos en Compras')->first();

            if(isset($account_descount)){
                $this->add_movement($expense->rate,$header_voucher->id,$account_descount->id,$expense->id,$user_id,0,$sin_formato_descuento_general);
            }
        }



          /* $providers = Provider::on(Auth::user()->database_name) /// BUSCAR PROVEEDOR
        ->find($expense->id_provider);*/

        $historial_expense = new HistorialExpenseController();

        $historial_expense->registerAction($expense,"expense","Se registró la Compra a Crédito");


        return redirect('expensesandpurchases/expensevoucher/'.$expense->id.'/'.$coin.'')->withSuccess('Gasto o Compra Guardada con Exito!');
    }


    public function increase_inventory($id_expense,$date)
    {


        $expense_detail = ExpensesDetail::on(Auth::user()->database_name)->where('id_expense',$id_expense)->get();



        if(isset($expense_detail)){

           foreach($expense_detail as $var){

                if(isset($var->id_inventory)){

                    $product = Product::on(Auth::user()->database_name)->find($var->id_inventory);

                    if(isset($product)){

                         if(($product->type == 'MERCANCIA') || ($product->type == 'COMBO') || ($product->type == 'MATERIAP')){

                            $global = new GlobalController;

                            $global->transaction_inv('compra',$var->id_inventory,'compra_n',$var->amount,$var->price,$date,$var->id_branch,$var->id_branch,0,$var->id_inventory_histories,$var->id,0,$var->id_expense);


                        }

                    }else{
                        return redirect('expensesandpurchases/registerpaymentafter/'.$id_expense.'')->withDanger('El Inventario no existe!');
                    }
                }
           }


        }else{
            return redirect('expensesandpurchases/registerpaymentafter/'.$id_expense.'')->withDanger('El Inventario de compra no existe!');
        }



            return "exito";


    }




    public function add_movement($bcv,$id_header,$id_account,$id_expense,$id_user,$debe,$haber)
    {

        $detail = new DetailVoucher();
        $detail->setConnection(Auth::user()->database_name);

        $detail->id_account = $id_account;
        $detail->id_header_voucher = $id_header;
        $detail->user_id = $id_user;
        $detail->tasa = $bcv;
        $detail->id_expense = $id_expense;

        $detail->debe = $debe;
        $detail->haber = $haber;


        $detail->status =  "C";

        /*Le cambiamos el status a la cuenta a M, para saber que tiene Movimientos en detailVoucher */

            $account = Account::on(Auth::user()->database_name)->findOrFail($detail->id_account);

            if($account->status != "M"){
                $account->status = "M";
                $account->save();
            }


        $detail->save();

    }


    public function add_pay_movement($bcv,$payment_type,$header_voucher,$id_account,$id_expense,$user_id,$amount_debe,$amount_haber)
    {


        //Cuentas por Cobrar Clientes

            //AGREGA EL MOVIMIENTO DE LA CUENTA CON LA QUE SE HIZO EL PAGO
            if(isset($id_account)){
                $this->add_movement($bcv,$header_voucher,$id_account,$id_expense,$user_id,$amount_debe,$amount_haber);

            }//SIN DETERMINAR
            else if($payment_type == 7){

                $account_sin_determinar = Account::on(Auth::user()->database_name)->where('description', 'like', 'Otros Egresos No Identificados')->first();

                if(isset($account_sin_determinar)){
                    $this->add_movement($bcv,$header_voucher,$account_sin_determinar->id,$id_expense,$user_id,$amount_debe,$amount_haber);
                }
            }//PAGO DE CONTADO
            else if($payment_type == 2){

                $account_contado = Account::on(Auth::user()->database_name)->where('description', 'like', 'Caja Chica')->first();

                if(isset($account_contado)){
                    $this->add_movement($bcv,$header_voucher,$account_contado->id,$id_expense,$user_id,$amount_debe,$amount_haber);
                }
            }//CONTRA ANTICIPO
            else if($payment_type == 3){

                $account_contra_anticipo = Account::on(Auth::user()->database_name)->where('description', 'like', 'Anticipos a Proveedores Nacionales')->first();

                if(isset($account_contra_anticipo)){
                    $this->add_movement($bcv,$header_voucher,$account_contra_anticipo->id,$id_expense,$user_id,$amount_debe,$amount_haber);
                }
            }


    }



    public function refreshrate($id_expense,$coin,$rate)
    {
        $sin_formato_rate = $rate;

        $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->find($id_expense);


        ExpensesDetail::on(Auth::user()->database_name)->where('id_expense',$id_expense)
                                ->update(['rate' => $sin_formato_rate]);


        ExpensesAndPurchase::on(Auth::user()->database_name)->where('id',$id_expense)
                                ->update(['rate' => $sin_formato_rate]);


        $historial_expense = new HistorialExpenseController();

        $historial_expense->registerAction($expense,"expense","Se actualizó la taza de: ".number_format($expense->rate, 2, ',', '.')." a ".$rate);


        return redirect('/expensesandpurchases/register/'.$id_expense.'/'.$coin.'')->withSuccess('Actualizacion de Tasa Exitosa!');

    }



    public function editexpensesandpurchaseproduct($id)
    {
            $expensesandpurchase_product = ExpensesAndPurchase::on(Auth::user()->database_name)->find($id);

            if(isset($expensesandpurchase_product)){

                $product= Product::on(Auth::user()->database_name)->find($expensesandpurchase_product->id_inventory);

                return view('admin.expensesandpurchases.edit_product',compact('expensesandpurchase_product','inventory'));
            }else{
                return redirect('/expensesandpurchases')->withDanger('No se Encontro el Producto!');
            }



    }
    public function editproduct(request $request,$id,$coin)
    {

        if(Auth::user()->role_id == '1' || $request->get('actualizarmiddleware') == '1'){
        $expense_detail = ExpensesDetail::on(Auth::user()->database_name)->find($id);
        $rate = null;

        if(isset($expense_detail)){

            if(isset($expense_detail->id_inventory))
            {
                $inventory = Inventory::on(Auth::user()->database_name)->find($expense_detail->id_inventory);
            }else{
                $inventory = null;
            }


            if($coin != 'bolivares'){
                $rate = $expense_detail->rate;
            }

            return view('admin.expensesandpurchases.edit_product',compact('rate','coin','expense_detail','inventory'));
        }else{
            return redirect('/expensesandpurchases')->withDanger('No se Encontro el Producto!');
        }

    }else{
        return redirect('/expensesandpurchases')->withDanger('No Tiene Permiso!');
    }

    }


    public function update(Request $request, $id)
    {

        $vars =  ExpensesAndPurchase::on(Auth::user()->database_name)->find($id);

        $vars_status = $vars->status;
        $vars_exento = $vars->exento;
        $vars_islr = $vars->islr;

        $data = request()->validate([


            'segment_id'         =>'required',
            'sub_segment_id'         =>'required',
            'unit_of_measure_id'         =>'required',


            'type'         =>'required',
            'description'         =>'required',

            'price'         =>'required',
            'price_buy'         =>'required',
            'cost_average'         =>'required',

            'money'         =>'required',

            'special_impuesto'         =>'required',
            'status'         =>'required',

        ]);

        $var = ExpensesAndPurchase::on(Auth::user()->database_name)->findOrFail($id);

        $var->segment_id = request('segment_id');
        $var->subsegment_id= request('sub_segment_id');
        $var->unit_of_measure_id = request('unit_of_measure_id');

        $var->code_comercial = request('code_comercial');
        $var->type = request('type');
        $var->description = request('description');

        $var->price = request('price');
        $var->price_buy = request('price_buy');

        $var->cost_average = request('cost_average');
        $var->photo_expensesandpurchase = request('photo_expensesandpurchase');

        $var->money = request('money');


        $var->special_impuesto = request('special_impuesto');

        if(request('exento') == null){
            $var->exento = "0";
        }else{
            $var->exento = "1";
        }
        if(request('islr') == null){
            $var->islr = "0";
        }else{
            $var->islr = "1";
        }


        if(request('status') == null){
            $var->status = $vars_status;
        }else{
            $var->status = request('status');
        }

        $var->save();

        $historial_expense = new HistorialExpenseController();

        $historial_expense->registerAction($var,"expense","Se actualizó la Compra");


        return redirect('/expensesandpurchases')->withSuccess('Actualizacion Exitosa!');
        }





        public function update_product(Request $request, $id)
        {
            if(Auth::user()->role_id == '1' || $request->get('actualizarmiddleware') == '1'){
            $data = request()->validate([

                'description'   =>'required',
                'amount'        =>'required',

                'coin'   =>'required',
                'price'   =>'required',

            ]);

            $var = ExpensesDetail::on(Auth::user()->database_name)->findOrFail($id);
            $validation = new ExpenseDetailValidationController();

            $price_old = $var->price;
            $amount_old = $var->amount;

            $coin = request('coin');
            $discount = request('discount');

            $valor_sin_formato_price = request('price');

            $var->price = $valor_sin_formato_price;
            $var->porc_discount = $discount;

            $rate_expense = request('rate_expense');

            if($coin != 'bolivares'){
                $var->price = $var->price * $rate_expense;
            }

            $var->description = request('description');

            $valor_sin_formato_amount = request('amount');

            $var->amount = $valor_sin_formato_amount;

            $exento = request('exento');

            if($exento == null){
                $exento = 0;
            }else{
                $exento = 1;
            }


            $var->exento = $exento;

            $islr = request('islr');
            if($islr == null){
                $var->islr = false;
            }else{
                $var->islr = true;
            }

            $var->save();

                if($var->expenses['status'] == 'P'){
                    $validation->calculateExpenseModify($var->id_expense);

                    $date = Carbon::now();
                    $date = $date->format('Y-m-d');

                    $expense_detail = ExpensesDetail::on(Auth::user()->database_name)->where('id_expense',$var->id_expense)->get();

                    if(isset($expense_detail)){

                            foreach($expense_detail as $varp){

                                    if(isset($varp->id_inventory)){

                                        $product = Product::on(Auth::user()->database_name)->find($varp->id_inventory);

                                        if(isset($product)){

                                            if(($product->type == 'MERCANCIA') || ($product->type == 'COMBO') || ($product->type == 'MATERIAP')){

                                                $global = new GlobalController;
                                                $global->transaction_inv('aju_compra',$varp->id_inventory,'compra_n',$varp->amount,$varp->price,$date,$varp->id_branch,$varp->id_branch,0,$varp->id_inventory_histories,$varp->id,0,$varp->id_expense);

                                            }

                                        }
                                    }
                            }
                    }
                }

            /*$historial_expense = new HistorialExpenseController();

            $historial_expense->registerAction($var,"expense_product","Actualizó el Producto: ".$var->inventories['code']."/
            Precio Viejo: ".number_format($price_old, 2, ',', '.')." Cantidad: ".$amount_old."/ Precio Nuevo: ".number_format($var->price, 2, ',', '.')." Cantidad: ".$var->amount);
        */
            return redirect('/expensesandpurchases/register/'.$var->id_expense.'/'.$coin.'')->withSuccess('Actualizacion Exitosa!');
        }else{
            return redirect('/expensesandpurchases')->withDanger('No Tiene Permiso!');
        }
        }


    public function destroy(Request $request)
    {
        if(Auth::user()->role_id == '1' || $request->get('eliminarmiddleware') == '1'){

        $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->find(request('id_expense_modal'));

        $detail = DetailVoucher::on(Auth::user()->database_name)->where('id_invoice',$expense->id)
        ->update(['status' => 'X']);


        $global = new GlobalController();
        $global->deleteAllProductsExpense($expense->id);

        ExpensePayment::on(Auth::user()->database_name)
                        ->where('id_expense',$expense->id)
                        ->update(['status' => 'X']);

        $expense->status = '0';
        $expense->save();

        $historial_expense = new HistorialExpenseController();

        $historial_expense->registerAction($expense,"expense","Se elimino la Compra");

        return redirect('/expensesandpurchases')->withDanger('Reverso Exitoso!!');
        }else{

            return redirect('/expensesandpurchases')->withDanger('No Tiene Permiso!');
        }
    }



    public function reversar_expense($id_expense)
    {

        $id_expense = $id_expense;

                 /******COURIERTOOL */
                 if(Auth::user()->id_company == '26'){


                    FacturasCour::on(Auth::user()->database_name)
                       ->where('id_expense',$id_expense)
                       ->delete();

                       }



                  /******* */

        $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->findOrFail($id_expense);

        $exist_multipayment = MultipaymentExpense::on(Auth::user()->database_name)
                            ->where('id_expense',$expense->id)
                            ->first();

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');

        if(empty($exist_multipayment)){
            if($expense != 'X'){
                $detail = DetailVoucher::on(Auth::user()->database_name)->where('id_expense',$id_expense)
                ->update(['status' => 'X']);


                $global = new GlobalController();
                $global->deleteAllProducts($expense->id);

                ExpensePayment::on(Auth::user()->database_name)
                                ->where('id_expense',$expense->id)
                                ->update(['status' => 'X']);

                $expense->status = 'X';
                $expense->save();

                $quotation_products = DB::connection(Auth::user()->database_name)->table('expenses_details')
                ->where('id_expense', '=', $expense->id)->get(); // Conteo de Productos para incluiro en el historial de inventario

                foreach($quotation_products as $det_products){ // guardando historial de inventario

                $global->transaction_inv('rev_compra',$det_products->id_inventory,'compra_reverso',$det_products->amount,$det_products->price,$datenow,$det_products->id_branch,$det_products->id_branch,0,$det_products->id_inventory_histories,$det_products->id,0,$det_products->id_expense);

                }


                //Crear un nuevo anticipo con el monto registrado en la cotizacion
                if((isset($expense->anticipo))&& ($expense->anticipo != 0)){

                    $account_anticipo = Account::on(Auth::user()->database_name)->where('description', 'like', 'Anticipos Clientes')->first();
                    $anticipoController = new AnticipoController();
                    $anticipoController->registerAnticipoProvider($datenow,$expense->id_provider,$account_anticipo->id,"bolivares",
                    $expense->anticipo,$expense->bcv,"reverso compra N°".$expense->id);

                }




                $historial_expense = new HistorialExpenseController();

                $historial_expense->registerAction($expense,"expense","Se Reversó la Compra");


                return redirect('expensesandpurchases/indexhistorial')->withSuccess('Reverso de Compra Exitosa!');
            }
        }else{
            return redirect('expensesandpurchases/indexhistorial')->withDanger('No se pudo reversar la Compra');
        }
    }



    public function reversar_expense_with_id($id_expense)
    {

        $id_expense = $id_expense;

              /******COURIERTOOL */
              if(Auth::user()->id_company == '26'){


                FacturasCour::on(Auth::user()->database_name)
                   ->where('id_expense',$id_expense)
                   ->delete();

                   }



              /******* */

        $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->findOrFail($id_expense);

        $exist_multipayment = MultipaymentExpense::on(Auth::user()->database_name)
                            ->where('id_expense',$expense->id)
                            ->first();

        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');

        if(empty($exist_multipayment)){
            if($expense != 'X'){

                HeaderVoucher::on(Auth::user()->database_name)
                ->join('detail_vouchers','detail_vouchers.id_header_voucher','header_vouchers.id')
                ->where('detail_vouchers.id_expense',$id_expense)
                ->update(['header_vouchers.status' => 'X']);

                $detail = DetailVoucher::on(Auth::user()->database_name)->where('id_expense',$id_expense)
                ->update(['status' => 'X']);


                $global = new GlobalController();
                $global->deleteAllProducts($expense->id);

                ExpensePayment::on(Auth::user()->database_name)
                                ->where('id_expense',$expense->id)
                                ->update(['status' => 'X']);

                $expense->status = 'X';
                $expense->save();



                //Crear un nuevo anticipo con el monto registrado en la cotizacion
                if((isset($expense->anticipo))&& ($expense->anticipo != 0)){

                    $account_anticipo = Account::on(Auth::user()->database_name)->where('description', 'like', 'Anticipos Clientes')->first();
                    $anticipoController = new AnticipoController();
                    $anticipoController->registerAnticipoProvider($datenow,$expense->id_provider,$account_anticipo->id,"bolivares",
                    $expense->anticipo,$expense->bcv,"reverso compra N°".$expense->id);

                }

                $historial_expense = new HistorialExpenseController();

                $historial_expense->registerAction($expense,"expense","Se Reversó la Factura");
            }
        }else{

            $this->reversar_expense_multipayment($id_expense,$exist_multipayment->id_header);
        }
    }

    public function reversar_expense_multipayment($id_expense,$id_header){


        if(isset($id_header)){

                  /******COURIERTOOL */
                  if(Auth::user()->id_company == '26'){


                    FacturasCour::on(Auth::user()->database_name)
                       ->where('id_expense',$id_expense)
                       ->delete();

                       }



                  /******* */

            $expense = ExpensesAndPurchase::on(Auth::user()->database_name)->find($id_expense);

            //aqui reversamos todo el movimiento del multipago
            DB::connection(Auth::user()->database_name)->table('detail_vouchers')
            ->join('header_vouchers', 'header_vouchers.id','=','detail_vouchers.id_header_voucher')
            ->where('header_vouchers.id','=',$id_header)
            ->update(['detail_vouchers.status' => 'X' , 'header_vouchers.status' => 'X']);

            //aqui se cambia el status de los pagos
            DB::connection(Auth::user()->database_name)->table('multipayments')
            ->join('expense_payments', 'expense_payments.id_expense','=','multipayments.id_expense')
            ->where('multipayments.id_header','=',$id_header)
            ->update(['expense_payments.status' => 'X']);

            //aqui aumentamos el inventario y cambiamos el status de los productos que se reversaron
            DB::connection(Auth::user()->database_name)->table('multipayments')
                ->join('expense_products', 'expense_products.id_expense','=','multipayments.id_expense')
                ->join('inventories','inventories.id','expense_products.id_inventory')
                ->join('products','products.id','inventories.product_id')
                ->where(function ($query){
                    $query->where('products.type','MERCANCIA')
                        ->orWhere('products.type','COMBO');
                })
                ->where('multipayments.id_header','=',$id_header)
                ->update(['inventories.amount' => DB::raw('inventories.amount+expense_products.amount') ,
                        'expense_products.status' => 'X']);


            //aqui le cambiamos el status a todas las facturas a X de reversado
            MultipaymentExpense::on(Auth::user()->database_name)
            ->join('expenses', 'expenses.id','=','multipayments.id_expense')
            ->where('id_header',$id_header)->update(['expenses.status' => 'X']);

            MultipaymentExpense::on(Auth::user()->database_name)->where('id_header',$id_header)->delete();



            $historial_expense = new HistorialExpenseController();

            $historial_expense->registerAction($expense,"expense","Se Reversó MultiCompra");


        }
    }

    public function deleteDetail(Request $request)
    {
        $id_detail = request('id_detail_modal');
        $coin = request('coin_modal');

        $detail_old = ExpensesDetail::on(Auth::user()->database_name)->find($id_detail);



        $validation = new ExpenseDetailValidationController();

        if($detail_old->expenses['status'] == 'P'){
            $validation->calculateExpenseModify($detail_old->id_expense);

            $date = Carbon::now();
            $date = $date->format('Y-m-d');

            $expense_detail = ExpensesDetail::on(Auth::user()->database_name)->where('id',$id_detail)->get();

            $detail_old->delete();

            if(isset($expense_detail)){

                    foreach($expense_detail as $varp){

                            if(isset($varp->id_inventory)){

                                $product = Product::on(Auth::user()->database_name)->find($varp->id_inventory);

                                if(isset($product)){

                                    if(($product->type == 'MERCANCIA') || ($product->type == 'COMBO') || ($product->type == 'MATERIAP')){

                                        $global = new GlobalController;
                                        $global->transaction_inv('rev_compra',$varp->id_inventory,'compra_reverso',$varp->amount,$varp->price,$date,$varp->id_branch,$varp->id_branch,0,$varp->id_inventory_histories,$varp->id,0,$varp->id_expense);

                                    }
                                }
                            }
                    }
            }


        } else {
            $detail_old->delete();
        }

        $historial_expense = new HistorialExpenseController();

        $historial_expense->registerAction($detail_old,"expense_product","Se elimino la Compra");

        return redirect('/expensesandpurchases/register/'.$detail_old->id_expense.'/'.$coin.'')->withDanger('Eliminacion exitosa!!');
    }


    public function listaccount(Request $request, $type = null)
    {


        //validar si la peticion es asincrona
        if($request->ajax()){
            try{
                $account = Account::on(Auth::user()->database_name)->find($type);
                $subcontrapartidas = Account::on(Auth::user()->database_name)->select('id','description')->where('code_one',$account->code_one)
                                                                    ->where('code_two',$account->code_two)
                                                                    ->where('code_three',$account->code_three)
                                                                    ->where('code_four',$account->code_four)
                                                                    ->where('code_five','<>',0)
                                                                    ->orderBy('description','asc')->get();

                return response()->json($subcontrapartidas,200);

            }catch(\Throwable $th){
                return response()->json(false,500);
            }
        }

    }

    public function listinventory(Request $request, $var = null){
        //validar si la peticion es asincrona
        if($request->ajax()){
            try{

                /*$respuesta = Inventory::on(Auth::user()->database_name)
                                        ->join('products','products.id','inventories.product_id')
                                        ->where('inventories.code',$var)
                                        ->select('inventories.id','products.type')
                                        ->get();*/
               $respuesta = Product::on(Auth::user()->database_name)->where('code_comercial',$var)->where('status',1)->get();
                return response()->json($respuesta,200);

            }catch(\Throwable $th){
                return response()->json(false,500);
            }
        }

    }







public function notas(request $request)
    {
     $agregarmiddleware = $request->get('agregarmiddleware');
     $actualizarmiddleware = $request->get('actualizarmiddleware');
     $eliminarmiddleware = $request->get('eliminarmiddleware');
     $namemodulomiddleware = $request->get('namemodulomiddleware');


     $expensesandpurchases = ExpensesAndPurchase::on(Auth::user()->database_name)
     ->join('debit_notes_expenses', 'expenses_and_purchases.id', '=', 'debit_notes_expenses.id_expense')
     ->where('expenses_and_purchases.amount_with_iva','<>',null)
     ->where('expenses_and_purchases.status','P')
     ->where('debit_notes_expenses.status',1)
     ->get();

         return view('admin.expensesandpurchases.notas',compact('namemodulomiddleware','expensesandpurchases','agregarmiddleware','eliminarmiddleware'));

    }




    public function crearnota(Request $request)
    {


        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');

        if($request->id == null){
            return view('admin.expensesandpurchases.crearnotas',compact('datenow'));

        }else{
            $idexpense = decrypt($request->id);
            $expense_details = ExpensesDetail::on(Auth::user()->database_name)
                                            ->where('id_expense', $idexpense)
                                            ->orderBy('id_expense' ,'DESC')
                                            ->get();


            return view('admin.expensesandpurchases.crearnotas',compact('datenow','expense_details'));


        }


    }


    public function selectfacturas()
    {
        $expensesandpurchases = ExpensesAndPurchase::on(Auth::user()->database_name)->orderBy('id' ,'DESC')
                                                    ->where('amount_with_iva','<>',null)
                                                    ->where('status','P')
                                                    ->get();

        foreach($expensesandpurchases as $expensesandpurchaser){
        $debinot = DebitNoteExpense::on(Auth::user()->database_name)->where('id_expense',$expensesandpurchaser->id)->get();

        if($debinot->count() > 0){

            foreach($debinot as $debinot){

                    if($debinot->percentage == '0.00'){
                        $expensesandpurchaser->credit = TRUE;
                    }else{
                        $expensesandpurchaser->debit = TRUE;
                    }

            }
        }else{
            $expensesandpurchaser->credit = FALSE;
            $expensesandpurchaser->debit = FALSE;
        }

        }


        $route = 'crearnota';

        return view('admin.expensesandpurchases.selectinvoice',compact('expensesandpurchases','route'));
    }


    public function notastore(Request $request)
    {


        if($request->ajax()){
            try{
                $tiponota = $request->tiponota;
                $idexpense = $request->id_expense;
                $id_user  = $request->id_user;
                $date = $request->date;
                $obs = $request->observation;
                $nrofactura = $request->invoice;

                $expensesandpurchases = ExpensesAndPurchase::on(Auth::user()->database_name)->orderBy('id' ,'DESC')
                ->where('amount_with_iva','<>',null)
                ->where('id',$idexpense)
                ->where('status','P')
                ->first();

                if($tiponota == 'credito'){

                $tipocuenta = $request->tipocuenta;
                $note = $request->note;
                $descripcionfactura = "NOTA CREDITO DE GASTOS Y COMPRA NRO ".$nrofactura;


                if($expensesandpurchases){

                    if($tipocuenta == 'descuento'){
                        $despor = $request->despor2;
                        $pordes = $request->pordes;

                        $despor =  str_replace(".", "", $despor);
                        $despor =  str_replace(",", ".", $despor);

                        /*** si el porcentaje es mayor a 150 se coloca al maximo que es el  100 */
                        if($pordes > 100){
                            $pordes = 100;
                        }


                    if($pordes == 0){

                        $resp['error'] = false;
                        $resp['msg'] = 'Debe Ingresar un Monto en Descuento';
                        return response()->json($resp);

                    } else{




                        if($expensesandpurchases->coin == 'bolivares'){

                            if($despor > $expensesandpurchases->amount){

                                $resp['error'] = false;
                                $resp['msg'] = 'El Monto de descuento no puede ser mayor a la base imponible';
                                return response()->json($resp);


                            }else{


                                $debit = new DebitNoteExpense();
                                $debit->setConnection(Auth::user()->database_name);
                                $debit->id_expense  = $idexpense;
                                $debit->id_user   = $id_user;
                                $debit->date   = $date;
                                $debit->percentage  = $pordes;
                                $debit->monto_perc  = $despor;
                                $debit->coin  = $expensesandpurchases->coin;
                                $debit->rate  = $expensesandpurchases->rate;
                                $debit->base_imponible  = $expensesandpurchases->amount;
                                $debit->obs      = $obs;
                                $debit->notapie  = $note;
                                $debit->save();

                                $headervoucher = new HeaderVoucher();
                                $headervoucher->setConnection(Auth::user()->database_name);
                                $headervoucher->description  = $descripcionfactura;
                                $headervoucher->date   = $date;
                                $headervoucher->status   = 1;
                                $headervoucher->save();


                               $descuentoenpago = Account::on(Auth::user()->database_name)->where('description', 'like', 'Descuentos en Pago')->first();

                                if(isset($descuentoenpago)){
                                    $this->add_movement($expensesandpurchases->rate,$headervoucher->id,$descuentoenpago->id,$idexpense,$id_user,0,$despor);
                                }

                                $cuentasporpagarproveedores = Account::on(Auth::user()->database_name)->where('description', 'like', 'Cuentas por Pagar Proveedores')->first();

                                if(isset($descuentoenpago)){
                                    $this->add_movement($expensesandpurchases->rate,$headervoucher->id,$cuentasporpagarproveedores->id,$idexpense,$id_user,$despor,0);
                                }

                                $resp['error'] = true;
                                $resp['msg'] = 'Nota de Credito Registrada Exitosamente';
                                return response()->json($resp);

                            }



                        }elseif($expensesandpurchases->coin == 'dolares'){

                            $montodescuento = $despor * $expensesandpurchases->rate;


                            if($montodescuento > $expensesandpurchases->amount){

                                $resp['error'] = false;
                                $resp['msg'] = 'El Monto de descuento no puede ser mayor a la base imponible';
                                return response()->json($resp);


                            }else{


                                $debit = new DebitNoteExpense();
                                $debit->setConnection(Auth::user()->database_name);
                                $debit->id_expense  = $idexpense;
                                $debit->id_user   = $id_user;
                                $debit->date   = $date;
                                $debit->percentage  = $pordes;
                                $debit->monto_perc  = $montodescuento;
                                $debit->coin  = $expensesandpurchases->coin;
                                $debit->rate  = $expensesandpurchases->rate;
                                $debit->base_imponible  = $expensesandpurchases->amount;
                                $debit->obs      = $obs;
                                $debit->notapie  = $note;
                                $debit->save();

                                $headervoucher = new HeaderVoucher();
                                $headervoucher->setConnection(Auth::user()->database_name);
                                $headervoucher->description  = $descripcionfactura;
                                $headervoucher->date   = $date;
                                $headervoucher->status   = 1;
                                $headervoucher->save();



                               $descuentoenpago = Account::on(Auth::user()->database_name)->where('description', 'like', 'Descuentos en Pago')->first();

                               if(isset($descuentoenpago)){
                                   $this->add_movement($expensesandpurchases->rate,$headervoucher->id,$descuentoenpago->id,$idexpense,$id_user,0,$montodescuento);
                               }

                               $cuentasporpagarproveedores = Account::on(Auth::user()->database_name)->where('description', 'like', 'Cuentas por Pagar Proveedores')->first();

                               if(isset($descuentoenpago)){
                                   $this->add_movement($expensesandpurchases->rate,$headervoucher->id,$cuentasporpagarproveedores->id,$idexpense,$id_user,$montodescuento,0);
                               }



                                $resp['error'] = true;
                                $resp['msg'] = 'Nota de Credito Registrada Exitosamente';
                                return response()->json($resp);

                            }

                        }else{
                                $resp['error'] = false;
                                $resp['msg'] = 'Verifique la moneda';
                                return response()->json($resp);

                            }

                        }//else de poders 0


                    }elseif($tipocuenta == 'devolucion'){
                        $preciodeduccion = 0;
                        $totalcantidad = array_sum($request->input('cantidad'));
                        $totalFactura = $request->input('totalfact');

                        $totalFactura =  str_replace(".", "", $totalFactura);
                        $totalFactura =  str_replace(",", ".", $totalFactura);


                        if($totalcantidad > 0){

                            foreach ($request->input('cantidad', []) as $i => $cantidad) {


                                if($cantidad > 0){

                                    $idinventario =  $request->input('idinventario.' . $i);
                                    $precioindividual =  $request->input('precio.' . $i);
                                    $cantidadreal =  $request->input('cantidadreal.' . $i);



                                    if($cantidad > $cantidadreal){

                                        $resp['error'] = false;
                                        $resp['msg'] = 'la cantidad Ingresada no puede superar a la cantidad del producto en la factura';
                                        return response()->json($resp);

                                    }else{



                                        if($expensesandpurchases->coin == 'dolares'){

                                            $precioindividual = $precioindividual * $expensesandpurchases->rate;

                                        $preciodeduccionproducto = $cantidad * $precioindividual;


                                        $totalFactura = $totalFactura * $expensesandpurchases->rate;
                                        $preciodeduccion += $cantidad * $precioindividual;

                                        }
                                        elseif($expensesandpurchases->coin == 'bolivares'){
                                            $preciodeduccionproducto = $cantidad * $precioindividual;
                                            $preciodeduccion += $cantidad * $precioindividual;
                                            $totalFactura = $totalFactura;

                                        }




                                        $debitdetal = new DebitNoteDetailExpense();
                                        $debitdetal->setConnection(Auth::user()->database_name);
                                        $debitdetal->id_inventory  = $idinventario;
                                        $debitdetal->amount   = $cantidad;
                                        $debitdetal->price  = $preciodeduccionproducto;
                                        $debitdetal->save();



                                    $arreglo[] = ['idetal' => $debitdetal->id];


                                    $inventory = Inventory::on(Auth::user()->database_name)->find($idinventario);

                                    $inventories_quotations = DB::connection(Auth::user()->database_name)
                                    ->table('inventory_histories')
                                    ->where('id_product','=',$inventory->product_id)
                                    ->select('*')
                                    ->get()->last();

                                    if(is_null($inventories_quotations)){
                                        $realcantidad = 0;
                                        $amount_real = $cantidad - $realcantidad;
                                    }else{
                                        $realcantidad = $inventories_quotations->amount_real;
                                        $amount_real = $realcantidad - $cantidad;
                                    }




                                    $invh = new InventoryHistories();
                                    $invh->setConnection(Auth::user()->database_name);
                                    $invh->id_product = $inventory->product_id;
                                    $invh->id_expense_detail = $idexpense;
                                    $invh->id_user = $id_user;
                                    $invh->id_branch = 1;
                                    $invh->id_centro_costo = 1;
                                    $invh->id_quotation_product = 0;
                                    $invh->date = $date;
                                    $invh->type = "rev_compra";
                                    $invh->price = $precioindividual;
                                    $invh->amount = $cantidad;
                                    $invh->amount_real = $amount_real;
                                    $invh->save();

                                        $validar = true;

                                    }




                                }//fin cantidad 0



                             }//fin foreach


                             $porcentaje = ($preciodeduccion * 100) / $totalFactura; // Regla de tres

                            $debit = new DebitNoteExpense();
                            $debit->setConnection(Auth::user()->database_name);
                            $debit->id_expense  = $idexpense;
                            $debit->id_user   = $id_user;
                            $debit->date   = $date;
                            $debit->percentage  = $porcentaje;
                            $debit->monto_perc  = $preciodeduccion;
                            $debit->coin  = $expensesandpurchases->coin;
                            $debit->rate  = $expensesandpurchases->rate;
                            $debit->base_imponible  = $expensesandpurchases->amount;
                            $debit->obs      = $obs;
                            $debit->notapie  = $note;
                            $debit->save();


                            DebitNoteDetailExpense::on(Auth::user()->database_name)
                            ->Wherein('id',$arreglo)
                            ->update(['id_debit_note_expenses' => $debit->id]);



                            $headervoucher = new HeaderVoucher();
                            $headervoucher->setConnection(Auth::user()->database_name);
                            $headervoucher->description  = $descripcionfactura;
                            $headervoucher->date   = $date;
                            $headervoucher->status   = 1;
                            $headervoucher->save();




                            $descuentoenpago = Account::on(Auth::user()->database_name)->where('description', 'like', 'Devolucion de Mercancia')->first();

                            if(isset($descuentoenpago)){
                                $this->add_movement($expensesandpurchases->rate,$headervoucher->id,$descuentoenpago->id,$idexpense,$id_user,0,$preciodeduccion);
                            }

                            $cuentasporpagarproveedores = Account::on(Auth::user()->database_name)->where('description', 'like', 'Cuentas por Pagar Proveedores')->first();

                            if(isset($descuentoenpago)){
                                $this->add_movement($expensesandpurchases->rate,$headervoucher->id,$cuentasporpagarproveedores->id,$idexpense,$id_user,$preciodeduccion,0);
                            }




                            if($validar == TRUE){
                                $resp['error'] = true;
                                $resp['msg'] = 'Nota de Credito Registrada Exitosamente';
                                return response()->json($resp);
                            }

                        }else{

                                $resp['error'] = false;
                                $resp['msg'] = 'Debe ingresar una cantidad en algun producto';
                                return response()->json($resp);

                        }





                    }else{
                            $resp['error'] = false;
                                $resp['msg'] = 'Verifique la cuenta seleccionada';
                                return response()->json($resp);


                    }



                }else{

                    return response()->json(false,500);
                }

                }else{

                    /********************* NOTAS DE DEBITO***********/

                    $pordes = 0;
                    $note = null;


                    if($expensesandpurchases->coin == 'dolares'){

                        $montocredito = $request->montocredito * $expensesandpurchases->rate;


                    }
                    elseif($expensesandpurchases->coin == 'bolivares'){

                        $montocredito = $request->montocredito;

                    }

                    //$montocredito =  str_replace(".", "", $montocredito);
                    $montocredito =  str_replace(",", ".", $montocredito);

                    $descripcionfactura = "NOTA DEBITO DE GASTOS Y COMPRA NRO ".$nrofactura;


                    $debit = new DebitNoteExpense();
                                $debit->setConnection(Auth::user()->database_name);
                                $debit->id_expense  = $idexpense;
                                $debit->id_user   = $id_user;
                                $debit->date   = $date;
                                $debit->percentage  = $pordes;
                                $debit->monto_perc  = $montocredito;
                                $debit->coin  = $expensesandpurchases->coin;
                                $debit->rate  = $expensesandpurchases->rate;
                                $debit->base_imponible  = $expensesandpurchases->amount;
                                $debit->obs      = $obs;
                                $debit->notapie  = $note;
                                $debit->save();

                                $headervoucher = new HeaderVoucher();
                                $headervoucher->setConnection(Auth::user()->database_name);
                                $headervoucher->description  = $descripcionfactura;
                                $headervoucher->date   = $date;
                                $headervoucher->status   = 1;
                                $headervoucher->save();

                                $resp['error'] = true;
                                $resp['msg'] = 'Nota de debito Registrada Exitosamente';
                                return response()->json($resp);




                }





            }catch(\error $error){
                return response()->json(false,500);
            }
        }



    }




    public function deletenota(Request $request)
    {
        if(Auth::user()->role_id == '1' || $request->get('eliminarmiddleware') == '1'){

            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');

            $descripcion = request('descripcion');



        DebitNoteExpense::on(Auth::user()->database_name)
            ->Where('id',request('id_expense_modal'))
            ->delete();

        $debitnotedetailexpense = DebitNoteDetailExpense::on(Auth::user()->database_name)
            ->Where('id_debit_note_expenses',request('id_expense_modal'))
            ->get();


        if($debitnotedetailexpense->count() > 0){

            foreach($debitnotedetailexpense as $debitnotedetailexpense){


                $inventory = Inventory::on(Auth::user()->database_name)->find($debitnotedetailexpense->id_inventory);

                                    $inventories_quotations = DB::connection(Auth::user()->database_name)
                                    ->table('inventory_histories')
                                    ->where('id_product','=',$inventory->product_id)
                                    ->select('*')
                                    ->get()->last();

                                    $amount_real = $inventories_quotations->amount_real + $debitnotedetailexpense->amount;
                                    $precioindividual = $debitnotedetailexpense->price / $debitnotedetailexpense->amount;

                                    $invh = new InventoryHistories();
                                    $invh->setConnection(Auth::user()->database_name);
                                    $invh->id_product = $inventory->product_id;
                                    $invh->id_user = Auth::user()->id;
                                    $invh->id_branch = 1;
                                    $invh->id_centro_costo = 1;
                                    $invh->id_quotation_product = 0;
                                    $invh->date = $datenow;
                                    $invh->type = "nota_debito_eliminada";
                                    $invh->price = $precioindividual;
                                    $invh->amount = $debitnotedetailexpense->amount;
                                    $invh->amount_real = $amount_real;
                                    $invh->save();

            }

            DebitNoteDetailExpense::on(Auth::user()->database_name)
            ->Where('id_debit_note_expenses',request('id_expense_modal'))->delete();

        }

        $headervoucher =  HeaderVoucher::on(Auth::user()->database_name)->where('description',$descripcion)->where('status',1)->first();

        DetailVoucher::on(Auth::user()->database_name)->where('id_header_voucher',$headervoucher->id)->delete();

        $headervoucher->delete();

        return redirect('/expensesandpurchases/notas')->withDanger('Nota Elimidada Con Exitos!!');
        }else{

            return redirect('/expensesandpurchases')->withDanger('No Tiene Permiso!');
        }
    }




    //******ASIGNAR FACTURA A COURIERTOOL */
    public function asignarcouriertool(Request $request)
    {


        if($request->court != null AND $request->tifac != null AND $request->nrofactcou != null){

            if($request->tipoarya == 'venta'){
                $totalFactura =  str_replace(".", "", $request->montomodal);
                $totalFactura =  str_replace(",", ".", $totalFactura);
                $factcour  = new FacturasCour();
                $factcour->setConnection(Auth::user()->database_name);
                $factcour->id_ventas = $request->idexpense;
                $factcour->tipo_fac = $request->tifac;
                $factcour->tipo_movimiento = $request->court;
                $factcour->numero =  $request->nrofactcou;
                $factcour->monto =  $totalFactura;
                $factcour->save();
                return redirect('/invoices')->withSuccess('Asignado a Couriertool Exitosa!');

            }


            if($request->tipoarya == 'compras'){
                $totalFactura =  str_replace(".", "", $request->montomodal);
                $totalFactura =  str_replace(",", ".", $totalFactura);
                $factcour  = new FacturasCour();
                $factcour->setConnection(Auth::user()->database_name);
                $factcour->id_expense = $request->idexpense;
                $factcour->tipo_fac = $request->tifac;
                $factcour->tipo_movimiento = $request->court;
                $factcour->numero =  $request->nrofactcou;
                $factcour->monto =  $totalFactura;
                $factcour->save();

                return redirect('expensesandpurchases/indexhistorial')->withSuccess('Asignado a Couriertool Exitosa!');


            }



        }

    }

    public function getislramount(Request $request) {
        $id = $request->get('id'); 

        $islrconcepts = IslrConcept::on(Auth::user()->database_name)->find($id);
        
       /* if(empty($islrconcepts)){
            
            if (isset($islrconcepts->pagos_mayores)){
                $pagos_mayores = $islrconcepts->pagos_mayores;
            } else {
                $islrconcepts->pagos_mayores = 0;
            }

            if (isset($islrconcepts->pagos_mayores)){
                $sustraendo = $islrconcepts->sustraendo;
            } else {
                $islrconcepts->sustraendo = 0;
            }
            
        } else {
            $pagos_mayores = 0;
            $sustraendo = 0;
        }*/
        
        return response()->json($islrconcepts);

    }

}
