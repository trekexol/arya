@extends('admin.layouts.dashboard')

@section('content')



<ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist" style="font-size: 10pt;">
    <li class="nav-item" role="presentation">
      <a class="nav-link font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('quotations') }}" role="tab" aria-controls="home" aria-selected="true">Cotizaciones</a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('invoices') }}" role="tab" aria-controls="profile" aria-selected="false">Facturas</a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('quotations.indexdeliverynote') }}" role="tab" aria-controls="contact" aria-selected="false">Notas De Entrega</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('orders.index') }}" role="tab" aria-controls="contact" aria-selected="false">Pedidos</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('creditnotes') }}" role="tab" aria-controls="home" aria-selected="true">Notas de Crédito</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('debitnotes') }}" role="tab" aria-controls="home" aria-selected="true">Notas de Dédito</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('sales') }}" role="tab" aria-controls="profile" aria-selected="false">Ventas</a>
      </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('anticipos') }}" role="tab" aria-controls="contact" aria-selected="false">Anticipos Clientes</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link active font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('clients') }}" role="tab" aria-controls="profile" aria-selected="false">Clientes</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('vendors') }}" role="tab" aria-controls="contact" aria-selected="false">Vendedores</a>
    </li>
  </ul>
  

<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
      <div class="col-sm-3">
          <h2>Clientes</h2>
      </div>
      <div class="col-sm-3 dropdown mb-4">
        <button class="btn btn-dark" type="button"
            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="false"
            aria-expanded="false">
            <i class="fas fa-bars"></i>
            Opciones
        </button>
        <div class="dropdown-menu animated--fade-in" aria-labelledby="dropdownMenuButton">
            
            <a href="{{ route('export.client_template') }}" class="dropdown-item bg-success text-white h5">Descargar Plantilla</a> 
            <form id="fileForm" method="POST" action="{{ route('import_client') }}" enctype="multipart/form-data" >
              @csrf
                <input id="file" type="file" value="import" accept=".xlsx" name="file" class="file">
            </form>
        </div> 
    </div> 
      <div class="col-md-6">
        <a href="{{ route('clients.create')}}" class="btn btn-primary btn-lg float-md-right" role="button" aria-pressed="true">Registrar Cliente</a>
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
        <div class="table-responsive">
            <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                <tr> 
                    <th>ID</th>
                    <th>Nombre / Razón Social</th>
                    <th>Nombre Comercial</th>
                    <th>Cedula o Rif</th>
                    <th>Dirección</th>
                    <th>Telefono</th>
                  
                    <th>Vendedor</th>
                    
                    <th></th>
                </tr>
                </thead>
                
                <tbody>
                    @if (empty($clients))
                    @else  
                        @foreach ($clients as $client)
                            <tr>
                                <td>{{$client->id}}</td>
                                <td>{{$client->name}}</td>
                                <td>{{$client->name_ref}}</td>
                                <td>{{$client->type_code}} {{$client->cedula_rif}}</td>
                                <td>{{$client->direction}}</td>
                                <td>{{$client->phone1}}</td>
                               

                                @if (isset($client->vendors['name']))
                                    <td>{{$client->vendors['name']}}</td>
                                @else
                                    <td></td>
                                @endif
                                

                                
                                <td>
                                    <a href="clients/{{$client->id }}/edit" title="Editar"><i class="fa fa-edit"></i></a>
                                </td>
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