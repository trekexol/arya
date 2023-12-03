<?php

if($tipo == 'agregar'){
?>

<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Agregar Pedido</h5>
</div>
<div class="modal-body" align="center">
    <form method="POST" id="carrito">

        <div class="table-responsive-md">
<table class="table table-light2 table-bordered dataTablematch table-sm" id="dataTablematch" >
        <small><select class="form-control form-control-sm filter-input">
            <option value="todo">Todo.</option>

        @foreach ($segmentos as $segmentos)

            <option value="{{$segmentos->segments['description']}}">{{$segmentos->segments['description']}}</option>

        @endforeach
        </select>
        </small>

    <thead>
    <tr>
        <th>Producto</th>
        <th style="display : none;">Tipo</th>
        <th>Pedido</th>
        <th>Disponible</th>
        <th>Monto</th>

    </tr>
    </thead>
    <tbody>
        @foreach ($inventories as $var)
        @if($var->amount > 0)

        <tr>
            <input type="hidden" name="mesa" id="mesa" value="{{ $mesa }}">

            <td>
                <small class="add" data-nombre="{{ $var->id }}">{{ $var->description}}</small>
                @if(isset($var->photo_product))
                <!--arya/storage/app/public/img/-->
                <img class="rounded float-right add" data-nombre="{{ $var->id }}" style="width:100px;  height:100px;" src="{{asset('arya/storage/app/public/img/'.$company->login.'/productos/'.$var->photo_product)}}">

                @endif
            </td>

            <td style="display : none;">
                <small>{{$var->segments['description']}}
                </small>
            </td>

            <td>
                <b style="color:blue">
                <small class="eli" data-nombre="{{ $var->id }}" id="sma{{ $var->id }}">
                    0
                </small>
                </b>
                <input style="width : 25%;" class="form-control form-control-sm cantidad" name="cantidad[]" id="{{ $var->id }}" type="hidden" value="" />

            </td>


            <td style="width : 5%;">
                <small>
                {{ $var->amount ?? 0}}
                <input id="disponible{{ $var->id }}" type="hidden" value="{{ $var->amount ?? 0}}" />
                </small>
            </td>



            <td style="width : 5%;">
                <small>{{ $var->price ?? 0}}
                    <input  class="form-control form-control-sm precio" name="precio[]" id="precio" type="hidden" value="{{ $var->price ?? 0}}" />
                </small>
            </td>


            <input name="id[]" id="id" type="hidden" value="{{ $var->id }}" />
            @csrf
        </tr>

        @endif
    @endforeach



    </tbody>

</table>


        </div>
<input type="submit" class="btn btn-primary agregarpedido" value="Agregar" />
</form>
</div>

    <script>

$(document).ready(function(){

    var tabladata = $('.dataTablematch').DataTable({
        "dom": 'lrtip',
        "dom": 'rtip',
        "responsive": true,
        "aLengthMenu": [[10,15,20 -1], [10,15,20, "All"]],
        //"bPaginate": false,
        "bLengthChange": false,
        "bFilter": true,
        "bInfo": false,
        "bAutoWidth": false
        })

$('.filter-input').change(function(){

    var dato = $(this).val();

    if(dato == 'todo'){
        tabladata.column(1).search("").draw();
    }else{
        tabladata.column(1)
        .search($(this).val())
        .draw();
    }
});


    $("#carrito").validate({
      submitHandler: function (form) {
       var datos = tabladata.$('input').serialize();
       //tabladata.column(0).search("").draw();
        $.ajax({
            type: "post",
            url: "{{ route('carrito') }}",
            dataType: "json",
            data: datos,
            success: function (response) {
              if(response.error == true){
                Swal.fire({
                        icon: 'success',
                        title: 'Exito!',
                        text: response.msg,
                        })
                        setTimeout("location.reload()", 1800);

                      }else{
                        Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.msg,
                        })


                     }

                   }
                 });





            return false; // required to block normal submit since you used ajax
          }
    }); ///fin $("#registro").validate({


        tabladata.$('.add').click(function(e){
        e.preventDefault();
            var value = $(this).data('nombre');

            valorinput = $("#"+value).val();
            disponible = $("#disponible"+value).val();

            if(valorinput == disponible){
                Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: "Solo Cantidad Disponible "+disponible,
                        })
            }

            else if(valorinput > 0){
                valorinput = parseFloat(valorinput) + 1;
            }else{
                valorinput = 1;

            }
            $("#"+value).val(valorinput);
            $("#sma"+value).empty().append(valorinput);

    });


    tabladata.$('.eli').click(function(e){
        e.preventDefault();
            var value = $(this).data('nombre');

            valorinput = $("#"+value).val();
            disponible = $("#disponible"+value).val();

            if(valorinput == 0){
                Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: "No puede ser Menor a Cero (0)",
                        })
            }

            else {
                valorinput = parseFloat(valorinput) - 1;
            }
            $("#"+value).val(valorinput);
            $("#sma"+value).empty().append(valorinput);

    });

});



    </script>


<?php

}
elseif($tipo == 'editar'){

    ?>


<div class="modal-header">
    <ul class="nav nav-pills" id="myTab" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Pedido Actual</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Actualizar Pedido</a>
        </li>
</div>
<div class="modal-body" align="center">



      <div class="tab-content" id="myTabContent">

        <div class="tab-pane fade show active table-responsive-md" id="home" role="tabpanel" aria-labelledby="home-tab">



                <table class="table table-md">
                    <thead>
                        <tr>
                          <th scope="col">Producto</th>
                          <th colspan="3" scope="col">Cantidad</th>
                          <th scope="col">Precio</th>
                          <th scope="col">Total</th>
                        </tr>
                      </thead>

                      <tbody>
                        <input type="hidden" name="mesa" id="mesa" value="{{ $mesa.'/'.'editar' }}">

                        <?php $total = 0; ?>
                        @foreach ($quotations as $quotations)
                        <tr>
                            <td>{{ $quotations->nombreproducto }}</td>
                            <td>{{ $quotations->amount }}
                            </td>
                            <td><i style="color: blue;" class="fa fa-plus up" data-id="{{ $quotations->id.'/'.'ADD' }}"></i></td>
                            <td><i style="color: red;" class="fa fa-minus up" data-id="{{ $quotations->id.'/'.'ELI' }}"></i></td>
                            <td>{{ number_format($quotations->price, 2, ',', '.') }}</td>
                            <td>{{number_format($quotations->price * $quotations->amount, 2, ',', '.')}}</td>
                        </tr>
                        <?php $total += $quotations->price * $quotations->amount; ?>
                        @endforeach
                        <?php
                            $iva = $total * 16 / 100;
                            $totalconiva = $total + $iva;
                        ?>
                        <tr class="alert alert-success">
                            <td colspan="4" align="center">TOTAL CON iva (16%)</td>
                            <td>{{number_format($totalconiva, 2, ',', '.')}} Bs</td>
                            <td>{{number_format($totalconiva / $quotations->rate, 2, ',', '.')}} $</td>
                        </tr>
                      </tbody>
                </table>


        </div>



        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <form method="POST" id="upcarrito">

                <div class="table-responsive-lg">
        <table class="table table-light2 table-bordered dataTablematch" id="dataTablematch" >
                <small><select class="form-control form-control-sm filter-input">
        <option value="todo">Todo.</option>
    @foreach ($segmentos2 as $segmentos)
    @if($segmentos->amount > 0)
        <option value="{{$segmentos->segments['description']}}">{{$segmentos->segments['description']}}</option>
    @endif
    @endforeach
    </select>
    </small>

            <thead>
            <tr>
                <th>Producto</th>
                <th style="display : none;">Tipo</th>
                <th>Pedido</th>
                <th>Disponible</th>
                <th>Monto</th>

            </tr>
            </thead>
            <tbody>
                @foreach ($inven as $var)
                @if($var->amount > 0)

                <tr>
                    <input type="hidden" name="mesa" id="mesa" value="{{ $mesa }}">
                    <input type="hidden" name="idfac" id="idfac" value="{{ $quotations->id_quotation }}">

                    <td>
                        <small class="add" data-nombre="{{ $var->id }}">{{ $var->description}}</small>
                        @if(isset($var->photo_product))
                        <!--arya/storage/app/public/img/-->
                        <img class="rounded float-right add" data-nombre="{{ $var->id }}" style="width:100px;  height:100px;" src="{{asset('arya/storage/app/public/img/'.$company->login.'/productos/'.$var->photo_product)}}">

                        @endif
                    </td>

                    <td style="display : none;">
                        <small>{{$var->segments['description']}}
                        </small>
                    </td>

                    <td>
                        <b style="color:blue">
                        <small class="eli" data-nombre="{{ $var->id }}" id="sma{{ $var->id }}">
                            0
                        </small>
                        </b>
                        <input style="width : 25%;" class="form-control form-control-sm cantidad" name="cantidad[]" id="{{ $var->id }}" type="hidden" value="" />

                    </td>


                    <td style="width : 5%;">
                        <small>
                        {{ $var->amount ?? 0}}
                        <input id="disponible{{ $var->id }}" type="hidden" value="{{ $var->amount ?? 0}}" />
                        </small>
                    </td>



                    <td style="width : 5%;">
                        <small>{{ $var->price ?? 0}}
                            <input  class="form-control form-control-sm precio" name="precio[]" id="precio" type="hidden" value="{{ $var->price ?? 0}}" />
                        </small>
                    </td>


                    <input name="id[]" id="id" type="hidden" value="{{ $var->id }}" />
                    @csrf
                </tr>

                @endif
            @endforeach



            </tbody>

        </table>


                </div>
        <input type="submit" class="btn btn-primary agregarpedido" value="Agregar" />
        </form>



        </div>

        </div>



 </div>


 <script>

$(document).ready(function(){

        var tabladata = $('.dataTablematch').DataTable({
            "dom": 'lrtip',
            "dom": 'rtip',
            "responsive": true,
            "aLengthMenu": [[10,20,30 -1], [10,20,30, "All"]],
            //"bPaginate": false,
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            "bAutoWidth": false
            })

    $('.filter-input').change(function(){

    var dato = $(this).val();

    if(dato == 'todo'){
        tabladata.column(1).search("").draw();
    }else{
        tabladata.column(1)
        .search($(this).val())
        .draw();
    }
    });


        $("#upcarrito").validate({
          submitHandler: function (form) {
           var datos = tabladata.$('input').serialize();
           //tabladata.column(0).search("").draw();
            $.ajax({
                type: "post",
                url: "{{ route('upcarrito') }}",
                dataType: "json",
                data: datos,
                success: function (response) {
                  if(response.error == true){
                    Swal.fire({
                            icon: 'success',
                            title: 'Exito!',
                            text: response.msg,
                            })
                            setTimeout("location.reload()", 1800);

                          }else{
                            Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.msg,
                            })


                         }

                       }
                     });





                return false; // required to block normal submit since you used ajax
              }
        }); ///fin $("#registro").validate({






    $('.up').click(function(e){
      e.preventDefault();

      var value = $(this).data('id');

        $.ajax({
        method: "POST",
        url: "{{ route('upcarritonew') }}",
        data: {value: value, "_token": "{{ csrf_token() }}"},
             success:(response)=>{
                 if(response.error == true){
                    Swal.fire({
                        icon: 'success',
                        title: 'Exito!',
                        text: response.msg,
                        })

            var valor = $("#mesa").val();
            var url = "{{ route('pedidosmesas') }}";
            $.post(url,{value: valor,"_token": "{{ csrf_token() }}"},function(data){
                $("#modalfacturas").empty().append(data);
            });

                 }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Error...',
                        text: response.msg,
                        })
                 }
             },
             error:(xhr)=>{
                Swal.fire({
                        icon: 'error',
                        title: 'Error...',
                        text: response.msg,
                        });
             }
         })

    });

    tabladata.$('.add').click(function(e){
        e.preventDefault();

            var value = $(this).data('nombre');

            valorinput = $("#"+value).val();
            disponible = $("#disponible"+value).val();

            if(valorinput == disponible){
                Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: "Solo Cantidad Disponible "+disponible,
                        })
            }

            else if(valorinput > 0){
                valorinput = parseFloat(valorinput) + 1;
            }else{
                valorinput = 1;

            }
            $("#"+value).val(valorinput);
            $("#sma"+value).empty().append(valorinput);

    });


    tabladata.$('.eli').click(function(e){
        e.preventDefault();
            var value = $(this).data('nombre');

            valorinput = $("#"+value).val();
            disponible = $("#disponible"+value).val();

            if(valorinput == 0){
                Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: "No puede ser Menor a Cero (0)",
                        })
            }

            else {
                valorinput = parseFloat(valorinput) - 1;
            }
            $("#"+value).val(valorinput);
            $("#sma"+value).empty().append(valorinput);

    });

});



        </script>

<?php


}

?>


<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

