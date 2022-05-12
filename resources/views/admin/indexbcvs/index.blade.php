@extends('admin.layouts.dashboard')

@section('content')

   

<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
        <div class="col-md-6">
            <h2>Indices BCV</h2>
        </div>   
       
    </div>

  </div>

  {{-- VALIDACIONES-RESPUESTA--}}
@include('admin.layouts.success')   {{-- SAVE --}}
@include('admin.layouts.danger')    {{-- EDITAR --}}
@include('admin.layouts.delete')    {{-- DELELTE --}}
{{-- VALIDACIONES-RESPUESTA --}}

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Listado de Indices BCV</h6>
    </div>
   
    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
            <tr>
                <th>Id</th>
                <th>Fecha Emisión</th>
                <th>Periodo</th>
                <th>Mes</th>
                <th>Tasa Promedio A/P</th>
                <th>Tasa Activa</th>
            
              
            </tr>
            </thead>
            
            <tbody>
                @if (empty($indexbcvs))
                @else
                    @foreach ($indexbcvs as $key => $var)
                    <tr>
                    <td>{{$var->id}}</td>
                    <td>{{$var->date}}</td>
                    <td>{{$var->period}}</td>
                    <td>{{$var->month}}</td>
                    <td>{{$var->rate_average_a_p}}</td>
                    <td>{{$var->rate_active}}</td>
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