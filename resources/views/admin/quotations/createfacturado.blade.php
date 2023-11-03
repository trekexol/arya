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
                <div class="card-header" ><h3>Registrar {{$type ?? ''}} / Cobrar</h3></div>

                <form method="POST" action="{{ route('quotations.storefacturacredit') }}" enctype="multipart/form-data">
                    @csrf
                <div class="card-body" >

                        <input type="hidden" name="coin" value="{{$coin}}" readonly>
                        <input type="hidden" id="date-begin-form" name="date-begin-form" value="{{$quotation->date_billing ?? $quotation->date_delivery_note ?? $datenow}}" readonly>

                        <!--Precio de costo de todos los productos-->
                        <input type="hidden" name="price_cost_total" value="{{$price_cost_total}}" readonly>
                        <input id="user_id" type="hidden" class="form-control @error('user_id') is-invalid @enderror" name="user_id" value="{{ Auth::user()->id }}" required autocomplete="user_id">

                        <input type="hidden" id="total_mercancia_credit" name="total_mercancia_credit" value="{{$total_mercancia ?? 0 }}" readonly>
                        <input type="hidden" id="total_servicios_credit" name="total_servicios_credit" value="{{$total_servicios ?? 0 }}" readonly>

                        <input type="hidden" id="grandtotal_form_credit" name="grandtotal_form"  readonly>

                        <input type="hidden" id="IGTF_input_pre_credit" name="IGTF_input_pre">

                        <input type="hidden" id="debitnote_input_pre_credit" name="debitnote_input_pre_credit">
                        <input type="hidden" id="creditnote_input_pre_credit" name="creditnote_input_pre_credit">

                        <div class="form-group row">
                            <label for="date-begin" class="col-md-2 col-form-label text-md-right">Fecha:</label>
                            <div class="col-md-3">
                                <input id="date-begin" type="date" class="form-control @error('date-begin') is-invalid @enderror" name="date-begin" value="{{ $quotation->date_billing ?? $datenow }}" autocomplete="date-begin">

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

                            @isset($quotation->number_invoice)
                            <label for="number_fact" class="col-md-2 col-form-label text-md-right">Factura:</label>
                            <div class="col-md-4">
                                <input id="number_fact" type="text" class="form-control @error('number_fact') is-invalid @enderror" name="number_fact" value="{{ $quotation->number_invoice ?? '' }}" readonly autocomplete="number_fact">

                                @error('number_fact')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            @endisset
                            @if($quotation->number_invoice == null)
                            <label for="number_fact" class="col-md-2 col-form-label text-md-right">Cotización:</label>
                            <div class="col-md-4">
                                <input id="id_quotation" type="text" class="form-control @error('id_quotation') is-invalid @enderror" name="id_quotation" value="{{ $quotation->id ?? '' }}" readonly autocomplete="id_quotation">

                                @error('id_quotation')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            @endif

                            <label for="number_delivery_note" class="col-md-2 col-form-label text-md-right">Nota de Entrega:</label>
                            <div class="col-md-3">
                                <input id="number_delivery_note" type="text" class="form-control @error('number_delivery_note') is-invalid @enderror" name="number_delivery_note" value="{{ $quotation->number_delivery_note ?? '' }}" readonly autocomplete="number_delivery_note">
                                @error('number_delivery_note')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                        </div>
                        <div class="form-group row">
                            <label for="cedula_rif" class="col-md-2 col-form-label text-md-right">CI/Rif Cliente:</label>
                            <div class="col-md-4">
                                <input id="cedula_rif" type="text" class="form-control @error('cedula_rif') is-invalid @enderror" name="cedula_rif" value="{{ $quotation->clients['cedula_rif']  ?? '' }}" readonly required autocomplete="cedula_rif">

                                @error('cedula_rif')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="serie" class="col-md-2 col-form-label text-md-right">N° de Control/Serie:</label>
                            <div class="col-md-3">
                                <input id="serie" type="text" class="form-control @error('serie') is-invalid @enderror" name="serie" value="{{ $quotation->serie ?? '' }}" readonly required autocomplete="serie">
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
                                <input id="total_factura" type="text" class="form-control @error('total_factura') is-invalid @enderror" name="total_factura" value="{{ number_format($quotation->total_factura  , 2, ',', '.') ?? 0 }}" readonly required autocomplete="total_factura">

                                @error('total_factura')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="base_imponible" class="col-md-2 col-form-label text-md-right">Base Imponible:</label>
                            <div class="col-md-3">
                                <input id="base_imponible" type="text" class="form-control @error('base_imponible') is-invalid @enderror" name="base_imponible" value="{{ number_format($quotation->base_imponible  , 2, ',', '.') ?? 0 }}" readonly required autocomplete="base_imponible">
                                @error('base_imponible')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
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
                            <label for="iva_retencion" class="col-md-2 col-form-label text-md-right">Retencion IVA:</label>

                            <div class="col-md-3">
                                <input id="iva_retencion" type="text" class="form-control @error('iva_retencion') is-invalid @enderror" name="iva_retencion" value="{{ number_format($total_retiene_islr, 2, ',', '.') }}" readonly required autocomplete="iva_retencion">

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
                            <label for="islr_retencion" class="col-md-2 col-form-label text-md-right">Retencion ISLR:</label>

                            <div class="col-md-3">
                                <input id="islr_retencion" type="text" class="form-control @error('islr_retencion') is-invalid @enderror" name="islr_retencion" value="{{ number_format($total_retiene_islr , 2, ',', '.') }}" readonly required autocomplete="islr_retencion">

                                @error('islr_retencion')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <div class="form-group row">
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
                                <a href="{{ route('anticipos.selectanticipo',[$quotation->id_client,$coin,$quotation->id]) }}" title="Productos"><i class="fa fa-eye"></i></a>
                            </div>
                            <label for="iva" class="col-md-1 col-form-label text-md-right">IVA:</label>
                            <div class="col-md-2">
                                <select class="form-control" name="iva" id="iva">
                                    @if(isset($quotation->iva_percentage))
                                        <option selected value="{{ $quotation->iva_percentage }}">{{ $quotation->iva_percentage }}%</option>
                                    @else
                                        <option value="{{$impuesto}}">{{$impuesto}}%</option>
                                        <option value="{{$impuesto2}}">{{$impuesto2}}%</option>
                                        <option value="{{$impuesto3}}">{{$impuesto3}}%</option>
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



                        @if ($total_credit_notes > 0)
                        <div class="form-group row creditnote" style="display: visible;" >
                        @else
                        <div class="form-group row creditnote" style="display: none;" >
                        @endif

                            <label for="creditnote" class="col-md-2 col-form-label text-md-right">Notas de Crédito:</label>

                            <div class="col-md-3">
                                <input id="creditnote_input" type="text" class="form-control @error('creditnote_input') is-invalid @enderror" name="creditnote_input" value="{{ number_format($total_credit_notes ?? 0, 2, ',', '.') ?? 0}}" readonly>
                            </div>
                        </div>



                        @if ($total_debit_notes > 0)
                        <div class="form-group row debitnote" style="display: visible;" >
                        @else
                        <div class="form-group row debitnote" style="display: none;" >
                        @endif

                            <label for="debitnote" class="col-md-2 col-form-label text-md-right">Notas de Débito:</label>

                            <div class="col-md-3">
                                <input id="debitnote_input" type="text" class="form-control @error('debitnote_input') is-invalid @enderror" name="debitnote_input" value="{{ number_format($total_debit_notes ?? 0, 2, ',', '.') ?? 0}}" readonly>
                            </div>
                        </div>



                        @if ($quotation->IGTF_amount > 0)
                        <div class="form-group row IGTF" style="display: visible;" >
                        @else
                        <div class="form-group row IGTF" style="display: none;" >
                        @endif

                            <label for="igtf" class="col-md-2 col-form-label text-md-right">IGTF:</label>

                            <div class="col-md-3">

                                <input id="IGTF_input" type="text" class="form-control @error('IGTF_input') is-invalid @enderror" name="IGTF_input" value="{{$quotation->IGTF_amount ?? 0}}" readonly>

                            </div>


                        </div>


                        <input type="hidden" name="id_quotation" value="{{$quotation->id}}" readonly>

                        <div class="form-group row">
                            <label for="total_pays" class="col-md-2 col-form-label text-md-right">Total a Cobrar</label>
                            <div class="col-md-2">
                                <input id="total_pay" type="text" class="form-control @error('total_pay') is-invalid @enderror" name="total_pay" readonly  required autocomplete="total_pay">

                                @error('total_pay')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            @if (isset($is_after) && ($is_after == true))
                                <div class="col-md-2">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="customSwitches">
                                        <label class="custom-control-label" for="customSwitches">Tiene Crédito</label>
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-3" id="IGTF_buttom">
                                <div class="custom-control custom-switch">
                                    @if ($quotation->IGTF_amount> 0 )
                                    <input type="checkbox" class="custom-control-input igtftotal" id="customSwitchesIGTFTotal" name="customSwitchesIGTFTotal" checked>
                                    @else
                                    <input type="checkbox" class="custom-control-input igtftotal" id="customSwitchesIGTFTotal" name="customSwitchesIGTFTotal">
                                    @endif
                                    <label class="custom-control-label" for="customSwitchesIGTFTotal">IGTF: 3% (Total General)</label>
                                </div>
                            </div>
                            @if (isset($is_after) && ($is_after == true))

                            <div class="col-md-2">
                                    <input id="credit" type="text" class="form-control @error('credit') is-invalid @enderror" name="credit" placeholder="Dias de Crédito" autocomplete="credit">
                            </div>
                            @endif
                        </div>

                        @if (Auth::user()->company['id']  == '26' AND $existe == FALSE)
                        <div class="form-group row" id="newcour">
                            <label for="court" class="col-md-2 col-form-label text-md-right">Tipo Couriertool:</label>

                            <div class="col-md-2">
                                <select  id="court"  name="court" class="form-control">
                                    <option value="">Seleccionar</option>
                                    <option value="1">PALETA</option>
                                    <option value="2">CONTENEDOR</option>
                                    <option value="3">GUIA MASTER</option>
                                    <option value="4">TULA</option>
                                    <option value="5">GUIA TERRESTRE</option>

                                </select>

                            </div>
                            <label id="tifaclabel" for="tifac" class="col-md-2 col-form-label text-md-right">Tipo Factura:</label>

                            <div class="col-md-2">
                                <select class="form-control" name="tifac" id="tifac">
                                    <option value="">Seleccionar</option>
                                    <option value="1">ADUANA</option>
                                    <option value="2">INTERNACIONAL</option>
                                    <option value="3">SEGURO</option>
                                    <option value="4">PICK UP</option>
                                    <option value="5">MANEJO</option>
                                    <option value="6">IMPUESTOS</option>

                                </select>
                            </div>

                            <label id="tifaclabel" for="tifac" class="col-md-2 col-form-label text-md-right">Nro Couriertool:</label>

                            <div class="col-md-2">
                                <input id="nrofactcou" type="text" class="form-control" name="nrofactcou" value="{{ old('nrofactcou') }}">
                            </div>

                        </div>
                        @endif
                        <br>
                        @if (isset($is_after) && ($is_after == true))
                            <div class="form-group row" id="formenviarcredito">

                                <div class="col-md-2">
                                </div>
                                <div id="divGuardar" class="col-md-3">
                                    <button type="submit" class="btn btn-primary">
                                        Guardar Factura a Crédito
                                    </button>
                                </div>
                                <div class="col-md-2">
                                    <a href="{{ route('quotations.create',[$quotation->id,$coin]) }}" id="btnfacturar" name="btnfacturar" class="btn btn-danger" title="facturar">Volver</a>
                                </div>
                            </div>
                        @endif


            </form>
            <form id="primer_form" method="POST" action="{{ route('quotations.storefactura') }}" enctype="multipart/form-data">
                @csrf

                        <input type="hidden" name="id_quotation" value="{{$quotation->id}}" readonly>
                        <input type="hidden" id="date-begin-form2" name="date-begin-form2" value="{{$quotation->date_billing ?? $quotation->date_delivery_note ?? $datenow}}" readonly>
                        <input type="hidden" id="date-payment-form" name="date-payment-form" value="{{$datenow ?? null}}" readonly>

                        <input type="hidden" name="coin" value="{{$coin}}" readonly>

                        <!--Precio de costo de todos los productos-->
                        <input type="hidden" name="price_cost_total" value="{{$price_cost_total}}" readonly>

                        <!--CANTIDAD DE PAGOS QUE QUIERO ENVIAR-->
                        <input type="hidden" id="amount_of_payments" name="amount_of_payments"  readonly>

                         <!--CANTIDAD DE PAGOS QUE QUIERO ENVIAR-->
                         <input type="hidden" id="amount_exento" name="amount_exento" value="{{$retiene_iva ?? 0 }}" readonly>

                        <!--Total del pago que se va a realizar-->
                        <input type="hidden" id="base_imponible_form" name="base_imponible_form"  readonly>

                        <!--Total del pago que se va a realizar-->
                        <input type="hidden" id="sub_total_form" name="sub_total_form" value="{{ $quotation->total_factura }}" readonly>

                        <!--Total de la factura sin restarle nada que se va a realizar-->
                        <input type="hidden" id="grandtotal_form" name="grandtotal_form"  readonly>

                        <!--Total del pago que se va a realizar-->
                        <input type="hidden" id="total_pay_form" name="total_pay_form"  readonly>

                        <!--IGTF-->
                        <input type="hidden" id="total_pay_form_before" name="total_pay_form_before">

                        <input id="IGTF_input_pre" type="hidden" name="IGTF_input_pre">
                        <input id="IGTF_input_store" type="hidden" name="IGTF_input_store" value="0">
                        <input id="IGTF_general" type="hidden" name="IGTF_general">
                        <input id="IGTF_general_form" type="hidden" name="IGTF_general_form">
                        <input id="total_pay_before" type="hidden" name="total_pay_before">
                        <input id="IGTF_porc" type="hidden" name="IGTF_porc" value="{{$igtfporc}}">

                        <input id="debitnote_input_pre" type="hidden"  name="debitnote_input_pre">
                        <input id="creditnote_input_pre" type="hidden"  name="creditnote_input_pre">

                        <!--Porcentaje de iva aplicado que se va a realizar-->
                        <input type="hidden" id="iva_form" name="iva_form"  readonly>
                        <input type="hidden" id="iva_amount_form" name="iva_amount_form"  readonly>

                        <!--Anticipo aplicado que se va a realizar-->
                        <input type="hidden" id="anticipo_form" name="anticipo_form"  readonly>

                        <input id="user_id" type="hidden" class="form-control @error('user_id') is-invalid @enderror" name="user_id" value="{{ Auth::user()->id }}" required autocomplete="user_id">

                        <input type="hidden" id="total_retiene_iva" name="total_retiene_iva"  readonly>
                        <input type="hidden" id="total_retiene_islr" name="total_retiene_islr" value="{{$total_retiene_islr }}" readonly>

                        <input type="hidden" id="total_mercancia" name="total_mercancia" value="{{$total_mercancia ?? 0 }}" readonly>
                        <input type="hidden" id="total_servicios" name="total_servicios" value="{{$total_servicios ?? 0 }}" readonly>

                        @if (Auth::user()->company['id']  == '26' AND $existe == FALSE)
                        <div class="form-group row" id="newcour2">
                            <label for="court" class="col-md-2 col-form-label text-md-right">Tipo Couriertool:</label>

                            <div class="col-md-2">
                                <select  id="court"  name="court" class="form-control">
                                    <option value="">Seleccionar</option>
                                    <option value="1">PALETA</option>
                                    <option value="2">CONTENEDOR</option>
                                    <option value="3">GUIA MASTER</option>
                                    <option value="4">TULA</option>
                                    <option value="5">GUIA TERRESTRE</option>

                                </select>

                            </div>
                            <label id="tifaclabel" for="tifac" class="col-md-2 col-form-label text-md-right">Tipo Factura:</label>

                            <div class="col-md-2">
                                <select class="form-control" name="tifac" id="tifac">
                                    <option value="">Seleccionar</option>
                                    <option value="1">ADUANA</option>
                                    <option value="2">INTERNACIONAL</option>
                                    <option value="3">SEGURO</option>
                                    <option value="4">PICK UP</option>
                                    <option value="5">MANEJO</option>
                                    <option value="6">IMPUESTOS</option>

                                </select>
                            </div>

                            <label id="tifaclabel" for="tifac" class="col-md-2 col-form-label text-md-right">Nro Couriertool:</label>

                            <div class="col-md-2">
                                <input id="nrofactcou" type="text" class="form-control" name="nrofactcou" value="{{ old('nrofactcou') }}">
                            </div>

                        </div>
                        @endif

                       <!--//formularios-->
                        <br>

                        <div>
                            <input type="hidden" id="id_quotation2" name="id_quotation2" value="{{$quotation->id}}">
                            <input type="hidden" id="anticipo_form2" name="anticipo_form2">
                        </div>
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
                                    <a href="#" onclick="pdf_comonotaentrega();" id="btnfacturarmaq" name="btnfacturarmaq" class="dropdown-item bg-light" title="imprimir">Imprimir Nota de Entrega</a> 
                                    @if (Auth::user()->mod_delete  == '1')
                                    <a href="#" class="dropdown-item bg-light delete" data-id-quotation={{$quotation->id}} data-toggle="modal" data-target="#reversarModal" title="Eliminar">Reversar Factura</a> 
                                    @endif
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
@endsection

@section('quotation_facturar')
    <script src="{{asset('js/facturar.js')}}"></script>
@endsection


@section('consulta')
    <script>
        $("#newcour").hide();
        $("#credit").hide();
        $(".igtfunic").hide();
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
                $("#newcour").show();
                $("#newcour2").hide();
                number_form = 1;
            }
            else {
            switchStatus = $(this).is(':checked');
                $("#credit").hide();
                $("#formulario1").show();
                $("#formenviarcredito").hide();
                $("#IGTF_buttom").show();
                $("#newcour2").show();
                $("#newcour").hide();

            }
        });

        /*$("#customSwitchesIGTFTotal").on('change', function() {
            if ($(this).is(':checked')) {
                $("#IGTF_form").show();
                calculateTotalIGTF();
            }
            else {
            switchStatus = $(this).is(':checked');
                $("#IGTF_form").hide();
                calculate();
            }
        }); */


        if("{{$quotation->total_factura}}" == 0){
            $("#divGuardar").hide();
        }

        $(document).ready(function () {
            $("#credit").mask('0000', { reverse: true });

        });
        $("#coin").on('change',function(){
            coin = $(this).val();
            if (coin == 'dolares'){
                $(".igtfunic").show();
            } else {
                $(".igtfunic").hide();
            }
            window.location = "{{route('quotations.createfacturar', [$quotation->id,''])}}"+"/"+coin;
        });

        $("#date-begin").on('change',function(){
           /* document.getElementById("date-begin-form").value = $(this).val();
            $("#date-begin-form").val($(this).val());*/
            var inputNombre = document.getElementById("date-begin-form");
               inputNombre.value = $(this).val();

            var inputNombre2 = document.getElementById("date-begin-form2");
               inputNombre2.value = $(this).val();
        });

        $("#date-payment").on('change',function(){
            document.getElementById("date-payment-form").value = $(this).val();

        });

        /*------------Saldar contra anticipo---------*/

        $("#cob_anticipo_saldar").on('focus', function() {
            $("#primer_form").attr("action",'{{route("quotations.storeanticiposaldar")}}');
        });


        $("#saveinvoice").on('focus', function() {
         $("#primer_form").attr("action",'{{route("quotations.storefactura")}}');
        });;

        function cambioderuta() {
            $("#primer_form").attr("action",'{{route("quotations.storeanticiposaldar")}}');
        }

        function restauraruta() {
            $("#primer_form").attr("action",'{{route("quotations.storefactura")}}');
        }
        /*------------fin Saldar contra anticipo---------*/

    </script>
    <script type="text/javascript">
            function pdf() {
                
                var nuevaVentana= window.open("{{ route('pdf',[$quotation->id,$coin])}}","ventana","left=800,top=800,height=800,width=1000,scrollbar=si,location=no ,resizable=si,menubar=no");
        
            }
            function pdf_media() {
                
                var nuevaVentana2= window.open("{{ route('pdf.media',[$quotation->id,$coin])}}","ventana","left=800,top=800,height=800,width=1000,scrollbar=si,location=no ,resizable=si,menubar=no");
        
            }
            function pdf_maq() {
                
                var nuevaVentana3= window.open("{{ route('pdf.maq',[$quotation->id,$coin])}}","ventana","left=800,top=800,height=800,width=1000,scrollbar=si,location=no ,resizable=si,menubar=no");
        
            }  
            
            function pdf_comonotaentrega() {
                let inputIva = document.getElementById("iva").value; 
                let date = document.getElementById("date-begin").value;
                var nuevaVentana4= window.open("{{ route('pdf.deliverynote',[$quotation->id,$coin,'',''])}}"+"/"+inputIva+"/"+date+"/1","ventana","left=800,top=800,height=800,width=1000,scrollbar=si,location=no ,resizable=si,menubar=no");

            }
            calculate();

            let igtf_saved = parseFloat("<?php echo $quotation->IGTF_amount;?>");

            function calculate() {

                let inputIva = document.getElementById("iva").value;

                //let totalIva = (inputIva * "<?php echo $quotation->total_factura; ?>") / 100;

                let totalFactura = "<?php echo $quotation->total_factura ?>";

                //AQUI VAMOS A SACAR EL MONTO DEL IVA DE LOS QUE ESTAN EXENTOS, PARA LUEGO RESTARSELO AL IVA TOTAL
                let totalBaseImponible = "<?php echo $quotation->base_imponible ?>";

                let totalIvaMenos = inputIva * totalBaseImponible / 100;

                let total_debit_notes = "<?php echo $total_debit_notes ?>";
                let total_credit_notes = "<?php echo $total_credit_notes ?>";


                    if (total_debit_notes == '') {
                        total_debit_notes = 0.00;
                    }

                    if (total_credit_notes == '') {
                        total_credit_notes = 0.00;
                    }

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
                var total_debit_notes_dos_decimales = parseFloat(total_debit_notes).toFixed(2);
                var debitnote_format = total_debit_notes.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});

                var total_credit_notes_dos_decimales = parseFloat(total_credit_notes).toFixed(2);
                var creditnote_format = total_credit_notes.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});


                // var grand_total = parseFloat(totalFactura) + parseFloat(totalIva);

                var grand_total = parseFloat(numbertotalfactura) + parseFloat(numbertotal_iva_exento);
                var grand_totalformat = grand_total.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});


                document.getElementById("grand_total").value = grand_totalformat;


                var inputAnticipo = document.getElementById("anticipo").value;

                var montoFormat = inputAnticipo.replace(/[$.]/g,'');

                var montoFormat_anticipo = montoFormat.replace(/[,]/g,'.');


                if(inputAnticipo){


                    document.getElementById("anticipo_form").value =  montoFormat_anticipo;
                    document.getElementById("anticipo_form2").value =  montoFormat_anticipo;
                }else{

                    document.getElementById("anticipo_form").value = 0;
                    document.getElementById("anticipo_form2").value = 0;
                }

                var total_pay = parseFloat(totalFactura) + total_iva_exento - montoFormat_anticipo  + parseFloat(total_debit_notes_dos_decimales) - parseFloat(total_credit_notes_dos_decimales);

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

                var total_pay = total_pay - calc_retencion_iva - total_islr_retencion;

                var total_payformat = total_pay.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});

               //IGTF
                var porcentaje = document.getElementById("IGTF_porc").value;

                var calc_porc = grand_total * (porcentaje/100);

                var IGTF_general = total_pay + calc_porc;

                var IGTF_input = calc_porc.toFixed(2);


                document.getElementById("total_pay").value =  total_payformat;

                document.getElementById("total_pay_form").value =  total_pay.toFixed(2);
                //////IGTF/////////
                document.getElementById("total_pay_form_before").value =  total_pay.toFixed(2);

                document.getElementById("IGTF_input_pre").value =  IGTF_input;
                document.getElementById("IGTF_input_pre_credit").value =  IGTF_input;

                document.getElementById("debitnote_input_pre").value = total_debit_notes_dos_decimales;
                document.getElementById("debitnote_input_pre_credit").value = total_debit_notes_dos_decimales;

                document.getElementById("creditnote_input_pre").value = total_credit_notes_dos_decimales;
                document.getElementById("creditnote_input_pre_credit").value = total_credit_notes_dos_decimales;


                document.getElementById("IGTF_general").value =  IGTF_general.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});

                document.getElementById("IGTF_general_form").value =  IGTF_general.toFixed(2);

                document.getElementById("total_pay_before").value = total_payformat;
                 //////IGTF/////////
                document.getElementById("iva_form").value =  inputIva;

                document.getElementById("iva_amount_form").value = document.getElementById("iva_amount").value;

                document.getElementById("grandtotal_form").value = grand_totalformat ;
                document.getElementById("grandtotal_form_credit").value = grand_totalformat ;

                //Quiere decir que el monto total a pagar es negativo o igual a cero
                if(total_pay.toFixed(2) <= 0){
                    document.getElementById("amount_pay").required = false;
                    document.getElementById("payment_type").required = false;
                    $("#amount_pay").hide();
                    $("#payment_type").hide();
                    $("#btn_agregar").hide();
                    $("#label_amount_pays").hide();
                    $("#IGTF_div_form").hide();
                }
            }



            $("#iva").on('change',function(){
                //calculate();

                let inputIva = document.getElementById("iva").value;

                //let totalIva = (inputIva * "<?php echo $quotation->total_factura; ?>") / 100;

                let totalFactura = "<?php echo $quotation->total_factura   ?>";

                //AQUI VAMOS A SACAR EL MONTO DEL IVA DE LOS QUE ESTAN EXENTOS, PARA LUEGO RESTARSELO AL IVA TOTAL
                let totalBaseImponible = "<?php echo $quotation->base_imponible   ?>";

                let totalIvaMenos = inputIva * totalBaseImponible / 100;


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


                var total_iva_exento =  totalIvaMenos;

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
                    document.getElementById("anticipo_form2").value =  montoFormat_anticipo;
                }else{
                    document.getElementById("anticipo_form").value = 0;
                    document.getElementById("anticipo_form2").value = 0;
                }

                var total_pay = parseFloat(totalFactura) + total_iva_exento - montoFormat_anticipo;

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

                var total_pay = total_pay - calc_retencion_iva - total_islr_retencion;
                var total_payformat = total_pay.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});

                document.getElementById("total_pay").value =  total_payformat;

                document.getElementById("total_pay_form").value =  total_pay.toFixed(2);

                document.getElementById("iva_form").value =  inputIva;

                document.getElementById("iva_amount_form").value = document.getElementById("iva_amount").value;

                document.getElementById("grandtotal_form").value = grand_totalformat;

                document.getElementById("grandtotal_form_credit").value = grand_totalformat;

                 //Quiere decir que el monto total a pagar es negativo o igual a cero
                 if(total_pay.toFixed(2) <= 0){
                    document.getElementById("amount_pay").required = false;
                    document.getElementById("payment_type").required = false;
                    $("#amount_pay").hide();
                    $("#payment_type").hide();
                    $("#btn_agregar").hide();
                    $("#label_amount_pays").hide();
                    $("#IGTF_div_form").hide();
                }

            });

            $("#customSwitchesIGTFTotal").on('change', function() {
                if ($(this).is(':checked')) {
                    $(".IGTF").show();

                    var val_input = document.getElementById("IGTF_input_pre").value;

                    var val_general = document.getElementById("IGTF_general").value;
                    var IGTF_general_form = document.getElementById("IGTF_general_form").value;

                    document.getElementById("IGTF_input").value = val_input;
                    document.getElementById("IGTF_input_store").value = val_input;
                    document.getElementById("total_pay").value = val_general;
                    document.getElementById("total_pay_form").value = IGTF_general_form;
                    document.getElementById("grandtotal_form").value = val_general;
                    document.getElementById("grandtotal_form_credit").value = val_general;


                } else {
                /*switchStatus = $(this).is(':checked');*/
                    $(".IGTF").hide();
                    $("#IGTF_input").val(0);
                    $("#IGTF_input_store").val(0);
                    var total_pay_before = document.getElementById("total_pay_before").value;
                    var IGTF_general_form = document.getElementById("IGTF_general_form").value;
                    var total_pay_form_before = document.getElementById("total_pay_form_before").value;
                    var grand_total = document.getElementById("grand_total").value;
                    document.getElementById("total_pay").value = total_pay_before;
                    document.getElementById("total_pay_form").value = total_pay_form_before;
                    document.getElementById("grandtotal_form").value = grand_total.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});
                    document.getElementById("grandtotal_form_credit").value = grand_total.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});
                }
            });

            if (igtf_saved > 0 ) {


                var val_input = document.getElementById("IGTF_input_pre").value;
                var val_general = document.getElementById("IGTF_general").value;
                var IGTF_general_form = document.getElementById("IGTF_general_form").value;

                document.getElementById("IGTF_input").value = val_input;
                document.getElementById("IGTF_input_store").value = val_input;
                document.getElementById("total_pay").value = val_general;
                document.getElementById("total_pay_form").value = IGTF_general_form;
                document.getElementById("grandtotal_form").value = val_general;
                document.getElementById("grandtotal_form_credit").value = val_general;
            }

            $("#anticipo").on('keyup',function(){
                //calculate();



                let inputIva = document.getElementById("iva").value;

                //let totalIva = (inputIva * "<?php echo $quotation->total_factura; ?>") / 100;

                let totalFactura = "<?php echo $quotation->total_factura ?>";

                //AQUI VAMOS A SACAR EL MONTO DEL IVA DE LOS QUE ESTAN EXENTOS, PARA LUEGO RESTARSELO AL IVA TOTAL
                let totalBaseImponible = "<?php echo $quotation->base_imponible ?>";


                let totalIvaMenos = inputIva * totalBaseImponible / 100;

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





                var total_iva_exento =  totalIvaMenos;

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

                if(inputAnticipo) {

                    document.getElementById("anticipo_form").value =  montoFormat_anticipo;
                    document.getElementById("anticipo_form2").value =  montoFormat_anticipo;
                }else{
                    document.getElementById("anticipo_form").value = 0;
                    document.getElementById("anticipo_form2").value = 0;
                }


                var total_pay = parseFloat(totalFactura) + total_iva_exento - montoFormat_anticipo;

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

                var total_pay = total_pay - calc_retencion_iva - total_islr_retencion;

                var total_payformat = total_pay.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});

                document.getElementById("total_pay").value =  total_payformat;

                document.getElementById("total_pay_form").value =  total_pay.toFixed(2);

                document.getElementById("iva_form").value =  inputIva;

                document.getElementById("iva_amount_form").value = document.getElementById("iva_amount").value;

                document.getElementById("grandtotal_form").value = grand_totalformat;

                document.getElementById("grandtotal_form_credit").value = grand_totalformat;

                //Quiere decir que el monto total a pagar es negativo o igual a cero
                 if(total_pay.toFixed(2) <= 0){
                    document.getElementById("amount_pay").required = false;
                    document.getElementById("payment_type").required = false;
                    $("#amount_pay").hide();
                    $("#payment_type").hide();
                    $("#btn_agregar").hide();
                    $("#label_amount_pays").hide();
                    $("#amount_pay").hide();
                    $("#IGTF_div_form").hide();
                }

            });

            /*------------Saldar nota---------*/
     $(document).on('click','#saldar',function(){

         var id_quotation = document.getElementById("id_quotation2").value;
         var anticipo = document.getElementById("anticipo_form2").value;
         var totalfac = document.getElementById("total_pay").value;

         var url = "{{ route('quotation.storesaldar') }}"+"/"+id_quotation+"/"+anticipo+"/"+totalfac;

         window.location.href = url;
     });


    </script>
@endsection
