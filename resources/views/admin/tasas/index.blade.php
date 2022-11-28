@extends('admin.layouts.dashboard')

@section('content')

   

<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
        <div class="col-md-6">
            <h2>Registrar Tasa del Día</h2>
        </div>
       
        @if (Auth::user()->role_id  == '1' || $agregarmiddleware  == '1' )
        <div class="col-md-6">
            <a href="{{ route('tasas.create')}}" class="btn btn-primary btn-lg float-md-right" role="button" aria-pressed="true">Registrar Tasa Nueva</a>
         
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
    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
            <tr>
                <th>Fecha de Inicio</th>
                <th>Fecha de Final</th>
                <th>Monto</th>
               <th></th>
              
            </tr>
            </thead>
            
            <tbody>
                @if (empty($tasas))
                @else
                    @foreach ($tasas as $key => $tasa)
                    <tr>
                    <td>{{ $tasa->date_begin }}</td>
                    <td>{{ $tasa->date_end }}</td>
                    <td>{{ number_format($tasa->amount, 2, ',', '.')}}</td>
                   
                   <td></td>
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