@extends('admin.layouts.dashboard')

@section('content')

<ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist" style="font-size: 10pt;">
    <li class="nav-item" role="presentation">
      <a class="nav-link  font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('quotations') }}" role="tab" aria-controls="home" aria-selected="true">Cotizaciones</a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link  font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('invoices') }}" role="tab" aria-controls="profile" aria-selected="false">Facturas</a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link active font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('quotations.indexdeliverynote') }}" role="tab" aria-controls="contact" aria-selected="false">Notas De Entrega</a>
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
        <a class="nav-link font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('vendors') }}" role="tab" aria-controls="contact" aria-selected="false">Vendedores</a>
    </li>
  </ul>



<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
      <div class="col-md-6">
          <h2>Notas de Entrega</h2>
      </div>

      <div class="col-md-3">
        <a href="{{ route('quotations.indexdeliverynotesald',"Nota de Entrega")}}" class="btn btn-success float-md-right" role="button" aria-pressed="true">Notas Saldadas</a>
      </div>
      <div class="col-md-3">
        <a href="{{ route('quotations.createquotation',"Nota de Entrega")}}" class="btn btn-primary float-md-right" role="button" aria-pressed="true">Registrar Nota de Entrega</a>
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

                <th class="text-center" style="width:11%;">Fecha</th>
                <th class="text-center">N°</th>
                <th class="text-center">Cliente</th>
                <th class="text-center">Pedido</th>
                <th class="text-center">Vendedor</th>
                <th class="text-center">Abono</th>
                <th class="text-center">REF</th>
                <th class="text-center">Monto</th>
                <th class="text-center" style="width:11%;">F.Cotización</th>
                <th class="text-center"></th>
                <th class="text-center"></th>



            </tr>
            </thead>

            <tbody>
                @if (empty($quotations))
                @else
                    <?php
                    $cont = 0;
                    ?>

                    @foreach ($quotations as $quotation)
                    <?php
                    $amount_bcv = 1;

                    if($quotation->bcv == 0 OR $quotation->bcv == 0.00){
                        $quotation->bcv = 1;
                    }

                    if($quotation->amount_with_iva == 0 OR $quotation->amount_with_iva == 0.00){
                        $quotation->amount_with_iva = 1;
                    }

                    $amount_bcv = $quotation->amount_with_iva / ($quotation->bcv ?? 1);

                    ?>

                      <tr>
                            <td class="text-center">{{ date_format(date_create($quotation->date_delivery_note),"d-m-Y") ?? ''}}</td>
                            <td class="text-center">{{ $quotation->number_delivery_note ?? $quotation->id ?? ''}}</td>
                            <td class="text-center">{{ $quotation->clients['name'] ?? ''}}</td>

                            <!--<td class="text-center">{{ /*$quotation->number_pedido ?? */''}}</td>-->

                            <td class="text-center"><input style="display:none" none; id="pedido{{$cont}}" data-pedido="{{$cont}}" data-quotation="{{$quotation->id}}" type="text" class="form-control pedidoedit2" name="pedido{{$cont}}" value="{{ $quotation->number_pedido ?? '' }}"> <div style="display: block; cursor:pointer;" class="pedidoedit{{$cont}}"> <span data-pedido="{{$cont}}" class="pedidoedit">{{ $quotation->number_pedido ?? 0 }}</span> </div></td>

                            <td class="text-center">{{ $quotation->vendors['name'] ?? ''}} {{ $quotation->vendors['surname'] ?? ''}}</td>
                            <td class="text-center">{{number_format($quotation->amount_anticipo, 2, ',', '.') ?? 0}}</td>
                            <td class="text-center">${{number_format($amount_bcv, 2, ',', '.') ?? 0}}</td>

                            <td class="text-center">{{number_format($quotation->amount_with_iva, 2, ',', '.') ?? 0}}</td>
                            <td class="text-center">{{ date_format(date_create($quotation->date_quotation),"d-m-Y") ?? ''}}</td>
                            @if ($quotation->coin == 'bolivares')
                            <td class="text-center font-weight-bold">Bs</td>
                            @endif
                            @if ($quotation->coin == 'dolares')
                            <td class="text-center font-weight-bold">USD</td>
                            @endif
                            <td class="text-center">

                                <a href="{{ route('quotations.create',[$quotation->id,$quotation->coin,"Nota de Entrega"])}}" title="Seleccionar"><i class="fa fa-check"></i></a>

                                <a href="{{ route('quotations.createdeliverynote',[$quotation->id,$quotation->coin])}}" title="Mostrar"><i class="fa fa-file-alt"></i></a>
                                @if (Auth::user()->mod_delete  == '1')
                                <a href="#" class="delete" data-id-quotation={{$quotation->id}} data-toggle="modal" data-target="#deleteModal" title="Eliminar"><i class="fa fa-trash text-danger"></i></a>
                                @endif

                            </td>

                        </tr>
                        <?php
                        $cont++;
                        ?>
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
            <form action="{{ route('quotations.reversar_delivery_note') }}" method="post">
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


     $(document).on('click','.pedidoedit',function(){
        let id_pedido = $(this).attr('data-pedido');
        /*var valinput = $('#'+id_pedido).val();*/

       $('.pedidoedit'+id_pedido).hide();

       $('#pedido'+id_pedido).show();
       $('#pedido'+id_pedido).focus();
    });

    $(document).on('blur','.pedidoedit2',function(){
        let id_pedido = $(this).attr('data-pedido');
        let id_quotation = $(this).attr('data-quotation');
        var valinput = $('#pedido'+id_pedido).val();

        var url = "{{ route('quotations.indexdeliverynote') }}"+"/"+id_quotation+"/"+valinput;

        window.location.href = url;
       /*  $('#pedido'+id_pedido).hide();
        $('.pedidoedit'+id_pedido).show();*/

    });

    $(document).on('click','.delete',function(){

         let id_quotation = $(this).attr('data-id-quotation');

         $('#id_quotation_modal').val(id_quotation);


     });

    </script>

@endsection
