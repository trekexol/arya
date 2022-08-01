@extends('admin.layouts.dashboard')

@section('content')

<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
      <div class="col-md-6">
          <h2>Empleados</h2>
      </div>
      <div class="col-md-6">
        <a href="{{ route('employees.create')}}" class="btn btn-primary btn-lg float-md-right" role="button" aria-pressed="true">Registrar Empleado</a>
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
                <th>Tipo de Nómina</th>
                <th>Salario</th>
                <th>Estatus</th>
                <th></th>
            </tr>
            </thead>
            
            <tbody>
                @if (empty($employees))
                @else  
                    @foreach ($employees as $employee)
                        <tr>
                            <td>{{$employee->id_empleado}}</td>
                            <td>{{$employee->nombres}}</td>
                            <td>{{$employee->apellidos}}</td>
                            <td>{{$employee->profession_id}}</td>
                            <td>{{number_format($employee->monto_pago, 2, ',', '.')}}</td>
                            <td>{{$employee->status}}</td>
                            <td>
                                <a href="employees/{{$employee->id }}/edit" title="Editar"><i class="fa fa-edit"></i></a>
                                <a href="#" class="delete" data-id-employee={{$employee->id}} data-toggle="modal" data-target="#deleteModal" title="Eliminar"><i class="fa fa-trash text-danger"></i></a>  
                            </td>
                        </tr>     
                    @endforeach   
                @endif
            </tbody>
        </table>
        </div>
    </div>
</div>
@if (empty($employee->id)) 
@else
<!-- Delete Warning Modal -->
<div class="modal modal-danger fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Eliminar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form action="{{ route('employees.delete') }}" method="post">
                @csrf
                @method('DELETE')
                <input id="id_employee_modal" type="hidden" class="form-control @error('id_employee_modal') is-invalid @enderror" name="id_employee_modal" readonly required autocomplete="id_employee_modal">
                       
                <h5 class="text-center">Seguro que desea eliminar?</h5>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger">Eliminar</button>
            </div>
            </form>
        </div>
    </div>
  </div>
@endif

    
@endsection
@section('javascript')
    <script>
    $('#dataTable').DataTable({
        "ordering": false,
        "order": [],
        'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
    });

    $(document).on('click','.delete',function(){
         
         let id_employee = $(this).attr('data-id-employee');

         $('#id_employee_modal').val(id_employee);

    });
    </script> 
@endsection
