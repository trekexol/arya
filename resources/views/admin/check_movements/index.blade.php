@extends('admin.layouts.dashboard')

@section('content')


<div class="container-fluid">
    
    
    <div class="row py-lg-12">
        <div class="col-sm-12 offset-sm-12">
            <a href="{{ route('check_movements.comprobanteschk') }}" class="btn btn-info" title="Transferencia">Chekear Comprobantes</a>
        </div>
    </div>
</div>
<br>

<div class="row py-lg-2">
    <div class="col-sm-4 h5 ">
        Chequear Movimientos en Desbalance
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
                    <th class="text-center">N° Cabecera</th>
                    <th class="text-center">Debe</th>
                    <th class="text-center">Haber</th>
                </tr>
                </thead>
                
                <tbody>
                    @if (empty($details))
                    @else
                        @foreach ($details as $var)
                        <tr>
                            <td class="text-center"><a href="{{ route('detailvouchers.create',['bolivares',$var->id_header_voucher]) }}">{{$var->id_header_voucher ?? ''}}</a></td>
                            <td class="text-right font-weight-bold">{{number_format($var->debe, 2, ',', '.')}}</td>
                            <td class="text-right font-weight-bold">{{number_format($var->haber, 2, ',', '.')}}</td>
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
            'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "Todo"]]
        });

        
    </script> 
@endsection