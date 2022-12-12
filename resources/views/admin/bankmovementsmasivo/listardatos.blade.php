

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
                        @if (!empty($movimientosmasivos))

                            @foreach ($movimientosmasivos as $var)
                            <tr id="{{$var->id_temp_movimientos}}">
                            <td>{{ date('d-m-Y', strtotime( $var->fecha ?? '')) }}</td>
                            <td class="text-center">{{$var->referencia_bancaria ?? ''}}</td>
                            <td>{{$var->banco ?? ''}}</td>
                            <td>{{$var->descripcion ?? ''}}</td>
                            <td>{{$var->moneda ?? ''}}</td>
                            <td>{{ $var->debe}}</td>
                            <td>{{ $var->haber}}</td>
                            <td>
                               @if (!empty($quotations))
                                    @foreach($quotations as $quotation)
                                        @if($var->debe == $quotation->amount_with_iva AND $var->moneda == $quotation->coin)

                                        <span class="badge badge-pill badge-success" data-toggle="modal" data-target="#MatchModal" name="matchvalue" data-id="{{$var->debe.'/'.$var->id_temp_movimientos.'/'.$var->fecha.'/'.$var->banco.'/match'.$var->moneda}}">Match</span>

                                        @endif
                                          @endforeach
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
