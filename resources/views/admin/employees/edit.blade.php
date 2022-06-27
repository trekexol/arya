@extends('admin.layouts.dashboard')

@section('content')
  
    <!-- container-fluid -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="row py-lg-2">
            <div class="col-md-6">
                <h2>Editar Empleado</h2>
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
            <form  method="POST"   action="{{ route('employees.update',$var->id) }}" enctype="multipart/form-data" >
                @method('PATCH')
                @csrf()
                <div class="container py-2">
                    <div class="row">
                        <div class="col-12 ">
                            <form >
                                <div class="form-group row">
                                    <label for="nombres" class="col-md-2 col-form-label text-md-right">Nombres</label>
        
                                    <div class="col-md-4">
                                        <input id="nombres" type="text" class="form-control @error('nombres') is-invalid @enderror" name="nombres" value="{{ $var->nombres }}" required autocomplete="nombres" autofocus>
        
                                        @error('nombres')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <label for="apellidos" class="col-md-2 col-form-label text-md-right">Apellidos</label>
        
                                    <div class="col-md-4">
                                        <input id="apellidos" type="text" class="form-control @error('apellidos') is-invalid @enderror" name="apellidos" value="{{ $var->apellidos }}" required autocomplete="apellidos">
        
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
                                            @if ($var->id_empleado[0] == 'V')
                                                <option selected value="V-">V-</option>
                                                <option value="">----------</option>
                                            @endif
                                            @if ($var->id_empleado[0] == 'E')
                                                <option selected value="E-">E-</option>
                                                <option value="">----------</option>
                                            @endif
                                            @if ($var->id_empleado[0] == 'J')
                                                <option selected value="J-">J-</option>
                                                <option value="">----------</option>
                                            @endif
                                            @if ($var->id_empleado[0] == 'G')
                                                <option selected value="G-">G-</option>
                                                <option value="">----------</option>
                                            @endif
                                            <option value="V-">V-</option>
                                            <option value="E-">E-</option>
                                            <option value="J-">J-</option>
                                            <option value="G-">G-</option>
                                        </select>
                                    </div>
                                    @php
                                        if(substr($var->id_empleado, 0, 2) == 'V-'){
                                            $code_filter = substr($var->id_empleado,2);
                                        }if(substr($var->id_empleado, 0, 2) == 'E-'){
                                            $code_filter = substr($var->id_empleado,2);
                                        }if(substr($var->id_empleado, 0, 2) == 'J-'){
                                            $code_filter = substr($var->id_empleado,2);
                                        }if(substr($var->id_empleado, 0, 2) == 'G-'){
                                            $code_filter = substr($var->id_empleado,2);
                                        }
                                    @endphp
                                    <div class="col-md-3">
                                        <input id="id_empleado" type="text" class="form-control @error('id_empleado') is-invalid @enderror" name="id_empleado" value="{{ $code_filter ?? $var->id_empleado  }}" required autocomplete="id_empleado">
        
                                        @error('id_empleado')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <label for="telefono1" class="col-md-2 col-form-label text-md-right">Teléfono</label>
        
                                    <div class="col-md-4">
                                        <input id="telefono1" type="text" class="form-control @error('telefono1') is-invalid @enderror" name="telefono1" value="{{ $var->telefono1  }}" required autocomplete="telefono1">
        
                                        @error('telefono1')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
        
                                <div class="form-group row">
                                    <label for="email" class="col-md-2 col-form-label text-md-right">Correo Electrónico</label>
        
                                    <div class="col-md-10">
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $var->email }}" required autocomplete="email">
        
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    
                                </div>
                                <div class="form-group row">
                                    <label for="estado" class="col-md-2 col-form-label text-md-right">Estado:</label>
                                    <div class="col-md-4">
                                        <select id="estado"  name="estado" class="form-control" required>
                                            @foreach($estados as $estado)
                                                @if ( $var->estado_id == $estado->id   )
                                                    <option selected style="backgroud-color:blue;" value="{{$var->estado_id}}"><strong>{{ $estado->descripcion }}</strong></option>
                                                @endif
                                            @endforeach
                                            <option class="hidden" disabled data-color="#A0522D" value="-1">------------------</option>
                                            @foreach($estados as $estado)
                                                <option value="{{ $estado['id'] }}" >
                                                    {{ $estado['descripcion'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div> 
                                    
                                    <label for="municipio" class="col-md-2 col-form-label text-md-right">Municipio:</label>
                                    <div class="col-md-4">
                                        <select  id="municipio"  name="Municipio" class="form-control">
                                            @foreach($municipios as $municipio)
                                                @if ( $var->municipio_id == $municipio->id)
                                                    <option selected style="backgroud-color:blue;" value="{{$var->municipio_id}}"><strong>{{ $municipio->descripcion }}</strong></option>
                                                @endif
                                            @endforeach
                                            <option class="hidden" disabled data-color="#A0522D" >------------------</option>
                                            @foreach($municipios as $municipio)
                                                <option value="{{ $municipio['id'] }}" >
                                                    {{ $municipio['descripcion'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                   
                                </div>
                             
                                <div class="form-group row">
                                    
                                    <label for="parroquia" class="col-md-2 col-form-label text-md-right">Parroquia:</label>
                                    <div class="col-md-4">
                                        <select class="form-control" id="parroquia"  name="Parroquia" class="form-control" >
                                            @foreach($parroquias as $parroquia)
                                                @if ( $var->parroquia_id == $parroquia->id)
                                                    <option selected style="backgroud-color:blue;" value="{{$var->parroquia_id}}"><strong>{{ $parroquia->descripcion }}</strong></option>
                                                @endif
                                            @endforeach
                                            <option class="hidden" disabled data-color="#A0522D" >------------------</option>
                                            @foreach($parroquias as $parroquia)
                                                <option value="{{ $parroquia['id'] }}" >
                                                    {{ $parroquia['descripcion'] }}
                                                </option>
                                            @endforeach </select>
                                    </div>
                                    <label for="direccion" class="col-md-2 col-form-label text-md-right">Dirección</label>
                                    
                                    <div class="col-md-4">
                                        
                                        <input type="text" class="form-control" id="direccion" name="direccion" required value="{{ $var->direccion }}" placeholder="Ej: La Paz">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="code_employee" class="col-md-2 col-form-label text-md-right">Código de Empleado (Opcional)</label>
        
                                    <div class="col-md-4">
                                        <input id="code_employee" type="text" class="form-control @error('code_employee') is-invalid @enderror" name="code_employee" value="{{ $var->code_employee }}" autocomplete="code_employee">
        
                                        @error('code_employee')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <label for="asignacion_general" class="col-md-2 col-form-label text-md-right">Asignación General</label>

                                    <div class="col-md-4">
                                        <input id="asignacion_general" type="text" class="form-control @error('asignacion_general') is-invalid @enderror" name="asignacion_general" value="{{ number_format($var->asignacion_general ?? 0, 2, ',', '.')}}" autocomplete="asignacion_general">
        
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
                                        <input id="fecha_ingreso" type="date" class="form-control @error('fecha_ingreso') is-invalid @enderror" name="fecha_ingreso" value="{{ $var->fecha_ingreso  }}" required autocomplete="fecha_ingreso">
        
                                        @error('fecha_ingreso')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <label for="fecha_nacimiento" class="col-md-2 col-form-label text-md-right">Fecha de Nacimiento</label>
        
                                    <div class="col-md-4">
                                        <input id="fecha_nacimiento" type="date" class="form-control @error('fecha_nacimiento') is-invalid @enderror" name="fecha_nacimiento" value="{{ $var->fecha_nacimiento }}" required autocomplete="fecha_nacimiento">
        
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
                                        <select id="position_id" name="position_id" class="form-control" required>
                                            @foreach($positions as $position)
                                                @if ( $var->position_id == $position->id   )
                                                    <option  selected style="backgroud-color:blue;" value="{{ $position->id }}"><strong>{{ $position->name }}</strong></option>
                                                @endif
                                            @endforeach
                                            <option class="hidden" disabled data-color="#A0522D" value="-1">------------------</option>
                                            @foreach($positions as $position)
                                                <option value="{{ $position['id'] }}" >
                                                    {{ $position['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div> 
                                    
                                    <label for="salarytype" class="col-md-2 col-form-label text-md-right">Tipo de Salario</label>
                                    <div class="col-md-4">
                                        <select  id="salarytype_id" name="salarytype_id" class="form-control">

                                            @foreach($salarytypes as $salarytype)
                                                      
                                                    @if ($var->salary_types_id == $salarytype->id)
                                                    <option selected style="backgroud-color:blue;" value="{{$salarytype->id}}"><strong>{{ $salarytype->name }}</strong></option>
                                                    @else 
                                                    <option style="backgroud-color:blue;" value="{{$salarytype->id}}"><strong>{{ $salarytype->name }}</strong></option>
                                                    @endif

                                            @endforeach

                                        </select>
                                    </div>
                                   
                                </div>
                              
                                <div class="form-group row">
                                    <label for="profession" class="col-md-2 col-form-label text-md-right">Tipo de Trabajador</label>
                                        <div class="col-md-4">
                                            <select  id="profession"  name="profession_id" class="form-control">
                                                @foreach($professions as $profession)
                                                    @if ( $var->profession_id == $profession->id)
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
                                    <label for="monto_pago" class="col-md-2 col-form-label text-md-right">Monto Pago</label>
        
                                    <div class="col-md-4">
                                        <input id="monto_pago" type="text" class="form-control @error('monto_pago') is-invalid @enderror" name="monto_pago" value="{{ number_format($var->monto_pago, 2, ',', '.')}}"  required autocomplete="monto_pago">
        
                                        @error('monto_pago')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="dias_pres_acumulado" class="col-md-3 col-form-label text-md-right">Dias de Prestaciones Acum.</label>
        
                                    <div class="col-md-2">
                                        <input id="dias_pres_acumulado" type="text" class="form-control @error('dias_pres_acumulado') is-invalid @enderror" name="dias_pres_acumulado" value="{{ $var->dias_acumulado_prestaciones }}"  autocomplete="dias_pres_acumulado">
        
                                        @error('dias_pres_acumulado')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <label for="dias_vaca_acumulado" class="col-md-3 col-form-label text-md-right">Dias de Vacaciones Acum.</label>
        
                                    <div class="col-md-2">
                                        <input id="dias_vaca_acumulado" type="text" class="form-control @error('dias_vaca_acumulado') is-invalid @enderror" name="dias_vaca_acumulado" value="{{ $var->dias_acumulado_vacaciones }}" autocomplete="dias_vaca_acumulado">
        
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
                                        <input id="acumulado_prestaciones" type="text" class="form-control @error('acumulado_prestaciones') is-invalid @enderror" name="acumulado_prestaciones" value="{{ $var->acumulado_prestaciones }}" required autocomplete="acumulado_prestaciones">
        
                                        @error('acumulado_prestaciones')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <label for="intereses_prest_acumulado" class="col-md-2 col-form-label text-md-right">Intereses de Prestaciones Acum.</label>

                                    <div class="col-md-4">
                                        <input id="intereses_prest_acumulado" type="text" class="form-control @error('intereses_prest_acumulado') is-invalid @enderror" name="intereses_prest_acumulado" value="{{ $var->int_acumulado_prestaciones }}" autocomplete="intereses_prest_acumulado">
        
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
                                        <input id="acumulado_utilidades" type="text" class="form-control @error('acumulado_utilidades') is-invalid @enderror" name="acumulado_utilidades" value="{{ $var->acumulado_utilidades }}" required autocomplete="acumulado_utilidades">
        
                                        @error('acumulado_utilidades')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                
                                    <label for="amount_utilities" class="col-md-2 col-form-label text-md-right">Monto de Utilidades</label>
                                    <div class="col-md-4">
                                        <select class="form-control" id="amount_utilities" name="amount_utilities" title="amount_utilities">
                                            @if($var->amount_utilities == "Ma")
                                                <option value="Ma">Máximo</option>
                                            @else
                                                <option value="Mi">Minimo</option>
                                            @endif
                                            <option value="nulo">----------------</option>
                                            
                                            <div class="dropdown">
                                                <option value="Ma">Máximo</option>
                                                <option value="Mi">Minimo</option>
                                            </div>
                                            
                                               
                                        </select>
                                    </div>
                                
                                </div>
                                
                                <div class="form-group row">
  
                                        <label for="centro_costo" class="col-md-2 col-form-label text-md-right">Centro Costo</label>

                                        <div class="col-md-4">
                                        <select class="form-control" id="centro_costo" name="centro_costo">
                                            @foreach($centro_costo as $cent)
                                                      
                                                    @if ($var->branch_id == $cent->id)
                                                    <option selected value="{{ $cent->id }}">{{ $cent->description }}</option>
                                                    @else
                                                    <option value="{{ $cent->id }}">{{ $cent->description }}</option>
                                                    @endif
                                            
                                            @endforeach
                                          
                                        </select>
                                        </div>

                                    <label for="rol" class="col-md-2 col-form-label text-md-right">Status</label>
        
                                    <div class="col-md-4">
                                        <select class="form-control" id="status" name="status" title="status">
  
                                            <div class="dropdown">
                                            @if($var->status == 1)
                                                <option selected value="1">Activo</option>
                                            @endif
                                            @if($var->status == 0)
                                                <option selected value="0">Inactivo</option>
                                            @endif
                                            @if($var->status == 2)
                                            <option selected value="2">Reposo</option>
                                            @endif
                                            @if($var->status == 3)
                                            <option selected value="3">Reposo Pre/Pos Parto</option>
                                            @endif
                                            @if($var->status == 4)
                                            <option selected value="4">Vacaciones</option>
                                            @endif
                                            @if($var->status == 5)
                                            <option selected value="5">Liquidado</option>
                                            @endif
                                            @if($var->status == 6)
                                            <option selected value="6">De Permiso</option>
                                            @endif
                                            @if($var->status == 7)
                                            <option selected value="7">Año Sabatico</option>
                                            @endif
                                            <option value="">---------------</option>
                                           
                                                <option value="1">Activo</option>
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
                                    <div class="col-sm-3 offset-sm-3">
                                        <button type="submit" class="btn btn-primary">
                                           Actualizar Empleado
                                        </button>
                                    </div>
                                    <div class="form-group col-sm-2">
                                        <a href="{{ route('employees') }}" name="danger" type="button" class="btn btn-danger btn-block">Regresar</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
@endsection
@section('validacion')
    <script>    
	$(function(){
        soloLetras('nombres');
        soloLetras('apellidos');
        soloAlfaNumerico('code_employee');
        soloAlfaNumerico('direccion');
    });

    $(document).ready(function () {
            $("#monto_pago").mask('000.000.000.000.000.000,00', { reverse: true });
            
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
            $("#telefono1").mask('0000 000-0000', { reverse: true });
            
        }); 

        $(document).ready(function () {
            $("#asignacion_general").mask('000.000.000.000.000,00', { reverse: true });
            
        });   
       
    </script>
@endsection
                @section('javascript_edit')
                    <script>
                            $("#estado").on('change',function(){
                                var estado_id = $(this).val();
                                // alert(estado_id);
                                getMunicipios(estado_id);
                            });
                
                        function getMunicipios(estado_id){
                           // alert(`../../municipio/list/${estado_id}`);
                            $.ajax({
                                url:`../../municipio/list/${estado_id}`,
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
                                            htmlOptions += `<option value='${id}'>${descripcion}</option>`;
                
                                        });
                                    }
                                    //console.clear();
                                    console.log(htmlOptions);
                                    municipio.html('');
                                    municipio.html(htmlOptions);
                                
                                    
                                
                                },
                                error:(xhr)=>{
                                    alert('Presentamos inconvenientes al consultar los datos');
                                }
                            })
                        }
                
                        $("#municipio").on('change',function(){
                                // var municipio_id = $(this).attr("id");
                                var municipio_id = $(this).val();
                                // alert(municipio_id);
                                var estado_id    = document.getElementById("estado").value;
                                getParroquias(municipio_id,estado_id);
                            });
                
                        function getParroquias(municipio_id,estado_id){
                            $.ajax({
                                url:`../../parroquia/list/${municipio_id}/${estado_id}`,
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
                                            htmlOptions += `<option value='${id}' >${descripcion}</option>`
                
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
                        // Funcion Solo Numero
                        $(function(){
                        soloNumeros('xtelf_local');
                        soloNumeros('xtelf_cel');
                        });
                    
                    </script>
                @endsection