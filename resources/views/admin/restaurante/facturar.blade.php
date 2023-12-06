
    <form method="POST" id="facturar">
        @csrf
        <input type="hidden" name="idfactura" id="idfactura" value="{{ $data }}">
        <div class="tab-pane fade show active table-responsive-lg" id="home" role="tabpanel" aria-labelledby="home-tab">

            <table class="table table-lg">
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
            <input type="hidden" name="montoculto" id="montoculto" value="{{number_format($totalconiva, 2)}}" />

    </div>
    <div class="card-footer">
        <div class="add">
        </div>

        <div class="form-row">
        <div class="col">
            <button type="button" class="btn btn-warning btn-sm metodo">Agregar Metodo de Pago</button>
        </div>
        <div class="col">
            <button type="button" class="btn btn-danger btn-sm eliminar">Eliminar Metodo de Pago</button>
        </div>
        <div class="col">
            <input type="submit" class="btn btn-primary btn-sm" value="Facturar" />
        </div>
      </div>
    </div>

</form>


    <script>
 $("#montoculto").mask('000000000000000.00', { reverse: true });
$(document).ready(function(){
    var numero = 0;
    $('.metodo').click(function(e){
        e.preventDefault();
        numero += 1;

        if(numero < 8){
        var valor = $("#montoculto").val();
            var url = "{{ route('metodos') }}/" + numero + "/" + valor;

            $.get(url, function(data) {
                $('.add').append('<div id="'+numero+'"></div>');
                $("#"+numero).append(data);
            });


            /*$.post(url,{valor:valor,numero:numero,"_token": "{{ csrf_token() }}"},function(data){

            });*/

        }else{
            numero -= 1;
        }

    });

    $('.eliminar').click(function(e){
        e.preventDefault();
        $(".campos"+numero).remove();

        if(numero < 1){
            numero = 0
        }else{
            numero -= 1;

        }


    });




    $("#facturar").validate({
          submitHandler: function (form) {
            $.ajax({
                type: "post",
                url: "{{ route('facturarpedido') }}",
                dataType: "json",
                data: $(form).serialize(),
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



