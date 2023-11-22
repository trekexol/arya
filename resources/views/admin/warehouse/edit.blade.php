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
                <div class="card-header">Editar Almacén</div>

                <div class="card-body">
                    <form  method="POST"   action="{{ route('warehouse.update',$var->id) }}" enctype="multipart/form-data" >
                        @method('PATCH')
                        @csrf()
                        
                       
                        <div class="form-group row">
                            <label for="companies" class="col-md-2 col-form-label text-md-right">Compañía:</label>
                            <div class="col-md-4">
                                <select class="form-control" id="company_id"  name="company_id" class="form-control" >
                                    @foreach($companies as $company)
                                        @if ( $var->company_id == $company->id)
                                            <option selected style="backgroud-color:blue;" value="{{$var->company_id}}"><strong>{{ $company->razon_social }}</strong></option>
                                        @endif
                                    @endforeach
                                    <option class="hidden" disabled data-color="#A0522D" >------------------</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company['id'] }}" >
                                            {{ $company['name'] }}
                                        </option>
                                    @endforeach </select>
                            </div>
                        
                        </div>
                       
                        <div class="form-group row">
                                <label for="description" class="col-md-2 col-form-label text-md-right">Nombre</label>

                                <div class="col-md-4">
                                    <input id="description" type="text" class="form-control @error('description') is-invalid @enderror" name="description" value="{{ $var->description }}" required autocomplete="description">

                                    @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <label for="direction" class="col-md-2 col-form-label text-md-right">Dirección</label>

                                <div class="col-md-4">
                                    <input id="direction" type="text" class="form-control @error('direction') is-invalid @enderror" name="direction" value="{{ $var->direction }}" required autocomplete="direction">

                                    @error('direction')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                        </div>
                        <div class="form-group row">
                            <label for="phone" class="col-md-2 col-form-label text-md-right">Teléfono</label>

                            <div class="col-md-4">
                                <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ $var->phone }}" maxlength="30" required autocomplete="phone">

                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="phone2" class="col-md-2 col-form-label text-md-right">Teléfono 2</label>

                            <div class="col-md-4">
                                <input id="phone2" type="text" class="form-control @error('phone2') is-invalid @enderror" name="phone2" value="{{ $var->phone2 }}" maxlength="30" required autocomplete="phone2">

                                @error('phone2')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                       

                        <div class="form-group row">
                            <label for="person_contact" class="col-md-2 col-form-label text-md-right">Persona Contácto</label>

                            <div class="col-md-4">
                                <input id="person_contact" type="text" class="form-control @error('person_contact') is-invalid @enderror" name="person_contact" value="{{ $var->person_contact }}" required autocomplete="person_contact">

                                @error('person_contact')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                              
                            <label for="phone_contact" class="col-md-2 col-form-label text-md-right">Teléfono Contácto</label>

                            <div class="col-md-4">
                                <input id="phone_contact" type="text" class="form-control @error('phone_contact') is-invalid @enderror" name="phone_contact" value="{{ $var->phone_contact }}" required autocomplete="phone_contact">

                                @error('phone_contact')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                       
                        <div class="form-group row">
                            <label for="observation" class="col-md-2 col-form-label text-md-right">Observación</label>

                            <div class="col-md-4">
                                <input id="observation" type="text" class="form-control @error('observation') is-invalid @enderror" name="observation" value="{{ $var->observation }}" required autocomplete="observation">

                                @error('observation')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="status" class="col-md-2 col-form-label text-md-right">Status</label>

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
                        <div class="form-group row mb-0">
                            <div class="form-group col-sm-2">
                            </div>    
                            <div class="form-group col-sm-4">
                                <button type="submit" class="btn btn-success btn-block"><i class="fa fa-send-o"></i>Actualizar</button>
                            </div>
                            <div class="form-group col-sm-4">
                                <a href="{{ route('warehouse') }}" name="danger" type="button" class="btn btn-danger btn-block">Cancelar</a>
                            </div>
                        </div>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('validacion')
    <script>    
    </script>
@endsection
@section('javascript_edit')
<script>
</script>
@endsection