@extends('admin.layouts.dashboard')

@section('content')


  <!-- /.container-fluid -->
  {{-- VALIDACIONES-RESPUESTA--}}
  @include('admin.layouts.success')   {{-- SAVE --}}
  @include('admin.layouts.danger')    {{-- EDITAR --}}
  @include('admin.layouts.delete')    {{-- DELELTE --}}
  {{-- VALIDACIONES-RESPUESTA --}}
<!-- DataTales Example -->
<div class="row justify-content-left">
    <div class="col-md-2">
    </div>
    <div class="col-md-4">
        <a href="{{ route('bankmovements') }}" class="btn btn-info" title="Transferencia">Bancos</a>
    </div>
</div>
<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-center font-weight-bold h3">Listado de Movimientos de Caja y Bancos</div>

                <div class="card-body">
                        <div class="table-responsive">
                        <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="text-center">Fecha</th>
                                    <th class="text-center">Referencia</th>
                                    <th class="text-center">Nº</th>
                                    <th class="text-center">Cuenta</th>
                                    <th class="text-center">Descripción</th>
                                    <th class="text-center">Debe</th>
                                    <th class="text-center">Haber</th>
                                    <th class="text-center">Debe USD</th>
                                    <th class="text-center">Haber USD</th>
                                    <th class="text-center"></th>
                                </tr>
                                </thead>
                                
                                <tbody>
                                    @if (empty($detailvouchers))
                                    @else
                                        @foreach ($detailvouchers as $var)
                                        <tr>
                                        <td>{{$var->header_date ?? ''}}</td>
                                        <td class="text-center">{{$var->id_header_voucher ?? ''}}</td>
                                        <td>{{$var->account_code_one ?? ''}}.{{$var->account_code_two ?? ''}}.{{$var->account_code_three ?? ''}}.{{$var->account_code_four ?? ''}}</td>
                                        <td>{{$var->account_description ?? ''}}</td>
                                        <td>{{$var->header_description ?? ''}}</td>
                                       
                                        <td>{{ number_format($var->debe, 2, ',', '.')}}</td>
                                        <td>{{ number_format($var->haber, 2, ',', '.')}}</td>

                                        <td>{{ number_format($var->debe / $var->tasa, 2, ',', '.')}}</td>
                                        <td>{{ number_format($var->haber / $var->tasa, 2, ',', '.')}}</td>
                                      
                                        <td>
                                    
                                            <a href="{{ route('bankmovements.delete',$var->id_header_voucher ?? null) }}" class="delete" title="Eliminar"><i class="fa fa-trash text-danger"></i></a>  
                                        </td>  
                                        
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        </div>
    </div>
</div>
</div>
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