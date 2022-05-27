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
    <div class="row justify-content-right">
        <div class="col-md-1">
        </div>
        <div class="col-md-11" style="text-align: right;">
            <a href="{{ route('bankmovements.indexorderpayment')}}" class="btn btn-info" title="Transferencia">Listar Orden de Pago</a>
        </div>

    </div>
    <div class="row justify-content-center">
        

        
        <div class="col-md-12">
            <div class="card">
                
                <div class="card-header text-center font-weight-bold h3">Ordenes de Pago Directo</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('directpaymentorders.store') }}" enctype="multipart/form-data">
                        @csrf
                       <input id="user_id" type="hidden" class="form-control @error('user_id') is-invalid @enderror" name="user_id" value="{{ Auth::user()->id }}" required autocomplete="user_id">
                        
                        <div class="form-group row">
                            @if (isset($accounts))
                            <label for="account" class="col-md-2 col-form-label text-md-right">Retirar desde:</label>
                        
                            <div class="col-md-4">
                            <select id="account"  name="account" class="form-control" required>
                                <option value="">Seleccione una Cuenta</option>
                                @foreach($accounts as $index => $value)
                                    <option value="{{ $index }}" {{ old('account') == $index ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                                </select>

                                @if ($errors->has('account_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('account_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                            @endif
                            
                       
                            <label for="date_begin" class="col-md-3 col-form-label text-md-right">Fecha del Retiro:</label>

                            <div class="col-md-3">
                                <input id="date_begin" type="date" class="form-control @error('date_begin') is-invalid @enderror" name="date" value="{{ $datenow ?? old('date_begin') }}" required autocomplete="date_begin">

                                @error('date_begin')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                       
                       
                        <div class="form-group row">
                            
                            <label for="description" class="col-md-2 col-form-label text-md-right">Descripción</label>

                            <div class="col-md-4">
                                <input id="description" type="text" class="form-control @error('description') is-invalid @enderror" name="description" value="{{ old('description') }}"  autocomplete="description">

                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="reference" class="col-md-3 col-form-label text-md-right">Número de Referencia:</label>

                            <div class="col-md-3">
                                <input id="reference" type="text" class="form-control @error('reference') is-invalid @enderror" name="reference" value="{{ old('reference') }}"  autocomplete="reference">

                                @error('reference')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <br>
                        <div class="form-group row">
                            <label for="beneficiario" class="col-md-2 col-form-label text-md-right">Beneficiario:</label>
                        
                            <div class="col-md-4">
                            <select id="beneficiario"  name="beneficiario" class="form-control" required>
                                <option value="">Seleccione un Beneficiario</option>
                               
                                    <option value="1" {{ old('Beneficiario') == 'Cliente' ? 'selected' : '' }}>
                                        Clientes
                                    </option>
                                    <option value="2" {{ old('Beneficiario') == 'Proveedor' ? 'selected' : '' }}>
                                        Proveedores
                                    </option>
                                </select>

                                @if ($errors->has('beneficiario_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('beneficiario_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                       
                          <div class="col-md-4">
                                <select  id="subbeneficiario"  name="Subbeneficiario" class="form-control" required>
                                    <option value="">Seleccionar</option>
                                </select>

                                @if ($errors->has('subbeneficiario_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('subbeneficiario_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>  
                        <div class="form-group row">
                           
                            <label for="contrapartida" class="col-md-2 col-form-label text-md-right">Contrapartida:</label>
                            <div class="col-md-4">
                                <select id="type_form"  name="type_form" class="form-control" required>
     
                                <option value="-1">Seleccione una Contrapartida</option>
                                @foreach($contrapartidas as $index => $value)
                                    
                                    @if ($value != 'Bancos' && $value != 'Efectivo en Caja' && $value != 'Superavit o Deficit' && $value != 'Otros Ingresos' && $value != 'Resultado del Ejercicio'  && $value != 'Resultados Anteriores')
                                        <option value="{{ $index }}" {{ old('type_form') == $index ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endif


                                @endforeach
                                </select>

                            </div>
                            <div class="col-md-4">
                                <select  id="account_counterpart"  name="Account_counterpart" class="form-control" required>
                                    <option value="">Seleccionar</option>
                                    @if (isset($accounts_inventory))
                                        @foreach ($accounts_inventory as $var)
                                            <option value="{{ $var->id }}">{{ $var->description }}</option>
                                        @endforeach
                                    @endif
                                </select>

                                @if ($errors->has('account'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('account') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>  
                        <div class="form-group row">
                            
                            <label for="amount" class="col-md-2 col-form-label text-md-right">Monto del Retiro:</label>

                            <div class="col-md-4">
                                <input id="amount" type="text" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount') }}" required autocomplete="amount">

                                @error('amount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="rate" class="col-md-2 col-form-label text-md-right">Tasa:</label>

                            <div class="col-md-4">
                                <input id="rate" type="text" class="form-control @error('rate') is-invalid @enderror" name="rate" value="{{ number_format($bcv, 2, ',', '.')}}"  autocomplete="rate">

                                @error('rate')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
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
                            @if (isset($branches))
                            <label for="branch" class="col-md-2 offset-md-2 col-form-label text-md-right">Centro de Costo:</label>
                            <div class="col-md-2">
                                <select id="branch"  name="branch" class="form-control" >
                                    <option value="ninguno">Ninguno</option>
                                    @foreach($branches as $var)
                                        <option value="{{ $var->id }}">{{ $var->description}}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                        </div>
                       
                        <br>
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                   Guardar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('javascript')
    
    <script>
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

@section('javascript2')
<script>
        
        $("#beneficiario").on('change',function(){
           
            var beneficiario_id = $(this).val();
            $("#subbeneficiario").val("");
           
            // alert(beneficiario_id);
            getSubbeneficiario(beneficiario_id);
        });

    function getSubbeneficiario(beneficiario_id){
       
       
        $.ajax({
            url:"{{ route('directpaymentorders.listbeneficiary') }}" + '/' + beneficiario_id,
           
            beforSend:()=>{
                alert('consultando datos');
            },
            success:(response)=>{
                let subbeneficiario = $("#subbeneficiario");
                let htmlOptions = `<option value='' >Seleccione..</option>`;
                // console.clear();

                if(response.length > 0){
                    if(beneficiario_id == "1"){
                        response.forEach((item, index, object)=>{
                            let {id,name} = item;
                            htmlOptions += `<option value='${id}' {{ old('Subbeneficiario') == '${id}' ? 'selected' : '' }}>${name}</option>`

                        });
                    }else{
                        response.forEach((item, index, object)=>{
                            let {id,razon_social} = item;
                            htmlOptions += `<option value='${id}' {{ old('Subbeneficiario') == '${id}' ? 'selected' : '' }}>${razon_social}</option>`

                        });
                    }
                }
                //console.clear();
                // console.log(htmlOptions);
                subbeneficiario.html('');
                subbeneficiario.html(htmlOptions);
            
                
            
            },
            error:(xhr)=>{
                alert('Presentamos inconvenientes al consultar los datos');
            }
        })
    }

    $("#subbeneficiario").on('change',function(){
            var subbeneficiario_id = $(this).val();
            var beneficiario_id    = document.getElementById("beneficiario").value;
            
        });


</script>
@endsection

@section('consultadeposito')
    <script>
         $("#type_form").on('change',function(){
            type_var = $(this).val();
           
            searchCode(type_var);
        });
        function searchCode(type_var){

        
            $.ajax({
                
                url:"{{ route('expensesandpurchases.listaccount') }}"+'/'+type_var,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                
                    let account = $("#account_counterpart");
                    let htmlOptions = `<option value='' >Seleccione..</option>`;
                    // console.clear();
                    if(response.length > 0){
                        
                        response.forEach((item, index, object)=>{
                            let {id,description} = item;
                            htmlOptions += `<option value='${id}' {{ old('Account') == '${id}' ? 'selected' : '' }}>${description}</option>`

                        });
                    }
                    account.html('');
                    account.html(htmlOptions);
                
                    
                
                },
                error:(xhr)=>{
                }
            })
        }

        $("#type_form").on('change',function(){
            var contrapartida_id = $(this).val();
            $("#account_counterpart").val("");
            
            getSubcontrapartida(contrapartida_id);
        });

        function getSubcontrapartida(contrapartida_id){
            
            $.ajax({
                url:"{{ route('directpaymentorders.listcontrapartida') }}" + '/' + contrapartida_id,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                    let subcontrapartida = $("#account_counterpart");
                    let htmlOptions = `<option value='' >Seleccione..</option>`;
                    // console.clear();
                    if(response.length > 0){
                        response.forEach((item, index, object)=>{
                            let {id,description} = item;
                            htmlOptions += `<option value='${id}' {{ old('account_counterpart') == '${id}' ? 'selected' : '' }}>${description}</option>`

                        });
                    }
                    //console.clear();
                    // console.log(htmlOptions);
                    subcontrapartida.html('');
                    subcontrapartida.html(htmlOptions);
                
                    
                
                },
                error:(xhr)=>{
                    alert('Presentamos inconvenientes al consultar los datos');
                }
            })
        }

        $("#account_counterpart").on('change',function(){
                var subcontrapartida_id = $(this).val();
                var contrapartida_id    = document.getElementById("type_form").value;
                
            });

        
	$(function(){
        soloNumeros('xtelf_local');
        soloNumeros('xtelf_cel');
    });
    
 



    </script>
@endsection


