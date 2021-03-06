@extends('admin.layouts.dashboard')

@section('content')
  
    <!-- container-fluid -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="row py-lg-2">
            <div class="col-md-6">
                <h2>Editar Producto !!</h2>
            </div>

        </div>
    </div>
    <!-- /container-fluid -->

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

    <div class="card shadow mb-4">
        <div class="card-body">
            <form  method="POST"   action="{{ route('productsreceipt.update',$product->id) }}" enctype="multipart/form-data" >
                @method('PATCH')
                @csrf()
                <div class="container py-2">
                    <div class="row">
                        <div class="col-12 ">
                            <form >
                               
                                <div class="form-group row">
                                <label for="description" class="col-md-2 col-form-label text-md-right">Descripción</label>
        
                                <div class="col-md-4">
                                    <input id="description" type="text" class="form-control @error('description') is-invalid @enderror" name="description" value="{{ $product->description }}" required autocomplete="description">
    
                                    @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                               </div>
                                <div class="form-group row">
                                        <label for="segment_id" class="col-md-2 col-form-label text-md-right">Segmento</label>
                                        <div class="col-md-4">   
                                            <select id="segment" name="segment" class="form-control" required>
                                                @foreach($segments as $segment)
                                                    @if ( $product->segment_id == $segment->id   )
                                                        <option  selected style="backgroud-color:blue;" value="{{ $segment->id }}"><strong>{{ $segment->description }}</strong></option>
                                                    @endif
                                                @endforeach
                                                <option class="hidden" disabled data-color="#A0522D" value="-1">------------------</option>
                                                @foreach($segments as $var2)
                                                    <option value="{{ $var2->id }}" >
                                                        {{ $var2->description }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div> 
                                        <div style="display:none;">
                                        <label for="subsegment" class="col-md-2 col-form-label text-md-right">Sub Segmento</label>
                                        <div class="col-md-4">
                                            <select id="subsegment" name="Subsegment" class="form-control" >
                                                @foreach($subsegments as $subsegment)
                                                    @if ( $product->subsegment_id == $subsegment->id   )
                                                        <option  selected style="backgroud-color:blue;" value="{{ $subsegment->id }}"><strong>{{ $subsegment->description }}</strong></option>
                                                    @endif
                                                @endforeach
                                                <option class="hidden" disabled data-color="#A0522D" value="-1">------------------</option>
                                                @foreach($subsegments as $var2)
                                                    <option value="{{ $var2->id }}" >
                                                        {{ $var2->description }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> 
                                </div>
                                <div class="form-group row" style="display:none;">
                                    <label for="twosubsegment" class="col-md-2 col-form-label text-md-right">Segundo Sub Segmento</label>
                                    <div class="col-md-4">
                                        <select id="twosubsegment" name="twoSubsegment" class="form-control" >
                                                @if ( isset($product->twosubsegment_id) )
                                                    <option  selected style="backgroud-color:blue;" value="{{ $product->twosubsegments['id'] }}"><strong>{{ $product->twosubsegments['description']}}</strong></option>
                                                @else
                                                    <option  selected style="backgroud-color:blue;" value="null"><strong>No tiene</strong></option>
                                                @endif
                                                <option disabled  style="backgroud-color:blue;"><strong>------------</strong></option>
                                                <option style="backgroud-color:blue;" value="null"><strong>Ninguno</strong></option>
                                                @foreach($twosubsegments as $twosubsegment)
                                                    <option style="backgroud-color:blue;" value="{{ $twosubsegment->id }}"><strong>{{ $twosubsegment->description }}</strong></option>
                                                @endforeach
                                        </select>
                                    </div> 
                                    <label for="threesubsegment" class="col-md-2 col-form-label text-md-right">Tercer Sub Segmento</label>
                                    <div class="col-md-4">
                                        <select id="threesubsegment" name="threeSubsegment" class="form-control" >
                                                @if ( isset($product->threesubsegments['id']) )
                                                    <option  selected style="backgroud-color:blue;" value="{{ $product->threesubsegments['id'] }}"><strong>{{ $product->threesubsegments['description']}}</strong></option>
                                                @else
                                                    <option  selected style="backgroud-color:blue;" value="null"><strong>No tiene</strong></option>
                                                @endif
                                                <option disabled  style="backgroud-color:blue;"><strong>------------</strong></option>
                                                <option style="backgroud-color:blue;" value="null"><strong>Ninguno</strong></option>
                                                @foreach($threesubsegments as $threesubsegment)
                                                    <option style="backgroud-color:blue;" value="{{ $threesubsegment->id }}"><strong>{{ $threesubsegment->description }}</strong></option>
                                                @endforeach
                                        </select>
                                    </div> 
                            </div>
           
        
                                <div class="form-group row">
                                    <div style="display:none;">
                                    <label for="unitofmeasure" class="col-md-2 col-form-label text-md-right">Unidad de Medida</label>
                                     <div class="col-md-4">
                                        <select id="unit_of_measure_id" name="unit_of_measure_id" class="form-control" required>
                                            @foreach($unitofmeasures as $var)
                                                @if ( $product->unit_of_measure_id == $var->id   )
                                                    <option  selected style="backgroud-color:blue;" value="{{ $var->id }}"><strong>{{ $var->description }}</strong></option>
                                                @endif
                                            @endforeach
                                            <option class="hidden" disabled data-color="#A0522D" value="-1">------------------</option>
                                            @foreach($unitofmeasures as $var2)
                                                <option value="{{ $var2['id'] }}" >
                                                    {{ $var2['description'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div> 
                                </div> 
                                    <label for="code_comercial" class="col-md-2 col-form-label text-md-right">Código Comercial</label>
        
                                    <div class="col-md-4">
                                        <input id="code_comercial" type="text" class="form-control @error('code_comercial') is-invalid @enderror" name="code_comercial" value="{{ $product->code_comercial }}" required autocomplete="code_comercial">
        
                                        @error('code_comercial')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
        
                               
                                <div class="form-group row">
                                    <label for="type" class="col-md-2 col-form-label text-md-right">Tipo</label>
                                    <div class="col-md-4">
                                        <select class="form-control" id="type" name="type" title="type">
                                            @if($product->type == "MERCANCIA")
                                                <option value="MERCANCIA">Mercancía</option>
                                            @elseif($product->type == "MATERIAP")
                                            <option value="MATERIAP">Materia Prima</option> 
                                            @elseif($product->type == "SERVICIO")
                                            <option value="SERVICIO">Servicio</option>
                                            @endif
                                           
                                            <option value="nulo">----------------</option>
                                            
                                            <div class="dropdown">
                                                <option value="MERCANCIA">Mercancía</option>
                                                <option value="MATERIAP">Materia Prima</option> 
                                                <option value="SERVICIO">Servicio</option>
                                            </div>
                                            
                                            
                                        </select>
                                    </div>
                                  
                                </div>
                                <div class="form-group row">
                                    <label for="price" class="col-md-2 col-form-label text-md-right">Precio</label>
        
                                    <div class="col-md-4">
                                        <input id="price" type="text" class="form-control @error('price') is-invalid @enderror" name="price" value="{{ $product->price }}" required autocomplete="price">
        
                                        @error('price')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div style="display:none;">
                                    <label for="price_buy" class="col-md-2 col-form-label text-md-right">Precio Compra</label>
        
                                    <div class="col-md-4">
                                        <input id="price_buy" type="text" class="form-control @error('price_buy') is-invalid @enderror" name="price_buy" value="{{ $product->price_buy }}" required autocomplete="price_buy">
        
                                        @error('price_buy')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    </div> 
                                </div>
        
                                <div class="form-group row" style="display:none;">
                                    <label for="lote" class="col-md-2 col-form-label text-md-right">Lote</label>
        
                                    <div class="col-md-4">
                                        <input id="lote" type="text" class="form-control @error('lote') is-invalid @enderror" name="lote" value="{{ $product->lote ?? old('lote') }}" autocomplete="lote">
        
                                        @error('lote')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <label for="fecha_vencimiento" class="col-md-2 col-form-label text-md-right">Fecha de Vencimiento</label>
                                    <div class="col-md-4">
                                        <input id="fecha_vencimiento" type="text" class="form-control @error('fecha_vencimiento') is-invalid @enderror" name="fecha_vencimiento" value="{{ $product->date_expirate ?? old('fecha_vencimiento') }}" autocomplete="fecha_vencimiento">
        
                                        @error('fecha_vencimiento')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>                    
                                </div>
                                
                                <div class="form-group row" style="display:none;">
                                    <label for="cost_average" class="col-md-2 col-form-label text-md-right">Costo Promedio</label>
        
                                    <div class="col-md-4">
                                        <input id="cost_average" type="text" class="form-control @error('cost_average') is-invalid @enderror" name="cost_average" value="{{ $product->cost_average }}" required autocomplete="cost_average">
        
                                        @error('cost_average')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <label for="account" class="col-md-2 col-form-label text-md-right">Cuenta Asociada:</label>
                        
                                    <div class="col-md-4">
                                    <select id="id_account"  name="id_account" class="form-control">
                                        @if (isset($product->id_account))
                                        <option value="actual">
                                            {{ $product->accounts['description'] ?? ''}}
                                        </option>
                                        @else

                                        @endif
                                        <option value="">Seleccione una Cuenta</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}">
                                                {{ $account->description }}
                                            </option>
                                        @endforeach
                                        </select>

                                        @if ($errors->has('account_id'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('account_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                               
                                <div class="form-group row">
                                    <label for="money" class="col-md-2 col-form-label text-md-right">Moneda</label>
        
                                    <div class="col-md-4">
                                        <select class="form-control" id="money" name="money" title="money">
                                            @if($product->money == "D")
                                                <option value="D">Dolar</option>
                                            @else
                                                <option value="Bs">Bolívares</option>
                                            @endif
                                            <option value="nulo">----------------</option>
                                            
                                            <div class="dropdown">
                                                <option value="D">Dolar</option>
                                                <option value="Bs">Bolívares</option>
                                            </div>
                                            
                                            
                                        </select>
                                    </div>
                                    <label for="exento" class="col-md-2 col-form-label text-md-right">exento</label>
                                    <div class="form-check">
                                        @if($product->exento == "1")
                                            <input class="form-check-input position-static" type="checkbox" id="exento" name="exento" value="1" checked aria-label="...">
                                        @else
                                            <input class="form-check-input position-static" type="checkbox" id="exento" name="exento"  aria-label="...">
                                        @endif
                                    </div>
                                    <div style="display:none;">
                                    <label for="islr" class="col-md-1 col-form-label text-md-right">Islr</label>
                                    <div class="form-check">
                                        @if($product->islr == "1")
                                            <input class="form-check-input position-static" type="checkbox" id="islr" name="islr" value="1" checked aria-label="...">
                                        @else
                                            <input class="form-check-input position-static" type="checkbox" id="islr" name="islr"  aria-label="...">
                                        @endif
                                    </div>
                                </div>
                                </div>
                               
                                <div class="form-group row">
                                    <div style="display:none;">
                                    <label for="special_impuesto" class="col-md-2 col-form-label text-md-right">Impuesto Especial</label>
        
                                    <div class="col-md-4">
                                        <input id="special_impuesto" type="text" class="form-control @error('special_impuesto') is-invalid @enderror" name="special_impuesto" value="{{ $product->special_impuesto }}" required autocomplete="special_impuesto">
        
                                        @error('special_impuesto')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                    <label for="rol" class="col-md-2 col-form-label text-md-right">Status</label>
                
                                    <div class="col-md-4">
                                        <select class="form-control" id="status" name="status" title="status">
                                            @if($product->status == 1)
                                                <option value="1">Activo</option>
                                            @else
                                                <option value="0">Inactivo</option>
                                            @endif
                                            <option value="nulo">----------------</option>
                                            
                                            <div class="dropdown">
                                                <option value="1">Activo</option>
                                                <option value="0">Inactivo</option>
                                            </div>
                                            
                                            
                                        </select>
                                    </div>
                                </div>
                                
                                <br>
                                <div class="form-group row mb-0">
                                    <div class="col-md-3 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                           Actualizar Producto
                                        </button>
                                    </div>
                                    <div class="col-md-2">
                                        <a href="{{ route('products') }}" id="btnfacturar" name="btnfacturar" class="btn btn-danger" title="facturar">Volver</a>  
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
 @endsection
 @section('validacion')
    <script>    
        $(document).ready(function () {
            $("#price").mask('000.000.000.000.000,00', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#price_buy").mask('000.000.000.000.000,00', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#cost_average").mask('000.000.000.000.000,00', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#special_impuesto").mask('000.000.000.000.000,00', { reverse: true });
            
        });

	
    </script>
@endsection

@section('product_edit')
    <script>
            
            $("#segment").on('change',function(){
                var segment_id = $(this).val();
                $("#subsegment").val("");
               
                // alert(segment_id);
                getSubsegment(segment_id);
            });

        function getSubsegment(segment_id){
            
            $.ajax({
                url:"{{ route('subsegment.list') }}" + '/' + segment_id,
             
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                    let subsegment = $("#subsegment");
                    let htmlOptions = `<option value='' >Seleccione..</option>`;
                    // console.clear();
                    if(response.length > 0){
                        response.forEach((item, index, object)=>{
                            let {id,description} = item;
                            htmlOptions += `<option value='${id}' {{ old('Subsegment') == '${id}' ? 'selected' : '' }}>${description}</option>`

                        });
                    }
                    //console.clear();
                    // console.log(htmlOptions);
                    subsegment.html('');
                    subsegment.html(htmlOptions);
                
                    
                
                },
                error:(xhr)=>{
                    alert('Presentamos inconvenientes al consultar los datos');
                }
            })
        }

        $("#subsegment").on('change',function(){
                var subsegment_id = $(this).val();
                //$("#twosubsegment option").remove();
                getTwoSubsegment(subsegment_id);
            });

     
    
            function getTwoSubsegment(subsegment_id){
            $.ajax({
                url:"{{ route('products.listtwosubsegment','') }}" + '/' + subsegment_id,
             
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                    let twosubsegment = $("#twosubsegment");
                    let htmlOptions = `<option value='' >Seleccione..</option>`;
                    // console.clear();
                    if(response.length > 0){
                        response.forEach((item, index, object)=>{
                            let {id,description} = item;
                            htmlOptions += `<option value='${id}' {{ old('TwoSubsegment') == '${id}' ? 'selected' : '' }}>${description}</option>`

                        });
                    }
                    //console.clear();
                    // console.log(htmlOptions);
                    twosubsegment.html('');
                    twosubsegment.html(htmlOptions);
                
                    
                
                },
                error:(xhr)=>{
                    alert('Presentamos inconvenientes al consultar los datos');
                }
            })
        }
        $("#twosubsegment").on('change',function(){
                var twosubsegment_id = $(this).val();
                $("#threesubsegment").val("");

                getThreeSubsegment(twosubsegment_id);
            });

     
    
            function getThreeSubsegment(twosubsegment_id){
            
            $.ajax({
                url:"{{ route('products.listthreesubsegment','') }}" + '/' + twosubsegment_id,
             
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                    let subsegment = $("#threesubsegment");
                    let htmlOptions = `<option value='' >Seleccione..</option>`;
                    // console.clear();
                    if(response.length > 0){
                        response.forEach((item, index, object)=>{
                            let {id,description} = item;
                            htmlOptions += `<option value='${id}' {{ old('ThreeSubsegment') == '${id}' ? 'selected' : '' }}>${description}</option>`

                        });
                    }
                    //console.clear();
                    // console.log(htmlOptions);
                    subsegment.html('');
                    subsegment.html(htmlOptions);
                
                    
                
                },
                error:(xhr)=>{
                    alert('Presentamos inconvenientes al consultar los datos');
                }
            })
        }



    </script>
@endsection