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
                    <div class="card-header text-center font-weight-bold h3">Crear de Cotización </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('quotationslic.store') }}" enctype="multipart/form-data">
                            @csrf

                            <input id="id_user" type="hidden" class="form-control @error('id_user') is-invalid @enderror" name="id_user" value="{{ Auth::user()->id }}" required autocomplete="id_user">
                            <input id="id_client" type="hidden" class="form-control @error('id_client') is-invalid @enderror" name="id_client" value="{{ $client->id ?? -1  }}" required autocomplete="id_client">
                            <input id="id_vendor" type="hidden" class="form-control @error('id_vendor') is-invalid @enderror" name="id_vendor" value="{{ $vendor->id ?? $client->id_vendor ?? -1  }}" required autocomplete="id_vendor">
                            <div class="form-group row">
                                <div class="col-sm-2">
                                    <label for="clients">Cliente:</label>
                                </div>
                                <div class="col-sm-3">
                                    <input id="client" type="text" class="form-control @error('client') is-invalid @enderror" name="client" value="{{ $client->name ?? '' }}" readonly required autocomplete="client">
                                    @if ($errors->has('client'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('client') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group col-sm-1">
                                    <a href="{{ route('quotationslic.selectclient') }}" title="Seleccionar Cliente"><i class="fa fa-eye"></i></a>
                                </div>
                                <div class="col-sm-2">
                                    <label for="vendors">Vendedor:</label>
                                </div>
                                <div class="col-sm-3">
                                    <input id="id_vendor" type="text" class="form-control @error('id_vendor') is-invalid @enderror" name="vendor" value="{{ $vendor->name ?? $client->vendors['name'] ?? 'Sin Vendedor' }}" readonly autocomplete="id_vendor">
                                    @if ($errors->has('id_vendor'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('id_vendor') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group col-md-1">
                                    <a href="{{ route('quotationslic.selectvendor',$client->id ?? -1) }}" title="Seleccionar Vendedor"><i class="fa fa-eye"></i></a>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2">
                                    <label for="date_quotation">Fecha de Cotización:</label>
                                </div>
                                <div class="col-sm-4">
                                    <input id="date_quotation" type="date" class="form-control @error('date_quotation') is-invalid @enderror" name="date_quotation" value="{{ $datenow }}" required autocomplete="date_quotation">
                                    @if ($errors->has('date_quotation'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('date_quotation') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2">
                                    <label for="note">Nota Pie de Factura:</label>
                                </div>
                                <div class="col-sm-4">
                                    <input id="note" type="text" class="form-control @error('note') is-invalid @enderror"  name="note" value="{{ old('note') }}" autocomplete="note">
                                    @if ($errors->has('note'))
                                        <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('note') }}</strong>
                                            </span>
                                    @endif
                                </div>
                                <div class="col-sm-2">
                                    <label for="base_imponible_pcb">% Iva Percibido:</label>
                                </div>
                                <div class="col-sm-4">
                                    <div class="input-group mb-3">
                                        <input id="iva_percibido" type="text" class="form-control @error('iva_percibido') is-invalid @enderror"  value="10" name="Iva_Percibido" autocomplete="iva_percibido">
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                    @if ($errors->has('iva_percibido'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('iva_percibido') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">

                                <div class="col-sm-2">
                                    <label for="transporte">Transporte:</label>
                                </div>
                                <div class="col-sm-4">
                                    <select class="form-control" id="transporte" name="Transporte">
                                        <option value="1">Seleccione</option>
                                        @foreach($transports as $transport)
                                            <option value="{{ $transport->id }}">
                                                {{ $transport->type}}-{{ $transport->placa}}-{{ $transport->modelos['description']}}-{{ $transport->colors['description']}}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('transporte'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('transporte') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="col-sm-2">
                                    <label for="conductor_id">Conductor:</label>
                                </div>
                                <div class="col-sm-4">
                                    <select class="form-control" id="conductor_id" name="Conductor">
                                        <option value="0">Seleccione</option>
                                        @foreach($drivers as $driver)

                                            <option value="{{ $driver->id }}">
                                                {{ $driver->type_code}}{{ $driver->cedula}} {{ $driver->name}} {{ $driver->last_name}}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('conductor_id'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('conductor_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2">
                                    <label for="licence">Licencia de Licores:</label>
                                </div>
                                <div class="col-sm-4">
                                    <input id="licence" type="text" class="form-control @error('licence') is-invalid @enderror"  name="Licencia" value="{{ $client->licence ?? '' }}" autocomplete="licence">
                                    @if ($errors->has('licence'))
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('licence') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2">
                                    <label for="destiny">Destino:</label>
                                </div>
                                <div class="col-sm-10">
                                    <input id="delivery" type="text" class="form-control @error('destiny') is-invalid @enderror"  name="Direccion_Destino" value="{{ $client->destiny ?? '' }}" autocomplete="destiny">
                                    @if ($errors->has('delivery'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('delivery') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2">
                                    <label for="delivery">Direccion Entrega:</label>
                                </div>
                                <div class="col-sm-10">
                                    <input id="delivery" type="text" class="form-control @error('delivery') is-invalid @enderror"  name="Direccion_Entrega" value="{{ $client->delivery ?? '' }}" autocomplete="delivery">
                                    @if ($errors->has('delivery'))
                                        <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('delivery') }}</strong>
                                            </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2">
                                    <label for="observation">Observaciones:</label>
                                </div>
                                <div class="col-sm-10">
                                    <input id="observation" type="text" class="form-control @error('observation') is-invalid @enderror"  name="observation" value="{{ old('observation') }}"  autocomplete="observation">
                                    @if ($errors->has('observation'))
                                        <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('observation') }}</strong>
                                            </span>
                                    @endif
                                </div>
                            </div>
                            <br>
                            <div class="form-group">
                                <div class="col-md-3 offset-md-4">
                                    <button type="submit" class="btn btn-info">
                                        Crear Cotización
                                    </button>
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
        $(function(){
            soloAlfaNumerico('code_comercial');
            soloAlfaNumerico('description');
        });
    </script>
@endsection
