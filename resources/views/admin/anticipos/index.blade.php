@extends('admin.layouts.dashboard')

@section('content')

   
<ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist" style="font-size: 10pt;">
    <li class="nav-item" role="presentation">
      <a class="nav-link  font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('quotations') }}" role="tab" aria-controls="home" aria-selected="true">Cotizaciones</a>
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
        <a class="nav-link active font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('anticipos') }}" role="tab" aria-controls="contact" aria-selected="false">Anticipos Clientes</a>
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
        <div class="col-md-3 h4">
            Anticipos Clientes. 
        </div>
        <div class="col-md-2">
            <a href="{{ route('anticipos')}}" class="btn btn-info btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-hand-holding-usd"></i>
                </span>
                <span class="text">Anticipos</span>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('anticipos.historic')}}" class="btn btn-success btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-book"></i>
                </span>
                <span class="text">Ver Historico de Anticipos</span>
            </a>
        </div>
       
        @if (Auth::user()->role_id  == '1' || $agregarmiddleware  == '1')
        <div class="col-md-3">
            <a href="{{ route('anticipos.create')}}" class="btn btn-primary" role="button" aria-pressed="true">Registrar un Anticipo</a>

        </div>
        @endif
       
            
       
    </div>

  </div>

  {{-- VALIDACIONES-RESPUESTA--}}
@include('admin.layouts.success')   {{-- SAVE --}}
@include('admin.layouts.danger')    {{-- EDITAR --}}
@include('admin.layouts.delete')    {{-- DELELTE --}}
{{-- VALIDACIONES-RESPUESTA --}}

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
            <tr>
                <th class="text-center">ID</th>
                <th class="text-center" width="11%">Fecha del Anticipo</th>                
                <th class="text-center">Cliente</th>
                <th class="text-center">Caja/Banco</th>
                <th class="text-center">Comp.</th>
                <th class="text-center">Referencia</th>
                <th class="text-center">REF</th>
                <th class="text-center">Monto</th>
                <th class="text-center">Moneda</th>
                @if (isset($control) && ($control == 'index'))
                    <th class="text-center"></th>
                @endif
            </tr>
            </thead>
            
            <tbody>
                @if (empty($anticipos))
                @else
                    @foreach ($anticipos as $key => $anticipo)
                    <?php 

                        $amount_bcv = 0;
                        
                        $amount_bcv = $anticipo->amount / $anticipo->rate;


                        if($anticipo->coin != 'bolivares'){
                            $anticipo->amount = $anticipo->amount / $anticipo->rate;
                        }


                        if (isset($anticipo->quotations['number_invoice'])) {
                            
                            $num_fac = 'Factura: '.$anticipo->quotations['number_invoice'].' Ctrl/Serie: '.$anticipo->quotations['serie'];

                        } else {

                            if (isset($anticipo->quotations['number_delivery_note'])) {
                            
                            $num_fac = 'Nota de Entrega: '.$anticipo->quotations['number_delivery_note'].' Ctrl/Serie: '.$anticipo->quotations['serie'];
                            
                            } else {

                                $num_fac = '';
                            }
                        }

                    ?>
                    <tr>
                        @if (isset($anticipo->id_anticipo_restante))
                            <td class="text-center">{{ $anticipo->id }}<br>{{ (isset($anticipo->id_anticipo_restante)) ? 'Restante de: '.$anticipo->id_anticipo_restante : '' }}</td>
                        @else
                            <td class="text-center">{{$anticipo->id ?? ''}}</td>
                        @endif
                        <td class="text-center">{{date('d-m-Y',strtotime($anticipo->date)) ?? ''}}</td>
                        <td class="text-center">{{$anticipo->clients['name'] ?? ''}}<br>{{$num_fac}}</td>
                        <td class="text-center">{{$anticipo->accounts['description'] ?? ''}}</td>
                        <td class="text-center"><a href="{{ route('detailvouchers.create',[$anticipo->coin,$anticipo->comprobante ?? '']) }}" title="Ver comprobante contable">{{ $anticipo->comprobante ?? '' }}</a></td>
                        <td class="text-center">{{$anticipo->reference ?? ''}}</td>
                        <td class="text-right">${{number_format($amount_bcv ?? 0, 2, ',', '.')}}</td>
                        <td class="text-right">{{number_format($anticipo->amount ?? 0, 2, ',', '.')}}</td>
                        <td class="text-center">{{$anticipo->coin ?? ''}}</td>
                   
                        
                        
                        @if (isset($control) && ($control == 'index'))
                            <td>
                                @if (Auth::user()->role_id  == '1' || $actualizarmiddleware  == '1')
                                <a href="{{ route('anticipos.edit',$anticipo->id) }}"  title="Editar"><i class="fa fa-edit"></i></a>
                                @endif
                                @if (Auth::user()->role_id  == '1' || $eliminarmiddleware  == '1')
                                <a href="#" class="delete" data-id-anticipo={{$anticipo->id}} data-toggle="modal" data-target="#deleteModal" title="Eliminar"><i class="fa fa-trash text-danger"></i></a>  
                                @endif
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
            <form action="{{ route('anticipos.delete') }}" method="post">
                @csrf
                @method('DELETE')
                <input id="id_anticipo_modal" type="hidden" class="form-control @error('id_anticipo_modal') is-invalid @enderror" name="id_anticipo_modal" readonly required autocomplete="id_anticipo_modal">
                       
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
        "ordering": true,
        "order": [],
        'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
    });

    

    $(document).on('click','.delete',function(){
         
         let id_anticipo = $(this).attr('data-id-anticipo');

         $('#id_anticipo_modal').val(id_anticipo);
        });
    </script> 
@endsection