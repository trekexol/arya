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
               
               
              
            </tr>
            </thead>
            
            <tbody>
                @if (empty($detailvouchers))
                @else
                    @foreach ($detailvouchers as $var)
                    <tr>
                    <td>{{$var->headers['date'] ?? ''}}</td>

                    
                    @if(isset($var->id_invoice))
                        <td>Factura</td>
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
                        
                        <td>{{$var->headers['description'] ?? ''}} fact({{ $var->id_invoice }}) / {{$var->accounts['description'] ?? ''}}</td>
                    
                    @elseif (isset($var->id_expense))
                        
                        <td>{{$var->headers['description'] ?? ''}} Compra({{ $var->id_expense }}) / {{$var->accounts['description'] ?? ''}}</td>
                    @else
                        
                        <td>{{$var->headers['description'] ?? ''}}</td>
                    @endif
                   
                    @if(isset($var->accounts['coin']))
                        @if(($var->debe != 0) && ($var->tasa))
                            <td class="text-right font-weight-bold">{{number_format($var->debe, 2, ',', '.')}}<br>{{number_format($var->debe/$var->tasa, 2, ',', '.')}}{{ $var->accounts['coin'] }}</td>
                        @else
                            <td class="text-right font-weight-bold">{{number_format($var->debe, 2, ',', '.')}}</td>
                        @endif
                        @if($var->haber != 0 && ($var->tasa))
                            <td class="text-right font-weight-bold">{{number_format($var->haber, 2, ',', '.')}}<br>{{number_format($var->haber/$var->tasa, 2, ',', '.')}}{{ $var->accounts['coin'] }}</td>
                        @else
                            <td class="text-right font-weight-bold">{{number_format($var->haber, 2, ',', '.')}}</td>
                        @endif
                    @else
                        @if (isset($coin) && $coin == "bolivares")
                            <td class="text-right font-weight-bold">{{number_format($var->debe, 2, ',', '.')}}</td>
                            <td class="text-right font-weight-bold">{{number_format($var->haber, 2, ',', '.')}}</td>
                        @elseif(isset($coin) && $coin == "dolares")
                            <td class="text-right font-weight-bold">{{number_format($var->debe/($var->tasa ?? 1), 2, ',', '.')}}</td>
                            <td class="text-right font-weight-bold">{{number_format($var->haber/($var->tasa ?? 1), 2, ',', '.')}}</td>
                        @endif
                        
                    @endif
                    
                    </tr>
                    @endforeach
                @endif
            </tbody>
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