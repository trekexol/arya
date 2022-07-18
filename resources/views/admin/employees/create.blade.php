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
                <div class="card-header text-center font-weight-bold h3">Registro de Empleados</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('employees.store') }}" enctype="multipart/form-data">
                        @csrf
                       
                        <div class="form-group row">
                            <label for="nombres" class="col-md-2 col-form-label text-md-right">Nombres</label>

                            <div class="col-md-4">
                                <input id="nombres" type="text" class="form-control @error('nombres') is-invalid @enderror" name="nombres" value="{{ old('nombres') }}" required autocomplete="nombres" autofocus>

                                @error('nombres')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="apellidos" class="col-md-2 col-form-label text-md-right">Apellidos</label>

                            <div class="col-md-4">
                                <input id="apellidos" type="text" class="form-control @error('apellidos') is-invalid @enderror" name="apellidos" value="{{ old('apellidos') }}" required autocomplete="apellidos">

                                @error('apellidos')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="id_empleado" class="col-md-2 col-form-label text-md-right">Cédula</label>
                            <div class="col-md-1 col-sm-1">
                                <select id="type_code" name="type_code" class="select2_single form-control">
                                    <option value="V-">V-</option>
                                    <option value="E-">E-</option>
                                    <option value="J-">J-</option>
                                    <option value="G-">G-</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input id="id_empleado" type="text" class="form-control @error('id_empleado') is-invalid @enderror" name="id_empleado" value="{{ old('id_empleado') }}" required autocomplete="id_empleado">

                                @error('id_empleado')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="telefono1" class="col-md-2 col-form-label text-md-right">Teléfono</label>

                            <div class="col-md-4">
                                <input id="telefono1" type="text" class="form-control @error('telefono1') is-invalid @enderror" name="telefono1" value="{{ old('telefono1') }}" placeholder="Ej: 0414 xxx-xxxx" required autocomplete="telefono1">

                                @error('telefono1')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                                    
                            <label for="direccion" class="col-md-2 col-form-label text-md-right">Dirección</label>
                        
                            <div class="col-md-4">
                                
                                <input type="text" class="form-control" id="direccion" name="direccion" required value="{{ old('direccion')}}" placeholder="Agregar Ubicación">
                            </div>
                        
                        
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
                        </div>
                        <div class="form-group row">
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
                            
                            <label for="parroquia" class="col-md-2 col-form-label text-md-right">Parroquia</label>
                        
                            <div class="col-md-4">
                                <select class="form-control" id="parroquia"  name="Parroquia" required class="form-control" value="{{ old('Parroquia')}}" >
                                    <option value="">Selecciona un Parroquia</option>
                                </select>
                            </div>
                    
                        </div>  

                       


                        <div class="form-group row">
                            <label for="code_employee" class="col-md-2 col-form-label text-md-right">Código de Empleado (Opcional)</label>

                            <div class="col-md-4">
                                <input id="code_employee" type="text" class="form-control @error('code_employee') is-invalid @enderror" name="code_employee" value="{{ old('code_employee') }}" autocomplete="code_employee">

                                @error('code_employee')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="email" class="col-md-2 col-form-label text-md-right">Correo Electrónico</label>

                            <div class="col-md-4">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            
                        </div>
                        <div class="form-group row">
                            <label for="monto_pago" class="col-md-2 col-form-label text-md-right">Salario Bs.</label>

                            <div class="col-md-3">
                                <input id="monto_pago" type="text" class="form-control @error('monto_pago') is-invalid @enderror" name="monto_pago" value="{{ old('monto_pago') }}" placeholder="Ej: 0,00" required autocomplete="monto_pago">

                                @error('monto_pago')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <label for="asignacion_general" class="col-md-3 col-form-label text-md-right">Asignación General $</label>
                            <div class="col-md-4">
                                <input id="asignacion_general" type="text" class="form-control @error('asignacion_general') is-invalid @enderror" name="asignacion_general" value="{{ old('asignacion_general') }}" autocomplete="asignacion_general">

                                @error('asignacion_general')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                        </div>


                        <div class="form-group row">
                            <label for="fecha_ingreso" class="col-md-2 col-form-label text-md-right">Fecha de Ingreso</label>

                            <div class="col-md-4">
                                <input id="fecha_ingreso" type="date" class="form-control @error('fecha_ingreso') is-invalid @enderror" name="fecha_ingreso" value="{{ old('fecha_ingreso') }}" required autocomplete="fecha_ingreso">

                                @error('fecha_ingreso')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="fecha_nacimiento" class="col-md-2 col-form-label text-md-right">Fecha de Nacimiento</label>

                            <div class="col-md-4">
                                <input id="fecha_nacimiento" type="date" class="form-control @error('fecha_nacimiento') is-invalid @enderror" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" required autocomplete="fecha_nacimiento">

                                @error('fecha_nacimiento')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="position" class="col-md-2 col-form-label text-md-right">Cargo</label>

                            <div class="col-md-4">
                            <select class="form-control" id="position_id" name="position_id">
                                @foreach($position as $var)
                                    <option value="{{ $var->id }}">{{ $var->name }}</option>
                                @endforeach
                              
                            </select>
                            </div>
                            <label for="profession" class="col-md-2 col-form-label text-md-right">Tipo de Nómina</label>

                            <div class="col-md-4">
                            <select class="form-control" id="profession_id" name="profession_id">
                                @foreach($profession as $var)
                                    <option value="{{ $var->id }}">{{ $var->name }}</option>
                                @endforeach
                              
                            </select>
                            </div>
                        </div>

               

                        
                       <div class="form-group row">
                            
                           <label for="salarytype" class="col-md-2 col-form-label text-md-right">Tipo de Salario</label>

                            <div class="col-md-4">
                            <select class="form-control" id="salarytype_id" name="salarytype_id">
                                @foreach($salarytype as $var)
                                    @if($var->id == 2)
                                      <option selected value="{{ $var->id}}">{{ $var->name }}</option>
                                    @else
                                      <option value="{{ $var->id}}">{{ $var->name }}</option>
                                    @endif     
                                @endforeach
                              
                            </select>
                            </div>

                        </div> 
                        
                        <div class="form-group row">
                            <label for="dias_pres_acumulado" class="col-md-3 col-form-label text-md-right">Dias de Prestaciones Acum.</label>

                            <div class="col-md-2">
                                <input id="dias_pres_acumulado" type="text" class="form-control @error('dias_pres_acumulado') is-invalid @enderror" name="dias_pres_acumulado" value="{{ old('dias_pres_acumulado') ?? 0 }}"  autocomplete="dias_pres_acumulado">

                                @error('dias_pres_acumulado')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="dias_vaca_acumulado" class="col-md-3 col-form-label text-md-right">Dias de Vacaciones Acum.</label>

                            <div class="col-md-2">
                                <input id="dias_vaca_acumulado" type="text" class="form-control @error('dias_vaca_acumulado') is-invalid @enderror" name="dias_vaca_acumulado" value="{{ old('dias_vaca_acumulado') ?? 0 }}" autocomplete="dias_vaca_acumulado">

                                @error('dias_vaca_acumulado')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="acumulado_prestaciones" class="col-md-2 col-form-label text-md-right">Acumulado Prestaciones</label>

                            <div class="col-md-4">
                                <input id="acumulado_prestaciones" type="text" class="form-control @error('acumulado_prestaciones') is-invalid @enderror" name="acumulado_prestaciones" value="{{ old('acumulado_prestaciones') ?? 0}}" autocomplete="acumulado_prestaciones">

                                @error('acumulado_prestaciones')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="intereses_prest_acumulado" class="col-md-2 col-form-label text-md-right">Intereses de Prestaciones Acum.</label>

                            <div class="col-md-4">
                                <input id="intereses_prest_acumulado" type="text" class="form-control @error('intereses_prest_acumulado') is-invalid @enderror" name="intereses_prest_acumulado" value="{{ old('intereses_prest_acumulado') ?? 0 }}" autocomplete="intereses_prest_acumulado">

                                @error('intereses_prest_acumulado')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                        </div>

                        <div class="form-group row">

                            <label for="acumulado_utilidades" class="col-md-2 col-form-label text-md-right">Acumulado Utilidades</label>

                            <div class="col-md-4">
                                <input id="acumulado_utilidades" type="text" class="form-control @error('acumulado_utilidades') is-invalid @enderror" name="acumulado_utilidades" value="{{ old('acumulado_utilidades') ?? 0 }}" autocomplete="acumulado_utilidades">

                                @error('acumulado_utilidades')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="amount_utilities" class="col-md-2 col-form-label text-md-right">Monto de Utilidades</label>
                            <div class="col-md-4">
                                <select class="form-control" name="amount_utilities" id="amount_utilities">
                                    <option value="Ma">Máximo</option>
                                    <option value="Mi">Minimo</option>
                                </select>
                                </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="centro_costo" class="col-md-2 col-form-label text-md-right">Centro Costo</label>

                                <div class="col-md-4">
                                    <select class="form-control" id="centro_costo" name="centro_costo">
                                        @foreach($centro_costo as $var)
                                            <option value="{{ $var->id }}">{{ $var->description }}</option>
                                        @endforeach
                                    
                                    </select>
                                </div>

                                <label for="rol" class="col-md-2 col-form-label text-md-right">Status</label>
        
                                <div class="col-md-4">
                                    <select class="form-control" id="status" name="status" title="status" required>

                                        <div class="dropdown">
                                            <option selected value="1">Activo</option>
                                            <option value="0">Inactivo</option>
                                            <option value="2">Reposo</option>
                                            <option value="3">Reposo Pre/Pos Parto</option>
                                            <option value="4">Vacaciones</option>
                                            <option value="5">Liquidado</option>                                            
                                            <option value="6">De Permiso</option>
                                            <option value="7">Año Sabatico</option>
                                        </div>
          
                                    </select>
                                </div>
                          
                        </div>
                        
                        <br>
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                   Registrar Empleado
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
            $("#id_empleado").mask('000.000.000.000.000', { reverse: true });
            
        }); 
        $(document).ready(function () {
            $("#telefono1").mask('0000 000-0000', { reverse: true });
            
        }); 
        $(document).ready(function () {
            $("#acumulado_prestaciones").mask('000.000.000.000.000,00', { reverse: true });
            
        });
        
        $(document).ready(function () {
            $("#intereses_prest_acumulado").mask('000.000.000.000.000,00', { reverse: true });
            
        });     
        $(document).ready(function () {
            $("#acumulado_utilidades").mask('000.000.000.000.000,00', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#monto_pago").mask('000.000.000.000.000,00', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#asignacion_general").mask('000.000.000.000.000,00', { reverse: true });
            
        });   
        </script>
    <script>    
        $(function(){
            soloLetras('nombres');
            soloLetras('apellidos');
          
            soloAlfaNumerico('code_employee');
            soloAlfaNumerico('direccion');
        });
    </script>
@endsection

@section('javascript')
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
