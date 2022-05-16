@extends('admin.layouts.dashboard')

@section('content')

{{-- VALIDACIONES-RESPUESTA--}}
@include('admin.layouts.success')   {{-- SAVE --}}
@include('admin.layouts.danger')    {{-- EDITAR --}}
@include('admin.layouts.delete')    {{-- DELELTE --}}
{{-- VALIDACIONES-RESPUESTA --}}

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center font-weight-bold h3">Registro de Anticipo</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('anticiposlic.store') }}">
                        @csrf
                        
                        <input id="id_user" type="hidden" class="form-control @error('id_user') is-invalid @enderror" name="id_user" value="{{ Auth::user()->id }}" required autocomplete="id_user">
                        <input id="id_client" type="hidden" class="form-control @error('id_client') is-invalid @enderror" name="id_client" value="{{ $client->id ?? null }}" required autocomplete="id_client">
                       
                        <div class="form-group row">
                            <label for="clients" class="col-md-3 col-form-label text-md-right">Cliente</label>
                            <div class="col-md-6">
                                <input id="client" type="text" class="form-control @error('client') is-invalid @enderror" name="client" value="{{ $client->name ?? '' }}" readonly required >
    
                                @error('client')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-1">
                                <a href="{{ route('anticiposlic.selectclient') }}" title="Seleccionar Cliente"><i class="fa fa-eye"></i></a>  
                            </div>
                        </div>
                        @if (isset($invoices_to_pay) && (count($invoices_to_pay)>0))
                        <div class="form-group row">
                            <label for="clients" class="col-md-3 col-form-label text-md-right">Factura/Nota de E.</label>
                            <div class="col-md-6">
                                <select  id="id_invoice"  name="id_invoice" class="form-control" width="20">
                                    <option selected value="">Anticipo al Cliente</option>
                                    @foreach($invoices_to_pay as $invoice)
                                    
                                    <?php
                                    if ($invoice->number_invoice > 0){
                                    $num_fac = 'Factura: '.$invoice->number_invoice;
                                    }
                                    if ($invoice->number_delivery_note > 0){
                                    $num_fac = 'Nota de Entrega: '.$invoice->number_delivery_note;
                                    }
                                    ?>
                                        <option  value="{{$invoice->id}}"> {{$num_fac ?? ''}} - Ctrl/Serie: {{ $invoice->serie ?? ''}} - {{ $invoice->observation ?? ''}}</option>
                                    @endforeach

                                </select>
                            </div>
                        </div>
                        @endif
                        
                        <div class="form-group row">
                            <label for="clients" class="col-md-3 col-form-label text-md-right">Cuentas</label>
                            <div class="col-md-6">
                                <select  id="id_account"  name="id_account" class="form-control" required>
                                    <option selected value="">Seleccione una Opcion</option>
                                    @foreach($accounts as $account)
                                            <option  value="{{$account->id}}">{{ $account->description }}</option>
                                    @endforeach
                                
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="date_begin" class="col-md-3 col-form-label text-md-right">Fecha de Inicio</label>

                            <div class="col-md-6">
                                <input id="date_begin" type="date" class="form-control @error('date_begin') is-invalid @enderror" name="date_begin" value="{{ $datenow }}" required autocomplete="date_begin">

                                @error('date_begin')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="coin" class="col-md-3 col-form-label text-md-right">Moneda</label>
                            <div class="col-md-6">
                                <select  id="coin" name="coin" class="form-control" required>
                                    <option value="">Seleccione una Moneda</option>
                                    <option  value="bolivares">Bolivares</option>
                                    <option  value="dolares">Dolares</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="amount" class="col-md-3 col-form-label text-md-right">Monto</label>

                            <div class="col-md-6">
                                <input id="amount" type="text" class="form-control @error('amount') is-invalid @enderror" name="amount" placeholder="0,00" required autocomplete="amount">

                                @error('amount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="rate" class="col-md-3 col-form-label text-md-right">Tasa del Dia</label>

                            <div class="col-md-6">
                                <input id="rate" type="text" class="form-control @error('rate') is-invalid @enderror" name="rate" value="{{ number_format($bcv, 2, ',', '.') }}" required autocomplete="rate">

                                @error('rate')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="reference" class="col-md-3 col-form-label text-md-right">Referencia</label>

                            <div class="col-md-6">
                                <input id="reference" type="text" class="form-control @error('reference') is-invalid @enderror" name="reference" value="{{ old('reference') }}" autocomplete="reference">

                                @error('reference')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                       
                    <br>
                        <div class="form-group row mb-0">

                            <div class="col-md-4 offset-md-4">
                                @if (isset($client->id))
                                    <button type="submit" class="btn btn-info">
                                        Registrar Anticipo
                                    </button>
                                @else
                                    <button type="submit" title="seleccione un cliente" disabled class="btn btn-info">
                                        Registrar Anticipo
                                    </button>
                                @endif
                                    
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('anticiposlic') }}" id="btnfacturar" name="btnfacturar" class="btn btn-danger" title="facturar">Volver</a>                                 
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('validacion')
    <script>    
        $(document).ready(function () {
            $("#amount").mask('000.000.000.000.000.000.000,00', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#rate").mask('000.000.000.000.000.000.000,00', { reverse: true });
            
        });




	$(function(){
        soloAlfaNumerico('description');
       
    });
    </script>
@endsection