<?php

namespace App\Http\Controllers;

use App\Segment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SegmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('valiuser')->only('index');
        $this->middleware('valimodulo:Segmentos');
    }

    public function index(Request $request)
    {

        $agregarmiddleware = $request->get('agregarmiddleware');
        $actualizarmiddleware = $request->get('actualizarmiddleware');
        $eliminarmiddleware = $request->get('eliminarmiddleware');



           $segments = Segment::on(Auth::user()->database_name)->orderBy('id', 'asc')->get();

        return view('admin.segments.index',compact('segments','agregarmiddleware','actualizarmiddleware','eliminarmiddleware'));

    }

    public function create(Request $request)
    {

        if(Auth::user()->role_id  == '1' || $request->get('agregarmiddleware') == 1){
        return view('admin.segments.create');
    }else{
        return redirect('/segments')->withDelete('No Tiene Acceso a Registrar');
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


                $buscarsegment  = Segment::on(Auth::user()->database_name)
                                ->where('description',$request->description)
                                ->where('status',1)
                                ->get();


                if($buscarsegment->count() > 0){
                    $resp['error'] = false;
                    $resp['msg'] = 'El Segmento que intenta agregar ya se Encuentra Registrado';

                    return response()->json($resp);
                }



                $users = new Segment();
                $users->setConnection(Auth::user()->database_name);

                $users->description = request('description');

                $users->status = 1;

                $users->save();



                $resp['error'] = true;
                $resp['msg'] = 'Segmento Registrado';

                return response()->json($resp);


            }catch(\error $error){
                $resp['error'] = false;
                $resp['msg'] = 'Error.';

                return response()->json($resp);
            }
        }



    }else{
        return redirect('/segments')->withDelete('No Tiene Acceso a Registrar');
        }
    }



    public function edit(Request $request,$id)
    {

        if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){
        $user  = Segment::on(Auth::user()->database_name)->find($id);

        return view('admin.segments.edit',compact('user'));
        }else{
        return redirect('/segments')->withDelete('No Tiene Acceso a Editar');
        }
    }




    public function update(Request $request,$id)
    {
        if(Auth::user()->role_id  == '1' || $request->get('actualizarmiddleware') == 1){
        $request->validate([

            'description'      =>'required|string|max:255',

        ]);



        $user          = Segment::on(Auth::user()->database_name)->findOrFail($id);
        $user->description        = request('description');



        $user->save();


        return redirect('/segments')->withSuccess('Registro Guardado Exitoso!');
    }else{
        return redirect('/segments')->withDelete('No Tiene Acceso a Editar');
        }

    }


}
