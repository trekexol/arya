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
                <div class="card-header" >Gasto o Compra {{ $retorno ?? 'almacenada' }} con Exito</div>
                
                <div class="card-body" >
                    <div class="form-group row">
                        <label for="date_payment" class="col-md-2 col-form-label text-md-right">Fecha de Factura:</label>
                        <div class="col-md-4">
                            <input id="date_payment" type="date" class="form-control @error('date_payment') is-invalid @enderror" name="date_payment" value="{{ $expense->date ?? '' }}" readonly autocomplete="date_payment">

                            @error('date_payment')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <label for="date_payment_expense" class="col-md-2 col-form-label text-md-right">Fecha de Pago:</label>
                        <div class="col-md-3">
                            <input id="date_payment_expense" type="date" class="form-control @error('date_payment') is-invalid @enderror" name="date_payment_expense" value="{{ $expense->date_payment ?? '' }}" readonly autocomplete="date_payment_expense">

                            @error('date_payment_expense')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>  
                        <div class="form-group row">
                            <label for="total_factura" class="col-md-2 col-form-label text-md-right">Total Factura:</label>
                            <div class="col-md-4">
                                <input id="total_factura" type="text" class="form-control @error('total_factura') is-invalid @enderror" name="total_factura" value="{{ number_format($expense->amount / ($bcv ?? 1), 2, ',', '.') ?? 0 }}" readonly required autocomplete="total_factura">
    
                                @error('total_factura')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="base_imponible" class="col-md-2 col-form-label text-md-right">Base Imponible:</label>
                            <div class="col-md-3">
                                <input id="base_imponible" type="text" class="form-control @error('base_imponible') is-invalid @enderror" name="base_imponible" value="{{ number_format($expense->base_imponible / ($bcv ?? 1), 2, ',', '.') ?? 0 }}" readonly required autocomplete="base_imponible">
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
                                <input id="iva_amounts" type="text" class="form-control @error('iva_amount') is-invalid @enderror" name="iva_amount" value="{{ number_format($expense->amount_iva / ($bcv ?? 1), 2, ',', '.') }}"  readonly required autocomplete="iva_amount"> 
                                
                                @error('iva_amount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="observation" class="col-md-2 col-form-label text-md-right">Retencion IVA:</label>

                            <div class="col-md-3">
                                <input id="observation" type="text" class="form-control @error('observation') is-invalid @enderror" name="observation" value="{{ number_format($expense->retencion_iva / ($bcv ?? 1), 2, ',', '.') }}" readonly required autocomplete="observation">

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
                                <input id="grand_total" type="text" class="form-control @error('grand_total') is-invalid @enderror" name="grand_total" value="{{ number_format(($expense->amount + $expense->amount_iva ) / ($bcv ?? 1), 2, ',', '.') ?? old('grand_total') }}" readonly required autocomplete="grand_total"> 
                           
                                @error('grand_total')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="note" class="col-md-2 col-form-label text-md-right">Retencion ISLR:</label>

                            <div class="col-md-3">
                                <input id="note" type="text" class="form-control @error('note') is-invalid @enderror" name="note" value="{{ number_format($expense->retencion_islr / ($bcv ?? 1), 2, ',', '.') }}" readonly required autocomplete="note">

                                @error('note')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        
                        <div class="form-group row">
                            <label for="anticipo" class="col-md-2 col-form-label text-md-right">Menos Anticipo:</label>
                            <div class="col-md-3">
                                <input id="anticipo" type="text" class="form-control @error('anticipo') is-invalid @enderror" name="anticipo" value="{{ number_format($expense->anticipo / ($bcv ?? 1), 2, ',', '.') ?? '0,00' }}" readonly required autocomplete="anticipo"> 
                           
                                @error('anticipo')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="iva" class="col-md-2 col-form-label text-md-right">IVA:</label>
                            <div class="col-md-2">
                            <select class="form-control" name="iva" id="iva">
                                <option value="{{ $expense->iva_percentage }}">{{ $expense->iva_percentage }}%</option>
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


                        @if($expense->porc_discount > 0)
                        <div id="div_descuento">
                        @else
                        <div id="div_descuento" style="display: none;">
                        @endif
                        
                         
                            <div class="form-group row">

                                <label for="porc_descuento_general" class="col-md-2 col-form-label text-md-right">Descuento %</label>
                                <div class="col-md-2">
                                    <input id="porc_descuento_general" type="text" class="form-control @error('porc_descuento_general') is-invalid @enderror" name="porc_descuento_general" placeholder="0,00" value="{{$expense->porc_discount ?? 0}}" autocomplete="porc_descuento_general" readonly> 
                                </div>    
                            </div>
                            <div class="form-group row">
                                <label for="descuento_general" class="col-md-2 col-form-label text-md-right">Monto Descuento</label>
                                <div class="col-md-3">
                                    <input id="descuento_general" type="text" class="form-control @error('descuento_general') is-invalid @enderror" name="descuento_general" placeholder="0,00" value="{{$expense->discount ?? 0}}" autocomplete="descuento_general" readonly> 
                            
                                    @error('descuento_general')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <div class="form-group row">
                            <label for="total_pays" class="col-md-2 col-form-label text-md-right">Total:</label>
                            <div class="col-md-3">
                                <input id="total_pay" type="text" class="form-control @error('total_pay') is-invalid @enderror" name="total_pay" value="{{ number_format($expense->amount_with_iva / ($bcv ?? 1), 2, ',', '.') }}" readonly  required autocomplete="total_pay"> 
                           
                                @error('total_pay')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            @if (isset($expense->credit_days))
                                <label for="total_pays" class="col-md-2 col-form-label text-md-right">Dias de Crédito:</label>
                                <div class="col-md-1">
                                    <input id="credit" type="text" class="form-control @error('credit') is-invalid @enderror" name="credit" value="{{ $expense->credit_days ?? '' }}" readonly autocomplete="credit"> 
                                </div>
                            @endif

                            <label for="note" class="col-md-1 col-form-label text-md-right">Tasa:</label>

                            <div class="col-md-1">
                                <input id="tasa_view" type="text" class="form-control @error('tasa_view') is-invalid @enderror" name="tasa_view" value="{{ number_format($expense->rate ?? 0.00, 2, ',', '.') }}" readonly autocomplete="tasa_view">

                                @error('tasa_view')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
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
                                    <a onclick="pdf_retencion_iva();" href="#" class="dropdown-item">Imprimir Retención de Iva</a> 
                                    <a onclick="pdf_retencion_islr();" href="#" class="dropdown-item">Imprimir Retención de ISLR</a> 
                                    <a onclick="pdf_media();" href="#" class="dropdown-item">Imprimir Factura Media Carta</a> 
                                    <a href="{{ route('expensesandpurchases.reversar_expense',$expense->id) }}" class="dropdown-item">Reversar Compra</a> 
                                </div>
                            </div> 
                            
                            <div class="col-md-3">
                                <a href="{{ route('expensesandpurchases.movement',[$expense->id,$coin]) }}" id="btnmovement" name="btnmovement" class="btn btn-light" title="movement">Ver Movimiento de Cuenta</a>  
                            </div>
                            
                            <div class="col-md-3">
                                <a href="{{ route('expensesandpurchases.index_historial') }}" id="btnvolver" name="btnvolver" class="btn btn-danger" title="volver">Ver Gastos o Compras</a>  
                            </div>
                        </div>
                        
                    </form>    
                </div>
            </div>
        </div>
</div>
@endsection



@section('consulta')


    <script type="text/javascript">

        $("#coin").on('change',function(){
            coin = $(this).val();
            window.location = "{{route('expensesandpurchases.create_expense_voucher', [$expense->id,''])}}"+"/"+coin;
        });
        function pdf() {
            
            var nuevaVentana= window.open("{{ route('pdf.expense',[$expense->id,$coin])}}","ventana","left=800,top=800,height=800,width=1000,scrollbar=si,location=no ,resizable=si,menubar=no");
    
        }
        function pdf_media() {
            
            var nuevaVentana2= window.open("{{ route('pdf.expense_media',[$expense->id,$coin])}}","ventana","left=800,top=800,height=800,width=1000,scrollbar=si,location=no ,resizable=si,menubar=no");
    
        }
        function pdf_retencion_iva() {
            
            var nuevaVentana= window.open("{{ route('expensesandpurchases.retencioniva',[$expense->id,$coin])}}","ventana","left=800,top=800,height=800,width=1000,scrollbar=si,location=no ,resizable=si,menubar=no");
    
        }

        function pdf_retencion_islr() {
            
            var nuevaVentana= window.open("{{ route('expensesandpurchases.retencionislr',[$expense->id,$coin])}}","ventana","left=800,top=800,height=800,width=1000,scrollbar=si,location=no ,resizable=si,menubar=no");
    
        }
           
    </script>
@endsection
