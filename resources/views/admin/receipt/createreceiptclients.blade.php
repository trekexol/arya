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
                <div class="card-header text-center font-weight-bold h3">Registro de Recibo de Cobro</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('receipt.store') }}" enctype="multipart/form-data">
                        @csrf
                       
                        <input id="id_user" type="hidden" class="form-control @error('id_user') is-invalid @enderror" name="id_user" value="{{ Auth::user()->id }}" required autocomplete="id_user">
                        <input id="id_client" type="hidden" class="form-control @error('id_client') is-invalid @enderror" name="id_client" value="{{ $client->id ?? -1  }}" required autocomplete="id_client">
                        <input id="id_vendor" type="hidden" class="form-control @error('id_vendor') is-invalid @enderror" name="id_vendor" value="{{ $vendor->id ?? $client->id_vendor ?? null  }}" required autocomplete="id_vendor">
                        <input id="type" type="hidden" class="form-control @error('type') is-invalid @enderror" name="type" value="{{ $type ?? null  }}" required autocomplete="type">
                       
                        



                        <br>
                       
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
