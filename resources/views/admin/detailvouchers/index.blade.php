@extends('admin.layouts.dashboard')

@section('content')

   

<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
        <div class="col-md-6">
            <h2>Comprobante Detalle</h2>
        </div>
       
        @if (Auth::user()->role_id  == '1' || Auth::user()->role_id  == '2' )
        <div class="col-md-6">
            <a href="{{ route('detailvouchers.create')}}" class="btn btn-primary btn-lg float-md-right" role="button" aria-pressed="true">Registrar un Comprobante Detalle</a>
         
        </div>
        @endif
       
            
       
    </div>

  </div>

  {{-- VALIDACIONES-RESPUESTA--}}
@include('admin.layouts.success')   {{-- SAVE --}}
@include('admin.layouts.danger')    {{-- EDITAR --}}
@include('admin.layouts.delete')    {{-- DELELTE --}}
{{-- VALIDACIONES-RESPUESTA --}}

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Listado de Comprobanes Detalle</h6>
    </div>
   
    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
            <tr>
                <th>Id</th>
                <th>Codigo</th>
                <th>Periodo</th>
                <th>Comprobante</th>
                <th>Debe</th>
                <th>Haber</th>
                <th>Referencia</th>
                <th>Status</th>
                <th>Opciones</th>
              
            </tr>
            </thead>
            
            <tbody>
                @if (empty($detailvouchers))
                @else
                    @foreach ($detailvouchers as $key => $var)
                    <tr>
                    <td>{{$var->id}}</td>
                    <td>{{$var->code_one}}.{{$var->code_two}}.{{$var->code_three}}.{{$var->code_four}}</td>
                    <td>{{$var->period}}</td>
                    <td>{{$var->id_header_voucher}}</td>

                    <td>{{$var->debe}}</td>
                    <td>{{$var->haber}}</td>

                    <td>{{$var->ref}}</td>
                    
                  
                    @if (Auth::user()->role_id  == '1')
                        @if($var->status == 1)
                            <td>Activo</td>
                        @else
                            <td>Inactivo</td>
                        @endif
                        <td>
                        <a href="{{route('detailvouchers.edit',$var->id) }}" title="Editar"><i class="fa fa-edit"></i></a>  
                        </td>
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
        "order": [],
        'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
    });
    </script> 
@endsection