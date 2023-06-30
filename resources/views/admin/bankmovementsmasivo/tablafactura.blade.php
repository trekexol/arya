<?php

if($tipo == 'match'){
?>

<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Facturas</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body" >

<table class="table table-light2 table-bordered" id="dataTablematch" >
    <thead>
    <tr>
        <th>Nro. Factura</th>
        <th>Cliente</th>
        <th>Monto</th>
        <th>Accion</th>


    </tr>
    </thead>
    <tbody>

            <?php

                foreach($quotations as $quotations){

                    echo "<tr>
                        <td>".$quotations['number_invoice']."</td>
                        <td>".$quotations->clients['name']."</td>
                        <td>".$quotations['amount_with_iva']."</td>";

                        echo "<td>
                            <button type='button' class='btn btn-outline-primary procesarfactura' value='$quotations[amount_with_iva]/$quotations[number_invoice]/$valormovimiento/$quotations[id]/$idmovimiento/$fechamovimiento/$bancomovimiento/$quotations[bcv]/$conta/$moneda'>Procesar</button>
                            </td>";

                       echo "</tr>";

                }

                ?>






    </tbody>
</table>

</div>

    <script>


$('.procesarfactura').click(function(e){
      e.preventDefault();

    var valor = $(this).val().split('/');
    var montoiva = valor[0];
    var nrofactura = valor[1];
    var montomovimiento = valor[2];
    var id = valor[3];
    var idmovimiento = valor[4];
    var fechamovimiento = valor[5];
    var bancomovimiento = valor[6];
    var tasa = valor[7];
    var conta = valor[8];
    var moneda = valor[9];
    $.ajax({
        method: "POST",
        url: "{{ route('procesarfact') }}",
        data: {moneda: moneda,conta: conta,tasa: tasa,bancomovimiento: bancomovimiento,fechamovimiento: fechamovimiento,montoiva: montoiva, nrofactura: nrofactura,montomovimiento: montomovimiento,id: id,idmovimiento: idmovimiento, "_token": "{{ csrf_token() }}"},
             success:(response)=>{

                 if(response == true){
                    Swal.fire({
                        icon: 'success',
                        title: 'Exito!',
                        text: 'Factura Procesada Exitosamente!',


                        })
                        $("#"+idmovimiento).empty().append();
                        $('#MatchModal').modal('hide');
                 }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Error...',
                        text: 'Error a Procesar Factura!',
                        })
                 }




             },
             error:(xhr)=>{
                Swal.fire({
                        icon: 'error',
                        title: 'Error...',
                        text: 'Error a Procesar!',
                        });
             }
         })







});

$('#dataTablematch').DataTable({
            "ordering": false,
            "order": [],
            'aLengthMenu': [[10, 20, 30, -1], [10, 20, 30, "All"]]
        });

    </script>


<?php

}
elseif($tipo == 'contra'){

    ?>


<div class="modal-header">

    <button type="button" class="add_button btn btn-secondary btn-sm">Agregar Contrapartida</button>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body" >
    <div class="table-responsive-xl">

        <table class="table table-sm">
            <thead>
                <tr>
                  <th scope="col">Banco</th>
                  <th scope="col">Descripcion</th>
                  <th scope="col">Moneda</th>
                  <th scope="col">Debe</th>
                  <th scope="col">Haber</th>
                  <th>Tasa</th>
                </tr>
              </thead>

              <tbody>

                <tr>
                    <th scope="row">{{$bancomovimiento}}</th>
                    <td>{{$descripcionbanco}}</td>
                    <td>{{$moneda}}</td>
                    <td>{{$valormovimiento}}</td>
                    <td>{{$montohaber}}</td>
                    <td><input id="rates" type="text" class="form-control form-control-sm" name="rates" value="{{ $bcv }}" ></td>

                  </tr>
              </tbody>
        </table>
      </div>
      <form id='pruebaform' >

      @csrf
      <input id="rate" type="hidden" class="form-control form-control-sm" name="rate" value="{{ $bcv }}" >
        <input type="hidden" id="valordebe" name="valordebe" value='{{$valormovimiento}}'>
        <input type="hidden" id="valorhaber" name="valorhaber" value='{{$montohaber}}'>
        <input type="hidden" name="referenciabanco" value='{{$referenciamovimiento}}'>
        <input type="hidden" name="banco" value='{{$bancomovimiento}}'>
        <input type="hidden" name="moneda" value='{{$moneda}}'>
        <input type="hidden" name="fechamovimiento" value='{{$fechamovimiento}}'>
        <input type="hidden" name="descripcionbanco" value='{{$descripcionbanco}}'>
        <input type="hidden" id="idmovimiento" name="idmovimiento" value='{{$idmovimiento}}'>

        <div class="field_wrapper"></div>


        <button type="button" class="btn btn-primary btn-sm procesarcontrapartida" >Procesar Contrapartida</button>

        </form>




 </div>


<script type="text/javascript">
    $(document).ready(function(){
        var x = 1;
        var contadordiv = 0;
        var maxField = 10;
        var addButton = $('.add_button');
        var wrapper = $('.field_wrapper');

        var debe = $('#valordebe').val();

        if(parseFloat(debe) > '0'){

            var montocontra = debe;

        }else{
            var haber = $('#valorhaber').val();
            var montocontra = haber;
        }




        $('.procesarcontrapartida').hide();

        $(addButton).click(function(){ //funcion para cuando agregue un campo

        if(contadordiv < maxField){  //si son mayor a 10 no permite mas campos
        var camposelect = "#selecontra"+x;
        var valor = x;


        var camposopcionales = '<div class="form-row" id="dinamicosdiv'+x+'">'+
            '<div class="form-group col-md-3">'+
                '<select  name="contra[]" id="selecontra'+x+'" class="form-control selecontra" required><option value="-1">Seleccione una Contrapartida</option>@foreach($contrapartidas as $index => $value) @if ($value != "Bancos" && $value != "Efectivo en Caja" && $value != "Superavit o Deficit" && $value != "Otros Ingresos" && $value != "Resultado del Ejercicio" && $value != "Resultados Anteriores") <option value="{{ $index }}" {{ old("type_form") == $index ? "selected" : "" }}>{{ $value }} </option> @endif @endforeach</select>'+
                '</div>'+
                '<div class="form-group col-md-3">'+

                    '<select  id="account_counterpart'+x+'"  name="valorcontra[]" class="form-control  account_counterpart" required> <option value="">Seleccionar</option> @if (isset($accounts_inventory)) @foreach ($accounts_inventory as $var) <option value="{{ $var->id }}">{{ $var->description }}</option> @endforeach @endif</select>'+

                        '</div>'+

                        '<div class="form-group col-md-3">'+

                            '<input type="text" class="form-control" placeholder="monto de la contrapartida" id="montosid'+x+'" name="montocontra[]" value="'+montocontra+'" />'+
                            '</div>'+
                            '<div class="form-group col-md-3">'+

                            '<button class="remove_button btn btn-outline-danger" value='+x+'>Eliminar</button>'+

                            '</div>'+
                            '</div>';



        $('.procesarcontrapartida').show();
        //$(wrapper).append(fieldHTML+fieldca);
        $(wrapper).append(camposopcionales);
        $(camposelect).on('change',function(){

           var contrapartida_id = $(this).val();

           getSubcontrapartida(contrapartida_id,valor);

       });
       $('.add_button').prop( 'disabled', false );
       contadordiv++;
            }else{

                $('.add_button').prop( 'disabled', true );

            }


            x++;

            function getSubcontrapartida(contrapartida_id,valor){

           $.ajax({
               url:"{{ route('listcontrapartidanew') }}" + '/' + contrapartida_id,
               beforSend:()=>{
                   alert('consultando datos');
               },
               success:(response)=>{

                    var camposelect2 = "#account_counterpart"+valor;
                   let subcontrapartida = $(camposelect2);
                   let htmlOptions = `<option value='' >Seleccione..</option>`;

                   if(response.length > 0){
                       response.forEach((item, index, object)=>{
                           let {id,description} = item;
                           htmlOptions += `<option value='${id}'>${description}</option>`

                       });
                   }

                   subcontrapartida.html('');
                   subcontrapartida.html(htmlOptions);


               },
               error:(xhr)=>{
                   alert('Presentamos inconvenientes al consultar los datos');
               }
           })
       }


        });

        $(wrapper).on('click', '.remove_button', function(e){
            e.preventDefault();
            valor = $(this).val();
            $("#dinamicosdiv"+valor).remove();
            $("#dinamicos"+valor).remove();

           contadordiv--;
           if(contadordiv == 0){
                $('.procesarcontrapartida').hide();
                $('.add_button').prop( 'disabled', false );

            }
        });




$('.procesarcontrapartida').click(function(e){
      e.preventDefault();
      $('.procesarcontrapartida').prop( 'disabled', true );
      idmovimiento = $('#idmovimiento').val();
      var rates = $('#rates').val();
      $('#rate').val(rates);

    $.ajax({
        method: "POST",
        url: "{{ route('procesarcontrapartidanew') }}",
        data: $('#pruebaform').serialize(),
             success:(response)=>{

                if(response.error == true){
                    $('.procesarcontrapartida').prop( 'disabled', false );

                Swal.fire({
                        icon: 'info',
                        title: 'Exito!',
                        html: response.msg,


                        })

                        $("#"+idmovimiento).empty().append();
                        $('#MatchModal').modal('hide');

                    }else{
                $('.procesarcontrapartida').prop( 'disabled', false );
                Swal.fire({
                        icon: 'info',
                        title: 'Error..',
                        html: response.msg,
                        })
             }




             },
             error:(xhr)=>{
                $('.procesarcontrapartida').prop( 'disabled', false );
                Swal.fire({
                        icon: 'error',
                        title: 'Error...',
                        text: 'Error a Procesar!',
                        });
             }
         })







});



    });
    </script>

<?php

}elseif($tipo == 'transferencia'){



                            if($valormovimiento == 0){
                                $monto = $montohaber;
                                $tipo = "HABER";
                            }elseif($montohaber == 0){
                                $monto = $valormovimiento;
                                $tipo = "DEBE";
                            }




?>
<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Transferencias entre Caja y Bancos</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body" >
    <div class="table-responsive-xl">
        <table class="table table-sm">
            <thead>
                <tr>
                  <th scope="col">Transferir desde</th>
                  <th scope="col">Número de Referencia</th>
                  <th scope="col">Fecha Transferenca</th>
                  <th scope="col">Moneda</th>
                  <th scope="col">Monto de la Transferencia</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                    <th scope="row">{{$account->description}}</th>
                    <td>{{$referenciamovimiento}}</td>
                    <td>{{ $fechamovimiento}}</td>
                    <td>{{$moneda}}</td>
                    <td> {{$monto}}</td>
                  </tr>
              </tbody>
        </table>
      </div>

      <form method="POST" id="procesartransf">
        @csrf
        <input id="id_account" type="hidden" class="form-control @error('id_account') is-invalid @enderror" name="id_account" value="{{ $account->id }}" required autocomplete="id_account" autofocus>
        <input id="user_id" type="hidden" class="form-control @error('user_id') is-invalid @enderror" name="user_id" value="{{ Auth::user()->id }}" required autocomplete="user_id">
        <input id="type_movement" type="hidden" class="form-control @error('type_movement') is-invalid @enderror" name="type_movement" value="TR" required autocomplete="type_movement" autofocus>
        <input id="date_begin" type="hidden" class="form-control @error('date_begin') is-invalid @enderror" name="date" value="{{ $fechamovimiento ?? old('date_begin') }}" required autocomplete="date_begin">
        <input id="reference" type="hidden" class="form-control @error('reference') is-invalid @enderror" name="reference" value="{{ $referenciamovimiento }}" required autocomplete="reference">
        <input class="form-control" type="hidden" name="coin" value="{{$moneda}}" id="coin">
        <input id="amount" type="hidden" class="form-control @error('amount') is-invalid @enderror" placeholder="0,00" name="amount" value="{{$monto}}" required autocomplete="amount">
        <input type="hidden" id="idmovimiento" name="idmovimiento" value='{{$idmovimiento}}'>
        <input type="hidden" name="descripcionbanco" value='{{$descripcionbanco}}'>



        <div class="form-group row">
            <label for="counterpart" class="col-md-1 col-form-label text-md-right">Desde:</label>

            <div class="col-md-3">
            <select class="form-control" id="iddesde" name="iddesde" required>
                <option value="">Selecciona una Cuenta</option>
                @foreach($counterparts as $vars)
                    <option value="{{ $vars->description.'/'.$vars->id }}">{{ $vars->description }}</option>
                @endforeach

            </select>
            </div>

            <label for="counterpart" class="col-md-1 col-form-label text-md-right">Hacia:</label>

            <div class="col-md-3">
            <select class="form-control" id="id_counterpart" name="id_counterpart" required>
                <option value="">Selecciona una Cuenta</option>
                @foreach($counterparts as $var)
                    <option value="{{ $var->id }}">{{ $var->description }}</option>
                @endforeach

            </select>
            </div>

            <label for="counterpart" class="col-md-1 col-form-label text-md-right">Tasa:</label>

            <div class="col-md-1">
            <input id="rate" type="text" class="form-control" name="rate" value="{{ $bcv }}" >

            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary  btn-sm procesartransferencia">
                    Guardar Transferencia
                 </button>
                </div>
        </div>


    </form>


</div>

<script type="text/javascript">
    $(document).ready(function(){



        $('.procesartransferencia').click(function(e){
      e.preventDefault();
      idmovimiento = $('#idmovimiento').val();

      $('.procesartransferencia').prop( 'disabled', true );
    $.ajax({
        method: "POST",
        url: "{{ route('guardartransferencia') }}",
        data: $('#procesartransf').serialize(),
             success:(response)=>{

                if(response.error == true){
                    $('.procesarcontrapartida').prop( 'disabled', false );


                    Swal.fire({
                        icon: 'info',
                        title: 'Exito!',
                        html: response.msg,


                        })

                        $("#"+idmovimiento).empty().append();
                        $('#MatchModal').modal('hide');

                    }else{
                        $('.procesartransferencia').prop( 'disabled', false );

                        Swal.fire({
                        icon: 'info',
                        title: 'Error..',
                        html: response.msg,
                        })
             }




             },
             error:(xhr)=>{
                Swal.fire({
                        icon: 'error',
                        title: 'Error...',
                        text: 'Error a Procesar!',
                        });
             }
         })







});











    });
    </script>
<?php
}

elseif($tipo == 'deposito'){

?>
<div class="modal-header">
<h5 class="modal-title">DEPOSITO</h5>

<button type="button" class="close" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
</div>
<div class="modal-body" >
    <form id='pruebaform' >
        @csrf
<div class="table-responsive-xl">

    <table class="table table-sm">
        <thead>
            <tr>
              <th scope="col">Banco</th>
              <th scope="col">Descripcion</th>
              <th scope="col">Referencia</th>
              <th scope="col">Moneda</th>
              <th scope="col">Debe</th>
              <th scope="col">Haber</th>
              <th scope="col">Tasa</th>
              <th scope="col">Acción</th>
            </tr>
          </thead>
          <tbody>
            <tr>
                <th scope="row">{{$bancomovimiento}}</th>
                <td>{{$descripcionbanco}}</td>
                <td>{{$referenciamovimiento}}</td>
                <td>{{$moneda}}</td>
                <td>{{$valormovimiento}}</td>
                <td>{{$montohaber}}</td>
                <td><input type="text" class="form-control form-control-sm" id="tasa" name="tasa" value="{{ $bcv}}">
                </td>
                <td><button type="button" class="add_button btn btn-secondary btn-sm">Agregar Contrapartida</button>
                </td>
              </tr>
          </tbody>
    </table>
  </div>




    <input type="hidden" id="valordebe" name="valordebe" value='{{$valormovimiento}}'>
    <input type="hidden" id="valorhaber" name="valorhaber" value='{{$montohaber}}'>
    <input type="hidden" name="referenciabanco" value='{{$referenciamovimiento}}'>
    <input type="hidden" name="banco" value='{{$bancomovimiento}}'>
    <input type="hidden" name="moneda" value='{{$moneda}}'>
    <input type="hidden" name="fechamovimiento" value='{{$fechamovimiento}}'>
    <input type="hidden" name="descripcionbanco" value='{{$descripcionbanco}}'>
    <input type="hidden" id="idmovimiento" name="idmovimiento" value='{{$idmovimiento}}'>


    <div class="field_wrapper">




      </div>


      <button type="button" class="btn btn-primary btn-sm procesarcontrapartida" >Procesar Contrapartida</button>



    </form>


</div>
</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
    var x = 1;
    var contadordiv = 0;
    var maxField = 10;
    var addButton = $('.add_button');
    var wrapper = $('.field_wrapper');
    idmovimiento = $('#idmovimiento').val();
    $('.procesarcontrapartida').hide();

    var debe = $('#valordebe').val();
    if(parseFloat(debe) > '0'){

            var montocontra = debe;
        }else{
            var haber = $('#valorhaber').val();
            var montocontra = haber;
        }

    $(addButton).click(function(){ //funcion para cuando agregue un campo

    if(contadordiv < maxField){  //si son mayor a 10 no permite mas campos
    var camposelect = "#selecontra"+x;
    var valor = x;


    var camposopcionales = '<div class="form-row" id="dinamicosdiv'+x+'">'+
        '<div class="form-group col-md-3">'+
            '<select  name="contra[]" id="selecontra'+x+'" class="form-control selecontra" required><option value="-1">Seleccione una Contrapartida</option>@foreach($contrapartidas as $index => $value) @if ($value != "Bancos" && $value != "Efectivo en Caja" && $value != "Superavit o Deficit" && $value != "Resultado del Ejercicio" && $value != "Resultados Anteriores") <option value="{{ $index }}" {{ old("type_form") == $index ? "selected" : "" }}>{{ $value }} </option> @endif @endforeach</select>'+
            '</div>'+
            '<div class="form-group col-md-3">'+

                '<select  id="account_counterpart'+x+'"  name="valorcontra[]" class="form-control  account_counterpart" required> <option value="">Seleccionar</option> @if (isset($accounts_inventory)) @foreach ($accounts_inventory as $var) <option value="{{ $var->id }}">{{ $var->description }}</option> @endforeach @endif</select>'+

                    '</div>'+

                    '<div class="form-group col-md-3">'+

                        '<input type="text" class="form-control" placeholder="monto de la contrapartida" value="'+montocontra+'" id="montosid'+x+'" name="montocontra[]" />'+
                        '</div>'+
                        '<div class="form-group col-md-3">'+

                        '<button class="remove_button btn btn-outline-danger" value='+x+'>Eliminar</button>'+

                        '</div>'+
                        '</div>';



    $('.procesarcontrapartida').show();
    //$(wrapper).append(fieldHTML+fieldca);
    $(wrapper).append(camposopcionales);
    $(camposelect).on('change',function(){

       var contrapartida_id = $(this).val();

       getSubcontrapartida(contrapartida_id,valor);

   });
   $('.add_button').prop( 'disabled', false );
   contadordiv++;
        }else{

            $('.add_button').prop( 'disabled', true );

        }


        x++;

        function getSubcontrapartida(contrapartida_id,valor){

       $.ajax({
           url:"{{ route('listcontrapartidanew') }}" + '/' + contrapartida_id,
           beforSend:()=>{
               alert('consultando datos');
           },
           success:(response)=>{

                var camposelect2 = "#account_counterpart"+valor;
               let subcontrapartida = $(camposelect2);
               let htmlOptions = `<option value='' >Seleccione..</option>`;

               if(response.length > 0){
                   response.forEach((item, index, object)=>{
                       let {id,description} = item;
                       htmlOptions += `<option value='${id}'>${description}</option>`

                   });
               }

               subcontrapartida.html('');
               subcontrapartida.html(htmlOptions);


           },
           error:(xhr)=>{
               alert('Presentamos inconvenientes al consultar los datos');
           }
       })
   }


    });

    $(wrapper).on('click', '.remove_button', function(e){
        e.preventDefault();
        valor = $(this).val();
        $("#dinamicosdiv"+valor).remove();
        $("#dinamicos"+valor).remove();

       contadordiv--;
       if(contadordiv == 0){
            $('.procesarcontrapartida').hide();
            $('.add_button').prop( 'disabled', false );

        }
    });




$('.procesarcontrapartida').click(function(e){
  e.preventDefault();
  $('.procesarcontrapartida').prop( 'disabled', true );
$.ajax({
    method: "POST",
    url: "{{ route('procesardeposito') }}",
    data: $('#pruebaform').serialize(),
         success:(response)=>{

            if(response.error == true){
                $('.procesarcontrapartida').prop( 'disabled', false );

            Swal.fire({
                    icon: 'info',
                    title: 'Exito!',
                    html: response.msg,


                    })

                    $("#"+idmovimiento).empty().append();
                    $('#MatchModal').modal('hide');

                    }else{
            $('.procesarcontrapartida').prop( 'disabled', false );
            Swal.fire({
                    icon: 'info',
                    title: 'Error..',
                    html: response.msg,
                    })
         }




         },
         error:(xhr)=>{
            $('.procesarcontrapartida').prop( 'disabled', false );
            Swal.fire({
                    icon: 'error',
                    title: 'Error...',
                    text: 'Error a Procesar!',
                    });
         }
     })







});



});
</script>

<?php

}

?>


<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
