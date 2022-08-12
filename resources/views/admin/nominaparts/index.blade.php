@extends('admin.layouts.dashboard')

@section('content')

<?php 

$pres_active = '';
$utili_active = '';
$vaca_active = '';
$liqui_active = '';

        if ($type == 'prestaciones'){
            $pres_active = 'active';
        }
        if ($type == 'utilidades'){
            $utili_active = 'active';
        }
        if ($type == 'vacaciones'){
            $vaca_active = 'active';
        }
        if ($type == 'liquidaciones'){
            $liqui_active = 'active';
        }

?>
<ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
    <a class="nav-link  font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('nominas') }}" role="tab" aria-controls="home" aria-selected="true">Nóminas</a>
    </li>
    <li class="nav-item" role="presentation">
    <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('nominaconcepts') }}" role="tab" aria-controls="profile" aria-selected="false">Conceptos de Nómina</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('nominabasescalc') }}" role="tab" aria-controls="profile" aria-selected="false">Bases de Cálculo</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('employees') }}" role="tab" aria-controls="profile" aria-selected="false">Empleados</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{$pres_active}} font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('nominaparts','prestaciones') }}" role="tab" aria-controls="profile" aria-selected="false">Prestaciones</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{$utili_active}} font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('nominaparts','utilidades') }}" role="tab" aria-controls="profile" aria-selected="false">Utilidades</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{$vaca_active}} font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('nominaparts','vacaciones') }}" role="tab" aria-controls="profile" aria-selected="false">Vacaciones</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{$liqui_active}} font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('nominaparts','liquidaciones') }}" role="tab" aria-controls="profile" aria-selected="false">Liquidaciones</a>
    </li>
</ul>

<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
      <div class="col-md-6">
        @if ($type == 'prestaciones')
        <h2>Prestaciones de Empleados</h2>
        @endif
        @if ($type == 'utilidades')
        <h2>Utilidades de Empleados</h2>
        @endif
        @if ($type == 'vacaciones')
        <h2>Vacaciones de Empleados</h2>
        @endif
        @if ($type == 'liquidaciones')
        <h2>Liquidación de Empleados</h2>
        @endif  
        
      </div>
      <div class="col-md-6" style="display: none;">
        <a href="{{ route('employees.create')}}" class="btn btn-primary btn-lg float-md-right" role="button" aria-pressed="true">Registrar Empleado</a>
      </div>
      @if ($type == 'vacaciones')
    
      <div class="col-md-6">
        <a href="{{ route('nominas.create_recibo_vacaciones')}}" class="btn btn-primary float-md-right" role="button" aria-pressed="true">Crear Recibo de Vacacioens</a>   
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
                <th>Sueldo</th>
                @if ($type == 'prestaciones')
                <th>Prestaciones</th>
                <th>Intereses</th>
                <th>Prestaciones + Int</th>
                <th></th>
                @endif
                @if ($type == 'utilidades')
                <th>Periodo</th>
                <th>Dias</th>
                <th>Alicuota de Utilidad</th>
                <th>Banavih</th>
                <th>INCES</th>
                <th>Total Utilidades</th>
                <th></th>
                @endif
                @if ($type == 'vacaciones')
                <th>Dias de Vac. del Periodo</th>
                <th>Dias de Vac. Disfutadas</th>
                <th>Dias de Vac. Acumuladas</th>
                <th></th>
                @endif
                @if ($type == 'liquidacion')
                <th>Salario Diario</th>
                <th>Salario Integral</th>
                <th>Prestaciones Acumuladas</th>
                <th>Prestaciones Sociales</th>
                <th>Intereses</th>
                <th>Total Bonificación y Vacaciones</th>
                <th></th>
                @endif

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

                            <td>{{number_format($employee->monto_pago, 2, ',', '.')}}</td>
                            
                            @if ($type == 'prestaciones')
                            <td>{{'0'}}</td>
                            <td>{{'0'}}</td>
                            <td>{{'0'}}</td>
                            <td>
                            <a href="{{ route('pdf.prestations',[$employee->id]) }}" title="Imprimir"><i class="fa fa-print" style="color: rgb(46, 132, 243);"></i></a> 
                            </td>
                            @endif
                            @if ($type == 'utilidades')
                            <td>{{' '}}</td>
                            <td>{{'0'}}</td>
                            <td>{{'0'}}</td>
                            <td>{{'0'}}</td>
                            <td>{{'0'}}</td>
                            <td>{{'0'}}</td>
                            <td>
                            <a href="{{ route('pdf.quotation',[$employee->id]) }}" title="Imprimir"><i class="fa fa-print" style="color: rgb(46, 132, 243);"></i></a> 
                            </td>
                            @endif
                            @if ($type == 'vacaciones')
                            <td>{{'0'}}</td>
                            <td>{{'0'}}</td>
                            <td>{{'0'}}</td>
                            <td>
                            <!--<a href="{{ ''/*route('pdf.quotation',[$employee->id]) */}}" title="Imprimir"><i class="fa fa-print" style="color: rgb(46, 132, 243);"></i></a> -->
                            </td>
                            @endif
                            @if ($type == 'liquidacion')
                            <td>{{'0'}}</td>
                            <td>{{'0'}}</td>
                            <td>{{'0'}}</td>
                            <td>{{'0'}}</td>
                            <td>{{'0'}}</td>
                            <td>{{'0'}}</td>
                            <td>
                            <a href="{{ route('pdf.quotation',[$employee->id]) }}" title="Imprimir"><i class="fa fa-print" style="color: rgb(46, 132, 243);"></i></a> 
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
