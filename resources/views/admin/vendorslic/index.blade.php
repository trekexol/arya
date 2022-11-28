@extends('admin.layouts.dashboard')

@section('content')


<ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
      <a class="nav-link font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('quotationslic') }}" role="tab" aria-controls="home" aria-selected="true">Cotizaciones<span style="color: green;">•</span></a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('invoiceslic') }}" role="tab" aria-controls="profile" aria-selected="false">Facturas<span style="color: green;">•</span></a>
    </li>

    <li class="nav-item" role="presentation">
      <a class="nav-link font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('quotationslic.indexdeliverynote') }}" role="tab" aria-controls="contact" aria-selected="false">Notas De Entrega<span style="color: green;">•</span></a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('clientslic') }}" role="tab" aria-controls="profile" aria-selected="false">Clientes<span style="color: green;">•</span></a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link active font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('vendorslic') }}" role="tab" aria-controls="contact" aria-selected="false">Vendedores<span style="color: green;">•</span></a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('saleslic') }}" role="tab" aria-controls="profile" aria-selected="false">Ventas<span style="color: green;">•</span></a>
      </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('anticiposlic') }}" role="tab" aria-controls="contact" aria-selected="false">Anticipos Clientes<span style="color: green;">•</span></a>
    </li>
  </ul>




<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
      <div class="col-md-6">
          <h2>Vendedores</h2>
      </div>
      @if (Auth::user()->role_id  == '1' || $agregarmiddleware  == '1')
      <div class="col-md-6">
        <a href="{{ route('vendorslic.create')}}" class="btn btn-primary btn-lg float-md-right" role="button" aria-pressed="true">Registrar un Vendedor</a>
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
                            
                            <td>{{$vendor->name}}</td>
                            <td>{{$vendor->surname}}</td>
                            <td>{{$vendor->cedula_rif}}</td>
                            <td>{{$vendor->comision}}%</td>
                            <td>{{$vendor->email}}</td>
                            <td>{{$vendor->phone}}</td>
                            <td>
                                @if (Auth::user()->role_id  == '1' || $actualizarmiddleware  == '1')
                                <a href="vendorslic/{{$vendor->id }}/edit" title="Editar"><i class="fa fa-edit"></i></a>
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