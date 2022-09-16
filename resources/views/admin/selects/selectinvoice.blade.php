@extends('admin.layouts.dashboard')

@section('content')

<div class="container-fluid">
    <div class="row py-lg-2">
       
        <div class="col-md-6">
            <h2>Seleccione una Factura</h2>
        </div>
        
    
    </div>
</div>

  <!-- /.container-fluid -->
  {{-- VALIDACIONES-RESPUESTA--}}
  @include('admin.layouts.success')   {{-- SAVE --}}
  @include('admin.layouts.danger')    {{-- EDITAR --}}
  @include('admin.layouts.delete')    {{-- DELELTE --}}
  {{-- VALIDACIONES-RESPUESTA --}}
<!-- DataTales Example -->
<div class="card shadow mb-4">
   
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr> 
                        <th class="text-center"></th>
                        <th class="text-center" style="width:11%;">Fecha</th>
                        <th class="text-center">Nº</th>
                        <th class="text-center">Cliente</th>
                        <th class="text-center">Vendedor</th>
                        <th class="text-center">Tasa</th>
                        <th class="text-center">REF</th>
                        <th class="text-center">Monto</th>
                        <th class="text-center">Moneda</th>
                        
                    </tr>
                    </thead>
                    
                    <tbody>
                        @if (empty($quotations))
                        @else  
                            @foreach ($quotations as $quotation)
                            <?php 
                                $amount_bcv = 0;
                                $amount_bcv = $quotation->amount_with_iva / $quotation->bcv;
                            ?>
        
                                <tr>
                                    <td class="text-center font-weight-bold">
                                        <a href="{{ route($route ?? 'creditnotes.createcreditnote',[$quotation->id,'N','N',$quotation->bcv]) }}" title="Seleccionar Factura" ><i class="fa fa-check"></i></a>
                                    </td>
                                    <td class="text-center font-weight-bold">{{date_format(date_create($quotation->date_billing),"d-m-Y")}}</td>
                                    @if ($quotation->status == "X")
                                        <td class="text-center font-weight-bold">{{ $quotation->number_invoice }}
                                        </td>
                                    @else
                                        <td class="text-center font-weight-bold font-weight-bold text-dark">
                                            {{ $quotation->number_invoice }}
                                        </td>
                                    @endif
                                    <td class="text-center font-weight-bold">{{$quotation->clients['name'] ?? ''}}</td>
                                    <td class="text-center font-weight-bold">{{$quotation->vendors['name'] ?? ''}} {{$quotation->vendors['surname'] ?? ''}}</td>
                                    <td class="text-right font-weight-bold">{{number_format($quotation->bcv, 2, ',', '.')}}</td>
                                    <td class="text-right font-weight-bold">${{number_format($amount_bcv, 2, ',', '.')}}</td>
                                    <td class="text-right font-weight-bold">{{number_format($quotation->amount_with_iva, 2, ',', '.')}}</td>
                                    <td class="text-center font-weight-bold">{{$quotation->coin}}</td>
                                   
                                    
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