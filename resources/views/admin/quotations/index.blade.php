@extends('admin.layouts.dashboard')

@section('content')


  <ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
      <a class="nav-link active font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('quotations') }}" role="tab" aria-controls="home" aria-selected="true">Cotizaciones</a>
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
        <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('sales') }}" role="tab" aria-controls="profile" aria-selected="false">Ventas</a>
      </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('anticipos') }}" role="tab" aria-controls="contact" aria-selected="false">Anticipos Clientes</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('clients') }}" role="tab" aria-controls="profile" aria-selected="false">Clientes</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('vendors') }}" role="tab" aria-controls="contact" aria-selected="false">Vendedores</a>
    </li>
  </ul>
  
<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
      <div class="col-md-4">
          <h2>Cotizaciones</h2>
      </div>
      <div class="col-sm-2">
        <select class="form-control" name="coin" id="coin">
            @if(isset($coin))
                <option disabled selected value="{{ $coin }}">{{ $coin }}</option>
                <option disabled  value="{{ $coin }}">-----------</option>
            @else
                <option disabled selected value="bolivares">Moneda</option>
            @endif
            
            <option  value="bolivares">Bolívares</option>
            <option value="dolares">Dólares</option>
        </select>
    </div>
      <div class="col-md-6">
        <a href="{{ route('quotations.createquotation')}}" class="btn btn-primary  float-md-right" role="button" aria-pressed="true">Registrar una Cotización</a>
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
        <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0" >
            <thead>
            <tr> 
                <th class="text-center"></th>
                <th class="text-center">N° de Control/Serie</th>
                <th class="text-center">Cliente</th>
                <th class="text-center">Vendedor</th>
                <th class="text-center">Transp. / Tipo de Entrega</th>
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
                                <a href="{{ route('quotations.create',[$quotation->id,'bolivares']) }}" title="Seleccionar"><i class="fa fa-check" style="color: orange;"></i></a>
                                <a href="{{ route('pdf.quotation',[$quotation->id,$coin ?? 'bolivares']) }}" title="Imprimir"><i class="fa fa-print" style="color: rgb(46, 132, 243);"></i></a> 
                                <a href="#" class="send" data-toggle="modal" data-id-quotation-send={{$quotation->id}} data-target="#emailModal" title="Enviar por Correo"><i class="fa fa-paper-plane" style="color: rgb(128, 119, 119);"></i></a> 
                            </td>
                            <td class="text-center">{{$quotation->serie ?? ''}}</td>
                            <td class="text-center">{{ $quotation->clients['name'] ?? ''}}</td>
                            <td class="text-center">{{ $quotation->vendors['name'] ?? ''}}</td>
                            <td class="text-center">{{ $quotation->transports['placa'] ?? ''}}</td>
                            <td class="text-center">{{ $quotation->date_quotation ?? ''}}</td>
                            <td>
                            <a href="#" class="delete" data-id-quotation={{$quotation->id}} data-toggle="modal" data-target="#deleteModal" title="Eliminar"><i class="fa fa-trash text-danger"></i></a>  
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
          <form action="{{ route('quotations.deleteQuotation') }}" method="post">
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
<div class="modal modal-danger fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Enviar Cotización por Correo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form action="{{ route('mails.quotationIndex',[$coin ?? 'bolivares']) }}" method="post">
                @csrf
                @method('POST')

                <input id="id_quotation_send_modal" type="hidden" class="form-control @error('id_quotation_send_modal') is-invalid @enderror" name="id_quotation_send_modal" readonly required autocomplete="id_quotation_send_modal">
               
                <h5 class="text-center">Email:</h5>
                <input id="email_modal" type="text" class="form-control @error('email_modal') is-invalid @enderror" name="email_modal" value="{{ $quotation->clients['email'] ?? '' }}" required autocomplete="email_modal">
                <br>
                <h5 class="text-center">Mensaje:</h5>
                <input id="message_modal" type="text" class="form-control @error('message_modal') is-invalid @enderror" name="message_modal" value="{{ $company->message_from_email ?? '' }}" required autocomplete="message_modal">
                       
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Enviar Correo</button> 
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
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


    $(document).on('click','.send',function(){
         
        let id_quotation_send = $(this).attr('data-id-quotation-send');

        $('#id_quotation_send_modal').val(id_quotation_send);
     });

    $("#coin").on('change',function(){
        
        var coin = $(this).val();
        window.location = "{{route('quotations', '')}}"+"/"+coin;
    });

    $(document).on('click','.delete',function(){
         
        let id_quotation = $(this).attr('data-id-quotation');

        $('#id_quotation_modal').val(id_quotation);
    });
    </script> 

@endsection