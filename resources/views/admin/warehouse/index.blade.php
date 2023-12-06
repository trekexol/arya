@extends('admin.layouts.dashboard')

@section('content')

<ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
    @if (Auth::user()->role_id  == '1')


      <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" href="{{ route('products') }}" role="tab" aria-controls="home" aria-selected="true">Productos</a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link  font-weight-bold" style="color: black;"  href="{{ route('inventories') }}" role="tab" aria-controls="profile" aria-selected="false">Inventario</a>
      </li>
      <li class="nav-item" role="presentation">
          <a class="nav-link font-weight-bold" style="color: black;" href="{{ route('combos') }}" role="tab" aria-controls="home" aria-selected="true">Combos</a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" href="{{ route('inventories.movement') }}" role="tab" aria-controls="contact" aria-selected="false">Movimientos de Inventario</a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link active font-weight-bold" style="color: black;" href="{{ route('warehouse') }}" role="tab" aria-controls="contact" aria-selected="false">Almacenes</a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" href="{{ route('warehouse.movement') }}" role="tab" aria-controls="contact" aria-selected="false">Transferencia de Almacén</a>
      </li>

    @else

    @foreach($sistemas as $sistemas)
    @if($namemodulomiddleware == $sistemas->name)
<li class="nav-item" role="presentation">
    <a class="nav-link active font-weight-bold" style="color: black;" id="home-tab"  href="{{ route($sistemas->ruta) }}" role="tab" aria-controls="home" aria-selected="false">{{$sistemas->name}}</a>
  </li>
  @else
  <li class="nav-item" role="presentation">
    <a class="nav-link font-weight-bold" style="color: black;" id="home-tab"  href="{{ route($sistemas->ruta) }}" role="tab" aria-controls="home" aria-selected="false">{{$sistemas->name}}</a>
  </li>
  @endif
  @if($sistemas->name == 'Inventario')
  <li class="nav-item" role="presentation">
      <a class="nav-link font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('inventories.movement') }}" role="tab" aria-controls="contact" aria-selected="false">Movimientos de Inventario</a>
    </li>
  @endif
@endforeach


  @endif
  </ul>


<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
      <div class="col-md-6">
          <h2>Almacenes</h2>
      </div>
      @if (Auth::user()->role_id  == '1' || $agregarmiddleware  == '1' )
      <div class="col-md-6">
        <a href="{{ route('warehouse.create')}}" class="btn btn-primary btn-lg float-md-right" role="button" aria-pressed="true">Registrar Almacén</a>
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

                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Telefono</th>
                    <th>Telefono 2</th>
                    <th>Persona de Contacto</th>
                    <th>Número de Contacto</th>
                    <th>Observación</th>
                    <th>Status</th>
                    <th>Compañía</th>
                    @if (Auth::user()->role_id  == '1' || $actualizarmiddleware  == '1' )
                    <th>Tools</th>
                    @endif
                </tr>
                </thead>
                
                <tbody>
                    @if (empty($warehouse))
                    @else  
                        @foreach ($warehouse as $var)
                            <tr>

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
                                <td>{{ $var->companies['razon_social'] ?? ''}}</td>
                                @if (Auth::user()->role_id  == '1' || $actualizarmiddleware  == '1' )
                                <td>
                                    <a href="warehouse/{{$var->id }}/edit" title="Editar"><i class="fa fa-edit"></i></a>
                                    <a href="#" onclick="deletealmacen({{$var->id}})" title="Eliminar"><i class="fa fa-trash text-danger"></i></a>  
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


    function deletealmacen(almacen) {
        if (confirm('¿Desea eliminar el almacén?')) { 
           $.ajax({
                url: `warehouse/verificalmacen`,
                method: 'GET',
                data: { id: almacen},            
                success: (response) => {
  
                    if (response.existe == 'No') {
                        // Redireccionar a la ruta de eliminación
                        window.location.href = `warehouse/delete/${almacen}`;
                    } else {
                        alert('El almacén tiene movimientos registrados');
                    }
                },
                error: (xhr, status, error) => {
                    alert('La verificación no se pudo completar, recargar la página: ' + error);
                }
            });
        }
    }

    </script> 

@endsection