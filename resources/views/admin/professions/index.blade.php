@extends('admin.layouts.dashboard')

@section('content')

   

<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
        <div class="col-md-6">
            <h2>Tipos de Empleados</h2>
        </div>
       
        @if (Auth::user()->role_id  == '1' || $agregarmiddleware  == '1' )
        <div class="col-md-6">
            <a href="{{ route('professions.create')}}" class="btn btn-primary btn-lg float-md-right" role="button" aria-pressed="true">Registrar Tipo de Empleado</a>
         
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
        <h6 class="m-0 font-weight-bold text-primary">Listado Tipos de Empleados</h6>
    </div>
   
    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
            <tr>
                <th>Id</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Status</th>
                @if (Auth::user()->role_id  == '1' || $actualizarmiddleware == '1') 
                <th>Opciones</th>
                @endif
            </tr>
            </thead>
            
            <tbody>
                @if (empty($professions))
                @else
                    @foreach ($professions as $key => $var)
                    <tr>
                    <td>{{$var->id}}</td>
                    <td>{!!$var->name!!}</td>
                    <td>{{$var->description}}</td>
                   
                   
                  
                        @if($var->status == 1)
                            <td>Activo</td>
                        @else
                            <td>Inactivo</td>
                        @endif
                        @if (Auth::user()->role_id  == '1' || $actualizarmiddleware == '1') 
                        <td>
                        <a href="{{route('professions.edit',$var->id) }}" title="Editar"><i class="fa fa-edit"></i></a>  
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
        "ordering": false,
        "order": [],
        'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
    });

    </script> 

@endsection