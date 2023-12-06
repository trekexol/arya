@extends('admin.layouts.dashboard')

@section('content')



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
                <div class="card-header text-center font-weight-bold h3">Registro de Almacenes</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('warehouse.store') }}" enctype="multipart/form-data" width="100%">
                        @csrf

                        <div class="form-group row">
                                <label for="description" class="col-md-2 col-form-label text-md-right">Nombre</label>

                                <div class="col-md-4">
                                    <input id="description" type="text" class="form-control @error('description') is-invalid @enderror" name="description" value="{{ old('description') }}" maxlength="150" required autocomplete="description">

                                    @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <label for="direction" class="col-md-2 col-form-label text-md-right">Dirección</label>

                                <div class="col-md-4">
                                    <input id="direction" type="text" class="form-control @error('direction') is-invalid @enderror" name="direction" value="{{ old('direction') }}" autocomplete="direction">

                                    @error('direction')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                        </div>
                        <div class="form-group row">
                            <label for="phone" class="col-md-2 col-form-label text-md-right">Teléfono</label>

                            <div class="col-md-4">
                                <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" maxlength="30" autocomplete="phone">

                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="phone2" class="col-md-2 col-form-label text-md-right">Teléfono 2</label>

                            <div class="col-md-4">
                                <input id="phone2" type="text" class="form-control @error('phone2') is-invalid @enderror" name="phone2" value="{{ old('phone2') }}" maxlength="30" autocomplete="phone2">

                                @error('phone2')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                       

                        <div class="form-group row">
                            <label for="person_contact" class="col-md-2 col-form-label text-md-right">Persona Contácto</label>

                            <div class="col-md-4">
                                <input id="person_contact" type="text" class="form-control @error('person_contact') is-invalid @enderror" name="person_contact" value="{{ old('person_contact') }}" maxlength="160" autocomplete="person_contact">

                                @error('person_contact')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                              
                            <label for="phone_contact" class="col-md-2 col-form-label text-md-right">Teléfono Contácto</label>

                            <div class="col-md-4">
                                <input id="phone_contact" type="text" class="form-control @error('phone_contact') is-invalid @enderror" name="phone_contact" value="{{ old('phone_contact') }}" maxlength="30" autocomplete="phone_contact">

                                @error('phone_contact')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                       
                        <div class="form-group row">
                            <label for="observation" class="col-md-2 col-form-label text-md-right">Observación</label>

                            <div class="col-md-4">
                                <input id="observation" type="text" class="form-control @error('observation') is-invalid @enderror" name="observation" value="{{ old('observation') }}" autocomplete="observation">

                                @error('observation')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="status" class="col-md-2 col-form-label text-md-right">Status</label>

                            <div class="col-md-4">
                            <select class="form-control" name="status" id="status">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="company_id" class="col-md-2 col-form-label text-md-right">Compañías</label>

                            <div class="col-md-4">
                                <select class="form-control" id="company_id" name="company_id">
                                    @foreach($companies as $var)
                                        <option value="{{ $var->id }}" {{ old('Companies') }}>{{ $var->razon_social }}</option>
                                    @endforeach
                                
                                </select>
                            </div>
                        </div>                       

                        <br>
                        <div class="form-group row">
                            <div class="col-sm-2">
                            </div>
                            <div class="col-sm-4">
                                <button type="submit" class="btn btn-primary">
                                   Registrar Almacén
                                </button>
                            </div>
                            <div class="col-sm-3">
                                <a href="{{route('warehouse')}}" class="btn btn-danger">
                                   Salir
                                </a>
                            </div>
                        </div>
                        <br>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('validacion')
    <script>    
    </script>
@endsection
@section('javascript')
    <script>
    </script>
@endsection
