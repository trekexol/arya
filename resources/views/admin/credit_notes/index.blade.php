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
        <a class="nav-link active font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('creditnotes') }}" role="tab" aria-controls="home" aria-selected="true">Notas de Crédito</a>
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
      
      @if (isset($historial))
        <div class="col-md-5">
            <h2>Historial de Notas de Crédito</h2>
        </div>
        <div class="col-md-3">
            <a href="{{ route('creditnotes')}}" class="btn btn-info  float-md-right" role="button" aria-pressed="true">Notas de Credito</a>
        </div>
      @else
        <div class="col-md-3">
            <h2>Notas de Crédito</h2>
        </div>
        <div class="col-md-3">
            <a href="{{ route('creditnotes.historial')}}" class="btn btn-info  float-md-right" role="button" aria-pressed="true">Historial</a>
        </div>
      @endif
      
      <div class="col-md-4">
        <a href="{{ route('creditnotes.createcreditnote')}}" class="btn btn-primary  float-md-right" role="button" aria-pressed="true">Registrar una Nota de Crédito</a>
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
                <th class="text-center">Fecha</th>
                <th class="text-center">N°</th>
                <th class="text-center">Factura</th>
                <th class="text-center">Cliente</th>
                <th class="text-center">Vendedor</th>
                <th class="text-center">Moneda</th>
                <th class="text-center">Monto USD</th>
                <th class="text-center">Monto Bs</th>
                <th class="text-center">(S)</th>
                <th class="text-center"></th>
               
            </tr>
            </thead>
            
            <tbody>
                @if (empty($creditnotes))
                @else  
                    @foreach ($creditnotes as $creditnote)
                        <tr>
                            <td>
                                <a href="{{ route('creditnotes.create',[$creditnote->id,'bolivares']) }}" title="Seleccionar"><i class="fa fa-check" style="color: orange;"></i></a>
                                 <a href="{{ route('pdf.creditnotemediacarta',[$creditnote->id,$creditnote->coin])}},ventana,left=800,top=800,height=800,width=1000,scrollbar=si,location=no,resizable=si,menubar=no" title="Seleccionar"><i class="fa fa-print" style="color: rgb(46, 132, 243);"></i></a>
                            </td>
                            <td class="text-center">{{ date_format(date_create($creditnote->date),"d-m-Y") ?? ''}}</td>
                            <td class="text-center">{{ $creditnote->serie ?? $creditnote->id ?? ''}}</td>
                            <td class="text-center">{{ $creditnote->quotations['number_invoice'] ?? ''}}</td>
                            <td class="text-center">{{ $creditnote->clients['name'] ?? $creditnote->quotations->clients['name'] ?? ''}}</td>
                            <td class="text-center">{{ $creditnote->vendors['name'] ?? $creditnote->quotations->vendors['name'] ?? ''}}</td>
                            <td class="text-center">{{ $creditnote->coin ?? ''}}</td>
                            <td class="text-center">{{ number_format($creditnote->amount_with_iva / $creditnote->rate ?? 0, 2, ',', '.')}}</td>
                            <td class="text-center">{{ number_format($creditnote->amount_with_iva ?? 0, 2, ',', '.')}}</td>
                            @if($creditnote->status == 'C')
                            <td class="text-center" style="color:darkgreen">{{ $creditnote->status ?? ''}}</td>
                            <td>
                            </td>  
                            @else
                            <td class="text-center">P</td>
                            <td>
                                <a href="#" class="delete" data-id-creditnote={{$creditnote->id}} data-toggle="modal" data-target="#deleteModal" title="Eliminar"><i class="fa fa-trash text-danger"></i></a>  
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
          <form action="{{ route('creditnotes.deletecreditnote') }}" method="post">
              @csrf
              @method('DELETE')
              <input id="id_creditnote_modal" type="hidden" class="form-control @error('id_creditnote_modal') is-invalid @enderror" name="id_creditnote_modal" readonly required autocomplete="id_creditnote_modal">
                     
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
         
        let id_creditnote = $(this).attr('data-id-creditnote');

        $('#id_creditnote_modal').val(id_creditnote);
    });
    </script> 

@endsection