@extends('admin.layouts.dashboard')

@section('content')

<div class="container-fluid">

    <!-- Page Heading -->
    
    <div class="justify-content-left border-left-danger">
        <div class="card-body h4">
                <div class="row py-lg-2">
                    <div class="col-md-6">
                        Nómina {{ $var->id }}: {{ $var->description }} 
                    </div>
                </div>
                <div class="row py-lg-2">
                    <div class="col-md-6">
                        Nómina: {{ $var->type }}
                    </div>
                </div>
                <div class="row py-lg-2">
                    <div class="col-md-6">
                        Tipo de Nómina: {{ $nomina_type_id_name }} 
                    </div>
                    <div class="col-md-3">
                        <!--<a href="{{ ''/*route('employees.create') */}}" class="btn btn-primary float-md-right" role="button" aria-pressed="true">Registrar Empleado</a>-->
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('nominas')}}" class="btn btn-danger float-md-right" role="button" aria-pressed="true">Volver</a>
                    </div>
                </div>
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
                <th class="text-center">Cédula</th>
                <th class="text-center">Nombres</th>
                <th class="text-center">Monto</th>
                <th class="text-center">Salario Neto</th>
                <th class="text-center">Otras Asignaciones</th>
                <th class="text-center">Deducciones</th>
                <th class="text-center">Total a Pagar</th>
                <th></th>
            </tr>
            </thead>
            
            <tbody>
                @if (empty($employees))
                @else 
                
                <?php 
                $total_asignaciones = 0;
                $total_deducciones = 0;
                $total_a_pagar = 0;
                $total_amount_salary = 0;
                $total_amount_salary_neto = 0;
                ?>
                    @foreach ($employees as $employee)
                        <tr>
                            <td class="text-center">{{$employee->id_empleado}}</td>
                            <td class="text-center">{{$employee->nombres}} {{$employee->apellidos}}</td>
                            <td class="text-center">{{number_format($employee->amount_salary, 2, ',', '.')}}</td>
                            <td class="text-center">{{number_format($employee->amount_salary - $employee->deducciones, 2, ',', '.')}}</td>
                            <td class="text-center">{{number_format($employee->asignaciones, 2, ',', '.')}}</td>
                            <td class="text-center">{{number_format($employee->deducciones, 2, ',', '.')}}</td>
                            <td class="text-center">{{number_format($employee->monto_pago, 2, ',', '.')}}</td>
                            
                           
                            <td class="text-center">
                                <a href="{{route('nominacalculations',[$var->id,$employee->id]) }}" title="Ver Detalles"><i class="fa fa-binoculars"></i></a>  
                           </td>
                        </tr>
                        <?php 
                        $total_amount_salary += number_format($employee->amount_salary, 2, '.', '');
                        $total_amount_salary_neto += number_format($employee->amount_salary - $employee->deducciones, 2, '.', '');
                        $total_asignaciones += number_format($employee->asignaciones, 2, '.', '');
                        $total_deducciones += number_format($employee->deducciones, 2, '.', '');
                        $total_a_pagar += number_format($employee->monto_pago, 2, '.', '');
                        ?>
                    @endforeach   
                @endif
            </tbody>
            <tfoot>
                <tr> 
                    <th class="text-center">Totales</th>
                    <th class="text-center">...</th>
                    <th class="text-center">{{number_format($total_amount_salary,2,'.','')}}</th>
                    
                    <th class="text-center"><strong>{{number_format($total_amount_salary_neto,2,'.','')}}</strong></th>
                    <th class="text-center"><strong>{{number_format($total_asignaciones,2,'.','')}}</strong></th>
                    <th class="text-center">{{number_format($total_deducciones,2,'.','')}}</th>
                    <th class="text-center"><strong>{{number_format($total_a_pagar,2,'.','')}}</strong></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
        </div>
    </div>
</div>
@if (empty($employee->id)) 
@else
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">¿Estás segura de que quieres eliminar esto?</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
        </div>
        <div class="modal-body">Seleccione "eliminar" si realmente desea eliminar este Militante.</div>
        <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
        <form method="POST" action="/employees/{{ $employee->id }}">
            @method('DELETE')
            @csrf
            <input type="hidden" id="employee_id" name="employee_id" value="">
            <a class="btn btn-primary"  onclick="$(this).closest('form').submit();">Confirmar</a>
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
    </script> 
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