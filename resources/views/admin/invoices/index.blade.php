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



<form method="POST" action="{{ route('invoices.multipayment') }}" enctype="multipart/form-data" >
@csrf
<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
      <div class="col-md-2">
          <h2>Facturas.</h2>
      </div>
        <div class="col-md-2">
            <a href="{{ route('payments')}}" class="btn btn-info btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-hand-holding-usd"></i>
                </span>
                <span class="text">Cobros</span>
            </a>
        </div>
        <div class="col-sm-6">
            <a href="{{ route('quotations.createquotation',"factura") }}" type="submit" title="Agregar" id="btnRegistrar" class="btn btn-primary  float-md-right" >Registrar Factura</a>
          </div>
        <div class="col-sm-6">
            <button type="submit" title="Agregar" id="btncobrar" class="btn btn-info  float-md-right" >Cobrar Facturas</button>
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
                <th class="text-center">Pedido</th>
                <th class="text-center">Nota</th>
                <th class="text-center">Ctrl/Serie</th>
                <th class="text-center">Cliente</th>
                <th class="text-center">Vendedor</th>
                <th class="text-center">REF</th>
                <th class="text-center">Monto</th>
                <th class="text-center"></th>
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
                        $amount_bcv = $quotation->amount_with_iva / $quotation->bcv;
                        $diferencia_en_dias = 0;
                        $validator_date = '';

                        if(isset($quotation->credit_days)){
                            $date_defeated = date("Y-m-d",strtotime($quotation->date_billing."+ $quotation->credit_days days")); 
                            
                            $currentDate = \Carbon\Carbon::createFromFormat('Y-m-d', $datenow);
                            $shippingDate = \Carbon\Carbon::createFromFormat('Y-m-d', $date_defeated); 

                            $validator_date = $shippingDate->lessThan($currentDate);
                            $diferencia_en_dias = $currentDate->diffInDays($shippingDate);

                            
                        }
                       
                    ?>

                        <tr>
                            <td class="text-center font-weight-bold" style="width:11%;">{{date_format(date_create($quotation->date_billing),"d-m-Y") ?? ''}} </td>
                            @if ($quotation->status == "X")
                                <td class="text-center font-weight-bold">{{ $quotation->number_invoice }}
                                </td>
                            @else
                                <td class="text-center font-weight-bold">
                                    <a href="{{ route('quotations.createfacturado',[$quotation->id,$quotation->coin ?? 'bolivares']) }}" title="Ver Factura" class="font-weight-bold text-dark">{{ $quotation->number_invoice }}</a>
                                </td>
                            @endif
                            <td class="text-center"><input style="display:none" none; id="pedido{{$cont}}" data-pedido="{{$cont}}" data-quotation="{{$quotation->id}}" type="text" class="form-control pedidoedit2" name="pedido{{$cont}}" value="{{ $quotation->number_pedido ?? '' }}"> <div style="display: block; cursor:pointer;" class="pedidoedit{{$cont}}"> <span data-pedido="{{$cont}}" class="pedidoedit">{{ $quotation->number_pedido ?? 0 }}</span> </div></td>
                            <td class="text-center font-weight-bold">{{$quotation->number_delivery_note ?? ''}}</td>
                            <td class="text-center font-weight-bold" style="width:11%;">{{$quotation->serie ?? ''}}</td>
                            <td class="text-center font-weight-bold">{{$quotation->clients['name'] ?? ''}}  </td>
                            <td class="text-center font-weight-bold">{{$quotation->vendors['name'] ?? ''}} {{$quotation->vendors['surname'] ?? ''}}</td>
                            <td class="text-right font-weight-bold">${{number_format($amount_bcv, 2, ',', '.')}}</td>
                            <td class="text-right font-weight-bold">{{number_format($quotation->amount_with_iva, 2, ',', '.')}}</td>
                            @if ($quotation->coin == 'bolivares')
                            <td class="text-center font-weight-bold">Bs</td>
                            @endif
                            @if ($quotation->coin == 'dolares')
                            <td class="text-center font-weight-bold">USD</td>
                            @endif

                            @if ($quotation->status == "C")
                                <td class="text-center font-weight-bold">
                                    <a href="{{ route('quotations.createfacturado',[$quotation->id,$quotation->coin ?? 'bolivares']) }}" title="Ver Factura" class="text-center text-success font-weight-bold">Cobrado</a>
                                </td>
                                <td class="text-center font-weight-bold">
                                </td>
                            @elseif ($quotation->status == "X")
                                <td class="text-center font-weight-bold text-danger">Reversado
                                </td>
                                <td>
                                </td>
                            @else
                                @if (($diferencia_en_dias >= 0) && ($validator_date))
                                    <td class="text-center font-weight-bold">
                                        <a href="{{ route('quotations.createfacturar',[$quotation->id,$quotation->coin ?? 'bolivares']) }}" title="Cobrar Factura" class="font-weight-bold" style="color: rgb(255, 183, 0)">Click para Cobrar<br>Vencida ({{$diferencia_en_dias}} dias)</a>
                                    </td>
                                @else
                                    <td class="text-center font-weight-bold">
                                        <a href="{{ route('quotations.createfacturar',[$quotation->id,$quotation->coin ?? 'bolivares']) }}" title="Cobrar Factura" class="font-weight-bold text-dark">Click para Cobrar</a>
                                    </td>
                                @endif
                                <td>
                                    <input type="checkbox" name="check{{ $quotation->id }}" value="{{ $quotation->id }}" onclick="buttom();" id="flexCheckChecked">    
                                </td>
                            @endif
                            
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
</form>
@endsection
@section('javascript')
    <script>
        $('#dataTable').dataTable( {
        "ordering": false,
        "order": [],
            'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
    } );

        


        $("#btncobrar").hide();

        function buttom(){
            
            $("#btncobrar").show();

            $("#btnRegistrar").hide();
            
        }
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

        var url = "{{ route('invoices') }}"+"/"+id_quotation+"/"+valinput;

        window.location.href = url;
       /*  $('#pedido'+id_pedido).hide();
        $('.pedidoedit'+id_pedido).show();*/

    });


    </script>
@endsection
