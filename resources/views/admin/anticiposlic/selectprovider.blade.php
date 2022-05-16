@extends('admin.layouts.dashboard')

@section('content')


  <!-- /.container-fluid -->
  {{-- VALIDACIONES-RESPUESTA--}}
  @include('admin.layouts.success')   {{-- SAVE --}}
  @include('admin.layouts.danger')    {{-- EDITAR --}}
  @include('admin.layouts.delete')    {{-- DELELTE --}}
  {{-- VALIDACIONES-RESPUESTA --}}
  <!-- Page Heading -->
  <div class="row py-lg-2">
    
    <div class="col-md-4 h3 text-center">
     Seleccionar Proveedor
    </div>
    
  </div>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                <tr> 
                    <th></th>
                   
                    <th>Nombre</th>
                    <th>Cedula o Rif</th>
                    <th>Dirección</th>
                    <th>Ciudad</th>
                    <th>Pais</th>
                    <th>Telefono</th>
                    <th>Telefono 2</th>
                    
                </tr>
                </thead>
                
                <tbody>
                    @if (empty($providers))
                    @else  
                        @foreach ($providers as $provider)
                            <tr>
                                <td>
                                    @if (isset($id_anticipo))
                                        <a href="{{ route('anticiposlic.edit',[$id_anticipo,-1,$provider->id]) }}"  title="Seleccionar"><i class="fa fa-check"></i></a>
                                    @else
                                        <a href="{{ route('anticiposlic.create_provider',$provider->id) }}"  title="Seleccionar"><i class="fa fa-check"></i></a>
                                    @endif
                               </td>
                                <td>{{$provider->razon_social}}</td>
                                <td>{{$provider->code_provider}}</td>
                                <td>{{$provider->direction}}</td>
                                <td>{{$provider->city}}</td>
                                <td>{{$provider->country}}</td>
                                <td>{{$provider->phone1}}</td>
                                <td>{{$provider->phone2}}</td>
                                
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