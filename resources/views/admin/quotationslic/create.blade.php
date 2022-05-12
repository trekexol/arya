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
        <div class="col-md-12" >
            <div class="card">
                <div class="card-header" ><h3>Registro de Cotización</h3></div>

                <div class="card-body" >
                    <div class="form-group row">
                        <label for="date_quotation" class="col-md-2 col-form-label text-md-right">Fecha de Cotización:</label>
                        <div class="col-md-2">
                            <input id="date_quotation" type="date" class="form-control @error('date_quotation') is-invalid @enderror" name="date_quotation" value="{{ $quotation->date_quotation ?? $datenow }}" readonly required autocomplete="date_quotation">

                            @error('date_quotation')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <label for="transports" class="col-md-2 col-form-label text-md-right">Transporte:</label>
                        <div class="col-md-2">
                            <input id="transport" type="text" class="form-control @error('transport') is-invalid @enderror" name="transport" value="{{ $quotation->transports['placa'] ?? old('transport') }}" readonly required autocomplete="transport">

                            @error('transport')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <label for="serie" class="col-md-2 col-form-label text-md-right">N° Ctrl/Serie Factura:</label>
                                           
                        <div class="col-md-2">
                            @if (isset($quotation->serie))
                            <input id="serie" type="text" class="form-control @error('serie') is-invalid @enderror" name="serie" value="{{ $quotation->serie ?? '' }}" autocomplete="serie">
                            @else
                            <input id="serie" type="text" class="form-control @error('serie') is-invalid @enderror" name="serie" value="{{ $quotation->serie ?? '' }}" disabled autocomplete="serie">
                            @endif


                        </div>

                        


                    </div>

                        <div class="form-group row">
                            
                            <label for="client" class="col-md-1 col-form-label text-md-right">Cliente:</label>
                            <div class="col-md-3">
                                <input id="client" type="text" class="form-control @error('client') is-invalid @enderror" name="client" value="{{ $quotation->clients['name'] ?? $datenow }}" readonly required autocomplete="client">
                                @error('client')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <label for="vendor" class="col-md-2 col-form-label text-md-right">Vendedor:</label>
                            <div class="col-md-2">
                                <input id="vendor" type="text" class="form-control @error('vendor') is-invalid @enderror" name="vendor" value="{{ $quotation->vendors['name'] ?? old('vendor') }}" readonly required autocomplete="vendor">
                                @error('vendor')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                           
                            <label for="serie" class="col-md-2 col-form-label text-md-right">N° Ctrl/Serie Nota E:</label>
                            <div class="col-md-2">
                            @if (isset($quotation->serie_note))
                            <input id="serie2" type="text" class="form-control @error('serie2') is-invalid @enderror" name="serie2" value="{{ $quotation->serie_note ?? '' }}" autocomplete="serie2">
                            @else
                            <input id="serie0" type="text" class="form-control" name="serie0" value="0" disabled>
                            <input id="serie2" type="hidden" name="serie2" value="0">
                            @endif

                           </div>
                        </div>

                        <div class="form-group row">

                            <label for="observation" class="col-md-2 col-form-label text-md-right">Observaciones:</label>
                            <div class="col-md-4">
                                <input id="observation" type="text" class="form-control @error('observation') is-invalid @enderror" name="observation" value="{{ $quotation->observation ?? old('observation') }}" autocomplete="observation">

                                @error('observation')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
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
                        </div>

                        <div class="form-group row">
                            <label for="note" class="col-md-2 col-form-label text-md-right">Nota Pie de Factura:</label>

                            <div class="col-md-4">
                                <input id="note" type="text" class="form-control @error('note') is-invalid @enderror" name="note" value="{{ $quotation->note ?? old('note') }}" autocomplete="note">

                                @error('note')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="rate" class="col-md-2 col-form-label text-md-right">Tasa:</label>
                            <div class="col-md-2">
                                <input id="rate" type="text" class="form-control @error('rate') is-invalid @enderror" name="rate" value="{{ bcdiv($quotation->bcv,'1',2) ?? bcdiv($bcv,'1',2) }}" required autocomplete="rate">
                                @error('rate')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <a href="#" onclick="refreshrate()" title="Guardar Tasa"><i class="fas fa-save"></i></a>
                            <label  class="col-md-1">BCV:</label><label for="tasaactual" id="tasaacutal">{{ number_format(bcdiv($bcv,'1',2), 2, ',', '.')}}</label>
                        </div>
                        <form id="formSendProduct" method="POST" action="{{ route('quotations.storeproduct') }}" enctype="multipart/form-data" onsubmit="return validacion()">
                            @csrf
                            <input id="id_quotation" type="hidden" class="form-control @error('id_quotation') is-invalid @enderror" name="id_quotation" value="{{ $quotation->id ?? -1}}" readonly required autocomplete="id_quotation">
                            <input id="id_inventory" type="hidden" class="form-control @error('id_inventory') is-invalid @enderror" name="id_inventory" value="{{ $inventory->id ?? -1 }}" readonly required autocomplete="id_inventory">
                            <input id="coinhidden" type="hidden" class="form-control @error('coin') is-invalid @enderror" name="coin" value="{{ $coin ?? 'bolivares' }}" readonly required autocomplete="coin">
                            <input id="bcv" type="hidden" class="form-control @error('bcv') is-invalid @enderror" name="bcv" value="{{ $bcv ?? $bcv_quotation_product }}" readonly required autocomplete="bcv">
                            <input id="id_user" type="hidden" class="form-control @error('id_user') is-invalid @enderror" name="id_user" value="{{ Auth::user()->id }}" readonly required autocomplete="id_user">


                                <div class="form-row col-md-12">
                                    <div class="form-group col-md-2">
                                        <label for="description" >Código</label>
                                        <input id="code_id" type="hidden" class="form-control @error('code_id') is-invalid @enderror" name="code_id" value="{{ $inventory->product_id ?? old('code_id') ?? '' }}"  required autocomplete="code" autofocus >
                                        <input id="code" type="text" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ $inventory->code ?? old('code') ?? '' }}"  required autocomplete="code" autofocus  onblur="mycodePrecio()" >


                                    </div>

                                    <div class="form-group col-md-1">

                                        <a href="" title="Buscar Producto Por Codigo" onclick="searchCode()"><i class="fa fa-search"></i></a>

                                            <a href="{{ route('quotations.selectproduct',[$quotation->id,$coin,'productos']) }}" title="Productos"><i class="fa fa-eye"></i></a>

                                    </div>

                                    <div class="form-group col-md-2">
                                        <label for="description" >Descripción</label>
                                        <input id="description" type="text" class="form-control @error('description') is-invalid @enderror" name="description" value="{{ $inventory->products['description'] ?? old('description') ?? '' }}" readonly required autocomplete="description">

                                        @error('description')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-1">
                                        <label for="amount" >Cantidad</label>
                                        <input id="amount_product"  type="text" class="form-control @error('amount') is-invalid @enderror" name="amount" value="1" required autocomplete="amount">

                                        @error('amount')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-1">
                                        @if (empty($inventory))
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="exento" id="gridCheck">
                                            <label class="form-check-label" for="gridCheck">
                                                Exento
                                            </label>
                                        </div>
                                        @else
                                        <div class="form-check">
                                            @if($inventory->products['exento'] == 1)
                                                <input class="form-check-input" type="checkbox" name="exento" checked id="gridCheck">
                                            @else
                                                <input class="form-check-input" type="checkbox" name="exento" id="gridCheck">
                                            @endif
                                            <label class="form-check-label" for="gridCheck">
                                                Exento
                                            </label>
                                        </div>
                                        @endif

                                    </div>
                                    <div class="form-group col-md-1">
                                        @if (empty($inventory))
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="exento" id="gridCheck">
                                            <label class="form-check-label" for="gridCheck">
                                                Retiene ISLR
                                            </label>
                                        </div>
                                        @else
                                            <div class="form-check">
                                                @if($inventory->products['exento'] == 1)
                                                    <input class="form-check-input" type="checkbox" name="islr" checked id="gridCheck2">
                                                @else
                                                    <input class="form-check-input" type="checkbox" name="islr" id="gridCheck2">
                                                @endif
                                                <label class="form-check-label" for="gridCheck2">
                                                    Retiene ISLR
                                                </label>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group col-md-2">
                                        @if(isset($inventory->products['price']) && (isset($quotation->bcv)) && ($inventory->products['money'] != 'Bs') && ($coin == 'bolivares'))
                                            <?php

                                                $product_Bs = $inventory->products['price'] * $quotation->bcv;

                                            ?>
                                            <label for="cost" >Precio</label>
                                            <input id="cost" type="text" class="form-control @error('cost') is-invalid @enderror" name="cost" value="{{ number_format($product_Bs, 2, ',', '.') ?? '' }}"  required autocomplete="cost">
                                        @else
                                            <label for="cost" >Precio</label>
                                            <input id="cost" type="text" class="form-control @error('cost') is-invalid @enderror" name="cost" value="{{number_format($inventory->products['price'] ?? 0, 2, ',', '.') ?? '' }}"  required autocomplete="cost">
                                        @endif


                                        @error('cost')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-1">
                                        <label for="discount" >Descuento</label>
                                        <input id="discount_product" type="text" class="form-control  @error('discount') is-invalid @enderror" name="discount" value="0" required autocomplete="discount">

                                        @error('discount')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-1">
                                        @if (isset($inventory))
                                            <input type="button" title="Agregar" value=" + "  onclick="sendProduct()" >
                                        @endif

                                    </div>
                                </div>
                        </form>


                    <div class="form-group ">
                        <div class="col-sm-2">
                            <label for="Precio">Precio Multiples:</label>
                        </div>
                        <div class="col-sm-4">
                            <select  id="precio_list"  name="Precio_lista" class="form-control">
                                <option value="">Selecciona</option>
                            </select>
                        </div>
                    </div>
                       <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                <tr>
                                        <th class="text-center">Código</th>
                                        <th class="text-center">Descripción</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-center">Precio</th>
                                        <th class="text-center">Descuento</th>
                                        <th class="text-center">Sub Total</th>
                                        <th class="text-center"><i class="fas fa-cog"></i></th>

                                </tr>
                                    </thead>

                                    <tbody>
                                        @if (empty($inventories_quotations))
                                        @else
                                        <?php
                                            $suma = 0.00;
                                        ?>

                                            @foreach ($inventories_quotations as $var)

                                            <?php
                                            $percentage = (($var->price * $var->amount_quotation) * $var->discount)/100;

                                            $total_less_percentage = ($var->price * $var->amount_quotation) - $percentage;

                                                if($coin == 'bolivares'){
                                                    $var->rate = null;
                                                }

                                                if(isset($var->rate)){
                                                    $product_Bs = $total_less_percentage / $var->rate;
                                                }


                                                if(isset($var->excento)){
                                                    if ($var->excento == 1) {
                                                        $vexcento = 1;
                                                    }else{
                                                        $vexcento = 0;
                                                    }
                                                    
                                                } else {
                                                    $vexcento = 0;
                                                }

                                            ?>
                                                <tr>
                                                <td style="text-align: right">{{ $var->code}}</td>



                                                
                                                @if($vexcento == 1)
                                                    <td style="text-align: right">{{ $var->description}} (E)</td>
                                                @else
                                                    <td style="text-align: right">{{ $var->description}}</td>
                                                @endif

                                                <td style="text-align: right">{{ $var->amount_quotation}}</td>
                                                <td style="text-align: right">{{number_format($var->price / ($var->rate ?? 1), 2, ',', '.')}}</td>
                                                <td style="text-align: right">{{number_format($var->discount, 0, '', '.')}}%</td>
                                                @if(isset($var->rate))
                                                    <td style="text-align: right">{{number_format($product_Bs, 2, ',', '.')}}</td>
                                                @else
                                                    <td style="text-align: right">{{number_format($total_less_percentage, 2, ',', '.')}}</td>
                                                @endif

                                                <?php
                                                    $suma += $total_less_percentage;

                                                ?>
                                                    <td style="text-align: right">
                                                    <a href="{{ route('quotations.productedit',[$var->quotation_products_id,$coin]) }}" title="Editar"><i class="fa fa-edit"></i></a>
                                                    <a href="#" class="delete" data-id={{$var->quotation_products_id}} data-description={{$var->description}} data-id-quotation={{$quotation->id}} data-coin={{$coin}} data-toggle="modal" data-target="#deleteModal" title="Eliminar"><i class="fa fa-trash text-danger"></i></a>                                                     </td>
                                                       
                                                       
                                                </tr>
                                            @endforeach

                                            <?php
                                                if(isset($var->rate)){
                                                    $suma = $suma / $var->rate;
                                                }
                                            ?>
                                            <tr>
                                                <td style="text-align: right">-------------</td>
                                                <td style="text-align: right">-------------</td>
                                                <td style="text-align: right">-------------</td>
                                                <td style="text-align: right">-------------</td>
                                                <td style="text-align: right">Total</td>
                                                <td style="text-align: right">{{number_format($suma, 2, ',', '.')}}</td>

                                                <td style="text-align: right"></td>

                                                </tr>
                                        @endif
                                    </tbody>
                                </table>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                @if(!isset($quotation->date_billing))
                                    <div class="col-md-12">
                                        <a href="#" onclick="guardarcambios()" id="btnguardar" name="btnguardar" class="btn btn-info" title="facturar">Guardar</a>
                                        @if($suma == 0)
                                            <a onclick="validate()" id="btnSendNote" name="btnfacturar" class="btn btn-info" title="facturar">Nota de Entrega</a>
                                        @else
                                            <a onclick="deliveryNoteSend()" id="btnSendNote" name="btnfacturar" class="btn btn-info" title="facturar">Nota de Entrega</a>
                                        @endif
                                   
                                @else
                                @endif
                                
                                    <a href="{{ route('pdf.previewfactura',[$quotation->id,$coin ?? null])}}"  id="btnfacturar" name="btnfacturar" class="btn btn-success" title="facturar">Imprimir Prev Factura</a>
                                    @if($suma == 0)
                                        <a onclick="validate()" id="btnfacturar" name="btnfacturar" class="btn btn-success" title="facturar">Facturar</a>
                                        <a onclick="validate()" id="btnorder" name="btnorder" class="btn btn-danger" title="order">Pedido</a>
                                    @else
                                        <a href="{{ route('quotations.createfacturar',[$quotation->id,$coin]) }}" id="btnfacturar" name="btnfacturar" class="btn btn-success" title="facturar">Facturar</a>
                                        <a href="{{ route('orders.create_order',[$quotation->id,$coin]) }}" id="btnorder" name="btnorder" class="btn btn-danger" title="order">Pedido</a>
                                    @endif
                                </div>

                            </div>

                </div>
            </div>
        </div>
    </div>
</div>
<!-- Delete Warning Modal -->
<div class="modal modal-danger fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Eliminar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form action="{{ route('quotations.deleteProduct') }}" method="post">
                @csrf
                @method('DELETE')
                <input id="id_quotation_product_modal" type="text" class="form-control @error('id_quotation_product_modal') is-invalid @enderror" name="id_quotation_product_modal" readonly required autocomplete="id_quotation_product_modal">
                <input id="id_quotation_modal" type="text" class="form-control @error('id_quotation_modal') is-invalid @enderror" name="id_quotation_modal" readonly required autocomplete="id_quotation_modal">
                <input id="coin_modal" type="text" class="form-control @error('coin_modal') is-invalid @enderror" name="coin_modal" readonly required autocomplete="coin_modal">

                <h5 class="text-center">Seguro que desea eliminar?</h5>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger">Eliminar</button>
            </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('quotation_create')

    <script>

        $(document).on('click','.delete',function(){
         let id = $(this).attr('data-id');
         let id_quotation = $(this).attr('data-id-quotation');
         let coin = $(this).attr('data-coin');
         let description = $(this).attr('data-description');

         $('#id_quotation_product_modal').val(id);
         $('#id_quotation_modal').val(id_quotation);
         $('#coin_modal').val(coin);
         $('#description_modal').val(description);
        });

        function mycodePrecio() {

            var code_id       = document.getElementById("code_id").value;
            var RutaProducto = " {{route('products.listprice')}}"+"/"+code_id;
            getCodigos(RutaProducto);
        }

        function getCodigos(RutaProducto){
            $.ajax({
                url:`${RutaProducto}`,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                    let precio_list = $("#precio_list");
                    let htmlOptions = `<option value='' >Seleccione..</option>`;
                  //  console.clear();
                    if(response.length > 0){
                        response.forEach((item, index, object)=>{
                            let {id,price} = item;
                            htmlOptions += `<option value='${price}' {{ old('Precio_lista') == '${id}' ? 'selected' : '' }}>${price}</option>`

                        });
                    }
                    //console.clear();
                     // console.log(htmlOptions);
                    precio_list.html('');
                    precio_list.html(htmlOptions);
                },
                error:(xhr)=>{
                    //alert('Presentamos inconvenientes al consultar los datos');
                }
            })
        }

        $("#precio_list").on('change',function(){
            var coin        = document.getElementById("coin").value;
            if(coin == 'dolares'){
                var precio_list     = document.getElementById("precio_list").value;
                var precio_str    = precio_list.replaceAll(".", ",");
                document.getElementsByName("cost")[0].value = precio_str;
            }else{
                var rate        = document.getElementById("rate").value;
                var rate_str    = rate.replaceAll(".", "");
                var rate_str2    = rate_str.replaceAll(",", ".");
                var precio_list = document.getElementById("precio_list").value;
                var resultado   = (parseFloat(precio_list) * parseFloat(rate_str2)).toFixed(2);
                var grand_totalformat = resultado.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});

                document.getElementsByName("cost")[0].value = new Intl.NumberFormat('es-MX').format(resultado);
            }
        });
        $(document).ready(function () {
            $("#discount_product").mask('000', { reverse: true });

        });

        $(document).ready(function () {
            $("#amount_product").mask('00000', { reverse: true });

        });

        let sum = "<?php echo number_format($suma, 2, ',', '.') ?>";

        document.querySelector('#total').innerText = sum.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});

        $(document).ready(function () {
            $("#total").mask('000.000.000.000.000,00', { reverse: true });

        });
        $(document).ready(function () {
            $("#rate").mask('000.000.000.000.000,00', { reverse: true });

        });
        $(document).ready(function () {
            $("#cost").mask('000.000.000.000.000,00', { reverse: true });

        });


    </script>

@endsection

@section('validacion')
    <script>
     $('#dataTable').dataTable( {
        "ordering": false,
        "order": [],
            'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
    } );

        $("#coin").on('change',function(){
            coin = $(this).val();
            window.location = "{{route('quotations.create', [$quotation->id,''])}}"+"/"+coin;
        });


    function sendProduct(){
        if(validacion()){
            document.getElementById("formSendProduct").submit();
        }

    }
    function deliveryNoteSend() {

            window.location = "{{route('quotations.createdeliverynote', [$quotation->id,$coin])}}";

    }

    function refreshrate() {

        let rate = document.getElementById("rate").value;
        window.location = "{{ route('quotations.refreshrate',[$quotation->id,$coin,'']) }}"+"/"+rate;

    }

    function guardarcambios() {

       // let rate = document.getElementById("rate").value;
        let observation = document.getElementById("observation").value;
        let note = document.getElementById("note").value;
        let serie2 = document.getElementById("serie2").value;

          if (observation == ''){
            observation = '-1';
          }
          if (note == ''){
            note = '-1';
          }
          if (serie2 == ''){
            serie2 = '-1';
          }

          

        window.location = "{{ route('quotations.guardarcambios',[$quotation->id,$coin,'','','']) }}"+"/"+observation+"/"+note+"/"+serie2;

    }



    function validate() {

        alert('Debe ingresar al menos un producto para poder continuar');
    }


    function validacion() {

        let amount = document.getElementById("amount_product").value;

        if (amount < 1) {

        alert('La cantidad del Producto debe ser mayor a 1');
        return false;
        }
        else {
            return true;
        }



    }

    </script>

@endsection

@section('consulta')
    <script>
       /* $("#formcoin").hide();
        $("#btnSendNote").hide();

        function deliveryNote(){
            $("#formcoin").show();
            $("#btnSendNote").show();
            $("#btnNote").hide();
        }*/

        function searchCode(){

            let reference_id = document.getElementById("code").value;

            if(reference_id != ""){
                $.ajax({

                url:"{{ route('quotations.listinventory') }}" + '/' + reference_id,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{


                    if(response.length > 0){
                        response.forEach((item, index, object)=>{
                            let {id,description,date} = item;

                           window.location = "{{route('quotations.createproduct', [$quotation->id,$coin,''])}}"+"/"+id;

                        });
                    }else{
                       //alert('No se Encontro este numero de Referencia');
                    }

                },
                error:(xhr)=>{
                   //alert('Presentamos Inconvenientes');
                }
            })
            }

        }
        $("body").toggleClass("sidebar-toggled");
        $(".sidebar").toggleClass("toggled");
        if ($(".sidebar").hasClass("toggled")) {
            $('.sidebar .collapse').collapse('hide');
        };

    </script>
@endsection

