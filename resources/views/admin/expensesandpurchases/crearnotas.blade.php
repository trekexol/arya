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

    <?php
    if(isset($_POST['tasa']) && isset($_POST['fac']) && isset($_POST['serie']) && isset($_POST['id']) && isset($_POST['idp']) && isset($_POST['coin'])){
        $tasa = decrypt($_POST['tasa']);
        $fac = decrypt($_POST['fac']);
        $serie = decrypt($_POST['serie']);
        $id = decrypt($_POST['id']);
        $idp = decrypt($_POST['idp']);
        $coin = decrypt($_POST['coin']);
        $activo = true;


    }
        else {
            $tasa = '0.00';
            $fac = '';
            $serie = '';
            $id = '';
            $idp = '';
            $activo = false;
            $coin = 'bolivares';
        }


        ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header text-center font-weight-bold h3">Registro de Nota de Debito </div>

                <div class="card-body">
                    <form id="notastore">
                        @csrf

                        <input id="id_user" type="hidden" class="form-control @error('id_user') is-invalid @enderror" name="id_user" value="{{ Auth::user()->id }}" required autocomplete="id_user">
                        <input id="id_provider" type="hidden" class="form-control @error('id_provider') is-invalid @enderror" name="id_provider" value="{{ $idp ?? null  }}" required autocomplete="id_client">
                        <input id="id_expense" type="hidden" class="form-control @error('id_expense') is-invalid @enderror" name="id_expense" value="{{ $id ?? null  }}" required autocomplete="id_expense">


                        <div class="form-group row">
                            <label for="invoices" class="col-sm-5 col-form-label text-md-right">Factura:</label>
                            <div class="col-sm-2">
                                <input id="invoice" type="text" class="form-control form-control-sm" name="invoice" value="{{ $fac ?? '' }}" readonly required autocomplete="invoice">
                            </div>
                            <div class="form-group col-sm-1">
                                <a href="#" title="Seleccionar Factura" data-toggle="modal" data-target="#MatchModal" name="matchvalue"><i class="fa fa-eye"></i></a>
                            </div>

                        </div>

                        @if($activo == TRUE)
                        <div class="form-group row">
                            <label for="invoices" class="col-sm-2 col-form-label text-md-right">Tipo de Cuenta:</label>
                            <div class="col-sm-2">
                                <select class="form-control form-control-sm" id="tipocuenta" name="tipocuenta">
                                    <option value="">Seleccione..</option>

                                    <option  value="devolucion" {{ old('tipocuenta') == 'devolucion' ? 'selected' : '' }}>Devolución</option>
                                    <option  value="descuento"  {{ old('tipocuenta') == 'descuento' ? 'selected' : '' }}>Descuento</option>
                                </select>
                            </div>
                            <label for="date" class="col-sm-1 col-form-label text-sm-right">Fecha:</label>
                            <div class="col-sm-2">
                                <input id="date_begin" type="date" class="form-control form-control-sm" name="date" value="{{ $datenow }}" required autocomplete="date">


                            </div>

                            <label for="importe" class="col-sm-2 col-form-label text-md-right">Monto de factura:</label>

                            <div class="col-sm-2">
                                <input id="importe" type="text" class="form-control form-control-sm" name="importe" value="{{ old('importe') }}" autocomplete="importe">
                            </div>


                        </div>

                        <div class="form-group row">

                            <label  class="col-sm-3 col-form-label text-md-right desclass">Porcentaje (%):</label>

                            <div class="col-sm-2 desclass">
                                <input id="pordes" type="text" class="form-control form-control-sm" name="pordes" value="{{ old('pordes') }}" autocomplete="importe">
                            </div>

                            <label  class="col-sm-3 col-form-label text-md-right desclass">Descuento Factura:</label>

                            <div class="col-sm-2 desclass">
                                <input id="despor" type="text" class="form-control form-control-sm" name="despor" value="{{ old('despor') }}" autocomplete="importe">
                                <input id="despor2" type="hidden" class="form-control form-control-sm" name="despor2" value="{{ old('despor2') }}" autocomplete="importe">

                            </div>

                        </div>

                        <div class="form-group row">

                            <label for="rate" class="col-sm-4 col-form-label text-md-right">Tasa:  {{ number_format($tasa ?? old('rate'), 10, ',', '.') }}</label>

                            <label for="rate" class="col-sm-4 col-form-label text-md-right">Moneda:  {{ $coin ?? old('coin') }}</label>

                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                            <table class="table table-light2 table-bordered" id="extabla">
                                <thead>
                                <tr>

                                    <th class="text-center">Descripción</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-center">Precio</th>
                                    <th class="text-center">Descuento</th>
                                    <th class="text-center">Sub Total</th>



                                </tr>
                                </thead>

                                <tbody>

                                    <?php
                                        $suma = 0.00;
                                        $nro = 1;
                                    ?>
                                        @foreach ($expense_details as $var)
                                        <?php

                                            if($coin != 'bolivares'){

                                                $precio = $var->price / $tasa;
                                                $percentage = (($precio * $var->amount) * $var->porc_discount)/100;
                                                $total_less_percentage = ($precio * $var->amount) - $percentage;
                                                $moneda = "$";

                                            }else{

                                                $precio = $var->price;
                                                $percentage = (($precio * $var->amount) * $var->porc_discount)/100;
                                                $total_less_percentage = ($precio * $var->amount) - $percentage;
                                                $moneda = "Bs";


                                            }


                                        ?>

                                            <tr>

                                            @if($var->exento == 1)
                                                <td style="text-align: center">{{ $var->description}} (E)</td>
                                            @else
                                                <td style="text-align: center">{{ $var->description}}</td>
                                            @endif

                                            <td style="text-align: right">
                                            {{$var->amount}}
                                            <input type="text" name="cantidad[]" id="{{ $nro }}" class="form-control form-control-sm cantidadval" value="" />
                                            <input type="hidden" name="prueba"  id="valoreal{{ $nro }}" class="form-control form-control-sm valoreal" value=" {{$var->amount}}" />


                                            <input type="hidden" name="idinventario[]" id="idinventario" class="form-control form-control-sm cantidadoriginal" value="{{ $var->id_inventory }}" />

                                            <input type="hidden" name="cantidadreal[]" id="cantidadreal" class="form-control form-control-sm cantidadoriginal" value=" {{$var->amount}}" />

                                        </td>
                                            <td style="text-align: right">
                                                {{number_format($precio, 2, ',', '.').' '.$moneda}}

                                            <input type="hidden" name="precio[]" id="precio" class="form-control form-control-sm cantidadoriginal" value="{{$precio }}" />

                                            </td>
                                            <td style="text-align: right">{{$var->porc_discount}}%</td>
                                            <td style="text-align: right">{{number_format($total_less_percentage, 2, ',', '.').' '.$moneda}}</td>
                                            <?php
                                                $suma += $total_less_percentage;
                                                $nro++;
                                            ?>


                                            </tr>
                                        @endforeach

                                        <tr>

                                            <td style="text-align: center">-------------</td>
                                            <td style="text-align: center">-------------</td>
                                            <td style="text-align: center">-------------</td>
                                            <td style="text-align: right" colspan="1">Total sin IVA</td>
                                            <td style="text-align: right">{{number_format($suma, 2, ',', '.').' '.$moneda}}</td>
                                            <input type="hidden" name="totalfact" id="totalfact" class="form-control form-control-sm cantidadoriginal" value="{{number_format($suma, 2, ',', '.') }}" />


                                            </tr>

                                </tbody>
                            </table>
                            </div>
                        </div>



                        <div class="form-group row">

                            <label for="observation" class="col-sm-2 col-form-label text-md-right">Observaciones:</label>

                            <div class="col-sm-8">
                                <input id="observation" type="text" class="form-control form-control-sm" name="observation" value="{{ old('observation') }}" autocomplete="observation">
                            </div>

                        </div>

                        <div class="form-group row">

                            <label for="note" class="col-sm-2 col-form-label text-md-right">Nota al Pie de Nota de Débito:</label>

                            <div class="col-sm-8">
                                <input id="note" type="text" class="form-control form-control-sm" name="note" value="{{ old('note') }}"  autocomplete="note">
                            </div>
                        </div>


                        <div class="form-group row">
                            <div class="col-sm-3 offset-sm-4">
                                <button type="submit" class="btn btn-info botonenviar">
                                  Registrar
                                </button>
                            </div>
                            <div class="col-sm-2">
                                <a href="{{ route('notas') }}" id="btnvolver" name="btnvolver" class="btn btn-danger" title="volver">Volver</a>
                            </div>

                            <div class="col-sm-2">
                                <a href="{{ route('crearnota') }}" id="btnvolver" name="btnvolver" class="btn btn-warning" title="limpiar">Limpiar</a>
                            </div>
                        </div>


                        </form>
                @else
                <div class="form-group row">
                    <div class="col-sm-12 text-center">
                        <a href="{{ route('notas') }}" id="btnvolver" name="btnvolver" class="btn btn-danger" title="volver">Volver</a>
                    </div>
                </div>
                @endif


                </div>

                <div class="modal modal-danger fade" id="MatchModal" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                        <div class="modal-content" id="modalfacturas">

                        </div>
                    </div>
                  </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('validacion')
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

<script type="text/javascript">


$(document).ready(function(){

    $("#invoiceform").hide();
    $(".desclass").hide();
    $(".cantidadval").hide();
    $(".cantidadoriginal").hide();
    $("#importe").mask('000.000.000.000.000.000.000,00', { reverse: true });
    $("#pordes").mask('00000000000000000000000', { reverse: true });
    $("#despor").mask('000000000000000000000.00', { reverse: true });
    $(".cantidadval").mask('000000000000000000000,00', { reverse: true });

    var totalFacturas = "<?php if(isset($suma)){ echo number_format($suma, 2, ',', '.'); } else{ }  ?>";

    $("#importe").val(totalFacturas);

            $("#tipocuenta").on('change',function(){

            value = $(this).val();

            if(value == ''){

                $("#pordes").val('');
                $("#despor").val('');
                $(".cantidadval").val('');

                $(".desclass").hide();
                $(".cantidadval").hide();



                }

            if(value == 'descuento'){

                $("#pordes").val('');
                $("#despor").val('');
                $(".cantidadval").val('');

                $(".desclass").show();
                $(".cantidadval").hide();



            }


            if(value == 'devolucion'){

                $(".cantidadval").val('');
                $("#pordes").val('');
                $("#despor").val('');

                $(".cantidadval").show();
                $(".desclass").hide();

            }

        });

        $("#pordes").on('change',function(){
                calculate(1);

        });

        $("#despor").on('change',function(){
                calculate(2);

        });

        $(".cantidadval").on('change',function(){

        idvalor = $(this).attr('id');
        cantidadreal = $("#valoreal"+idvalor).val();

        cantidad = $(this).val();

            if(parseFloat(cantidad) > parseFloat(cantidadreal)){


           $("#"+idvalor).val(0);

            Swal.fire({
                        icon: 'info',
                        title: 'Error..',
                        html: 'la cantidad ingresada es mayor que la cantidad real',
                        })

            }


         });




function calculate(valor) {
    var porc_discount = 0;
    var discount = 0;
    var totalBaseImponible = 0;
    var totalFactura = "<?php if(isset($suma)){ echo $suma; }  ?>";
        if (valor == '1'){
        porc_discount = $("#pordes").val();

                if(porc_discount > 100){
                discount = totalFactura * 100 / 100;

                totalBaseImponible = totalFactura - discount;
                discount =  discount.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});

                    $("#despor").val(discount);
                }else{

                    discount = totalFactura * porc_discount / 100;

                    totalBaseImponible = totalFactura - discount;
                    discount =  discount.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});
                    $("#despor").val(discount);

                    discount2 = discount.replace(".","");
                    $("#despor2").val(discount2);

                }

        }

        if (valor == '2'){
        discount = $("#despor").val();

        discount2 = discount.replace(".",",");

        $("#despor2").val(discount2);



        if(discount > parseFloat(totalFactura)){

            $("#pordes").val(0);

        }else{
            discount =  discount.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});

            porcentaje = (discount * 100) / totalFactura; // Regla de tres
            porc_discount = porcentaje;  // Quitar los decimales

            if(isNaN(porc_discount)){
                porc_discount = 100;
            }

            $("#pordes").val(porc_discount);

        }


        }

}//fin funcion calcular


    $('#extabla').DataTable({
                        "ordering": false,
                        "order": [],
                        'aLengthMenu': [[10], [10]]
                    });




$('[name="matchvalue"]').click(function(e){
        e.preventDefault();

       var url = "{{ route('selectfacturas') }}";

     $.post(url,{"_token": "{{ csrf_token() }}"},function(data){
            $("#modalfacturas").empty().append(data);

          });



     });






/*********************************VALIDADOR DE FORMULARIO************************************/
$("#notastore").validate({

        rules: {
            date: "required",
            invoice: "required",
            tipocuenta: "required",
            rate: "required",
            coin: "required",
            pordes: "required",
            despor: "required",

        },

        messages:{
            date: "Seleccione una Fecha",
            invoice: "Seleccione una Factura",
            tipocuenta: "Seleccione un Tipo de Cuenta",
            rate: "Ingrese Tasa valida",
            coin: "Seleccione una moneda",
            pordes: "Ingrese un %",
            despor: "Ingrese un %",


        },


/*MODIFICANDO PARA MOSTRAR LA ALERTA EN EL LUGAR QUE DESEO CON UN DIV*/
    errorPlacement: function(error, element) {


        if(element.attr("name") == "tipocuenta") {
        $("#tipocuenta").removeClass("error");
        $("#tipocuenta").addClass("is-invalid");

        $("#tipocuenta").attr("data-toggle","popover");
        $("#tipocuenta").attr("data-placement","top");
        $("#tipocuenta").attr("data-content","Seleccione un tipo de Cuenta");
        $("#tipocuenta").popover('show');

        }

        if(element.attr("name") == "rate") {

        $("#rate").removeClass("error");
        $("#rate").addClass("is-invalid");
        $("#rate").attr("data-toggle","popover");
        $("#rate").attr("data-placement","top");
        $("#rate").attr("data-content","Ingrese una Tasa Valida");
        $("#rate").popover('show');

        }

        if(element.attr("name") == "date") {

            $("#date_begin").removeClass("error");
            $("#date_begin").addClass("is-invalid");
            $("#date_begin").attr("data-toggle","popover");
            $("#date_begin").attr("data-placement","top");
            $("#date_begin").attr("data-content","Seleccione una Fecha");
            $("#date_begin").popover('show');

            }


            if(element.attr("name") == "pordes") {

            $("#pordes").removeClass("error");
            $("#pordes").addClass("is-invalid");
            $("#pordes").attr("data-toggle","popover");
            $("#pordes").attr("data-placement","top");
            $("#pordes").attr("data-content","Ingrese un %");
            $("#pordes").popover('show');

            }

            if(element.attr("name") == "despor") {

            $("#despor").removeClass("error");
            $("#despor").addClass("is-invalid");
            $("#despor").attr("data-toggle","popover");
            $("#despor").attr("data-placement","top");
            $("#despor").attr("data-content","Ingrese un %%");
            $("#despor").popover('show');

            }

        },

        submitHandler: function (form) {

            $.ajax({
                method: "POST",
                url: "{{ route('notastore') }}",
                data: $('#notastore').serialize(),
            success:function(response){
             if(response.error == true){
                Swal.fire({
                        icon: 'info',
                        title: 'Exito!',
                        html: response.msg,


                        })
                        window.location.replace("{{ route('notas')}}");


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
                    title: 'Error...',
                    html: response.msg,
                    });
         }
            });



            return false; // required to block normal submit since you used ajax
        }
    }); ///fin $("#registro").validate({





});
    </script>

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection
