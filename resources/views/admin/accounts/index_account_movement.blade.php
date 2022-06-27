@extends('admin.layouts.dashboard')

@section('content')


<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
        <div class="col-sm-8 h4">
            Listado de Comprobantes Contables detallados de la <br>cuenta: Nº {{ $account->code_one }}.{{ $account->code_two }}.{{ $account->code_three }}.{{ $account->code_four }}.{{ $account->code_five }} / {{ $account->description }}
        </div>
        <div class="col-sm-4">
            <a href="{{ route('accounts') }}" class="btn btn-light2"><i class="fas fa-eye" ></i>
                &nbsp Plan de Cuentas
            </a>
        </div>
    </div>
    <!-- Page Heading -->
  </div>
  {{-- VALIDACIONES-RESPUESTA--}}
@include('admin.layouts.success')   {{-- SAVE --}}
@include('admin.layouts.danger')    {{-- EDITAR --}}
@include('admin.layouts.delete')    {{-- DELELTE --}}
{{-- VALIDACIONES-RESPUESTA --}}
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
            <tr>
                <th>Fecha</th>
                <th>Tipo de Movimiento</th>
                
                <th>Referencia</th>
              
                <th>Descripción</th>
                <th>Debe</th>
                <th>Haber</th>
                <th>Saldo</th>
               
               
              
            </tr>
            </thead>
            
            <tbody>
                @if (empty($detailvouchers))
                @else
                    <?php 
                    $saldos = 0;
                    $cont0 = 0;
                    $cont = 0;

                        foreach ($detailvouchers_most as $row) {
 
                  
                            if ($cont0 <= 0){
                            $saldos += $account->balance_previus + $row->debe - $row->haber;    
                            } else{
                            $saldos += $row->debe - $row->haber;        
                            }

                            $cont0++;
                        
                            $saldo_most[] = array($cont0,$saldos);
                            
                        
                        }
                        
                        rsort($saldo_most);

                    ?>               
                    @foreach ($detailvouchers as $var)

                    <tr>
                    <td>{{$var->date ?? ''}}</td>

                    
                    @if(isset($var->id_invoice))
                        @if (isset($var->quotations['number_invoice']))
                            <td>Factura</td>
                        @elseif(isset($var->quotations['number_delivery_note']))
                            <td>Nota de Entrega</td>
                        @endif
                        
                        <td>
                        <a href="{{ route('accounts.header_movements',[$var->id_header_voucher,'header_voucher',$account->id]) }}" title="Crear">{{ $var->id_header_voucher }}</a>
                        </td>
                    @elseif(isset($var->id_expense))
                        <td>Gasto o Compra</td>
                        <td>
                        <a href="{{ route('accounts.header_movements',[$var->id_header_voucher,'header_voucher',$account->id]) }}" title="Crear">{{$var->id_header_voucher }}</a>
                        </td>
                    @elseif(isset($var->id_header_voucher)) 
                        <td>Otro</td>
                        <td>
                        <a href="{{ route('accounts.header_movements',[$var->id_header_voucher,'header_voucher',$account->id]) }}" title="Crear">{{ $var->id_header_voucher }}</a>
                        </td>
                    @endif
                    
                                   
                   
                    @if (isset($var->id_invoice))

                       @if (isset($var->quotations['number_invoice']))
                       
                        <td>{{$var->description ?? ''}} fact({{ $var->quotations['number_invoice'] }})  / {{$var->accounts['description'] ?? ''}}</td>
                    
                       @elseif(isset($var->quotations['number_delivery_note']))
                       
                        <td>{{$var->description ?? ''}} nota({{ $var->quotations['number_delivery_note'] }})  / {{$var->accounts['description'] ?? ''}}</td>
                      
                       @endif
                        
                        
                    @elseif (isset($var->id_expense))
                        
                        <td>{{$var->description ?? ''}} Compra({{ $var->id_expense }}) / {{$var->accounts['description'] ?? ''}}</td>
                   
                    @elseif (isset($var->id_anticipo))
                        <td>{{$var->description ?? ''}} {{ $var->id_anticipo ?? '' }}</td>                   
                    @else
                        
                     <td>{{$var->description ?? ''}}</td>

                    @endif
                   
                    @if(isset($var->accounts['coin']))
                    
                        <?php
                        $cont++;  

                        
                        for ($f=0; $f < $cont; $f++) { 

                            $saldo = $saldo_most[$f][1];

                        }

                        ?>
                    
                        @if(($var->debe != 0) && ($var->tasa))
                            <td class="text-right font-weight-bold">{{number_format($var->debe, 2, ',', '.')}}<br>{{ $var->accounts['coin'] }}{{number_format($var->debe/$var->tasa, 2, ',', '.')}}</td>
                        @else
                            <td class="text-right font-weight-bold">{{number_format($var->debe, 2, ',', '.')}}</td>
                        @endif
                        @if($var->haber != 0 && ($var->tasa))
                            <td class="text-right font-weight-bold">{{number_format($var->haber, 2, ',', '.')}}<br>{{ $var->accounts['coin'] }}{{number_format($var->haber/$var->tasa, 2, ',', '.')}}</td>
                        @else
                            <td class="text-right font-weight-bold">{{number_format($var->haber, 2, ',', '.')}}</td>
                        @endif

                        @if(($var->tasa))
                        <td class="text-right font-weight-bold">{{number_format($saldo, 2, ',', '.')}}<br>{{ $var->accounts['coin'] }}{{number_format($saldo/$var->tasa, 2, ',', '.')}}</td>
                        @else
                        <td class="text-right font-weight-bold">{{number_format($saldo, 2, ',', '.')}}</td>
                        @endif
                        
                  
                  
                   @else
                        <?php
                        $cont++;  

                        
                        for ($f=0; $f < $cont; $f++) { 

                            $saldo = $saldo_most[$f][1];

                        }

                        ?>
                       

                        @if (isset($coin) && $coin == "bolivares")
                            <td class="text-right font-weight-bold">{{number_format($var->debe, 2, ',', '.')}}</td>
                            <td class="text-right font-weight-bold">{{number_format($var->haber, 2, ',', '.')}}</td>
                            <td class="text-right font-weight-bold">{{number_format($saldo, 2, ',', '.')}}</td>
                            
                        @elseif(isset($coin) && $coin == "dolares")
                            <td class="text-right font-weight-bold">${{number_format($var->debe/($var->tasa ?? 1), 2, ',', '.')}}</td>
                            <td class="text-right font-weight-bold">${{number_format($var->haber/($var->tasa ?? 1), 2, ',', '.')}}</td>
                            <td class="text-right font-weight-bold">${{number_format($saldo/($var->tasa ?? 1), 2, ',', '.')}}</td>
                        @endif

                    @endif
                    
                    </tr>
                    @endforeach
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>Saldo Anterior</td>
                    <td></td>
                    <td></td>
                    @if(isset($var->accounts['coin']))
                        @if(($var->tasa)) 
                        <td>{{number_format($account->balance_previus, 2, ',', '.')}}<br>${{number_format($account->balance_previus/($var->tasa ?? 1), 2, ',', '.')}}</td>
                        @else
                        <td>{{number_format($account->balance_previus, 2, ',', '.')}}</td>
                        @endif
                    @else
                       <td>{{number_format($account->balance_previus, 2, ',', '.')}}</td>
                    @endif
                </tr>
            </tfoot>
        </table>

        </div>
    </div>

</div>

@endsection
@section('javascript')
    <script>
    $('#dataTable').DataTable({
        "ordering": false,
        "order": [],
        'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
    });
    </script> 
@endsection