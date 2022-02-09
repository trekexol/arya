@extends('admin.layouts.dashboard')

@section('content')
  
    <!-- container-fluid -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="row py-lg-2">
            <div class="col-md-6">
                <h2>Editar Vendedor</h2>
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
            <form  method="POST"   action="{{ route('vendors.update',$var->id) }}" enctype="multipart/form-data" >
                @method('PATCH')
                @csrf()
                <div class="container py-2">
                    <div class="row">
                        <div class="col-12 ">
                            <form >
                                <input type="hidden" class="form-control" name="user_id" value="{{ Auth::user()->id }}" readonly>
                               

                        <div class="form-group row">
                            <label for="code" class="col-md-2 col-form-label text-md-right">Código</label>

                            <div class="col-md-4">
                                <input id="code" type="text" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ $var->code }}" autocomplete="code" autofocus>

                                @error('code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="cedula_rif" class="col-md-2 col-form-label text-md-right">Cédula o Rif</label>

                            <div class="col-md-4">
                                <input id="cedula_rif" type="text" class="form-control @error('cedula_rif') is-invalid @enderror" name="cedula_rif" value="{{ $var->cedula_rif }}" required autocomplete="cedula_rif">

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
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $var->name }}" required autocomplete="name">

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="surname" class="col-md-2 col-form-label text-md-right">Apellido</label>

                            <div class="col-md-4">
                                <input id="surname" type="text" class="form-control @error('surname') is-invalid @enderror" name="surname" value="{{ $var->surname }}" autocomplete="surname">

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
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $var->email }}" autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="comision" class="col-md-2 col-form-label text-md-right">Comisión</label>

                            <div class="col-md-4">
                                <input id="comision" type="text" class="form-control @error('comision') is-invalid @enderror" name="comision" value="{{ $var->comision ?? 0 }}" autocomplete="comision">

                                @error('comision')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            
                        </div>

                        <div class="form-group row">
                            <label for="comision" class="col-md-2 col-form-label text-md-right">Tipo de Comisión</label>
                            <div class="col-md-4">
                                <select id="comision_id" name="comision_id" class="form-control" required>
                                    @foreach($comisions as $comision)
                                        @if ( $var->comision_id == $comision->id   )
                                            <option  selected style="backgroud-color:blue;" value="{{ $comision->id }}"><strong>{{ $comision->description }}</strong></option>
                                        @endif
                                    @endforeach
                                    <option class="hidden" disabled data-color="#A0522D" value="-1">------------------</option>
                                    @foreach($comisions as $comision)
                                        <option value="{{ $comision['id'] }}" >
                                            {{ $comision['description'] }}
                                        </option>
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
                                <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ $var->phone }}" autocomplete="phone">

                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="phone2" class="col-md-2 col-form-label text-md-right">Teléfono 2</label>

                            <div class="col-md-4">
                                <input id="phone2" type="text" class="form-control @error('phone2') is-invalid @enderror" name="phone2" value="{{ $var->phone2 }}" autocomplete="phone2">

                                @error('phone2')
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
                            <label for="instagram" class="col-md-2 col-form-label text-md-right">Instagram</label>

                            <div class="col-md-4">
                                <input id="instagram" type="text" class="form-control @error('instagram') is-invalid @enderror" name="instagram" value="{{ $var->instagram ?? 'N/A' }}"  autocomplete="instagram">

                                @error('instagram')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="facebook" class="col-md-2 col-form-label text-md-right">Facebook</label>

                            <div class="col-md-4">
                                <input id="facebook" type="text" class="form-control @error('facebook') is-invalid @enderror" name="facebook" value="{{ $var->facebook ?? 'N/A'}}"  autocomplete="facebook">

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
                                <input id="twitter" type="text" class="form-control @error('twitter') is-invalid @enderror" name="twitter" value="{{ $var->twitter ?? 'N/A'}}"  autocomplete="twitter">

                                @error('twitter')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="especification" class="col-md-2 col-form-label text-md-right">Especificación</label>

                            <div class="col-md-4">
                                <input id="especification" type="text" class="form-control @error('especification') is-invalid @enderror" name="especification" value="{{ $var->especification ?? 'N/A'}}"  autocomplete="especification">

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
                                <input id="observation" type="text" class="form-control @error('observation') is-invalid @enderror" name="observation" value="{{ $var->observation }}"  autocomplete="observation">

                                @error('observation')
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
                                    <div class="col-md-3 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                           Actualizar Empleado
                                        </button>
                                    </div>
                                    <div class="col-md-2">
                                        <a href="{{ route('vendors') }}" name="danger" type="button" class="btn btn-danger btn-block">Cancelar</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    @endsection
                    @section('validacion_usuario')
                    <script>
                         $(document).ready(function () {
                            $("#comision").mask('000.000.000.000.000,00', { reverse: true });
                            
                        });
                    $(function(){
                        soloAlfaNumerico('code');
                        soloNumeros('cedula_rif');
                        soloLetras('name');
                        soloLetras('surname');
                        soloNumeros('phone');
                        soloNumeros('phone2');
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
                       
                    </script>
                @endsection