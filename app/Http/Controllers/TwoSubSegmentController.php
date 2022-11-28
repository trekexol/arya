<?php

namespace App\Http\Controllers;

use App\Subsegment;
use App\TwoSubsegment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoSubSegmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Segundo Sub Segmento');
    }

    public function list(Request $request, $id_subsegment = null){
        //validar si la peticion es asincrona
        if($request->ajax()){
            try{
                
                $subsegment = TwoSubsegment::on(Auth::user()->database_name)->select('id','description')->where('subsegment_id',$id_subsegment)->orderBy('description','asc')->get();
                return response()->json($subsegment,200);
            
            }catch(Throwable $th){
                return response()->json(false,500);
            }
        }
    }
   
    public function index(Request $request)
    {

        $agregarmiddleware = $request->get('agregarmiddleware');
        $actualizarmiddleware = $request->get('actualizarmiddleware');
        $eliminarmiddleware = $request->get('eliminarmiddleware');

        $subsegments = TwoSubsegment::on(Auth::user()->database_name)->orderBy('id', 'asc')->get();
     

        return view('admin.twosubsegments.index',compact('subsegments','agregarmiddleware','actualizarmiddleware','eliminarmiddleware'));
      
    }

    public function create(Request $request)
    {

        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == '1'){
        $subsegments   = Subsegment::on(Auth::user()->database_name)->orderBy('description', 'asc')->get();

        return view('admin.twosubsegments.create',compact('subsegments'));
    }else{
        return redirect('/twosubsegments')->withSuccess('No Tiene Acceso a Registrar');
        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == '1'){
        $data = request()->validate([
            'description'         =>'required|max:255',
            'segment_id'         =>'required',
            
        ]);

        $users = new TwoSubsegment();
        $users->setConnection(Auth::user()->database_name);

        $users->description = request('description');
        $users->subsegment_id = request('segment_id');
        $users->status = 1;

        $users->save();
        return redirect('/twosubsegments')->withSuccess('Registro Exitoso!');
    }else{
        return redirect('/twosubsegments')->withSuccess('No Tiene Acceso a Registrar');
        }
    }
    
    public function messages()
    {
        return [
            'segment_id.required' => 'A title is required',
            'segment_id'  => 'A message is required',
        ];
    }


    public function edit(request $request, $id)
    {
        if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){
        $var        = TwoSubsegment::on(Auth::user()->database_name)->find($id);
        $subsegments   = SubSegment::on(Auth::user()->database_name)->get();

        return view('admin.twosubsegments.edit',compact('var','subsegments'));
    }else{
        return redirect('/twosubsegments')->withSuccess('No Tiene Acceso a Editar');
        }
    }

   


    public function update(Request $request,$id)
    {
        if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){
        $subsegment =  TwoSubsegment::on(Auth::user()->database_name)->find($id);
       
        $subsegment_status = $subsegment->status;

        $request->validate([
            'description'         =>'required|max:255',
            'segment_id'  => 'required|integer',
            
        ]);

        
        $var = TwoSubsegment::on(Auth::user()->database_name)->findOrFail($id);
        $var->description         = request('description');
        $var->subsegment_id       = request('segment_id');
      
       
        if(request('status') == null){
            $var->status = $subsegment_status;
        }else{
            $var->status = request('status');
        }
       

        $var->save();


        return redirect('/twosubsegments')->withSuccess('Registro Guardado Exitoso!');
    }else{
        return redirect('/twosubsegments')->withSuccess('No Tiene Acceso a Editar');
        }

    }
}
