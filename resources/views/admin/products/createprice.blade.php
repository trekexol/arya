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

                <div class="card-header text-center font-weight-bold h3">Registro de Precio</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('products.storeprice') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id_product" value="{{ $product_detail->id }}">
                        <div class="form-group row">
                            <div class="col-sm-2">
                                <label for="code_comercial">Código Comercial:</label>
                            </div>
                            <div class="col-sm-4">
                                <input id="code_comercial" type="text" class="form-control @error('code_comercial') is-invalid @enderror" name="Codigo_Comercial" value="{{ $product_detail->code_comercial }}" autocomplete="code_comercial" readonly>
                                @error('code_comercial')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-sm-2">
                                <label for="code_comercial">Descripción:</label>
                            </div>
                            <div class="col-sm-4">
                                <input id="code_comercial" type="text" class="form-control @error('code_comercial') is-invalid @enderror" name="Codigo_Comercial" value="{{ $product_detail->description }}" readonly>
                                @error('code_comercial')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-2">
                                <label for="price">Precio:</label>
                            </div>
                            <div class="col-sm-4">
                                <input onkeyup="noespac(this)" id="price" type="text" class="form-control @error('price') is-invalid @enderror" name="Precio" value="{{ old('Precio') }}"  autocomplete="price">
                                @error('price')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <br>
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                   Registrar Precio
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('products.productprices',$product_detail->id) }}" id="" name="" class="btn btn-danger" title="Atras">Volver</a>
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

            if ( n1 == '' || n1 == null) {
                alert("Verifique el Precio Costo del Producto");
            } else {
                // var n2 = document.getElementById('xponcetaje').value; // PORCENTAJE
                var n2_format  = n2.replace(",", "." );
                var resultado       = (parseFloat(n1) * parseFloat(n3)  * parseFloat(n2_format));
                document.getElementsByName("Capacidad")[0].value = resultado;
            }
        }


        $(document).ready(function () {
            $("#degree").mask('000.000.000.000.000,00', { reverse: true });

        });

        $(document).ready(function () {
            $("#liter").mask('000.000.000.000.000,00', { reverse: true });

        });



	$(function(){
        soloAlfaNumerico('code_comercial');
        soloAlfaNumerico('description');
    });
    </script>
@endsection

@section('javascript')
    <script>
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
                            htmlOptions += `<option value='${id}' {{ old('Sub_Segmento') == '${id}' ? 'selected' : '' }}>${description}</option>`

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

