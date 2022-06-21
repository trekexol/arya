@extends('admin.layouts.dashboard')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header text-center font-weight-bold h3">Registro de Usuarios</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-md-3 col-form-label text-md-right">Nombre</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus maxlength="40"/>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-3 col-form-label text-md-right">Correo Electronico</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-3 col-form-label text-md-right">Contraseña</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password_confirmation" class="col-md-3 col-form-label text-md-right">Confirmar Contraseña</label>

                            <div class="col-md-6">
                                <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                       
                        <div class="form-group row">
                            <label for="rol" class="col-md-3 col-form-label text-md-right">Sucursal</label>
                            <div class="col-md-4">
                                <select class="form-control" id="id_branch" name="id_branch">
                                @isset($branches)
                                    @foreach($branches as $branch)
                                        <option value="{{$branch->id}}">{{ $branch->description ?? '' }}</option>
                                    @endforeach
                                @endisset
                                </select>
                            </div>
                        </div>
                         <div class="form-group row">
                            <label for="rol" class="col-md-3 col-form-label text-md-right">Rol</label>

                            <div class="col-md-3">
                            <select class="form-control" id="roles_id" name="roles_id">
                                <option value="1">Administrador</option>
                                <option value="2">Usuario</option>
                                <option value="11">Propietario Condominio</option>
                            </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="rol" class="col-md-3 col-form-label text-md-right">Status</label>

                            <div class="col-md-3">
                            <select class="form-control" name="status" id="status">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-2 offset-md-3">
                                <button type="submit" class="btn btn-primary">
                                   Registrar
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('users') }}" id="btnvolver" name="btnvolver" class="btn btn-danger" title="volver">Volver</a>  
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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
