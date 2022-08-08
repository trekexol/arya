@extends('admin.layouts.dashboard')

@section('content')


<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
        <div class="col-sm-7 h4">
            Listado de Comprobantes Contables detallados <br>de {{ $type }} {{ $var->id ?? '' }}
        </div>
        <div class="col-sm-3">
            <a href="{{ route('accounts') }}" class="btn btn-light2"><i class="fas fa-eye" ></i>
                Plan de Cuentas
            </a>
        </div>
        <div class="col-sm-2">
            <a href="{{ route('accounts.movements',$id_account) }}" class="btn btn-light2"><i class="fas fa-undo" ></i>
                Volver
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
                <th>Nº</th>
                <th>Cuenta</th>
                <th>Tipo de Movimiento</th>
                <th>Comprobante</th>
                <th>Descripción</th>
                <th>Referencia</th>
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
                    
                    <td>{{$var->accounts['code_one'] ?? ''}}.{{$var->accounts['code_two'] ?? ''}}.{{$var->accounts['code_three'] ?? ''}}.{{$var->accounts['code_four'] ?? ''}}.{{$var->accounts['code_five'] ?? ''}}</td>
                    <td>{{$var->accounts['description'] ?? ''}}</td>
                    
                    @if(isset($var->id_invoice))
                        <td>Factura</td>
                        <td>
                        {{ $var->id_invoice }}
                        </td>
                    @elseif(isset($var->id_expense))
                        <td>Gasto o Compra</td>
                        <td>
                        {{ $var->id_expense }}
                        </td>
                    @elseif(isset($var->id_header_voucher)) 
                        <td>Otro</td>
                        <td class="text-center"><a href="{{ route('detailvouchers.create',['bolivares',$var->id_header_voucher ?? '']) }}" title="Ver comprobante contable">{{ $var->id_header_voucher ?? '' }}</a></td>

                    @endif

                   
                    @if (isset($var->id_invoice))
                        
                        <td>{{$var->headers['description'] ?? ''}} fact({{ $var->id_invoice }}) / {{$var->accounts['description'] ?? ''}}</td>
                    
                    @elseif (isset($var->id_expense))
                        
                        <td>{{$var->headers['description'] ?? ''}} Compra({{ $var->id_expense }}) / {{$var->accounts['description'] ?? ''}}</td>
                    @else
                        
                        <td>{{$var->headers['description'] ?? ''}}</td>
                    @endif

                         <td>
                        {{ $reference  }}
                        </td>
                                
                   
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
                        <td class="text-right font-weight-bold">{{number_format($var->debe, 2, ',', '.')}}</td>
                        <td class="text-right font-weight-bold">{{number_format($var->haber, 2, ',', '.')}}</td>
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