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
                <div class="card-header text-center font-weight-bold h3">Registro de Vendedores e</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('vendors.store') }}" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" class="form-control" name="user_id" value="{{ Auth::user()->id }}" readonly>


                        <div class="form-group row">
                            <label for="code" class="col-md-2 col-form-label text-md-right">Código de Vendedor (Opcional)</label>

                            <div class="col-md-4">
                                <input id="code" type="text" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ 0 ?? old('code') }}" autocomplete="code" autofocus>

                                @error('code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="cedula_rif" class="col-md-2 col-form-label text-md-right">Cédula o Rif</label>
                            <div class="col-md-1 col-sm-1">
                                <select id="type_code" name="type_code" class="select2_single form-control">
                                    <option value="V-">V-</option>
                                    <option value="E-">E-</option>
                                    <option value="J-">J-</option>
                                    <option value="G-">G-</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input id="cedula_rif" type="text" class="form-control @error('cedula_rif') is-invalid @enderror" name="cedula_rif" value="{{ old('cedula_rif') }}" required autocomplete="cedula_rif">

                                @error('cedula_rif')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="name" class="col-md-2 col-form-label text-md-right">Nombre</label>

                            <div class="col-md-4">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name">

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="surname" class="col-md-2 col-form-label text-md-right">Apellido</label>

                            <div class="col-md-4">
                                <input id="surname" type="text" class="form-control @error('surname') is-invalid @enderror" name="surname" value="{{ old('surname') }}" required autocomplete="surname">

                                @error('surname')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email" class="col-md-2 col-form-label text-md-right">Correo Electrónico</label>

                            <div class="col-md-4">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') ?? '@.com' }}" autocomplete="email" required>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="comision" class="col-md-2 col-form-label text-md-right">Comisión</label>

                            <div class="col-md-4">
                                <input id="comision" type="text" class="form-control @error('comision') is-invalid @enderror" name="comision" value="{{ old('comision') ?? 0 }}" required autocomplete="comision">

                                @error('comision')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                        </div>

                        <div class="form-group row">

                            <label for="comision_id" class="col-md-2 col-form-label text-md-right">Tipo de Comisión
                            </label>

                            <div class="col-md-4">
                            <select class="form-control" id="comision_id" name="comision_id">
                                @foreach($comisions as $var)
                                    <option value="{{ $var->id }}">{{ $var->description }}</option>
                                @endforeach

                            </select>
                            </div>
                            <label for="employee" class="col-md-2 col-form-label text-md-right">Empleado</label>

                            <div class="col-md-4">

                                @if (count($employees) == 0)
                                <select class="form-control" id="employee_id" name="employee_id">
                                    <option selected value="0">Ninguno</option>

                                    </select>

                                @else

                                    <select class="form-control" id="employee_id" name="employee_id">
                                        <option selected value="">Ninguno</option>
                                    @foreach($employees as $var)
                                        <option value="{{ $var->id }}">{{ $var->nombres }}</option>
                                    @endforeach
                                    </select>
                                @endif

                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="phone" class="col-md-2 col-form-label text-md-right">Teléfono</label>

                            <div class="col-md-4">
                                <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') ?? 0}}" autocomplete="phone">

                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="phone2" class="col-md-2 col-form-label text-md-right">Teléfono 2</label>

                            <div class="col-md-4">
                                <input id="phone2" type="text" class="form-control @error('phone2') is-invalid @enderror" name="phone2" value="{{ old('phone2') ?? 0}}" autocomplete="phone2">

                                @error('phone2')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">

                            <label for="estado" class="col-md-2 col-form-label text-md-right">Estado</label>

                            <div class="col-md-4">
                                <select id="estado"  name="estado" class="form-control" required>
                                    <option value="">Seleccione un Estado</option>
                                    @foreach($estados as $index => $value)
                                        <option value="{{ $index }}" {{ old('Estado') == $index ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                    </select>

                                    @if ($errors->has('estado_id'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('estado_id') }}</strong>
                                        </span>
                                    @endif
                            </div>

                            <label for="municipio" class="col-md-2 col-form-label text-md-right">Municipio</label>

                            <div class="col-md-4">
                                <select  id="municipio"  name="Municipio" class="form-control" required>
                                    <option value="">Selecciona un Municipio</option>
                                </select>

                                @if ($errors->has('municpio_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('municpio_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="parroquia" class="col-md-2 col-form-label text-md-right">Parroquia</label>

                            <div class="col-md-4">
                                <select class="form-control" id="parroquia"  name="Parroquia" required class="form-control" value="{{ old('Parroquia')}}" >
                                    <option value="">Selecciona un Parroquia</option>
                                </select>
                            </div>


                           <!-- <label for="direccion" class="col-md-2 col-form-label text-md-right">Dirección</label>

                            <div class="col-md-4">

                                <input type="text" class="form-control" id="direction" name="direction" required value="{{ old('direction')}}" >
                            </div>-->
                        </div>


                        <div class="form-group row">
                            <label for="instagram" class="col-md-2 col-form-label text-md-right">Instagram</label>

                            <div class="col-md-4">
                                <input id="instagram" type="text" class="form-control @error('instagram') is-invalid @enderror" name="instagram" value="{{ old('instagram') ?? 'N/A' }}"  autocomplete="instagram">

                                @error('instagram')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="facebook" class="col-md-2 col-form-label text-md-right">Facebook</label>

                            <div class="col-md-4">
                                <input id="facebook" type="text" class="form-control @error('facebook') is-invalid @enderror" name="facebook" value="{{ old('facebook') ?? 'N/A' }}"  autocomplete="facebook">

                                @error('facebook')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="twitter" class="col-md-2 col-form-label text-md-right">Twitter</label>

                            <div class="col-md-4">
                                <input id="twitter" type="text" class="form-control @error('twitter') is-invalid @enderror" name="twitter" value="{{ old('twitter') ?? 'N/A' }}"  autocomplete="twitter">

                                @error('twitter')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="especification" class="col-md-2 col-form-label text-md-right">Especificación (Opcional)</label>

                            <div class="col-md-4">
                                <input id="especification" type="text" class="form-control @error('especification') is-invalid @enderror" name="especification" value="{{ old('especification') ?? 'N/A' }}"  autocomplete="especification">

                                @error('especification')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="observation" class="col-md-2 col-form-label text-md-right">Observación</label>

                            <div class="col-md-4">
                                <input id="observation" type="text" class="form-control @error('observation') is-invalid @enderror" name="observation" value="{{ old('observation') ?? '' }}"  autocomplete="observation">

                                @error('observation')
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
                                   Registrar Vendedor
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('vendors') }}" id="btnfacturar" name="btnfacturar" class="btn btn-danger" title="facturar">Volver</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('validacion_usuario')
<script>
    $(document).ready(function () {
        $("#comision").mask('000.000.000.000.000', { reverse: true });

    });
    $(document).ready(function () {
        $("#phone").mask('0000 000-0000', { reverse: true });

    });
    $(document).ready(function () {
        $("#phone2").mask('0000 000-0000', { reverse: true });

    });
    $(document).ready(function () {
        $("#cedula_rif").mask('000.000.000', { reverse: true });

    });
    </script>
<script>

$(function(){
    soloAlfaNumerico('code');
    soloLetras('name');
    soloLetras('surname');
});

</script>
@endsection

@section('validacion_vendor')
    <script>

            $("#estado").on('change',function(){
                var estado_id = $(this).val();
                $("#municipio").val("");
                $("#parroquia").val("");
                // alert(estado_id);
                getMunicipios(estado_id);
            });

        function getMunicipios(estado_id){
            // alert(`../municipio/list/${estado_id}`);
            $.ajax({
                url:`../municipio/list/${estado_id}`,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                    let municipio = $("#municipio");
                    let htmlOptions = `<option value='' >Seleccione..</option>`;
                    // console.clear();
                    if(response.length > 0){
                        response.forEach((item, index, object)=>{
                            let {id,descripcion} = item;
                            htmlOptions += `<option value='${id}' {{ old('Municipio') == '${id}' ? 'selected' : '' }}>${descripcion}</option>`

                        });
                    }
                    //console.clear();
                    // console.log(htmlOptions);
                    municipio.html('');
                    municipio.html(htmlOptions);



                },
                error:(xhr)=>{
                    alert('Presentamos inconvenientes al consultar los datos');
                }
            })
        }

        $("#municipio").on('change',function(){
                var municipio_id = $(this).val();
                var estado_id    = document.getElementById("estado").value;
                getParroquias(municipio_id,estado_id);
            });

        function getParroquias(municipio_id,estado_id){
            // alert(`../parroquia/list/${municipio_id}/${estado_id}`);
            $.ajax({
                url:`../parroquia/list/${municipio_id}/${estado_id}`,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                    let parroquia = $("#parroquia");
                    let htmlOptions = `<option value='' >Seleccione..</option>`;
                    // console.clear();
                    if(response.length > 0){
                        response.forEach((item, index, object)=>{
                            let {id,descripcion} = item;
                            htmlOptions += `<option value='${id}' {{ old('Parroquia') == '${descripcion}' ? 'selected' : '' }} >${descripcion}</option>`

                        });
                    }
                    // console.clear();
                    // console.log(htmlOptions);
                    parroquia.html('');
                    parroquia.html(htmlOptions);
                },
                error:(xhr)=>{
                    alert('Presentamos inconvenientes al consultar los datos');
                }
            })
        }
    </script>
@endsection
