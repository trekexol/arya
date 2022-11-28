@extends('admin.layouts.dashboard')

@section('content')

   

<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
        <div class="col-md-6">
            <h2>Recibo de Vacaciones</h2>
        </div>
       
        @if (Auth::user()->role_id  == '1' || $agregarmiddleware  == '1' )
        <div class="col-md-6">
            <a href="{{ route('receiptvacations.indexemployees')}}" class="btn btn-primary btn-lg float-md-right" role="button" aria-pressed="true">Registrar un Recibo de Vacaciones</a>
         
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
                <th>Id</th>
                <th>Cédula Empleado</th>
                <th>Nombre Empleado</th>
                <th>Fecha de Inicio</th>
                <th>Fecha de Final</th>
                <th>Dias de Vacaciones</th>
                <th>Bono de Vacaciones</th>
                <th>Dias Feriados</th>
                <th>lph</th>
                <th>sso</th>
                <th>Seguro Paro Forzoso</th>
                <th>Último sueldo</th>
                <th>Total a Pagar</th>
                
                <th>Status</th>
<<<<<<< HEAD
                <th>Opciones</th>
              
=======
                @if (Auth::user()->role_id  == '1' || $actualizarmiddleware  == '1')
                <th>Opciones</th>
                @endif
>>>>>>> fb39095d (todo el proyecto)
            </tr>
            </thead>
            
            <tbody>
                @if (empty($receiptvacations))
                @else
                    @foreach ($receiptvacations as $key => $var)
                    <tr>
                    <td>{{$var->id}}</td>
                    <td>{{ $var->employees['id_empleado']}}</td>
                    <td>{{ $var->employees['nombres']}}</td>
                    <td>{{$var->date_begin}}</td>
                    <td>{{$var->date_end}}</td>
                    <td>{{$var->days_vacations}}</td>
                    <td>{{$var->bono_vacations}}</td>
                    <td>{{$var->days_feriados}}</td>
                    <td>{{$var->lph}}</td>
                    <td>{{$var->sso}}</td>
                    <td>{{$var->seguro_paro_forzoso}}</td>
                    <td>{{$var->ultimo_sueldo}}</td>
                    <td>{{$var->total_pagar}}</td>
                 
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
                        @if (Auth::user()->role_id  == '1' || $actualizarmiddleware  == '1')
>>>>>>> fb39095d (todo el proyecto)
                        <td>
                        <a href="{{route('receiptvacations.edit',$var->id) }}" title="Editar"><i class="fa fa-edit"></i></a>  
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