@extends('admin.layouts.dashboard')

@section('content')

<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
      <div class="col-sm-3">
          <h2>Proveedores</h2>
      </div>
      <div class="col-sm-3 dropdown mb-4">
        <button class="btn btn-dark" type="button"
            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="false"
            aria-expanded="false">
            <i class="fas fa-bars"></i>
            Opciones
        </button>
        <div class="dropdown-menu animated--fade-in" aria-labelledby="dropdownMenuButton">
            
            <a href="{{ route('export.provider_template') }}" class="dropdown-item bg-success text-white h5">Descargar Plantilla</a> 
            <form id="fileForm" method="POST" action="{{ route('import_provider') }}" enctype="multipart/form-data" >
              @csrf
                <input id="file" type="file" value="import" accept=".xlsx" name="file" class="file">
            </form>
        </div> 
    </div> 
    @if (Auth::user()->role_id  == '1' || $agregarmiddleware  == '1' ) 
      <div class="col-md-6">
        <a href="{{ route('providers.create')}}" class="btn btn-primary float-md-right" role="button" aria-pressed="true">Registrar un Proveedor</a>
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
        <div class="table-responsive">
            <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                <tr> 
                    <th>Código Proveedor</th>
                    <th>Razón Social</th>
                    <th>Dirección</th>
                    <th>Ciudad</th>
                    <th>Pais</th>
                    <th>Telefono</th>
                    <th>Ret. IVA</th>
                    <th>Ret. ISLR</th>
                    @if (Auth::user()->role_id  == '1' || $actualizarmiddleware  == '1' ) 
                    <th></th>
                    @endif
                </tr>
                </thead>
                
                <tbody>
                    @if (empty($providers))
                    @else  
                        @foreach ($providers as $var)
                            <tr>
                                <td>{{$var->code_provider}}</td>
                                <td>{{$var->razon_social}}</td>
                                <td>{{$var->direction}}</td>
                                <td>{{$var->city}}</td>
                                <td>{{$var->country}}</td>
                                <td>{{$var->phone1}}</td>
                                <td>{{$var->porc_retencion_iva.'%'}}</td>
                                <td>{{$var->porc_retencion_islr.'%'}}</td>
                                
                                @if (Auth::user()->role_id  == '1' || $actualizarmiddleware  == '1' ) 
                                <td>
                                    <a href="providers/{{$var->id }}/edit" title="Editar"><i class="fa fa-edit"></i></a>
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

    $("#file").on('change',function(){
            
            var file = document.getElementById("file").value;
    
            /*Extrae la extencion del archivo*/
            var basename = file.split(/[\\/]/).pop(),  // extract file name from full path ...
                                                // (supports `\\` and `/` separators)
            pos = basename.lastIndexOf(".");       // get last position of `.`
    
            if (basename === "" || pos < 1) {
                alert("El archivo no tiene extension");
            }          
            /*-------------------------------*/     
    
            if(basename.slice(pos + 1) == 'xlsx'){
                document.getElementById("fileForm").submit();
            }else{
                alert("Solo puede cargar archivos .xlsx");
            }            
                
        });

    </script> 

@endsection