@extends('admin.layouts.dashboard')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-center font-weight-bold h3">Registro de Conceptos de Nominas</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('nominaconcepts.store') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="abbreviation" class="col-md-2 col-form-label text-md-right">Concepto Abreviado</label>

                            <div class="col-md-3">
                                <input id="abbreviation" type="text" class="form-control @error('abbreviation') is-invalid @enderror" name="abbreviation" value="{{ old('abbreviation') }}" maxlength="60" required autocomplete="abbreviation">

                                @error('abbreviation')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="description" class="col-md-2 col-form-label text-md-right">Descripción</label>

                            <div class="col-md-3">
                                <input id="description" type="text" class="form-control @error('description') is-invalid @enderror" name="description" value="{{ old('description') }}" maxlength="60" required autocomplete="description">

                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                           
                            <label for="order" class="col-md-1 col-form-label text-md-right">Orden</label>

                            <div class="col-md-2">
                                <input id="order" type="number" class="form-control @error('order') is-invalid @enderror" name="order" value="{{ old('order') }}" maxlength="60" required autocomplete="order">

                                @error('order')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="sign" class="col-md-2 col-form-label text-md-right">Calcular <br>con Nómina:</label>
                            <div class="col-md-2">
                                <select class="form-control" name="calculate" id="calculate">
                                    <option value="N">No</option>
                                    <option value="S">Si</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="sign" class="col-md-2 col-form-label text-md-right">Signo:</label>
                            <div class="col-md-3">
                                <select class="form-control" name="sign" id="sign">
                                    <option value="A">Asignación</option>
                                    <option value="D">Deducción</option>
                                  
                                </select>
                            </div>
                            <label for="type" class="col-md-2 col-form-label text-md-right">Tipo Nómina:</label>
                            <div class="col-md-3">
                                <select class="form-control" name="type" id="type">
                                    <option value="Primera Quincena">Primera Quincena</option>
                                    <option value="Segunda Quincena">Segunda Quincena</option>
                                    <option value="Semanal">Semanal</option>
                                    <option value="Mensual">Mensual</option>
                                    <option value="Especial">Especial</option>
                                    <option value="Quincena">Quincenal</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="formula_m" class="col-md-2 col-form-label text-md-right">Fórmula Mensual:</label>

                            <div class="col-md-6">
                                <select class="form-control" name="formula_m" id="formula_m">
                                    <option value="">Seleccionar Formula</option>
                                    @if (isset($formulas))
                                        @foreach ($formulas as $var)
                                            
                                                <option value="{{ $var->id }}">{{ $var->description }}</option>
                                           
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <label for="formula_q" class="col-md-2 col-form-label text-md-right">(30 dias)</label>
                        </div>
                        <div class="form-group row">
                            <label for="formula_s" class="col-md-2 col-form-label text-md-right">Fórmula Semanal:</label>

                            <div class="col-md-6">
                                <select class="form-control" name="formula_s" id="formula_s">
                                    <option value="">Seleccionar Formula</option>
                                    @if (isset($formulas))
                                        @foreach ($formulas as $var)
                                           
                                                <option value="{{ $var->id }}">{{ $var->description }}</option>
                                           
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <label for="formula_q" class="col-md-2 col-form-label text-md-right">(4 Semanas)</label>
                        </div>
                        <div class="form-group row">
                            <label for="formula_q" class="col-md-2 col-form-label text-md-right">Fórmula Quincenal:</label>

                            <div class="col-md-6">
                                <select class="form-control" name="formula_q" id="formula_q">
                                    <option value="">Seleccionar Formula</option>
                                    @if (isset($formulas))
                                        @foreach ($formulas as $var)
                                            
                                                <option value="{{ $var->id }}">{{ $var->description }}</option>
                                            
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <label for="formula_q" class="col-md-2 col-form-label text-md-right">(15 dias)</label>
                        </div>
                        <div class="form-group row">
                            <label for="minimum" class="col-md-2 col-form-label text-md-right">Mínimo:</label>

                            <div class="col-md-4">
                                <input id="minimum" type="text" class="form-control @error('minimum') is-invalid @enderror" name="minimum"  value="0.00" maxlength="60" required autocomplete="off" placeholder='0,00'>

                                @error('minimum')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="maximum" class="col-md-2 col-form-label text-md-right">Máximo:</label>

                            <div class="col-md-4">
                                <input id="maximum" type="text" class="form-control @error('maximum') is-invalid @enderror" name="maximum"  value="9999999999.99" maxlength="60" required autocomplete="off" placeholder='0,00'>

                                @error('maximum')
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
                                   Registrar Nuevo Concepto
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
     $(document).ready(function () {
        $("#minimum").mask('000.000.000.000.000,00', { reverse: true });
    });
    $(document).ready(function () {
        $("#maximum").mask('000.000.000.000.000,00', { reverse: true });
    });

	$(function(){
        soloAlfaNumerico('description');
       
    });
    </script>
@endsection