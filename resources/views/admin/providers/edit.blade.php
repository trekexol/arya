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
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">Editar Proveedor</div>

                <div class="card-body">
                    <form  method="POST"   action="{{ route('providers.update',$var->id) }}" enctype="multipart/form-data" >
                        @method('PATCH')
                        @csrf()
                        
                        <div class="form-group row">
                            <label for="code_provider" class="col-sm-2 col-form-label text-md-right">Código de Proveedor </label>
                            <div class="col-md-1 col-sm-1">
                                <select id="type_code" name="type_code" class="select2_single form-control">
                                    @if ($var->code_provider[0] == 'V')
                                        <option selected value="V-">V-</option>
                                        <option value="">----------</option>
                                    @endif
                                    @if ($var->code_provider[0] == 'E')
                                        <option selected value="E-">E-</option>
                                        <option value="">----------</option>
                                    @endif
                                    @if ($var->code_provider[0] == 'J')
                                        <option selected value="J-">J-</option>
                                        <option value="">----------</option>
                                    @endif
                                    @if ($var->code_provider[0] == 'G')
                                        <option selected value="G-">G-</option>
                                        <option value="">----------</option>
                                    @endif
                                    @if ($var->code_provider[0] == 'O')
                                    <option selected value="O-">O-</option>
                                    <option value="">----------</option>
                                    @endif
                                    @if ($var->code_provider[0] == 'ID')
                                    <option selected value="ID-">ID-</option>
                                    <option value="">----------</option>
                                    @endif
                                    @if ($var->code_provider[0] == 'DNI')
                                    <option selected value="DNI-">DNI-</option>
                                    <option value="">----------</option>
                                    @endif
                                    @if ($var->code_provider[0] == 'NIT')
                                    <option selected value="NIT-">NIT-</option>
                                    <option value="">----------</option>
                                    @endif
                                    <option value="V-">V-</option>
                                    <option value="J-">J-</option>
                                    <option value="G-">G-</option>
                                    <option value="E-">E-</option>
                                    <option value="O-">O-</option>
                                    <option value="ID-">ID-</option>
                                    <option value="DNI-">DNI-</option>
                                    <option value="NIT-">NIT-</option>
                                </select>
                            </div>
                            @php
                                if(substr($var->code_provider, 0, 2) == 'V-'){
                                    $code_filter = substr($var->code_provider,2);
                                }if(substr($var->code_provider, 0, 2) == 'E-'){
                                    $code_filter = substr($var->code_provider,2);
                                }if(substr($var->code_provider, 0, 2) == 'J-'){
                                    $code_filter = substr($var->code_provider,2);
                                }if(substr($var->code_provider, 0, 2) == 'G-'){
                                    $code_filter = substr($var->code_provider,2);
                                }if(substr($var->code_provider, 0, 2) == 'O-'){
                                    $code_filter = substr($var->code_provider,2);
                                }if(substr($var->code_provider, 0, 2) == 'ID-'){
                                    $code_filter = substr($var->code_provider,2);
                                }if(substr($var->code_provider, 0, 2) == 'DNI-'){
                                    $code_filter = substr($var->code_provider,2);
                                }if(substr($var->code_provider, 0, 2) == 'NIT-'){
                                    $code_filter = substr($var->code_provider,2);
                                }
                            @endphp
                            <div class="col-sm-3">
                                <input id="code_provider" type="text" class="form-control @error('code_provider') is-invalid @enderror" name="code_provider" value="{{ $code_filter ?? $var->code_provider }}" required autocomplete="code_provider" autofocus>

                                @error('code_provider')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="razon_social" class="col-sm-2 col-form-label text-md-right">Razón Social</label>

                            <div class="col-sm-4">
                                <input id="razon_social" type="text" class="form-control @error('razon_social') is-invalid @enderror" name="razon_social" value="{{ $var->razon_social }}" required autocomplete="razon_social">

                                @error('razon_social')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="country" class="col-sm-2 col-form-label text-md-right">Pais</label>

                            <div class="col-sm-4">
                                <input id="country" type="text" class="form-control @error('country') is-invalid @enderror" name="country" value="{{ $var->country }}" required autocomplete="country">

                                @error('country')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="city" class="col-sm-2 col-form-label text-md-right">Ciudad</label>

                            <div class="col-sm-4">
                                <input id="city" type="text" class="form-control @error('city') is-invalid @enderror" name="city" value="{{ $var->city }}" required autocomplete="city">

                                @error('city')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                       
                        <div class="form-group row">
                            <label for="direction" class="col-sm-2 col-form-label text-md-right">Dirección</label>

                            <div class="col-sm-4">
                                <input id="direction" type="text" class="form-control @error('direction') is-invalid @enderror" name="direction" value="{{ $var->direction }}" required autocomplete="direction">

                                @error('direction')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="phone1" class="col-sm-2 col-form-label text-md-right">Teléfono</label>

                            <div class="col-sm-4">
                                <input id="phone1" type="text" class="form-control @error('phone1') is-invalid @enderror" name="phone1" value="{{ $var->phone1 }}" required autocomplete="phone1">

                                @error('phone1')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="phone2" class="col-sm-2 col-form-label text-md-right">Teléfono 2</label>

                            <div class="col-sm-4">
                                <input id="phone2" type="text" class="form-control @error('phone2') is-invalid @enderror" name="phone2" value="{{ $var->phone2 }}" required autocomplete="phone2">

                                @error('phone2')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                       

                        <div class="form-group row">
                            <label for="amount_max_credit" class="col-sm-2 col-form-label text-md-right">Monto Máximo de Crédito</label>

                            <div class="col-sm-4">
                                <input id="amount_max_credit" type="text" class="form-control @error('amount_max_credit') is-invalid @enderror" name="amount_max_credit" value="{{ number_format($var->amount_max_credit, 2, ',', '.') }}" required autocomplete="amount_max_credit">

                                @error('amount_max_credit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                              
                              <label for="balance" class="col-sm-2 col-form-label text-md-right">Saldo</label>

                              <div class="col-sm-4">
                                  <input id="balance" type="text" class="form-control @error('balance') is-invalid @enderror" name="balance" value="{{ number_format($var->balance, 2, ',', '.')}}" required autocomplete="balance">
  
                                  @error('balance')
                                      <span class="invalid-feedback" role="alert">
                                          <strong>{{ $message }}</strong>
                                      </span>
                                  @enderror
                              </div>
                        </div>

                       
                        <div class="form-group row">
                            <label for="email" class="col-sm-2 col-form-label text-md-right">Tiene Crédito</label>

                            <div class="form-check">
                                @if($var->has_credit == 1)
                                    <input class="form-check-input position-static" type="checkbox" onclick="calc();" id="has_credit" name="has_credit" value="true" checked aria-label="...">
                                @else
                                    <input class="form-check-input position-static" type="checkbox" onclick="calc();" id="has_credit" name="has_credit"  aria-label="...">
                                @endif
                            </div>
                            <label id="days_credit_label" for="days_credit" class="col-sm-2 col-form-label text-md-right">Dias de Crédito</label>

                            <div class="col-sm-1">
                            <input id="days_credit" type="text" class="form-control @error('days_credit') is-invalid @enderror" name="days_credit" value="{{ $var->days_credit }}" required autocomplete="days_credit">

                            @error('days_credit')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="porc_retencion_iva" class="col-sm-2 col-form-label text-md-right">Porcentaje Retención de IVA</label>

                            <div class="col-sm-4">
                                <input id="porc_retencion_iva" type="text" class="form-control @error('porc_retencion_iva') is-invalid @enderror" name="porc_retencion_iva" value="{{ $var->porc_retencion_iva }}" required autocomplete="porc_retencion_iva">

                                @error('porc_retencion_iva')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="islr_retencion" class="col-md-2 col-form-label text-md-right">Retencion ISLR:</label>

                                <div class="col-sm-3">
                                
                                <select class="form-control" name="islr_concept" id="islr_concept">
                                    <option disabled selected value="0">Seleccionar</option>
                                    @if ($var->concepto_islr > 0)
                                        @foreach ($islrconcepts as $islrconcept)
                                             @if($var->concepto_islr == $islrconcept->id)
                                             <option selected value="{{$islrconcept->id}}" data-id="{{$islrconcept->value}}" >{{ $islrconcept->description }} - {{$islrconcept->value}}%</option>
                                             @else
                                             <option value="{{$islrconcept->id}}" data-id="{{$islrconcept->value}}" >{{ $islrconcept->description }} - {{$islrconcept->value}}%</option>
                                             @endif
                                        @endforeach
                                    @else
                                        @foreach ($islrconcepts as $islrconcept)
                                        <option value="{{$islrconcept->id}}" data-id="{{$islrconcept->value}}" >{{ $islrconcept->description }} - {{$islrconcept->value}}%</option>
                                       @endforeach
                                    @endif
                                </select>
                                </div>
                                <label for="porc_retencion_islr" class="col-md-3 col-form-label text-md-right">Porcentaje Retención de ISLR</label>

                                <div class="col-sm-3">
                                    <input id="porc_retencion_islr" type="text" class="form-control @error('porc_retencion_islr') is-invalid @enderror" name="porc_retencion_islr" value="{{ $var->porc_retencion_islr ?? 0}}"  autocomplete="porc_retencion_islr" readonly>
    
                                    @error('porc_retencion_islr')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                       </div>

                        <div class="form-group row">
                            <label for="segmento" class="col-sm-2 col-form-label text-md-right">Status</label>
                            <div class="col-sm-2">
                                <select class="form-control" id="status" name="status" title="status">
                                    @if($var->status == 1)
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
                        <div class="form-group row mb-0">
                            <div class="form-group col-sm-4 offset-sm-2">
                                <button type="submit" class="btn btn-success btn-block"><i class="fa fa-send-o"></i>Actualizar</button>
                            </div>
                            <div class="form-group col-sm-4">
                                <a href="{{ route('providers') }}" name="danger" type="button" class="btn btn-danger btn-block">Cancelar</a>
                            </div>
                        </div>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('validacion')
    <script>    
	$(function(){
        
        soloLetras('country');
        soloLetras('city');

       
    });
    $(document).ready(function () {
            $("#phone1").mask('0000 000-0000', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#phone2").mask('0000 000-0000', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#porc_retencion_iva").mask('000', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#porc_retencion_islr").mask('000', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#amount_max_credit").mask('000.000.000.000.000.000,00', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#balance").mask('000.000.000.000.000.000,00', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#days_credit").mask('000', { reverse: true });
            
        });

        $("#islr_concept").on('change',function(){
            
            document.getElementById("porc_retencion_islr").value = $(this).find(':selected').data('id');
           
        });



        var has_credit = document.getElementById('has_credit').value;
        
        if(has_credit == "true"){
            $("#days_credit_label").show();
            $("#days_credit").show();
        }else{
            $("#days_credit_label").hide();
            $("#days_credit").hide();
        }

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
    </script>
@endsection