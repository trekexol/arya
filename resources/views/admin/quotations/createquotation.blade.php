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
                <div class="card-header text-center font-weight-bold h3">Registro de {{$type ?? 'Cotización'}}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('quotations.store') }}" enctype="multipart/form-data">
                        @csrf
                       
                        <input id="id_user" type="hidden" class="form-control @error('id_user') is-invalid @enderror" name="id_user" value="{{ Auth::user()->id }}" required autocomplete="id_user">
                        <input id="id_client" type="hidden" class="form-control @error('id_client') is-invalid @enderror" name="id_client" value="{{ $client->id ?? -1  }}" required autocomplete="id_client">
                        <input id="id_vendor" type="hidden" class="form-control @error('id_vendor') is-invalid @enderror" name="id_vendor" value="{{ $vendor->id ?? $client->id_vendor ?? null  }}" required autocomplete="id_vendor">
                        <input id="type" type="hidden" class="form-control @error('type') is-invalid @enderror" name="type" value="{{ $type ?? null  }}" required autocomplete="type">
                       
                        
                        <div class="form-group row">
                            <label for="clients" class="col-md-2 col-form-label text-md-right">Cliente</label>
                            <div class="col-md-3">
                                <input id="client" type="text" class="form-control @error('client') is-invalid @enderror" name="client" value="{{ $client->name ?? '' }}" readonly required autocomplete="client">
    
                                @error('client')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-1">
                                <a href="{{ route('quotations.selectclient',$type) }}" title="Seleccionar Cliente"><i class="fa fa-eye"></i></a>  
                            </div>

                        </div>
                           @if($type == 'factura' || $type == 'Nota de Entrega')
                            <label for="serie" class="col-md-3 col-form-label text-md-right">N° de Control/Serie:</label>

                            <div class="col-md-2">
                                <input id="serie" type="text" class="form-control @error('serie') is-invalid @enderror" name="serie" value="{{ old('serie') }}" autocomplete="serie">

                                @error('serie')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                           @endif 
                            
                        </div>

                        <div class="form-group row">
                           
                            <label for="vendors" class="col-md-2 col-form-label text-md-right">Vendedor</label>
                            <div class="col-md-3">
                                <input id="id_vendor" type="text" class="form-control @error('id_vendor') is-invalid @enderror" name="vendor" value="{{ $vendor->name ?? $client->vendors['name'] ?? '' }} {{ $vendor->surname ?? $client->vendors['surname'] ?? '' }}" readonly required autocomplete="id_vendor">

                                    @error('id_vendor')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>      
                            <div class="form-group col-md-1">
                                <a href="{{ route('quotations.selectvendor',[$client->id ?? -1,$type]) }}" title="Seleccionar Vendedor"><i class="fa fa-eye"></i></a>  
                            </div>
                           

                            <label for="transports" class="col-md-2 col-form-label text-md-right">Transporte / Tipo de Entrega</label>

                            <div class="col-md-3">
                            <select class="form-control" id="id_transport" name="id_transport">
                                <option selected value="-1">Ninguno</option>
                                @foreach($transports as $var)
                                    <option value="{{ $var->id }}">{{ $var->placa }}</option>
                                @endforeach
                              
                            </select>
                            </div> 
                           
                        </div>

                        
                        <div class="form-group row">
                            <label for="date_quotation" class="col-md-2 col-form-label text-md-right">Fecha de {{$type ?? 'Cotización'}}</label>
                            <div class="col-md-3">
                                <input id="date_quotation" type="date" class="form-control @error('date_quotation') is-invalid @enderror" name="date_quotation" value="{{ $datenow }}" required autocomplete="date_quotation">
    
                                @error('date_quotation')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                          


                            <label for="rol" class="col-md-2 col-form-label text-md-right">Sucursal</label>
                            <div class="col-md-3">
                               

                                <select class="form-control" id="id_branch" name="id_branch">
                                    @isset($branches)

                                    @foreach($branches as $branch)
                                        @if ($user_branch->id == $branch->id) 
                                            <option selected value="{{$branch->id}}">{{ $branch->description ?? '' }}</option>
                                        @else
                                            @if ($user->role_id == 1) 
                                            <option value="{{$branch->id}}">{{ $branch->description ?? '' }}</option>
                                            @endif
                                        @endif
                                    @endforeach
                                @endisset
                                </select>
                            </div>
                            
                        </div>
                       
                       
                        <div class="form-group row">
                           
                            <label for="observation" class="col-md-2 col-form-label text-md-right">Observaciones</label>

                            <div class="col-md-4">
                                <input id="observation" type="text" class="form-control @error('observation') is-invalid @enderror" name="observation" value="{{ old('observation') }}" autocomplete="observation">

                                @error('observation')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <br>
                       
                        <div class="form-group row">
                            <div class="col-sm-3 offset-sm-4">
                                <button type="submit" class="btn btn-info">
                                  Crear {{$type ?? 'Cotización'}}
                                </button>
                            </div>
                            <div class="col-sm-3">
                                @if ($type == "Nota de Entrega")
                                <a href="{{ route('quotations.indexdeliverynote') }}" id="btnvolver" name="btnvolver" class="btn btn-danger" title="volver">Volver a Notas de Ent.</a>  
                                @endif
                                @if ($type == "factura")
                                <a href="{{ route('invoices') }}" id="btnvolver" name="btnvolver" class="btn btn-danger" title="volver">Volver a Faturas</a>  
                                @endif
                                @if ($type != "Nota de Entrega" && $type != "factura") 
                                <a href="{{ route('quotations') }}" id="btnvolver" name="btnvolver" class="btn btn-danger" title="volver">Volver a Cotizaciones</a>  
                                @endif
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
