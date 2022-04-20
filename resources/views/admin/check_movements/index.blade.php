@extends('admin.layouts.dashboard')

@section('content')


<!-- container-fluid -->
<div class="container-fluid">

    
</div>
<div class="row py-lg-2">

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
                    <th class="text-center">N°</th>
                    <th class="text-center">Fecha</th>
                    <th class="text-center">Descripción</th>
                    <th class="text-center">Debe</th>
                    <th class="text-center">Haber</th>
                </tr>
                </thead>
                
                <tbody>
                    @if (empty($detailvouchers))
                    @else
                        @foreach ($detailvouchers as $var)
                        <tr>
                        <td class="text-center">{{$var->id ?? ''}}</td>
                        <td class="text-center">{{$var->date ?? ''}}</td>
                        <td class="text-center">{{$var->description ?? ''}}</td>
                        
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
            'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "Todo"]]
        });

        
    </script> 
@endsection