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
    <div class="row py-lg-2">
        <div class="col-md-8 offset-md-2 ">
            <h2>Aumentar el Inventario de un Producto</h2>
        </div>
      </div>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"></div>

                <div class="card-body">
                    <form method="POST" action="{{ route('inventories.store_increase_inventory') }}" enctype="multipart/form-data">
                        @csrf

                        <input id="id_inventory" type="hidden" class="form-control @error('id_inventory') is-invalid @enderror" name="id_inventory" value="{{ $inventory->id ?? null}}" readonly required autocomplete="id_inventory">
                       
                        <input id="amount_old" type="hidden" class="form-control @error('amount_old') is-invalid @enderror" name="amount_old" value="{{ $inventory->amount ?? null}}" readonly required autocomplete="amount_old">
                        <input id="id_user" type="hidden" class="form-control @error('id_user') is-invalid @enderror" name="id_user" value="{{ Auth::user()->id }}" readonly required autocomplete="id_user">
                       

                        <div class="form-group row">
                            <label for="name_product" class="col-md-2 col-form-label text-md-right">Nombre del Producto</label>

                            <div class="col-md-4">
                                <input id="name_product" type="text" class="form-control @error('name_product') is-invalid @enderror" name="name_product" value="{{ $inventory->description ?? ''}}" readonly required autocomplete="name_product">

                                @error('name_product')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="code" class="col-md-2 col-form-label text-md-right">Código</label>

                            <div class="col-md-4">
                                <input id="code" type="text" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ $inventory->code_comercial ?? '' }}" required readonly autocomplete="code">

                                @error('code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div> 
                        </div>
                        <div class="form-group row">
                            <label for="cantidad" class="col-md-2 col-form-label text-md-right">Cantidad Actual</label>
                            <div class="col-md-4">
                                <input id="amount" type="text" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ $inventory->amount }}" readonly required autocomplete="amount">

                                @error('amount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="cantidad" class="col-md-2 col-form-label text-md-right">Cantidad a Aumentar</label>
                            <div class="col-md-4">
                                <input onkeyup="noespac(this)"  id="amount_new" type="text" class="form-control @error('amount_new') is-invalid @enderror" name="amount_new"  required autocomplete="amount_new" focus>

                                @error('amount_new')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        
                        </div>

                       
                           
                        <div class="form-group row">
                            <label for="price" class="col-md-2 col-form-label text-md-right">Precio de Venta</label>

                            <div class="col-md-4">
                                <input id="price" type="text" readonly class="form-control @error('price') is-invalid @enderror" value="{{ $inventory->price ?? 0}}" name="price" required autocomplete="price">

                                @error('price')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="money" class="col-md-2 col-form-label text-md-right">Moneda</label>

                            @if (!empty($inventory))
                            <div class="col-md-4">
                                @if($inventory->money == "D")
                                    <input id="money" type="text" readonly class="form-control @error('money') is-invalid @enderror" name="money" value="Dolar" required autocomplete="money">
                                @else
                                    <input id="money" type="text" readonly class="form-control @error('money') is-invalid @enderror" name="money" value="Bolívares" required autocomplete="money">
                                @endif
                                @error('money')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            @endif
                        </div>
                        <div class="form-group row">
                            <label for="price_buy" class="col-md-2 col-form-label text-md-right">Precio de Compra</label>

                            <div class="col-md-4">
                                <input id="price_buy" type="text" readonly class="form-control @error('price_buy') is-invalid @enderror" value="{{ $inventory->price_buy ?? 0 }}" name="price_buy" required autocomplete="price_buy">

                                @error('price_buy')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="rate" class="col-md-2 col-form-label text-md-right">Tasa</label>

                            <div class="col-md-4">
                                <input  id="rate" type="text" onkeyup="noespac(this)" class="form-control @error('rate') is-invalid @enderror" name="rate" value="{{ number_format($bcv ?? 0, 10, ',', '.') }}" required autocomplete="rate">
                
                                @error('rate')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            @if (isset($contrapartidas))      
                            <label for="contrapartida" class="col-md-2 col-form-label text-md-right">Contrapartida:</label>
                        
                            <div class="col-md-4">
                            <select id="contrapartida"  name="contrapartida" class="form-control" required>
                                <option value="">Seleccione una Contrapartida</option>
                                @foreach($contrapartidas as $index => $value)
                                    <option value="{{ $index }}" {{ old('Contrapartida') == $index ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                                </select>

                                @if ($errors->has('contrapartida_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('contrapartida_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                            @endif
                            <div class="col-md-4">
                                    <select  id="subcontrapartida"  name="Subcontrapartida" class="form-control" required>
                                        <option value="">Seleccionar</option>
                                    </select>

                                    @if ($errors->has('subcontrapartida_id'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('subcontrapartida_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                        </div>  
                       
                        <br>
                            <div class="form-group row">
                                <div class="col-md-4 offset-md-2">
                                    <button type="submit" class="btn btn-primary">
                                        Actualizar Inventario
                                     </button>  
                                </div>
                               
                                <div class="col-md-2">
                                    <a href="{{ route('inventories') }}" id="btnreturn" name="btnreturn" class="btn btn-danger" title="facturar">Regresar</a>  
                                </div>
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

        
        
        $("#contrapartida").on('change',function(){
            var contrapartida_id = $(this).val();
            $("#subcontrapartida").val("");
            
            getSubcontrapartida(contrapartida_id);
        });

        function getSubcontrapartida(contrapartida_id){
            
            $.ajax({
                url:"{{ route('directpaymentorders.listcontrapartida') }}" + '/' + contrapartida_id,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                    let subcontrapartida = $("#subcontrapartida");
                    let htmlOptions = `<option value='' >Seleccione..</option>`;
                    // console.clear();
                    if(response.length > 0){
                        response.forEach((item, index, object)=>{
                            let {id,description} = item;
                            htmlOptions += `<option value='${id}' {{ old('Subcontrapartida') == '${id}' ? 'selected' : '' }}>${description}</option>`

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

        $("#subcontrapartida").on('change',function(){
                var subcontrapartida_id = $(this).val();
                var contrapartida_id    = document.getElementById("contrapartida").value;
                
            });
    </script>
@endsection
