@extends('admin.layouts.dashboard')

@section('content')



    {{-- VALIDACIONES-RESPUESTA--}}
    @include('admin.layouts.success')   {{-- SAVE --}}
    @include('admin.layouts.danger')    {{-- EDITAR --}}
    @include('admin.layouts.delete')    {{-- DELELTE --}}
    {{-- VALIDACIONES-RESPUESTA --}}
    <style>
        .error {

       color: #dc3545;
       font-size:100%;


       }
       </style>
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
                <div class="card-header text-center font-weight-bold h3">Registro de Proveedores</div>

                <div class="card-body">
                    <form method="POST" id="providerstore">
                        @csrf

                        <div class="form-group row">
                            <label for="code_provider" class="col-md-2 col-form-label text-md-right">Código de Proveedor</label>
                            <div class="col-md-1 col-sm-1">
                                <select id="type_code" name="type_code" class="select2_single form-control">
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
                            <div class="col-md-3">
                                <input id="code_provider" type="text" class="form-control @error('code_provider') is-invalid @enderror" name="code_provider" value="{{ old('code_provider') }}" autocomplete="code_provider" autofocus>

                                @error('code_provider')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="razon_social" class="col-md-2 col-form-label text-md-right">Razón Social</label>

                            <div class="col-md-4">
                                <input id="razon_social" type="text" class="form-control @error('razon_social') is-invalid @enderror" name="razon_social" value="{{ old('razon_social') }}" required autocomplete="razon_social">

                                @error('razon_social')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
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

                            <div class="col-md-4">
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

                            </div>


                        <div class="form-group row">
                            <label for="phone1" class="col-md-2 col-form-label text-md-right">Teléfono</label>

                            <div class="col-md-4">
                                <input id="phone1" type="text" class="form-control @error('phone1') is-invalid @enderror" name="phone1" value="{{ old('phone1') ?? 0 }}" autocomplete="phone1">

                                @error('phone1')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="phone2" class="col-md-2 col-form-label text-md-right">Teléfono 2</label>

                            <div class="col-md-4">
                                <input id="phone2" type="text" class="form-control @error('phone2') is-invalid @enderror" name="phone2" value="{{ old('phone2') ?? 0 }}" autocomplete="phone2">

                                @error('phone2')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>



                        <div class="form-group row">
                            <label for="amount_max_credit" class="col-md-2 col-form-label text-md-right">Monto Máximo de Crédito</label>

                            <div class="col-md-4">
                                <input id="amount_max_credit" type="text" class="form-control @error('amount_max_credit') is-invalid @enderror" name="amount_max_credit" value="{{old('amount_max_credit') ?? 0}}" autocomplete="amount_max_credit">

                                @error('amount_max_credit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                              <label for="balance" class="col-md-2 col-form-label text-md-right">Saldo</label>

                              <div class="col-md-4">
                                  <input id="balance" type="text" class="form-control @error('balance') is-invalid @enderror" name="balance" value="{{ old('balance') ?? 0 }}" autocomplete="balance">

                                  @error('balance')
                                      <span class="invalid-feedback" role="alert">
                                          <strong>{{ $message }}</strong>
                                      </span>
                                  @enderror
                              </div>
                        </div>


                        <div class="form-group row">
                                <label for="" class="col-md-2 col-form-label text-md-right">Tiene Crédito</label>

                                <div class="form-check">
                                    <input class="form-check-input position-static creditcheck"  type="checkbox" id="has_credit" name="has_credit" value="1" aria-label="...">
                                </div>
                                <div class="col-md-2">
                                    <label id="days_credit_label" for="days_credit_label" class=" col-form-label text-md-right">Dias de Crédito</label>
                                </div>
                                <div class="col-md-1">
                                  <input id="days_credit" type="text" class="form-control @error('days_credit') is-invalid @enderror" name="days_credit" value="{{ old('days_credit') ?? 0 }}" autocomplete="days_credit">

                                  @error('days_credit')
                                      <span class="invalid-feedback" role="alert">
                                          <strong>{{ $message }}</strong>
                                      </span>
                                  @enderror
                                </div>

                        </div>

                        <div class="form-group row">
                            <label for="porc_retencion_iva" class="col-md-2 col-form-label text-md-right">Porcentaje Retención de IVA</label>

                            <div class="col-md-4">
                                <input id="porc_retencion_iva" type="text" class="form-control @error('porc_retencion_iva') is-invalid @enderror" name="porc_retencion_iva" value="{{ old('porc_retencion_iva') ?? 0}}"  autocomplete="porc_retencion_iva">

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
                                    @if (isset($islrconcepts))
                                        @foreach ($islrconcepts as $islrconcept)
                                            <option value="{{$islrconcept->id}}"  data-id="{{$islrconcept->value}}" >{{ $islrconcept->description }} - {{$islrconcept->value}}%</option>
                                        @endforeach
                                    @endif
                                </select>
                                </div>
                                <label for="porc_retencion_islr" class="col-md-3 col-form-label text-md-right">Porcentaje Retención de ISLR</label>

                                <div class="col-sm-3">
                                    <input id="porc_retencion_islr" type="text" class="form-control @error('porc_retencion_islr') is-invalid @enderror" name="porc_retencion_islr" value="{{ old('porc_retencion_islr') ?? 0}}"  autocomplete="porc_retencion_islr" readonly>
    
                                    @error('porc_retencion_islr')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                       </div>
                        <br>
                        <div class="form-group row mb-0">
                            <div class="col-md-3 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                   Registrar proveedor
                                </button>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('providers') }}" id="btnfacturar" name="btnfacturar" class="btn btn-info" title="facturar">Ver Listado de Proveedores</a>
                            </div>
                        </div>
                        </div>
                        <br>
                    </form>


                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('javascript')
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

<script>

$('#dataTable').dataTable( {
        "ordering": false,
        "order": [],
            'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
    } );
$(document).ready(function() {

            $("#providerstore").validate({
                rules: {
                    code_provider: {
                        required: true,
                        maxlength: 20,
                    },
                    razon_social: {
                        required: true,
                        maxlength: 80,
                    },
                    country: {
                        required: true,
                    },
                    city:{
                        required: true,
                    },
                    direction:{
                        required: true,
                    },

                },
                messages: {
                    code_provider: {
                        required: "Ingrese Codigo de Proveedor",
                        maxlength: "El Codigo no puede ser mayor a 20 digitos"
                        },
                    razon_social: {
                        required: "Ingrese Razon Social",
                        maxlength: "Razon Social no puede Contener mas de 80 caracteres"
                        },
                    country: {
                        required: "Ingrese Pais",
                        },
                    city:{
                        required: "Ingrese Ciudad",
                    },
                    direction:{
                        required: "Ingrese Direccion",
                    }
                },




        submitHandler: function (form) {



            $.ajax({
            type: "post",
            url: "{{ route('providers.store') }}",
            dataType: "json",
            data: $(form).serialize(),
            success:function(response){
             if(response.error == true){
                Swal.fire({
                        icon: 'info',
                        title: 'Exito!',
                        html: response.msg,


                        })
                setTimeout("location.reload()", 2500);

             }else{

                Swal.fire({
                        icon: 'info',
                        title: 'Error..',
                        html: response.msg,
                        })
             }




         },
         error:(response)=>{


            Swal.fire({
                    icon: 'error',
                    title: 'Error2...',
                    html: response.msg,
                    });
         }
            });



            return false; // required to block normal submit since you used ajax
        }
            });




    $("#phone1").mask('0000 000-0000', { reverse: true });
    $("#phone2").mask('0000 000-0000', { reverse: true });
    $("#porc_retencion_iva").mask('000', { reverse: true });
    $("#amount_max_credit").mask('000.000.000.000.000.000,00', { reverse: true });
    $("#balance").mask('000.000.000.000.000.000,00', { reverse: true });
    $("#days_credit").mask('000', { reverse: true });



        $(function(){
            soloLetras('country');
            soloLetras('city');
        });


        $("#days_credit_label").hide();
        $("#days_credit").hide();
        document.getElementById('days_credit').value = 0;


        $(".creditcheck").click(function(e){

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


        });

        $("#islr_concept").on('change',function(){
            
            document.getElementById("porc_retencion_islr").value = $(this).find(':selected').data('id');
           
        });


});

</script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection
