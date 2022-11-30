

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
                    </tr>
                    </thead>
                    
                    <tbody>
                        @if (!empty($movimientosmasivos))
                     
                            @foreach ($movimientosmasivos as $var)
                            <tr>
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
                            
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>


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
                
                 
                
                $('#dataTable').DataTable({
                        "ordering": false,
                        "order": [],
                        'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
                    });
                
                });
                    </script> 