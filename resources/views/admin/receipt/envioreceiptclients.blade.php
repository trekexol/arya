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
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-center font-weight-bold h3">Enviar Recibos de Condominio</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('mails.receiptmasive') }}" enctype="multipart/form-data">
                        @csrf
                       
                        <input id="id_user" type="hidden" class="form-control @error('id_user') is-invalid @enderror" name="id_user" value="{{ Auth::user()->id }}" required autocomplete="id_user">
                        <input id="id_client" type="hidden" class="form-control @error('id_client') is-invalid @enderror" name="id_client" value="{{ $client->id ?? -1  }}" required autocomplete="id_client">

                        <div class="form-group row">
                           
                            
                            <label for="clients" class="col-md-3 col-form-label text-md-right">Condominio</label>
                            <div class="col-md-3">
                                <input id="client" type="text" class="form-control @error('client') is-invalid @enderror" name="client" value="{{ $client->name ?? '' }}" readonly required autocomplete="client">
    
                                @error('client')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-1">
                                <a href="{{ route('receipt.selectclientemail',$type) }}" title="Seleccionar"><i class="fa fa-eye"></i></a>  
                            </div>

                           

                        </div>           


                        
                        <div class="form-group row">
                            <label for="clients" class="col-md-3 col-form-label text-md-right">Gasto de Condominio</label>
                            <div class="col-md-8">
                               
                                @if (isset($invoices_to_pay) && (count($invoices_to_pay)>0))
                                <select  id="id_invoice"  name="id_invoice" class="form-control" width="20" required>
                                    
                                    @foreach($invoices_to_pay as $invoice)
                                    
                                    <?php
                                    $num_fac = '';
                                    
                                    if ($invoice->number_invoice > 0){
                                    $num_fac = 'Relación de Gasto: '.$invoice->number_invoice;
                                    }
                                    ?>
                                        <option  value="{{$invoice->id}}"> {{$num_fac ?? ''}} - {{ $invoice->serie ?? ''}} - Monto: {{ number_format($invoice->amount_with_iva, 2, ',', '.') ?? '0'}}Bs. - ${{ number_format($invoice->amount_with_iva/$invoice->bcv, 2, ',', '.') ?? '0'}} - {{ $invoice->observation ?? ''}}</option>
                                    @endforeach

                                </select>
                                @else
                                @if (isset($client->id ))
                                
                                <label class="col-md-8 col-form-label text-md-left">El Condominio no posee Relaciones Pendientes</label>
                                
                                @endif
    
                            @endif

                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="clients" class="col-md-3 col-form-label text-md-right">Mensaje:</label>
                            <div class="col-md-8">    
                                <input id="message_modal" type="text" class="col-md-6 form-control @error('message_modal') is-invalid @enderror" name="message_modal" value="" required autocomplete="message_modal">
                            </div> 
                        </div>

                       
                        <div class="form-group row">
                            <div class="col-sm-3 offset-sm-4">
                                <button type="submit" class="btn btn-info">
                                 Enviar Recibo
                                </button>
                            </div>
                            <div class="col-sm-2">
                                <a href="{{ route('receiptr') }}" id="btnvolver" name="btnvolver" class="btn btn-danger" title="volver">Volver</a>  
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
	$(function(){
        soloAlfaNumerico('code_comercial');
        soloAlfaNumerico('description');
    });
    </script>
@endsection
