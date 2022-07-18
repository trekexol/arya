@extends('admin.layouts.dashboard')

@section('content')
  
    <!-- container-fluid -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="row py-lg-2">
            <div class="col-md-6">
                <h2>Editar Nómina</h2>
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

    <div class="card shadow mb-4">
        <div class="card-body">
            <form  method="POST"   action="{{ route('nominas.update',$var->id) }}" enctype="multipart/form-data" >
                @method('PATCH')
                @csrf()
                <div class="container py-2">
                    <div class="row">
                        <div class="col-12 ">
                           
                           
                                <div class="form-group row">
                                    <label for="description" class="col-md-2 col-form-label text-md-right">Descripción </label>
        
                                    <div class="col-md-4">
                                        <input id="description" type="text" class="form-control @error('description') is-invalid @enderror" name="description" value="{{ $var->description }}" maxlength="60" required autocomplete="description">
        
                                        @error('description')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <label for="type" class="col-md-2 col-form-label text-md-right">Tipo Nómina:</label>
                                    
                                    <div class="col-md-4">
                                        <select class="form-control" id="type" name="type" title="type">
                                            
                                                <option value="{{ $var->type }}">{{ $var->type }}</option>
                                           
                                            <option value="nulo">----------------</option>
                                            
                                            <div class="dropdown">
                                                <option value="Primera Quincena">Primera Quincena</option>
                                                <option value="Segunda Quincena">Segunda Quincena</option>
                                                <option value="Semanal">Semanal</option>
                                                <option value="Mensual">Mensual</option>
                                                <option value="Especial">Especial</option>
                                            </div>
                                            
                                               
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="date_begin" class="col-md-2 col-form-label text-md-right">Fecha Desde:</label>
        
                                    <div class="col-md-4">
                                        <input id="date_begin" type="date" class="form-control @error('date_begin') is-invalid @enderror" name="date_begin" value="{{ $var->date_begin }}" required autocomplete="date_begin">
        
                                        @error('date_begin')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <label for="date_end" class="col-md-2 col-form-label text-md-right">Fecha Hasta:</label>
        
                                    <div class="col-md-4">
                                        <input id="date_end" type="date" class="form-control @error('date_end') is-invalid @enderror" name="date_end" value="{{ $var->date_end }}">
        
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
                                                    @if ( $var->id_profession == $profession->id)
                                                        <option selected style="backgroud-color:blue;" value="{{$profession->id}}"><strong>{{ $profession->name }}</strong></option>
                                                    @endif
                                                @endforeach
                                                <option class="hidden" disabled data-color="#A0522D" value="-1">------------------</option>
                                                @foreach($professions as $profession)
                                                    <option value="{{ $profession['id'] }}" >
                                                        {{ $profession['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <label for="rate" class="col-md-2 col-form-label text-md-right">Tasa:</label>
                                        <div class="col-md-3">
                                            <input id="rate" type="text" class="form-control @error('rate') is-invalid @enderror" name="rate" value="{{  number_format(bcdiv($var->rate ?? $bcv, '1', 2) , 2, ',', '.') }}" required autocomplete="rate">
                                            @error('rate')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                       
                                </div>
                                <div class="form-group row">
                                <label for="rol" class="col-md-2 col-form-label text-md-right">Status</label>
                                    <div class="col-md-4">
                                        <select class="form-control" id="status" name="status" title="status">
                                            @if($var->status == 1)
                                                <option value="1">Activo</option>
                                            @else
                                                <option value="0">Inactivo</option>
                                            @endif
                                            <option value="nulo">----------------</option>
                                            
                                            <div class="dropdown">
                                                <option value="1">Activo</option>
                                                <option value="0">Inactivo</option>
                                            </div>
                                        </select>
                                    </div>
                                </div>
                            <br>
                                <div class="form-group row justify-content-center">
                                    <div class="form-group col-sm-2">
                                        <button type="submit" class="btn btn-info btn-block"><i class="fa fa-send-o"></i>Registrar</button>
                                    </div>
                                    <div class="form-group col-sm-2">
                                        <a href="{{ route('nominas') }}" name="danger" type="button" class="btn btn-danger btn-block">Cancelar</a>
                                    </div>
                                </div>

                            </form>
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