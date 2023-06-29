

    <table class="table table-light2 table-bordered dataTableclass" id="dataTable"  cellspacing="0">
                <thead>
                    <tr>
                        <th class="text-center">Fecha</th>
                        <th class="text-center">Referencia</th>
                        <th class="text-center">Banco</th>
                        <th class="text-center">Descripcion</th>
                        <th class="text-center">Moneda</th>
                        <th class="text-center">Debe</th>
                        <th class="text-center">Haber</th>
                        <th class="text-center">Accion</th>
                        <th class="text-center">Eliminar</th>
                    </tr>
                    </thead>

                    <tbody>

                        @if (!empty($movimientosmasivoss))

                            @foreach ($movimientosmasivoss as $var)


                            <tr id="{{$var->id_temp_movimientos}}">
                            <td>{{ date('d-m-Y', strtotime( $var->fecha ?? '')) }}</td>
                            <td class="text-center">{{$var->referencia_bancaria ?? ''}}</td>
                            <td>{{$var->banco ?? ''}}</td>
                            <td>{{$var->descripcion ?? ''}}</td>
                            <td>{{$var->moneda ?? ''}}</td>
                            <td>{{ $var->debe}}</td>
                            <td>{{ $var->haber}}</td>
                            <td>
                                @php
                                    if($var->conta == 'debe'){
                                        $plata = $var->debe;
                                    }else{
                                        $plata = $var->haber;
                                    }
                                @endphp





                                        @if($var->match == 1)

                                        <span class="badge badge-pill badge-success" data-toggle="modal" data-target="#MatchModal" name="matchvalue" data-id="{{$plata.'/'.$var->id_temp_movimientos.'/'.$var->fecha.'/'.$var->banco.'/match/'.$var->moneda.'/'.$var->conta}}">Match</span>

                                       @elseif($var->match == 0)

                                       @else

                                        <span class="badge badge-pill badge-success procesarfactura"  data-id="{{$var->amount_with_iva.'/'.$var->match.'/'.$plata.'/'.$var->idinvoice.'/'.$var->id_temp_movimientos.'/'.$var->fecha.'/'.$var->banco.'/'.$var->bcv.'/'.$var->conta}}">Match {{$var->match}}</span>

                                        @endif


                                <span class="badge badge-pill badge-warning" data-toggle="modal" data-target="#MatchModal" name="matchvalue" data-id="{{$var->debe.'/'.$var->id_temp_movimientos.'/'.$var->fecha.'/'.$var->banco.'/contra/'.$var->haber.'/'.$var->referencia_bancaria.'/'.$var->moneda.'/'.$var->descripcion}}">Contrapartida</span>
                                <span class="badge badge-pill badge-primary" data-toggle="modal" data-target="#MatchModal" name="matchvalue" data-id="{{$var->debe.'/'.$var->id_temp_movimientos.'/'.$var->fecha.'/'.$var->banco.'/transferencia/'.$var->haber.'/'.$var->referencia_bancaria.'/'.$var->moneda.'/'.$var->descripcion}}">Transferencia</span>
                                <span class="badge badge-pill badge-info" data-toggle="modal" data-target="#MatchModal" name="matchvalue" data-id="{{$var->debe.'/'.$var->id_temp_movimientos.'/'.$var->fecha.'/'.$var->banco.'/deposito/'.$var->haber.'/'.$var->referencia_bancaria.'/'.$var->moneda.'/'.$var->descripcion}}">Deposito</span>

                            </td>
                            <td><span class="badge badge-pill badge-danger"  name="matchvalueliminar" data-id="{{$var->id_temp_movimientos}}">Eliminar Movimiento</span>
                            </td>
                            </tr>
                            @endforeach

                    @endif
                </tbody>
            </table>

            <div class="modal modal-danger fade" id="MatchModal" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                    <div class="modal-content" id="modalfacturas">

                    </div>
                </div>
              </div>

            <script type="text/javascript">


                $(document).ready(function(){

                 /********MODAL CUANDO CONSIGUE MATCH**********/

                $('[name="matchvalue"]').click(function(e){
                    e.preventDefault();
                   var value = $(this).data('id');
                   var url = "{{ route('facturasmovimientos') }}";

                 $.post(url,{value: value,"_token": "{{ csrf_token() }}"},function(data){
                        $("#modalfacturas").empty().append(data);

                      });



                 });


    $('.procesarfactura').click(function(e){
      e.preventDefault();

    var valor = $(this).data('id').split('/');
    var montoiva = valor[0];
    var nrofactura = valor[1];
    var montomovimiento = valor[2];
    var id = valor[3];
    var idmovimiento = valor[4];
    var fechamovimiento = valor[5];
    var bancomovimiento = valor[6];
    var tasa = valor[7];
    var conta = valor[8];
    $.ajax({
        method: "POST",
        url: "{{ route('procesarfact') }}",
        data: {conta: conta,tasa: tasa,bancomovimiento: bancomovimiento,fechamovimiento: fechamovimiento,montoiva: montoiva, nrofactura: nrofactura,montomovimiento: montomovimiento,id: id,idmovimiento: idmovimiento, "_token": "{{ csrf_token() }}"},
             success:(response)=>{

                 if(response == true){
                    Swal.fire({
                        icon: 'success',
                        title: 'Exito!',
                        text: 'Factura '+nrofactura+' Procesada Exitosamente!',


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



                 $('[name="matchvalueliminar"]').click(function(e){
                    e.preventDefault();
                   var idmov = $(this).data('id');


                   $.ajax({
            type: "post",
            url: "{{ route('eliminarmovimiento') }}",
            dataType: "json",
            data: {idmov: idmov, "_method": "DELETE", "_token": "{{ csrf_token() }}"},
            success:function(response){
             if(response.error == true){
                Swal.fire({
                        icon: 'info',
                        title: 'Exito!',
                        html: response.msg,


                        })
                        $("#"+idmov).empty().append();

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



                 });



                $('#dataTable').DataTable({
                        "ordering": false,
                        "order": [],
                        'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
                    });

                });
                    </script>
