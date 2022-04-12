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
            <form  method="POST"   action="{{ route('users.update',$user->id) }}" enctype="multipart/form-data" >
                @method('PATCH')
                @csrf()
                <div class="container py-2">
                    <div class="row">
                        <div class="col-12 ">
                            <form>
                            @if (Auth::user()->role_id  == '1')
                            <div class="form-group row">
                                    <div class="col-sm-2">
                                        <label for="inputEmail">Roles:</label>
                                        @error('roles')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                        </label>
                                    </div>
                                    <div class="col-sm-4">
                                        <select class="form-control" id="roles" name="Roles" title="Color del Producto">
                                            <option value="{{$user->role_id}}">{{$user->roles['description']}}</option>
                                            <option value="0">----------------</option>
                                            @if (empty($roles))
                                            @else
                                                <div class="dropdown">
                                                    @foreach($roles as $role)
                                                        <option value="{{ $role->id }}" {{ old('Roles') }}>{{ $role->description }}</option>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </select>
                                    </div>
                            </div>
                            @endif
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
                                        <label for="xcedula">Correo Electrónico:</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="email" name="email" value="{{ $user->email }}" >
                                    </div>
                                    
                                </div>
                                 <div class="form-group row">
                                    <div class="col-sm-2">
                                        <label for="xcedula">Sucursal:</label>
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-control" id="id_branch" name="id_branch">
                                        @isset($user->id_branch)
                                            <option value="{{$user->id_branch}}">{{ $user->branches['description'] ?? '' }}</option>
                                            <option value="">----------------</option>
                                        @else
                                            <option value="">Seleccionar</option>
                                        @endisset
                                        @isset($branches)
                                            @foreach($branches as $branch)
                                                <option value="{{$branch->id}}">{{ $branch->description ?? '' }}</option>
                                            @endforeach
                                        @endisset
                                        </select>
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
                               
                             <br>


                                <div class="form-group row">
                                    <div class="col-sm-2">
                                        <label for="inputPassword">Clave</label>
                                        @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                        </label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="password" id="inputPassword" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Password" >
                                    </div>

                                    <div class="col-sm-2">
                                        <label for="inputConfirmPassword">Confirmación Clave:</label>
                                        </label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="password" id="inputConfirmPassword" class="form-control" placeholder="Password" name="password_confirmation" >
                                    </div>
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
                