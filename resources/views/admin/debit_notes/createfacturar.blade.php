@extends('admin.layouts.dashboard')

@section('content')



    {{-- VALIDACIONES-RESPUESTA--}}
    @include('admin.layouts.success')   {{-- SAVE --}}
    @include('admin.layouts.danger')    {{-- EDITAR --}}
    @include('admin.layouts.delete')    {{-- DELELTE --}}
    {{-- VALIDACIONES-RESPUESTA --}}
    
@if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{$error}}</li>
                @endforeach
            </ul>
        </div>
    @endif



<div class="container" >
    <div class="row justify-content-center" >
        
            <div class="card" style="width: 70rem;" >
                <div class="card-header" ><h3>Facturar</h3></div>
                
                <div class="card-body" >

                        <input type="hidden" name="coin" value="{{$coin}}" readonly>

                        <!--Precio de costo de todos los productos-->
                        <input type="hidden" name="price_cost_total" value="{{$price_cost_total}}" readonly>
                        <input id="user_id" type="hidden" class="form-control @error('user_id') is-invalid @enderror" name="user_id" value="{{ Auth::user()->id }}" required autocomplete="user_id">
                       
                        <input type="hidden" id="total_mercancia_credit" name="total_mercancia_credit" value="{{$total_mercancia ?? 0 / ($bcv ?? 1)}}" readonly>
                        <input type="hidden" id="total_servicios_credit" name="total_servicios_credit" value="{{$total_servicios ?? 0 / ($bcv ?? 1)}}" readonly>

                        <div class="form-group row">
                            <label for="date-begin" class="col-md-2 col-form-label text-md-right">Fecha:</label>
                            <div class="col-md-3">
                                <input id="date-begin" type="date" class="form-control @error('date-begin') is-invalid @enderror" name="date-begin" value="{{ $creditnote->date_billing ?? $creditnote->date_delivery_note ?? $datenow }}" autocomplete="date-begin">
    
                                @error('date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="date-payment" class="col-md-3 col-form-label text-md-right">Fecha del Pago:</label>
                            <div class="col-md-3">
                                <input id="date-payment" type="date" class="form-control @error('date-payment') is-invalid @enderror" name="date-payment" value="{{ $datenow }}" autocomplete="date-payment">
    
                                @error('date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="cedula_rif" class="col-md-2 col-form-label text-md-right">CI/Rif Cliente:</label>
                            <div class="col-md-4">
                                <input id="cedula_rif" type="text" class="form-control @error('cedula_rif') is-invalid @enderror" name="cedula_rif" value="{{ $creditnote->clients['cedula_rif']  ?? $creditnote->quotations->clients['cedula_rif'] ?? '' }}" readonly required autocomplete="cedula_rif">

                                @error('cedula_rif')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="serie" class="col-md-2 col-form-label text-md-right">N° de Control/Serie:</label>
                            <div class="col-md-3">
                                <input id="serie" type="text" class="form-control @error('serie') is-invalid @enderror" name="serie" value="{{ $creditnote->quotations['serie'] ?? '' }}" readonly required autocomplete="serie">
                                @error('serie')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            
                        </div>

                        <div class="form-group row">
                            <label for="total_factura" class="col-md-2 col-form-label text-md-right">Total Factura:</label>
                            <div class="col-md-4">
                                <input id="total_factura" type="text" class="form-control @error('total_factura') is-invalid @enderror" name="total_factura" value="{{ number_format($creditnote->total_factura / ($bcv ?? 1) , 2, ',', '.') ?? 0 }}" readonly required autocomplete="total_factura">
    
                                @error('total_factura')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="base_imponible" class="col-md-2 col-form-label text-md-right">Base Imponible:</label>
                            <div class="col-md-3">
                                <input id="base_imponible" type="text" class="form-control @error('base_imponible') is-invalid @enderror" name="base_imponible" value="{{ number_format($creditnote->base_imponible / ($bcv ?? 1) , 2, ',', '.') ?? 0 }}" readonly required autocomplete="base_imponible">
                                @error('base_imponible')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div hidden class="form-group row">
                            <label for="porc_retencion_iva" class="col-md-4 col-form-label text-md-right">Porcentaje Retención Iva:</label>
                            <div class="col-md-2">
                                <input id="porc_retencion_iva" type="text" class="form-control @error('porc_retencion_iva') is-invalid @enderror" value="{{ $client->percentage_retencion_iva ?? 0 }}" readonly name="porc_retencion_iva" autocomplete="porc_retencion_iva">
    
                                @error('porc_retencion_iva')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="porc_retencion_islr" class="col-md-3 col-form-label text-md-right">Porcentaje Retención ISLR:</label>
                            <div class="col-md-2">
                                <input id="porc_retencion_islr" type="text" class="form-control @error('porc_retencion_islr') is-invalid @enderror" value="{{ $client->percentage_retencion_islr ?? 0 }}" readonly name="porc_retencion_islr"  autocomplete="porc_retencion_islr">
                                @error('porc_retencion_islr')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="iva_amount" class="col-md-2 col-form-label text-md-right">Monto de Iva</label>
                            <div class="col-md-4">
                                <input id="iva_amount" type="text" class="form-control @error('iva_amount') is-invalid @enderror" name="iva_amount"  readonly required autocomplete="iva_amount"> 
                                
                                @error('iva_amount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label hidden for="iva_retencion" class="col-md-2 col-form-label text-md-right">Retencion IVA:</label>

                            <div hidden class="col-md-3">
                                <input id="iva_retencion" type="text" class="form-control @error('iva_retencion') is-invalid @enderror" name="iva_retencion" readonly required autocomplete="iva_retencion">

                                @error('iva_retencion')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="grand_totals" class="col-md-2 col-form-label text-md-right">Total General</label>
                            <div class="col-md-4">
                                <input id="grand_total" type="text" class="form-control @error('grand_total') is-invalid @enderror" name="grand_total"  readonly required autocomplete="grand_total"> 
                           
                                @error('grand_total')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                           
                            <label for="iva" class="col-md-1 col-form-label text-md-right">IVA:</label>
                            <div class="col-md-2">
                                <select class="form-control" name="iva" id="iva">
                                    @if(isset($creditnote->iva_percentage))
                                        <option value="{{ $creditnote->iva_percentage }}">{{ $creditnote->iva_percentage }}%</option>
                                    @else
                                        <option value="16">16%</option>
                                        <option value="12">12%</option>
                                    @endif
                                    
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <select class="form-control" name="coin" id="coin">
                                    <option value="bolivares">Bolívares</option>
                                    @if($coin == 'dolares')
                                        <option selected value="dolares">Dolares</option>
                                    @else 
                                        <option value="dolares">Dolares</option>
                                    @endif
                                </select>
                            </div>
                            
                        </div>
                        
                        
                        <div hidden class="form-group row">

                            <label for="anticipo" class="col-md-2 col-form-label text-md-right">Menos Anticipo:</label>
                            @if (empty($anticipos_sum))
                                <div class="col-md-3">
                                    <input id="anticipo" type="text" class="form-control @error('anticipo') is-invalid @enderror" name="anticipo" placeholder="0,00"  value="0,00" readonly required autocomplete="anticipo"> 
                            
                                    @error('anticipo')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            @else
                                <div class="col-md-3">
                                    <input id="anticipo" type="text" class="form-control @error('anticipo') is-invalid @enderror" name="anticipo" value="{{ number_format($anticipos_sum ?? 0, 2, ',', '.') ?? 0.00 }}" readonly required autocomplete="anticipo"> 
                            
                                    @error('anticipo')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            @endif
                            <div class="col-md-1">
                                <a href="{{ route('anticipos.selectanticipo',[$creditnote->id_client ?? $creditnote->quotations['id_client'],$coin,$creditnote->id_quotation]) }}" title="Productos"><i class="fa fa-eye"></i></a>  
                            </div>
                            <label hidden for="islr_retencion" class="col-md-2 col-form-label text-md-right">Retencion ISLR:</label>

                            <div hidden class="col-md-3">
                                <input id="islr_retencion" type="text" class="form-control @error('islr_retencion') is-invalid @enderror" name="islr_retencion" value="{{ number_format($total_retiene_islr / ($bcv ?? 1), 2, ',', '.') }}" readonly required autocomplete="islr_retencion">

                                @error('islr_retencion')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                     
                        <input type="hidden" name="id_creditnote" value="{{$creditnote->id}}" readonly>

                        <div class="form-group row">
                            <label for="total_pays" class="col-md-2 col-form-label text-md-right">Total de la Nota de Dédito</label>
                            <div class="col-md-4">
                                <input id="total_pay" type="text" class="form-control @error('total_pay') is-invalid @enderror" name="total_pay" readonly  required autocomplete="total_pay"> 
                           
                                @error('total_pay')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <br>
                      
            <form method="POST" action="{{ route('debitnotes.storefactura') }}" enctype="multipart/form-data">
                @csrf   

                        <input type="hidden" name="id_creditnote" value="{{$creditnote->id}}" readonly>

                        <input type="hidden" id="date-begin-form" name="date-begin-form" value="{{$creditnote->date_billing ?? $creditnote->date_delivery_note ?? $datenow}}" readonly>

                        <input type="hidden" id="date-payment-form" name="date-payment-form" value="{{$datenow}}" readonly>

                        <input type="hidden" name="coin" value="{{$coin}}" readonly>

                        <!--Precio de costo de todos los productos-->
                        <input type="hidden" name="price_cost_total" value="{{$price_cost_total}}" readonly>

                        <!--CANTIDAD DE PAGOS QUE QUIERO ENVIAR-->
                        <input type="hidden" id="amount_of_payments" name="amount_of_payments"  readonly>

                        
                        <!--Total del pago que se va a realizar-->
                        <input type="hidden" id="base_imponible_form" name="base_imponible_form"  readonly>

                        <!--Total del pago que se va a realizar-->
                        <input type="hidden" id="sub_total_form" name="sub_total_form" value="{{ $creditnote->total_factura / ($bcv ?? 1)}}" readonly>
                        
                        <!--Total de la factura sin restarle nada que se va a realizar-->
                        <input type="hidden" id="grandtotal_form" name="grandtotal_form"  readonly>
                        
                        <!--Total del pago que se va a realizar-->
                        <input type="hidden" id="total_pay_form" name="total_pay_form"  readonly>

                      

                        <!--Porcentaje de iva aplicado que se va a realizar-->
                        <input type="hidden" id="iva_form" name="iva_form"  readonly>
                        <input type="hidden" id="iva_amount_form" name="iva_amount_form"  readonly>

                        <!--Anticipo aplicado que se va a realizar-->
                        <input type="hidden" id="anticipo_form" name="anticipo_form"  readonly>

                        <input id="user_id" type="hidden" class="form-control @error('user_id') is-invalid @enderror" name="user_id" value="{{ Auth::user()->id }}" required autocomplete="user_id">
                        
                        <input type="hidden" id="total_retiene_iva" name="total_retiene_iva"  readonly>
                        <input type="hidden" id="total_retiene_islr" name="total_retiene_islr" value="{{$total_retiene_islr / ($bcv ?? 1)}}" readonly>

                        <input type="hidden" id="total_mercancia" name="total_mercancia" value="{{$total_mercancia ?? 0 / ($bcv ?? 1)}}" readonly>
                        <input type="hidden" id="total_servicios" name="total_servicios" value="{{$total_servicios ?? 0 / ($bcv ?? 1)}}" readonly>

                        <br>
                        <div class="form-group row" id="enviarpagos">
                            <div class="col-md-2">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">
                                    Guardar Nota de Débito
                                 </button>
                            </div>
                            
                            <div class="col-md-2">
                            @if(isset($creditnote->date_delivery_note))
                                 <a href="{{ route('debitnotes.indexdeliverynote') }}" id="btnfacturar" name="btnfacturar" class="btn btn-danger" title="facturar">Volver</a>  
                            @else
                                @if (isset($is_after) && ($is_after == false))
                                    <a href="{{ route('invoices') }}" id="btnfacturar" name="btnfacturar" class="btn btn-danger" title="facturar">Volver</a>                             
                                @else
                                    <a href="{{ route('debitnotes.create',[$creditnote->id,$coin]) }}" id="btnfacturar" name="btnfacturar" class="btn btn-danger" title="facturar">Volver</a>  
                                @endif
                            @endif
                             </div>
                        </div>
                        
                    </form>    
                </div>
            </div>
        </div>
</div>
@endsection

@section('creditnote_facturar')
    <script src="{{asset('js/facturar.js')}}"></script> 
@endsection   


@section('consulta')
    <script>
        $("#credit").hide();
        $("#formenviarcredito").hide();
        var switchStatus = false;
        $("#customSwitches").on('change', function() {
            if ($(this).is(':checked')) {
                switchStatus = $(this).is(':checked');
                $("#credit").show();
                $("#formulario1").hide();
                $("#formulario2").hide();
                $("#formulario3").hide();
                $("#formulario4").hide();
                $("#formulario5").hide();
                $("#formulario6").hide();
                $("#formulario7").hide();
                $("#formenviarcredito").show();
                $("#enviarpagos").hide();
                number_form = 1; 
            }
            else {
            switchStatus = $(this).is(':checked');
                $("#credit").hide();
                $("#formulario1").show();
                $("#formenviarcredito").hide();
                $("#enviarpagos").show();
            }
        });


        $(document).ready(function () {
            $("#credit").mask('0000', { reverse: true });
            
        });
        $("#coin").on('change',function(){
            coin = $(this).val();
            window.location = "{{route('debitnotes.createfacturar', [$creditnote->id,''])}}"+"/"+coin;
        });

        $("#date-begin").on('change',function(){
            document.getElementById("date-begin-form").value = $(this).val();
            
        });

        $("#date-payment").on('change',function(){
            document.getElementById("date-payment-form").value = $(this).val();
            
        });

        
    </script>
    <script type="text/javascript">

            calculate();

            function calculate() {
                let inputIva = document.getElementById("iva").value; 

                //let totalIva = (inputIva * "<?php echo $creditnote->total_factura; ?>") / 100;  

                let totalFactura = "<?php echo $creditnote->total_factura  / ($bcv ?? 1) ?>";       

                //AQUI VAMOS A SACAR EL MONTO DEL IVA DE LOS QUE ESTAN EXENTOS, PARA LUEGO RESTARSELO AL IVA TOTAL
                let totalBaseImponible = "<?php echo $creditnote->base_imponible  / ($bcv ?? 1) ?>";

                let totalIvaMenos = (inputIva * "<?php echo $creditnote->base_imponible  / ($bcv ?? 1) ; ?>") / 100;  




                /*Toma la Base y la envia por form*/
                let base_imponible_form = document.getElementById("base_imponible").value; 

                var montoFormat = base_imponible_form.replace(/[$.]/g,'');

                var montoFormat_base_imponible_form = montoFormat.replace(/[,]/g,'.');    

                document.getElementById("base_imponible_form").value =  montoFormat_base_imponible_form;
                /*-----------------------------------*/
                /*Toma la Base y la envia por form*/
                let sub_total_form = document.getElementById("total_factura").value; 

                var montoFormat = sub_total_form.replace(/[$.]/g,'');

                var montoFormat_sub_total_form = montoFormat.replace(/[,]/g,'.');    

                //document.getElementById("sub_total_form").value =  montoFormat_sub_total_form;
                /*-----------------------------------*/

                var total_iva_exento =  parseFloat(totalIvaMenos);

                var iva_format = total_iva_exento.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});
               

                //document.getElementById("retencion").value = parseFloat(totalIvaMenos);
                //------------------------------

                document.getElementById("iva_amount").value = iva_format;


                var numbertotalfactura = parseFloat(totalFactura).toFixed(2);
                var numbertotal_iva_exento = parseFloat(total_iva_exento).toFixed(2);
                
                // var grand_total = parseFloat(totalFactura) + parseFloat(totalIva);
                var grand_total = parseFloat(numbertotalfactura) + parseFloat(numbertotal_iva_exento) ;
                

                var grand_totalformat = grand_total.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});
                


                document.getElementById("grand_total").value = grand_totalformat;


                let inputAnticipo = document.getElementById("anticipo").value;  

                var montoFormat = inputAnticipo.replace(/[$.]/g,'');

                var montoFormat_anticipo = montoFormat.replace(/[,]/g,'.');

                if(inputAnticipo){
                    
                    document.getElementById("anticipo_form").value =  montoFormat_anticipo;
                }else{
                    document.getElementById("anticipo_form").value = 0;
                }


                var total_pay = parseFloat(totalFactura) + total_iva_exento;

               
                //retencion de iva

                let porc_retencion_iva = "<?php echo $client->percentage_retencion_iva ?>";
                var calc_retencion_iva = total_iva_exento * porc_retencion_iva / 100;
                var total_retencion_iva = calc_retencion_iva.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});
            
                document.getElementById("iva_retencion").value =  total_retencion_iva;
                    
                document.getElementById("total_retiene_iva").value =  calc_retencion_iva;
                
                //-----------------------

                //retencion de islr
                    var total_islr_retencion = document.getElementById("total_retiene_islr").value;
                //------------------------------------


                var total_payformat = total_pay.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});
                
                document.getElementById("total_pay").value =  total_payformat;

                document.getElementById("total_pay_form").value =  total_pay.toFixed(2);

                document.getElementById("iva_form").value =  inputIva;

                document.getElementById("iva_amount_form").value = document.getElementById("iva_amount").value;
               
                document.getElementById("grandtotal_form").value = grand_totalformat;
                

                //Quiere decir que el monto total a pagar es negativo o igual a cero
                if(total_pay.toFixed(2) <= 0){
                    document.getElementById("amount_pay").required = false;
                    document.getElementById("payment_type").required = false;
                    $("#amount_pay").hide();
                    $("#payment_type").hide();
                    $("#btn_agregar").hide();
                    $("#label_amount_pays").hide();
                }
            }        
                
              
       
            $("#iva").on('change',function(){
                //calculate();


                let inputIva = document.getElementById("iva").value; 

                //let totalIva = (inputIva * "<?php echo $creditnote->total_factura; ?>") / 100;  

                let totalFactura = "<?php echo $creditnote->total_factura  / ($bcv ?? 1) ?>";       

                //AQUI VAMOS A SACAR EL MONTO DEL IVA DE LOS QUE ESTAN EXENTOS, PARA LUEGO RESTARSELO AL IVA TOTAL
                let totalBaseImponible = "<?php echo $creditnote->base_imponible  / ($bcv ?? 1) ?>";

                let totalIvaMenos = (inputIva * "<?php echo $creditnote->base_imponible  / ($bcv ?? 1) ; ?>") / 100;  


                /*Toma la Base y la envia por form*/
                let base_imponible_form = document.getElementById("base_imponible").value; 

                var montoFormat = base_imponible_form.replace(/[$.]/g,'');

                var montoFormat_base_imponible_form = montoFormat.replace(/[,]/g,'.');    

                document.getElementById("base_imponible_form").value =  montoFormat_base_imponible_form;
                /*-----------------------------------*/
                /*Toma la Base y la envia por form*/
                let sub_total_form = document.getElementById("total_factura").value; 

                var montoFormat = sub_total_form.replace(/[$.]/g,'');

                var montoFormat_sub_total_form = montoFormat.replace(/[,]/g,'.');    

                //document.getElementById("sub_total_form").value =  montoFormat_sub_total_form;
                /*-----------------------------------*/


                var total_iva_exento =  parseFloat(totalIvaMenos);

                var iva_format = total_iva_exento.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});
               
                //document.getElementById("retencion").value = parseFloat(totalIvaMenos);
                //------------------------------



                document.getElementById("iva_amount").value = iva_format;


                // var grand_total = parseFloat(totalFactura) + parseFloat(totalIva);
                var grand_total = parseFloat(totalFactura) + parseFloat(total_iva_exento);

                var grand_totalformat = grand_total.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});

                document.getElementById("grand_total").value = grand_totalformat;



                let inputAnticipo = document.getElementById("anticipo").value;  

                var montoFormat = inputAnticipo.replace(/[$.]/g,'');

                var montoFormat_anticipo = montoFormat.replace(/[,]/g,'.');

                if(inputAnticipo){
                    
                    document.getElementById("anticipo_form").value =  montoFormat_anticipo;
                }else{
                    document.getElementById("anticipo_form").value = 0;
                }        

                var total_pay = parseFloat(totalFactura) + total_iva_exento;

                // var total_pay = parseFloat(totalFactura) + total_iva_exento - inputAnticipo;

                //retencion de iva
                
                let porc_retencion_iva = "<?php echo $client->percentage_retencion_iva ?>";
                var calc_retencion_iva = total_iva_exento * porc_retencion_iva / 100;
                var total_retencion_iva = calc_retencion_iva.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});
            
                document.getElementById("iva_retencion").value =  total_retencion_iva;
                    
                document.getElementById("total_retiene_iva").value =  calc_retencion_iva;
                
                //-----------------------

                //retencion de islr
                    var total_islr_retencion = document.getElementById("total_retiene_islr").value;
                //------------------------------------

                var total_pay = total_pay;
                var total_payformat = total_pay.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});

                document.getElementById("total_pay").value =  total_payformat;

                document.getElementById("total_pay_form").value =  total_pay.toFixed(2);

                document.getElementById("iva_form").value =  inputIva;
              
                document.getElementById("iva_amount_form").value = document.getElementById("iva_amount").value;

                document.getElementById("grandtotal_form").value = grand_totalformat;

                 //Quiere decir que el monto total a pagar es negativo o igual a cero
                 if(total_pay.toFixed(2) <= 0){
                    document.getElementById("amount_pay").required = false;
                    document.getElementById("payment_type").required = false;
                    $("#amount_pay").hide();
                    $("#payment_type").hide();
                    $("#btn_agregar").hide();
                    $("#label_amount_pays").hide();
                }
               
            });

            $("#anticipo").on('keyup',function(){
                //calculate();



                let inputIva = document.getElementById("iva").value; 

                //let totalIva = (inputIva * "<?php echo $creditnote->total_factura; ?>") / 100;  

                let totalFactura = "<?php echo $creditnote->total_factura ?>";       

                //AQUI VAMOS A SACAR EL MONTO DEL IVA DE LOS QUE ESTAN EXENTOS, PARA LUEGO RESTARSELO AL IVA TOTAL
                let totalBaseImponible = "<?php echo $creditnote->base_imponible ?>";

                let totalIvaMenos = (inputIva * "<?php echo $creditnote->base_imponible; ?>") / 100;  


                /*Toma la Base y la envia por form*/
                let base_imponible_form = document.getElementById("base_imponible").value; 

                var montoFormat = base_imponible_form.replace(/[$.]/g,'');

                var montoFormat_base_imponible_form = montoFormat.replace(/[,]/g,'.');    

                document.getElementById("base_imponible_form").value =  montoFormat_base_imponible_form;
                /*-----------------------------------*/
                /*Toma la Base y la envia por form*/
                let sub_total_form = document.getElementById("total_factura").value; 

                var montoFormat = sub_total_form.replace(/[$.]/g,'');

                var montoFormat_sub_total_form = montoFormat.replace(/[,]/g,'.');    

                //document.getElementById("sub_total_form").value =  montoFormat_sub_total_form;
                /*-----------------------------------*/





                var total_iva_exento =  parseFloat(totalIvaMenos);

                var iva_format = total_iva_exento.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});

                //document.getElementById("retencion").value = parseFloat(totalIvaMenos);
                //------------------------------



                document.getElementById("iva_amount").value = iva_format;


                // var grand_total = parseFloat(totalFactura) + parseFloat(totalIva);
                var grand_total = parseFloat(totalFactura) + parseFloat(total_iva_exento);

                var grand_totalformat = grand_total.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});


                document.getElementById("grand_total").value = grand_totalformat;



                let inputAnticipo = document.getElementById("anticipo").value;  

                var montoFormat = inputAnticipo.replace(/[$.]/g,'');

                var montoFormat_anticipo = montoFormat.replace(/[,]/g,'.');

                if(inputAnticipo){
                    
                    document.getElementById("anticipo_form").value =  montoFormat_anticipo;
                }else{
                    document.getElementById("anticipo_form").value = 0;
                }


                var total_pay = parseFloat(totalFactura) + total_iva_exento;

               //retencion de iva
                
               let porc_retencion_iva = "<?php echo $client->percentage_retencion_iva ?>";
                var calc_retencion_iva = total_iva_exento * porc_retencion_iva / 100;
                var total_retencion_iva = calc_retencion_iva.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});
            
                document.getElementById("iva_retencion").value =  total_retencion_iva;
                    
                document.getElementById("total_retiene_iva").value =  calc_retencion_iva;
                
                //-----------------------

                //retencion de islr
                    var total_islr_retencion = document.getElementById("total_retiene_islr").value;
                //------------------------------------

                var total_pay = total_pay;

                var total_payformat = total_pay.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});

                document.getElementById("total_pay").value =  total_payformat;

                document.getElementById("total_pay_form").value =  total_pay.toFixed(2);

                document.getElementById("iva_form").value =  inputIva;

                document.getElementById("iva_amount_form").value = document.getElementById("iva_amount").value;
               
                document.getElementById("grandtotal_form").value = grand_totalformat;

                 //Quiere decir que el monto total a pagar es negativo o igual a cero
                 if(total_pay.toFixed(2) <= 0){
                    document.getElementById("amount_pay").required = false;
                    document.getElementById("payment_type").required = false;
                    $("#amount_pay").hide();
                    $("#payment_type").hide();
                    $("#btn_agregar").hide();
                    $("#label_amount_pays").hide();
                }
                
            });

       
    </script>
@endsection
