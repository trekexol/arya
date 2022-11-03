@extends('admin.layouts.dashboard')

@section('content')
  
    <!-- container-fluid -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="row py-lg-2">
            <div class="col-md-6">
                <h2>Editar Fórmula de Nómina </h2>
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
            <form  method="POST"   action="{{ route('nominaformulas.update',$var->id) }}" enctype="multipart/form-data" >
                @method('PATCH')
                @csrf()
                <div class="container py-2">
                    <div class="row">
                        <div class="col-12 ">
                           
                           
                                <div class="form-group row">
                                    <label for="description" class="col-md-2 col-form-label text-md-right">Descripción </label>
        
                                    <div class="col-md-4">
                                        <input id="description" type="text" class="form-control @error('description') is-invalid @enderror" name="description" value="{{ $var->description }}" maxlength="300" required autocomplete="description">
        
                                        @error('description')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <label for="type" class="col-md-2 col-form-label text-md-right">Tipo Nómina:</label>
                                    
                                    <div class="col-md-4">
                                        <select class="form-control" id="type" name="type" title="type">
                                            @if ($var->type == "Q")
                                                <option selected value="Q">Quincenal</option>
                                            @elseif ($var->type == "M")
                                                <option selected value="M">Mensual</option>
                                            @elseif ($var->type == "S")
                                                <option selected value="S">Semanal</option>
                                            @elseif ($var->type == "S")
                                                <option selected value="E">Especial</option>
                                            @elseif ($var->type == "S")
                                                <option selected value="A">Asignación General</option>
                                            @endif
                                                
                                           
                                            <option value="T">Ninguna</option>
                                            
                                            <div class="dropdown">
                                            @if ($var->type != "Q")
                                                <option value="Q">Quincenal</option>
                                            @endif
                                            @if ($var->type != "M")
                                                <option value="M">Mensual</option>
                                            @endif
                                            @if ($var->type != "S")
                                                <option value="S">Semanal</option>
                                            @endif
                                            @if ($var->type != "S")
                                                <option value="E">Especial</option>
                                            @endif
                                            @if ($var->type != "S")
                                                <option value="A">Asignación General</option>
                                            @endif

                                            </div>
                                            
                                               
                                        </select>
                                    </div>
                                </div>
                               
                            <br>
                                <div class="form-group row justify-content-center">
                                    <div class="form-group col-sm-2">
                                        <button type="submit" class="btn btn-info btn-block"><i class="fa fa-send-o"></i>Registrar</button>
                                    </div>
                                    <div class="form-group col-sm-2">
                                        <a href="{{ route('nominaformulas') }}" name="danger" type="button" class="btn btn-danger btn-block">Cancelar</a>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>

                    @endsection
