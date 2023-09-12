@extends('admin.layouts.dashboard')

@section('content')

<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
        <div class="col-sm-12">
            <h2>Materia prima para Combo {{$combo->id}} {{$combo->description}}</h2>
        </div>
    </div>
  
    <div class="row py-lg-2">

        <div class="col-sm-10">
             <input type="button" title="Agregar" value="Guardar Cambios" class="btn btn-primary float-md-right" role="button" aria-pressed="true"  onclick="formSend();" >
        </div>
        <div class="col-sm-2">
            <a href="{{ route('combos') }}" class="btn btn-danger">
                Volver
            </a>
        </div>
    </div>
</div>
  <!-- /.container-fluid -->
  {{-- VALIDACIONES-RESPUESTA--}}
  @include('admin.layouts.success')   {{-- SAVE --}}
  @include('admin.layouts.danger')    {{-- EDITAR --}}
  @include('admin.layouts.delete')    {{-- DELELTE --}}
  {{-- VALIDACIONES-RESPUESTA --}}
<!-- DataTales Example -->
<form id="formSend" method="POST" action="{{ route('combos.store_assign') }}" enctype="multipart/form-data">
    @csrf
    <input id="id_products" type="hidden" class="form-control @error('id_products') is-invalid @enderror" name="id_products"  readonly required autocomplete="id_products">
    <input id="id_combo" type="hidden" class="form-control @error('id_combo') is-invalid @enderror" name="id_combo" value="{{ $id_combo }}" readonly required autocomplete="id_combo">
    
    <input id="combo_products" type="hidden" class="form-control @error('combo_products') is-invalid @enderror" name="combo_products" readonly required autocomplete="combo_products">
              
    
    
<div class="card shadow mb-4">
    
    <div class="card-body">
        <div class="container">
            @if (session('flash'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{session('flash')}}
                    <button type="button" class="close" data-dismiss="alert" aria-label="close">
                        <span aria-hidden="true">&times; </span>
                    </button>
                </div>   
            @endif
            <div class="form-group row">
                <label for="price" class="col-md-2 col-form-label text-md-right">Precio de Venta:</label>

                <div class="col-md-2">
                    <input  style="text-align: right;" id="price" type="text" class="form-control @error('price') is-invalid @enderror" name="price" value="{{ old('price') }}" required autocomplete="price">

                    @error('price')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <label for="price_buy" class="col-md-2 col-form-label text-md-right">Precio de Compra:</label>

                <div class="col-md-2">
                    <input  style="text-align: right;" id="price_buy" type="text" class="form-control @error('price_buy') is-invalid @enderror" name="price_buy" value="{{ old('price_buy') }}" required autocomplete="price_buy">

                    @error('price_buy')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" checked id="customSwitches" name="updatePrices">
                    <label class="custom-control-label" for="customSwitches">Actualizar Precios</label>
                    
                </div>
            </div>
        </div>
        <div class="table-responsive">
        <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
            <tr> 
                <th></th>
                <th class="text-center"width="1%">Cantidad</th>
                <th class="text-center"width="1%">ID</th>
                <th class="text-center"width="1%">Código Comercial</th>
                <th>Descripción</th>
                <th class="text-center" width="1%">Costo Unidad</th>
                <th class="text-center" width="1%">Costo Total</th>
                <th class="text-center" width="1%">Moneda</th>
                <th class="text-center" >Segmento</th>
                <th class="text-center" width="1%">Stock</th>
                <th class="text-left" width="1%">Unidad/Med</th>
                
                
            </tr>
            </thead>
            
            <tbody>
                @if (empty($products))
               
                @else  

                    @foreach ($products as $product)
                    <?php
                    $product->enc = 0;   
                    ?>
                       @if(isset($combo_products))
                            @foreach ($combo_products as $productwo)

                                    @if ($productwo->id_product == $product->id) 

                                            <tr>
                                                <td>
                                                    <input onclick="selectProduct({{ $product }});" type="checkbox" id="flexCheckChecked{{$product->id}}">                        
                                                </td>
                                                <td >
                                                    <input onkeyup="noespac(this)" style="text-align: right;" id="amount{{ $product->id }}" onblur="updateAmount({{$product}})" type="text" class="form-control @error('amount{{ $product->id }}') is-invalid @enderror" name="amount{{ $product->id }}" placeholder="0" autocomplete="amount{{ $product->id }}">
                                                </td>
                                                <td class="text-center">{{$product->id}}</td>
                                                <td class="text-center">{{$product->code_comercial ?? ''}}</td>
                                                <td>{{$product->description ?? ''}}</td>
                                                <td class="text-right">{{ $product->price_buy ?? 0}}</td>
                                                <td class="text-right">{{ $product->price_buy * $productwo->amount_per_product ?? 0}}</td>
                                                @if($product->money == "D")
                                                    <td class="text-center">USD</td>
                                                @else
                                                    <td class="text-center">Bs.</td>
                                                @endif
                                                <td>{{$product->segments['description'] ?? ''}}</td>
     
                                                <td class="text-right">{{$productwo->amount}}</td> 
                                                <td class="text-left" style="text-align: left;">{{$product->unit_of_measure_id ?? ''}}</td> 
                                            </tr> 
                                            <?php
                                            $product->enc = 1;
                                            ?>   

                                    @endif                                   
        
                            @endforeach
                                
                        @endif
                            
                    @endforeach  
                    
                    
                    @foreach ($products as $product)


                        @if ($product->enc == 0) 

                        <tr>
                            <td>
                                <input onclick="selectProduct({{ $product }});" type="checkbox" id="flexCheckChecked{{$product->id}}">                        
                            </td>
                            <td>
                                <input onkeyup="noespac(this)" style="text-align: right;" id="amount{{ $product->id }}" onblur="updateAmount({{$product}})" type="text" class="form-control amountp @error('amount{{ $product->id }}') is-invalid @enderror" name="amount{{ $product->id }}" data-amountid="amountold{{ $product->id }}" placeholder="0" autocomplete="amount{{ $product->id }}">
                                <input onkeyup="noespac(this)" style="text-align: right; display:none;" id="amountold{{ $product->id }}" type="text" class="form-control amountold" name="amountold{{ $product->id }}">
                            </td>
                            <td class="text-center">{{$product->id}}</td>
                            <td class="text-center">{{$product->code_comercial ?? ''}}</td>
                            <td>{{$product->description ?? ''}}</td>
                            <td class="text-right">{{ $product->price_buy ?? 0}}</td>
                            <td class="text-right">{{ $product->price_buy ?? 0}}</td>
                            @if($product->money == "D")
                                <td class="text-center">Dolar</td>
                            @else
                                <td class="text-center">Bolívar</td>
                            @endif
                            <td>{{$product->segments['description'] ?? ''}}</td>
                            <td class="text-right">{{$product->amount}}</td> 
                            <td class="text-left" style="text-align: left;">{{$product->unit_of_measure_id ?? ''}}</td> 
                        </tr>          
                        @endif 

                    @endforeach   
                @endif



            </tbody>
        </table>
        
        </div>
    </div>
</div>

</form>
@endsection
@section('javascript')
    
    <script>

        $('#dataTable').DataTable({
            "ordering": true,
            "order": [[ 0, 'asc' ]],
            'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
        });

        function formSend(){
            document.getElementById("formSend").submit();       
        }

        
        let products = [];
        var controller_add = true;
        var value_old = 0;

        var bcv = "{{$bcv ?? 1}}";
        
        function selectProduct(product){

            var isChecked = document.getElementById('flexCheckChecked'+product.id).checked;
            
            if(isChecked){
                $('#amount'+product.id).removeAttr('disabled');
                document.getElementById('amount'+product.id).value = "0";

            }else{
                var amount = document.getElementById('amount'+product.id).value;
                
                if (amount > 0){

                    var price_form = document.getElementById("price").value;
                    var price_buy_form = document.getElementById("price_buy").value; 
                    
                    var price = product.price * amount;
                    var price_buy = product.price_buy * amount;
                    
                    if (price_form > 0 ) {
                        document.getElementById("price").value = parseFloat(price_form) - parseFloat(price);
                    } else {
                        document.getElementById("price").value = 0;
                    }
                    if (price_buy_form > 0 ) {
                    document.getElementById("price_buy").value = parseFloat(price_buy_form) - parseFloat(price_buy);
                    } else {
                    document.getElementById("price_buy").value = 0;   
                    }

                } else {

                    document.getElementById('amount'+product.id).value = 0;
                
                }
                
                $('#amount'+product.id).attr('disabled', true);
                
            }
            
            addProduct(product.id);
        }



        function updateAmount(product){

            var isChecked = document.getElementById('flexCheckChecked'+product.id).checked;
             
            var amount = document.getElementById('amount'+product.id).value;
            var value_old = document.getElementById('amountold'+product.id).value;

            var price_form = document.getElementById("price").value;
            var price_buy_form = document.getElementById("price_buy").value; 
            
            var price = product.price * amount;
            var price_buy =product.price_buy * amount;

            if (isChecked == true) {

                if (price_form >= 0){
                   document.getElementById("price").value = parseFloat(price_form) + parseFloat(price);
                } else {
                   document.getElementById("price").value = parseFloat(price_form);
                }

                if (price_buy_form >= 0){
                    document.getElementById("price_buy").value = parseFloat(price_buy_form) + parseFloat(price_buy);
                } else {
                    document.getElementById("price_buy").value = parseFloat(price_form);
                }
                /*updateValuePrice(isChecked,product.price * amount,product.price_buy * amount);*/
            
            } else {
                
                /*updateValuePrice(isChecked,product.price * amount,product.price_buy * amount);*/

            }
            
        }

       /* function updateValuePrice(isChecked,price,price_buy){
            

            var price_form = document.getElementById("price").value;
                var price_buy_form = document.getElementById("price_buy").value; 

            
            if(isChecked){
                document.getElementById("price").value = parseFloat(price_form) + parseFloat(price);
                document.getElementById("price_buy").value = parseFloat(price_buy_form) + parseFloat(price_buy);
            }else{
                document.getElementById("price").value = (parseFloat(price_form) - parseFloat(price)) ; 
                document.getElementById("price_buy").value = (parseFloat(price_buy_form) - parseFloat(price_buy)) 
            }
           
        }*/
        

        
        //esta funcion agrega y elimina productos de la lista que se anadiran al combo
        function addProduct(id_product){
            
            products.forEach(function(element, index, object) {
                if(element == id_product){
                    //elimina el elemento al encontrarlo
                    object.splice(index, 1);
                    controller_add = false;
                }
            });
            
            if(controller_add){
                //agrega el elemento si no existe en la lista de products
                products.push(id_product);
            }else{
                controller_add = true;
            }
            
            document.getElementById("id_products").value = products;
        }


        
        /*$(".amountp").on('change',function(){
            var amountid = $(this).attr('data-amountid');
            var amount = $(this).val();

            $('#'+amountid).val(amount);

        });*/

        function amountold(id){

            var amount = $('#amount'+id).val();
            $('#amountold'+id).val(amount);
            
        }

       /* function newFormat(old_format){
            var montoFormat = old_format.replace(/[$.]/g,'');

            return montoFormat.replace(/[,]/g,'.');       
        }*/

    </script> 
        

    @if (isset($combo_products))
        <?php
            //Aqui se inicializa los precios y toda la pagina
            $total_price = 0;  
            $total_price_buy = 0;        
        ?>
        @foreach ($combo_products as $combo)
            <?php
               // if((isset($combo->products['money'])) && ($combo->products['money'] == 'D')){
                    $total_price += $combo->products['price'] ?? 0;  
                    $total_price_buy += $combo->products['price_buy'] * $combo->amount_per_product ?? 0; 
                /*}else{
                    $total_price += ($combo->products['price'] ?? 0) / ($bcv ?? 1);  
                    $total_price_buy += ($combo->products['price_buy'] ?? 0) / ($bcv ?? 1); 
                }*/
                    
            ?>
            <script>
                //aqui seleccionamos los productos que ya tenga el combo y los asignamos a la lista de productos
                products.push("{{ $combo->id_product }}");
                document.getElementById("combo_products").value = products;
                document.getElementById("flexCheckChecked{{ $combo->id_product }}").checked = true;
                document.getElementById("amount{{ $combo->id_product }}").value = "{{ $combo->amount_per_product}}";
                document.getElementById("id_products").value = products;
            </script> 
        @endforeach
        <script>
            //aqui asignamos los precios
            document.getElementById("price").value = "{{ number_format($total_price, 3, ',', '.') }}";
            document.getElementById("price_buy").value = "{{ number_format($total_price_buy, 3, ',', '.') }}";
        </script> 
    @endif
@endsection
