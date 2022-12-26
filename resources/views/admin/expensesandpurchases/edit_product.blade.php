@extends('admin.layouts.dashboard')

@section('content')
  
   

    {{-- VALIDACIONES-RESPUESTA--}}
@include('admin.layouts.success')   {{-- SAVE --}}
@include('admin.layouts.danger')    {{-- EDITAR --}}
@include('admin.layouts.delete')    {{-- DELELTE --}}
{{-- VALIDACIONES-RESPUESTA --}}


    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h2>Editar Producto </h2></div>
    
                    <div class="card-body">
            <form  method="POST"   action="{{ route('expensesandpurchases.update_product',[$expense_detail->id,$coin]) }}" enctype="multipart/form-data" onsubmit="return validacion()">
                @method('PATCH')
                @csrf()
                    <input id="rate_expense" type="hidden" class="form-control @error('rate_expense') is-invalid @enderror" name="rate_expense" value="{{ $expense_detail->expenses['rate'] ?? -1}}" readonly required autocomplete="rate_expense"> 
                            
                    <div class="form-group row">
                        @if(isset($expense_detail->inventories['code']))
                            <label for="description" class="col-md-2 col-form-label text-md-right">Código</label>
                            <div class="col-md-3">
                                <input id="code" type="text" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ $expense_detail->inventories['code'] ?? old('code') ?? '' }}" readonly required autocomplete="code" autofocus>
                            </div>
                            <label for="description"  class="col-md-3 col-form-label text-md-right">Descripción</label>
                        @else
                            <label for="description"  class="col-md-2 col-form-label text-md-right">Descripción</label>
                        @endif
                        
                        <div class="col-md-3">
                            <input id="description" type="text" class="form-control @error('description') is-invalid @enderror" name="description" value="{{ $expense_detail->description ?? $inventory->products['description'] ?? old('description') ?? '' }}" readonly required autocomplete="description">
                        </div>
                    </div>
                   
                    <div class="form-group row">
                        <label id="coinlabel" for="coin" class="col-md-2 col-form-label text-md-right">Moneda:</label>

                        <div class="col-md-3">
                            <select class="form-control" name="coin" id="coin">
                                <option value="bolivares">Bolívares</option>
                                @if($coin == 'dolares')
                                    <option selected value="dolares">Dolares</option>
                                @else 
                                    <option value="dolares">Dolares</option>
                                @endif
                            </select>
                        </div>
                        @if(isset($inventory->amount))
                            <label for="amount_old" class="col-md-3 col-form-label text-md-right">Cantidad En Inventario</label>
                            <div class="col-md-3">
                                <input id="amount_old" type="text" class="form-control @error('amount_old') is-invalid @enderror" name="amount_old" value="{{ $inventory->amount ?? 0 }}" readonly required autocomplete="amount_old">
                            </div> 
                        @endif
                    </div>
                    <div class="form-group row">
                        <label for="price" class="col-md-2 col-form-label text-md-right">Precio</label>
                        <div class="col-md-3">
                            <input id="price" type="text" class="form-control @error('price') is-invalid @enderror" name="price" value="{{ number_format($expense_detail->price / ($rate ?? 1), 2, ',', '.')}}"  required autocomplete="price">
                        </div>  
                        <label for="rate" class="col-md-3 col-form-label text-md-right">Tasa</label>
                        <div class="col-md-3">
                            <input id="rate" type="text" readonly class="form-control @error('rate') is-invalid @enderror" name="rate" value="{{ number_format($expense_detail->rate, 2, ',', '.')}}"  required autocomplete="rate">
                        </div>  
                        
                    </div>
                    <div class="form-group row">
                        <label for="price" class="col-md-2 col-form-label text-md-right">Descuento</label>
                        <div class="col-md-3">
                            <input id="discount" type="text" class="form-control @error('price') is-invalid @enderror" name="discount" value="{{ $expense_detail->porc_discount }}"  required autocomplete="price">
                        </div>  
                    </div>
                    
                    
                        <div class="form-group row">
                            <label for="amount" class="col-md-2 col-form-label text-md-right">Cantidad</label>

                            <div class="col-md-3">
                                <input id="amount" type="text" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ number_format($expense_detail->amount, 2, ',', '.') }}" required autocomplete="amount">

                                @error('amount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="gridCheck" class="col-md-2 col-form-label text-md-right">Exento</label>
                            <div class="col-md-1">
                                @if(($expense_detail->exento == 1))
                                    <input class="form-check-input" type="checkbox" name="exento" checked id="gridCheck">
                                @else
                                    <input class="form-check-input" type="checkbox" name="exento" id="gridCheck">
                                @endif
                            </div>  
                            <label for="gridCheck2" class="col-md-1 col-form-label text-md-right">ISLR</label>
                            <div class="col-md-1">
                                @if(($expense_detail->islr == 1))
                                    <input class="form-check-input" type="checkbox" name="islr" checked id="gridCheck2">
                                @else
                                    <input class="form-check-input" type="checkbox" name="islr" id="gridCheck2">
                                @endif
                            </div>  
                        </div>
                            
                                <br>
                                <div class="form-group row mb-0">
                                    <div class="col-md-4 offset-md-4">
                                        <button type="submit" class="btn btn-info">
                                           Actualizar Producto Cotizado
                                        </button>
                                    </div>
                                </form>
                                    <div class="col-md-2">
                                        <a href="{{ route('expensesandpurchases.create_detail',[$expense_detail->id_expense,$coin]) }}" id="btnfacturar" name="btnfacturar" class="btn btn-danger" title="facturar">Volver</a>  
                                    </div>
                                </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
 @endsection
 @section('validacion')
    <script>    
     
        $(document).ready(function () {
            $("#amount").mask('000.000.000.000.000.000.000.000,00', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#price").mask('000.000.000.000.000.000.000.000,00', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#rate").mask('000.000.000.000.000.000.000.000,00', { reverse: true });
            
        });
        $("#coin").on('change',function(){
            coin = $(this).val();
            var price = document.getElementById("price").value;
            var price_new_format = price.replace(/[$.]/g,'').replace(/[,]/g,'.');
            if(coin == 'bolivares'){
                var total = price_new_format * document.getElementById("rate_expense").value;
                document.getElementById("price").value = total.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});;        
            }else{
                var total = price_new_format / document.getElementById("rate_expense").value;
                document.getElementById("price").value = total.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});;        
            }
        });


        function validacion() {

            let amount = document.getElementById("amount").value; 

            if (amount < 1) {

                alert('La cantidad del Producto debe ser mayor a 1');
                return false;
            }

            var discount = document.getElementById("discount").value; 

            if ((discount < 0) || (discount > 100)) {

                alert('El descuento debe estar entre 0% y 100%');
                return false;
            }
            
                return true;
           



        }

    </script>
@endsection

