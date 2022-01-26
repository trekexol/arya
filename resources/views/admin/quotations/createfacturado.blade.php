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
                <div class="card-header" >Facturar</div>
                
                <div class="card-body" >
                        
                    <div class="form-group row">
                        <label for="total_factura" class="col-md-2 col-form-label text-md-right">Nº Factura:</label>
                        <div class="col-md-4">
                            <input id="num_factura" type="text" class="form-control @error('total_factura') is-invalid @enderror" name="num_factura" value="{{ $quotation->number_invoice}}" readonly>
                        </div>

                        <label for="date_quotation" class="col-md-2 col-form-label text-md-right">CI/Rif: </label>
                        <div class="col-md-3">
                            <input id="date_quotation" type="text" class="form-control @error('date_quotation') is-invalid @enderror" name="date_quotation" value="{{ $quotation->clients['cedula_rif']  ?? '' }}" readonly required autocomplete="date_quotation">
    
                            @error('date_quotation')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>  
                    </div>
                    
              
                    
                    <div class="form-group row">
                            <label for="date_quotation" class="col-md-2 col-form-label text-md-right">Cliente: </label>
                            <div class="col-md-4">
                                <input id="name_cliente" type="text" class="form-control @error('date_quotation') is-invalid @enderror" name="name_cliente" value="{{ $quotation->clients['name']  ?? '' }}" readonly>

                                @error('date_quotation')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="client" class="col-md-2 col-form-label text-md-right">N° de Control/Serie:</label>
                            <div class="col-md-3">
                                <input id="client" type="text" class="form-control @error('client') is-invalid @enderror" name="client" value="{{ $quotation->serie ?? '' }}" readonly required autocomplete="client">
                                @error('client')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            
                        </div>
                        <div class="form-group row">
                            <label for="total_factura" class="col-md-2 col-form-label text-md-right">Total Factura:</label>
                            <div class="col-md-4">
                                <input id="total_factura" type="text" class="form-control @error('total_factura') is-invalid @enderror" name="total_factura" value="{{ number_format($quotation->amount / ($bcv ?? 1), 2, ',', '.') ?? 0 }}" readonly required autocomplete="total_factura">
    
                                @error('total_factura')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="base_imponible" class="col-md-2 col-form-label text-md-right">Base Imponible:</label>
                            <div class="col-md-3">
                                <input id="base_imponible" type="text" class="form-control @error('base_imponible') is-invalid @enderror" name="base_imponible" value="{{ number_format($quotation->base_imponible / ($bcv ?? 1), 2, ',', '.') ?? 0 }}" readonly required autocomplete="base_imponible">
                                @error('base_imponible')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="iva_amounts" class="col-md-2 col-form-label text-md-right">Monto de Iva:</label>
                            <div class="col-md-4">
                                <input id="iva_amounts" type="text" class="form-control @error('iva_amount') is-invalid @enderror" name="iva_amount" value="{{ number_format($quotation->amount_iva / ($bcv ?? 1), 2, ',', '.') ?? 0 }}"  readonly required autocomplete="iva_amount"> 
                                
                                @error('iva_amount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="observation" class="col-md-2 col-form-label text-md-right">Retencion IVA:</label>

                            <div class="col-md-3">
                                <input id="observation" type="text" class="form-control @error('observation') is-invalid @enderror" name="observation" value="{{ number_format(bcdiv(($quotation->retencion_iva), '1', 2)/ ($bcv ?? 1), 2, ',', '.') ?? 0 }}" readonly required autocomplete="observation">

                                @error('observation')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="grand_totals" class="col-md-2 col-form-label text-md-right">Total General:</label>
                            <div class="col-md-4">
                                <input id="grand_total" type="text" class="form-control @error('grand_total') is-invalid @enderror" name="grand_total" value="{{ number_format( ($quotation->amount + $quotation->amount_iva) / ($bcv ?? 1), 2, ',', '.') }}" readonly required autocomplete="grand_total"> 
                           
                                @error('grand_total')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="note" class="col-md-2 col-form-label text-md-right">Retencion ISLR:</label>

                            <div class="col-md-3">
                                <input id="note" type="text" class="form-control @error('note') is-invalid @enderror" name="note" value="{{ number_format($quotation->retencion_islr / ($bcv ?? 1), 2, ',', '.') ?? 0 }}" readonly required autocomplete="note">

                                @error('note')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        
                        <div class="form-group row">
                            <label for="anticipo" class="col-md-2 col-form-label text-md-right">Menos Anticipo:</label>
                            <div class="col-md-4">
                                <input id="anticipo" type="text" class="form-control @error('anticipo') is-invalid @enderror" name="anticipo" value="{{ number_format($quotation->anticipo / ($bcv ?? 1), 2, ',', '.') }}" readonly required autocomplete="anticipo"> 
                           
                                @error('anticipo')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="iva" class="col-md-2 col-form-label text-md-right">IVA:</label>
                            <div class="col-md-2">
                            <select class="form-control" name="iva" id="iva">
                                <option value="{{ $quotation->iva_percentage }}">{{ $quotation->iva_percentage }}%</option>
                            </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="total_pays" class="col-md-2 col-form-label text-md-right">Total a Pagar:</label>
                            <div class="col-md-4">
                                <input id="total_pay" type="text" class="form-control @error('total_pay') is-invalid @enderror" name="total_pay" readonly value="{{ number_format(($quotation->amount_with_iva / ($bcv ?? 1)) - ($quotation->anticipo / ($bcv ?? 1)) - ($quotation->retencion_iva / ($bcv ?? 1)) - ($quotation->retencion_islr / ($bcv ?? 1)), 2, ',', '.') ?? '0,00' }}"  required autocomplete="total_pay"> 
                           
                                @error('total_pay')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label id="coinlabel" for="coin" class="col-md-1 col-form-label text-md-right">Tasa:</label> 
                            <div class="col-md-2">
                                <input id="monto_taza" type="text" class="form-control" name="monto_taza" value="{{ number_format( $quotation->bcv ?? 1, 2,',', '.') }}" readonly> <!--By dacson-->
                            </div>                              
                            @if (isset($quotation->credit_days))
                                <label for="total_pays" class="col-md-2 col-form-label text-md-right">Dias de Crédito:</label>
                                <div class="col-md-1">
                                    <input id="credit" type="text" class="form-control @error('credit') is-invalid @enderror" name="credit" value="{{ $quotation->credit_days ?? '' }}" readonly autocomplete="credit"> 
                                </div>
                            @endif
                            
                        </div>
                        <div class="form-group row">
                            <label id="coinlabel" for="coin" class="col-md-2 col-form-label text-md-right">Moneda:</label>

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

                        <br>
                        <div class="form-group row">
                           
                            <div class="col-md-3">
                                <a onclick="pdf();" id="btnimprimir" name="btnimprimir" class="btn btn-info" title="imprimir">Imprimir Factura</a>  
                            </div>
                            
                            <div class="col-sm-3  dropdown mb-4">
                                <button class="btn btn-success" type="button"
                                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="false"
                                    aria-expanded="false">
                                    <i class="fas fa-bars"></i>
                                    Opciones
                                </button>
                                <div class="dropdown-menu animated--fade-in"
                                    aria-labelledby="dropdownMenuButton">
                                    <a href="#" onclick="pdf_media();" id="btnfacturar" name="btnfacturar" class="dropdown-item bg-light" title="imprimir">Imprimir Factura Media Carta</a>  
                                    <a href="#" onclick="pdf_maq();" id="btnfacturarmaq" name="btnfacturarmaq" class="dropdown-item bg-light" title="imprimir">Imprimir Factura Matricial Carta</a> 

                                    <a href="#" class="dropdown-item bg-light delete" data-id-quotation={{$quotation->id}} data-toggle="modal" data-target="#reversarModal" title="Eliminar">Reversar Factura</a> 
                                </div>
                            </div> 
                           
                            <div class="col-md-3">
                                <a href="{{ route('invoices.movement',[$quotation->id,$coin]) }}" id="btnmovement" name="btnmovement" class="btn btn-light" title="movement">Ver Movimiento de Cuenta</a>  
                            </div>
                           
                            <div class="col-md-2">
                                <a href="{{ route('invoices') }}" id="btnfacturar" name="btnfacturar" class="btn btn-danger" title="facturar">Ver Facturas</a>  
                            </div>
                        </div>
                        
                    </form>    
                </div>
            </div>
        </div>
</div>
<!-- Delete Warning Modal -->
<div class="modal modal-danger fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Eliminar Multipago</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
           
                <h5 class="text-center">Esta factura fue pagada con multipago, al reversarla, reversará todas las facturas 
                    realizadas en el multipago.</h5>
                <h5 class="text-center">Seguro quiere reversar todas las facturas?</h5>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <a href="{{ route('quotations.reversar_quotation_multipayment',[$quotation->id,$reverso ?? null]) }}" type="submit" class="btn btn-danger">Eliminar</a>
            </div>
            
        </div>
    </div>
</div>

<!-- Delete Warning Modal -->
<div class="modal modal-danger fade" id="reversarModal" tabindex="-1" role="dialog" aria-labelledby="reversar" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Eliminar Factura</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('quotations.reversarQuotation') }}" method="post">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <h5 class="text-center">Seguro quiere reversar la factura?</h5>
                    <input id="id_quotation_modal" type="hidden" class="form-control @error('id_quotation_modal') is-invalid @enderror" name="id_quotation_modal" readonly required autocomplete="id_quotation_modal">
                    
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



@section('consulta')
    @if (isset($reverso))
        <script>
            $( document ).ready(function() {
                $('#deleteModal').modal('toggle')
            });


        </script>
    @endif

    <script type="text/javascript">

            $("#coin").on('change',function(){
                coin = $(this).val();
                window.location = "{{route('quotations.createfacturado', [$quotation->id,''])}}"+"/"+coin;
            });

            $(document).on('click','.delete',function(){
                let id_quotation = $(this).attr('data-id-quotation');

                $('#id_quotation_modal').val(id_quotation);
            });
            function pdf() {
                
                var nuevaVentana= window.open("{{ route('pdf',[$quotation->id,$coin])}}","ventana","left=800,top=800,height=800,width=1000,scrollbar=si,location=no ,resizable=si,menubar=no");
        
            }
            function pdf_media() {
                
                var nuevaVentana2= window.open("{{ route('pdf.media',[$quotation->id,$coin])}}","ventana","left=800,top=800,height=800,width=1000,scrollbar=si,location=no ,resizable=si,menubar=no");
        
            }
            function pdf_maq() {
                
                var nuevaVentana3= window.open("{{ route('pdf.maq',[$quotation->id,$coin])}}","ventana","left=800,top=800,height=800,width=1000,scrollbar=si,location=no ,resizable=si,menubar=no");
        
            }          
               // calculate();
                
            function calculate() {
                let inputIva = document.getElementById("iva").value; 

                //let totalIva = (inputIva * "<?php echo $quotation->total_factura; ?>") / 100;  

                let totalFactura = "<?php echo $quotation->total_factura / ($bcv ?? 1) ?>";       

                //AQUI VAMOS A SACAR EL MONTO DEL IVA DE LOS QUE ESTAN EXENTOS, PARA LUEGO RESTARSELO AL IVA TOTAL
                let totalBaseImponible = "<?php echo $quotation->base_imponible / ($bcv ?? 1) ?>";

                let totalIvaMenos = (inputIva * "<?php echo $quotation->base_imponible / ($bcv ?? 1); ?>") / 100;  

               


                var total_iva_exento =  parseFloat(totalIvaMenos);

                var iva_format = total_iva_exento.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});

                //document.getElementById("retencion").value = parseFloat(totalIvaMenos);
                //------------------------------

               

                document.getElementById("iva_amounts").value = iva_format;


                // var grand_total = parseFloat(totalFactura) + parseFloat(totalIva);
                var grand_total = parseFloat(totalFactura) + parseFloat(total_iva_exento);

                var grand_totalformat = grand_total.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});


                document.getElementById("grand_total").value = grand_totalformat;

                let inputAnticipo = document.getElementById("anticipo").value;  

                var montoFormat = inputAnticipo.replace(/[$.]/g,'');

                var montoFormat_anticipo = montoFormat.replace(/[,]/g,'.');             

                var total_pay = parseFloat(totalFactura) + total_iva_exento - montoFormat_anticipo;

               

                var total_payformat = total_pay.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});

                document.getElementById("total_pay").value =  total_payformat;

                document.getElementById("total_pay_form").value =  total_pay.toFixed(2);

                document.getElementById("iva_form").value =  inputIva;

                document.getElementById("anticipo_form").value =  inputAnticipo;
            }        

       
    </script>
@endsection
