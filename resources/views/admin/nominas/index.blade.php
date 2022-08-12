@extends('admin.layouts.dashboard')

@section('content')

<ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
    <a class="nav-link active font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('nominas') }}" role="tab" aria-controls="home" aria-selected="true">Nóminas</a>
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
        <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('nominaparts','prestaciones') }}" role="tab" aria-controls="profile" aria-selected="false">Prestaciones</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('nominaparts','utilidades') }}" role="tab" aria-controls="profile" aria-selected="false">Utilidades</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('nominaparts','vacaciones') }}" role="tab" aria-controls="profile" aria-selected="false">Vacaciones</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('nominaparts','liquidaciones') }}" role="tab" aria-controls="profile" aria-selected="false">Liquidaciones</a>
    </li>
</ul>
<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
        <div class="col-md-6">
            <h2>Nóminas Registradas</h2>
        </div>
       
        @if (Auth::user()->role_id  == '1' || Auth::user()->role_id  == '2' )
        <div class="col-md-6">
            <a href="{{ route('nominas.create')}}" class="btn btn-primary float-md-right" role="button" aria-pressed="true">Registrar una Nómina</a>
         
        </div>
        @endif
    </div>
  </div>   

<!-- container-fluid -->
<div class="container-fluid" style="display:none;">

    <!-- Page Heading -->
    <div class="row py-lg-3">
        <div class="col-sm-2  dropdown mb-4">
            <button class="btn btn-light2" type="button"
                id="dropdownMenuButton" data-toggle="dropdown" >
                <i class="fas fa-bars"></i>
                    Recibos
                
            </button>
            <div class="dropdown-menu animated--fade-in"
                aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="{{ route('nominas.create_recibo_vacaciones') }}">Recibo de Vacaciones</a>
                <a class="dropdown-item" href="{{ route('nominas.create_recibo_prestaciones') }}">Recibo de Prestaciones</a>
                <a class="dropdown-item" href="{{ route('nominas.create_recibo_utilidades') }}">Recibo de Utilidades</a>
            </div>
        </div> 
       
        <div class="col-sm-3">
            <a href="{{ route('nominas.create_recibo_liquidacion_auto') }}" class="btn btn-light2"><i class="fas fa-print" ></i>
                Calcula Liquidación Auto
            </a>
        </div>
        <div class="col-sm-3">
            <a href="#" class="btn btn-light2"><i class="fas fa-print" ></i>
                Crear Liquidación
            </a>
        </div>
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
                <th class="text-center">ID</th>
                <th class="text-center">Descripción</th>
                <th class="text-center">Nómina</th>
                <th class="text-center" style="width:11%">Desde</th>
                <th class="text-center" style="width:11%">Hasta</th>
                <th class="text-center" style="width:10%">Tipo de Nómina</th>
                <th class="text-center">Ver</th>
                <th class="text-center">Calcular</th>
               <th class="text-center" style="width:8%">PDF</th>
               <th class="text-center">Acción</th>
              
            </tr>
            </thead>
            
            <tbody>
                @if (empty($nominas))

                @else
                    @foreach ($nominas as $key => $nomina)
                    <tr>
                    <td class="text-center">{{$nomina->id}}</td>
                    <td class="text-center">{{$nomina->description}}</td>
                    <td class="text-center">{{$nomina->type}}</td>
                    <td class="text-center">{{$nomina->date_begin}}</td>
                    <td class="text-center">{{$nomina->date_end}}</td>
                    <td class="text-center">{{$nomina->nomina_type_id_name}}</td>
                    
                    <td class="text-center">
                        <a href="{{route('nominas.selectemployee',$nomina->id) }}" title="Ver Detalles"><i class="fa fa-binoculars"></i></a> 
                    </td>

                    <td class="text-center">  
                        <a href="{{route('nominas.calculate',$nomina->id) }}" title="Recalcular Conceptos de Nómina"><i class="fa fa-calculator"></i> </a>   
                        @if ($nomina->check_exist == 'Existe')
                        <a href="{{route('nominas.calculatecont',$nomina->id) }}" title="Recrear Asiento Contable"><i class="fa fa-list-alt" style="color: green"></i></a>      
                        <a href="{{route('nominas.searchMovementNomina',$nomina->id) }}" title="Ver Movimiento Contable Nomina"><i class="fa fa-search"></i></a>  
                        @else
                        <a href="{{route('nominas.calculatecont',$nomina->id) }}" title="Crear Asiento Contable"><i class="fa fa-list-alt" ></i></a>     
                        @endif    
                    </td>

                    <td class="text-center">    
                    <a href="{{route('nominas.print_nomina_calculation_all',$nomina->id)}}" target="_blank" title="Todos los Recibos Individuales"><i class="fa fa-print"></i></a>  
                    <a href="{{route('nominas.print_payrool_summary',$nomina->id)}}" target="_blank" onclick="" title="Resumen de la Nomina"><i class="fa fa-print"></i></a>  
                    <a href="{{route('nominas.print_payrool_summary_all',$nomina->id)}}" target="_blank" onclick="" title="Reporte de la Nomina"><i class="fa fa-print"></i></a>  
                    </td>  
                    
                    <td class="text-center">
                        <a href="{{route('nominas.edit',$nomina->id) }}" title="Editar"><i class="fa fa-edit"></i></a>
                        <a href="#" class="delete" data-id-nomina={{$nomina->id}} data-toggle="modal" data-target="#deleteModal" title="Eliminar"><i class="fa fa-trash text-danger"></i></a>  
                    </td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        </div>
    </div>
</div>

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
            <form action="{{ route('nominas.delete') }}" method="post">
                @csrf
                @method('DELETE')
                <input id="id_nomina_modal" type="hidden" class="form-control @error('id_nomina_modal') is-invalid @enderror" name="id_nomina_modal" readonly required autocomplete="id_nomina_modal">
                       
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

@if (isset($exist_nomina_calculation))
<div class="modal modal-danger fade" id="recalculateModal" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ya se calculó la Nómina: {{$exist_nomina_calculation->description ?? ''}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            
                <h5 class="text-center">Seguro desea Eliminar los cálculos y hacerlos nuevamente? Nota: (Se perderán los conceptos que no esten programados con el cálculo de la nómina) </h5>
                
            </div>
            <div class="modal-footer">
                <a href="{{ route('nominas.recalculate',$exist_nomina_calculation->id) }}" type="submit" class="btn btn-info">Recalcular</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
           
        </div>
    </div>
  </div>

@endif

@if (isset($exist_nomina_calculationcont))
<div class="modal modal-danger fade" id="recalculateModalcont" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabelcont">Recrear comprobante Contable de la Nómina: {{$exist_nomina_calculationcont->id_nomina}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            
                <h5 class="text-center">Seguro desea recrear los comprobantes nuevamente? </h5>
                
            </div>
            <div class="modal-footer">
                <a href="{{ route('nominas.recalculatecont',$exist_nomina_calculationcont->id_nomina) }}" type="submit" class="btn btn-info">Recrear</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
           
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
         
         let id_nomina = $(this).attr('data-id-nomina');

         $('#id_nomina_modal').val(id_nomina);

    });


    if("{{isset($exist_nomina_calculation)}}"){
        $('#recalculateModal').modal('show'); // abrir
    }

    if("{{isset($exist_nomina_calculationcont)}}"){
        $('#recalculateModalcont').modal('show'); // abrir
    }
   
    </script> 
@endsection