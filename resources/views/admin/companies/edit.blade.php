@extends('admin.layouts.dashboard')

@section('content')

    <!-- container-fluid -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="row py-lg-2">
            <div class="col-md-6">
                <h2>Editar de Compañía </h2>
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
            <form  method="POST"   action="{{ route('companies.update',$company->id) }}" enctype="multipart/form-data" >
                @method('PATCH')
                @csrf()
                    <input type="hidden" id="dolar" name="dolar"   value="{{  $bcv }}">
                    <div class="form-group row">
                        <label for="login" class="col-sm-2 col-form-label">Login(*)</label>

                        <div class="col-sm-4">
                            <?php
                            if ($company->login == 'nestornarvaez'){
                                
                                $company->login = 'frigosur';
                            
                            }
                            ?>
                            
                            <input id="login" type="text" class="form-control @error('login') is-invalid @enderror" name="Login" value="{{ $company->login }}" required autocomplete="login" autofocus>

                            @error('login')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <label for="email" class="col-sm-2 col-form-label ">Correo Electronico(*)</label>

                        <div class="col-sm-4">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="Email" value="{{ $company->email }}" required autocomplete="email" autofocus>

                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="code_rif" class="col-sm-2 col-form-label ">RIF(*)</label>
                        <div class="col-sm-4">
                            <input id="code_rif" type="text" class="form-control @error('code_rif') is-invalid @enderror" name="Codigo" value="{{ $company->code_rif }}" required autocomplete="code_rif" autofocus>
                            @error('code_rif')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <label for="razon_social" class="col-sm-2 col-form-label ">Razon Social(*)</label>
                        <div class="col-sm-4">
                            <input id="razon_social" type="text" class="form-control @error('razon_social') is-invalid @enderror" name="Razon_Social" value="{{ $company->razon_social }}" required autocomplete="razon_social" autofocus>
                            @error('razon_social')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="phone" class="col-sm-2 col-form-label ">Telefono(*)</label>
                        <div class="col-sm-4">
                            <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="Telefono" value="{{ $company->phone }}" required autocomplete="phone" autofocus maxlength="11">

                            @error('phone')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <label for="franqueo_postal" class="col-sm-2 col-form-label">Franqueo Postal(*)</label>

                        <div class="col-sm-4">
                            <input id="franqueo_postal" type="text" class="form-control @error('franqueo_postal') is-invalid @enderror" name="Franqueo_Postal" value="{{ $company->franqueo_postal }}" required autocomplete="franqueo_postal" >

                            @error('franqueo_postal')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="andress" class="col-sm-2 col-form-label ">Direccion(*)</label>
                        <div class="col-sm-10">
                            <input id="andress" type="text" class="form-control @error('andress') is-invalid @enderror" name="Direccion" value="{{ $company->address }}" required autocomplete="andress" autofocus>

                            @error('andress')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="tax_1" class="col-sm-2 col-form-label ">Impuesto(*)</label>
                        <div class="col-sm-4">
                            <input id="tax_1" type="text" class="form-control @error('tax_1') is-invalid @enderror" name="Impuesto" value="{{ $company->tax_1 }}" required autocomplete="tax_1" autofocus maxlength="3">

                            @error('tax_1')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <label for="tax_2" class="col-sm-2 col-form-label">Impuesto-2</label>
                        <div class="col-sm-4">
                            <input id="tax_2" type="text" class="form-control @error('tax_2') is-invalid @enderror" name="Impuesto_2" value="{{$company->tax_2}}"  autocomplete="tax_2" maxlength="3" >

                            @error('tax_2')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="tax_3" class="col-sm-2 col-form-label ">Impuesto-3</label>
                        <div class="col-sm-4">
                            <input id="tax_3" type="text" class="form-control @error('tax_3') is-invalid @enderror" name="Impuesto_3" value="{{$company->tax_3}}"  autocomplete="tax_3" autofocus maxlength="3">

                            @error('tax_3')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <label for="retencion" class="col-sm-2 col-form-label">Retencion-ISRL:</label>
                        <div class="col-sm-4">
                            <input id="retencion" type="text" class="form-control @error('retencion') is-invalid @enderror" name="Retencion_ISRL" value="{{$company->retention_islr}}"  autocomplete="retencion" maxlength="3" >

                            @error('retencion')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-2">
                            <label for="tipo_inv">Tipo de Inventario:</label>
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" id="tipo_inv" name="Tipo_Inventario">
                                <option value="{{$company->tipoinv_id}}" selected>{{$company->tipoinv['description']}}</option>
                                <option value="0">-------------------</option>
                                @foreach($tipoinvs as $index => $value)
                                    <option value="{{ $index }}" {{ old('Tipo_Inventario') == $index ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>

                            @if ($errors->has('tipo_inv'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('tipo_inv') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-sm-2">
                            <label for="tipo_rate">Tipo Tasa:</label>
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" id="tipo_rate" name="Tipo_Tasa">
                                <option value="{{$company->tiporate_id}}" selected>{{$company->tiporate['description']}}</option>
                                <option value="0">-------------------</option>
                                @foreach($tiporates as $index => $value)
                                    <option value="{{ $index }}" {{ old('Tipo_Tasa') == $index ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>

                            @if ($errors->has('tipo_rate'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('tipo_rate') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="tax_3" class="col-sm-2 col-form-label ">Tasa</label>
                        <div class="col-sm-4">
                            <input id="tasa" type="text" class="form-control @error('tasa') is-invalid @enderror" name="Tasa" value="{{ $company->rate}}" required autocomplete="tasa" autofocus readonly>

                            @error('tasa')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <label for="tasa_petro" class="col-sm-2 col-form-label">Tasa Petro:</label>
                        <div class="col-sm-4">
                            <input id="tasa_petro" type="text" class="form-control @error('tasa_petro') is-invalid @enderror" name="Tasa_Petro" value="{{ $company->rate_petro }}" required autocomplete="tasa_petro" >

                            @error('tasa_petro')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="periodo" class="col-sm-2 col-form-label">Periodo Actual</label>
                        <div class="col-sm-4">
                            <input id="periodo" type="text" class="form-control @error('periodo') is-invalid @enderror" name="Periodo" value="{{ $periodo }}" required autocomplete="periodo" readonly>

                            @error('periodo')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="form-group  col-sm-6 ">
                            <button type="submit" class="btn btn-success btn-block"><i class="fa fa-send-o"></i>Modificar</button>
                        </div>
                        <div class="form-group col-sm-6">
                            <a href="{{route('danger','companies')}}" name="danger" type="button" class="btn btn-danger btn-block">Cancelar</a>
                        </div>
                    </div>
            </form>
        </div>
    </div>
@endsection

@section('validacion')
    <script>
	$(function(){
        soloLetras('name');
        soloAlfaNumerico('description');
    });
    </script>
@endsection
