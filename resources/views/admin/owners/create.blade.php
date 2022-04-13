
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
                <div class="card-header text-center font-weight-bold h3">Registro de Propietarios</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('owners.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input id="id_user" type="hidden" class="form-control @error('id_user') is-invalid @enderror" name="id_user" value="{{ Auth::user()->id }}" required autocomplete="id_user">
                        
                        <div class="form-group row">
                            <label for="type_code" class="col-md-2 col-form-label text-md-right">Código/ID:</label>
                                
                            <div style="display:none;">
                                <div class="col-md-1">
                                    <select class="form-control" name="type_code" id="type_code">
                                        <option value="J-">J-</option>
                                        <option value="G-">G-</option>
                                        <option value="V-">V-</option>
                                        <option value="E-">E-</option>
                                    </select>
                                </div>
                            </div>
                                <div class="col-md-3">
                                    <input id="cedula_rif" type="text" class="form-control @error('cedula_rif') is-invalid @enderror" name="cedula_rif" value="{{ old('cedula_rif') }}" required autocomplete="cedula_rif">
    
                                    @error('cedula_rif')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                           <div style="display:none;">
                                <label for="vendor" class="col-md-2 col-form-label text-md-right">Vendedor:</label>

                                <div class="col-md-3">
                                <select class="form-control" id="id_vendor" name="id_vendor">
                                    <option value="">Seleccione un Vendedor</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                    @endforeach
                                
                                </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-md-2 col-form-label text-md-right">Nombre:</label>

                            <div class="col-md-4">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div style="display:none;">
                            <label for="direction" class="col-md-2 col-form-label text-md-right">Nombre Comercial</label>

                            <div class="col-md-4">
                                <input id="namecomercial" type="text" class="form-control @error('namecomercial') is-invalid @enderror" name="namecomercial" value="{{ old('namecomercial') }}" autocomplete="namecomercial">

                                @error('namecomercial')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>                               
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="country" class="col-md-2 col-form-label text-md-right">Pais</label>

                            <div class="col-md-4">
                                <input id="country" type="text" class="form-control @error('country') is-invalid @enderror" name="country" value="Venezuela" required autocomplete="country">


                                @error('country')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <label for="city" class="col-md-2 col-form-label text-md-right">Ciudad</label>

                            <div class="col-md-3">
                                <input id="city" type="text" class="form-control @error('city') is-invalid @enderror" name="city" value="{{ old('city') }}" required autocomplete="city">

                                @error('city')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                        </div>
                        <div class="form-group row">
                            <label for="direction" class="col-md-2 col-form-label text-md-right">Dirección</label>

                            <div class="col-md-4">
                                <input id="direction" type="text" class="form-control @error('direction') is-invalid @enderror" name="direction" value="{{ old('direction') }}" required autocomplete="direction">

                                @error('direction')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div style="display:none;">
                                <label for="personcontact" class="col-md-2 col-form-label text-md-right">Persona Contacto</label>

                                <div class="col-md-4">
                                    <input id="personcontact" type="text" class="form-control @error('personcontact') is-invalid @enderror" name="personcontact" value="{{ old('personcontac') }}" autocomplete="personcontact">

                                    @error('personcontact')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                       </div>
                        <div class="form-group row">
                            <label for="phone1" class="col-md-2 col-form-label text-md-right">Teléfono</label>

                            <div class="col-md-4">
                                <input id="phone1" type="text" class="form-control @error('phone1') is-invalid @enderror" name="phone1" value="{{ old('phone1') }}" placeholder="0000 000-0000" required autocomplete="phone1">

                                @error('phone1')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="phone2" class="col-md-2 col-form-label text-md-right">Teléfono 2</label>

                            <div class="col-md-3">
                                <input id="phone2" type="text" class="form-control @error('phone2') is-invalid @enderror" name="phone2" value="{{ old('phone2') }}" placeholder="0000 000-0000"  autocomplete="phone2">

                                @error('phone2')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div style="display:none;">
                            <div class="form-group row">
                                <label for="email" class="col-md-2 col-form-label text-md-right">Tiene Crédito</label>

                                <div class="form-check">
                                    <input class="form-check-input position-static" type="checkbox" id="has_credit" name="has_credit" onclick="calc();" value="option1" aria-label="...">
                                </div>
                                <label id="days_credit_label" for="days_credit_label" class="col-md-2 col-form-label text-md-right">Dias de Crédito</label>

                                <div class="col-md-2">
                                    <input id="days_credit" type="text" class="form-control @error('days_credit') is-invalid @enderror" name="days_credit" value="{{ 0 ?? old('days_credit') }}"  autocomplete="days_credit">
    
                                    @error('days_credit')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                                
                        </div>
                        
                        <div class="form-group row">
                            <div style="display:none;">
                            <label for="amount_max_credit" class="col-md-2 col-form-label text-md-right">Monto Máximo de Crédito</label>

                            <div class="col-md-4">
                                <input id="amount_max_credit" type="text" class="form-control @error('amount_max_credit') is-invalid @enderror" name="amount_max_credit" value="{{ old('amount_max_credit') }}"  autocomplete="amount_max_credit">

                                @error('amount_max_credit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                            <label for="aliquot" class="col-md-2 col-form-label text-md-right">% Alicuota</label>

                            <div class="col-md-4">
                                <input id="aliquot" type="text" class="form-control @error('aliquot') is-invalid @enderror" name="aliquot" value="{{ old('aliquot') ?? 0 }}" autocomplete="aliquot">

                                @error('aliquot')
                                    <span class="invalid-feedback col-md-12" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                               
                            </div>
                            <div class="col-md-6">   
                                (Usar solo punto "." para separar los decimales en la Alicuota).
                            </div> 
                             
                        </div>

                        <div class="form-group row" style="display:none;">
                            <label for="percentage_retencion_iva" class="col-md-2 col-form-label text-md-right">Porcentaje Retención <br>de Iva</label>

                            <div class="col-md-4">
                                <input id="percentage_retencion_iva" type="text" class="form-control @error('percentage_retencion_iva') is-invalid @enderror" name="percentage_retencion_iva" value="{{ old('percentage_retencion_iva') ?? 0 }}"  autocomplete="percentage_retencion_iva">

                                @error('percentage_retencion_iva')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                              
                              <label for="percentage_retencion_islr" class="col-md-2 col-form-label text-md-right">Porcentaje Retención de ISLR</label>

                              <div class="col-md-3">
                                  <input id="percentage_retencion_islr" type="text" class="form-control @error('percentage_retencion_islr') is-invalid @enderror" name="percentage_retencion_islr" value="{{ old('percentage_retencion_islr') ?? 0 }}"  autocomplete="percentage_retencion_islr">
  
                                  @error('percentage_retencion_islr')
                                      <span class="invalid-feedback" role="alert">
                                          <strong>{{ $message }}</strong>
                                      </span>
                                  @enderror
                              </div>
                        </div>
                        
                        <div class="form-group row">
                            <label id="centro_costo_label" for="centro_costo" class="col-md-2 col-form-label text-md-right">Condominio:</label>
                                
                            <div class="col-sm-3">
                                <select class="form-control" id="id_cost_center" name="id_cost_center" title="cost_center">
                                    <option value="1">Ninguno</option>
                                    @if(!empty($branches))
                                        @foreach ($branches as $var)
                                            <option value="{{ $var->id }}">{{ $var->description }}</option>
                                        @endforeach
                                        
                                    @endif
                                
                                </select>
                            </div>
                            <label for="email" class="col-md-3 col-form-label text-md-right">Email</label>

                            <div class="col-md-3">
                                <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="sincorreo@outlook.com" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                       

                       
                        
                        <br>
                        <div class="form-group row mb-0">
                            <div class="col-md-3">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">
                                   Registrar Cliente
                                </button>
                                
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('owners') }}" name="danger" type="button" class="btn btn-danger">Cancelar</a>
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
        
        $("#days_credit_label").hide();
        $("#days_credit").hide();
        document.getElementById('days_credit').value = 0;


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

        $(document).ready(function () {
            $("#cedula_rif").mask('00000000000000', { reverse: true });
            
        });
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
            $("#percentage_retencion_iva").mask('000', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#percentage_retencion_islr").mask('000', { reverse: true });
            
        });
    </script>
@endsection

