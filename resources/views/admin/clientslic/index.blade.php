@extends('admin.layouts.dashboard')

@section('content')



<ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
      <a class="nav-link  font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('quotations') }}" role="tab" aria-controls="home" aria-selected="true">Cotizaciones</a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link  font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('invoices') }}" role="tab" aria-controls="profile" aria-selected="false">Facturas</a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('quotations.indexdeliverynote') }}" role="tab" aria-controls="contact" aria-selected="false">Notas De Entrega</a>
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
      <div class="col-md-6">
          <h2>Clientes.</h2>
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
                   
                    <th>Nombre</th>
                    <th>Cedula o Rif</th>
                    <th>Dirección</th>
                    <th>Telefono</th>
                  
                    <th>Dias de Crédito</th>
                   
                    <th>Vendedor</th>
                    
                    <th></th>
                </tr>
                </thead>
                
                <tbody>
                    @if (empty($clients))
                    @else  
                        @foreach ($clients as $client)
                            <tr>
                               
                                <td>{{$client->name}}</td>
                                <td>{{$client->type_code}} {{$client->cedula_rif}}</td>
                                <td>{{$client->direction}}</td>
                                <td>{{$client->phone1}}</td>
                                <td>{{$client->days_credit}}</td>
                               

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
    </script> 
@endsection