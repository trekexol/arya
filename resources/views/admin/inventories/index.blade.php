@extends('admin.layouts.dashboard')

@section('header')

<style> 
    .krajee-default .file-caption-info,.krajee-default .file-size-info{display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;width:0px;height:0px;margin:auto}
    .file-zoom-content>.file-object.type-flash,.file-zoom-content>.file-object.type-image,.file-zoom-content>.file-object.type-video{max-width:100%;max-height:100%;width:auto}
    .file-zoom-content>.file-object.type-flash,.file-zoom-content>.file-object.type-video{height:100%}
    .file-zoom-content>.file-object.type-default,.file-zoom-content>.file-object.type-html,.file-zoom-content>.file-object.type-pdf,.file-zoom-content>.file-object.type-text{width:100%}
    .btn-file{overflow:hidden}
    .btn-file input[type=file]{top:0;left:0;min-width:100%;min-height:100%;text-align:right;opacity:0;background:none;cursor:inherit;display:none}
    .btn-file ::-ms-browse{font-size:10000px;width:100%;height:100%}
    .file-footer-buttons{margin-top: -31px;align-content:right; align:right; justify-content: right; text-align: right;}
    .file-zoom-dialog .file-other-icon{font-size:22em;font-size:50vmin}
    .file-zoom-dialog .modal-dialogimg{width:auto}
    .file-zoom-dialog .modal-header{display:flex;align-items:center;justify-content:space-between}
    .file-zoom-dialog .btn-navigate{margin:0 .1rem;padding:0;font-size:1.2rem;width:2.4rem;height:2.4rem;top:50%;border-radius:50%;text-align:center}
    .btn-navigate *{width:auto}
    .file-zoom-dialog .floating-buttons{top:5px;right:10px}
    .file-zoom-dialog .btn-kv-prev{left:0}
    .file-zoom-dialog .btn-kv-next{right:0}
    .file-zoom-dialog .kv-zoom-header{padding:0px}
    .file-zoom-dialog .kv-zoom-body{padding:.25rem}
    .file-zoom-dialog .kv-zoom-description{position:absolute;opacity:.8;font-size:.8rem;background-color:#1a1a1a;padding:1rem;text-align:center;border-radius:.5rem;color:#fff;left:15%;right:15%;bottom:15%}
    .file-zoom-dialog .kv-desc-hide{float:right;padding:0 .1rem;background:0 0;border:none}
    .file-input-ajax-new .no-browse .form-control,.file-input-new .no-browse .form-control{border-top-right-radius:4px;border-bottom-right-radius:4px}
    .file-drop-zone .file-preview-thumbnails{cursor:default}
    .floating-buttons .btn-kv{margin-right:-0px;z-index:3000}
    .kv-zoom-actions{min-width:140px}
    .kv-zoom-actions .btn-kv{margin-right:-0px}
    .file-zoom-content{text-align:center;white-space:nowrap;min-height:300px}
    .file-zoom-content .file-preview-image,.file-zoom-content .file-preview-video{max-height:100%}
    .file-zoom-content>.file-object.type-image{height:auto;min-height:inherit}
    .clickable .file-drop-zone-title{cursor:pointer}
    .file-grabbing,.file-grabbing *{cursor:not-allowed!important}
    .file-grabbing .file-preview-thumbnails *{cursor:grabbing!important}
    .file-preview .kv-zoom-cache{display:none}
    .file-preview-object,.file-preview-other-frame,.kv-zoom-body{display:flex;align-items:center;justify-content:center}
    .kv-file-remove i {display: none; position: fixed;}
    .fa-trash-alt i {display: none; position: fixed;}
   
       canvas{
           display: none;
           position: fixed;
           justify-content: center;
       }

       img {
       display: block;
       margin: 0 auto;
       max-width: 100%;
       }

       .btnimg {
        color:  rgb(78, 115, 223);opacity:0.8; 
        background-color: transparent;
        border-style: none !important;
       }
       
       .btnimg:hover { 
        background-color: rgb(253, 253, 253);opacity:0.8;
       }
   </style>
@endsection


@section('content')

<ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
      <a class="nav-link font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('products') }}" role="tab" aria-controls="home" aria-selected="true">Productos</a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link active font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('inventories') }}" role="tab" aria-controls="profile" aria-selected="false">Inventario</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('combos') }}" role="tab" aria-controls="home" aria-selected="true">Combos</a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('inventories.movement') }}" role="tab" aria-controls="contact" aria-selected="false">Movimientos de Inventario</a>
    </li>
    
  </ul>


  <div class="modal modal-danger fade" id="movementModal" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">

    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Cálculo del Costo de Inventario. Vuelva a elegir el archivo para confirmar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                    <form action="{{ route('import_product_procesar') }}" method="post"  enctype="multipart/form-data" >
                        @csrf
                        <input id="amount" type="hidden" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ number_format($total_amount_for_import ?? 0, 2, '.', '') }}" readonly required autocomplete="amount">
                                    
                        <div class="form-group row">
                            <div class="offset-sm-1">
                                <input id="file_form" type="file" value="import" accept=".xlsx" name="file" class="file">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="contrapartida" class="col-sm-12 col-form-label text-md-center">El Total del costo a Cargar es: {{number_format($total_amount_for_import ?? 0, 2, ',', '.')}}</label>
                        </div>
                           <div class="form-group row">
                                <label for="rate" class="col-sm-2 col-form-label text-md-right">Tasa:</label>
                                <div class="col-sm-3">
                                    <input id="rate" type="text" class="form-control @error('rate') is-invalid @enderror" name="rate" value="{{  number_format(bcdiv($bcv ?? 0, '1', 2) , 2, ',', '.') }}" required autocomplete="rate">
                                    @error('rate')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <label for="rate" class="col-sm-2 col-form-label text-md-right">Moneda:</label>
                                <div class="col-sm-4">
                                    <select id="coin"  name="coin" class="form-control">
                                        <option selected value="dolares">Dolares</option>
                                        <option value="bolivares">Bolivares</option>
                                    </select>  
                                </div>
                            </div>
                            <div class="form-group row">
                                    @if (isset($contrapartidas))      
                                    <label for="contrapartida" class="col-sm-4 col-form-label text-md-right">Contrapartida/Cargo:</label>
                                
                                    <div class="col-sm-4">
                                    <select id="contrapartida"  name="contrapartida" class="form-control">
                                        <option value="">Seleccionar</option>
                                        @foreach($contrapartidas as $index => $value)
                                            <option value="{{ $index }}" {{ old('Contrapartida') == $index ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                        </select>

                                        @if ($errors->has('contrapartida_id'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('contrapartida_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    @endif
                                    <div class="col-sm-4">
                                        <select  id="subcontrapartida"  name="Subcontrapartida" class="form-control">
                                            <option value="">Seleccionar</option>
                                        </select>

                                        @if ($errors->has('subcontrapartida_id'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('subcontrapartida_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                            </div>  
            </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-info">Aceptar</button>
                        </div>
             </form>
        </div>
    </div>
</div>


<div class="modal modal-danger fade" id="movementModaltwo" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">

    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabelcombo">Crear Combo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="inventarycombo" name="inventarycombo" method="POST" action="{{ route('inventories.store_inventory_combo') }}" enctype="multipart/form-data" >
                @csrf
                <input id="type_add" type="hidden" name="type_add" value="1">
                <input id="id_product" type="hidden" name="id_product">
                <input id="cant_disponible" type="hidden" name="cant_disponible">
                <input id="cant_actual" type="hidden" name="cant_actual">
                <input id="name_combo" type="hidden" name="name_combo">
                <input id="serie" type="hidden" name="serie">
                                
                    <div class="modal-body">
                        <h6 class="modal-title" id="exampleModalLabelmed2"></h6>
                        <br>
                        <h6 class="modal-title" id="exampleModalLabelmed"></h6>
                    <br>
                    <div class="form-group row">
                        <div class="col-sm-6">
                        <span id="type_add_text"></span>
                        </div> 
                        <div class="col-sm-4">
                    <input id="disponible" style="text-align: center" type="number" class="form-control @error('disponible') is-invalid @enderror" name="disponible" value="0" required>
                    </div> 
                    </div> 
                    <br>
                    <button type="submit" class="btn btn-info"> <span id="type_add_button"></span></button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="container-fluid">
    <!-- Page Heading -->

    <div class="row py-lg-2">
        <div class="col-sm-2">
            <button class="btn btn-dark" type="button"
            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="false"
            aria-expanded="false">
            <i class="fas fa-bars"></i>
            Opciones 
        </button>
        <div class="dropdown-menu animated--fade-in" aria-labelledby="dropdownMenuButton">
            <h6>Importación Masiva de Inventario</h6>
            <a href="{{ route('export.product_template_inventary') }}" class="dropdown-item bg-success text-white h5">Descargar Plantilla Productos Excel</a> 
                <form id="fileForm" method="POST" action="{{ route('import_inventary') }}" enctype="multipart/form-data" >
                @csrf
                <input id="file" type="file" value="import" accept=".xlsx" name="file" class="file">
                
                <br>
                <br>
                <button type="submit" class="dropdown-item bg-warning text-white h5">Subir Plantilla Productos Excel</button>
                </form>
            <!-- <a href="#" onclick="import_product();" class="dropdown-item bg-warning text-white h5"></a> 
           <a href="#" onclick="import_product_update_price();" class="dropdown-item bg-info text-white h5">Actualizar Precio Productos</a> -->
        </div> 
        </div>
        <div class="col-sm-3">
            <button class="btn btn-info dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
                Imprimir
            </button>
            <div class="dropdown-menu animated--fade-in" aria-labelledby="dropdownMenuButton">
                
                <a class="dropdown-item" onclick="pdfinventory();" style="color: rgb(4, 119, 252)"> <i class="fas fa-download fa-sm fa-fw mr-2 text-blue-400"></i><strong>Imprimir Inventario Actual</strong></a>
            </div> 
        </div>
        <div class="col-sm-3 offset-sm-2  dropdown mb-4">
            <a href="{{ route('products.create')}}" class="btn btn-primary  float-md-center"  role="button" aria-pressed="true">Registrar un Producto</a>
        </div> 
        <div class="col-sm-2">
            <select class="form-control" name="type" id="type">
                @if(isset($type))
                    @if ($type == 'MATERIAP')
                        <option disabled selected value="{{$type}}">MATERIA PRIMA</option>
                    @endif
                    @if ($type == 'todos')
                        <option disabled selected value="{{$type}}">TODOS</option>    
                    @endif
                    @if ($type == 'MERCANCIA' or $type == 'COMBO')
                    <option disabled selected value="{{$type}}">{{$type}}</option> 
                    @endif
                    <option value="todos">-------------</option>
                    <option value="todos">TODOS</option>
                    <option value="MERCANCIA">MERCANCIA</option>
                    <option value="MATERIAP">MATERIA PRIMA</option>
                    <option value="COMBO">COMBO</option>
                @else
                    <option value="todos">TODOS</option>
                    <option value="MERCANCIA">MERCANCIA</option>
                    <option value="MATERIAP">MATERIA PRIMA</option>
                    <option value="COMBO">COMBO</option>

                @endif
                

            </select>
        </div>
    </div>


       
 </div>
  <!-- /.container-fluid -->
  {{-- VALIDACIONES-RESPUESTA--}}
  @include('admin.layouts.success')   {{-- SAVE --}}
  @include('admin.layouts.danger')    {{-- EDITAR --}}
  @include('admin.layouts.delete')    {{-- DELELTE --}}
  {{-- VALIDACIONES-RESPUESTA --}}
<!-- DataTales Example -->
<div class="card shadow mb-4">
    
    <div class="card-body">
        <div class="container">
            @if (session('flash'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{session('flash')}}
                <button type="button" class="close" data-dismiss="alert" aria-label="close">
                    <span aria-hidden="true">&times; </span>
                </button>
            </div>   
        @endif
        </div>
        <div class="table-responsive">
        <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
                <th class="text-center">ID</th>
                <th class="text-center">Código Comercial</th>
                <th class="text-center">Descripción</th>
                <th class="text-center">Tipo</th>
                <th class="text-center">Cantidad</th>
                <th class="text-center">Costo</th>
                
                <th class="text-center">Moneda</th>
              
                <th class="text-center" style="width: 1%">Foto del Producto.</th>
                
                <th class="text-center"></th>
            </tr>
            </thead>
            
            <tbody>
                @if (empty($inventories))
                @else  
                    @foreach ($inventories as $var)
                     <?php
                     if (isset($var->description)){
                     $descripcion = $var->description;
                     } else {
                        $descripcion = '';
                     }
                     ?>    
                    <tr>
                            <td class="text-center">{{ $var->id ?? '' }}</td>
                            <td class="text-center">{{ $var->code_comercial ?? '' }}</td>
                            <td class="text-center">{{ $var->description ?? '' }}</td>
                            <td class="text-center">{{ $var->type ?? '' }}</td>
                            <td class="text-right">{{number_format($var->amount ?? 0, 3, ',', '')}}</td> 
                            <td class="text-right">{{number_format($var->price_buy ?? 0, 3, ',', '.') }}</td>
                            
                            @if($var->money == "D")
                            <td class="text-center">USD</td>
                            @else
                            <td class="text-center">Bs</td>
                            @endif

                            <td class="text-center">
        
                                @if(isset($var->photo_product))
                                <!--arya/storage/app/public/img/-->
                                <img style="width:60px; max-width:60px; height:80px; max-height:80px" src="{{asset('arya/storage/app/public/img/'.$company->login.'/productos/'.$var->photo_product)}}">
                                <div class="file-footer-buttons">
                                <button type="button" class="btnimg btn-sm" title="Ver detalles" data-toggle="modal" data-target="#imagenModal" onclick="loadimg('{{asset('arya/storage/app/public/img/'.$company->login.'/productos/'.$var->photo_product)}}')"><i class="fas fa-search-plus"></i></button>     </div>  
                                @endif

                            </td> 
                            
                            <td class="text-center">
                                @if($var->type == 'COMBO')
                                <span class="inv_combo" data-desc="{{$descripcion}}" data-id_combo="{{$var->id_inventory}}" data-cantidad_combos="{{$var->combos_disponibles}}" data-serie="{{$var->code_comercial}}" data-cantidad_actual="{{number_format($var->amount ?? 0, 3, ',', '')}}"><i class="fa fa-plus invent_combo" style="color: blue; cursor: pointer;" title="Crear Combo"></i></span> 
                                <span class="inv_combo_des" data-desc="{{$descripcion}}" data-id_combo="{{$var->id_inventory}}" data-cantidad_combos="{{$var->combos_disponibles}}" data-serie="{{$var->code_comercial}}" data-cantidad_actual="{{number_format($var->amount ?? 0, 3, ',', '')}}"><i class="fa fa-minus" style="color: rgb(248, 62, 62); cursor: pointer;" title="Deshacer Combo"></i></span> 
                                <a href="{{ route('combos.create_assign',$var->id_inventory) }}"  title="Ver Productos del Combo"><i class="fa fa-list"></i></a>
                                @else
                                <a href="{{ route('inventories.create_increase_inventory',$var->id_inventory) }}" style="color: blue;" title="Aumentar Inventario"><i class="fa fa-plus"></i></a>
                                <a href="{{ route('inventories.create_decrease_inventory',$var->id_inventory) }}" style="color: rgb(248, 62, 62);" title="Disminuir Inventario"><i class="fa fa-minus"></i></a>
                                @endif
                            </td>
                        </tr>     
                    @endforeach   
                @endif
            </tbody>
        </table>
        </div>
    </div>

            
        <!-- vista previa imagen Modal -->
        <div class="modal modal-danger fade" id="imagenModal" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Vista Previa</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <main>
                            <section>
                                <canvas id="canvas"></canvas>
                                <div class="full-img">
                                <img src="" alt="" id="myImage" class="myImage">      
                                </div>
                            </section>
                        </main>
                    </div>
            </div>
        </div>
</div>
  
@endsection

@section('javascript')
<script>
    if("{{isset($total_amount_for_import)}}"){
        $('#movementModal').modal('show');
    }
    

</script>

    <script type="text/javascript">
            function pdfinventory() {
                
                var nuevaVentanainventory = window.open("{{ route('pdf.inventory')}}","ventana","left=800,top=800,height=800,width=1000,scrollbar=si,location=no ,resizable=si,menubar=no");
        
            }

        $('#dataTable').DataTable({
            "ordering": true,
            "order": [],
            'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
        });

        $("#file").on('change',function(){
            
            var file = document.getElementById("file").value;

            /*Extrae la extencion del archivo*/
            var basename = file.split(/[\\/]/).pop(),  // extract file name from full path ...
                                               // (supports `\\` and `/` separators)
            pos = basename.lastIndexOf(".");       // get last position of `.`

            if (basename === "" || pos < 1) {
                alert("El archivo no tiene extension");
            }          
            /*-------------------------------*/     

            if(basename.slice(pos + 1) == 'xlsx'){
                
            }else{
                alert("Solo puede cargar archivos .xlsx");
            }            
               
        });


        
        $(document).on('click','.inv_combo',function(){
    
                var desc = $(this).attr("data-desc");
                var serie = $(this).attr("data-serie");
                var id_combo = $(this).attr("data-id_combo");
                var cantidad_combos = $(this).attr("data-cantidad_combos");
                var cantidad_actual = $(this).attr("data-cantidad_actual");

                $('#id_product').val(id_combo);
                $('#movementModaltwo').modal('show');
                $('#exampleModalLabelcombo').html('Crear Combo ID: '+id_combo+' '+desc);
                $('#exampleModalLabelmed').show();
                $('#exampleModalLabelmed').html('Combos Disponibles: '+cantidad_combos);
                $('#exampleModalLabelmed2').html('Inventario Actual: '+cantidad_actual);
                $('#disponible').val(cantidad_combos);
                $('#cant_disponible').val(cantidad_combos);
                $('#cant_actual').val(cantidad_actual);
                $('#serie').val(serie);
                $('#name_combo').val(desc);
                $('#type_add').val('1');
                $('#type_add_text').html('Ingrese la cantidad a crear:');
                $('#type_add_button').html('Agregar al Inventario del Combo');
                

        });

        $(document).on('click','.inv_combo_des',function(){
    
                var desc = $(this).attr("data-desc");
                var serie = $(this).attr("data-serie");
                var id_combo = $(this).attr("data-id_combo");
                var cantidad_combos = $(this).attr("data-cantidad_combos");
                var cantidad_actual = $(this).attr("data-cantidad_actual");
                
                $('#id_product').val(id_combo);
                $('#movementModaltwo').modal('show');
                $('#exampleModalLabelcombo').html('Deshacer Combo ID: '+id_combo+' '+serie+' '+desc);
                $('#exampleModalLabelmed').hide();
                $('#exampleModalLabelmed2').html('Inventario Actual: '+cantidad_actual);
                $('#disponible').val(0);
                $('#type_add').val('0');
                $('#cant_disponible').val(cantidad_combos);
                $('#cant_actual').val(cantidad_actual);
                $('#serie').val(serie);
                $('#name_combo').val(desc);
                $('#type_add_text').html('Ingrese la cantidad a devolver:');
                $('#type_add_button').html('Disminuir Inventario del Combo ');

        });


        function loadimg (url){
        
                const domString = url
                //console.log(domString)
                var ctx = canvas.getContext('2d')
                var img = new Image()
                img.src = domString
                img.onload = function(){
                document.getElementById('myImage').setAttribute('src',domString)
                }
        }

        $("#file_form").on('change',function(){
            
            var file = document.getElementById("file_form").value;

            /*Extrae la extencion del archivo*/
            var basename = file.split(/[\\/]/).pop(),  // extract file name from full path ...
                                               // (supports `\\` and `/` separators)
            pos = basename.lastIndexOf(".");       // get last position of `.`

            if (basename === "" || pos < 1) {
                alert("El archivo no tiene extension");
            }          
            /*-------------------------------*/     

            if(basename.slice(pos + 1) == 'xlsx'){
              
            }else{
                alert("Solo puede cargar archivos .xlsx");
            }            
               
        });


        $("#contrapartida").on('change',function(){
            var contrapartida_id = $(this).val();
            $("#subcontrapartida").val("");
            
            getSubcontrapartida(contrapartida_id);
        });

        function getSubcontrapartida(contrapartida_id){
            
            $.ajax({
                url:"{{ route('directpaymentorders.listcontrapartida') }}" + '/' + contrapartida_id,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                    let subcontrapartida = $("#subcontrapartida");
                    let htmlOptions = `<option value='' >Seleccione..</option>`;
                    // console.clear();
                    if(response.length > 0){
                        response.forEach((item, index, object)=>{
                            let {id,description} = item;
                            htmlOptions += `<option value='${id}' {{ old('Subcontrapartida') == '${id}' ? 'selected' : '' }}>${description}</option>`

                        });
                    }
                    //console.clear();
                    // console.log(htmlOptions);
                    subcontrapartida.html('');
                    subcontrapartida.html(htmlOptions);
                
                    
                
                },
                error:(xhr)=>{
                    alert('Presentamos inconvenientes al consultar los datos');
                }
            })
        }

        $("#subcontrapartida").on('change',function(){
                var subcontrapartida_id = $(this).val();
                var contrapartida_id    = document.getElementById("contrapartida").value;
                
            });
            
        $("#type").on('change',function(){
            type = $(this).val();
            window.location = "{{route('inventories', [''])}}"+"/"+type;
        });

        </script> 
@endsection
