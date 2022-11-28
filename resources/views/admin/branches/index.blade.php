@extends('admin.layouts.dashboard')

@section('content')

<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
      <div class="col-md-6">
          <h2>Sucursales</h2>
      </div>
      @if (Auth::user()->role_id  == '1' || $agregarmiddleware  == '1' )
      <div class="col-md-6">
        <a href="{{ route('branches.create')}}" class="btn btn-primary btn-lg float-md-right" role="button" aria-pressed="true">Registrar Sucursal</a>
      </div>
      @endif
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
                    <th>Compañía</th>
                    <th>Parroquia</th>
                    <th>Descripción</th>
                    <th>Dirección</th>
                   
                    <th>Telefono</th>
                    <th>Telefono 2</th>

                    <th>Persona de Contacto</th>
                    <th>Número de Contacto</th>
                    <th>Observación</th>
                  
                    <th>Status</th>
                    @if (Auth::user()->role_id  == '1' || $actualizarmiddleware  == '1' )
                    <th>Tools</th>
                    @endif
                </tr>
                </thead>
                
                <tbody>
                    @if (empty($branches))
                    @else  
                        @foreach ($branches as $var)
                            <tr>
                                <td>{{ $var->companies['razon_social'] ?? ''}}</td>
                                <td>{{$var->parroquias['descripcion']}}</td>
                                <td>{{$var->description}}</td>
                                <td>{{$var->direction}}</td>
                                <td>{{$var->phone}}</td>
                                <td>{{$var->phone2}}</td>
                                <td>{{$var->person_contact}}</td>
                                <td>{{$var->phone_contact}}</td>
                                <td>{{$var->observation}}</td>
                                
                                @if($var->status == 1)
                                    <td>Activo</td>
                                @else
                                    <td>Inactivo</td>
                                @endif
                                @if (Auth::user()->role_id  == '1' || $actualizarmiddleware  == '1' )
                                <td>
                                    <a href="branches/{{$var->id }}/edit" title="Editar"><i class="fa fa-edit"></i></a>
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