@extends('admin.layouts.dashboard')

@section('content')
  
    <!-- container-fluid -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="row py-lg-2">
            <div class="col-md-6">
                <h2>Editar de Usuario </h2>
            </div>

        </div>
    </div>
    <!-- /container-fluid -->

    {{-- VALIDACIONES-RESPUESTA--}}
@include('admin.layouts.success')   {{-- SAVE --}}
@include('admin.layouts.danger')    {{-- EDITAR --}}
@include('admin.layouts.delete')    {{-- DELELTE --}}
{{-- VALIDACIONES-RESPUESTA --}}

    <div class="card shadow mb-4">
        <div class="card-body">
            <form  method="POST"   action="{{ route('users.updateuser') }}" enctype="multipart/form-data" >
                @method('PATCH')
                @csrf()
                <div class="container py-2">
                    <div class="row">
                        <div class="col-12 ">
                     
                               <div class="form-group row">
                                    
                                    <div class="col-sm-2">
                                        <label for="name">Nombre:</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="name" name="name" value="{{$user->name}}" >
                                    </div>
                                    @error('name')
                       
                                        <strong class="alert alert-danger">{{ $message }}</strong>
        
                                    @enderror
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-2">
                                        <label for="xcedula">Correo Electrónico:</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="email" name="email" value="{{ $user->email }}" >
                                    </div>
                                    @error('email')
                                        <strong class="alert alert-danger">{{ $message }}</strong>
                                    @enderror
                                </div>
                               
                


                                <div class="form-group row">
                                    <div class="col-sm-2">
                                        <label for="inputPassword">Clave</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="password" value="{{old('password')}}" id="inputPassword" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Password" >
                                    </div>
                                    @error('password')
                           
                                        <strong class="alert alert-danger">{{ $message }}</strong>
                             
                                    @enderror

                                </div>

                                <div class="form-group row">
                            

                                    <div class="col-sm-2">
                                        <label for="inputConfirmPassword">Confirmación Clave:</label>
                                        </label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="password" value="{{old('passwordconf')}}" id="inputConfirmPassword" class="form-control" placeholder="Password" name="password_confirmation" >
                                    </div>

                                    @error('password_confirmation')
                                    <strong class="alert alert-danger">{{ $message }}</strong>
                                @enderror
                                </div>
                            <br>
                                <div class="form-group row">
                                    <div class="form-group col-sm-3 offset-sm-2">
                                        <button type="submit" class="btn btn-success btn-block"><i class="fa fa-send-o"></i>Registrar</button>
                                    </div>
                                    <div class="form-group col-sm-2">
                                        <a href="{{ route('users') }}" name="danger" type="button" class="btn btn-danger btn-block">Cancelar</a>
                                    </div>
                                </div>
               
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>
                    @endsection

                    @section('validacion_usuario')
                    <script>
                           
                    $(function(){
                        soloLetras('name');
                       
                    });
                    
                 
                
                
                
                    </script>
                @endsection
                