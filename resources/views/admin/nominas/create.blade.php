@extends('admin.layouts.dashboard')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header text-center font-weight-bold h3">Registro de Nómina</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('nominas.store') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="description" class="col-md-2 col-form-label text-md-right">Descripción </label>

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
                                    <option value="Primera Quincena">Primera Quincena</option>
                                    <option value="Segunda Quincena">Segunda Quincena</option>
                                    <option value="Semanal">Semanal</option>
                                    <option value="Mensual">Mensual</option>
                                    <option value="Especial">Especial</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="date_begin" class="col-md-2 col-form-label text-md-right">Fecha Desde:</label>

                            <div class="col-md-4">
                                <input id="date_begin" type="date" class="form-control @error('date_begin') is-invalid @enderror" name="date_begin" value="{{ $datenow }}" required autocomplete="date_begin">

                                @error('date_begin')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="date_end" class="col-md-2 col-form-label text-md-right">Fecha Hasta:</label>

                            <div class="col-md-4">
                                <input id="date_end" type="date" class="form-control @error('date_end') is-invalid @enderror" name="date_end" >

                                @error('date_end')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="profession" class="col-md-2 col-form-label text-md-right">Tipo de Trabajador</label>
                            <div class="col-md-4">
                                <select  id="profession"  name="id_profession" class="form-control">
                                    @foreach($professions as $profession)
                                            <option selected value="{{$profession->id}}">{{ $profession->name }}</option>
                                        @endforeach
                                    
                                </select>
                            </div>
                            <label for="rate" class="col-md-2 col-form-label text-md-right">Tasa:</label>
                            <div class="col-md-3">
                                <input id="rate" type="text" class="form-control @error('rate') is-invalid @enderror" name="rate" value="{{  number_format(bcdiv($nomina->rate ?? $bcv, '1', 2) , 2, ',', '.') }}" required autocomplete="rate">
                                @error('rate')
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
                                   Registrar Nómina
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('nominas') }}" id="btnvolver" name="btnvolver" class="btn btn-danger" title="volver">Volver</a>  
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

    $(document).ready(function () {
        $("#rate").mask('000.000.000.000.000,00', { reverse: true });
        
    });
    </script>
@endsection