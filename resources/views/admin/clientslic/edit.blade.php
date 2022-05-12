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
                <div class="card-header h4">Editar Cliente</div>
                <div class="card-body">
                    <form  method="POST"   action="{{ route('clients.update',$var->id) }}" enctype="multipart/form-data" >
                        @method('PATCH')
                        @csrf()
                        <div class="card-body">
                            <form method="POST" action="{{ route('clients.store') }}" enctype="multipart/form-data">
                                @csrf
                                <input id="id_user" type="hidden" class="form-control @error('id_user') is-invalid @enderror" name="id_user" value="{{ Auth::user()->id }}" required autocomplete="id_user">
                                <div class="form-group row">
                                    <label for="type_code" class="col-md-2 col-form-label text-md-right">Código, Cédula / Rif:</label>
                                    <div class="col-md-1">
                                        <select class="form-control" name="type_code" id="type_code">
                                            <option value="{{ $var->type_code }}">{{ $var->type_code }}</option>
                                            <option >-------------------------</option>
                                            <option value="J-">J-</option>
                                            <option value="G-">G-</option>
                                            <option value="V-">V-</option>
                                            <option value="E-">E-</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input id="cedula_rif" type="text" class="form-control @error('cedula_rif') is-invalid @enderror" name="cedula_rif" value="{{ $var->cedula_rif }}" required autocomplete="cedula_rif">
                                        @error('cedula_rif')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <label for="vendor" class="col-md-2 col-form-label text-md-right">Vendedor:</label>
                                    <div class="col-md-3">
                                        <select class="form-control" id="id_vendor" name="id_vendor">
                                            @if(empty( $var->vendors['id'] ))
                                                <option value="">Sin Vendedor</option>
                                                <option value="">--------------</option>
                                            @else
                                                <option value="{{ $var->vendors['id'] }}">{{ $var->vendors['name'] }}</option>
                                                <option value="">--------------</option>
                                            @endif
                                            @foreach($vendors as $vendor)
                                                <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                        <div class="form-group row">
                            <div class="col-sm-2">
                                <label for="name">Nombre:</label>
                            </div>
                            <div class="col-sm-4">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $var->name }}" placeholder =" Nombre o Razon Social" required autocomplete="name" autofocus>
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-sm-2">
                                <label for="licencia">Licencia:</label>
                            </div>
                            <div class="col-sm-4">
                                <input id="licencia" type="text" class="form-control @error('licencia') is-invalid @enderror" name="Licencia" value="{{ $var->licence }}" placeholder =" Licencia de Licores" required autocomplete="licencia" autofocus>
                                @error('licencia')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="country" class="col-md-2 col-form-label text-md-right">Pais</label>

                            <div class="col-md-4">
                                <input id="country" type="text" class="form-control @error('country') is-invalid @enderror" name="country" value="{{ $var->country }}" required autocomplete="country">

                                @error('country')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="city" class="col-md-2 col-form-label text-md-right">Ciudad</label>

                            <div class="col-md-4">
                                <input id="city" type="text" class="form-control @error('city') is-invalid @enderror" name="city" value="{{ $var->city }}" required autocomplete="city">

                                @error('city')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-2">
                                <label for="direction">Dirección:</label>
                            </div>
                            <div class="col-sm-10">
                                <input id="direction" type="text" class="form-control @error('direction') is-invalid @enderror" name="direction" value="{{ $var->direction }}" required autocomplete="direction">

                                @error('direction')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-2">
                                <label for="direction_destiny">Dirección Destino:</label>
                            </div>
                            <div class="col-sm-10">
                                <input id="direction_destiny" type="text" class="form-control @error('direction_destiny') is-invalid @enderror" name="Direccion_Destino" value="{{ $var->destiny }}" required autocomplete="direction_destiny">

                                @error('direction_destiny')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-2">
                                <label for="direction_delivery">Dirección Entrega:</label>
                            </div>
                            <div class="col-sm-10">
                                <input id="direction_delivery" type="text" class="form-control @error('direction_delivery') is-invalid @enderror" name="Direccion_Entrega" value="{{ $var->delivery }}" required autocomplete="direction_delivery">

                                @error('direction_delivery')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="amount_max_credit" class="col-md-2 col-form-label text-md-right">Monto Máximo de Crédito</label>

                            <div class="col-md-4">
                            <input id="amount_max_credit" type="text" class="form-control @error('amount_max_credit') is-invalid @enderror" name="amount_max_credit" value="{{ $var->amount_max_credit }}" required autocomplete="amount_max_credit">

                            @error('amount_max_credit')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            </div>



                            <label for="email" class="col-md-2 col-form-label text-md-right">Moneda Bs.</label>
                            <div class="form-check">
                               
                                @if ($var->coin == 1)
                                <input class="form-check-input position-static" type="checkbox" id="has_mon" checked name="has_mon" onclick="bsfijo();" value="option1" aria-label="...">
                                <input id="moneda" type="hidden" name="moneda" value="1">
                                @else
                                <input class="form-check-input position-static" type="checkbox" id="has_mon" name="has_mon" onclick="bsfijo();" value="option1" aria-label="...">
                                <input id="moneda" type="hidden" name="moneda" value="0">
                                @endif
                                </div>

                        </div>
                        <div class="form-group row">
                            <label for="phone1" class="col-md-2 col-form-label text-md-right">Teléfono</label>

                            <div class="col-md-4">
                                <input id="phone1" type="text" class="form-control @error('phone1') is-invalid @enderror" name="phone1" value="{{ $var->phone1 }}" required autocomplete="phone1">

                                @error('phone1')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="phone2" class="col-md-2 col-form-label text-md-right">Teléfono 2</label>
                            <div class="col-md-4">
                                <input id="phone2" type="text" class="form-control @error('phone2') is-invalid @enderror" name="phone2" value="{{ $var->phone2 }}" required autocomplete="phone2">
                                @error('phone2')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email" class="col-md-2 col-form-label text-md-right">Tiene Crédito</label>
                            <div class="form-check">
                                @if (isset($var->days_credit))
                                    <input class="form-check-input position-static" type="checkbox" id="has_credit" checked name="has_credit" onclick="calc();" value="option1" aria-label="...">
                                @else
                                    <input class="form-check-input position-static" type="checkbox" id="has_credit" name="has_credit" onclick="calc();" value="option1" aria-label="...">
                                @endif
                               </div>
                              <label id="days_credit_label" for="days_credit_label" class="col-md-2 col-form-label text-md-right">Dias de Crédito</label>
                              <div class="col-md-2">
                                  <input id="days_credit" type="text" class="form-control @error('days_credit') is-invalid @enderror" name="days_credit" value="{{ $var->days_credit }}"  autocomplete="days_credit">

                                  @error('days_credit')
                                      <span class="invalid-feedback" role="alert">
                                          <strong>{{ $message }}</strong>
                                      </span>
                                  @enderror
                              </div>
                        </div>

                        <div class="form-group row">
                            <label for="retencion_iva" class="col-md-2 col-form-label text-md-right">Retención <br>de Iva</label>

                            <div class="col-md-4">
                                <input id="retencion_iva" type="text" class="form-control @error('retencion_iva') is-invalid @enderror" name="retencion_iva" value="{{ number_format($var->percentage_retencion_iva, 0, '', '.')}}" required autocomplete="retencion_iva">

                                @error('retencion_iva')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                              <label for="retencion_islr" class="col-md-2 col-form-label text-md-right">Retención de ISLR</label>

                              <div class="col-md-4">
                                  <input id="retencion_islr" type="text" class="form-control @error('retencion_islr') is-invalid @enderror" name="retencion_islr" value="{{ number_format($var->percentage_retencion_islr, 0, '', '.') }}" required autocomplete="retencion_islr">

                                  @error('retencion_islr')
                                      <span class="invalid-feedback" role="alert">
                                          <strong>{{ $message }}</strong>
                                      </span>
                                  @enderror
                              </div>
                        </div>
                        <div class="form-group row">
                            <label for="vendor" class="col-md-2 col-form-label text-md-right">Vendedor:</label>

                            <div class="col-md-3">
                            <select class="form-control" id="id_vendor" name="id_vendor">
                                <option value="{{ $var->id_vendor ?? '' }}">{{ $var->vendors['name'] ?? 'Seleccione un Vendedor' }}</option>
                                <option value="nulo" disabled>----------------</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                @endforeach

                            </select>
                            </div>

                            <label for="segmento" class="col-md-2 col-form-label text-md-right">Status</label>
                            <div class="col-md-4">
                                <select class="form-control" id="status" name="status" title="status">
                                    @if($var->status == 1)
                                        <option value="1">Activo</option>
                                    @else
                                        <option value="0">Inactivo</option>
                                    @endif
                                    <option value="nulo" disabled>----------------</option>

                                    <div class="dropdown">
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </div>


                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="form-group row mb-0">
                            <div class="form-group col-sm-2">
                            </div>
                            <div class="form-group col-sm-2">
                                <button type="submit" class="btn btn-success btn-block"><i class="fa fa-send-o"></i>Actualizar</button>
                            </div>
                            <div class="form-group col-sm-2">
                                <a href="{{ route('clients') }}" name="danger" type="button" class="btn btn-danger btn-block">Cancelar</a>
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
        soloAlfaNumerico('code_client');
        soloAlfaNumerico('razon_social');
        sololetras('name');
        sololetras('country');
        sololetras('city');
        soloAlfaNumerico('direction');
        sololetras('seller');
    });



    function calc()
    {
        if (document.getElementById('has_credit').checked)
        {
            $("#days_credit_label").show();
            $("#days_credit").show();

            document.getElementById('days_credit').value = 0;
        } else {
            $("#days_credit_label").hide();
            $("#days_credit").hide();
            document.getElementById('days_credit').value = 0;
        }
    }


    function bsfijo()
    {
        if (document.getElementById('has_mon').checked) {
            $("#moneda").val(1);

        } else {
            $("#moneda").val(0);
        }


    }


        $(document).ready(function () {
            $("#phone1").mask('0000 000-0000', { reverse: true });

        });
        $(document).ready(function () {
            $("#phone2").mask('0000 000-0000', { reverse: true });

        });
        $(document).ready(function () {
            $("#amount_max_credit").mask('000.000.000.000.000.000,00', { reverse: true });

        });
        $(document).ready(function () {
            $("#retencion_iva").mask('000', { reverse: true });

        });
        $(document).ready(function () {
            $("#retencion_islr").mask('000', { reverse: true });

        });
    </script>
@endsection
