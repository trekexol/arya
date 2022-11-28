@extends('admin.layouts.dashboard')

@section('content')
  
    <!-- container-fluid -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="row py-lg-2">
            <div class="col-md-6">
                <h2>Editar Transporte</h2>
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
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header text-center font-weight-bold h3">Registro de Transporte</div>
    
                    <div class="card-body">
                        <form  method="POST"   action="{{ route('transports.update',$transport->id) }}" enctype="multipart/form-data" >
                            @method('PATCH')
                            @csrf()
                           
                            <input id="user_id" type="hidden" class="form-control @error('user_id') is-invalid @enderror" name="user_id" value="{{ Auth::user()->id }}" required autocomplete="user_id">
    
                            <div class="form-group row">
                                
                                <label for="modelo" class="col-md-2 col-form-label text-md-right">Modelo</label>
    
                                <div class="col-md-4">
                                <select class="form-control" id="modelo_id" name="modelo_id">
                                    @foreach($modelos as $var)
                                        <option value="{{ $var->id }}">{{ $var->description }}</option>
                                    @endforeach
                                  
                                </select>
                                </div>
                                <label for="color" class="col-md-2 col-form-label text-md-right">Color</label>
    
                                <div class="col-md-4">
                                <select class="form-control" id="color_id" name="color_id">
                                    @foreach($colors as $var)
                                        <option value="{{ $var->id }}">{{ $var->description }}</option>
                                    @endforeach
                                  
                                </select>
                                </div>
                            </div>
                            
                           
                            <div class="form-group row">
                                <label for="rol" class="col-md-2 col-form-label text-md-right">Tipo</label>
    
                                <div class="col-md-4">
                                <select class="form-control" name="type" id="type">
                                    <option value="Carro">Carro</option>
                                    <option value="Camioneta">Camioneta</option>
                                    <option value="Camion">Camión</option>
                                </select>
                                </div>
                                <label for="placa" class="col-md-2 col-form-label text-md-right">Placa</label>
    
                                <div class="col-md-4">
                                    <input id="placa" type="text" class="form-control @error('placa') is-invalid @enderror" name="placa" value="{{ $transport->placa }}" required autocomplete="placa">
    
                                    @error('placa')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="photo_transport" class="col-md-2 col-form-label text-md-right">Foto Transporte</label>
    
                                <div class="col-md-4">
                                    <input id="photo_transport" type="text" class="form-control @error('photo_transport') is-invalid @enderror" name="photo_transport" value="{{ old('photo_transport') }}" autocomplete="photo_transport">
    
                                    @error('photo_transport')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <label for="rol" class="col-md-2 col-form-label text-md-right">Status</label>
    
                                <div class="col-md-4">
                                <select class="form-control" name="status" id="status">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                                </div>
                            </div>
                            
                            <br>
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                       Actualizar Transporte
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

 @endsection
                @section('validacion')
                    <script>    
                    $(function(){
                        soloAlfaNumerico('placa');
                       
                    });
                    </script>
                @endsection
