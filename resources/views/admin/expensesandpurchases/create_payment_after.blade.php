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
        <input id="IGTF_porc" type="hidden" name="IGTF_porc" value="{{$igtfporc}}">

            <div class="card" style="width: 70rem;" >
                <div class="card-header" ><h3>Gasto o Compras</h3></div>
                <div class="card-body" >
                    <input id="user_id" type="hidden" class="form-control @error('user_id') is-invalid @enderror" name="user_id" value="{{ Auth::user()->id }}" required autocomplete="user_id">
                    <input type="hidden" name="coin" value="{{$coin}}" readonly>
                    <input type="hidden" id="id_islr_concept_credit" name="id_islr_concept_credit" value="0" readonly>
                    <div class="form-group row">
                        <label for="date_payment" class="col-md-2 col-form-label text-md-right">Fecha de Factura:</label>
                        <div class="col-md-4">
                            <input id="date_payment" type="date" class="form-control @error('date_payment') is-invalid @enderror" name="date_payment" value="{{ $expense->date ?? $datenow }}" required autocomplete="date_payment">

                            @error('date_payment')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <label for="date_payment_expense" class="col-md-2 col-form-label text-md-right">Fecha de Pago:</label>
                        <div class="col-md-3">
                            <input id="date_payment_expense" type="date" class="form-control @error('date_payment') is-invalid @enderror" name="date_payment_expense" value="{{ $expense->date_payment ?? $datenow }}" required autocomplete="date_payment_expense">

                            @error('date_payment_expense')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                        <div class="form-group row">
                            <label for="total_factura" class="col-md-2 col-form-label text-md-right">Total Factura: </label>
                            <div class="col-md-4">
                                <input id="total_factura" type="text" class="form-control @error('total_factura') is-invalid @enderror" name="total_factura" value="{{ number_format($expense->total_factura, 2, ',', '.') ?? 0 }}" readonly required autocomplete="total_factura">

                                @error('total_factura')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="base_imponible" class="col-md-2 col-form-label text-md-right">Base Imponible:</label>
                            <div class="col-md-3">
                                <input id="base_imponible" type="text" class="form-control @error('base_imponible') is-invalid @enderror" name="base_imponible" value="{{ number_format($expense->base_imponible, 2, ',', '.') ?? 0 }}" readonly required autocomplete="base_imponible">
                                @error('base_imponible')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                        @if($expense->porc_discount > 0)
                        <div id="div_descuento">
                        @else
                        <div id="div_descuento" style="display: none;">
                        @endif


                            <div class="form-group row">

                                <label for="porc_descuento_general" class="col-md-2 col-form-label text-md-right">Descuento %</label>
                                <div class="col-md-2">
                                    <input id="porc_descuento_general" onkeyup="noespac(this)" type="text" class="form-control @error('porc_descuento_general') is-invalid @enderror" name="porc_descuento_general" placeholder="0.00" value="{{$expense->porc_discount ?? 0}}" autocomplete="porc_descuento_general">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="descuento_general" class="col-md-2 col-form-label text-md-right">Monto Descuento</label>
                                <div class="col-md-3">
                                    <input id="descuento_general" onkeyup="noespac(this)" type="text" class="form-control @error('descuento_general') is-invalid @enderror" name="descuento_general" placeholder="0.00" value="{{$expense->discount ?? 0}}" autocomplete="descuento_general">

                                    @error('descuento_general')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="total_descuento_general" class="col-md-2 col-form-label text-md-right">Total con Descuento</label>
                                <div class="col-md-3">
                                    <input id="total_descuento_general" type="text" class="form-control @error('total_descuento_general') is-invalid @enderror" name="total_descuento_general" placeholder="0,00" value="{{$expense->total_factura - $expense->discount}}" readonly>

                                    @error('total_descuento_general')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
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


                            <label for="iva_retencion" class="col-md-2 col-form-label text-md-right">Retencion IVA: </label>

                            <div class="col-md-3">
                                <input id="iva_retencion" type="text" class="form-control @error('iva_retencion') is-invalid @enderror" name="iva_retencion" value="{{ number_format($total_retiene_iva / ($bcv ?? 1), 2, ',', '.') }}" readonly required autocomplete="iva_retencion">

                                @error('iva_retencion')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-1">
                                @if (isset($expense->retencion_iva) && ($expense->retencion_iva != 0))
                                    <input class="form-check-input position-static" checked type="checkbox" id="retencion_iva_check" onclick="calculate(1);" name="retencion_iva_check"  value="option1" aria-label="...">
                                @else
                                    <input class="form-check-input position-static" type="checkbox" id="retencion_iva_check" onclick="calculate(1);" name="retencion_iva_check"  value="option1" aria-label="...">
                                @endif
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
                                <input id="islr_retencion" type="text" class="form-control @error('islr_retencion') is-invalid @enderror" name="islr_retencion" value="0" readonly required autocomplete="islr_retencion">

                                @error('islr_retencion')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-1">
                                @if (isset($expense->id_islr_concept))
                                    <input class="form-check-input position-static" checked type="checkbox" id="retencion_islr_check" onclick="calculate(1);checked_islr();" name="retencion_islr_check"  value="option1" aria-label="...">
                                @else
                                    <input class="form-check-input position-static" type="checkbox" id="retencion_islr_check" onclick="calculate(1);checked_islr();" name="retencion_islr_check"  value="option1" aria-label="...">
                                @endif
                                 </div>
                        </div>
                        <div id="islr-form" class="form-group row">
                            <div class="col-sm-3 offset-sm-8">
                            <select class="form-control" name="islr_concept" id="islr_concept" data-ajax="{{url('expensesandpurchases/getislramount')}}"> 
                                <option disabled selected value="0">Seleccionar</option>
                                @if (isset($islrconcepts))

                                    @foreach ($islrconcepts as $islrconcept)
                                        @if($provider->concepto_islr > 0 and $islrconcept->id == $provider->concepto_islr)
                                            <option selected value="{{$islrconcept->value}}" data-id="{{ $islrconcept->id }}" data-pagosmayores="{{ $islrconcept->pagos_mayores }}" data-sustraendo="{{ $islrconcept->sustraendo }}">{{ $islrconcept->description }} - {{$islrconcept->value}}%</option>
                                        @else
                                            <option value="{{$islrconcept->value}}" data-id="{{ $islrconcept->id }}" data-pagosmayores="{{ $islrconcept->pagos_mayores }}" data-sustraendo="{{ $islrconcept->sustraendo }}">{{ $islrconcept->description }} - {{$islrconcept->value}}%</option>
                                        @endif
                                    @endforeach
                                
                                @endif
                            </select>

                            </div>
                        </div>

                        <div class="form-group row">

                            <label for="anticipo" class="col-md-2 col-form-label text-md-right">Menos Anticipo:</label>
                            @if (empty($anticipos_sum))
                                <div class="col-md-3">
                                    <input id="anticipo" type="text" class="form-control @error('anticipo') is-invalid @enderror" name="anticipo" placeholder="0,00" readonly required autocomplete="anticipo">

                                    @error('anticipo')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            @else
                                <div class="col-md-3">
                                    <input id="anticipo" type="text" class="form-control @error('anticipo') is-invalid @enderror" name="anticipo" value="{{ number_format($anticipos_sum, 2, ',', '.') ?? 0.00 }}" readonly required autocomplete="anticipo">

                                    @error('anticipo')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            @endif
                            <div class="col-md-1">
                                <a href="{{ route('anticipos.selectanticipo_provider',[$expense->id_provider,$coin,$expense->id]) }}" title="Productos"><i class="fa fa-eye"></i></a>
                            </div>
                            <label for="iva" class="col-md-1 col-form-label text-md-right">IVA:</label>
                            <div class="col-md-2">
                                <select class="form-control" name="iva" id="iva">
                                    @if(isset($expense->iva_percentage))
                                        <option selected value="{{ $expense->iva_percentage }}">{{ $expense->iva_percentage }}%</option>
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


                        <!--<div class="form-group row">
                            <div class="col-md-2">
                            </div>
                            <div class="col-md-4">
                                <div class="custom-control custom-switch">
                                //if($expense->porc_discount > 0)
                                    <input type="checkbox" class="custom-control-input" id="checkdescuento" checked>
                                    <label class="custom-control-label" id="forcheckdescuento" for="checkdescuento">Descuento General Aplicado</label>
                                    //else
                                    <input type="checkbox" class="custom-control-input" id="checkdescuento">
                                    <label class="custom-control-label" id="forcheckdescuento" for="checkdescuento">Aplicar Descuento General</label>
                                    //endif
                                </div>
                            </div>

                        </div>-->

                        @if($debitnoteexpense)
                        <?php if($coin == 'dolares'){
                            $montodebito =  $debitnoteexpense->monto_perc / $bcv;
                        } else{
                            $montodebito =  $debitnoteexpense->monto_perc;
                        }?>


                    @if($debitnoteexpense->percentage > 0)
                    <div class="form-group row">
                        <label for="descuentonota" class="col-md-2 col-form-label text-md-right">Nota de Credito</label>
                        <div class="col-md-3">
                            <input id="notacredito" type="text" class="form-control @error('descuentonota') is-invalid @enderror" name="descuentonota" placeholder="0,00" value="{{ number_format($montodebito, 2, ',', '.') ?? 0 }}" readonly>

                            @error('descuentonota')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    @elseif($debitnoteexpense->percentage == 0)
                    <div class="form-group row">
                        <label for="descuentonota" class="col-md-2 col-form-label text-md-right"> Nota de Débito</label>
                        <div class="col-md-3">
                            <input id="descuentonota" type="text" class="form-control @error('descuentonota') is-invalid @enderror" name="descuentonota" placeholder="0,00" value="{{ number_format($montodebito, 2, ',', '.') ?? 0 }}" readonly>

                            @error('descuentonota')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    @endif
                    @endif



                        <input type="hidden" name="id_expense" value="{{$expense->id}}" readonly>


                        @if ($expense->IGTF_amount > 0)
                        <div class="form-group row IGTF" style="display: visibilty;">
                            <label for="igtf" class="col-md-2 col-form-label text-md-right">IGTF:</label>

                            <div class="col-md-3">

                                <input id="IGTF_input" type="text" class="form-control @error('IGTF_input') is-invalid @enderror" name="IGTF_input" value="{{$expense->IGTF_amount}}" readonly>

                            </div>


                        </div>
                        @else
                        <div class="form-group row IGTF" style="display: none;" >
                            <label for="igtf" class="col-md-2 col-form-label text-md-right">IGTF:</label>

                            <div class="col-md-3">

                                <input id="IGTF_input" type="text" class="form-control @error('IGTF_input') is-invalid @enderror" name="IGTF_input" value="{{ 0}}" readonly>

                            </div>


                        </div>
                        @endif



                        <div class="form-group row">
                            <label for="total_pays" class="col-md-2 col-form-label text-md-right">Total</label>
                            <div class="col-md-4">
                                <input id="total_pay" type="text" class="form-control @error('total_pay') is-invalid @enderror" name="total_pay" readonly  required autocomplete="total_pay">

                                @error('total_pay')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-3" id="IGTF_buttom">
                                <div class="custom-control custom-switch">

                                    @if ($expense->IGTF_amount > 0 )
                                    <input type="checkbox" class="custom-control-input igtftotal" id="customSwitchesIGTFTotal" name="customSwitchesIGTFTotal" checked>
                                    @else
                                    <input type="checkbox" class="custom-control-input igtftotal" id="customSwitchesIGTFTotal" name="customSwitchesIGTFTotal">
                                    @endif
                                    <label class="custom-control-label" for="customSwitchesIGTFTotal">IGTF: 3% (Total General)</label>
                                </div>
                            </div>
                        </div>



            <form method="POST" action="{{ route('expensesandpurchases.store_expense_payment') }}" enctype="multipart/form-data">
                @csrf
                <input id="igtfvalor" type="hidden" class="form-control @error('IGTF_input') is-invalid @enderror" name="igtfvalor" readonly>
                <input  type="hidden" name="IGTF_porc" value="{{$igtfporc}}">

                        <input type="hidden" id="id_islr_concept" name="id_islr_concept" value="{{ $expense->id_islr_concept ?? null }}" readonly>

                        <input type="hidden" name="id_expense" value="{{$expense->id}}" readonly>

                        <input type="hidden" name="coin" value="{{$coin}}" readonly>

                        <!--CANTIDAD DE PAGOS QUE QUIERO ENVIAR-->
                        <input type="hidden" id="amount_of_payments" name="amount_of_payments"  readonly>

                         <!--Total del pago que se va a realizar-->
                         <input type="hidden" id="base_imponible_form" name="base_imponible_form"  readonly>

                         <!--Total del pago que se va a realizar-->
                        <input type="hidden" id="sub_total_form" name="sub_total_form" value="{{ $expense->total_factura }}" readonly>

                        <!--Total de la factura sin restarle nada que se va a realizar-->
                        <input type="hidden" id="grandtotal_form" name="grandtotal_form"  readonly>

                        <!--Total del pago que se va a realizar-->
                        <input type="hidden" id="total_pay_form" name="total_pay_form"  readonly>

                        <input type="hidden" id="descuento_form" name="descuento_form" value="0" readonly>
                        <input type="hidden" id="porc_descuento_form" name="porc_descuento_form" value="{{ $expense->porc_discount ?? 0}}" readonly>

                        <!--Porcentaje de iva aplicado que se va a realizar-->
                        <input type="hidden" id="iva_form" name="iva_form"  readonly>
                        <input type="hidden" id="iva_amount_form" name="iva_amount_form"  readonly>

                        <!--Anticipo aplicado que se va a realizar-->
                        <input type="hidden" id="anticipo_form" name="anticipo_form"  readonly>

                        <input id="user_id" type="hidden" class="form-control @error('user_id') is-invalid @enderror" name="user_id" value="{{ Auth::user()->id }}" required autocomplete="user_id">

                        <input type="hidden" id="total_retiene_iva" name="total_retiene_iva" value="0" readonly>
                        <input type="hidden" id="total_retiene_islr" name="total_retiene_islr" value="{{ $total_retiene_islr ?? 0 }}" readonly>

                        <input type="hidden" id="date_payment_form" name="date_payment_form" value="{{$expense->date}}" readonly>

                        <input type="hidden" id="date_payment_form_expense" name="date_payment_expense" value="{{$expense->date_payment ?? $datenow}}" readonly>



                        <div class="form-group row" id="formulario1" >
                            <label id="label_amount_pays" for="amount_pays" class="col-md-2 col-form-label text-md-right">Forma de Pago:</label>
                            <div class="col-md-3">
                                <input id="amount_pay" type="text" class="form-control @error('amount_pay') is-invalid @enderror"  name="amount_pay" placeholder="0,00" required autocomplete="amount_pay">

                                @error('amount_pay')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                    <select  id="payment_type"  name="payment_type" class="form-control">
                                        <option selected value="0">Tipo de Pago 1</option>
                                        <option value="1">Cheque</option>
                                        <option value="2">Contado</option>
                                        <option value="3">Contra Anticipo</option>

                                        <option value="5">Depósito Bancario</option>
                                        <option value="6">Efectivo</option>
                                        <option value="7">Indeterminado</option>

                                        <option value="9">Tarjeta de Crédito</option>
                                        <option value="10">Tarjeta de Débito</option>
                                        <option value="11">Transferencia</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select  id="account_bank"  name="account_bank" class="form-control">
                                        <option selected value="0">Seleccione una Opcion</option>
                                        @foreach($accounts_bank as $account)
                                                <option  value="{{$account->id}}">{{ $account->description }}</option>
                                           @endforeach

                                    </select>
                                    <select  id="account_efectivo"  name="account_efectivo" class="form-control">
                                        <option selected value="0">Seleccione una Opcion</option>
                                        @foreach($accounts_efectivo as $account)
                                                <option  value="{{$account->id}}">{{ $account->description }}</option>
                                           @endforeach

                                    </select>
                                    <select  id="account_punto_de_venta"  name="account_punto_de_venta" class="form-control">
                                        <option selected value="0">Seleccione una Opcion</option>
                                        @foreach($accounts_punto_de_venta as $account)
                                                <option  value="{{$account->id}}">{{ $account->description }}</option>
                                           @endforeach

                                    </select>
                                    <input id="credit_days" type="text" class="form-control @error('credit_days') is-invalid @enderror" name="credit_days" placeholder="Dias de Crédito" autocomplete="credit_days">

                                    @error('credit_days')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <br>
                                    <input id="reference"  maxlenght="30" type="text" class="form-control @error('reference') is-invalid @enderror" name="reference" placeholder="Referencia" autocomplete="reference">

                                    @error('reference')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-1">
                                    <a id="btn_agregar" class="btn btn-info btn-circle" onclick="addForm()" title="Agregar"><i class="fa fa-plus"></i></a>
                                </div>
                        </div>
                        <div id="formulario2" class="form-group row" style="display:none;">
                                <label for="amount_pay2s" class="col-md-2 col-form-label text-md-right">Forma de Pago 2:</label>
                                <div class="col-md-3">
                                    <input id="amount_pay2" type="text" class="form-control @error('amount_pay2') is-invalid @enderror" placeholder="0,00" name="amount_pay2"   autocomplete="amount_pay2">

                                    @error('amount_pay2')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <select  id="payment_type2"  name="payment_type2" class="form-control">
                                        <option selected value="0">Tipo de Pago 2</option>
                                        <option value="1">Cheque</option>
                                        <option value="2">Contado</option>
                                        <option value="3">Contra Anticipo</option>

                                        <option value="5">Depósito Bancario</option>
                                        <option value="6">Efectivo</option>
                                        <option value="7">Indeterminado</option>

                                        <option value="9">Tarjeta de Crédito</option>
                                        <option value="10">Tarjeta de Débito</option>
                                        <option value="11">Transferencia</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select  id="account_bank2"  name="account_bank2" class="form-control">
                                        <option selected value="0">Seleccione una Opcion</option>
                                        @foreach($accounts_bank as $account)
                                                <option  value="{{$account->id}}">{{ $account->description }}</option>
                                           @endforeach

                                    </select>
                                    <select  id="account_efectivo2"  name="account_efectivo2" class="form-control">
                                        <option selected value="0">Seleccione una Opcion</option>
                                        @foreach($accounts_efectivo as $account)
                                                <option  value="{{$account->id}}">{{ $account->description }}</option>
                                           @endforeach

                                    </select>
                                    <select  id="account_punto_de_venta2"  name="account_punto_de_venta2" class="form-control">
                                        <option selected value="0">Seleccione una Opcion</option>
                                        @foreach($accounts_punto_de_venta as $account)
                                                <option  value="{{$account->id}}">{{ $account->description }}</option>
                                           @endforeach

                                    </select>
                                    <input id="credit_days2" type="text" class="form-control @error('credit_days2') is-invalid @enderror" name="credit_days2" placeholder="Dias de Crédito" autocomplete="credit_days2">

                                    @error('credit_days2')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <br>
                                    <input id="reference2" maxlenght="30"  type="text" class="form-control @error('reference2') is-invalid @enderror" name="reference2" placeholder="Referencia" autocomplete="reference2">

                                    @error('reference2')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-1">
                                    <a id="btn_agregar2" class="btn btn-danger btn-circle" onclick="deleteForm()" title="Eliminar"><i class="fa fa-trash"></i></a>
                                </div>

                        </div>

                        <div id="formulario3" class="form-group row" style="display:none;">
                            <label for="amount_pay3s" class="col-md-2 col-form-label text-md-right">Forma de Pago 3:</label>
                            <div class="col-md-3">
                                <input id="amount_pay3" type="text" class="form-control @error('amount_pay3') is-invalid @enderror" placeholder="0,00" name="amount_pay3" placeholder="Monto del Pago"  autocomplete="amount_pay3">

                                @error('amount_pay3')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <select  id="payment_type3"  name="payment_type3" class="form-control">
                                    <option selected value="0">Tipo de Pago 3</option>
                                    <option value="1">Cheque</option>
                                    <option value="2">Contado</option>
                                    <option value="3">Contra Anticipo</option>

                                    <option value="5">Depósito Bancario</option>
                                    <option value="6">Efectivo</option>
                                    <option value="7">Indeterminado</option>

                                    <option value="9">Tarjeta de Crédito</option>
                                    <option value="10">Tarjeta de Débito</option>
                                    <option value="11">Transferencia</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select  id="account_bank3"  name="account_bank3" class="form-control">
                                    <option selected value="0">Seleccione una Opcion</option>
                                    @foreach($accounts_bank as $account)
                                            <option  value="{{$account->id}}">{{ $account->description }}</option>
                                       @endforeach

                                </select>
                                <select  id="account_efectivo3"  name="account_efectivo3" class="form-control">
                                    <option selected value="0">Seleccione una Opcion</option>
                                    @foreach($accounts_efectivo as $account)
                                            <option  value="{{$account->id}}">{{ $account->description }}</option>
                                       @endforeach

                                </select>
                                <select  id="account_punto_de_venta3"  name="account_punto_de_venta3" class="form-control">
                                    <option selected value="0">Seleccione una Opcion</option>
                                    @foreach($accounts_punto_de_venta as $account)
                                            <option  value="{{$account->id}}">{{ $account->description }}</option>
                                       @endforeach

                                </select>
                                <input id="credit_days3" type="text" class="form-control @error('credit_days3') is-invalid @enderror" name="credit_days3" placeholder="Dias de Crédito" autocomplete="credit_days3">

                                @error('credit_days3')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <br>
                                <input id="reference3" maxlenght="30"  type="text" class="form-control @error('reference3') is-invalid @enderror" name="reference3" placeholder="Referencia" autocomplete="reference3">

                                @error('reference3')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-1">
                                <a id="btn_agregar3" class="btn btn-danger btn-circle" onclick="deleteForm()" title="Eliminar"><i class="fa fa-trash"></i></a>
                            </div>

                        </div>
                        <div id="formulario4" class="form-group row" style="display:none;">
                            <label for="amount_pay4s" class="col-md-2 col-form-label text-md-right">Forma de Pago 4:</label>
                            <div class="col-md-3">
                                <input id="amount_pay4" type="text" class="form-control @error('amount_pay4') is-invalid @enderror" placeholder="0,00" name="amount_pay4" placeholder="Monto del Pago"  autocomplete="amount_pay4">

                                @error('amount_pay4')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <select  id="payment_type4"  name="payment_type4" class="form-control">
                                    <option selected value="0">Tipo de Pago 4</option>
                                    <option value="1">Cheque</option>
                                    <option value="2">Contado</option>
                                    <option value="3">Contra Anticipo</option>

                                    <option value="5">Depósito Bancario</option>
                                    <option value="6">Efectivo</option>
                                    <option value="7">Indeterminado</option>

                                    <option value="9">Tarjeta de Crédito</option>
                                    <option value="10">Tarjeta de Débito</option>
                                    <option value="11">Transferencia</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select  id="account_bank4"  name="account_bank4" class="form-control">
                                    <option selected value="0">Seleccione una Opcion</option>
                                    @foreach($accounts_bank as $account)
                                            <option  value="{{$account->id}}">{{ $account->description }}</option>
                                       @endforeach

                                </select>
                                <select  id="account_efectivo4"  name="account_efectivo4" class="form-control">
                                    <option selected value="0">Seleccione una Opcion</option>
                                    @foreach($accounts_efectivo as $account)
                                            <option  value="{{$account->id}}">{{ $account->description }}</option>
                                       @endforeach

                                </select>
                                <select  id="account_punto_de_venta4"  name="account_punto_de_venta4" class="form-control">
                                    <option selected value="0">Seleccione una Opcion</option>
                                    @foreach($accounts_punto_de_venta as $account)
                                            <option  value="{{$account->id}}">{{ $account->description }}</option>
                                       @endforeach

                                </select>
                                <input id="credit_days4" type="text" class="form-control @error('credit_days4') is-invalid @enderror" name="credit_days4" placeholder="Dias de Crédito" autocomplete="credit_days4">

                                @error('credit_days4')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <br>
                                <input id="reference4" maxlenght="30"  type="text" class="form-control @error('reference4') is-invalid @enderror" name="reference4" placeholder="Referencia" autocomplete="reference4">

                                @error('reference4')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-1">
                                <a id="btn_agregar4" class="btn btn-danger btn-circle" onclick="deleteForm()" title="Eliminar"><i class="fa fa-trash"></i></a>
                            </div>

                        </div>
                        <div id="formulario5" class="form-group row" style="display:none;">
                            <label for="amount_pay5s" class="col-md-2 col-form-label text-md-right">Forma de Pago 5:</label>
                            <div class="col-md-3">
                                <input id="amount_pay5" type="text" class="form-control @error('amount_pay5') is-invalid @enderror" placeholder="0,00" name="amount_pay5" placeholder="Monto del Pago"  autocomplete="amount_pay5">

                                @error('amount_pay5')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <select  id="payment_type5"  name="payment_type5" class="form-control">
                                    <option selected value="0">Tipo de Pago 5</option>
                                    <option value="1">Cheque</option>
                                    <option value="2">Contado</option>
                                    <option value="3">Contra Anticipo</option>

                                    <option value="5">Depósito Bancario</option>
                                    <option value="6">Efectivo</option>
                                    <option value="7">Indeterminado</option>

                                    <option value="9">Tarjeta de Crédito</option>
                                    <option value="10">Tarjeta de Débito</option>
                                    <option value="11">Transferencia</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select  id="account_bank5"  name="account_bank5" class="form-control">
                                    <option selected value="0">Seleccione una Opcion</option>
                                    @foreach($accounts_bank as $account)
                                            <option  value="{{$account->id}}">{{ $account->description }}</option>
                                       @endforeach

                                </select>
                                <select  id="account_efectivo5"  name="account_efectivo5" class="form-control">
                                    <option selected value="0">Seleccione una Opcion</option>
                                    @foreach($accounts_efectivo as $account)
                                            <option  value="{{$account->id}}">{{ $account->description }}</option>
                                       @endforeach

                                </select>
                                <select  id="account_punto_de_venta5"  name="account_punto_de_venta5" class="form-control">
                                    <option selected value="0">Seleccione una Opcion</option>
                                    @foreach($accounts_punto_de_venta as $account)
                                            <option  value="{{$account->id}}">{{ $account->description }}</option>
                                       @endforeach

                                </select>
                                <input id="credit_days5" type="text" class="form-control @error('credit_days5') is-invalid @enderror" name="credit_days5" placeholder="Dias de Crédito" autocomplete="credit_days5">

                                @error('credit_days5')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <br>
                                <input id="reference5" maxlenght="30"  type="text" class="form-control @error('reference5') is-invalid @enderror" name="reference5" placeholder="Referencia" autocomplete="reference5">

                                @error('reference5')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-1">
                                <a id="btn_agregar5" class="btn btn-danger btn-circle" onclick="deleteForm()" title="Eliminar"><i class="fa fa-trash"></i></a>
                            </div>

                        </div>
                        <div id="formulario6" class="form-group row" style="display:none;">
                            <label for="amount_pay6s" class="col-md-2 col-form-label text-md-right">Forma de Pago 6:</label>
                            <div class="col-md-3">
                                <input id="amount_pay6" type="text" class="form-control @error('amount_pay6') is-invalid @enderror" placeholder="0,00" name="amount_pay6" placeholder="Monto del Pago"  autocomplete="amount_pay6">

                                @error('amount_pay6')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <select  id="payment_type6"  name="payment_type6" class="form-control">
                                    <option selected value="0">Tipo de Pago 6</option>
                                    <option value="1">Cheque</option>
                                    <option value="2">Contado</option>
                                    <option value="3">Contra Anticipo</option>

                                    <option value="5">Depósito Bancario</option>
                                    <option value="6">Efectivo</option>
                                    <option value="7">Indeterminado</option>

                                    <option value="9">Tarjeta de Crédito</option>
                                    <option value="10">Tarjeta de Débito</option>
                                    <option value="11">Transferencia</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select  id="account_bank6"  name="account_bank6" class="form-control">
                                    <option selected value="0">Seleccione una Opcion</option>
                                    @foreach($accounts_bank as $account)
                                            <option  value="{{$account->id}}">{{ $account->description }}</option>
                                       @endforeach

                                </select>
                                <select  id="account_efectivo6"  name="account_efectivo6" class="form-control">
                                    <option selected value="0">Seleccione una Opcion</option>
                                    @foreach($accounts_efectivo as $account)
                                            <option  value="{{$account->id}}">{{ $account->description }}</option>
                                       @endforeach

                                </select>
                                <select  id="account_punto_de_venta6"  name="account_punto_de_venta6" class="form-control">
                                    <option selected value="0">Seleccione una Opcion</option>
                                    @foreach($accounts_punto_de_venta as $account)
                                            <option  value="{{$account->id}}">{{ $account->description }}</option>
                                       @endforeach

                                </select>
                                <input id="credit_days6" type="text" class="form-control @error('credit_days6') is-invalid @enderror" name="credit_days6" placeholder="Dias de Crédito" autocomplete="credit_days6">

                                @error('credit_days6')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <br>
                                <input id="reference6" maxlenght="30"  type="text" class="form-control @error('reference6') is-invalid @enderror" name="reference6" placeholder="Referencia" autocomplete="reference6">

                                @error('reference6')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-1">
                                <a id="btn_agregar6" class="btn btn-danger btn-circle" onclick="deleteForm()" title="Eliminar"><i class="fa fa-trash"></i></a>
                            </div>

                        </div>
                        <div id="formulario7" class="form-group row" style="display:none;">
                            <label for="amount_pay7s" class="col-md-2 col-form-label text-md-right">Forma de Pago 7:</label>
                            <div class="col-md-3">
                                <input id="amount_pay7" type="text" class="form-control @error('amount_pay7') is-invalid @enderror" placeholder="0,00" name="amount_pay7" placeholder="Monto del Pago"  autocomplete="amount_pay7">

                                @error('amount_pay7')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <select  id="payment_type7"  name="payment_type7" class="form-control">
                                    <option selected value="0">Tipo de Pago 7</option>
                                    <option value="1">Cheque</option>
                                    <option value="2">Contado</option>
                                    <option value="3">Contra Anticipo</option>

                                    <option value="5">Depósito Bancario</option>
                                    <option value="6">Efectivo</option>
                                    <option value="7">Indeterminado</option>

                                    <option value="9">Tarjeta de Crédito</option>
                                    <option value="10">Tarjeta de Débito</option>
                                    <option value="11">Transferencia</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select  id="account_bank7"  name="account_bank7" class="form-control">
                                    <option selected value="0">Seleccione una Opcion</option>
                                    @foreach($accounts_bank as $account)
                                            <option  value="{{$account->id}}">{{ $account->description }}</option>
                                       @endforeach

                                </select>
                                <select  id="account_efectivo7"  name="account_efectivo7" class="form-control">
                                    <option selected value="0">Seleccione una Opcion</option>
                                    @foreach($accounts_efectivo as $account)
                                            <option  value="{{$account->id}}">{{ $account->description }}</option>
                                       @endforeach

                                </select>
                                <select  id="account_punto_de_venta7"  name="account_punto_de_venta7" class="form-control">
                                    <option selected value="0">Seleccione una Opcion</option>
                                    @foreach($accounts_punto_de_venta as $account)
                                            <option  value="{{$account->id}}">{{ $account->description }}</option>
                                       @endforeach

                                </select>
                                <input id="credit_days7" type="text" class="form-control @error('credit_days7') is-invalid @enderror" name="credit_days7" placeholder="Dias de Crédito" autocomplete="credit_days7">

                                @error('credit_days7')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <br>
                                <input id="reference7" maxlenght="30"  type="text" class="form-control @error('reference7') is-invalid @enderror" name="reference7" placeholder="Referencia" autocomplete="reference7">

                                @error('reference7')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-1">
                                <a id="btn_agregar7" class="btn btn-danger btn-circle" onclick="deleteForm()" title="Eliminar"><i class="fa fa-trash"></i></a>
                            </div>

                        </div>
                        <br>
                        <div class="form-group row" id="enviarpagos">
                            <div class="col-md-2">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">
                                    Guardar
                                 </button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('expensesandpurchases.index_historial') }}" id="btnfacturar" name="btnfacturar" class="btn btn-danger" title="Volver">Volver</a>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
</div>
@endsection

@section('javascript')
    <script src="{{asset('js/facturar.js')}}"></script>
@endsection


@section('consulta')
    <script>

       $("#credit").hide();
        $("#formenviarcredito").hide();
        var switchStatus = false;

            $("#date_payment").on('change',function(){
                 document.getElementById("date_payment_form").value = document.getElementById("date_payment").value;
            });
            $("#date_payment_expense").on('change',function(){
                 document.getElementById("date_payment_form_expense").value = document.getElementById("date_payment_expense").value;
            });

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
            window.location = "{{route('expensesandpurchases.create_payment_after', [$expense->id,''])}}"+"/"+coin;
        });

        var islr_concept = '<?php echo $provider->porc_retencion_islr ?>';

        var pagos_mayores = '<?php echo $islr_pagos_mayores ?>';
        var sustraendo = '<?php echo $islr_sustraendo ?>';

        if(islr_concept == 0){
            $("#islr-form").hide();
        }else{
            $("#islr-form").show();
        }


        function checked_islr() {
            var retencion_islr_check = $("#retencion_islr_check").is(':checked');

            if(retencion_islr_check){
                //valor inicial del porcentaje de islr
                $("#islr-form").show();
            }else{
                var selectElement = document.getElementById('islr_concept');
                $("#islr-form").hide();
                islr_concept = 0;
                selectElement.selectedIndex = 0;
                calculate(1);
            }

        }



        $("#islr_concept").on('change',function(){
            islr_concept = $(this).val();
            var selected_option = $(this).find('option:selected');
            var id_islr = selected_option.attr('data-id');
            var rutamount = $(this).attr('data-ajax');
            var Ruta = rutamount;
            let pagos_mayores_form = $("#pagos_mayores_form");
            let sustraendo_form = $("#sustraendo_form");

            pagos_mayores = selected_option.attr('data-pagosmayores');
            sustraendo = selected_option.attr('data-sustraendo');

            document.getElementById("id_islr_concept").value = $(this).find(':selected').data('id');
            document.getElementById("id_islr_concept_credit").value = $(this).find(':selected').data('id');
            
            pagos_mayores_form.val(pagos_mayores);
            sustraendo_form.val(sustraendo);
            calculate(1);

        });
    </script>
    <script type="text/javascript">

calculate(1);

function calculate(valor) {

    var porc_discount = 0;
    var discount = 0;
    let totalIva = 0;
    let inputIva = document.getElementById("iva").value;
    //let totalIva = (inputIva * "<?php echo $expense->total_factura; ?>") / 100;
    let totalFactura = "<?php echo $expense->total_factura ?>";
    let descuentonota = "<?php echo $expense->total_factura ?>";
    //AQUI VAMOS A SACAR EL MONTO DEL IVA DE LOS QUE ESTAN EXENTOS, PARA LUEGO RESTARSELO AL IVA TOTAL
    let totalBaseImponible = "<?php echo $expense->base_imponible ?>";

    if (valor == '0'){
        porc_discount = "<?php echo $expense->porc_discount ?>";
        discount = "<?php echo $expense->discount ?>";

    }
    if (valor == '1'){
        porc_discount = $("#porc_descuento_general").val();
        discount = totalFactura * porc_discount / 100;


        if (totalBaseImponible != totalFactura) {

            totalBaseImponible = totalBaseImponible - (totalBaseImponible * porc_discount / 100);

        }


        if (totalBaseImponible == totalFactura) {
        totalBaseImponible = totalFactura - discount;
        }
    }


    if (valor == '2'){
        discount = $("#descuento_general").val();
        totalBaseImponible = totalFactura - discount;
        porcentaje = (discount * 100) / totalFactura; // Regla de tres
        porc_discount = porcentaje;  // Quitar los decimales

        $("#porc_descuento_general").val(porc_discount);
        $("#porc_descuento_form").val(porc_discount);


    }


    totalFactura = totalFactura - discount;

            if (totalBaseImponible > 0){

               totalIvaMenos = (totalFactura * inputIva) / 100;
               totalIva = (totalBaseImponible * inputIva) / 100;


           } else {
               totalIvaMenos = 0;

           }

    //let totalIvaMenos = parseInt(inputIva * "<?php echo $expense->base_imponible ; ?>", 10) / 100

    //Toma la Base y la envia por form
    let base_imponible_form = document.getElementById("base_imponible").value;

    var montoFormat = base_imponible_form.replace(/[$.]/g,'');

    var montoFormat_base_imponible_form = montoFormat.replace(/[,]/g,'.');

    document.getElementById("base_imponible_form").value =  montoFormat_base_imponible_form;

    //Toma la Base y la envia por form

    let sub_total_form = document.getElementById("total_factura").value;

    var montoFormat = sub_total_form.replace(/[$.]/g,'');

    var montoFormat_sub_total_form = montoFormat.replace(/[,]/g,'.');


    //document.getElementById("sub_total_form").value =  montoFormat_sub_total_form;
    var total_iva_exento =  parseFloat(totalIva);


    var iva_format = total_iva_exento.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});

    //retencion de iva

    var retencion_iva_check = $("#retencion_iva_check").is(':checked');

    let porc_retencion_iva = "<?php echo $provider->porc_retencion_iva ?>";
    var calc_retencion_iva = total_iva_exento * porc_retencion_iva / 100;
    var total_retencion_iva = calc_retencion_iva.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});


    document.getElementById("iva_retencion").value =  total_retencion_iva;

    if(retencion_iva_check){
        document.getElementById("total_retiene_iva").value =  calc_retencion_iva;
    }else{
        document.getElementById("total_retiene_iva").value = 0;
    }
    //-----------------------

    //retencion de islr
    var retencion_islr_check = $("#retencion_islr_check").is(':checked');
    let total_retiene_islr= "<?php echo $total_retiene_islr / ($bcv ?? 1) ?>";

    pagos_mayores = Number(pagos_mayores);

    if (pagos_mayores > 0) {

        if (total_retiene_islr > pagos_mayores){
            var sustraendo_form = Number(sustraendo);
        } else {
            var sustraendo_form = 0;
        }
    } else {
        var sustraendo_form = 0;
    }

    if(retencion_islr_check){
        sustraendo_form = sustraendo_form;
    } else {
        sustraendo_form = 0;
    }


    let porc_retencion_islr = islr_concept;
    var calc_retencion_islr = (total_retiene_islr * porc_retencion_islr / 100) - sustraendo_form;
    var total_retencion_islr = calc_retencion_islr.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});

    document.getElementById("islr_retencion").value =  total_retencion_islr;

    if(retencion_islr_check){
        document.getElementById("total_retiene_islr").value =  calc_retencion_islr;
    }else{
        document.getElementById("total_retiene_islr").value = 0;
    }
    //------------------------------------

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

    var total_pay = totalFactura + total_iva_exento - montoFormat_anticipo;


    var total_iva_retencion = document.getElementById("total_retiene_iva").value;

    var total_islr_retencion = document.getElementById("total_retiene_islr").value;

    var total_pay = total_pay - total_iva_retencion - total_islr_retencion ;

    var notacredito = $("#notacredito").val();
   var debito = $("#descuentonota").val();






   if(notacredito == undefined && debito == undefined){
    var total_pay = total_pay;
   }

   else if(notacredito == undefined){
     debito = debito.replace('.', '');
        debito = debito.replace(',', '.');
        var total_pay = debito + total_pay;
    }

    else if(debito == undefined){
        notacredito = notacredito.replace('.', '');
        notacredito = notacredito.replace(',', '.');
        var total_pay =  total_pay - notacredito;


    }

    var porcentajeigft = document.getElementById("IGTF_porc").value;


    if (valor == '3'){
        $(".IGTF").show();


var calc_porc = grand_total * porcentajeigft / 100;

var IGTF_input = calc_porc.toFixed(2);
var IGTF_input = IGTF_input.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});
document.getElementById("IGTF_input").value = IGTF_input;
document.getElementById("igtfvalor").value = IGTF_input;


total_pay = total_pay + parseFloat(IGTF_input);

    }else{
        $(".IGTF").hide();
        $('#customSwitchesIGTFTotal').prop('checked',false);

                    //$("#IGTF_input").val(0);
                    var IGTF_input = 0;
                    document.getElementById("igtfvalor").value = IGTF_input;

                    //document.getElementById("IGTF_input").value = IGTF_input;

                    total_pay = total_pay + parseFloat(IGTF_input);
    }



    var total_payformat = total_pay.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});




    document.getElementById("total_pay").value =  total_payformat;

    document.getElementById("total_pay_form").value =  total_payformat;

    document.getElementById("iva_form").value =  inputIva;

    document.getElementById("iva_amount_form").value = document.getElementById("iva_amount").value;

    document.getElementById("grandtotal_form").value = grand_totalformat;



    var grand_totalformat_m_discount = discount.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});
    var totalFactura_form = totalFactura.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});




    $("#descuento_general").val(grand_totalformat_m_discount);
    $("#total_descuento_general").val(totalFactura_form);

    $("#descuento_general_form").val(discount);
    $("#descuento_form").val(discount);
    $("#porc_descuento_form").val(porc_discount);

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


$("#date_payment").on('change',function(){
     document.getElementById("date_payment_form").value = document.getElementById("date_payment").value;
});
$("#date_payment_expense").on('change',function(){
     document.getElementById("date_payment_form_expense").value = document.getElementById("date_payment_expense").value;
});


$("#iva").on('change',function(){
    calculate(1);
});

$("#anticipo").on('keyup',function(){
    calculate(1);
});

$("#porc_descuento_general").on('change',function(){
    calculate(1);
});

$("#descuento_general").on('change',function(){
    calculate(2);
});

var valorigtf = $("#IGTF_input").val();

if(valorigtf > 0){
    $('#customSwitchesIGTFTotal').prop('checked',true);

    calculate(3);

    }
$("#customSwitchesIGTFTotal").on('change', function() {
                if ($(this).is(':checked')) {
                    calculate(3);

                } else {
                    calculate(1);


                    }

            });


$("#checkdescuento").on('change', function() {

    if ($(this).is(':checked')) {

        $("#div_descuento").show();
        document.getElementById("forcheckdescuento").innerHTML = "Descuento Aplicado";

        if (porc_discount > 0){

            calculate(1);
        } else {
            calculate(1);
        }


    }else {


    document.getElementById("descuento_form").value = 0;
    $("#porc_descuento_general").val(0);
    $("#porc_descuento_form").val(0);
    $("#descuento_form").val(0);
    $("#descuento_general").val(0);

    $("#div_descuento").hide();
        document.getElementById("forcheckdescuento").innerHTML = "Aplicar Descuento";
        calculate(1);
    }

});





    </script>
@endsection
