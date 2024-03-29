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
                <div class="card-header" ><h3>Registro de Importe Nota de Crédito</h3></div>

                <div class="card-body" >
                   
                       
                       
                        <div class="form-group row">
                            <label for="date_creditnote" class="col-md-2 col-form-label text-md-right">Fecha:</label>
                            <div class="col-md-4">
                                <input id="date_creditnote" type="date" class="form-control @error('date_creditnote') is-invalid @enderror" name="date_creditnote" value="{{ $creditnote->date_creditnote ?? $datenow }}" readonly required autocomplete="date_creditnote">
    
                                @error('date_creditnote')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                           
                                <label for="client" class="col-md-2 col-form-label text-md-right">Cliente:</label>
                                <div class="col-md-4">
                                    <input id="client" type="text" class="form-control @error('client') is-invalid @enderror" name="client" value="{{ $creditnote->clients['name'] ?? $creditnote->quotations->clients['name'] ?? '' }}" readonly required autocomplete="client">
                                    @error('client')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                        </div>

                        <div class="form-group row">
                            <label for="serie" class="col-md-2 col-form-label text-md-right">N° de Control/Serie:</label>

                            <div class="col-md-3">
                                <input id="serie" type="text" class="form-control @error('serie') is-invalid @enderror" name="serie" value="{{ $creditnote->serie ?? '' }}" readonly required autocomplete="serie">

                                @error('serie')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                           
                            <label for="vendor" class="col-md-3 col-form-label text-md-right">Vendedor:</label>
                            <div class="col-md-4">
                                <input id="vendor" type="text" class="form-control @error('vendor') is-invalid @enderror" name="vendor" value="{{ $creditnote->vendors['name'] ?? $creditnote->quotations->vendors['name']  ?? '' }}" readonly required autocomplete="vendor">
                                @error('vendor')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            
                        </div>
                        
                        
                        <div class="form-group row">
                            <label for="transports" class="col-md-2 col-form-label text-md-right">Transporte/ Tipo de Entrega:</label>
                            <div class="col-md-4">
                                <input id="transport" type="text" class="form-control @error('transport') is-invalid @enderror" name="transport" value="{{ $creditnote->transports['placa'] ?? old('transport') }}" readonly required autocomplete="transport"> 
                           
                                @error('transport')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="observation" class="col-md-2 col-form-label text-md-right">Observaciones:</label>

                            <div class="col-md-4">
                                <input id="observation" type="text" class="form-control @error('observation') is-invalid @enderror" name="observation" value="{{ $creditnote->observation ?? old('observation') }}" readonly required autocomplete="observation">

                                @error('observation')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                       
                        <div class="form-group row">
                            <label for="invoice" class="col-md-2 col-form-label text-md-right">Factura:</label>
                            <div class="col-md-4">
                                <input id="invoice" type="text" class="form-control @error('invoice') is-invalid @enderror" name="invoice" value="{{ $creditnote->quotations['number_invoice'] ?? '' }}" readonly required autocomplete="invoice">
                                @error('invoice')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label  class="col-md-2 col-form-label text-md-right"><h6>Total: </h6></label>
                            <div class="col-md-2 col-form-label text-md-left">
                                <label for="totallabel" id="total"><h3></h3></label>
                            </div>

                        </div>
                        <form id="formSendProduct" method="POST" action="{{ route('creditnotes.storeproduct') }}" enctype="multipart/form-data" onsubmit="return validacion()">
                            @csrf
                            <input id="id_creditnote" type="hidden" class="form-control @error('id_creditnote') is-invalid @enderror" name="id_creditnote" value="{{ $creditnote->id ?? -1}}" readonly required autocomplete="id_creditnote">
                            <input id="id_inventory" type="hidden" class="form-control @error('id_inventory') is-invalid @enderror" name="id_inventory" value="{{ $inventory->id ?? -1 }}" readonly required autocomplete="id_inventory">
                            <input id="coinhidden" type="hidden" class="form-control @error('coin') is-invalid @enderror" name="coin" value="{{ $coin ?? 'bolivares' }}" readonly required autocomplete="coin">
                            <input id="bcv" type="hidden" class="form-control @error('bcv') is-invalid @enderror" name="bcv" value="{{ $bcv ?? $bcv_creditnote_product }}" readonly required autocomplete="bcv">
                            <input id="id_user" type="hidden" class="form-control @error('id_user') is-invalid @enderror" name="id_user" value="{{ Auth::user()->id }}" readonly required autocomplete="id_user">
                       
                        
                        <div class="form-group row" id="formcoin">

                            

                            <label id="coinlabel" for="coin" class="col-md-1 col-form-label text-md-right">Moneda:</label>

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
                            <label for="rate" class="col-md-1 col-form-label text-md-right">Tasa:</label>
                            <div class="col-md-2">
                                <input id="rate" type="text" class="form-control @error('rate') is-invalid @enderror" name="rate" value="{{ number_format(bcdiv($creditnote->rate ?? 0, '1', 2), 2, ',', '.') }}" required autocomplete="rate">
                                @error('rate')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <a href="#" onclick="refreshrate()" title="actualizar tasa"><i class="fa fa-redo-alt"></i></a>  
                            <label  class="col-md-2 col-form-label text-md-right h6">Tasa actual:</label>
                            <div class="col-md-2 col-form-label text-md-left">
                                <label for="tasaactual" id="tasaacutal">{{ number_format(bcdiv(($bcv), '1', 2), 2, ',', '.')}}</label>
                            </div>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="customSwitches">
                                <label class="custom-control-label" for="customSwitches">Auto</label>
                                
                            </div>
                        </div>
                        <br>
                       
                            
                                <div class="form-row col-md-12">
                                    <div class="form-group col-md-2">
                                        <label for="description" >Código</label>
                                        <input id="code" type="text" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ $inventory->code ?? old('code') ?? '' }}" required autocomplete="code" onblur="searchCode()">
                                    </div>
                                   
                                    <div class="form-group col-md-1">
                                        
                                        <a href="" title="Buscar Producto Por Codigo" onclick="searchCode()"><i class="fa fa-search"></i></a>  
                                    
                                            <a href="{{ route('creditnotes.selectproduct',[$creditnote->id,$coin,'productos']) }}" title="Productos"><i class="fa fa-eye"></i></a>  
                                        
                                    </div>
                                    
                                    <div class="form-group col-md-2">
                                        <label for="description" >Descripción</label>
                                        <input id="description" type="text" class="form-control @error('description') is-invalid @enderror" name="description" value="{{ $inventory->products['description'] ?? old('description') ?? '' }}" required autocomplete="description">
        
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
                                       
                                        @endif
                                    </div>
                                    <div class="form-group col-md-2">
                                        @if(isset($inventory->products['price']) && (isset($creditnote->rate)) && ($inventory->products['money'] != 'Bs') && ($coin == 'bolivares')) 
                                            <?php 
                                                
                                                $product_Bs = $inventory->products['price'] * $creditnote->rate;
                                               
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
                                        @if (empty($inventories_creditnotes))
                                        @else
                                        <?php
                                            $suma = 0.00;
                                        ?>
                                       
                                            @foreach ($inventories_creditnotes as $var)

                                            <?php
                                                if($coin != 'bolivares'){
                                                    $var->price = bcdiv(($var->price / ($var->rate ?? 1)), '1', 2);
                                                }
                                                
                                                $percentage = (($var->price * $var->amount_creditnote) * $var->discount)/100;

                                                $total_less_percentage = ($var->price * $var->amount_creditnote) - $percentage;


                                            ?>
                                                <tr>
                                                <td style="text-align: right">{{ $var->code}}</td>
                                                @if($var->exento == '1')
                                                    <td style="text-align: right">{{ $var->description}} (E)</td>
                                                @else
                                                    <td style="text-align: right">{{ $var->description}}</td>
                                                @endif
                                                
                                                <td style="text-align: right">{{ $var->amount_creditnote}}</td>
                                                <td style="text-align: right">{{number_format($var->price, 2, ',', '.')}}</td>
                                                <td style="text-align: right">{{number_format($var->discount, 0, '', '.')}}%</td>
                                                
                                                <td style="text-align: right">{{number_format($total_less_percentage, 2, ',', '.')}}</td>
                                               

                                                <?php
                                                    $suma += $total_less_percentage;
                                                    
                                                ?>
                                                    <td style="text-align: right">
                                                        <a href="{{ route('creditnotes.productedit',[$var->credit_note_details_id,$coin]) }}" title="Editar"><i class="fa fa-edit"></i></a>  
                                                        <a href="#" class="delete" data-id={{$var->credit_note_details_id}} data-description={{$var->description}} data-id-creditnote={{$creditnote->id}} data-coin={{$coin}} data-toggle="modal" data-target="#deleteModal" title="Eliminar"><i class="fa fa-trash text-danger"></i></a>  
                                                    </td>
                                            
                                                </tr>
                                            @endforeach

                                            <?php
                                                
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
                               
                                @if (empty($inventories_creditnotes))
                    
                                    <div class="col-sm-4">   
                                        <a href="{{ route('quotations.createfacturar_after',[$creditnote->id_quotation ?? $creditnote->id,$coin ?? 'bolivares']) }}" id="btnfacturar" name="btnfacturar" class="btn btn-success" title="facturar">Volver a Factura</a>
                                    </div>
                                    
                                    <div class="col-sm-4" style="display: none;">   
                                        <a href="{{ route('creditnotes.createfacturar',[$creditnote->id,$coin]) }}" id="btnfacturar" name="btnfacturar" class="btn btn-success" title="facturar">Generar Nota de Débito</a>  
                                    </div> 
                                @else
                                <div class="col-sm-4">   
                                    <a href="{{ route('creditnotes') }}" id="btnfacturar" name="btnfacturar" class="btn btn-danger" title="facturar">Volver</a>
                                </div>
                                @endif
                            
                            <div class="col-sm-4">
                                @if ($existe_comprobante > 0)
                                    @if (isset($inventories_creditnotes))
                                    <a href="{{ route('movements.debitnote',[$creditnote->id,$coin]) }}" id="btnmovement" name="btnmovement" class="btn btn-light" title="movement">Ver Movimiento de Cuenta</a>  
                                    @endif
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
            <form action="{{ route('creditnotes.deleteProduct') }}" method="post">
                @csrf
                @method('DELETE')
                <input id="id_creditnote_product_modal" type="hidden" class="form-control @error('id_creditnote_product_modal') is-invalid @enderror" name="id_creditnote_product_modal" readonly required autocomplete="id_creditnote_product_modal">
                <input id="id_creditnote_modal" type="hidden" class="form-control @error('id_creditnote_modal') is-invalid @enderror" name="id_creditnote_modal" readonly required autocomplete="id_creditnote_modal">
                <input id="coin_modal" type="hidden" class="form-control @error('coin_modal') is-invalid @enderror" name="coin_modal" readonly required autocomplete="coin_modal">
                       
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

@section('creditnote_create')
    
    <script>
     
        $(document).ready(function () {
            $("#discount_product").mask('000', { reverse: true });
            
        });
        
        $(document).ready(function () {
            $("#amount_product").mask('00000', { reverse: true });
            
        });
        
        let sum = "<?php echo number_format($suma, 2, ',', '.') ?>";
      
        document.querySelector('#total').innerText = sum.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});;

        $(document).ready(function () {
            $("#total").mask('000.000.000.000.000,00', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#rate").mask('000.000.000.000.000,00', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#cost").mask('000.000.000.000.000,00', { reverse: true });
            
        });
        $("#code").keydown(function(event){ 
            if(event.which == 13){   // teclear enter
                /*sendProduct(callback);
                //Una funcion anonima para retornar el resultado despues de 1 segundo
                searchCode();*/
                    //alert(12); 
                 searchCode(); 
                //alert(13);             
            }
       });
      
        /*$("#description").on('change',function(){
          alert('change');
        });*/

        checkbox = document.getElementById('customSwitches'); // retoma el valor anterior
        checkbox.checked = eval(window.localStorage.getItem(checkbox.id));
        checkbox.addEventListener('change', function(){
            window.localStorage.setItem(checkbox.id, checkbox.checked);
        })

        if( $('#customSwitches').prop('checked')) { // validar seleccionado
            var value=$.trim($("#description").val()); // valida el campo si esta lleno
            if(value.length>0 ){
                //alert('enviando');
                sendProduct();
                $("#description").val('');
            }

          document.getElementById("code").focus();   
        }

        $(document).on('click','.delete',function(){
         let id = $(this).attr('data-id');
         let id_creditnote = $(this).attr('data-id-creditnote');
         let coin = $(this).attr('data-coin');
         let description = $(this).attr('data-description');

         $('#id_creditnote_product_modal').val(id);
         $('#id_creditnote_modal').val(id_creditnote);
         $('#coin_modal').val(coin);
         $('#description_modal').val(description);
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
            window.location = "{{route('creditnotes.create', [$creditnote->id,'',''])}}"+"/"+coin+"/"+"{{$inventory->id ?? ''}}";
        });


    function sendProduct(){
        if(validacion()){
            document.getElementById("formSendProduct").submit();
        }
        
    }
   
    function refreshrate() {
       
        let rate = document.getElementById("rate").value; 
        window.location = "{{ route('creditnotes.refreshrate',[$creditnote->id,$coin,'']) }}"+"/"+rate;
       
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


    function alertad() {
       
       alert('envia');
        //console.log("enviar");          
   }



    function searchCode2(callback){
            
            let reference_id = document.getElementById("code").value; 
            
            if(reference_id != ""){
                $.ajax({
                
                url:"{{ route('creditnotes.listinventory') }}" + '/' + reference_id,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                 
                    
                    if(response.length > 0){
                        response.forEach((item, index, object)=>{
                            let {id,description,date} = item;
                          
                           window.location = "{{route('creditnotes.createproduct', [$creditnote->id,$coin,''])}}"+"/"+id;
                           
                        });
                    }else{
                        window.location = "{{route('creditnotes.create', [$creditnote->id,$coin,''])}}";
                       //alert('No se Encontro este numero de Referencia');
                    }
                   
                },
                error:(xhr)=>{
                   //alert('Presentamos Inconvenientes');
                }
            })
            }
           //alert('busca');
            //console.log("buscar");

           callback();
        }  
    
    </script> 

@endsection    

@section('consulta')
    <script>

        function searchCode(){ 
            let reference_id = document.getElementById("code").value; 
            if(reference_id != ""){
                $.ajax({
                url:"{{ route('creditnotes.listinventory') }}" + '/' + reference_id,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                
                    if(response.length > 0){
                        response.forEach((item, index, object)=>{
                            let {id,description,date} = item;
                          
                           window.location = "{{route('creditnotes.createproduct', [$creditnote->id,$coin,''])}}"+"/"+id;
                           
                        });
                    }else{

                          window.location = "{{route('creditnotes.create', [$creditnote->id,$coin,''])}}";
                       //alert('No se Encontro este numero de Referencia');
                    }
                   
                },
                error:(xhr)=>{
                   //alert('Presentamos Inconvenientes');
                }
            })
            }
           
        }
        

    </script>
@endsection

