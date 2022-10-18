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
        <div class="col-12">
            <div class="card">
                <div class="card-header text-center font-weight-bold h3">Registro de Compañía</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('companies.store') }}">
                        @csrf
                        <input type="hidden" id="dolar" name="dolar"   value="{{  $bcv }}">
                        <div class="form-group row">
                            <label for="login" class="col-sm-2 col-form-label">Login(*)</label>

                            <div class="col-sm-4">
                                <input id="login" type="text" class="form-control @error('login') is-invalid @enderror" name="Login" value="{{ $company->login ?? '' }}" required autocomplete="login" autofocus>

                                @error('login')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="email" class="col-sm-2 col-form-label ">Correo Electronico(*)</label>

                            <div class="col-sm-4">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="Email" value="{{ $company->email ?? '' }}" required autocomplete="email" autofocus>

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
                                <input id="code_rif" type="text" class="form-control @error('code_rif') is-invalid @enderror" name="Codigo" value="{{ $company->code_rif ?? '' }}" required autocomplete="code_rif" autofocus>
                                @error('code_rif')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="razon_social" class="col-sm-2 col-form-label ">Razon Social(*)</label>
                            <div class="col-sm-4">
                                <input id="razon_social" type="text" class="form-control @error('razon_social') is-invalid @enderror" name="Razon_Social" value="{{ $company->razon_social ?? '' }}" required autocomplete="razon_social" autofocus>
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
                                <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ $company->phone ?? '' }}" required autocomplete="phone" autofocus >

                                @error('phone')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="franqueo_postal" class="col-sm-2 col-form-label">Franqueo Postal(*)</label>

                            <div class="col-sm-4">
                                <input id="franqueo_postal" type="text" class="form-control @error('franqueo_postal') is-invalid @enderror" name="Franqueo_Postal" value="{{ $company->franqueo_postal ?? '' }}" autocomplete="franqueo_postal" >

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
                                <input id="andress" type="text" class="form-control @error('andress') is-invalid @enderror" name="Direccion" value="{{ $company->address ?? '' }}" required autocomplete="andress" autofocus>

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
                                <input id="tax_1" type="text" class="form-control @error('tax_1') is-invalid @enderror" name="Impuesto" value="{{ $company->tax_1 ?? 0 }}" required autocomplete="tax_1" autofocus >

                                @error('tax_1')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="tax_2" class="col-sm-2 col-form-label">Impuesto-2</label>
                            <div class="col-sm-4">
                                <input id="tax_2" type="text" class="form-control @error('tax_2') is-invalid @enderror" name="Impuesto_2" value="{{ $company->tax_2 ?? 0 }}"  autocomplete="tax_2">

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
                                <input id="tax_3" type="text" class="form-control @error('tax_3') is-invalid @enderror" name="Impuesto_3" value="{{ $company->tax_3 ?? 0 }}"  autocomplete="tax_3" autofocus>

                                @error('tax_3')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="retencion" class="col-sm-2 col-form-label">Retencion-ISRL:</label>
                            <div class="col-sm-4">
                                <input id="retencion" type="text" class="form-control @error('retencion') is-invalid @enderror" name="Retencion_ISRL" value="{{ $company->retention_islr ?? 0 }}"  autocomplete="retencion">

                                @error('retencion')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="iva_p" class="col-sm-2 col-form-label ">Iva Percibido %:</label>
                            <div class="col-sm-4">
                                <input id="iva_p" type="text" class="form-control @error('iva_p') is-invalid @enderror" name="iva_p" value="{{ $company->iba_percibido_porc ?? 0 }}"  autocomplete="iva_p">

                                @error('iva_p')
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
                                @if (isset($company->tipoinv_id))
                                <select class="form-control" id="tipo_inv" name="Tipo_Inventario">
                                    <option value="{{ $company->tipoinv_id }}" required>{{ $company->tipoinv['description'] }}</option>
                                    <option value="" disabled>-----------</option>
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
                                @endif
                            </div>
                            <div class="col-sm-2">
                                <label for="tipo_rate">Tipo Tasa:</label>
                            </div>
                            <div class="col-sm-4">
                                @if (isset($company->tiporate_id))
                                <select class="form-control" id="rate_type" name="rate_type">
                                    <option value="{{ $company->tiporate_id }}" required>{{ $company->tiporate['description'] }}</option>
                                    <option value="" disabled>-----------</option>
                                    @foreach($tiporates as $index => $value)
                                        <option value="{{ $index }}" {{ old('rate_type') == $index ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>

                                @if ($errors->has('rate_type'))
                                    <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('rate_type') }}</strong>
                                            </span>
                                @endif
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tax_3" class="col-sm-2 col-form-label ">Tasa</label>
                            <div class="col-sm-4">
                                    <input id="rate" type="text" class="form-control @error('rate') is-invalid @enderror" name="Tasa" value="{{ $company->rate ?? 0 }}" required autocomplete="rate" autofocus>

                                @error('rate')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="rate_petro" class="col-sm-2 col-form-label">Tasa Petro:</label>
                            <div class="col-sm-4">
                                <input id="rate_petro" type="text" class="form-control @error('rate_petro') is-invalid @enderror" name="Tasa_Petro" value="{{ $company->rate_petro ?? 0 }}" required autocomplete="rate_petro" >

                                @error('rate_petro')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="periodo" class="col-sm-2 col-form-label">Periodo Actual:</label>
                            <div class="col-sm-4">
                                <input id="periodo" type="text" class="form-control @error('periodo') is-invalid @enderror" name="Periodo" value="{{ $periodo }}" required autocomplete="periodo" readonly>

                                @error('periodo')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="pie_factura" class="col-sm-2 col-form-label">Pie de Factura:</label>
                            <div class="col-sm-4">
                                <input id="pie_factura" type="text" class="form-control @error('pie_factura') is-invalid @enderror" name="pie_factura" value="{{ $company->pie_factura ?? null }}" autocomplete="pie_factura" >

                                @error('pie_factura')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="message_from_email" class="col-sm-2 col-form-label">Mensaje Email:</label>
                            <div class="col-sm-10">
                                <input id="message_from_email" type="text" class="form-control @error('message_from_email') is-invalid @enderror" name="message_from_email" value="{{ $company->message_from_email ?? '' }}"  autocomplete="message_from_email" >

                                @error('message_from_email')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="form-group  col-sm-2 offset-sm-4">
                                <button type="submit" class="btn btn-info btn-block"><i class="fa fa-send-o"></i>Guardar</button>
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
	
    $(document).ready(function () {
        $("#phone").mask('0000 000-0000', { reverse: true });
            
    });
    $(document).ready(function () {
        $("#rate").mask('000.000.000.000.000.000.000.000,00', { reverse: true });
            
    });
    $(document).ready(function () {
        $("#rate_petro").mask('000.000.000.000.000.000.000.000,00', { reverse: true });
            
    });
    $(document).ready(function () {
        $("#retencion").mask('000.000.000.000.000.000.000.000,00', { reverse: true });
            
    });
    $(document).ready(function () {
        $("#tax_1").mask('000,00', { reverse: true });
            
    });
    $(document).ready(function () {
        $("#tax_2").mask('000,00', { reverse: true });
            
    });
    $(document).ready(function () {
        $("#tax_3").mask('000,00', { reverse: true });
            
    });

    /*$("#tipo_rate").change(function(){
        var opc     = $("#tipo_rate").val();
        var dolar   =document.getElementById("dolar").value;

        if(opc == '1'){
            $("#tasa").attr("readonly", true);
            document.getElementById("tasa").value = "";
            document.getElementsByName("Tasa")[0].value = dolar;
        }else if(opc == '2'){
            $("#tasa").attr("readonly", false);
            document.getElementById("tasa").value = "";
        }else{
            document.getElementById("tasa").value = "";
            $("#tasa").attr("readonly", true);
            document.getElementsByName("Tasa")[0].value = '1';
        }

    });*/

        $("#rate_type").on('change',function(){
            var rate_type = $(this).val();
            
            getSearch(rate_type);
        });

        function getSearch(rate_type){
            
            if(rate_type == 1){

            $.ajax({
                url:"{{ route('companies.bcvlist') }}",
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                   
                    if(response.length > 0){

                        document.getElementById("rate").value = response;
                    }
                   
                   
                },
                error:(xhr)=>{
                    
                }
            })
            }
        }

    </script>
@endsection
