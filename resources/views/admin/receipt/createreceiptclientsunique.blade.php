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
                <div class="card-header text-center font-weight-bold h3">Crear Recibo de Condominio Individual</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('receipt.storeclientsunique') }}" enctype="multipart/form-data">
                        @csrf
                       
                        <input id="id_user" type="hidden" class="form-control @error('id_user') is-invalid @enderror" name="id_user" value="{{ Auth::user()->id }}" required autocomplete="id_user">
                        <input id="id_owner" type="hidden" class="form-control @error('id_owner') is-invalid @enderror" name="id_owner" value="{{ $client->id ?? -1  }}" required autocomplete="id_owner">

                        
                        <div class="form-group row">
                           
                            
                            <label for="clients" class="col-md-3 col-form-label text-md-right">Condominio</label>
                            <div class="col-md-3">
                                <input id="client" type="text" class="form-control @error('client') is-invalid @enderror" name="client" value="{{ $client->name ?? '' }}" readonly required autocomplete="client">
    
                                @error('client')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-1">
                                <a href="{{ route('receipt.selectclientfactura',$type) }}" title="Seleccionar Cliente"><i class="fa fa-eye"></i></a>  
                            </div>

                           

                        </div>           
                        
                        
                        
                        
                        <div class="form-group row">
                        
                            <label for="clients" class="col-md-3 col-form-label text-md-right">Propietario</label>
                            <div class="col-md-3">
                                <input id="owner" type="text" class="form-control @error('owner') is-invalid @enderror" name="owner" value="{{ $owner->name ?? '' }}" readonly required autocomplete="owner">
    
                                @error('owner')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-1">
                                <a href="{{ route('receipt.selectownersreceiptunique',$type) }}" title="Seleccionar Propietario"><i class="fa fa-eye"></i></a>  
                            </div>

                        </div>           

                       
                        <div class="form-group row">
                            <div class="col-sm-3 offset-sm-4">
                                <button type="submit" class="btn btn-info">
                                  Crear Recibo
                                </button>
                            </div>
                            <div class="col-sm-2">
                                <a href="{{ route('receipt') }}" id="btnvolver" name="btnvolver" class="btn btn-danger" title="volver">Volver</a>  
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
