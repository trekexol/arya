<?php

namespace App\Http\Controllers;


use App\Segment;
use App\Subsegment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubsegmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Sub Segmentos');
    }

    public function list(Request $request, $id_segment = null){
        //validar si la peticion es asincrona
        if($request->ajax()){
            try{

                $subsegment = Subsegment::on(Auth::user()->database_name)->select('id','description')->where('segment_id',$id_segment)->orderBy('description','asc')->get();
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


         $subsegments      =   Subsegment::on(Auth::user()->database_name)->orderBy('id', 'asc')->get();

        return view('admin.subsegment.index',compact('subsegments','agregarmiddleware','actualizarmiddleware','eliminarmiddleware'));

    }

    public function create(Request $request)
    {

        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){
        $segments                  = Segment::on(Auth::user()->database_name)->orderBy('description', 'asc')->get();

        return view('admin.subsegment.create',compact('segments'));
    }else{
        return redirect('/subsegment')->withSuccess('No Tiene Acceso a Registrar');
        }
    }

    public function store(Request $request)
    {

        $resp = array();
        $resp['error'] = false;
        $resp['msg'] = '';

        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){


            if($request->ajax()){
                try{


                    $buscarsegment  = Subsegment::on(Auth::user()->database_name)
                                    ->where('description',$request->description)
                                    ->where('segment_id',$request->segment_id)
                                    ->where('status',1)
                                    ->get();


                    if($buscarsegment->count() > 0){
                        $resp['error'] = false;
                        $resp['msg'] = 'El SubSegmento que intenta agregar ya se Encuentra Registrado';

                        return response()->json($resp);
                    }



                    $users = new Subsegment();
                    $users->setConnection(Auth::user()->database_name);

                    $users->description = request('description');
                    $users->segment_id = request('segment_id');
                    $users->status = request('status');

                    $users->save();



                    $resp['error'] = true;
                    $resp['msg'] = 'SubSegmento Registrado';

                    return response()->json($resp);


                }catch(\error $error){
                    $resp['error'] = false;
                    $resp['msg'] = 'Error.';

                    return response()->json($resp);
                }
            }


    }else{
        return redirect('/subsegment')->withSuccess('No Tiene Acceso a Registrar');
        }
    }

    public function messages()
    {
        return [
            'segment_id.required' => 'A title is required',
            'segment_id'  => 'A message is required',
        ];
    }


    public function edit(Request $request,$id)
    {

        if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){

        $var  = Subsegment::on(Auth::user()->database_name)->find($id);
        $segments        = Segment::on(Auth::user()->database_name)->get();

        return view('admin.subsegment.edit',compact('var','segments'));
    }else{
        return redirect('/subsegment')->withSuccess('No Tiene Acceso a Editar');
        }
    }




    public function update(Request $request,$id)
    {
        if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){
        $subsegment =  Subsegment::on(Auth::user()->database_name)->find($id);

        $subsegment_status = $subsegment->status;

        $request->validate([
            'description'         =>'required|max:255',
            'segment_id'  => 'required|integer',

        ]);//verifica que el usuario existe


        $var = Subsegment::on(Auth::user()->database_name)->findOrFail($id);
        $var->description         = request('description');
        $var->segment_id       = request('segment_id');


        if(request('status') == null){
            $var->status = $subsegment_status;
        }else{
            $var->status = request('status');
        }


        $var->save();


        return redirect('/subsegment')->withSuccess('Registro Guardado Exitoso!');

    }else{
        return redirect('/subsegment')->withSuccess('No Tiene Acceso a Editar');
        }

    }


}
