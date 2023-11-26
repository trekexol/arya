<?php

if($tipo == 'agregar'){
?>

<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Agregar Pedido</h5>
</div>
<div class="modal-body" align="center">
    <form method="POST" id="carrito">

        <div class="table-responsive-lg">
<table class="table table-light2 table-bordered dataTablematch" id="dataTablematch" >
    <small><input type="text"  class="form-control form-control-sm filter-input" placeholder="Buscar Producto" /></small>

    <thead>
    <tr>
        <th>Producto</th>
        <th>Cant. Inventario</th>
        <th>Monto</th>
        <th>---</th>
        <th>Cantidad</th>


    </tr>
    </thead>
    <tbody>
        @foreach ($inventories as $var)
        @if($var->amount > 0)

        <tr>
            <input type="hidden" name="mesa" id="mesa" value="{{ $mesa }}">



            <td><small>{{ $var->description}}</small></td>
            <td><small>{{ $var->amount ?? 0}}</small></td>
            <td><small><input  class="form-control form-control-sm precio" name="precio[]" id="precio" type="number" value="{{ $var->price ?? 0}}" /></small></td>
            <td>
                @if(isset($var->photo_product))
                <!--arya/storage/app/public/img/-->
                <img style="width:60px; max-width:60px; height:80px; max-height:80px" src="{{asset('arya/storage/app/public/img/'.$company->login.'/productos/'.$var->photo_product)}}">
                <div class="file-footer-buttons">
                <button type="button" class="btnimg btn-sm" title="Ver detalles" data-toggle="modal" data-target="#imagenModal" onclick="loadimg('{{asset('arya/storage/app/public/img/'.$company->login.'/productos/'.$var->photo_product)}}')"><i class="fas fa-search-plus"></i></button>     </div>
                @endif

            </td>
            <td>
                <small><input class="form-control form-control-sm cantidad" name="cantidad[]" id="cantidad" type="number" /></small>
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
        "aLengthMenu": [[10,20,30 -1], [10,20,30, "All"]],
        //"bPaginate": false,
        "bLengthChange": false,
        "bFilter": true,
        "bInfo": false,
        "bAutoWidth": false
        })

$('.filter-input').keyup(function(){

tabladata.column(0)
.search($(this).val())
.draw();

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
<div class="modal-body" >



      <div class="tab-content" id="myTabContent">

        <div class="tab-pane fade show active table-responsive-md" id="home" role="tabpanel" aria-labelledby="home-tab">



                <table class="table table-md">
                    <thead>
                        <tr>
                          <th scope="col">Producto</th>
                          <th scope="col">Cantida</th>
                          <th scope="col">Precio</th>
                          <th scope="col">Total</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php $total = 0; ?>
                        @foreach ($quotations as $quotations)
                        <tr>
                            <td>{{ $quotations->nombreproducto }}</td>
                            <td>{{ $quotations->amount }}</td>
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
                            <td colspan="2" align="center">TOTAL CON iva (16%)</td>
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
            <small><input type="text"  class="form-control form-control-sm filter-input" placeholder="Buscar Producto" /></small>

            <thead>
            <tr>
                <th>Producto</th>
                <th>Cant. Inventario</th>
                <th>Monto</th>
                <th>---</th>
                <th>Cantidad</th>


            </tr>
            </thead>
            <tbody>
                @foreach ($inven as $var)
                @if($var->amount > 0)

                <tr>
                    <input type="hidden" name="mesa" id="mesa" value="{{ $mesa }}">
                    <input type="hidden" name="idfac" id="idfac" value="{{ $quotations->id_quotation }}">


                    <td><small>{{ $var->description}}</small></td>
                    <td><small>{{ $var->amount ?? 0}}</small></td>
                    <td><small><input  class="form-control form-control-sm precio" name="precio[]" id="precio" type="number" value="{{ $var->price ?? 0}}" /></small></td>
                    <td>
                        @if(isset($var->photo_product))
                        <!--arya/storage/app/public/img/-->
                        <img style="width:60px; max-width:60px; height:80px; max-height:80px" src="{{asset('arya/storage/app/public/img/'.$company->login.'/productos/'.$var->photo_product)}}">
                        <div class="file-footer-buttons">
                        <button type="button" class="btnimg btn-sm" title="Ver detalles" data-toggle="modal" data-target="#imagenModal" onclick="loadimg('{{asset('arya/storage/app/public/img/'.$company->login.'/productos/'.$var->photo_product)}}')"><i class="fas fa-search-plus"></i></button>     </div>
                        @endif

                    </td>
                    <td>
                        <small><input class="form-control form-control-sm cantidad" name="cantidad[]" id="cantidad" type="number" /></small>
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

    $('.filter-input').keyup(function(){

    tabladata.column(0)
    .search($(this).val())
    .draw();

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

    });



        </script>

<?php


}

?>


<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

