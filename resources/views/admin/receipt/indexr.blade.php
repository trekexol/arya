@extends('admin.layouts.dashboard')

@section('content')

@if (Auth::user()->role_id  == '1')
<ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('productsreceipt') }}" role="tab" aria-controls="home" aria-selected="true">Productos/Servicios</a>
      </li>
      <li class="nav-item" role="presentation">
          <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('receipt') }}" role="tab" aria-controls="profile" aria-selected="false">Relación de Gastos de Condominio</a>
      </li>
      <li class="nav-item" role="presentation">
          <a class="nav-link active font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('receiptr') }}" role="tab" aria-controls="profile" aria-selected="false">Recibos de Condominio</a>
        </li>
  
      <li class="nav-item" role="presentation">
          <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('condominiums') }}" role="tab" aria-controls="profile" aria-selected="false">Condominios</a>
      </li>
        <li class="nav-item" role="presentation">
          <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('owners') }}" role="tab" aria-controls="profile" aria-selected="false">Propietarios</a>
      </li>
   <!-- <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="contact-tab"  href="{{ ''/*route('receiptr')*/ }}" role="tab" aria-controls="contact" aria-selected="false">Anticipos Propietarios</a>
    </li>-->
  </ul>
  @endif


  @if (Auth::user()->role_id  == '11')
  <ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">

    <li class="nav-item" role="presentation">
        <a class="nav-link active font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('receiptr') }}" role="tab" aria-controls="profile" aria-selected="false">Recibos de Condominio</a>
      </li>

  </ul>
  @endif


<form method="POST" action="{{ route('invoices.multipayment') }}" enctype="multipart/form-data" >
@csrf
<!-- container-fluid -->
<div class="container-fluid">
    <div class="row py-lg-4">
        <div class="col-md-4">
            <h2>Recibos de Condominio </h2>
        </div>
    </div>
    <!-- Page Heading -->
    <div class="row py-lg-2">

      @if (Auth::user()->role_id  == '1')

       
          <div class="col-sm-3">
            <a href="{{ route('receipt.createreceiptclients',"factura") }}" type="submit" title="Agregar" id="btnRegistrar" class="btn btn-primary  float-md-right" >Generar Recibos de Condomino</a>
          </div>
          <div class="col-sm-3">
            <a href="{{ route('receipt.createreceiptclients',"factura") }}" type="submit" title="Agregar" id="btnRegistrar" class="btn btn-primary  float-md-right" >Crear Recibo Individual</a>
          </div>
          <div class="col-sm-3">
            <a href="{{ route('receipt.createreceiptclients',"factura") }}" type="submit" title="Agregar" id="btnRegistrar" class="btn btn-primary  float-md-right" >Pendientes por Verificar</a>
          </div>
          <div class="col-sm-3">
            <a href="{{ route('receipt.envioreceiptclients') }}" type="submit" title="Agregar" id="btnRegistrar" class="btn btn-primary  float-md-right" >Enviar Correo Masivo</a>
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
                <th class="text-center">Fecha</th>
                <th class="text-center">Nº Recibo</th>
                <th class="text-center">Relación</th>
                <th class="text-center">Propietario</th>
                <th class="text-center">Monto USD</th>
                <th class="text-center">Monto Bs.</th>
                <th class="text-center">Status</th>
                <th class="text-center">Verificado</th>
               <!-- <th class="text-center"></th> -->
            </tr>
            </thead>
            
            <tbody>
                @if (empty($quotations))
                @else  
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
                                <td class="text-center font-weight-bold">{{ $quotation->number_delivery_note }}
                                </td>
                            @else
                                <td class="text-center font-weight-bold">
                                    <a href="{{ route('receipt.createreceiptfacturado',[$quotation->id,$quotation->coin ?? 'bolivares']) }}" title="Ver Factura" class="font-weight-bold text-dark">{{ $quotation->number_delivery_note }}</a>
                                </td>
                            @endif
                            <td class="text-center font-weight-bold">{{$quotation->number_invoice ?? ''}}</td>
                           
                            <td class="text-center font-weight-bold">{{$quotation->owners['name'] ?? ''}}  </td>
                            
                            <td class="text-right font-weight-bold">${{number_format($amount_bcv, 2, ',', '.')}}</td>
                            <td class="text-right font-weight-bold">{{number_format($quotation->amount_with_iva, 2, ',', '.')}}</td>


                            @if ($quotation->status == "C")
                                <td class="text-center font-weight-bold">
                                    @if ($quotation->verified == 1)
                                        @if (Auth::user()->role_id  == '11')
                                        <a href="{{ route('receipt.createreceiptfacturado',[$quotation->id,$quotation->coin ?? 'bolivares']) }}" title="Ver Factura" class="text-center text-success font-weight-bold">Pagado</a>
                                        @endif
                                        @if (Auth::user()->role_id  != '11')
                                        <a href="{{ route('receipt.createreceiptfacturado',[$quotation->id,$quotation->coin ?? 'bolivares']) }}" title="Ver Factura" class="text-center text-success font-weight-bold">Cobrado</a>
                                        @endif
                                    @else
                                        @if (Auth::user()->role_id  == '11')
                                        <a href="{{ route('receipt.createreceiptfacturado',[$quotation->id,$quotation->coin ?? 'bolivares']) }}" title="Ver Factura" class="text-center text-success font-weight-bold">Pendiente por Verificar</a>
                                        @endif
                                        @if (Auth::user()->role_id  != '11')
                                        <a href="{{ route('receipt.createreceiptfacturado',[$quotation->id,$quotation->coin ?? 'bolivares']) }}" title="Ver Factura" class="text-center text-success font-weight-bold">Pendiente por Verificar</a>
                                        @endif
                                    @endif
                                </td>
                                 <td>
                                    @if ($quotation->verified == '1' & $quotation->verified == 1)
                                    <span style="cursor: pointer;" class="verifiedh" data-input="{{$quotation->id}}"><i style="color:green" class="fa fa-check"></i><div style="display: none;"><input type="checkbox" data-input="{{$quotation->id}}" id="verified{{$quotation->id}}"  name="verified" class="verified" value="1" checked ></div></span>    
                                    @else
                                        @if (Auth::user()->role_id  != '11')
                                        <input type="checkbox" data-input="{{$quotation->id}}" id="verified{{$quotation->id}}" name="verified" class="verified" value="0">
                                        @endif
                                    @endif
                                </td>
                            @elseif ($quotation->status == "X")
                                <td class="text-center font-weight-bold text-danger">Reversado
                                </td>
                                <td>
                                </td>
                            @else
                                @if (($diferencia_en_dias >= 0) && ($validator_date))
                                    <td class="text-center font-weight-bold">
                                        <a href="{{ route('receipt.createfacturar_aftereceipt',[$quotation->id,$quotation->coin ?? 'bolivares']) }}" title="Cobrar Factura" class="font-weight-bold" style="color: rgb(255, 183, 0)">Click para Cobrar<br>Vencida ({{$diferencia_en_dias}} dias)</a>
                                    </td>
                                @else
                                    <td class="text-center font-weight-bold">
                                        @if (Auth::user()->role_id  == '11')
                                        <a href="{{ route('receipt.createfacturar_aftereceipt',[$quotation->id,$quotation->coin ?? 'bolivares']) }}" title="Cobrar Factura" class="font-weight-bold text-dark">Click para Pagar</a>
                                        @endif
                                        @if (Auth::user()->role_id  != '11')
                                        <a href="{{ route('receipt.createfacturar_aftereceipt',[$quotation->id,$quotation->coin ?? 'bolivares']) }}" title="Cobrar Factura" class="font-weight-bold text-dark">Click para Cobrar</a>
                                        @endif

                                    </td>
                                @endif
  

                                  
                                <td>
                                    <a href="#" title="Enviar Correo" data-rute="{{ route('mails.receipt',[$quotation->id,$quotation->coin]) }}" data-email="{{$quotation->owners['email'] ?? ''}}" data-msg="Recibo de Condomino Fecha {{ date_format(date_create($quotation->date_billing),"d-m-Y") ?? '' }}" data-toggle="modal" data-target="#emailModal" class="buttonemail"><i class="fa fa-envelope"></i></a> 
                                
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
</form>

<div class="modal modal-danger fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Enviar Recibo por Correo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form id="envemail" action="" method="post">
                @csrf
                @method('POST')
                <h5 class="text-center">Email:</h5>
                <input id="caprute" type="hidden" value="">
                <input id="email_modal" type="text" class="form-control @error('email_modal') is-invalid @enderror" name="email_modal" value="" required autocomplete="email_modal">
                <br>
                <h5 class="text-center">Mensaje:</h5>
                <input id="message_modal" type="text" class="form-control @error('message_modal') is-invalid @enderror" name="message_modal" value="" required autocomplete="message_modal">
                       
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
        $('#dataTable').dataTable( {
        "ordering": false,
        "order": [],
            'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
    } );

        

        
        $(document).on('change','.verified',function(){
            let id_quotation = $(this).attr('data-input');
            

            if($("#verified"+id_quotation).is(':checked')) {
                var check = 1;
            } else {
                var check = 0;
            }

            var url = "{{ route('receiptr') }}"+"/"+id_quotation+"/"+check;
                window.location.href = url;

            
        });

        $(document).on('click','.verifiedh',function(){
            let id_quotation = $(this).attr('data-input');
        
            var check = 0;

            var url = "{{ route('receiptr') }}"+"/"+id_quotation+"/"+check;
                window.location.href = url;

            
        });

        
        $(document).on('click','.buttonemail',function(){
         let email = $(this).attr('data-email');
         let msg = $(this).attr('data-msg');
         let rute = $(this).attr('data-rute');
          
         $('#email_modal').val(email);
         $('#message_modal').val(msg);
         $("#envemail").attr("action",rute);  
         $("#caprute").val(rute);       
        });


    </script>
@endsection

