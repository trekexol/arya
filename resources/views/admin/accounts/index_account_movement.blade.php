@extends('admin.layouts.dashboard')

@section('content')


<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-12">
        <div class="col-sm-12 h4">
            Listado de Comprobantes Contables detallados de la cuenta: Nº {{ $account->code_one }}.{{ $account->code_two }}.{{ $account->code_three }}.{{ $account->code_four }}.{{ $account->code_five }} / {{ $account->description }}
        </div>
    </div>

    <div class="row py-lg-12">
        <div class="col-sm-4">
            <a href="{{ route('accounts') }}" class="btn btn-light2"><i class="fas fa-eye" ></i>
                &nbsp Volver al Plan de Cuentas
            </a>
        </div>

           Periodo: &nbsp
        
            <select class="form-control col-sm-1" name="period" id="period">
                @for ($i = 0; $i < count($periodselect); $i++)
                
                    @if ($period == $periodselect[$i])
                    <option selected value="{{ $periodselect[$i] }}">{{ $periodselect[$i] }}</option>
                    @else
                    <option value="{{ $periodselect[$i] }}">{{ $periodselect[$i] }}</option>
                    @endif

                @endfor

            </select>
     
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
            <input id="input_id" type="hidden" name="input_id" autocomplete="input_id" value="{{$account->id}}">
            <input id="input_coin" type="hidden" name="input_coin" autocomplete="input_coin" value="{{$coin}}">
            <thead>
            <tr>
                <th style="width: 11%;">Fecha</th>
                <th>Tipo de Movimiento</th>
                <th>Comprobante</th>
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
                    $saldos_USD = 0;
                    $cont0 = 0;
                    $cont = 0;

                        foreach ($detailvouchers_most as $row) {
 
                  
                            if ($cont0 <= 0){
                            $saldos += $balance_previus + $row->debe - $row->haber;    
                            } else{
                            $saldos += $row->debe - $row->haber;        
                            }

                            if ($cont0 <= 0){
                            $saldos_USD += $balance_previus/$account->rate + ($row->debe/$row->tasa) - $row->haber/$row->tasa;    
                            } else{
                            $saldos_USD += ($row->debe/$row->tasa) - $row->haber/$row->tasa;        
                            }

                            $cont0++;
                        
                            $saldo_most[] = array($cont0,$saldos);
                            $saldo_most_usd[] = array($cont0,$saldos_USD);
                            
                        
                        }
                        
                        rsort($saldo_most);
                        rsort($saldo_most_usd);

                    ?>               
                    @foreach ($detailvouchers as $var)
                     <?php
                     if (isset($var->reference)) {
                     $reference = 'Referencia: '.$var->reference.'.'; 
                     } else {
                     $reference = '';                        
                     }
                     ?>
                    <tr>
                    <td>{{ date_format(date_create($var->date ?? ''),"d-m-Y")}}</td>

                    
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
                       
                        <td>{{$var->description ?? ''}} fact({{ $var->quotations['number_invoice'] }})  / {{$var->accounts['description'] ?? ''}} {{$reference}}</td>
                    
                       @elseif(isset($var->quotations['number_delivery_note']))
                       
                        <td>{{$var->description ?? ''}} nota({{ $var->quotations['number_delivery_note'] }})  / {{$var->accounts['description'] ?? ''}} {{$reference}}</td>
                      
                       @endif
   
                    @elseif (isset($var->id_expense))
                        
                        <td>{{$var->description ?? ''}} Compra({{ $var->id_expense }}) / {{$var->accounts['description'] ?? ''}} {{$reference}}</td>
                   
                    @elseif (isset($var->id_anticipo))
                        <td>{{$var->description ?? ''}} {{ $var->id_anticipo ?? '' }} {{$reference}}</td>                   
                    @else
                        
                     <td>{{$var->description ?? ''}} {{$reference}}</td>

                    @endif
                   
                    @if(isset($var->accounts['coin']))
                    
                        <?php
                        $cont++;  

                        
                        for ($f=0; $f < $cont; $f++) { 

                            $saldo = $saldo_most[$f][1];
                            $saldo_USD = $saldo_most_usd[$f][1];

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
                        <td class="text-right font-weight-bold">{{number_format($saldo, 2, ',', '.')}}<br>{{ $var->accounts['coin'] }}{{number_format($saldo_USD, 2, ',', '.')}}</td>
                        @else
                        <td class="text-right font-weight-bold">{{number_format($saldo, 2, ',', '.')}}</td>
                        @endif
                        
                  
                  
                   @else
                        <?php
                        $cont++;  

                        
                        for ($f=0; $f < $cont; $f++) { 

                            $saldo = $saldo_most[$f][1];
                            $saldo_USD = $saldo_most_usd[$f][1];
                        }

                        ?>
                       

                        @if (isset($coin) && $coin == "bolivares")
                            <td class="text-right font-weight-bold">{{number_format($var->debe, 2, ',', '.')}}</td>
                            <td class="text-right font-weight-bold">{{number_format($var->haber, 2, ',', '.')}}</td>
                            <td class="text-right font-weight-bold">{{number_format($saldo, 2, ',', '.')}}</td>
                            
                        @elseif(isset($coin) && $coin == "dolares")
                            <td class="text-right font-weight-bold">${{number_format($var->debe/($var->tasa ?? 1), 2, ',', '.')}}</td>
                            <td class="text-right font-weight-bold">${{number_format($var->haber/($var->tasa ?? 1), 2, ',', '.')}}</td>
                            <td class="text-right font-weight-bold">${{number_format($saldo_USD ?? 1, 2, ',', '.')}}</td>
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
                        <td>{{number_format($balance_previus, 2, ',', '.')}}<br>${{number_format($balance_previus/($account->rate ?? 1), 2, ',', '.')}}</td>
                        @else
                        <td>{{number_format($balance_previus, 2, ',', '.')}}</td>
                        @endif
                    @else
                       <td>{{number_format($balance_previus, 2, ',', '.')}}</td>
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

    $("#period").on('change',function(){
        
        var period = $(this).val();
        var account = $('#input_id').val();
        var coin = $('#input_coin').val();

       window.location = "{{route('accounts.movements', '')}}"+"/"+account+"/"+coin+"/"+period;

    });

    </script> 
@endsection