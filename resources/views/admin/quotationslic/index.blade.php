@extends('admin.layouts.dashboard')

@section('content')


  <ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
      <a class="nav-link active font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('quotationslic') }}" role="tab" aria-controls="home" aria-selected="true">Cotizaciones<span style="color: green;">•</span></a>
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
        <a class="nav-link font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('vendorslic') }}" role="tab" aria-controls="contact" aria-selected="false">Vendedores<span style="color: green;">•</span></a>
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
          <h2>Cotizaciones</h2>
      </div>
      @if (Auth::user()->role_id  == '1' || $agregarmiddleware  == '1')
      <div class="col-md-6">
        <a href="{{ route('quotationslic.createquotation')}}" class="btn btn-primary  float-md-right" role="button" aria-pressed="true">Registrar una Cotización</a>
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
        <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0" >
            <thead>
            <tr> 
                <th class="text-center"></th>
                <th class="text-center">ID</th>
                <th class="text-center">N° de Control/Serie</th>
                <th class="text-center">Cliente</th>
                <th class="text-center">Vendedor</th>
                <th class="text-center">Transporte</th>
                <th class="text-center">Fecha de Cotización</th>
                <th class="text-center"></th>
               
            </tr>
            </thead>
            
            <tbody>
                @if (empty($quotations))
                @else  
                    @foreach ($quotations as $quotation)
                        <tr>
                            <td>
                            @if (Auth::user()->role_id  == '1' || $agregarmiddleware  == '1')
                            <a href="{{ route('quotationslic.create',[$quotation->id,'bolivares']) }}" title="Seleccionar"><i class="fa fa-check" style="color: orange;"></i></a>
                            @endif
                        </td>
                            <td class="text-center">{{$quotation->id ?? ''}}</td>
                            <td class="text-center">{{$quotation->serie ?? ''}}</td>
                            <td class="text-center">{{ $quotation->clients['name'] ?? ''}}</td>
                            <td class="text-center">{{ $quotation->vendors['name'] ?? ''}}</td>
                            <td class="text-center">{{ $quotation->transports['placa'] ?? ''}}</td>
                            <td class="text-center">{{ $quotation->date_quotation ?? ''}}</td>
                            <td>
                                @if (Auth::user()->role_id  == '1' || $eliminarmiddleware  == '1')
                            <a href="#" class="delete" data-id-quotation={{$quotation->id}} data-toggle="modal" data-target="#deleteModal" title="Eliminar"><i class="fa fa-trash text-danger"></i></a>  
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
<!-- Delete Warning Modal -->
<div class="modal modal-danger fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Eliminar</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body">
          <form action="{{ route('quotationslic.deleteQuotation') }}" method="post">
              @csrf
              @method('DELETE')
              <input id="id_quotation_modal" type="hidden" class="form-control @error('id_quotation_modal') is-invalid @enderror" name="id_quotation_modal" readonly required autocomplete="id_quotation_modal">
                     
              <h5 class="text-center">Seguro que desea eliminar?</h5>
              
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-danger">Eliminar</button>
          </div>
          </form>
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

    $(document).on('click','.delete',function(){
         
        let id_quotation = $(this).attr('data-id-quotation');

        $('#id_quotation_modal').val(id_quotation);
    });
    </script> 

@endsection