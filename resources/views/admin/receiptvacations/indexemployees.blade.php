@extends('admin.layouts.dashboard')

@section('content')

<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
      <div class="col-md-6">
          <h2>Seleccione un Empleado</h2>
      </div> 
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
        <div class="container">
            @if (session('flash'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{session('flash')}}
                <button type="button" class="close" data-dismiss="alert" aria-label="close">
                    <span aria-hidden="true">&times; </span>
                </button>
            </div>   
        @endif
        </div>
        <div class="table-responsive">
        <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
            <tr> 
                <th>Cedula</th>
                <th>Nombres</th>
                <th>Apellidos</th>
                <th>Celular</th>
                <th>Fecha Ingreso</th>
                <th>Fecha Egreso</th>
                <th>Fecha.Nac</th>
                <th>Dirección</th>
                <th>Monto de Pago</th>
                <th>Correo Electronico</th>
                <th>Acumulado Prestaciones</th>
                <th>Acumulado Utilidades</th>
                <th>Status</th>
                <th>Centro Costo</th>
                <th>Tools</th>
            </tr>
            </thead>
            
            <tbody>
                @if (empty($employees))
                @else  
                    @foreach ($employees as $employee)
                        <tr>
                            <td>
                                <a class="text-dark" href="register/{{$employee->id}}" title="elejir">{{$employee->id_empleado}}</a>
                            </td>
                            <td>{{$employee->nombres}}</td>
                            <td>{{$employee->apellidos}}</td>
                            <td>{{$employee->telefono1}}</td>
                            <td>{{$employee->fecha_ingreso}}</td>
                            <td>{{$employee->fecha_egreso}}</td>
                            <td>{{$employee->fecha_nacimiento}}</td>
                            <td>{{$employee->direccion}}</td>
                            <td>{{$employee->monto_pago}}</td>
                            <td>{{$employee->email}}</td>
                            
                            <td>{{$employee->acumulado_prestaciones}}</td>
                            <td>{{$employee->acumulado_utilidades}}</td>
                            
                            @if($employee->status == 1)
                                <td>Activo</td>
                            @else
                                <td>Inactivo</td>
                            @endif
                            
                            <td>{{$employee->centro_cos}}</td>
                            @if (Auth::user()->role_id  == '1' || $agregarmiddleware  == '1')
                            <td>
                                <a href="employees/{{$employee->id }}" title="Ver"><i class="fas fa-file-alt"></i></a>
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
@section('js_modal_employees')
    <script>
        $('#deleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var employee_id = button.data('employeeid') // Extract info from data-* attributes
        // console.log(post_id)
        // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
        // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
        var modal = $(this)
        modal.find('.modal-footer #employee_id').val(employee_id)
        });
        $(document).ready(function() {
            $('#example').DataTable( {
                "order": [[ 5, "desc" ]]
            } );
        } );
   
    </script>
@endsection

@section('javascript')

<script>
    $('#dataTable').dataTable( {
      "ordering": false,
      "order": [],
            'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]],
            'iDisplayLength': '50'
    } );
</script>
    
@endsection