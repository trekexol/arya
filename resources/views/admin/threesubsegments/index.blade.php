@extends('admin.layouts.dashboard')

@section('content')

   

<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
        <div class="col-md-8">
            <h2>Tercer Sub Segmento Registrado</h2>
        </div>
       
        @if (Auth::user()->role_id  == '1' || $agregarmiddleware  == '1' )
        <div class="col-md-4">
            <a href="{{ route('threesubsegments.create')}}" class="btn btn-primary btn-lg float-md-right" role="button" aria-pressed="true">Registrar</a>
         
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
                
                <th>Descripción</th>
                <th>Segundo Sub Segmento</th>
                <th>Status</th>
<<<<<<< HEAD
                <th class="col-md-1"></th>
              
=======
                @if (Auth::user()->role_id  == '1' || $actualizarmiddleware == '1') 
                <th class="col-md-1"></th>
                @endif
>>>>>>> fb39095d (todo el proyecto)
            </tr>
            </thead>
            
            <tbody>
                @if (empty($subsegments))
                @else
                    @foreach ($subsegments as $key => $segment)
                    <tr>
                    
                    <td>{{$segment->description ?? ''}}</td>
                    <td>{{ $segment->subsegments['description'] ?? ''}}</td>
                    
<<<<<<< HEAD
                    @if (Auth::user()->role_id  == '1')
=======
                    
>>>>>>> fb39095d (todo el proyecto)
                            @if($segment->status == 1)
                                <td>Activo</td>
                            @else
                                <td>Inactivo</td>
                            @endif
<<<<<<< HEAD
                        <td>
=======
                            @if (Auth::user()->role_id  == '1' || $actualizarmiddleware == '1') 
                            <td>
>>>>>>> fb39095d (todo el proyecto)
                        <a href="{{route('threesubsegments.edit',$segment->id) }}" title="Editar"><i class="fa fa-edit"></i></a>  
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