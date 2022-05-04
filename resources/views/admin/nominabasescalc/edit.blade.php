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
            <form  method="POST"   action="{{ route('nominaconcepts.update',$var->id) }}" enctype="multipart/form-data" >
                @method('PATCH')
                @csrf()
                <div class="container py-2">
                    <div class="row">
                        <div class="col-12 ">
                            <div class="form-group row">
                                <label for="abbreviation" class="col-md-2 col-form-label text-md-right">Concepto Abreviado</label>
    
                                <div class="col-md-3">
                                    <input id="abbreviation" type="text" class="form-control @error('abbreviation') is-invalid @enderror" name="abbreviation" value="{{ $var->abbreviation ?? old('abbreviation') }}" maxlength="60" required autocomplete="abbreviation">
    
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
                                    <input id="description" type="text" class="form-control @error('description') is-invalid @enderror" name="description" value="{{ $var->description ?? old('description') }}" maxlength="60" required autocomplete="description">
    
                                    @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                               
                                <label for="order" class="col-md-1 col-form-label text-md-right">Orden</label>
    
                                <div class="col-md-2">
                                    <input id="order" type="number" class="form-control @error('order') is-invalid @enderror" name="order" value="{{ $var->order ?? old('order') }}" maxlength="60" required autocomplete="order">
    
                                    @error('order')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <label for="sign" class="col-md-2 col-form-label text-md-right">Calcular <br>con Nómina:</label>
                                <div class="col-md-2">
                                    <select class="form-control" id="calculate" name="calculate" title="calculate">
                                        @if($var->calculate == "N")
                                            <option value="N">No</option>
                                        @else
                                            <option value="S">Si</option>
                                        @endif
                                        <option value="nulo">----------------</option>
                                        
                                        <div class="dropdown">
                                            <option value="N">No</option>
                                            <option value="S">Si</option>
                                        </div>
                                        
                                           
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="sign" class="col-md-2 col-form-label text-md-right">Signo:</label>
                                <div class="col-md-3">
                                    <select class="form-control" id="sign" name="sign" title="sign">
                                        @if($var->sign == "A")
                                            <option value="A">Asignación</option>
                                        @else
                                            <option value="D">Deducción</option>
                                        @endif
                                        <option value="nulo">----------------</option>
                                        
                                        <div class="dropdown">
                                            <option value="A">Asignación</option>
                                            <option value="D">Deducción</option>
                                        </div>
                                        
                                           
                                    </select>
                                </div>
                                <label for="type" class="col-md-2 col-form-label text-md-right">Tipo Nómina:</label>
                                <div class="col-md-3">
                                    <select class="form-control" id="type" name="type" title="type">
                                        
                                            <option value="{{ $var->type }}">{{ $var->type }}</option>
                                    
                                        <option value="nulo">----------------</option>
                                        
                                        <div class="dropdown">
                                            <option value="Primera Quincena">Primera Quincena</option>
                                            <option value="Segunda Quincena">Segunda Quincena</option>
                                            <option value="Semanal">Semanal</option>
                                            <option value="Mensual">Mensual</option>
                                            <option value="Especial">Especial</option>
                                            <option value="Quincenal">Quincenal</option>
                                        </div>
                                        
                                        
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="formula_m" class="col-md-2 col-form-label text-md-right">Fórmula Mensual:</label>
    
                                <div class="col-md-6">
                                    <select class="form-control" id="formula_m" name="formula_m" title="formula_mensual">
                                        @foreach ($formulas as $formula)
                                            <option value="{{ $formula->id }}">{{ $formula->description }}</option>
                                        @endforeach
                                    <option value="nulo">----------------</option>
                                    
                                    <div class="dropdown">
                                        @foreach ($formulas as $m)
                                            <option value="{{ $m->id }}">{{ $m->description }}</option>
                                        @endforeach
                                    </div>

                                </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="formula_s" class="col-md-2 col-form-label text-md-right">Fórmula Semanal:</label>
    
                                <div class="col-md-6">
                                   <select class="form-control" id="formula_s" name="formula_s" title="formula_semanal">
                                        @foreach ($formulas as $formula_s)
                                            <option value="{{ $formula_s->id }}">{{ $formula_s->description }}</option>
                                        @endforeach
                                    <option value="nulo">----------------</option>
                                    
                                    <div class="dropdown">
                                        @foreach ($formulas as $s)
                                            <option value="{{ $s->id }}">{{ $s->description }}</option>
                                        @endforeach
                                    </div>
                                    
                                </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="formula_q" class="col-md-2 col-form-label text-md-right">Fórmula Quincenal:</label>
    
                                <div class="col-md-6">
                                    <input id="formula_q" type="text" class="form-control @error('formula_q') is-invalid @enderror" name="formula_q" value="{{ $var->formula_q ?? old('formula_q') }}" maxlength="60" autocomplete="formula_q">
    
                                    @error('formula_q')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="minimum" class="col-md-2 col-form-label text-md-right">Mínimo:</label>
    
                                <div class="col-md-4">
                                    <input id="minimum" type="text" class="form-control @error('minimum') is-invalid @enderror" name="minimum"  value="{{ $var->minimum }}" maxlength="60" required autocomplete="off" placeholder='0,00' >
    
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
                                    <input id="maximum" type="text" class="form-control @error('maximum') is-invalid @enderror" name="maximum"  value="{{ $var->maximum }}" maxlength="60" required autocomplete="off" placeholder='0,00'>
    
                                    @error('maximum')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                               
                            <br>
                                <div class="form-group row justify-content-center">
                                    <div class="form-group col-sm-2">
                                        <button type="submit" class="btn btn-info btn-block"><i class="fa fa-send-o"></i>Registrar</button>
                                    </div>
                                    <div class="form-group col-sm-2">
                                        <a href="{{ route('nominaconcepts') }}" name="danger" type="button" class="btn btn-danger btn-block">Cancelar</a>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>

                    @endsection

                    @section('validacion')
                    <!-- Se encarga de los input number, el formato -->
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js" integrity="sha512-pHVGpX7F/27yZ0ISY+VVjyULApbDlD0/X0rgGbTqCE7WFW5MezNTWG/dnhtbBuICzsd0WQPgpE4REBLv+UqChw==" crossorigin="anonymous"></script>

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