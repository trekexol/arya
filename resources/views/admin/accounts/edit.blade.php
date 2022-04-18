@extends('admin.layouts.dashboard')

@section('content')
  
    <!-- container-fluid -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="row py-lg-2">
            <div class="col-md-6">
                <h2>Editar Cuenta</h2>
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

    <div class="card shadow mb-4 ">
        <div class="card-body ">
            <form  method="POST"   action="{{ route('accounts.update',$var->id) }}" enctype="multipart/form-data" >
                @method('PATCH')
                @csrf()
                <div class="container py-2">
                    <div class="row ">
                        <div class="col-12 ">
                                <div class="form-group row ">
                                    <label for="description" class="col-md-4 col-form-label text-md-right">Descripción</label>
        
                                    <div class="col-md-4">
                                        <input id="description" type="text" class="form-control @error('description') is-invalid @enderror" name="description" value="{{ $var->description }}" required autocomplete="description">
        
                                        @error('description')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="balance_previus" class="col-md-4 col-form-label text-md-right">Saldo Anterior</label>
        
                                    <div class="col-md-4">
                                        @if(isset($var->coin))
                                            <input id="balance_previus" type="text" class="form-control @error('balance_previus') is-invalid @enderror" readonly name="balance_previus" value="{{ number_format(bcdiv( ($var->balance_previus ?? 0) / ($var->rate ?? 1) , '1', 2), 2, ',', '.')}}"  required autocomplete="balance_previus">
                                        @else
                                            <input id="balance_previus" type="text" class="form-control @error('balance_previus') is-invalid @enderror" readonly name="balance_previus" value="{{ number_format(bcdiv( ($var->balance_previus ?? 0) , '1', 2), 2, ',', '.')}}"  required autocomplete="balance_previus">
                                        @endif
                                    
                                        @error('balance_previus')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    
                                </div>
                                <div class="form-group row">
                                    <label for="segmento" class="col-md-4 col-form-label text-md-right">Moneda</label>
                                    <div class="col-md-4">
                                        <select class="form-control" id="coin" name="coin" title="coin">
                                            @if($var->coin == '$')
                                                <option value="$">Dolar</option>
                                            @else
                                                <option value="BsS">Bolivares</option>
                                            @endif
                                            <option value="nulo">----------------</option>
                                            
                                            <div class="dropdown">
                                                <option value="$">Dolar</option>
                                                <option value="BsS">Bolivares</option>
                                            </div>
                                            
                                            
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row" id="rate_form">
                                    @if (isset($var->rate) && ($var->rate != 0))
                                        <label for="rate" id="rate_label" class="col-md-4 col-form-label text-md-right">Tasa Guardada de la Cuenta</label>
                                        <div class="col-md-4">
                                            <input id="rate" type="text" class="form-control @error('rate') is-invalid @enderror" name="rate" value="{{ number_format(bcdiv($var->rate, '1', 2), 2, ',', '.') }}" disabled autocomplete="rate">
            
                                            @error('rate')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        
                                    @else
                                        <label for="rate" id="rate_label" class="col-md-4 col-form-label text-md-right">Tasa del Dia</label>                           
                                        <div class="col-md-4">
                                            <input id="rate" type="text" class="form-control @error('rate') is-invalid @enderror" name="rate" value="{{ number_format(bcdiv($rate, '1', 2), 2, ',', '.') }}" disabled autocomplete="rate">
            
                                            @error('rate')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>   
                                    @endif
                                    <label for="rate" id="rate_label" class="col-md-2 col-form-label text-md-right">Tasa del Dia {{ number_format(bcdiv($rate, '1', 2), 2, ',', '.') }}</label>                           
                                    
                                    
                                </div>
                                
                            
                                <br>
                                <div class="form-group row mb-0">
                                    <div class="col-md-6 offset-md-4">
                                       <button type="submit" class="btn btn-primary">
                                           Actualizar Cuenta
                                        </button>
                                        <a href="{{ route('accounts') }}" name="danger" type="button" class="btn btn-danger">Cancelar</a>
                                    </div>
                                    
                                </div>
                            </form>
                        </div>
                    </div>
@endsection

@section('validacion_usuario')
<script>
     

    $(function(){
        soloAlfaNumerico('description');
    });

    $(document).ready(function () {
        
        $('#balance_previus').mask('###.###.###.###.###.###.###.###.###.###.###.###,##', {
            reverse: true,
            translation: {
                '#': {
                pattern: /-|\d/,
                recursive: true
                }
            },
            onChange: function(value, e) {
                var target = e.target,
                    position = target.selectionStart; // Capture initial position

                target.value = value.replace(/(?!^)-/g, '').replace(/^,/, '').replace(/^-,/, '-');

                target.selectionEnd = position; // Set the cursor back to the initial position.
            }
        });
    });
    $(document).ready(function () {
        $("#rate").mask('000.000.000.000.000.000.000,00', { reverse: true });
        
    });
    
    /*$("#rate_form").hide();
   

    $("#coin").on('change',function(){
        var coin = $(this).val();
 
         if(coin == '$'){
             $("#rate_form").show();
            
             
         }else{
             $("#rate_form").hide();
            
         }
    });*/

</script>

@endsection
            