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
                <div class="card-header text-center font-weight-bold h3">Transferencias entre Caja y Bancos</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('bankmovements.storetransfer') }}" enctype="multipart/form-data">
                        @csrf
                        <input id="id_account" type="hidden" class="form-control @error('id_account') is-invalid @enderror" name="id_account" value="{{ $account->id }}" required autocomplete="id_account" autofocus>
                        <input id="user_id" type="hidden" class="form-control @error('user_id') is-invalid @enderror" name="user_id" value="{{ Auth::user()->id }}" required autocomplete="user_id">
                        <input id="type_movement" type="hidden" class="form-control @error('type_movement') is-invalid @enderror" name="type_movement" value="TR" required autocomplete="type_movement" autofocus>
                        
                       
                        <div class="form-group row">
                            
                            <label for="account" class="col-md-2 col-form-label text-md-right">Transferir desde:</label>

                            <div class="col-md-4">
                                <input id="account" type="text" class="form-control @error('account') is-invalid @enderror" name="account" value="{{ $account->description ?? old('account') }}" readonly required autocomplete="account" autofocus>

                                @error('account')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="date_begin" class="col-md-2 col-form-label text-md-right">Fecha Transferenca:</label>

                            <div class="col-md-4">
                                <input id="date_begin" type="date" class="form-control @error('date_begin') is-invalid @enderror" name="date" value="{{ $datenow ?? old('date_begin') }}" required autocomplete="date_begin">

                                @error('date_begin')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="counterpart" class="col-md-2 col-form-label text-md-right">Transferir a :</label>

                            <div class="col-md-4">
                            <select class="form-control" id="id_counterpart" name="id_counterpart" required>
                                <option value="">Selecciona una Cuenta</option>
                                @foreach($counterparts as $var)
                                    <option value="{{ $var->id }}">{{ $var->description }}</option>
                                @endforeach
                              
                            </select>
                            </div>
                            <label for="reference" class="col-md-2 col-form-label text-md-right">Número de Referencia:</label>

                            <div class="col-md-4">
                                <input id="reference" type="text" class="form-control @error('reference') is-invalid @enderror" name="reference" value="{{ old('reference') }}" required autocomplete="reference">

                                @error('reference')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label id="coinlabel" for="coin" class="col-md-2 col-form-label text-md-right">Moneda:</label>

                            <div class="col-md-2">
                                <select class="form-control" name="coin" id="coin">
                                    <option selected value="bolivares">Bolívares</option>
                                    <option value="dolares">Dolares</option>
                                </select>
                            </div>
                            <label for="rate" class="col-md-1 col-form-label text-md-right">Tasa:</label>
                            <div class="col-md-3">
                                <input id="rate" type="text" class="form-control @error('rate') is-invalid @enderror" name="rate" value="{{ $bcv }}" required autocomplete="rate">
                                @error('rate')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            
                            <label for="amount" class="col-md-2 col-form-label text-md-right">Monto de la Transferencia</label>

                            <div class="col-md-4">
                                <input id="amount" type="text" class="form-control @error('amount') is-invalid @enderror" placeholder="0,00" name="amount" value="{{ old('amount') }}" required autocomplete="amount">

                                @error('amount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            
                        </div>
                        <br>
                        <div class="form-group row mb-0">
                            <div class="col-md-3 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                   Guardar Transferencia
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('bankmovements') }}" id="btnfacturar" name="btnfacturar" class="btn btn-danger" title="facturar">Volver</a>  
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('validacion_usuario')
    <script>
        
    $(function(){
        
        soloAlfaNumerico('description');
        
    });

    </script>
@endsection

@section('javascript')
    
    <script>
        $(document).ready(function () {
            $("#amount").mask('00.000.000.000.000,00', { reverse: true });
            
        });
       
        $(document).ready(function () {
            $("#rate").mask('000.000.000.000.000,00', { reverse: true });
            
        });
    </script> 

@endsection    