
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
                <div class="card-header text-center font-weight-bold h3">Registro de Productos.</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                        @csrf

                        <input id="id_user" type="hidden" class="form-control @error('id_user') is-invalid @enderror" name="id_user" value="{{ Auth::user()->id }}" readonly required autocomplete="id_user">
                       
                        <div class="form-group row">
                            <label for="type" class="col-md-2 col-form-label text-md-right">Tipo</label>
                            <div class="col-md-4">
                            <select class="form-control" name="type" id="type">
                                <option value="MERCANCIA">Mercancía</option>
                                <option value="MATERIAP">Materia Prima</option>
                                <option value="SERVICIO">Servicio</option>
                            </select>
                            </div>
                            <label for="description" class="col-md-2 col-form-label text-md-right">Descripción</label>

                            <div class="col-md-4">
                                <input id="description" type="text" class="form-control @error('description') is-invalid @enderror" name="description" value="{{ old('description') }}" required autocomplete="description">

                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                       
                        <div class="form-group row">
                                    
                            <label for="segment" class="col-md-2 col-form-label text-md-right">Segmento</label>
                        
                            <div class="col-md-4">
                            <select id="segment"  name="segment" class="form-control" required>
                                <option value="">Seleccione un Segmento</option>
                                @foreach($segments as $index => $value)
                                    <option value="{{ $index }}" {{ old('Segment') == $index ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                                </select>

                                @if ($errors->has('segment_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('segment_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                       
                            <label for="subsegment" class="col-md-2 col-form-label text-md-right">Sub Segmento</label>
                        
                            <div class="col-md-4">
                                <select  id="subsegment"  name="Subsegment" class="form-control" >
                                    <option value="">Selecciona un Sub Segmento</option>
                                </select>

                                @if ($errors->has('subsegment_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('subsegment_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>  

                        <div class="form-group row">
                                    
                            <label for="segment" class="col-md-2 col-form-label text-md-right">Sub Segmento 2 (Opcional)</label>
                        
                            <div class="col-md-4">
                                <select  id="twosubsegment"  name="twoSubsegment" class="form-control" >
                                    <option value=""></option>
                                </select>

                                @if ($errors->has('subsegment_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('subsegment_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <label for="subsegment" class="col-md-2 col-form-label text-md-right">Sub Segmento 3 (Opcional)</label>
                        
                            <div class="col-md-4">
                                <select  id="threesubsegment"  name="threeSubsegment" class="form-control" >
                                    <option value=""></option>
                                </select>

                                @if ($errors->has('subsegment_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('subsegment_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>  

                       
                        <div class="form-group row">
                            <label for="unitofmeasure" class="col-md-2 col-form-label text-md-right">Unidad de Medida</label>

                            <div class="col-md-4">
                            <select class="form-control" id="unit_of_measure_id" name="unit_of_measure_id">
                                @foreach($unitofmeasures as $var)
                                    <option value="{{ $var->id }}">{{ $var->description }}</option>
                                @endforeach
                              
                            </select>
                            </div>
                            <label for="code_comercial" class="col-md-2 col-form-label text-md-right">Código Comercial</label>

                            <div class="col-md-4">
                                <input id="code_comercial" type="text" class="form-control @error('code_comercial') is-invalid @enderror" name="code_comercial" value="{{ old('code_comercial') }}" required autocomplete="code_comercial">

                                @error('code_comercial')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                       
                       
                        <div class="form-group row">
                            <label for="price" class="col-md-2 col-form-label text-md-right">Precio</label>

                            <div class="col-md-4">
                                <input id="price" type="text" class="form-control @error('price') is-invalid @enderror" name="price" value="{{ 0 ?? old('price') }}" required autocomplete="price">

                                @error('price')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="price_buy" class="col-md-2 col-form-label text-md-right">Precio Compra</label>

                            <div class="col-md-4">
                                <input id="price_buy" type="text" class="form-control @error('price_buy') is-invalid @enderror" name="price_buy" value="{{ 0 ?? old('price_buy') }}" required autocomplete="price_buy">

                                @error('price_buy')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>



                        <div class="form-group row">
                            <label for="lote" class="col-md-2 col-form-label text-md-right">Lote</label>

                            <div class="col-md-4">
                                <input id="lote" type="text" class="form-control @error('lote') is-invalid @enderror" name="lote" value="{{ old('lote') }}" autocomplete="lote">

                                @error('lote')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="fecha_vencimiento" class="col-md-2 col-form-label text-md-right">Fecha de Vencimiento</label>
                            <div class="col-md-4">
                                <input id="fecha_vencimiento" type="text" class="form-control @error('fecha_vencimiento') is-invalid @enderror" name="fecha_vencimiento" value="{{ old('fecha_vencimiento') }}" autocomplete="fecha_vencimiento" placeholder="00-00-2022">

                                @error('fecha_vencimiento')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>                    
                        </div>

                        
                        <div class="form-group row">
                           
                            <label for="money" class="col-md-2 col-form-label text-md-right">Moneda</label>

                            <div class="col-md-4">
                                <select class="form-control" name="money" id="money">
                                    <option value="D">Dolar</option>
                                    <option value="Bs">Bolívares</option>
                                </select>
                            </div>

                            <label for="exento" class="col-md-2 col-form-label text-md-right">exento</label>
                            
                            <div class="form-check">
                                <input class="form-check-input position-static" type="checkbox" id="exento" name="exento" value="1" aria-label="...">
                            </div>
                            <label for="islr" class="col-md-1 col-form-label text-md-right">Islr</label>
                            
                            <div class="form-check">
                                <input class="form-check-input position-static" type="checkbox" id="islr" name="islr" value="1" aria-label="...">
                            </div>
                            
                        </div>


                        @if ((Auth::user()->id_company  == '21'))
                        <div id="companylic">
                            <div class="form-group row">
                                <div class="col-sm-2">
                                    <label for="box">Cajas:</label>
                                </div>
                                <div class="col-sm-4">
                                    <input id="box" type="text" class="form-control @error('box') is-invalid @enderror" name="Cajas" value="{{ 1 ?? old('Cajas') }}" required autocomplete="box">
                                    @error('box')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-sm-2">
                                    <label for="degree">Grado de Alcohol:</label>
                                </div>
                                <div class="col-sm-4">
                                    <input id="degree" type="text" class="form-control @error('degree') is-invalid @enderror" name="Grado" value="{{ old('Grado') }}" required autocomplete="degree">
                                    @error('degree')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2">
                                    <label for="bottle">Botellas por Caja:</label>
                                </div>
                                <div class="col-sm-4">
                                    <input id="bottle" type="text" class="form-control @error('bottle') is-invalid @enderror" name="Botellas" value="{{ old('Botellas') }}" required autocomplete="bottle">
                                    @error('bottle')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-sm-2">
                                    <label for="liter">Litros por Botellas:</label>
                                </div>
                                <div class="col-sm-2">
                                    <input id="liter" type="text" class="form-control @error('liter') is-invalid @enderror" name="Litros" value="{{ old('Litros') }}"  required autocomplete="liter">
                                    @error('liter')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                           </div>
                      
                            <div class="form-group row">
                                <div class="col-sm-2">
                                    <label for="capacity">Capacidad de Litros:</label>
                                </div>
                                <div class="col-sm-4">
                                    <input id="capacity" type="text" class="form-control @error('capacity') is-invalid @enderror" name="Capacidad" value="0" required autocomplete="capacity" readonly>
                                    @error('capacity')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @endif
                       
                        <div class="form-group row">
                            <div style="display: none;">
                            <label for="special_impuesto" class="col-md-2 col-form-label text-md-right">Impuesto Especial</label>

                            <div class="col-md-4">
                                <input id="special_impuesto" type="text" class="form-control @error('special_impuesto') is-invalid @enderror" name="special_impuesto" value="{{ 0 ?? old('special_impuesto') }}" required autocomplete="special_impuesto">

                                @error('special_impuesto')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            </div>
                            <label id="AssociateAccount" for="account" class="col-md-2 col-form-label text-md-right">Asociar a Cuenta:</label>
                        
                            <div id="AssociateAccount2" class="col-md-4">
                            <select id="id_account"  name="id_account" class="form-control" required>
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
                                                        
                            <label for="cost_average" class="col-md-2 col-form-label text-md-right">Costo Promedio</label>

                            <div class="col-md-4">
                                <input id="cost_average" type="text" class="form-control @error('cost_average') is-invalid @enderror" name="cost_average" value="{{ 0 ?? old('cost_average') }}" required autocomplete="cost_average">

                                @error('cost_average')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="imagen" class="col-md-2 col-form-label text-md-right">Subir Foto</label>
                            <div class="col-md-4">
                                <input id="fotop" style="border:0;" name="fotop" type="file" data-initial-preview="" accept="image/*">
                             
                           <br>

                        </div>
                        </div>
                        
                        <p id="valueInput"></p> 
                        <br>

                        <div class="form-group row mb-0">
                            <div class="col-md-3 offset-md-2">
                                <button type="submit" class="btn btn-primary">
                                   Registrar Producto
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('products') }}" id="btnfacturar" name="btnfacturar" class="btn btn-danger" title="facturar">Volver</a>  
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

        function litros(){
            var n1 = document.getElementById('bottle').value;
            var n2 = document.getElementById('liter').value;
            var n3 = document.getElementById('box').value;

            if ( n1 == '' || n1 == null ) {
                alert("Agregar la cantidad de botellas");
                exit;
            }  
            
            
            if ( n2 == '' || n2 == null ) {
                alert("Agregar la cantidad de litros o mililitros");
                exit;
            }  

            
            if ( n3 == '' || n3 == null ) {
                alert("Agregar la cantidad de cajas");
                exit;
            }  

            
            if ((n1 != null || n1 != '') && (n2 != null || n2 != '') && (n3 != null || n3 != '')) {
                // var n2 = document.getElementById('xponcetaje').value; // PORCENTAJE
                var n2_format  = n2.replace(",", "." );
                var resultado       = (parseFloat(n1) * parseFloat(n3)  * parseFloat(n2_format));
                document.getElementsByName("Capacidad")[0].value = resultado;
            }
        } 
     
        $("#liter").on('blur',function(){
            litros();
        }); 
        
        $("#bottle").on('blur',function(){
            litros();
        }); 
        
        $("#box").on('blur',function(){
            litros();
        }); 
        
        $(document).ready(function () {
            $("#price").mask('000.000.000.000.000,00', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#liter").mask('000.000.000.000.000,00', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#degree").mask('000.000.000.000.000,00', { reverse: true });
            
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
@section('javascript')
<!--<script src="{{ ''/*asset("vendor/bootstrap-fileinput/js/fileinput.min.js")*/}}" type="text/javascript"></script>
<script src="{{ ''/*asset("vendor/bootstrap-fileinput/js/locales/es.js")*/}}" type="text/javascript"></script>
<script src="{{ ''/*asset("vendor/bootstrap-fileinput/themes/fas/theme.min.js")*/}}" type="text/javascript"></script>
<script src="{{ ''/*asset("assets/pages/script/imagen/foto.js")*/}}" type="text/javascript"></script> 
<link href="{{ ''/*asset("vendor/bootstrap-fileinput/css/fileinput.min.css")*/}}" rel="stylesheet" type="text/css"/>-->

<script src="{{''/*asset('assets/pages/script/imagen/foto.js')*/}}"></script>

<script src="{{url('assets/pages/script/imagen/foto.js')}}"></script> 




<script>    
        $("#type").on('change',function(){
            var type = $(this).val();
          
            if(type == 'SERVICIO'){
                $("#AssociateAccount").hide();
                
                $('#id_account').removeAttr('required');
                $("#AssociateAccount2").hide();
            }else{
                $("#AssociateAccount").show();
                $("#AssociateAccount2").show();
                $('#id_account').prop('required',true);
                

            }
        });

        $("#segment").on('change',function(){
            var segment_id = $(this).val();
            $("#subsegment").val("");
            
            // alert(segment_id);
            getSubsegment(segment_id);
        });


        function getSubsegment(segment_id){
            // alert(`../subsegment/list/${segment_id}`);
            $.ajax({
                url:`../subsegment/list/${segment_id}`,
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
                    
                }
            })
        }

        $("#subsegment").on('change',function(){
                var subsegment_id = $(this).val();
                var segment_id    = document.getElementById("segment").value;

                get2Subsegment(subsegment_id);
            });


        function get2Subsegment(subsegment_id){
           
            $.ajax({
                url:"{{ route('twosubsegments.list','') }}" + '/' + subsegment_id,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                    let subsegment = $("#twosubsegment");
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
                    
                }
            })
        }



        $("#twosubsegment").on('change',function(){
                var subsegment_id = $(this).val();
                var segment_id    = document.getElementById("segment").value;

                get3Subsegment(subsegment_id);
            });


        function get3Subsegment(subsegment_id){
           
            $.ajax({
                url:"{{ route('threesubsegments.list','') }}" + '/' + subsegment_id,
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
                            htmlOptions += `<option value='${id}' {{ old('Subsegment') == '${id}' ? 'selected' : '' }}>${description}</option>`

                        });
                    }
                    //console.clear();
                    // console.log(htmlOptions);
                    subsegment.html('');
                    subsegment.html(htmlOptions);
                
                    
                
                },
                error:(xhr)=>{
                    
                }
            })
        }
    </script>
@endsection

