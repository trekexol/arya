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
        
                                    <div class="col-md-4">
                                        <input id="id_empleado" type="text" class="form-control @error('id_empleado') is-invalid @enderror" name="id_empleado" value="{{ $var->id_empleado  }}" required autocomplete="id_empleado">
        
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
                                    <label for="code_employee" class="col-md-2 col-form-label text-md-right">Código de Empleado (Opcional)</label>
        
                                    <div class="col-md-4">
                                        <input id="code_employee" type="text" class="form-control @error('code_employee') is-invalid @enderror" name="code_employee" value="{{ $var->code_employee }}" autocomplete="code_employee">
        
                                        @error('code_employee')
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
                                        <select  id="salarytype"  name="salarytype_id" class="form-control">
                                            @foreach($salarytypes as $salarytype)
                                                @if ( $var->salarytype_id == $salarytype->id)
                                                    <option selected style="backgroud-color:blue;" value="{{$salarytype->id}}"><strong>{{ $salarytype->name }}</strong></option>
                                                @endif
                                            @endforeach
                                            <option class="hidden" disabled data-color="#A0522D" value="-1">------------------</option>
                                            @foreach($salarytypes as $salarytype)
                                                <option value="{{ $salarytype['id'] }}" >
                                                    {{ $salarytype['name'] }}
                                                </option>
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
                                    <label for="acumulado_prestaciones" class="col-md-2 col-form-label text-md-right">Acumulado Prestaciones</label>
        
                                    <div class="col-md-4">
                                        <input id="acumulado_prestaciones" type="text" class="form-control @error('acumulado_prestaciones') is-invalid @enderror" name="acumulado_prestaciones" value="{{ $var->acumulado_prestaciones }}" required autocomplete="acumulado_prestaciones">
        
                                        @error('acumulado_prestaciones')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <label for="acumulado_utilidades" class="col-md-2 col-form-label text-md-right">Acumulado Utilidades</label>
        
                                    <div class="col-md-4">
                                        <input id="acumulado_utilidades" type="text" class="form-control @error('acumulado_utilidades') is-invalid @enderror" name="acumulado_utilidades" value="{{ $var->acumulado_utilidades }}" required autocomplete="acumulado_utilidades">
        
                                        @error('acumulado_utilidades')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label for="centro_costo" class="col-md-2 col-form-label text-md-right">Centro Costo</label>
        
                                    <div class="col-md-4">
                                        <input id="centro_costo" type="text" class="form-control @error('centro_costo') is-invalid @enderror" name="centro_costo" value="{{ $var->centro_cos }}" autocomplete="centro_costo">
        
                                        @error('centro_costo')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
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
        soloNumeros('telefono1');
        soloNumeros('telefono2');
        soloAlfaNumerico('code_employee');
        soloAlfaNumerico('direccion');
    });

    $(document).ready(function () {
            $("#monto_pago").mask('000.000.000.000.000.000,00', { reverse: true });
            
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