@extends('admin.layouts.dashboard')

@section('content')

   

<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
        <div class="col-md-6">
            <h2>Tipos de Pagos</h2>
        </div>
       
        @if (Auth::user()->role_id  == '1' || $agregarmiddleware  == '1' )
        <div class="col-md-6">
            <a href="{{ route('paymenttypes.create')}}" class="btn btn-primary btn-lg float-md-right" role="button" aria-pressed="true">Registrar un Tipo de Pago</a>
         
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
        <h6 class="m-0 font-weight-bold text-primary">Listado de Tipos de Pagos</h6>
    </div>
   
    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
            <tr>
                <th>Id</th>
                <th>Descripción</th>
                <th>Tipo</th>
                <th>Días de Crédito</th>
                <th>Pide Referencia</th>
                <th>Caja Chica</th>
                <th>Naturaleza</th>
                <th>Punto</th>
                <th>Status</th>
<<<<<<< HEAD
                <th>Opciones</th>
              
=======
                @if (Auth::user()->role_id  == '1' || $actualizarmiddleware == '1') 
                <th>Opciones</th>
                @endif
>>>>>>> fb39095d (todo el proyecto)
            </tr>
            </thead>
            
            <tbody>
                @if (empty($paymenttypes))
                @else
                    @foreach ($paymenttypes as $key => $var)
                    <tr>
                    <td>{{$var->id}}</td>
                    <td>{{$var->description}}</td>
                    <td>{{$var->type}}</td>
                    <td>{{$var->credit_days}}</td>
                    <td>{{$var->pide_ref}}</td>
                    <td>{{$var->small_box}}</td>
                    <td>{{$var->nature}}</td>
                    <td>{{$var->point}}</td>
                   
                   
<<<<<<< HEAD
                    @if (Auth::user()->role_id  == '1')
=======
>>>>>>> fb39095d (todo el proyecto)
                        @if($var->status == 1)
                            <td>Activo</td>
                        @else
                            <td>Inactivo</td>
                        @endif
<<<<<<< HEAD
=======
                        @if (Auth::user()->role_id  == '1' || $actualizarmiddleware == '1') 
>>>>>>> fb39095d (todo el proyecto)
                        <td>
                        <a href="{{route('paymenttypes.edit',$var->id) }}" title="Editar"><i class="fa fa-edit"></i></a>  
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