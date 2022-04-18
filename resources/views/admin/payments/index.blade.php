@extends('admin.layouts.dashboard')

@section('content')


<ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
      <a class="nav-link  font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('quotations') }}" role="tab" aria-controls="home" aria-selected="true">Cotizaciones</a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link active font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('invoices') }}" role="tab" aria-controls="profile" aria-selected="false">Facturas</a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('quotations.indexdeliverynote') }}" role="tab" aria-controls="contact" aria-selected="false">Notas De Entrega</a>
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
        <div class="col-md-2">
            <h2>Cobros</h2>
        </div>
        <div class="col-md-2">
            <a href="{{ route('invoices')}}" class="btn btn-info btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-file-alt"></i>
                </span>
                <span class="text">Facturas</span>
            </a>
        </div>
        <div class="col-md-2 offset-sm-1">
            <a href="{{ route('report_payments.index','todos')}}" class="btn btn-info btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-print"></i>
                </span>
                <span class="text">Reporte</span>
            </a>
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
                <th class="text-center">Fecha</th>
                <th class="text-center">Nº</th>
                <th class="text-center">Nº Factura</th>
                <th class="text-center">Cliente</th>
                <th class="text-center">Referencia</th>
                <th class="text-center">Tipo de Pago</th>
                <th class="text-center">Cuenta</th>
                <th class="text-center">Monto</th>
                <th class="text-center">$</th>
                <th class="text-center" width="5%"></th>
            </tr>
            </thead>
            
            <tbody>
                @if (empty($payment_quotations))
                @else  
                    @foreach ($payment_quotations as $payment_quotation)
                        <tr>
                            <td class="text-center font-weight-bold">{{$payment_quotation->created_at->format('d-m-Y')}}</td>
                            
                            <td class="text-center font-weight-bold">
                                <a href="{{ route('payments.movement',$payment_quotation->id_quotation ?? -1) }}" title="Ver Movimiento" class="font-weight-bold text-dark">{{ $payment_quotation->id }}</a>
                            </td>
                            <td class="text-center font-weight-bold">{{$payment_quotation->number_invoice ?? ''}}</td>
                            <td class="text-center font-weight-bold">{{$payment_quotation->name_client ?? ''}}</td>
                            <td class="text-center font-weight-bold">{{ $payment_quotation->reference}}</td>
                            <td class="text-center font-weight-bold">{{ $payment_quotation->type}}</td>
                            <td class="text-center font-weight-bold">{{ $payment_quotation->description_account ?? ''}}</td>
                            <td class="text-right font-weight-bold">{{number_format($payment_quotation->amount, 2, ',', '.')}}</td>
                            <td class="text-right font-weight-bold">{{number_format(bcdiv(($payment_quotation->amount / $payment_quotation->rate), '1', 2), 2, ',', '.')}}$</td>
                            <td class="text-center">
                                <a href="#" onclick="pdf({{ $payment_quotation->id }});" title="Mostrar"><i class="fa fa-file-alt"></i></a>
                                <a href="#"  class="delete" title="Borrar" data-id-quotation={{$payment_quotation->id_quotation ?? -1}} data-toggle="modal" data-target="#deleteModal"><i class="fa fa-trash text-danger"></i></a>                        
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
                <h5 class="modal-title" id="exampleModalLabel">Eliminar Todos los Pagos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form action="{{ route('payments.deleteAllPayments') }}" method="post">
                @csrf
                @method('DELETE')
                <input id="id_quotation_modal" type="hidden" class="form-control @error('id_quotation_modal') is-invalid @enderror" name="id_quotation_modal" readonly required autocomplete="id_quotation_modal">
                    
                <h5 class="text-center">Seguro que desea eliminar todos los pagos pertenecientes a esta factura?</h5>
                
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
        $('#dataTable').dataTable( {
        "ordering": false,
        "order": [],
            'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
        } );

        
       
        function pdf(id_payment) {
            var nuevaVentana= window.open("{{ route('payments.pdf',['',''])}}"+"/"+id_payment+"/"+'bolivares',"ventana","left=800,top=800,height=800,width=1000,scrollbar=si,location=no ,resizable=si,menubar=no");   
        }

        $(document).on('click','.delete',function(){
            let id_quotation = $(this).attr('data-id-quotation');
        
            $('#id_quotation_modal').val(id_quotation);
        });
    </script>
@endsection
