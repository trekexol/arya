@extends('admin.layouts.dashboard')

@section('content')

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
        <div class="col-12">
            <div class="card">
                <div class="card-header">Registro de Tipo de Inventario</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('inventarytypes.store') }}">
                        @csrf
                        <div class="form-group row">
                            <label for="description" class="col-sm-4 col-form-label text-md-right">Descripción</label>

                            <div class="col-sm-6">
                                <input id="description" type="text" class="form-control @error('description') is-invalid @enderror" name="Descripcion" value="{{ old('Descripcion') }}" required autocomplete="description"  style="text-transform: uppercase;">

                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="form-group  col-sm-7 text-right ">
                                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-send-o"></i>Guardar</button>
                            </div>
                            <div class="form-group col-sm-5">
                                <a href="{{route('danger','inventarytypes')}}" name="danger" type="button" class="btn btn-danger btn-sm">Cancelar</a>
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
        soloAlfaNumerico('description');
    });
    </script>
@endsection
