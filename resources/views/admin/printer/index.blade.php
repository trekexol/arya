@extends('admin.layouts.dashboard')

@section('content')

<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
  
     <div class="col-sm-3">
          <h2>Printer llamada</h2>
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
               
            <a href="{{ route('printer.printer') }}" class="dropdown-item bg-success text-white h5">Imprimir</a> 

    </div>
</div>


    
@endsection
