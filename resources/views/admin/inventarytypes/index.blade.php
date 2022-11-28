@extends('admin.layouts.dashboard')

@section('content')



<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
        <div class="col-md-6">
            <h2>Tipos de Inventario</h2>
        </div>
<<<<<<< HEAD
        @if (Auth::user()->role_id  == '1' || Auth::user()->role_id  == '2' )
=======
        @if (Auth::user()->role_id  == '1' || $agregarmiddleware  == '1' )
>>>>>>> fb39095d (todo el proyecto)
        <div class="col-md-6">
            <a href="{{ route('inventarytypes.create')}}" class="btn btn-primary btn-lg float-md-right" role="button" aria-pressed="true">Registrar un Tipo de Inventario</a>
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
        <h6 class="m-0 font-weight-bold text-primary">Listado de Tipos de Inventario</h6>
    </div>

    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
            <tr>
                <th>Nro</th>
                <th>Descripción</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
                @if (empty($inventarytypes))
                @else
                    @foreach ($inventarytypes as $key => $var)
                    <tr>
                    <td>{{$var->id}}</td>
<<<<<<< HEAD
                    <td>{{$var->description}}</td>
                    @if (Auth::user()->role_id  == '1')
=======
                    <td>{{$var->description}}</td>         
>>>>>>> fb39095d (todo el proyecto)
                        @if($var->status == 1)
                            <td>Activo</td>
                        @else
                            <td>Inactivo</td>
                        @endif
<<<<<<< HEAD
                    @endif
=======
        
>>>>>>> fb39095d (todo el proyecto)
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