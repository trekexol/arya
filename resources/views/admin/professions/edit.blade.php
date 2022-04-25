@extends('admin.layouts.dashboard')

@section('content')
  
    <!-- container-fluid -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="row py-lg-2">
            <div class="col-md-6">
                <h2>Editar Tipo de Empleado</h2>
            </div>

        </div>
    </div>
    <!-- /container-fluid -->

    {{-- VALIDACIONES-RESPUESTA--}}
@include('admin.layouts.success')   {{-- SAVE --}}
@include('admin.layouts.danger')    {{-- EDITAR --}}
@include('admin.layouts.delete')    {{-- DELELTE --}}
{{-- VALIDACIONES-RESPUESTA --}}

@if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{$error}}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <form  method="POST"   action="{{ route('professions.update',$user->id) }}" enctype="multipart/form-data" >
                @method('PATCH')
                @csrf()
                <div class="container py-2">
                    <div class="row">
                        <div class="col-12 ">
                            <form>
                           
                               <div class="form-group row">
                                    
                                    <div class="col-sm-2">
                                        <label for="name">Nombre:</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="name" name="name" value="{{$user->name}}" >
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-2">
                                        <label for="xcedula">Status:</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <select class="form-control" id="status" name="status" title="status">
                                            @if($user->status == 1)
                                                <option value="1">Activo</option>
                                            @else
                                                <option value="0">Inactivo</option>
                                            @endif
                                            <option value="nulo">----------------</option>
                                            
                                            <div class="dropdown">
                                                <option value="1">Activo</option>
                                                <option value="0">Inactivo</option>
                                            </div>
                                            
                                               
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-2">
                                        <label for="xcedula">Descripción:</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="description" name="description" value="{{ $user->description }}" >
                                    </div>
                                    
                                </div>
                               
                            

                            <br>
                                <div class="form-group row">
                                    <div class="form-group col-sm-6">
                                        <button type="submit" class="btn btn-success btn-block"><i class="fa fa-send-o"></i>Registrar</button>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <a href="{{ route('professions') }}" name="danger" type="button" class="btn btn-danger btn-block">Cancelar</a>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
@endsection

@section('validacion')
    <script>    
	$(function(){
        soloAlfaNumerico('name');
        soloAlfaNumerico('description');
      
    });
    </script>
@endsection