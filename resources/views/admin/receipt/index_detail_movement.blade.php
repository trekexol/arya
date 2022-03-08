@extends('admin.layouts.dashboard')

@section('content')


<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
        <div class="col-sm-6 h5">
            Listado de Comprobantes Contables detallado
        </div>
        <div class="col-sm-3">
            <a href="{{ route('accounts') }}" class="btn btn-light"><i class="fas fa-eye" ></i>
                &nbsp Plan de Cuentas
            </a>
        </div>
        <div class="col-sm-3">
        @if ((isset($return)) && ($return == 'payments'))
            <a href="{{ route('payments') }}" class="btn btn-light"><i class="fas fa-undo" ></i>
                &nbsp Volver
            </a>
        @else
            <a href="{{ route('quotations.createfacturado',[$quotation->id,$coin]) }}" class="btn btn-light"><i class="fas fa-undo" ></i>
                &nbsp Volver a la Factura
            </a>
        @endif
        
            
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
                <th class="text-center font-weight-bold" width="12%">Fecha</th>
                <th class="text-center font-weight-bold">Cuenta</th>
                <th class="text-center font-weight-bold">Referencia</th>
                <th class="text-center font-weight-bold">Factura</th>
                <th class="text-center font-weight-bold">Descripción</th>
                <th class="text-center font-weight-bold">Debe</th>
                <th class="text-center font-weight-bold">Haber</th>
               
               
              
            </tr>
            </thead>
            
            <tbody>
                @if (isset($multipayments_detail))
                    @foreach ($multipayments_detail as $key => $var)
                    <tr>
                    <td class="text-center font-weight-bold">{{$var->headers['date']}}</td>
                    <td class="text-center font-weight-bold">{{$var->accounts['code_one']}}.{{$var->accounts['code_two']}}.{{$var->accounts['code_three']}}.{{$var->accounts['code_four']}}.{{$var->accounts['code_five']}}</td>
                    <td class="text-center font-weight-bold">{{$var->id_header_voucher}}</td>
                    <td class="text-center font-weight-bold">{{$var->id_invoice}}</td>
                    <td class="font-weight-bold">{{$var->headers['description']}} fact(@foreach ($invoices as $invoice) {{$invoice->id_quotation}}, @endforeach) / {{$var->accounts['description']}}</td>

                    @if ($coin == 'bolivares')
                        <td class="text-right font-weight-bold">{{number_format($var->debe, 2, ',', '.')}}</td>
                        <td class="text-right font-weight-bold">{{number_format($var->haber, 2, ',', '.')}}</td>
                    @else
                        @if(($var->debe != 0) && ($var->tasa))
                            <td class="text-right font-weight-bold">{{number_format($var->debe  / $var->tasa, 2, ',', '.')}}</td>
                        @else
                            <td class="text-right font-weight-bold">{{number_format($var->debe, 2, ',', '.')}}</td>
                        @endif
                        @if($var->haber != 0 && ($var->tasa))
                            <td class="text-right font-weight-bold">{{number_format($var->haber / $var->tasa, 2, ',', '.')}}</td>
                        @else
                            <td class="text-right font-weight-bold">{{number_format($var->haber, 2, ',', '.')}}</td>
                        @endif
                    @endif
                   
                 
                    </tr>
                    @endforeach
                @endif
                @if (empty($detailvouchers))
                @else
                    @foreach ($detailvouchers as $key => $var)
                    <tr>
                    <td class="text-center font-weight-bold">{{$var->headers['date']}}</td>
                    <td class="text-center font-weight-bold">{{$var->accounts['code_one']}}.{{$var->accounts['code_two']}}.{{$var->accounts['code_three']}}.{{$var->accounts['code_four']}}.{{$var->accounts['code_five']}}</td>
                    <td class="text-center font-weight-bold">{{$var->id_header_voucher}}</td>
                    <td class="text-center font-weight-bold">{{$var->quotations['number_invoice'] ?? $var->id_invoice}}</td>
                    <td class="font-weight-bold">{{$var->headers['description']}} fact({{ $var->id_invoice }}) / {{$var->accounts['description']}}</td>

                    @if ($coin == 'bolivares')
                        <td class="text-right font-weight-bold">{{number_format($var->debe, 2, ',', '.')}}</td>
                        <td class="text-right font-weight-bold">{{number_format($var->haber, 2, ',', '.')}}</td>
                    @else
                        @if(($var->debe != 0) && ($var->tasa))
                            <td class="text-right font-weight-bold">{{number_format($var->debe  / $var->tasa, 2, ',', '.')}}</td>
                        @else
                            <td class="text-right font-weight-bold">{{number_format($var->debe, 2, ',', '.')}}</td>
                        @endif
                        @if($var->haber != 0 && ($var->tasa))
                            <td class="text-right font-weight-bold">{{number_format($var->haber / $var->tasa, 2, ',', '.')}}</td>
                        @else
                            <td class="text-right font-weight-bold">{{number_format($var->haber, 2, ',', '.')}}</td>
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
        $('#dataTable').dataTable( {
        "ordering": false,
        "order": [],
            'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
        } );
        
        
    </script>
@endsection