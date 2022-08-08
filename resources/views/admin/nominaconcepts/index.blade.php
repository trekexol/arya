@extends('admin.layouts.dashboard')

@section('content')

    <ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
        <a class="nav-link  font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('nominas') }}" role="tab" aria-controls="home" aria-selected="true">Nóminas</a>
        </li>
        <li class="nav-item" role="presentation">
        <a class="nav-link active font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('nominaconcepts') }}" role="tab" aria-controls="profile" aria-selected="false">Conceptos de Nómina</a>
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
            <h2>Conceptos de Nóminas Registradas</h2>
        </div>
       
        @if (Auth::user()->role_id  == '1' || Auth::user()->role_id  == '2' )
        <div class="col-md-6">
            <a href="{{ route('nominaconcepts.create')}}" class="btn btn-primary float-md-right" role="button" aria-pressed="true">Registrar un Concepto de Nómina</a>
         
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
                <th class="text-center">Abreviatura</th>
                <th class="text-center">Descripción</th>
                <th class="text-center">Signo</th>
                <th class="text-center">Tipo de Nómina</th>
                <th class="text-center">Fórmula Mensual</th>
                <th class="text-center">Fórmula Semanal</th>
                <th class="text-center">Fórmula Quincenal</th>
                <th class="text-center">Calcular Nómina</th>
               <th class="text-center"></th>
              
            </tr>
            </thead>
            
            <tbody>
                @if (empty($nominaconcepts))
                @else
                    @foreach ($nominaconcepts as $nominaconcept)
                    <tr>

                    <td class="text-center font-weight-bold">{{$nominaconcept->abbreviation}}</td>
                    <td class="text-center">{{$nominaconcept->description}}</td>
                    @if($nominaconcept->sign == "A")
                        <td class="text-center">Asignación</td>
                    @else
                        <td class="text-center">Deducción</td>
                    @endif
                    
                    <td class="text-center">{{$nominaconcept->type}}</td>
                    <td class="text-center">{{$nominaconcept->formulasm['description'] ?? ''}}</td>
                    <td class="text-center">{{$nominaconcept->formulass['description'] ?? ''}}</td>
                    <td class="text-center">{{$nominaconcept->formulasq['description'] ?? ''}}</td>
                   
                    @if($nominaconcept->calculate == "S")
                        <td class="text-center">Si</td>
                    @else
                        <td class="text-center">No</td>
                    @endif
                    @if (Auth::user()->role_id  == '1')
                        <td class="text-center">
                            <a href="{{route('nominaconcepts.edit',$nominaconcept->id) }}" title="Editar"><i class="fa fa-edit"></i></a>  
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