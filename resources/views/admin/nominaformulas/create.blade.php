@extends('admin.layouts.dashboard')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header text-center font-weight-bold h3">Registro de Fórmula Nómina</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('nominaformulas.store') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="description" class="col-md-2 col-form-label text-md-right">Fórmula</label>

                            <div class="col-md-4">
                                <input id="description" type="text" class="form-control @error('description') is-invalid @enderror" name="description"  maxlength="60" required autocomplete="description">

                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="type" class="col-md-2 col-form-label text-md-right">Tipo Nómina:</label>
                            <div class="col-md-4">
                                <select class="form-control" name="type" id="type">
                                    <option value="Q">Quincenal</option>
                                    <option value="S">Semanal</option>
                                    <option value="M">Mensual</option>
                                    <option value="E">Especial</option>
                                    <option value="A">Asignación General</option>
                                </select>
                            </div>
                        </div>
                        
                    <br>
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                   Guardar Fórmula
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