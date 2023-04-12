<?php

namespace App\Http\Controllers;

use App\Import;
use App\Product;
use App\Quotation;
use App\ImportDetail;
use App\ExpensesAndPurchase;
use App\ExpensesDetail;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ImportController extends Controller
{

    public function __construct(){

        $this->middleware('auth');
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Importaciones');
    }

    public function index(Request $request)
    {

        $agregarmiddleware = $request->get('agregarmiddleware');
        $actualizarmiddleware = $request->get('actualizarmiddleware');
        $eliminarmiddleware = $request->get('eliminarmiddleware');

        $user= auth()->user();
        $imports = Import::on(Auth::user()->database_name)->orderBy('id' ,'DESC')->get();
       /* $quotationss = DB::connection(Auth::user()->database_name)
            ->table('expenses_and_purchases')
            ->leftjoin('import_details', 'expenses_and_purchases.id', '=', 'import_details.id_purchases')
            ->select('quotations.*')
            ->whereNull('import_details.id_purchases')
            ->get();*/

        return view('admin.imports.index',compact('imports','agregarmiddleware','actualizarmiddleware','eliminarmiddleware'));
    }


    public function create(request $request)
    {
        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){



if($request->serv == TRUE){
            $id = decrypt($request->idfact);

            $sql = "SELECT a.invoice,b.description,b.amount,b.price
            FROM expenses_and_purchases a, expenses_details b, inventories c, products d
            WHERE a.id = b.id_expense
            AND b.id_inventory = c.id
            AND c.product_id = d.id
            AND d.type = 'MERCANCIA'
            AND a.status IN ('C','P')
            AND a.invoice = '$id'";

            $datosinv = DB::connection(Auth::user()->database_name)->select($sql);

            $idser = $request->idservi;


            foreach($idser as $idser){

                $idserarray[] = "'$idser'";

            }

            $cadena_equipo = implode(',',$idserarray);



            $sql2 = "SELECT a.invoice,b.description,b.price
            FROM expenses_and_purchases a, expenses_details b, inventories c, products d
            WHERE a.id = b.id_expense
            AND b.id_inventory = c.id
            AND c.product_id = d.id
            AND d.type = 'SERVICIO'
            AND a.status IN ('C','P')
            AND a.invoice IN ($cadena_equipo)";


            $datosserv = DB::connection(Auth::user()->database_name)->select($sql2);


           return view('admin.imports.create',compact('id','datosinv','datosserv'));
        }



        elseif($request->inv == TRUE){
            $id = decrypt($request->id);
            $datosserv = [];
            $sql = "SELECT a.invoice,b.description,b.amount,b.price
            FROM expenses_and_purchases a, expenses_details b, inventories c, products d
            WHERE a.id = b.id_expense
            AND b.id_inventory = c.id
            AND c.product_id = d.id
            AND d.type = 'MERCANCIA'
            AND a.status IN ('C','P')
            AND a.invoice = '$id'";

            $datosinv = DB::connection(Auth::user()->database_name)->select($sql);

           if(count($datosinv) > 0){
            $id = $id;
           }else{
             $id = null;
           }
           return view('admin.imports.create',compact('id','datosinv','datosserv'));
        }elseif($request->id == null){
            $request->id == null;
            $id = $request->id;

        }

        return view('admin.imports.create',compact('id'));

    }else{
        return redirect('/imports')->withDelete('No tiene Permiso!');

    }
    }



    public function cargar(request $request)
    {

        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){
            $sql = 'SELECT a.invoice, a.serie
            FROM expenses_and_purchases a, expenses_details b, inventories c, products d
            WHERE a.id = b.id_expense
            AND b.id_inventory = c.id
            AND c.product_id = d.id
            AND d.type = "MERCANCIA"
            AND a.status IN ("C","P")
            GROUP BY a.invoice, a.serie';

            $quotationss = DB::connection(Auth::user()->database_name)->select($sql);


        return view('admin.imports.importquotation',compact('quotationss'));

    }else{
        return redirect('/imports')->withDelete('No tiene Permiso!');

    }

    }

    public function cargarservicio(request $request)
    {
        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){
            $sql = 'SELECT a.invoice, a.serie
            FROM expenses_and_purchases a, expenses_details b, inventories c, products d
            WHERE a.id = b.id_expense
            AND b.id_inventory = c.id
            AND c.product_id = d.id
            AND d.type = "SERVICIO"
            AND a.status IN ("C","P")
            GROUP BY a.invoice, a.serie';

            $quotationss = DB::connection(Auth::user()->database_name)->select($sql);
            $idfact = $request->idfact;

        return view('admin.imports.importser',compact('quotationss','idfact'));

    }else{
        return redirect('/imports')->withDelete('No tiene Permiso!');

    }

    }




    public function selectimport(request $request,$id)
    {
        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){
        $user= auth()->user();
        $import_id          =   Import::on(Auth::user()->database_name)->where('id',$id)->get()->first();
        $imports            =   Import::on(Auth::user()->database_name)->get();
        $importDetails      =   ImportDetail::on(Auth::user()->database_name)->get();


        $quotationss = DB::connection(Auth::user()->database_name)
            ->table('expenses_and_purchases')
            ->leftjoin('import_details', 'expenses_and_purchases.id', '=', 'import_details.id_purchases')
            ->select('expenses_and_purchases.*')
            ->whereNull('import_details.id_purchases')
            ->get();

        return view('admin.imports.importdetails',compact('quotationss','imports','importDetails','import_id'));

    }else{
        return redirect('/imports')->withDelete('No tiene Permiso!');

    }
    }


    public function cargarDetails(request $request,$id,$quotation = null)
    {

        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){


        if(isset($id)){
            $import     = Import::on(Auth::user()->database_name)->where('id',$id)->get()->first();
            $id_import =  $import->id;
        }

        if(isset($quotation)){
            $quotation     = ExpensesAndPurchase::on(Auth::user()->database_name)->where('id',$quotation)->get()->first();
            $id_quotation =  $quotation->id;
        }

        $ldate = date('Y-m-d');
        $user= auth()->user();
        $imports = new ImportDetail();
        $imports->setConnection(Auth::user()->database_name);

        $imports->id_import       = $id_import;
        $imports->id_purchases    = $id_quotation;
        $imports->fecha           = $ldate;
        $imports->status          = '1';
        $imports->save();




        return redirect('/imports')->withSuccess('Registro de Importacion-Segunradia Exitosa!');

    }else{
        return redirect('/imports')->withDelete('No tiene Permiso!');

    }

    }



    public function calcular(request $request,$id)
    {

        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){


        $import             =   Import::on(Auth::user()->database_name)->where('id',$id)->get()->first();

        $import_quotation   =$import->id;
        $import_details     =   ImportDetail::on(Auth::user()->database_name)->where('id_import',$id)->get();
        $import_expense    =   ExpensesAndPurchase::on(Auth::user()->database_name)->where('id', $import->id_purchases)->get()->first();
        $expenses_imports           =   ExpensesDetail::on(Auth::user()->database_name)->where('id_expense', $import_expense->id)->get();
        foreach ($import_details as $import_detail ){

            $expenses           =   ExpensesDetail::on(Auth::user()->database_name)->where('id_expense', $import_detail->id_purchases)->get();

            $inventories_expenses = DB::connection(Auth::user()->database_name)->table('products')->join('inventories', 'products.id', '=', 'inventories.product_id')
                ->join('expenses_details', 'inventories.id', '=', 'expenses_details.id_inventory')
                ->where('expenses_details.id_expense',$import_detail->id_purchases)
                ->select('products.*','expenses_details.price as price','expenses_details.rate as rate',
                    'expenses_details.amount as amount_expense','expenses_details.exento as retiene_iva_expense'
                    ,'expenses_details.islr as retiene_islr_expense')
                ->get();

        }

        $total = 0;
        foreach ($expenses_imports as $expenses_import ){
            $total += ($expenses_import->price * $expenses_import->amount);
            $inventories_import_expenses = DB::connection(Auth::user()->database_name)->table('products')->join('inventories', 'products.id', '=', 'inventories.product_id')
                ->join('expenses_details', 'inventories.id', '=', 'expenses_details.id_inventory')
                ->where('expenses_details.id_expense',$expenses_import->id)
                ->select('products.*','expenses_details.price as price','expenses_details.rate as rate',
                    'expenses_details.amount as amount_expense','expenses_details.exento as retiene_iva_expense'
                    ,'expenses_details.islr as retiene_islr_expense')
                ->get();
        }


        $total_gasto = 0;
        foreach($expenses as $var){
            //Se calcula restandole el porcentaje de descuento (discount)

            $total_gasto += ($var->price * $var->amount);
            //-----------------------------

        }

        $resultado = $total + $total_gasto;
            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');

            return view('admin.imports.importPrincipal',compact('import_quotation','import','import_details','inventories_import_expenses','expenses','expenses_imports','import_expense','resultado','total_gasto','total'));

        }else{
            return redirect('/imports')->withDelete('No tiene Permiso!');

        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){

        $data = request()->validate([
            'Fecha'         =>'required',
        ]);

        $user= auth()->user();
        $imports = new Import();
        $imports->setConnection(Auth::user()->database_name);

        $imports->id_user         = $user->id;
        $imports->id_purchases    = request('Nro_Factura');
        $imports->fecha           = request('Fecha');
        $imports->observaciones   = request('Observaciones');
        $imports->save();

        return redirect('/imports')->withSuccess('Registro de Importacion Exitoso!');

    }else{
        return redirect('/imports')->withDelete('No tiene Permiso!');

    }
    }


    public function calcularfiltro(Request $request, $id)
    {

        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){
        $total_pricipal =  request('total_1');
        $resultado_total =  request('total_2');




        $imports = Import::on(Auth::user()->database_name)->findOrFail($id);
        $imports->porcentaje_general = request('Precio');
        $imports->save();


        $import             =   Import::on(Auth::user()->database_name)->where('id',$id)->get()->first();

        $import_quotation   =$import->id;
        $import_details     =   ImportDetail::on(Auth::user()->database_name)->where('id_import',$id)->get();

        $import_expense    =   ExpensesAndPurchase::on(Auth::user()->database_name)->where('id', $import->id_purchases)->get()->first();
        $expenses_imports           =   ExpensesDetail::on(Auth::user()->database_name)->where('id_expense', $import_expense->id)->get();

        $expenses_imports2           =   ExpensesDetail::on(Auth::user()->database_name)->where('id_expense', $import_expense->id)->get();

        foreach ($import_details as $import_detail ){

            $expenses           =   ExpensesDetail::on(Auth::user()->database_name)->where('id_expense', $import_detail->id_purchases)->get();

            $inventories_expenses = DB::connection(Auth::user()->database_name)->table('products')->join('inventories', 'products.id', '=', 'inventories.product_id')
                ->join('expenses_details', 'inventories.id', '=', 'expenses_details.id_inventory')
                ->where('expenses_details.id_expense',$import_detail->id_purchases)
                ->select('products.*','expenses_details.price as price','expenses_details.rate as rate',
                    'expenses_details.amount as amount_expense','expenses_details.exento as retiene_iva_expense'
                    ,'expenses_details.islr as retiene_islr_expense')
                ->get();

        }

        $total= 0;
        $base_imponible= 0;

        //este es el total que se usa para guardar el monto de todos los productos que estan exentos de iva, osea retienen iva
        $total_retiene_iva = 0;
        $retiene_iva = 0;

        $total_retiene_islr = 0;
        $retiene_islr = 0;

        foreach($inventories_expenses as $var){
            //Se calcula restandole el porcentaje de descuento (discount)

            $total += ($var->price * $var->amount_expense);
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

        $total_import = 0;
        foreach ($expenses_imports as $expenses_import ){
            $total_import += ($expenses_import->price * $expenses_import->amount);

            $inventories_import_expenses = DB::connection(Auth::user()->database_name)->table('products')->join('inventories', 'products.id', '=', 'inventories.product_id')
                ->join('expenses_details', 'inventories.id', '=', 'expenses_details.id_inventory')
                ->where('expenses_details.id_expense',$expenses_import->id)
                ->select('products.*','expenses_details.price as price','expenses_details.rate as rate',
                    'expenses_details.amount as amount_expense','expenses_details.exento as retiene_iva_expense'
                    ,'expenses_details.islr as retiene_islr_expense')
                ->get();



        }

        $resultado = $total + $total_import;
        foreach ($expenses_imports2 as $expenses_import2 ){


            $inventories_import_expenses2 = DB::connection(Auth::user()->database_name)->table('products')->join('inventories', 'products.id', '=', 'inventories.product_id')
                ->join('expenses_details', 'inventories.id', '=', 'expenses_details.id_inventory')
                ->where('expenses_details.id_expense',$expenses_import2->id_expense)
                ->select('products.*','expenses_details.price as price','expenses_details.rate as rate',
                    'expenses_details.amount as amount_expense','expenses_details.exento as retiene_iva_expense'
                    ,'expenses_details.islr as retiene_islr_expense')
                ->get();
           foreach ($inventories_import_expenses2 as $inventories_import_expenses){



               $venta =  ( $resultado_total/ $total_pricipal )  * $inventories_import_expenses->price * $import->porcentaje_general / 100  ;
               $compra  = ( $resultado_total / $total_pricipal )  * $expenses_import2->amount * $inventories_import_expenses->price  / $expenses_import2->amount;
               $var = Product::on(Auth::user()->database_name)->findOrFail($inventories_import_expenses->id);
               $var->price =   $venta;
               $var->price_buy = $compra;
               $var->save();
           }
        }
        return redirect()->route('imports.calcular',$id);


    }else{
        return redirect('/imports')->withDelete('No tiene Permiso!');

    }
    }




}
