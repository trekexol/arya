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
        <a class="nav-link font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('debitnotes') }}" role="tab" aria-controls="home" aria-selected="true">Notas de Débito</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('sales') }}" role="tab" aria-controls="profile" aria-selected="false">Ventas</a>
      </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('anticipos') }}" role="tab" aria-controls="contact" aria-selected="false">Anticipos Clientes</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('clients') }}" role="tab" aria-controls="profile" aria-selected="false">Clientes</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link active font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('vendors') }}" role="tab" aria-controls="contact" aria-selected="false">Vendedores</a>
    </li>
  </ul>



<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
      <div class="col-md-6">
          <h2>Vendedores</h2>
      </div>
      @if(Auth::user()->role_id  == '1' || $agregarmiddleware == 1)
      <div class="col-md-6">
        <a href="{{ route('vendors.create')}}" class="btn btn-primary btn-lg float-md-right" role="button" aria-pressed="true">Registrar un Vendedor</a>
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
<nav class="col-md-12">
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
      <button class="nav-link active" id="nav-home-tab" data-toggle="tab" data-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">ACTIVOS</button>
      <button class="nav-link" id="nav-profile-tab" data-toggle="tab" data-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">INACTIVOS</button>
    </div>
  </nav>
  <div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">

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
                        <th>ID</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Cédula o Rif</th>
                        <th>Comisión</th>
                        <th>Correo Electrónico</th>
                        <th>Teléfono</th>
                        <th></th>
                    </tr>
                    </thead>

                    <tbody>
                        @if (empty($vendors))
                        @else
                            @foreach ($vendors as $vendor)
                                <tr>
                                    <td>{{$vendor->id}}</td>
                                    <td>{{$vendor->name}}</td>
                                    <td>{{$vendor->surname}}</td>
                                    <td>{{$vendor->cedula_rif}}</td>
                                    <td>{{$vendor->comision}}%</td>
                                    <td>{{$vendor->email}}</td>
                                    <td>{{$vendor->phone}}</td>
                                    <td>
                                    @if(Auth::user()->role_id  == '1' || $actualizarmiddleware == 1)
                                        <a href="vendors/{{$vendor->id }}/edit" title="Editar"><i class="fa fa-edit"></i></a>
                                    @endif
                                     </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                </div>
            </div>
        </div>

    </div>



    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">

        <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">

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
                    <table class="table table-light2 table-bordered" id="dataTables" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Cédula o Rif</th>
                            <th>Comisión</th>
                            <th>Correo Electrónico</th>
                            <th>Teléfono</th>
                            <th></th>
                        </tr>
                        </thead>

                        <tbody>
                            @if (empty($vendorsinac))
                            @else
                                @foreach ($vendorsinac as $vendorsinac)
                                    <tr>
                                        <td>{{$vendorsinac->id}}</td>
                                        <td>{{$vendorsinac->name}}</td>
                                        <td>{{$vendorsinac->surname}}</td>
                                        <td>{{$vendorsinac->cedula_rif}}</td>
                                        <td>{{$vendorsinac->comision}}%</td>
                                        <td>{{$vendorsinac->email}}</td>
                                        <td>{{$vendorsinac->phone}}</td>
                                        <td>
                                        @if(Auth::user()->role_id  == '1' || $actualizarmiddleware == 1)
                                            <a href="vendors/{{$vendorsinac->id }}/edit" title="Editar"><i class="fa fa-edit"></i></a>
                                        @endif
                                         </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>

        </div>

    </div>
  </div>



@endsection
@section('javascript')

    <script>
    $('#dataTable, #dataTables').DataTable({
        "ordering": false,
        "order": [],
        'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
    });



    </script>

@endsection
