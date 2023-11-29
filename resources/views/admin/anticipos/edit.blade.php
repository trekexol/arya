@extends('admin.layouts.dashboard')

@section('content')

    <!-- container-fluid -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="row py-lg-2">
            <div class="col-md-6">
                <h2>Editar Anticipo</h2>
            </div>

        </div>
    </div>
    <!-- /container-fluid -->

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

    <div class="card shadow mb-4">
        <div class="card-body">
            <form  method="POST"   action="{{ route('anticipos.update',$anticipo->id) }}" enctype="multipart/form-data" >
                @method('PATCH')
                @csrf()
                <div class="container py-2">
                    <div class="row">
                        <div class="col-12 ">
                            <form>
                                <input id="id_user" type="hidden" class="form-control @error('id_user') is-invalid @enderror" name="id_user" value="{{ Auth::user()->id }}" required autocomplete="id_user">
                                <input id="id_client" type="hidden" class="form-control @error('id_client') is-invalid @enderror" name="id_client" value="{{ $client->id ?? $anticipo->clients['id'] ?? -1 }}" required autocomplete="id_client">
                                <input id="id_provider" type="hidden" class="form-control @error('id_provider') is-invalid @enderror" name="id_provider" value="{{ $provider->id ?? $anticipo->providers['id'] ?? -1 }}" required autocomplete="id_provider">

                                <div class="form-group row">
                                    <label for="clients" class="col-md-3 col-form-label text-md-right">Beneficiario</label>
                                    <div class="col-md-5">
                                        <input id="client" type="text" class="form-control @error('client') is-invalid @enderror" name="client" value="{{ $client->name ?? $anticipo->clients['name'] ?? $provider->razon_social ?? $anticipo->providers['razon_social'] ?? '' }}" readonly required >

                                        @error('client')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-1">
                                        @if (isset($anticipo->clients['name']))
                                            <a href="{{ route('anticipos.selectclient',$anticipo->id) }}" title="Seleccionar Cliente"><i class="fa fa-eye"></i></a>
                                        @else
                                            <a href="{{ route('anticipos.selectprovider',$anticipo->id) }}" title="Seleccionar Proveedor"><i class="fa fa-eye"></i></a>
                                        @endif
                                      </div>
                                </div>
                                @if (isset($invoices_to_pay) && (count($invoices_to_pay)>0))
                                <div class="form-group row">
                                    <label for="clients" class="col-md-3 col-form-label text-md-right">Factura/NE: </label>
                                    <div class="col-md-5">
                                        <select  id="id_quotation"  name="id_quotation" class="form-control" width="20">
                                            @if (empty($anticipo->id_quotation) || (isset($client)))
                                                <option selected value="">Anticipo al Cliente</option>
                                            @else
                                                <option selected value="{{ $anticipo->quotations['id'] }}">Numero
                                                    {{($anticipo->quotations['number_invoice'] != null) ? 'Factura' : ''}}
                                                    {{($anticipo->quotations['number_delivery_note'] != null) ? 'Nota Entrega' : ''}}
                                                    {{($anticipo->quotations['number_order'] != null) ? 'Pedido' : ''}}:
                                                    {{ $anticipo->quotations['number_invoice'] ?? $anticipo->quotations['number_delivery_note'] ?? $anticipo->quotations['number_order'] }}</option>
                                                <option disabled>------------------------------</option>
                                            @endif

                                            @foreach($invoices_to_pay as $invoice)

                                                <?php
                                                    if (isset($invoice->number_invoice)){
                                                        $num_fac = 'Factura: '.$invoice->number_invoice;
                                                    }else if (isset($invoice->number_delivery_note)){
                                                        $num_fac = 'Nota de Entrega: '.$invoice->number_delivery_note;
                                                    }else if (isset($invoice->number_order)){
                                                        $num_fac = 'Pedido: '.$invoice->number_order;
                                                    }
                                                ?>
                                                <option  value="{{$invoice->id}}"> {{$num_fac ?? ''}} - Ctrl/Serie: {{ $invoice->serie ?? ''}} - {{ $invoice->observation ?? ''}}</option>

                                               @endforeach

                                        </select>
                                    </div>
                                </div>
                                @elseif(isset($expenses_to_pay) && (count($expenses_to_pay)>0))
                                <div class="form-group row">
                                    <label for="clients" class="col-md-3 col-form-label text-md-right">Factura de Compra:</label>
                                    <div class="col-md-5">
                                        <select  id="id_expense"  name="id_expense" class="form-control" width="20">
                                        @if (empty($anticipo->id_expense) || (isset($provider)))
                                            <option selected value="">Anticipo al Proveedor</option>
                                        @else
                                            <option selected value="{{ $anticipo->expenses['id'] }}">Orden: {{$anticipo->expenses['id']}} - Factura: {{$anticipo->expenses['invoice'] ?? ''}} - Ctrl/Serie: {{ $anticipo->expenses['serie'] ?? ''}}</option>
                                            <option disabled>------------------------------</option>
                                        @endif
                                            @foreach($expenses_to_pay as $invoice)
                                                <option  value="{{$invoice->id}}">Orden: {{$invoice->expenses['id']}} - Factura: {{$invoice->expenses['invoice'] ?? ''}} - Ctrl/Serie: {{ $invoice->serie ?? ''}} - {{ $invoice->observation ?? ''}}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>
                                @endif

                                <div class="form-group row">
                                    <label for="clients" class="col-md-3 col-form-label text-md-right">Cuentas</label>
                                    <div class="col-md-5">
                                        <select  id="id_account"  name="id_account" class="form-control" required>
                                            <option selected value="{{ $anticipo->accounts['id'] }}">{{ $anticipo->accounts['description'] }}</option>
                                            <option disabled>------------------------------</option>
                                            @foreach($accounts as $account)
                                                    <option  value="{{$account->id}}">{{ $account->description }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="date_begin" class="col-md-3 col-form-label text-md-right">Fecha de Inicio</label>

                                    <div class="col-md-5">
                                        <input id="date_begin" type="date" class="form-control @error('date_begin') is-invalid @enderror" name="date_begin" value="{{ $anticipo->date ?? $datenow }}" required autocomplete="date_begin">

                                        @error('date_begin')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="coin" class="col-md-3 col-form-label text-md-right">Moneda</label>
                                    <div class="col-md-5">
                                        <select  id="coin" name="coin" class="form-control" required>
                                            <option value="{{ $anticipo->coin }}">{{ $anticipo->coin }}</option>
                                            <option disabled>------------</option>
                                            <option  value="bolivares">Bolivares</option>
                                            <option  value="dolares">Dolares</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="amount" class="col-md-3 col-form-label text-md-right">Monto</label>

                                    <div class="col-md-5">
                                        <input id="amount" type="text" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ number_format($anticipo->amount ?? 0, 2, ',', '.') }}" required autocomplete="amount">

                                        @error('amount')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="rate" class="col-md-3 col-form-label text-md-right">Tasa</label>

                                    <div class="col-md-5">
                                        <input id="rate" type="text" class="form-control @error('rate') is-invalid @enderror" name="rate" value="{{ $anticipo->rate ?? $bcv }}" required autocomplete="rate">

                                        @error('rate')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <label for="rate" class="col-md-3 col-form-label text-md-right">Tasa hoy: {{ number_format($bcv ?? 0, 2, ',', '.') }}</label>

                                </div>
                                <div class="form-group row">
                                    <label for="reference" class="col-md-3 col-form-label text-md-right">Referencia</label>

                                    <div class="col-md-5">
                                        <input id="reference" type="text" class="form-control @error('reference') is-invalid @enderror" name="reference" value="{{ $anticipo->reference ?? old('reference') }}" autocomplete="reference">

                                        @error('reference')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>



                            <br>
                                <div class="form-group row">
                                    <div class="col-sm-2 offset-sm-3">
                                        <button type="submit" class="btn btn-success btn-block"><i class="fa fa-send-o"></i>Actualizar</button>
                                    </div>
                                    <div class="col-sm-2">
                                        @if (isset($anticipo->clients['id']))
                                            <a href="{{ route('anticipos') }}" name="danger" type="button" class="btn btn-danger btn-block">Cancelar</a>
                                        @else
                                            <a href="{{ route('anticipos.index_provider') }}" name="danger" type="button" class="btn btn-danger btn-block">Cancelar</a>
                                        @endif
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>

                    @endsection

@section('validacion')
    <script>
        $(function(){

            soloAlfaNumerico('reference');
        });
        $(document).ready(function () {
            $("#amount").mask('000.000.000.000.000.000.000,00', { reverse: true });

        });
        $(document).ready(function () {
            $("#rate").mask('000.000.000.000.000.000.000,00', { reverse: true });

        });

        $("#coin").on('change',function(){
            var coin = $(this).val();

            var amount = document.getElementById("amount").value;
            var montoFormat = amount.replace(/[$.]/g,'');
            var amountFormat = montoFormat.replace(/[,]/g,'.');

            var rate = document.getElementById("rate").value;
            var rateFormat = rate.replace(/[$.]/g,'');
            var rateFormat = rateFormat.replace(/[,]/g,'.');

            if(coin != 'bolivares'){

                var total = amountFormat / rateFormat;

                document.getElementById("amount").value = total.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});;
            }else{
                var total = amountFormat * rateFormat;

                document.getElementById("amount").value = total.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});;

            }
        });
    </script>
@endsection
