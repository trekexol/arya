<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;


use App;
use App\Account;
use App\Client;
use App\Company;
use App\DetailVoucher;
use App\Employee;
use App\ExpensesAndPurchase;
use App\ExpensesDetail;
use App\Http\Controllers\UserAccess\UserAccessController;
use App\Product;
use App\Provider;
use App\Quotation;
use App\QuotationProduct;
use App\Vendor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class Report2Controller extends Controller
{
   
    public $modulo = "Reportes";
   
    public function index_accounts_receivable($typeperson,$id_client_or_vendor = null)
    {        
       
        $userAccess = new UserAccessController();

        if($userAccess->validate_user_access($this->modulo)){
            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');   
            $client = null; 
            $vendor = null; 


            if(isset($typeperson) && $typeperson == 'Cliente'){
                if(isset($id_client_or_vendor)){
                    $client    = Client::on(Auth::user()->database_name)->find($id_client_or_vendor);
                }
            }else if (isset($typeperson) && $typeperson == 'Vendedor'){
                if(isset($id_client_or_vendor)){
                    $vendor    = Vendor::on(Auth::user()->database_name)->find($id_client_or_vendor);
                }
            }
            
            return view('admin.reports.index_accounts_receivable',compact('client','datenow','typeperson','vendor'));
        }else{
            return redirect('/home')->withDanger('No tiene Acceso al modulo de '.$this->modulo);
        }
    }

    public function index_debtstopay($id_provider = null)
    {
      
        $userAccess = new UserAccessController();
        
        if($userAccess->validate_user_access($this->modulo)){
            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');   
            $provider = null; 

            if(isset($id_provider)){
                $provider    = Provider::on(Auth::user()->database_name)->find($id_provider);
            }
        }else{
            return redirect('/home')->withDanger('No tiene Acceso al modulo de '.$this->modulo);
        }

        return view('admin.reports.index_debtstopay',compact('provider','datenow'));
      
    }


    public function index_ledger()
    {
        
        $user       =   auth()->user();
        $users_role =   $user->role_id;
       
            $date = Carbon::now();
            $datenow = $date->format('Y-m-d');    
            
            $datebeginyear = $date->firstOfYear()->format('Y-m-d');

        return view('admin.reports.index_ledger',compact('datebeginyear','datenow'));
      
    }

    public function index_accounts()
    {
        
        $user       =   auth()->user();
        $users_role =   $user->role_id;
        
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');    
        
        $datebeginyear = $date->firstOfYear()->format('Y-m-d');

       
        return view('admin.reports.index_accounts',compact('datebeginyear','datenow'));
      
    }

    public function index_bankmovements()
    {
        
        $user       =   auth()->user();
        $users_role =   $user->role_id;
        
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');   
        $datebeginyear = $date->firstOfYear()->format('Y-m-d');

        $accounts_banks = DB::connection(Auth::user()->database_name)->table('accounts')
                            ->where('code_one', 1)
                            ->where('code_two', 1)
                            ->where('code_three', 1)
                            ->whereIn('code_four', [1,2])
                            ->where('code_five', '<>',0)
                            ->where('description','not like', 'Punto de Venta%')
                            ->get();

        return view('admin.reports.index_bankmovements',compact('accounts_banks','datebeginyear','datenow'));
      
    }

    public function index_sales_books()
    {
        
        $user       =   auth()->user();
        $users_role =   $user->role_id;
        
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');    
        
        $datebeginyear = $date->firstOfYear()->format('Y-m-d');

        return view('admin.reports.index_sales_books',compact('datebeginyear','datenow'));
      
    }

    public function index_purchases_books()
    {
        
        $user       =   auth()->user();
        $users_role =   $user->role_id;
        
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');    
        
        $datebeginyear = $date->firstOfYear()->format('Y-m-d');

        return view('admin.reports.index_purchases_books',compact('datebeginyear','datenow'));
      
    }

    public function index_inventory()
    {
        
        $user       =   auth()->user();
        $users_role =   $user->role_id;
        
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');    
        
        $datebeginyear = $date->firstOfYear()->format('Y-m-d');

        return view('admin.reports.index_inventory',compact('datebeginyear','datenow'));
      
    }

    public function index_operating_margin()
    {
        
        $user       =   auth()->user();
        $users_role =   $user->role_id;
        
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');    
        
        $datebeginyear = $date->firstOfYear()->format('Y-m-d');

        return view('admin.reports.index_operating_margin',compact('datebeginyear','datenow'));
      
    }


    public function index_clients()
    {
        
        $user       =   auth()->user();
        $users_role =   $user->role_id;
        
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');    
        
        $clients = Client::on(Auth::user()->database_name)->orderBy('created_at','asc')->first();

        $date_begin = Carbon::parse($clients->created_at);
        $datebeginyear = $date_begin->format('Y-m-d');

        //$datebeginyear = $date->firstOfYear()->format('Y-m-d');

        return view('admin.reports.index_clients',compact('datebeginyear','datenow'));
      
    }

    public function index_providers()
    {
        
        $user       =   auth()->user();
        $users_role =   $user->role_id;
        
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');    
        
        $providers = Provider::on(Auth::user()->database_name)->orderBy('created_at','asc')->first();

        $date_begin = Carbon::parse($providers->created_at);
        $datebeginyear = $date_begin->format('Y-m-d');

        //$datebeginyear = $date->firstOfYear()->format('Y-m-d');

        return view('admin.reports.index_providers',compact('datebeginyear','datenow'));
      
    }

    public function index_employees()
    {
        
        $user       =   auth()->user();
        $users_role =   $user->role_id;
        
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');    
        
        $employees = Employee::on(Auth::user()->database_name)->orderBy('created_at','asc')->first();

        $date_begin = Carbon::parse($employees->created_at);
        $datebeginyear = $date_begin->format('Y-m-d');

        //$datebeginyear = $date->firstOfYear()->format('Y-m-d');

        return view('admin.reports.index_employees',compact('datebeginyear','datenow'));
      
    }

    public function index_sales()
    {
        
        $user       =   auth()->user();
        $users_role =   $user->role_id;
        
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d');    
        
        $datebeginyear = $date->firstOfYear()->format('Y-m-d');

        return view('admin.reports.index_sales',compact('datebeginyear','datenow'));
      
    }
     
    public function store_accounts_receivable(Request $request)
    {
        
        $date_end = request('date_end');
        $type = request('type');
        $id_client = request('id_client');
        $id_vendor = request('id_vendor');
        $typeinvoice = request('typeinvoice');
        $coin = request('coin');
        $client = null;
        $vendor = null;
        $typeperson = 'ninguno';

        if($type != 'todo'){
            if(isset($id_client)){
                $client    = Client::on(Auth::user()->database_name)->find($id_client);
                $typeperson = 'Cliente';
                $id_client_or_vendor = $id_client;
            }
            if(isset($id_vendor)){
                $vendor    = Vendor::on(Auth::user()->database_name)->find($id_vendor);
                $typeperson = 'Vendedor';
                $id_client_or_vendor = $vendor;
            }
        }

        return view('admin.reports.index_accounts_receivable',compact('coin','typeinvoice','date_end','client','vendor','typeperson'));
    }

    public function store_debtstopay(Request $request)
    {
        
        $date_end = request('date_end');
        $type = request('type');
        $id_provider = request('id_provider');
        $coin = request('coin');

        $provider = null;

        if($type != 'todos'){

            if(isset($id_provider)){
                $provider    = Provider::on(Auth::user()->database_name)->find($id_provider);
            }
        }

        return view('admin.reports.index_debtstopay',compact('date_end','provider','coin'));
    }

    public function store_accounts(Request $request)
    {
        
        $client = null;
        $date_begin = request('date_begin');
        $date_end = request('date_end');
        $level = request('level');
        
        if(isset($request->id_client)){
            $client = Client::on(Auth::user()->database_name)->find($request->id_client);
        }
        
        return view('admin.reports.index_accounts',compact('client','date_begin','date_end','level'));
    }

    public function store_bankmovements(Request $request)
    {
        
        $id_bank = request('id_bank');
        $coin = request('coin');
        $date_begin = request('date_begin');
        $date_end = request('date_end');
        $account_bank = request('account_bank');

        if(isset($account_bank)){
            $account_bank = Account::on(Auth::user()->database_name)->find($account_bank);
        }
        $type = request('type');
        
        $accounts_banks = DB::connection(Auth::user()->database_name)->table('accounts')
                            ->where('code_one', 1)
                            ->where('code_two', 1)
                            ->where('code_three', 1)
                            ->whereIn('code_four', [1,2])
                            ->where('code_five', '<>',0)
                            ->where('description','not like', 'Punto de Venta%')
                            ->get();
        
        
        return view('admin.reports.index_bankmovements',compact('coin','accounts_banks','id_bank','date_begin','date_end','account_bank','type'));
    }

    public function store_sales_books(Request $request)
    {
        
       
        $coin = request('coin');
        $date_begin = request('date_begin');
        $date_end = request('date_end');
        
        return view('admin.reports.index_sales_books',compact('coin','date_begin','date_end'));
    }

    public function store_purchases_books(Request $request)
    {
        
       
        $coin = request('coin');
        $date_begin = request('date_begin');
        $date_end = request('date_end');
        
        return view('admin.reports.index_purchases_books',compact('coin','date_begin','date_end'));
    }

    public function store_inventory(Request $request)
    {
        
       
        $coin = request('coin');
        $date_begin = request('date_begin');
        $date_end = request('date_end');
        $name = request('name');
        
        return view('admin.reports.index_inventory',compact('name','coin','date_begin','date_end'));
    }

    public function store_operating_margin(Request $request)
    {
        $coin = request('coin');
        $date_begin = request('date_begin');
        $date_end = request('date_end');
        
        return view('admin.reports.index_operating_margin',compact('coin','date_begin','date_end'));
    }

    public function store_clients(Request $request)
    {
        
        $date_begin = request('date_begin');
        $date_end = request('date_end');
        $name = request('name');
        
        return view('admin.reports.index_clients',compact('name','date_begin','date_end'));
    }

    public function store_providers(Request $request)
    {
        
        $date_begin = request('date_begin');
        $date_end = request('date_end');
        $name = request('name');
        
        return view('admin.reports.index_providers',compact('name','date_begin','date_end'));
    }

    public function store_sales(Request $request)
    {
        
        $date_begin = request('date_begin');
        $date_end = request('date_end');
        $name = request('name');
        $coin = request('coin');
        
        return view('admin.reports.index_sales',compact('name','coin','date_begin','date_end'));
    }


    function debtstopay_pdf($coin,$date_end,$id_provider = null)
    {
      
        $pdf = App::make('dompdf.wrapper');

       
        $date = Carbon::now();
        $datenow = $date->format('d-m-Y'); 
        if(empty($date_end)){
            $date_end = $datenow;

            $date_consult = $date->format('Y-m-d'); 
        }else{
            $date_end = Carbon::parse($date_end)->format('d-m-Y');

            $date_consult = Carbon::parse($date_end)->format('Y-m-d');
        }
        
        $period = $date->format('Y'); 
        
        if(empty($coin)){
            $coin = "bolivares";
        }
      
        if(isset($id_provider)){
            
            if((isset($coin)) && ($coin == "bolivares")){
                $expenses = DB::connection(Auth::user()->database_name)->table('expenses_and_purchases')
                                    ->join('providers', 'providers.id','=','expenses_and_purchases.id_provider')
                                    ->leftjoin('anticipos', 'anticipos.id_expense','=','expenses_and_purchases.id')
                                    ->whereIn('expenses_and_purchases.status',[1,'P'])
                                    ->where('expenses_and_purchases.amount','<>',null)
                                    ->where('expenses_and_purchases.date','<=',$date_consult)
                                    ->where('expenses_and_purchases.id_provider',$id_provider)
                                    ->select('expenses_and_purchases.rate','expenses_and_purchases.date','expenses_and_purchases.id','expenses_and_purchases.serie','providers.razon_social as name_provider','expenses_and_purchases.amount','expenses_and_purchases.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                                    ->groupBy('expenses_and_purchases.rate','expenses_and_purchases.date','expenses_and_purchases.id','expenses_and_purchases.serie','providers.razon_social','expenses_and_purchases.amount','expenses_and_purchases.amount_with_iva')
                                    ->get();
            }else{
                $expenses = DB::connection(Auth::user()->database_name)->table('expenses_and_purchases')
                                    ->join('providers', 'providers.id','=','expenses_and_purchases.id_provider')
                                    ->leftjoin('anticipos', 'anticipos.id_expense','=','expenses_and_purchases.id')
                                    ->whereIn('expenses_and_purchases.status',[1,'P'])
                                    ->where('expenses_and_purchases.amount','<>',null)
                                    ->where('expenses_and_purchases.date','<=',$date_consult)
                                    ->where('expenses_and_purchases.id_provider',$id_provider)
                                    ->select('expenses_and_purchases.rate','expenses_and_purchases.date','expenses_and_purchases.id','expenses_and_purchases.serie','providers.razon_social as name_provider','expenses_and_purchases.amount','expenses_and_purchases.amount_with_iva', DB::raw('SUM(anticipos.amount/anticipos.rate) As amount_anticipo'))
                                    ->groupBy('expenses_and_purchases.rate','expenses_and_purchases.date','expenses_and_purchases.id','expenses_and_purchases.serie','providers.razon_social','expenses_and_purchases.amount','expenses_and_purchases.amount_with_iva')
                                    ->get();
            }
           
        }else{
            if((isset($coin)) && ($coin == "bolivares")){
                $expenses = DB::connection(Auth::user()->database_name)->table('expenses_and_purchases')
                                    ->join('providers', 'providers.id','=','expenses_and_purchases.id_provider')
                                    ->leftjoin('anticipos', 'anticipos.id_expense','=','expenses_and_purchases.id')
                                    ->whereIn('expenses_and_purchases.status',[1,'P'])
                                    ->where('expenses_and_purchases.amount','<>',null)
                                    ->where('expenses_and_purchases.date','<=',$date_consult)
                                    ->select('expenses_and_purchases.rate','expenses_and_purchases.date','expenses_and_purchases.id','expenses_and_purchases.serie','providers.razon_social as name_provider','expenses_and_purchases.amount','expenses_and_purchases.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                                    ->groupBy('expenses_and_purchases.rate','expenses_and_purchases.date','expenses_and_purchases.id','expenses_and_purchases.serie','providers.razon_social','expenses_and_purchases.amount','expenses_and_purchases.amount_with_iva')
                                    ->get();
            }else{
                $expenses = DB::connection(Auth::user()->database_name)->table('expenses_and_purchases')
                                    ->join('providers', 'providers.id','=','expenses_and_purchases.id_provider')
                                    ->leftjoin('anticipos', 'anticipos.id_expense','=','expenses_and_purchases.id')
                                    ->whereIn('expenses_and_purchases.status',[1,'P'])
                                    ->where('expenses_and_purchases.amount','<>',null)
                                    ->where('expenses_and_purchases.date','<=',$date_consult)
                                    ->select('expenses_and_purchases.rate','expenses_and_purchases.date','expenses_and_purchases.id','expenses_and_purchases.serie','providers.razon_social as name_provider','expenses_and_purchases.amount','expenses_and_purchases.amount_with_iva', DB::raw('SUM(anticipos.amount/anticipos.rate) As amount_anticipo'))
                                    ->groupBy('expenses_and_purchases.rate','expenses_and_purchases.date','expenses_and_purchases.id','expenses_and_purchases.serie','providers.razon_social','expenses_and_purchases.amount','expenses_and_purchases.amount_with_iva')
                                    ->get();
            }
        }
        
        $pdf = $pdf->loadView('admin.reports.debtstopay',compact('expenses','datenow','date_end','coin'));
        return $pdf->stream();
                 
    }


    function ledger_pdf($date_begin = null,$date_end = null)
    {
      
        $pdf = App::make('dompdf.wrapper');

        $company = Company::on(Auth::user()->database_name)->find(1);
        
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d'); 
        $period = $date->format('Y'); 
       
        if(isset($date_begin)){
            $from = $date_begin;
        }
        if(isset($date_end)){
            $to = $date_end;
        }else{
            $to = $datenow;
        }

        $details = DB::connection(Auth::user()->database_name)->table('detail_vouchers')
                ->join('accounts', 'accounts.id','=','detail_vouchers.id_account')
                ->join('header_vouchers', 'header_vouchers.id','=','detail_vouchers.id_header_voucher')
                ->select('accounts.code_one','accounts.code_two','accounts.code_three'
                        ,'accounts.code_four','accounts.code_five','accounts.description as account_description'
                        ,'detail_vouchers.debe','detail_vouchers.haber'
                        ,'header_vouchers.description as header_description'
                        ,'header_vouchers.id as id_header'
                        ,'header_vouchers.date as date')
                ->orderBy('accounts.code_one','asc')
                ->orderBy('accounts.code_two','asc')
                ->orderBy('accounts.code_three','asc')
                ->orderBy('accounts.code_four','asc')
                ->orderBy('accounts.code_five','asc')
                ->get();
        
        $pdf = $pdf->loadView('admin.reports.ledger',compact('company','datenow','details','date_begin','date_end'));
        return $pdf->stream();
                 
    }


 

    function accounts_pdf($coin,$level,$date_begin = null,$date_end = null)
    {
        
        $pdf = App::make('dompdf.wrapper');

        
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d'); 
        $period = $date->format('Y'); 
        $detail_old = DetailVoucher::on(Auth::user()->database_name)->orderBy('created_at','asc')->first();
        
        
        if(isset($date_begin)){
            $from = $date_begin;
        }else{
            $from = $detail_old->created_at->format('Y-m-d');
            
        }
        if(isset($date_end)){
            $to = $date_end;
        }else{
            $to = $datenow;
        }

        if(empty($level)){
            $level = 5;
        }


        if(isset($coin) && ($coin == "bolivares")){
            $accounts_all = $this->calculation($from,$to);
        }else{
            $accounts_all = $this->calculation_dolar("dolares");
        }
     
        $accounts = $accounts_all->filter(function($account) use ($level)
        {
          
            if($account->level <= $level){
                //aqui se valida que la cuentas de code_one de 4 para arriba no se toma en cuenta el balance previo
                if($account->code_one <= 3){
                    $total = $account->balance_previus + $account->debe - $account->haber;
                }else{
                    $total = $account->debe - $account->haber;
                }
                
                if ($total != 0) {
                    return $account;
                }
            }
            
        });

        
        
        $pdf = $pdf->loadView('admin.reports.accounts',compact('coin','datenow','accounts','level','detail_old','date_begin','date_end'))->setPaper('a4', 'landscape');
        return $pdf->stream();
                 
    }

    function bankmovements_pdf($type,$coin,$date_begin,$date_end,$account_bank = null)
    {
        
        $pdf = App::make('dompdf.wrapper');
        
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d'); 
        $period = $date->format('Y'); 

        
        if(isset($account_bank)){

            if(isset($type) && ($type == 'Todo')){
                
                    $details_banks =   DB::connection(Auth::user()->database_name)->select(
                        'SELECT d.* ,h.description as header_description,h.id as header_id, 
                        h.reference as header_reference,h.date as header_date,
                        a.description as account_description,a.code_one as account_code_one,
                        a.code_two as account_code_two,a.code_three as account_code_three,
                        a.code_four as account_code_four,a.code_five as account_code_five
                        FROM header_vouchers h
                        INNER JOIN detail_vouchers d 
                            ON d.id_header_voucher = h.id
                        INNER JOIN accounts a
                            ON d.id_account = a.id


                        WHERE d.id_header_voucher IN ( SELECT de.id_header_voucher FROM detail_vouchers de WHERE de.id_account = ? ) AND
                        (DATE_FORMAT(d.created_at, "%Y-%m-%d") >= ? AND DATE_FORMAT(d.created_at, "%Y-%m-%d") <= ?) AND
                        (h.description LIKE "Deposito%" OR
                        h.description LIKE "Retiro%" OR
                        h.description LIKE "Transferencia%")'
                        , [$account_bank,$date_begin, $date_end]);
                

            }else if (isset($type)){
               
                $details_banks =   DB::connection(Auth::user()->database_name)->select(
                    'SELECT d.* ,h.description as header_description,h.id as header_id, 
                    h.reference as header_reference,h.date as header_date,
                    a.description as account_description,a.code_one as account_code_one,
                    a.code_two as account_code_two,a.code_three as account_code_three,
                    a.code_four as account_code_four,a.code_five as account_code_five
                    FROM header_vouchers h
                    INNER JOIN detail_vouchers d 
                        ON d.id_header_voucher = h.id
                    INNER JOIN accounts a
                        ON d.id_account = a.id
    
    
                    WHERE d.id_header_voucher IN ( SELECT de.id_header_voucher FROM detail_vouchers de WHERE de.id_account = ? ) AND
                    (DATE_FORMAT(d.created_at, "%Y-%m-%d") >= ? AND DATE_FORMAT(d.created_at, "%Y-%m-%d") <= ?) AND
                    (h.description LIKE ?)'
                    , [$account_bank,$date_begin, $date_end,$type."%"]);
                
            }
            
        }else{
            if(isset($type) && ($type == 'Todo')){
                $details_banks = DB::connection(Auth::user()->database_name)->table('detail_vouchers')
                ->join('header_vouchers', 'header_vouchers.id', '=', 'detail_vouchers.id_header_voucher')
                ->join('accounts', 'accounts.id', '=', 'detail_vouchers.id_account')
                ->whereRaw(
                    "(DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') <= ?)", 
                    [$date_begin, $date_end])
                ->where(function($query) {
                    $query->where('header_vouchers.description','LIKE','Deposito%')
                        ->orwhere('header_vouchers.description','LIKE','Retiro%')
                        ->orwhere('header_vouchers.description','LIKE','Transferencia%');
                })
                ->select('detail_vouchers.*','header_vouchers.description as header_description','header_vouchers.id as header_id', 
                'header_vouchers.reference as header_reference','header_vouchers.date as header_date',
                'accounts.description as account_description','accounts.code_one as account_code_one',
                'accounts.code_two as account_code_two','accounts.code_three as account_code_three',
                'accounts.code_four as account_code_four','accounts.code_five as account_code_five')
                ->orderBy('header_vouchers.id','desc')
                ->get();
            }else if (isset($type)){
                $details_banks = DB::connection(Auth::user()->database_name)->table('detail_vouchers')
                ->join('header_vouchers', 'header_vouchers.id', '=', 'detail_vouchers.id_header_voucher')
                ->join('accounts', 'accounts.id', '=', 'detail_vouchers.id_account')
                ->whereRaw(
                    "(DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') <= ?)", 
                    [$date_begin, $date_end])
                ->where('header_vouchers.description','LIKE',$type.'%')
                ->select('detail_vouchers.*','header_vouchers.description as header_description','header_vouchers.id as header_id', 
                'header_vouchers.reference as header_reference','header_vouchers.date as header_date',
                'accounts.description as account_description','accounts.code_one as account_code_one',
                'accounts.code_two as account_code_two','accounts.code_three as account_code_three',
                'accounts.code_four as account_code_four','accounts.code_five as account_code_five')
                ->orderBy('header_vouchers.id','desc')
                ->get();
            }
        }
        
       
        
        $pdf = $pdf->loadView('admin.reports.bankmovements',compact('details_banks','coin','datenow','date_begin','date_end'))->setPaper('a4', 'landscape');
        return $pdf->stream();
                 
    }

    function sales_books_pdf($coin,$date_begin,$date_end)
    {
        
        $pdf = App::make('dompdf.wrapper');

        
        $date = Carbon::now();
        $datenow = $date->format('d-m-Y'); 
        $period = $date->format('Y'); 
        $quotations = Quotation::on(Auth::user()->database_name)
                                    ->where('date_billing','<>',null)
                                    ->whereRaw(
                                        "(DATE_FORMAT(date_billing, '%Y-%m-%d') >= ? AND DATE_FORMAT(date_billing, '%Y-%m-%d') <= ?)", 
                                        [$date_begin, $date_end])
                                    ->orderBy('date_billing','desc')->get();

        $date_begin = Carbon::parse($date_begin);
        $date_begin = $date_begin->format('d-m-Y');
        $date_end = Carbon::parse($date_end);
        $date_end = $date_end->format('d-m-Y');


        $pdf = $pdf->loadView('admin.reports.sales_books',compact('coin','quotations','datenow','date_begin','date_end'))->setPaper('a4', 'landscape');
        return $pdf->stream();
                 
    }

    function purchases_book_pdf($coin,$date_begin,$date_end)
    {
        
        $pdf = App::make('dompdf.wrapper');

        $date = Carbon::now();
        $datenow = $date->format('d-m-Y'); 
        $period = $date->format('Y'); 
        $expenses = ExpensesAndPurchase::on(Auth::user()->database_name)
                                    ->where('amount','<>',null)
                                    ->whereRaw(
                                        "(DATE_FORMAT(date, '%Y-%m-%d') >= ? AND DATE_FORMAT(date, '%Y-%m-%d') <= ?)", 
                                        [$date_begin, $date_end])
                                    ->orderBy('date','desc')->get();

        $total_exento = ExpensesDetail::on(Auth::user()->database_name)
                                    ->where('exento','1')
                                    ->whereRaw(
                                        "(DATE_FORMAT(created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(created_at, '%Y-%m-%d') <= ?)", 
                                        [$date_begin, $date_end])
                                    ->select(DB::connection(Auth::user()->database_name)->raw('SUM(price*amount) as total'))->get();

        $total_exento = $total_exento[0]->total;

        $date_begin = Carbon::parse($date_begin);
        $date_begin = $date_begin->format('d-m-Y');
        $date_end = Carbon::parse($date_end);
        $date_end = $date_end->format('d-m-Y');


        $pdf = $pdf->loadView('admin.reports.purchases_books',compact('coin','expenses','datenow','date_begin','date_end','total_exento'))->setPaper('a4', 'landscape');
        return $pdf->stream();
                 
    }

    function inventory_pdf($coin,$date_begin,$date_end,$name = null)
    {
        
        $pdf = App::make('dompdf.wrapper');

        $date = Carbon::now();
        $datenow = $date->format('d-m-Y'); 
        $period = $date->format('Y'); 


        if(isset($name)){
            $products = Product::on(Auth::user()->database_name)
            ->join('inventories', 'inventories.product_id', '=', 'products.id')
            ->where('products.description','LIKE',$name.'%')
            ->whereRaw(
                "(DATE_FORMAT(products.created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(products.created_at, '%Y-%m-%d') <= ?)", 
                [$date_begin, $date_end])
            ->orderBy('products.description','asc')->get();
        }else{
            $products = Product::on(Auth::user()->database_name)
            ->join('inventories', 'inventories.product_id', '=', 'products.id')
            ->whereRaw(
                "(DATE_FORMAT(products.created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(products.created_at, '%Y-%m-%d') <= ?)", 
                [$date_begin, $date_end])
            ->orderBy('products.description','asc')->get();
        }
        

        $date_begin = Carbon::parse($date_begin);
        $date_begin = $date_begin->format('d-m-Y');

        $date_end = Carbon::parse($date_end);
        $date_end = $date_end->format('d-m-Y');

        $company = Company::on(Auth::user()->database_name)->find(1);
        //Si la taza es automatica
        if($company->tiporate_id == 1){
            $rate = $this->search_bcv();
        }else{
            //si la tasa es fija
            $rate = $company->rate;
        }

        $pdf = $pdf->loadView('admin.reports.inventory',compact('rate','coin','products','datenow','date_begin','date_end'))->setPaper('a4', 'landscape');
        return $pdf->stream();
                 
    }

    function operating_margin_pdf($coin,$date_begin,$date_end)
    {
        
        $pdf = App::make('dompdf.wrapper');

        $date = Carbon::now();
        $datenow = $date->format('d-m-Y'); 
        $period = $date->format('Y'); 

        $date_begin = Carbon::parse($date_begin);
        $from = $date_begin->format('Y-m-d');
        $date_begin = $date_begin->format('d-m-Y');

        $date_end = Carbon::parse($date_end);
        $to = $date_end->format('Y-m-d');
        $date_end = $date_end->format('d-m-Y');

       

        if(isset($coin) && ($coin == "bolivares")){
            $accounts_all = $this->calculation($from,$to);
        }else{
            $accounts_all = $this->calculation_dolar("dolares");
        }
      
        $ventas = 0;
        $costos = 0;
        $gastos = 0;
        $utilidad = 0;
        $margen_operativo = 0;
        $gastos_costos = 0;
        $rentabilidad = 0;


        foreach($accounts_all as $account){
            if(($account->code_one == 4) && ($account->code_two == 0) && ($account->code_three == 0) && ($account->code_four == 0) && ($account->code_five == 0) ){
                $ventas = $account->debe - $account->haber;
            }
            if(($account->code_one == 5) && ($account->code_two == 0) && ($account->code_three == 0) && ($account->code_four == 0) && ($account->code_five == 0) ){
                $costos = $account->debe - $account->haber;
            }
            if(($account->code_one == 6) && ($account->code_two == 0) && ($account->code_three == 0) && ($account->code_four == 0) && ($account->code_five == 0) ){
                $gastos = $account->debe - $account->haber;
            }
        }

        $ventas = $ventas * -1;

        $utilidad = $ventas - $costos - $gastos;
        $gastos_costos = $gastos + $costos;

        if(($utilidad > 0) && ($ventas >0)){
            $margen_operativo = ($utilidad / $ventas) * 100;
        }else{
            
            if(($utilidad > 0)){
                $margen_operativo = $utilidad;
            }else{
                $margen_operativo = $ventas;
            }
        }

        //RENTABILIDAD
        if(($utilidad > 0) && ($gastos_costos > 0)){
            $rentabilidad = ($utilidad/$gastos_costos) * 100;
        }else{
            
            if(($utilidad > 0)){
                $margen_operativo = $utilidad * 100;
            }else{
                $margen_operativo = $gastos_costos * 100;
            }
        }
       
        $pdf = $pdf->loadView('admin.reports.operating_margin',compact('rentabilidad','margen_operativo','utilidad','ventas','costos','gastos','coin','datenow','date_begin','date_end'));
        return $pdf->stream();
                 
    }

    function clients_pdf($date_begin,$date_end,$name = null)
    {
        
        $pdf = App::make('dompdf.wrapper');

        $date = Carbon::now();
        $datenow = $date->format('d-m-Y'); 
        $period = $date->format('Y'); 


        if(isset($name)){
            $clients = Client::on(Auth::user()->database_name)
            ->where('name','LIKE',$name.'%')
            ->whereRaw(
                "(DATE_FORMAT(created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(created_at, '%Y-%m-%d') <= ?)", 
                [$date_begin, $date_end])
            ->orderBy('name','asc')->get();
        }else{
            $clients = Client::on(Auth::user()->database_name)
            ->whereRaw(
                "(DATE_FORMAT(created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(created_at, '%Y-%m-%d') <= ?)", 
                [$date_begin, $date_end])
            ->orderBy('name','asc')->get();
        }
        

        $date_begin = Carbon::parse($date_begin);
        $date_begin = $date_begin->format('d-m-Y');

        $date_end = Carbon::parse($date_end);
        $date_end = $date_end->format('d-m-Y');

       
        $pdf = $pdf->loadView('admin.reports.clients',compact('clients','datenow','date_begin','date_end'))->setPaper('a4', 'landscape');
        return $pdf->stream();
                 
    }

    function providers_pdf($date_begin,$date_end,$name = null)
    {
        
        $pdf = App::make('dompdf.wrapper');

        $date = Carbon::now();
        $datenow = $date->format('d-m-Y'); 
        $period = $date->format('Y'); 


        if(isset($name)){
            $providers = Provider::on(Auth::user()->database_name)
            ->where('razon_social','LIKE',$name.'%')
            ->whereRaw(
                "(DATE_FORMAT(created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(created_at, '%Y-%m-%d') <= ?)", 
                [$date_begin, $date_end])
            ->orderBy('razon_social','asc')->get();
        }else{
            $providers = Provider::on(Auth::user()->database_name)
            ->whereRaw(
                "(DATE_FORMAT(created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(created_at, '%Y-%m-%d') <= ?)", 
                [$date_begin, $date_end])
            ->orderBy('razon_social','asc')->get();
        }
        

        $date_begin = Carbon::parse($date_begin);
        $date_begin = $date_begin->format('d-m-Y');

        $date_end = Carbon::parse($date_end);
        $date_end = $date_end->format('d-m-Y');

       
        $pdf = $pdf->loadView('admin.reports.providers',compact('providers','datenow','date_begin','date_end'))->setPaper('a4', 'landscape');
        return $pdf->stream();
                 
    }

    function employees_pdf($date_begin,$date_end,$name = null)
    {
        
        $pdf = App::make('dompdf.wrapper');

        $date = Carbon::now();
        $datenow = $date->format('d-m-Y'); 
        $period = $date->format('Y'); 


        if(isset($name)){
            $employees = Employee::on(Auth::user()->database_name)
            ->where('nombres','LIKE',$name.'%')
            ->whereRaw(
                "(DATE_FORMAT(created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(created_at, '%Y-%m-%d') <= ?)", 
                [$date_begin, $date_end])
            ->orderBy('nombres','asc')->get();
        }else{
            $employees = Employee::on(Auth::user()->database_name)
            ->whereRaw(
                "(DATE_FORMAT(created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(created_at, '%Y-%m-%d') <= ?)", 
                [$date_begin, $date_end])
            ->orderBy('nombres','asc')->get();
        }
        

        $date_begin = Carbon::parse($date_begin);
        $date_begin = $date_begin->format('d-m-Y');

        $date_end = Carbon::parse($date_end);
        $date_end = $date_end->format('d-m-Y');

       
        $pdf = $pdf->loadView('admin.reports.employees',compact('employees','datenow','date_begin','date_end'))->setPaper('a4', 'landscape');
        return $pdf->stream();
                 
    }

    function sales_pdf($coin,$date_begin,$date_end,$name = null)
    {
        
        $pdf = App::make('dompdf.wrapper');

        $date = Carbon::now();
        $datenow = $date->format('d-m-Y'); 
        $period = $date->format('Y'); 

        if(isset($name)){
            $sales = Product::on(Auth::user()->database_name)
            ->join('inventories', 'inventories.product_id', '=', 'products.id')
            ->join('quotation_products', 'quotation_products.id_inventory', '=', 'inventories.id')
            ->join('segments', 'segments.id', '=', 'products.segment_id')
            ->leftjoin('subsegments', 'subsegments.id', '=', 'products.subsegment_id')
            ->where('quotation_products.status','C')
            ->where('products.description','LIKE',$name.'%')
            ->whereRaw(
                "(DATE_FORMAT(quotation_products.created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotation_products.created_at, '%Y-%m-%d') <= ?)", 
                [$date_begin, $date_end])
            ->select('products.description', DB::connection(Auth::user()->database_name)->raw('SUM(quotation_products.amount) as amount_sales'), DB::connection(Auth::user()->database_name)->raw('SUM(quotation_products.price*quotation_products.amount) as price_sales'), DB::connection(Auth::user()->database_name)->raw('SUM(quotation_products.price*quotation_products.amount/quotation_products.rate) as price_sales_dolar'),'products.type','products.price as price','products.price_buy as price_buy','inventories.code','products.money as money','segments.description as segment_description','subsegments.description as subsegment_description')
            ->groupBy('products.description','products.type','products.price','products.price_buy','inventories.code','products.money','segments.description','subsegments.description')
            ->orderBy('products.description','asc')->get();
           
        }else{
            $sales = Product::on(Auth::user()->database_name)
            ->join('inventories', 'inventories.product_id', '=', 'products.id')
            ->join('quotation_products', 'quotation_products.id_inventory', '=', 'inventories.id')
            ->join('segments', 'segments.id', '=', 'products.segment_id')
            ->leftjoin('subsegments', 'subsegments.id', '=', 'products.subsegment_id')
            ->where('quotation_products.status','C')
            ->whereRaw(
                "(DATE_FORMAT(quotation_products.created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(quotation_products.created_at, '%Y-%m-%d') <= ?)", 
                [$date_begin, $date_end])
            ->select('products.description', DB::connection(Auth::user()->database_name)->raw('SUM(quotation_products.amount) as amount_sales'), DB::connection(Auth::user()->database_name)->raw('SUM(quotation_products.price*quotation_products.amount) as price_sales'), DB::connection(Auth::user()->database_name)->raw('SUM(quotation_products.price*quotation_products.amount/quotation_products.rate) as price_sales_dolar'),'products.type','products.price as price','products.price_buy as price_buy','inventories.code','products.money as money','segments.description as segment_description','subsegments.description as subsegment_description')
            ->groupBy('products.description','products.type','products.price','products.price_buy','inventories.code','products.money','segments.description','subsegments.description')
            ->orderBy('products.description','asc')->get();
        }
        
        $date_begin = Carbon::parse($date_begin);
        $date_begin = $date_begin->format('d-m-Y');

        $date_end = Carbon::parse($date_end);
        $date_end = $date_end->format('d-m-Y');

        $company = Company::on(Auth::user()->database_name)->find(1);
        //Si la taza es automatica
        if($company->tiporate_id == 1){
            $rate = $this->search_bcv();
        }else{
            //si la tasa es fija
            $rate = $company->rate;
        }

       
        $pdf = $pdf->loadView('admin.reports.sales',compact('coin','rate','sales','datenow','date_begin','date_end'))->setPaper('a4', 'landscape');
        return $pdf->stream();
                 
    }

    function accounts_receivable_pdf($coin,$date_end,$typeinvoice,$typeperson,$id_client_or_vendor = null)
    {
        
        $pdf = App::make('dompdf.wrapper');
        $quotations = null;
        
        $date = Carbon::now();
        $datenow = $date->format('d-m-Y'); 
        if(empty($date_end)){
            $date_end = $datenow;

            $date_consult = $date->format('Y-m-d'); 
        }else{
            $date_end = Carbon::parse($date_end)->format('d-m-Y');

            $date_consult = Carbon::parse($date_end)->format('Y-m-d');
        }
        
        $period = $date->format('Y'); 
        

        if(isset($typeperson) && ($typeperson == 'Cliente')){
            if(isset($coin) && $coin == 'bolivares'){
                if(isset($typeinvoice) && ($typeinvoice == 'notas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P'])
                    ->where('quotations.amount','<>',null)
                    ->where('quotations.date_quotation','<=',$date_consult)
                    ->where('quotations.date_delivery_note','<>',null)
                    ->where('quotations.date_billing',null)
                    
                    ->where('quotations.id_client',$id_client_or_vendor)
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_delivery_note','desc')
                    ->get();
                }else if(isset($typeinvoice) && ($typeinvoice == 'facturas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P'])
                    ->where('quotations.amount','<>',null)
                    ->where('quotations.date_quotation','<=',$date_consult)
                    ->where('quotations.date_billing','<>',null)
                    ->where('quotations.id_client',$id_client_or_vendor)
                    
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_billing','desc')
                    ->get();
                }else
                {
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                                         ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                                        ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                                        ->whereIn('quotations.status',[1,'P'])
                                        ->where('quotations.amount','<>',null)
                                        ->where('quotations.date_quotation','<=',$date_consult)
                                        ->where('quotations.id_client',$id_client_or_vendor)
                                        
                                        ->select('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                                        ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                                        ->orderBy('quotations.date_quotation','desc')
                                        ->get();
                }
            }else{
                //PARA CUANDO EL REPORTE ESTE EN DOLARES
                if(isset($typeinvoice) && ($typeinvoice == 'notas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P'])
                    ->where('quotations.amount','<>',null)
                    ->where('quotations.date_quotation','<=',$date_consult)
                    ->where('quotations.date_delivery_note','<>',null)
                    ->where('quotations.date_billing',null)
                    ->where('quotations.id_client',$id_client_or_vendor)
                    
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_delivery_note','desc')
                    ->get();
                }else if(isset($typeinvoice) && ($typeinvoice == 'facturas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                                        ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P'])
                    ->where('quotations.amount','<>',null)
                    ->where('quotations.date_quotation','<=',$date_consult)
                    ->where('quotations.date_billing','<>',null)
                    ->where('quotations.id_client',$id_client_or_vendor)
                    
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_billing','desc')
                    ->get();
                }else
                {
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                                         ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                                        ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                                        ->whereIn('quotations.status',[1,'P'])
                                        ->where('quotations.amount','<>',null)
                                        ->where('quotations.date_quotation','<=',$date_consult)
                                        ->where('quotations.id_client',$id_client_or_vendor)
                                        
                                        ->select('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                                        ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                                        ->orderBy('quotations.date_quotation','desc')
                                        ->get();
                }
            }
        }else if(isset($typeperson) && $typeperson == 'Vendedor'){
            if(isset($coin) && $coin == 'bolivares'){
                if(isset($typeinvoice) && ($typeinvoice == 'notas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P'])
                    ->where('quotations.amount','<>',null)
                    ->where('quotations.date_quotation','<=',$date_consult)
                    ->where('quotations.date_delivery_note','<>',null)
                    ->where('quotations.date_billing',null)
                    ->where('quotations.id_vendor',$id_client_or_vendor)
                    
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_delivery_note','desc')
                    ->get();
                }else if(isset($typeinvoice) && ($typeinvoice == 'facturas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P'])
                    ->where('quotations.amount','<>',null)
                    ->where('quotations.date_quotation','<=',$date_consult)
                    ->where('quotations.date_billing','<>',null)
                    ->where('quotations.id_vendor',$id_client_or_vendor)
                    
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_billing','desc')
                    ->get();
                }else
                {
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                    ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P'])
                    ->where('quotations.amount','<>',null)
                    ->where('quotations.date_quotation','<=',$date_consult)
                    ->where('quotations.id_vendor',$id_client_or_vendor)
                    
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_quotation','desc')
                    ->get();
                }
            }else{
                
                //PARA CUANDO EL REPORTE ESTE EN DOLARES
                if(isset($typeinvoice) && ($typeinvoice == 'notas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P'])
                    ->where('quotations.amount','<>',null)
                    ->where('quotations.date_quotation','<=',$date_consult)
                    ->where('quotations.date_delivery_note','<>',null)
                    ->where('quotations.date_billing',null)
                    ->where('quotations.id_vendor',$id_client_or_vendor)
                    
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_delivery_note','desc')
                    ->get();
                }else if(isset($typeinvoice) && ($typeinvoice == 'facturas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P'])
                    ->where('quotations.amount','<>',null)
                    ->where('quotations.date_quotation','<=',$date_consult)
                    ->where('quotations.date_billing','<>',null)
                    ->where('quotations.id_vendor',$id_client_or_vendor)
                    
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_billing','desc')
                    ->get();
                }else
                {
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                    ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P'])
                    ->where('quotations.amount','<>',null)
                    ->where('quotations.date_quotation','<=',$date_consult)
                    ->where('quotations.id_vendor',$id_client_or_vendor)
                    
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount / anticipos.rate) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_quotation','desc')
                    ->get();
                }
            }
        }else{
            
            if(isset($coin) && $coin == 'bolivares'){
                if(isset($typeinvoice) && ($typeinvoice == 'notas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P'])
                    ->where('quotations.amount','<>',null)
                    ->where('quotations.date_quotation','<=',$date_consult)
                    ->where('quotations.date_delivery_note','<>',null)
                    ->where('quotations.date_billing',null)
                    
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_delivery_note','desc')
                    ->get();
                }else if(isset($typeinvoice) && ($typeinvoice == 'facturas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                     ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P'])
                    ->where('quotations.amount','<>',null)
                    ->where('quotations.date_quotation','<=',$date_consult)
                    ->where('quotations.date_billing','<>',null)
                    
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_billing','desc')
                    ->get();
                }else
                {
                   
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                                        ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                                        ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                                        ->whereIn('quotations.status',[1,'P'])
                                        ->where('quotations.amount','<>',null)
                                        ->where('quotations.date_quotation','<=',$date_consult)
                                        
                                        ->select('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount) As amount_anticipo'))
                                        ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                                        ->orderBy('quotations.date_quotation','desc')
                                        ->get();

                    
                }
            }else{
                
                //PARA CUANDO EL REPORTE ESTE EN DOLARES
                if(isset($typeinvoice) && ($typeinvoice == 'notas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                    ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P'])
                    ->where('quotations.amount','<>',null)
                    ->where('quotations.date_quotation','<=',$date_consult)
                    ->where('quotations.date_delivery_note','<>',null)
                    ->where('quotations.date_billing',null)
                    
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount/anticipos.rate) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_delivery_note','desc')
                    ->get();
                }else if(isset($typeinvoice) && ($typeinvoice == 'facturas')){
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                    ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                    ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                    ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                    ->whereIn('quotations.status',[1,'P'])
                    ->where('quotations.amount','<>',null)
                    ->where('quotations.date_quotation','<=',$date_consult)
                    ->where('quotations.date_billing','<>',null)
                    
                    ->select('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount/anticipos.rate) As amount_anticipo'))
                    ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                    ->orderBy('quotations.date_billing','desc')
                    ->get();
                }else
                {
                    $quotations = DB::connection(Auth::user()->database_name)->table('quotations')
                                        ->leftjoin('clients', 'clients.id','=','quotations.id_client')
                                        ->leftjoin('vendors', 'vendors.id','=','quotations.id_vendor')
                                        ->leftjoin('anticipos', 'anticipos.id_quotation','=','quotations.id')
                                        ->whereIn('quotations.status',[1,'P'])
                                        ->where('quotations.amount','<>',null)
                                        ->where('quotations.date_quotation','<=',$date_consult)
                                        
                                        ->select('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name as name_vendor','clients.name as name_client','quotations.amount','quotations.amount_with_iva', DB::raw('SUM(anticipos.amount/anticipos.rate) As amount_anticipo'))
                                        ->groupBy('quotations.date_billing','quotations.date_delivery_note','quotations.retencion_islr','quotations.retencion_iva','quotations.bcv','quotations.number_invoice','quotations.number_delivery_note','quotations.date_quotation','quotations.id','quotations.serie','vendors.name','clients.name','quotations.amount','quotations.amount_with_iva')
                                        ->orderBy('quotations.date_quotation','desc')
                                        ->get();
                }
            }
        }
        
        $pdf = $pdf->loadView('admin.reports.accounts_receivable',compact('coin','quotations','datenow','date_end'));
        return $pdf->stream();
                 
    }


    function retencion_iva_expense($date_begin = null,$date_end = null,$level = null)
    {
      
        $pdf = App::make('dompdf.wrapper');

        
        $date = Carbon::now();
        $datenow = $date->format('Y-m-d'); 
        $period = $date->format('Y'); 
        $detail_old = DetailVoucher::on(Auth::user()->database_name)->orderBy('created_at','asc')->first();

        if(isset($date_begin)){
            $from = $date_begin;
        }else{
            $from = $detail_old->created_at->format('Y-m-d');
        }
        if(isset($date_end)){
            $to = $date_end;
        }else{
            $to = $datenow;
        }
        if(isset($level)){
            
        }else{
            $level = 5;
        }

        $accounts_all = $this->calculation($from,$to);

        $accounts = $accounts_all->filter(function($account)
        {
            if($account->code_one <= 3){
                $total = $account->balance_previus + $account->debe - $account->haber;
                if ($total != 0) {
                    return $account;
                }
            }
            
        });

        $pdf = $pdf->loadView('admin.reports.balance_general',compact('datenow','accounts','level','detail_old','date_begin','date_end'));
        return $pdf->stream();
                 
    }
    //agregar que retorne el monto en dolares
    public function calculation($date_begin,$date_end)
    {
        
        //dd($date_begin);
        $accounts = Account::on(Auth::user()->database_name)->orderBy('code_one', 'asc')
                         ->orderBy('code_two', 'asc')
                         ->orderBy('code_three', 'asc')
                         ->orderBy('code_four', 'asc')
                         ->orderBy('code_five', 'asc')
                         ->get();

                       
                         if(isset($accounts)) {
                            foreach ($accounts as $var) {
                 
                                    
                                if($var->code_one != 0){
                                    
                                    if($var->code_two != 0){
                    
                    
                                        if($var->code_three != 0){
                    
                    
                                            if($var->code_four != 0){

                                                if($var->code_five != 0){
                                                    //Calculo de superavit
                                                    if(($var->code_one == 3) && ($var->code_two == 2) && ($var->code_three == 1) && 
                                                    ($var->code_four == 1) && ($var->code_five == 1) ){
                                                        $var = $this->calculation_superavit($var,4,'bolivares',$date_begin,$date_end);
                                                    }else{
                                                        /*CALCULA LOS SALDOS DESDE DETALLE COMPROBANTE */                                                   
                                                        $total_debe = DB::connection(Auth::user()->database_name)->table('accounts')
                                                        ->join('detail_vouchers', 'detail_vouchers.id_account', '=', 'accounts.id')
                                                        ->where('accounts.code_one', $var->code_one)
                                                        ->where('accounts.code_two', $var->code_two)
                                                        ->where('accounts.code_three', $var->code_three)
                                                        ->where('accounts.code_four', $var->code_four)
                                                        ->where('accounts.code_five', $var->code_five)
                                                        ->where('detail_vouchers.status', 'C')
                                                        //->whereBetween('detail_vouchers.created_at', [$date_begin, $date_end])
                                                        ->whereRaw(
                                                        "(DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') <= ?)", 
                                                        [$date_begin, $date_end])
                                                        ->sum('debe');
                                                        
                                                       

                                                        $total_haber = DB::connection(Auth::user()->database_name)->table('accounts')
                                                        ->join('detail_vouchers', 'detail_vouchers.id_account', '=', 'accounts.id')
                                                        ->where('accounts.code_one', $var->code_one)
                                                        ->where('accounts.code_two', $var->code_two)
                                                        ->where('accounts.code_three', $var->code_three)
                                                        ->where('accounts.code_four', $var->code_four)
                                                        ->where('accounts.code_five', $var->code_five)
                                                        ->where('detail_vouchers.status', 'C')
                                                        //->whereBetween('detail_vouchers.created_at', [$date_begin, $date_end])
                                                        ->whereRaw(
                                                        "(DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') <= ?)", 
                                                        [$date_begin, $date_end])
                                                        ->sum('haber');   

                                                        
                                                        /*---------------------------------------------------*/

                                                

                                                        $var->debe = $total_debe;
                                                        $var->haber = $total_haber;
                                                    }
                                                }else
                                                {
                                                    if(($var->code_one == 3) && ($var->code_two == 2) && ($var->code_three == 1) && 
                                                    ($var->code_four == 1)){
                                                        $var = $this->calculation_superavit($var,4,'bolivares',$date_begin,$date_end);
                                                    }else{
                                                            /*CALCULA LOS SALDOS DESDE DETALLE COMPROBANTE */                                                   
                                                            $total_debe = DB::connection(Auth::user()->database_name)->table('accounts')
                                                                                ->join('detail_vouchers', 'detail_vouchers.id_account', '=', 'accounts.id')
                                                                                ->where('accounts.code_one', $var->code_one)
                                                                                ->where('accounts.code_two', $var->code_two)
                                                                                ->where('accounts.code_three', $var->code_three)
                                                                                ->where('accounts.code_four', $var->code_four)
                                                                                ->where('detail_vouchers.status', 'C')
                                                                                //->whereBetween('detail_vouchers.created_at', [$date_begin, $date_end])
                                                                                ->whereRaw(
                                                            "(DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') <= ?)", 
                                                            [$date_begin, $date_end])
                                                                                ->sum('debe');
                            
                                                            $total_haber = DB::connection(Auth::user()->database_name)->table('accounts')
                                                                                ->join('detail_vouchers', 'detail_vouchers.id_account', '=', 'accounts.id')
                                                                                ->where('accounts.code_one', $var->code_one)
                                                                                ->where('accounts.code_two', $var->code_two)
                                                                                ->where('accounts.code_three', $var->code_three)
                                                                                ->where('accounts.code_four', $var->code_four)
                                                                                ->where('detail_vouchers.status', 'C')
                                                                                //->whereBetween('detail_vouchers.created_at', [$date_begin, $date_end])
                                                                                ->whereRaw(
                                                            "(DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') <= ?)", 
                                                            [$date_begin, $date_end])
                                                                                ->sum('haber');   

                                                            $total_balance = DB::connection(Auth::user()->database_name)->table('accounts')
                                                                                ->where('accounts.code_one', $var->code_one)
                                                                                ->where('accounts.code_two', $var->code_two)
                                                                                ->where('accounts.code_three', $var->code_three)
                                                                                ->where('accounts.code_four', $var->code_four)
                                                                                ->sum('balance_previus');   
                                                            /*---------------------------------------------------*/
                        
                                                
                        
                                                            $var->debe = $total_debe;
                                                            $var->haber = $total_haber;
                                                            $var->balance_previus = $total_balance;
                        
                                                        }
                                                    }                          
                    
                                            }else{
                                               
                                                if(($var->code_one == 3) && ($var->code_two == 2) && ($var->code_three == 1)){
                                                    $var = $this->calculation_superavit($var,4,'bolivares',$date_begin,$date_end);
                                                }else{
                                          
                                                    /*CALCULA LOS SALDOS DESDE DETALLE COMPROBANTE */ 
                                                        $total_debe = DB::connection(Auth::user()->database_name)->table('accounts')
                                                                        ->join('detail_vouchers', 'detail_vouchers.id_account', '=', 'accounts.id')
                                                                        ->where('accounts.code_one', $var->code_one)
                                                                        ->where('accounts.code_two', $var->code_two)
                                                                        ->where('accounts.code_three', $var->code_three)
                                                                        ->where('detail_vouchers.status', 'C')
                                                                        //->whereBetween('detail_vouchers.created_at', [$date_begin, $date_end])
                                                                        ->whereRaw(
                                                                        "(DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') <= ?)", 
                                                                        [$date_begin, $date_end])
                                                                        ->sum('debe');
                                
                                                        $total_haber =  DB::connection(Auth::user()->database_name)->table('accounts')
                                                                        ->join('detail_vouchers', 'detail_vouchers.id_account', '=', 'accounts.id')
                                                                        ->where('accounts.code_one', $var->code_one)
                                                                        ->where('accounts.code_two', $var->code_two)
                                                                        ->where('accounts.code_three', $var->code_three)
                                                                        ->where('detail_vouchers.status', 'C')
                                                                        //->whereBetween('detail_vouchers.created_at', [$date_begin, $date_end])
                                                                        ->whereRaw(
                                                                        "(DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') <= ?)", 
                                                                        [$date_begin, $date_end])
                                                                        ->sum('haber');    
                                                                        
                                                                        $total_balance = DB::connection(Auth::user()->database_name)->table('accounts')
                                                                        ->where('accounts.code_one', $var->code_one)
                                                                        ->where('accounts.code_two', $var->code_two)
                                                                        ->where('accounts.code_three', $var->code_three)
                                                                        ->sum('balance_previus');   
                                                    /*---------------------------------------------------*/                               
                            
                                                    
                            
                                                    $var->debe = $total_debe;
                                                    $var->haber = $total_haber;       
                                                    $var->balance_previus = $total_balance;
                                                
                                                   }
                                                }
                                }else{
                                    
                                    if(($var->code_one == 3) && ($var->code_two == 2)){
                                        $var = $this->calculation_superavit($var,4,'bolivares',$date_begin,$date_end);
                                    }else{
                                        /*CALCULA LOS SALDOS DESDE DETALLE COMPROBANTE */                                   
                                            $total_debe = DB::connection(Auth::user()->database_name)->table('accounts')
                                                                            ->join('detail_vouchers', 'detail_vouchers.id_account', '=', 'accounts.id')
                                                                            ->where('accounts.code_one', $var->code_one)
                                                                            ->where('accounts.code_two', $var->code_two)
                                                                            ->where('detail_vouchers.status', 'C')
                                                                            //->whereBetween('detail_vouchers.created_at', [$date_begin, $date_end])
                                                                            ->whereRaw(
                                                                            "(DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') <= ?)", 
                                                                            [$date_begin, $date_end])
                                                                            ->sum('debe');
                    
                                        
                                            $total_haber = DB::connection(Auth::user()->database_name)->table('accounts')
                                                                            ->join('detail_vouchers', 'detail_vouchers.id_account', '=', 'accounts.id')
                                                                            ->where('accounts.code_one', $var->code_one)
                                                                            ->where('accounts.code_two', $var->code_two)
                                                                            ->where('detail_vouchers.status', 'C')
                                                                            //->whereBetween('detail_vouchers.created_at', [$date_begin, $date_end])
                                                                            ->whereRaw(
                                                                            "(DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') <= ?)", 
                                                                            [$date_begin, $date_end])
                                                                            ->sum('haber');

                                            $total_balance = DB::connection(Auth::user()->database_name)->table('accounts')
                                                                            ->where('accounts.code_one', $var->code_one)
                                                                            ->where('accounts.code_two', $var->code_two)
                                                                            ->sum('balance_previus'); 
                                        /*---------------------------------------------------*/
                                        
                                        $var->debe = $total_debe;
                                        $var->haber = $total_haber;
                                        $var->balance_previus = $total_balance;
                                    }                                       
                                }
                    }else{
                        //Cuentas NIVEL 2 EJEMPLO 1.0.0.0
                        /*CALCULA LOS SALDOS DESDE DETALLE COMPROBANTE */
                        if($var->code_one == 3){
                            $var = $this->calculation_capital($var,'bolivares',$date_begin,$date_end);
                        
                        }else{
                            $total_debe = DB::connection(Auth::user()->database_name)->table('accounts')
                                                        ->join('detail_vouchers', 'detail_vouchers.id_account', '=', 'accounts.id')
                                                        ->where('accounts.code_one', $var->code_one)
                                                        ->where('detail_vouchers.status', 'C')
                                                        //->whereBetween('detail_vouchers.created_at', [$date_begin, $date_end])
                                                        ->whereRaw(
                                                        "(DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') <= ?)", 
                                                        [$date_begin, $date_end])
                                                        ->sum('debe');
    
                        
                        
                            $total_haber = DB::connection(Auth::user()->database_name)->table('accounts')
                                                        ->join('detail_vouchers', 'detail_vouchers.id_account', '=', 'accounts.id')
                                                        ->where('accounts.code_one', $var->code_one)
                                                        ->where('detail_vouchers.status', 'C')
                                                        //->whereBetween('detail_vouchers.created_at', [$date_begin, $date_end])
                                                        ->whereRaw(
                                                        "(DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') <= ?)", 
                                                        [$date_begin, $date_end])
                                                        ->sum('haber');
                            $total_balance = DB::connection(Auth::user()->database_name)->table('accounts')
                                                        ->where('accounts.code_one', $var->code_one)
                                                        ->sum('balance_previus'); 
                            /*---------------------------------------------------*/

                        
                            $var->debe = $total_debe;
                            $var->haber = $total_haber;           
                            $var->balance_previus = $total_balance;
                        }
                    }
                }else{
                    return redirect('/accounts')->withDanger('El codigo uno es igual a cero!');
                }
            } 
        }  else{
            return redirect('/accounts')->withDanger('No hay Cuentas');
        }              

       
        
         return $accounts;
    }


    public function calculation_capital($var,$coin,$date_begin,$date_end)
    {
        $total_debe = DB::connection(Auth::user()->database_name)->table('accounts')
                                    ->join('detail_vouchers', 'detail_vouchers.id_account', '=', 'accounts.id')
                                    ->where('accounts.code_one','>=', $var->code_one)
                                    ->where('detail_vouchers.status', 'C')
                                    //->whereBetween('detail_vouchers.created_at', [$date_begin, $date_end])
                                    ->whereRaw(
                                    "(DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') <= ?)", 
                                    [$date_begin, $date_end])
                                    ->sum('debe');

    
    
        $total_haber = DB::connection(Auth::user()->database_name)->table('accounts')
                                    ->join('detail_vouchers', 'detail_vouchers.id_account', '=', 'accounts.id')
                                    ->where('accounts.code_one','>=', $var->code_one)
                                    ->where('detail_vouchers.status', 'C')
                                    //->whereBetween('detail_vouchers.created_at', [$date_begin, $date_end])
                                    ->whereRaw(
                                    "(DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') <= ?)", 
                                    [$date_begin, $date_end])
                                    ->sum('haber');
        $total_balance = DB::connection(Auth::user()->database_name)->table('accounts')
                                    ->where('accounts.code_one', $var->code_one)
                                    ->sum('balance_previus'); 
        /*---------------------------------------------------*/

    
        $var->debe = $total_debe;
        $var->haber = $total_haber;           
        $var->balance_previus = $total_balance;

        return $var;
    }

    public function calculation_superavit($var,$code,$coin,$date_begin,$date_end)
    {
        $total_debe = DB::connection(Auth::user()->database_name)->table('accounts')
                ->join('detail_vouchers', 'detail_vouchers.id_account', '=', 'accounts.id')
                ->where('accounts.code_one','>=', $code)
                ->where('detail_vouchers.status', 'C')
                ->whereRaw(
                "(DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') <= ?)", 
                [$date_begin, $date_end])
                ->sum('debe');



        $total_haber = DB::connection(Auth::user()->database_name)->table('accounts')
                ->join('detail_vouchers', 'detail_vouchers.id_account', '=', 'accounts.id')
                ->where('accounts.code_one','>=', $code)
                ->where('detail_vouchers.status', 'C')
                //->whereBetween('detail_vouchers.created_at', [$date_begin, $date_end])
                ->whereRaw(
                "(DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') >= ? AND DATE_FORMAT(detail_vouchers.created_at, '%Y-%m-%d') <= ?)", 
                [$date_begin, $date_end])
                ->sum('haber');


        $var->debe = $total_debe;
        $var->haber = $total_haber;    
        //asi cuadra el balance
        $var->balance_previus = 0;   
 
         return $var;
 
    }
    public function calculation_dolar($coin)
    {
        
        $accounts = Account::on(Auth::user()->database_name)->orderBy('code_one', 'asc')
                         ->orderBy('code_two', 'asc')
                         ->orderBy('code_three', 'asc')
                         ->orderBy('code_four', 'asc')
                         ->orderBy('code_five', 'asc')
                         ->get();
        
                       
        if(isset($accounts)) {
            
            foreach ($accounts as $var) 
            {
                if($var->code_one != 0)
                {
                    if($var->code_two != 0)
                    {
                        if($var->code_three != 0)
                        {
                            if($var->code_four != 0)
                            {
                                if($var->code_five != 0)
                                {
                                    //Calculo de superavit
                                    if(($var->code_one == 3) && ($var->code_two == 2) && ($var->code_three == 1) && 
                                    ($var->code_four == 1) && ($var->code_five == 1) ){
                                        $var = $this->calculation_superavit_dolar($var,4,$coin);
                                    }else{
                                    /*CALCULA LOS SALDOS DESDE DETALLE COMPROBANTE */    
                                     if($coin == 'bolivares'){
                                        $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                                                        FROM accounts a
                                                        INNER JOIN detail_vouchers d 
                                                            ON d.id_account = a.id
                                                        WHERE a.code_one = ? AND
                                                        a.code_two = ? AND
                                                        a.code_three = ? AND
                                                        a.code_four = ? AND
                                                        a.code_five = ? AND
                                                        d.status = ?
                                                        '
                                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,$var->code_five,'C']);
                                        $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                                                        FROM accounts a
                                                        INNER JOIN detail_vouchers d 
                                                            ON d.id_account = a.id
                                                        WHERE a.code_one = ? AND
                                                        a.code_two = ? AND
                                                        a.code_three = ? AND
                                                        a.code_four = ? AND
                                                        a.code_five = ? AND
                                                        d.status = ?
                                                        '
                                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,$var->code_five,'C']);
    
                                        $total_dolar_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS dolar
                                                        FROM accounts a
                                                        INNER JOIN detail_vouchers d 
                                                            ON d.id_account = a.id
                                                        WHERE a.code_one = ? AND
                                                        a.code_two = ? AND
                                                        a.code_three = ? AND
                                                        a.code_four = ? AND
                                                        a.code_five = ? AND
                                                        d.status = ?
                                                        '
                                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,$var->code_five,'C']);
    
                                        $total_dolar_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS dolar
                                                        FROM accounts a
                                                        INNER JOIN detail_vouchers d 
                                                            ON d.id_account = a.id
                                                        WHERE a.code_one = ? AND
                                                        a.code_two = ? AND
                                                        a.code_three = ? AND
                                                        a.code_four = ? AND
                                                        a.code_five = ? AND
                                                        d.status = ?
                                                        '
                                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,$var->code_five,'C']);
    
                                                        $var->balance =  $var->balance_previus;
    
                                       
                                        }else{
                                            $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
                                            FROM accounts a
                                            INNER JOIN detail_vouchers d 
                                                ON d.id_account = a.id
                                            WHERE a.code_one = ? AND
                                            a.code_two = ? AND
                                            a.code_three = ? AND
                                            a.code_four = ? AND
                                            a.code_five = ? AND
                                            d.status = ?
                                            '
                                            , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,$var->code_five,'C']);
                                            
                                            $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
                                            FROM accounts a
                                            INNER JOIN detail_vouchers d 
                                                ON d.id_account = a.id
                                            WHERE a.code_one = ? AND
                                            a.code_two = ? AND
                                            a.code_three = ? AND
                                            a.code_four = ? AND
                                            a.code_five = ? AND
                                            d.status = ?
                                            '
                                            , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,$var->code_five,'C']);
    
                                           
                                            if(($var->balance_previus != 0) && ($var->rate !=0)){
                                                $var->balance =  $var->balance_previus / ($var->rate ?? 1);
                                                $var->balance_previus = $var->balance;
                                            }
                                            
                                        }
                                        $total_debe = $total_debe[0]->debe;
                                        $total_haber = $total_haber[0]->haber;
                                        if(isset($total_dolar_debe[0]->dolar)){
                                            $total_dolar_debe = $total_dolar_debe[0]->dolar;
                                            $var->dolar_debe = $total_dolar_debe;
                                        }
                                        if(isset($total_dolar_haber[0]->dolar)){
                                            $total_dolar_haber = $total_dolar_haber[0]->dolar;
                                            $var->dolar_haber = $total_dolar_haber;
                                        }
                                    
                                        $var->debe = $total_debe;
                                        $var->haber = $total_haber;

                                       
                                    }

                                }else{
                                    
                            
                                    //Calculo de superavit
                                    if(($var->code_one == 3) && ($var->code_two == 2) && ($var->code_three == 1) && 
                                    ($var->code_four == 1) ){
                                        $var = $this->calculation_superavit_dolar($var,4,$coin);
                                    }else{
                                    /*CALCULA LOS SALDOS DESDE DETALLE COMPROBANTE */                                                   
                                
                                    if($coin == 'bolivares'){
                                    $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                                                    FROM accounts a
                                                    INNER JOIN detail_vouchers d 
                                                        ON d.id_account = a.id
                                                    WHERE a.code_one = ? AND
                                                    a.code_two = ? AND
                                                    a.code_three = ? AND
                                                    a.code_four = ? AND
                                                    d.status = ?
                                                    '
                                                    , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C']);
                                    $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                                                    FROM accounts a
                                                    INNER JOIN detail_vouchers d 
                                                        ON d.id_account = a.id
                                                    WHERE a.code_one = ? AND
                                                    a.code_two = ? AND
                                                    a.code_three = ? AND
                                                    a.code_four = ? AND
                                                    d.status = ?
                                                    '
                                                    , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C']);

                                    $total_dolar_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS dolar
                                                    FROM accounts a
                                                    INNER JOIN detail_vouchers d 
                                                        ON d.id_account = a.id
                                                    WHERE a.code_one = ? AND
                                                    a.code_two = ? AND
                                                    a.code_three = ? AND
                                                    a.code_four = ? AND
                                                    d.status = ?
                                                    '
                                                    , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C']);

                                    $total_dolar_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS dolar
                                                    FROM accounts a
                                                    INNER JOIN detail_vouchers d 
                                                        ON d.id_account = a.id
                                                    WHERE a.code_one = ? AND
                                                    a.code_two = ? AND
                                                    a.code_three = ? AND
                                                    a.code_four = ? AND
                                                    d.status = ?
                                                    '
                                                    , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C']);

                                                    $var->balance =  $var->balance_previus;

                                    $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus) AS balance
                                                    FROM accounts a
                                                    WHERE a.code_one = ? AND
                                                    a.code_two = ?  AND
                                                    a.code_three = ? AND
                                                    a.code_four = ?
                                                    '
                                                    , [$var->code_one,$var->code_two,$var->code_three,$var->code_four]);
                                
                                    }else{
                                        $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
                                        FROM accounts a
                                        INNER JOIN detail_vouchers d 
                                            ON d.id_account = a.id
                                        WHERE a.code_one = ? AND
                                        a.code_two = ? AND
                                        a.code_three = ? AND
                                        a.code_four = ? AND
                                        d.status = ?
                                        '
                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C']);
                                        
                                        $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
                                        FROM accounts a
                                        INNER JOIN detail_vouchers d 
                                            ON d.id_account = a.id
                                        WHERE a.code_one = ? AND
                                        a.code_two = ? AND
                                        a.code_three = ? AND
                                        a.code_four = ? AND
                                        d.status = ?
                                        '
                                        , [$var->code_one,$var->code_two,$var->code_three,$var->code_four,'C']);

                                        $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus/a.rate) AS balance
                                                    FROM accounts a
                                                    WHERE a.code_one = ? AND
                                                    a.code_two = ?  AND
                                                    a.code_three = ? AND
                                                    a.code_four = ?
                                                    '
                                                    , [$var->code_one,$var->code_two,$var->code_three,$var->code_four]);

                                        /*if(($var->balance_previus != 0) && ($var->rate !=0))
                                        $var->balance =  $var->balance_previus / $var->rate;*/
                                    }
                                    $total_debe = $total_debe[0]->debe;
                                    $total_haber = $total_haber[0]->haber;
                                    if(isset($total_dolar_debe[0]->dolar)){
                                        $total_dolar_debe = $total_dolar_debe[0]->dolar;
                                        $var->dolar_debe = $total_dolar_debe;
                                    }
                                    if(isset($total_dolar_haber[0]->dolar)){
                                        $total_dolar_haber = $total_dolar_haber[0]->dolar;
                                        $var->dolar_haber = $total_dolar_haber;
                                    }
                                
                                    $var->debe = $total_debe;
                                    $var->haber = $total_haber;

                                    $total_balance = $total_balance[0]->balance;
                                    $var->balance = $total_balance;
                                    $var->balance_previus = $total_balance;
                                }  
                                }
                            }else{          
                                //Calculo de superavit
                                if(($var->code_one == 3) && ($var->code_two == 2) && ($var->code_three == 1)){
                                    $var = $this->calculation_superavit_dolar($var,4,$coin);
                                }else{
                            
                                if($coin == 'bolivares'){
                                $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                                                FROM accounts a
                                                INNER JOIN detail_vouchers d 
                                                    ON d.id_account = a.id
                                                WHERE a.code_one = ? AND
                                                a.code_two = ? AND
                                                a.code_three = ? AND
                                                
                                                d.status = ?
                                                '
                                                , [$var->code_one,$var->code_two,$var->code_three,'C']);
                                $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                                                FROM accounts a
                                                INNER JOIN detail_vouchers d 
                                                    ON d.id_account = a.id
                                                WHERE a.code_one = ? AND
                                                a.code_two = ? AND
                                                a.code_three = ? AND
                                                
                                                d.status = ?
                                                '
                                                , [$var->code_one,$var->code_two,$var->code_three,'C']);

                                $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus) AS balance
                                            FROM accounts a
                                            WHERE a.code_one = ? AND
                                            a.code_two = ?  AND
                                            a.code_three = ?
                                            '
                                            , [$var->code_one,$var->code_two,$var->code_three]);
                                
                                }else{
                                        $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
                                        FROM accounts a
                                        INNER JOIN detail_vouchers d 
                                            ON d.id_account = a.id
                                        WHERE a.code_one = ? AND
                                        a.code_two = ? AND
                                        a.code_three = ? AND
                                        
                                        d.status = ?
                                        '
                                        , [$var->code_one,$var->code_two,$var->code_three,'C']);
                                        
                                        $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
                                        FROM accounts a
                                        INNER JOIN detail_vouchers d 
                                            ON d.id_account = a.id
                                        WHERE a.code_one = ? AND
                                        a.code_two = ? AND
                                        a.code_three = ? AND
                                        
                                        d.status = ?
                                        '
                                        , [$var->code_one,$var->code_two,$var->code_three,'C']);
                        
                                        $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus/a.rate) AS balance
                                            FROM accounts a
                                            WHERE a.code_one = ? AND
                                            a.code_two = ? AND
                                            a.code_three = ?
                                            '
                                            , [$var->code_one,$var->code_two,$var->code_three]);

                                    }
                                    $total_debe = $total_debe[0]->debe;
                                    $total_haber = $total_haber[0]->haber;
                                
                                    $var->debe = $total_debe;
                                    $var->haber = $total_haber;

                                    

                                    $total_balance = $total_balance[0]->balance;
                                    $var->balance = $total_balance;
                                    $var->balance_previus = $total_balance;
                                            
                                }        
                                }   
                        }else{
                            //Calculo de superavit
                            if(($var->code_one == 3) && ($var->code_two == 2) ){
                                $var = $this->calculation_superavit_dolar($var,4,$coin);
                            }else{
                            if($coin == 'bolivares'){
                                $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                                                FROM accounts a
                                                INNER JOIN detail_vouchers d 
                                                    ON d.id_account = a.id
                                                WHERE a.code_one = ? AND
                                                a.code_two = ? AND
                                                d.status = ?
                                                '
                                                , [$var->code_one,$var->code_two,'C']);
                                $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                                                FROM accounts a
                                                INNER JOIN detail_vouchers d 
                                                    ON d.id_account = a.id
                                                WHERE a.code_one = ? AND
                                                a.code_two = ? AND
                                                d.status = ?
                                                '
                                                , [$var->code_one,$var->code_two,'C']);
                                
                                $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus) AS balance
                                            FROM accounts a
                                            WHERE a.code_one = ? AND
                                            a.code_two = ?
                                            '
                                            , [$var->code_one,$var->code_two]);
                                
                                }else{
                                    $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
                                    FROM accounts a
                                    INNER JOIN detail_vouchers d 
                                        ON d.id_account = a.id
                                    WHERE a.code_one = ? AND
                                    a.code_two = ? AND
                                    d.status = ?
                                    '
                                    , [$var->code_one,$var->code_two,'C']);
                                    
                                    $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
                                    FROM accounts a
                                    INNER JOIN detail_vouchers d 
                                        ON d.id_account = a.id
                                    WHERE a.code_one = ? AND
                                    a.code_two = ? AND
                                    d.status = ?
                                    '
                                    , [$var->code_one,$var->code_two,'C']);

                                    $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus/a.rate) AS balance
                                            FROM accounts a
                                            WHERE a.code_one = ? AND
                                            a.code_two = ?
                                            '
                                            , [$var->code_one,$var->code_two]);
                    
                                }
                                
                                $total_debe = $total_debe[0]->debe;
                                $total_haber = $total_haber[0]->haber;
                                $var->debe = $total_debe;
                                $var->haber = $total_haber;

                                

                                $total_balance = $total_balance[0]->balance;
                                $var->balance = $total_balance;
                                $var->balance_previus = $total_balance;
                        }
                        }
                    }else{
                        //Calcular patrimonio con las cuentas mayores o iguales a 3.0.0.0.0
                        if($var->code_one == 3){
                            $var = $this->calculation_capital_dolar($var,$coin);
                           
                        }else{
                            if($coin == 'bolivares'){
                                $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                                                FROM accounts a
                                                INNER JOIN detail_vouchers d 
                                                    ON d.id_account = a.id
                                                WHERE a.code_one = ? AND
                                                d.status = ?
                                                '
                                                , [$var->code_one,'C']);
                                $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                                                FROM accounts a
                                                INNER JOIN detail_vouchers d 
                                                    ON d.id_account = a.id
                                                WHERE a.code_one = ? AND
                                                d.status = ?
                                                '
                                                , [$var->code_one,'C']);
    
                                $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus) AS balance
                                                FROM accounts a
                                                WHERE a.code_one = ?
                                                '
                                                , [$var->code_one]);
                                
                                }else{
                                    $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
                                    FROM accounts a
                                    INNER JOIN detail_vouchers d 
                                        ON d.id_account = a.id
                                    WHERE a.code_one = ? AND
                                    d.status = ?
                                    '
                                    , [$var->code_one,'C']);
                                    
                                    $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
                                    FROM accounts a
                                    INNER JOIN detail_vouchers d 
                                        ON d.id_account = a.id
                                    WHERE a.code_one = ? AND
                                    d.status = ?
                                    '
                                    , [$var->code_one,'C']);
    
                                    $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus/a.rate) AS balance
                                                FROM accounts a
                                                WHERE a.code_one = ?
                                                '
                                                , [$var->code_one]);
                    
                                }
                                $total_debe = $total_debe[0]->debe;
                                $total_haber = $total_haber[0]->haber;
                                $var->debe = $total_debe;
                                $var->haber = $total_haber;
    
                                $total_balance = $total_balance[0]->balance;
    
                                $var->balance = $total_balance;
                                $var->balance_previus = $total_balance;
                        }
                    }
                }else{
                    return redirect('/accounts/menu')->withDanger('El codigo uno es igual a cero!');
                }
            } 
        
        }else{
            return redirect('/accounts/menu')->withDanger('No hay Cuentas');
        }              
                 
       
        
         return $accounts;
    }
    public function calculation_capital_dolar($var,$coin)
    {
        if($coin == 'bolivares'){
            $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                            FROM accounts a
                            INNER JOIN detail_vouchers d 
                                ON d.id_account = a.id
                            WHERE a.code_one >= ? AND
                            d.status = ?
                            '
                            , [$var->code_one,'C']);
            $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                            FROM accounts a
                            INNER JOIN detail_vouchers d 
                                ON d.id_account = a.id
                            WHERE a.code_one >= ? AND
                            d.status = ?
                            '
                            , [$var->code_one,'C']);

            $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus) AS balance
                            FROM accounts a
                            WHERE a.code_one = ?
                            '
                            , [$var->code_one]);
            
            }else{
                $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
                FROM accounts a
                INNER JOIN detail_vouchers d 
                    ON d.id_account = a.id
                WHERE a.code_one >= ? AND
                d.status = ?
                '
                , [$var->code_one,'C']);
                
                $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
                FROM accounts a
                INNER JOIN detail_vouchers d 
                    ON d.id_account = a.id
                WHERE a.code_one >= ? AND
                d.status = ?
                '
                , [$var->code_one,'C']);

                $total_balance =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(a.balance_previus/a.rate) AS balance
                            FROM accounts a
                            WHERE a.code_one = ?
                            '
                            , [$var->code_one]);

            }
            $total_debe = $total_debe[0]->debe;
            $total_haber = $total_haber[0]->haber;
            $var->debe = $total_debe;
            $var->haber = $total_haber;

            $total_balance = $total_balance[0]->balance;

            $var->balance = $total_balance;
            $var->balance_previus = $total_balance;

            return $var;
    }
    public function calculation_superavit_dolar($var,$code,$coin)
   {
    if($coin == 'bolivares'){
        $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe) AS debe
                        FROM accounts a
                        INNER JOIN detail_vouchers d 
                            ON d.id_account = a.id
                        WHERE a.code_one >= ? AND
                        d.status = ?
                        '
                        , [$code,'C']);
        $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber) AS haber
                        FROM accounts a
                        INNER JOIN detail_vouchers d 
                            ON d.id_account = a.id
                        WHERE a.code_one >= ? AND
                        d.status = ?
                        '
                        , [$code,'C']);

        
        }else{
            $total_debe =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.debe/d.tasa) AS debe
            FROM accounts a
            INNER JOIN detail_vouchers d 
                ON d.id_account = a.id
            WHERE a.code_one >= ? AND
            d.status = ?
            '
            , [$code,'C']);
            
            $total_haber =   DB::connection(Auth::user()->database_name)->select('SELECT SUM(d.haber/d.tasa) AS haber
            FROM accounts a
            INNER JOIN detail_vouchers d 
                ON d.id_account = a.id
            WHERE a.code_one >= ? AND
            d.status = ?
            '
            , [$code,'C']);
        
        //Por ahora tomare el balance como 0 ya que algun movimiento hicieron y todo cuadra si el balance aqui es cero
        $var->balance_previus = 0;
        $var->balance = 0;
        /*if(($var->balance_previus != 0) && ($var->rate !=0)){
            $var->balance =  $var->balance_previus / ($var->rate ?? 1);
            $var->balance_previus = $var->balance;
        }*/

        }
        $total_debe = $total_debe[0]->debe;
        $total_haber = $total_haber[0]->haber;
        $var->debe = $total_debe;
        $var->haber = $total_haber;

       
        //$total_balance = $total_balance[0]->balance;

        //$var->balance = $total_balance;

        return $var;

   }
   
    public function select_client()
    {
        
        $clients    = Client::on(Auth::user()->database_name)->get();
    
        return view('admin.reports.selectclient',compact('clients'));
    }

    public function select_vendor()
    {
        $vendors    = Vendor::on(Auth::user()->database_name)->get();
    
        return view('admin.reports.selectvendor',compact('vendors'));
    }

    public function select_client_ne()
    {

        $clients    = Client::on(Auth::user()->database_name)->get();
        return view('admin.reports.selectclient_ne',compact('clients'));
    }

    
    public function select_vendor_ne()
    {
        $vendors    = Vendor::on(Auth::user()->database_name)->get();
    
        return view('admin.reports.selectvendor_ne',compact('vendors'));
    }


    public function select_provider()
    {
        $providers    = Provider::on(Auth::user()->database_name)->get();
    
        return view('admin.reports.selectprovider',compact('providers'));
    }
    

    public function search_bcv()
    {
        /*Buscar el indice bcv*/
        $urlToGet ='http://www.bcv.org.ve/tasas-informativas-sistema-bancario';
        $pageDocument = @file_get_contents($urlToGet);
        preg_match_all('|<div class="col-sm-6 col-xs-6 centrado"><strong> (.*?) </strong> </div>|s', $pageDocument, $cap);

        if ($cap[0] == array()){ // VALIDAR Concidencia
            $titulo = '0,00';
        }else {
            $titulo = $cap[1][4];
        }

        $bcv_con_formato = $titulo;
        $bcv = str_replace(',', '.', str_replace('.', '',$bcv_con_formato));


        /*-------------------------- */
       return bcdiv($bcv, '1', 2);

    }

}
