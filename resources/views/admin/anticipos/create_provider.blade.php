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
                <div class="card-header text-center font-weight-bold h3">Registro de Anticipo a Proveedor</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('anticipos.store_provider') }}">
                        @csrf
                        
                        <input id="id_user" type="hidden" class="form-control @error('id_user') is-invalid @enderror" name="id_user" value="{{ Auth::user()->id }}" required autocomplete="id_user">
                        <input id="id_provider" type="hidden" class="form-control @error('id_provider') is-invalid @enderror" name="id_provider" value="{{ $provider->id ?? null }}" required autocomplete="id_provider">
                       
                        <div class="form-group row">
                            <label for="providers" class="col-md-3 col-form-label text-md-right">Proveedor</label>
                            <div class="col-md-6">
                                <input id="provider" type="text" class="form-control @error('provider') is-invalid @enderror" name="provider" value="{{ $provider->razon_social ?? '' }}" readonly required >
    
                                @error('provider')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-1">
                                <a href="{{ route('anticipos.selectprovider') }}" title="Seleccionar Proveedor"><i class="fa fa-eye"></i></a>  
                            </div>
                        </div>
                        @if (isset($expenses) && (count($expenses)>0))
                        <div class="form-group row">
                            <label for="clients" class="col-md-3 col-form-label text-md-right">Gasto o Compra</label>
                            <div class="col-md-6">
                                <select  id="id_expense"  name="id_expense" class="form-control">
                                    <option selected value="-1">Seleccione una Gasto o Compra</option>
                                    @foreach($expenses as $expense)
                                            <option  value="{{$expense->id}}"> Orden: {{$expense->id}} - Factura: {{ $expense->invoice ?? '' }} - Ctrl/Serie: {{ $expense->serie ?? '' }} - Monto: {{$expense->amount_with_iva}}</option>
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
                                <input id="rate" type="text" class="form-control @error('rate') is-invalid @enderror" name="rate" value="{{ number_format($bcv, 2, ',', '.')  }}" required autocomplete="rate">

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
                                @if (isset($provider->id))
                                    <button type="submit" class="btn btn-info">
                                        Registrar Anticipo
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-info" disabled title="Debe Seleccionar un Proveedor para poder Continuar">
                                        Registrar Anticipo
                                    </button>
                                @endif
                                
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('anticipos.index_provider') }}" id="btnfacturar" name="btnfacturar" class="btn btn-danger" title="facturar">Volver</a>                                 
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


@section('javascript')

<script>
    $("#id_expense").on('change',function(){
            id = $(this).val();
            
           
            searcrate(id);

        });
        
   function searcrate(id){

       //we will send data and recive data fom our AjaxController
        $.ajax({
          url:"{{ route('anticipos.consultrate') }}" +'/'+id,
          //data:{'name':"luis"},
         // type:'post',
          success: function (response) {
           
           
            amount = response.rate;
            
            var rateFormat = amount.replace(/[$.]/g,',');
 
            document.getElementById("rate").value = rateFormat;

          },
          statusCode: {
             404: function() {
                alert('web not found');
             }
          },
          error:function(x,xs,xt){
              //nos dara el error si es que hay alguno
              window.open(JSON.stringify(x));
              alert('error: ' + JSON.stringify(x) +"\n error string: "+ xs + "\n error throwed: " + xt);
          } 
    
       }); 
      
/*
$.ajax({
    
    url:"{{ route('anticipos.consultrate') }}" +'/'+id,
    beforSend:()=>{
        alert('consultando datos');
    },
    success:(response)=>{
       
        let account = $("#rate");
        // let htmlOptions = `<option value='' >Seleccione..</option>`;
        // console.clear();
       if(response.length > 0){
            
            response.forEach((item, index, object)=>{
                let {id} = item;
                
                htmlOptions = id;
                //htmlOptions += `<option value='${id}' {{ old('Account') == '${id}' ? 'selected' : '' }}>${description}</option>`

            }); 
        }
        account.value(5);
        //account.html(htmlOptions);

        alert(response);
    
        
    
    },
    error:(xhr)=>{
        alert('mmm');
    }
}) */
}
  
</script>

@endsection