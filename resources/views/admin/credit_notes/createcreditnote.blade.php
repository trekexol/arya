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
                <div class="card-header text-center font-weight-bold h3">Registro de Nota de Crédito</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('creditnotes.store') }}" enctype="multipart/form-data">
                        @csrf
                       
                        <input id="id_user" type="hidden" class="form-control @error('id_user') is-invalid @enderror" name="id_user" value="{{ Auth::user()->id }}" required autocomplete="id_user">
                        <input id="id_client" type="hidden" class="form-control @error('id_client') is-invalid @enderror" name="id_client" value="{{ $client->id ?? null  }}" required autocomplete="id_client">
                        <input id="id_vendor" type="hidden" class="form-control @error('id_vendor') is-invalid @enderror" name="id_vendor" value="{{ $vendor->id ?? $client->id_vendor ?? null  }}" required autocomplete="id_vendor">
                        <input id="id_invoice" type="hidden" class="form-control @error('id_invoice') is-invalid @enderror" name="id_invoice" value="{{ $invoice->id ?? null  }}" required autocomplete="id_invoice">
                       
                        
                        <div class="form-group row">
                            <label for="date" class="col-md-2 col-form-label text-md-right">Fecha</label>
                            <div class="col-md-3">
                                <input id="date_begin" type="date" class="form-control @error('date') is-invalid @enderror" name="date" value="{{ $datenow }}" required autocomplete="date">
    
                                @error('date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="serie" class="col-md-3 col-form-label text-md-right">N° de Control/Serie (Opcional):</label>

                            <div class="col-md-3 ">
                                <input id="serie" type="text" class="form-control @error('serie') is-invalid @enderror" name="serie" value="{{ old('serie') }}" autocomplete="serie">

                                @error('serie')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="invoices" class="col-md-2 col-form-label text-md-right">Asignar a</label>
                            <div class="col-md-3">
                                <select class="form-control" id="type" name="type">
                                    <option id="typeinvoice" selected value="Factura">Factura</option>
                                    <option id="typeclient" value="Cliente">Cliente</option>
                                </select>
                            </div> 
                            <div style="display: none">
                                <label for="transports" class="col-md-2 offset-sm-1 col-form-label text-md-right">Transporte / Tipo de Entrega</label>

                                <div class="col-md-3">
                                    <select class="form-control" id="id_transport" name="id_transport">
                                        <option selected value="-1">Ninguno</option>
                                        @foreach($transports as $var)
                                            <option value="{{ $var->id }}">{{ $var->placa }}</option>
                                        @endforeach
                                    
                                    </select>
                                </div> 
                            </div> 
                        </div> 
                        <div id="invoiceform" class="form-group row">
                            <label for="invoices" class="col-md-2 col-form-label text-md-right">Factura</label>
                            <div class="col-md-3">
                                <input id="invoice" type="text" class="form-control @error('invoice') is-invalid @enderror" name="invoice" value="{{ $invoice->number_invoice ?? '' }}" readonly required autocomplete="invoice">
    
                                @error('invoice')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-1">
                                <a href="{{ route('creditnotes.selectinvoice') }}" title="Seleccionar Factura"><i class="fa fa-eye"></i></a>  
                            </div>
                        </div>

                        <div id="clientform" class="form-group row">
                            <label for="clients" class="col-md-2 col-form-label text-md-right">Cliente</label>
                            <div class="col-md-3">
                                <input id="client" type="text" class="form-control @error('client') is-invalid @enderror" name="client" value="{{ $client->name ?? '' }}" readonly required autocomplete="client">
    
                                @error('client')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-1">
                                <a href="{{ route('creditnotes.selectclient') }}" title="Seleccionar Cliente"><i class="fa fa-eye"></i></a>  
                            </div>
                        </div>

                        
                        <div id="vendorform" class="form-group row">
                            <label for="vendors" class="col-md-2 col-form-label text-md-right">Vendedor</label>
                             <div class="col-md-3">
                                 <input id="id_vendor" type="text" class="form-control @error('id_vendor') is-invalid @enderror" name="vendor" value="{{ $vendor->name ?? $client->vendors['name'] ?? '' }}" readonly required autocomplete="id_vendor">
 
                                     @error('id_vendor')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                     @enderror
                             </div>
                             <div class="form-group col-md-1">
                                 <a href="{{ route('debitnotes.selectvendor',$client->id ?? -1) }}" title="Seleccionar Vendedor"><i class="fa fa-eye"></i></a>  
                             </div>
                            
                         </div>
                        
                         <div class="form-group row">
 
                             <label for="account" class="col-md-2 col-form-label text-md-right">Cuenta:</label>
                             <div class="col-md-3">
                                 <select class="form-control" id="id_account" name="id_account" required>
                                     
                                     <option selected value="Ventas por Bienes">Ventas por Bienes</option>
                                 </select>
                             </div>
                             <div class="form-group col-md-1">
                             </div>
                             <label for="observation" class="col-md-2 col-form-label text-md-right">Observaciones:</label>
 
                             <div class="col-md-4">
                                 <input id="observation" type="text" class="form-control @error('observation') is-invalid @enderror" name="observation" value="{{ old('observation') }}" autocomplete="observation">
 
                                 @error('observation')
                                     <span class="invalid-feedback" role="alert">
                                         <strong>{{ $message }}</strong>
                                     </span>
                                 @enderror
                             </div>
                         </div>
                         <div class="form-group row">
                             <label for="importe" class="col-md-2 col-form-label text-md-right">Monto(Opcional):</label>
 
                             <div class="col-md-2">
                                 <input id="importe" type="text" class="form-control @error('importe') is-invalid @enderror" name="importe" value="{{ old('importe') }}" autocomplete="importe">
 
                                 @error('importe')
                                     <span class="invalid-feedback" role="alert">
                                         <strong>{{ $message }}</strong>
                                     </span>
                                 @enderror
                             </div> 
                             
                             <label for="moneda" class="col-md-2 col-form-label text-md-right">Moneda:</label>
                             <div class="col-md-2">
                             <select class="form-control" id="coin" name="coin" required>
                                 <option selected value="bolivares">Bolivares</option>
                                 <option value="dolares">Dolares</option>
                             </select>
                            </div>   
 
                             <label for="rate" class="col-md-2 col-form-label text-md-right">Tasa:</label>
 
                             <div class="col-md-2">
                                 <input onkeyup="numeric(this)" id="rate" type="text" class="form-control @error('rate') is-invalid @enderror" name="rate" value="{{ number_format($tasa ?? old('rate'), 10, ',', '.') }}" autocomplete="rate">
 
                                 @error('rate')
                                     <span class="invalid-feedback" role="alert">
                                         <strong>{{ $message }}</strong>
                                     </span>
                                 @enderror
                             </div>  
 
  
                         </div>
                         
                         <br>
                        
                         <div class="form-group row">
                             <div class="col-sm-3 offset-sm-4">
                                 <button type="submit" class="btn btn-info">
                                   Registrar
                                 </button>
                             </div>
                             <div class="col-sm-2">
                                <a href="{{ route('creditnotes') }}" id="btnvolver" name="btnvolver" class="btn btn-danger" title="volver">Volver</a>  
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

        $("#clientform").hide();
        $("#vendorform").hide();

        if("{{isset($client)}}"){
            $("#invoiceform").hide();
            $("#clientform").show();
            $("#vendorform").show();
            document.getElementById("typeclient").selected = true;
        }


        $("#type").on('change',function(){
           
            var type = $(this).val();

            if(type == "Factura"){
                $("#invoiceform").show();
                $("#clientform").hide();
                $("#vendorform").hide();
            }else{
                $("#invoiceform").hide();
                $("#clientform").show();
                $("#vendorform").show();
            }
            
       });


       $(document).ready(function () {
            $("#importe").mask('000.000.000.000.000.000.000,00', { reverse: true });
            
        });


        function numeric(e) {
            
            e.value = e.value.replace(/\./g, ',');
            e.value = e.value.replace(/[A-Z]/g, '');
            e.value = e.value.replace(/[a-z]/g, '');
        
            return e.value;
            
        }
    </script>
@endsection
