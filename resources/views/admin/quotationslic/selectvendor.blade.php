@extends('admin.layouts.dashboard')

@section('content')

<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
      <div class="col-md-6">
          <h2>Seleccionar Vendedor</h2>
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
                <th></th>
               
                <th>Cédula o Rif</th>
                <th>Nombres</th>
                <th>Apellidos</th>
                <th>Correo Electrónico</th>
                <th>Teléfono</th>
                <th>Teléfono 2</th>
               
               
            </tr>
            </thead>
            
            <tbody>
                @if (empty($vendors))
                @else  
                    @foreach ($vendors as $vendor)
                        <tr>
                            <td>
                                <a href="{{ route('quotations.createquotationvendor',[$id_client ?? '-1',$vendor->id ?? '-1']) }}"  title="Seleccionar"><i class="fa fa-check" style="color: orange"></i></a>
                            </td>
                            
                            <td>{{$vendor->cedula_rif}}</td>
                            <td>{{$vendor->name}}</td>
                            <td>{{$vendor->surname}}</td>
                            <td>{{$vendor->email}}</td>
                            <td>{{$vendor->phone}}</td>
                            <td>{{$vendor->phone2}}</td>
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