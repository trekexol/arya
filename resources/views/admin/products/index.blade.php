@extends('admin.layouts.dashboard')

@section('content')

<ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
      <a class="nav-link active font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('products') }}" role="tab" aria-controls="home" aria-selected="true">Productos</a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('inventories') }}" role="tab" aria-controls="profile" aria-selected="false">Inventarios</a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('combos') }}" role="tab" aria-controls="home" aria-selected="true">Combos</a>
  </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('inventories.movement') }}" role="tab" aria-controls="contact" aria-selected="false">Movimientos de Inventario</a>
    </li>
    
  </ul>

<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
      <div class="col-sm-3 offset-sm-4  dropdown mb-4">
          <button class="btn btn-dark" type="button"
              id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="false"
              aria-expanded="false">
              <i class="fas fa-bars"></i>
              Opciones 
          </button>
          <div class="dropdown-menu animated--fade-in" aria-labelledby="dropdownMenuButton">
              <h6>Importación Masiva Productos e Inventario</h6>
              <a href="{{ route('export.product_template') }}" class="dropdown-item bg-success text-white h5">Descargar Plantilla Excel</a> 
              <form id="fileForm" method="POST" action="{{ route('import_product') }}" enctype="multipart/form-data" >
                @csrf
                <input id="file" type="file" value="import" accept=".xlsx" name="file" class="file">
              </form>
              <br>
              <a href="#" onclick="import_product();" class="dropdown-item bg-warning text-white h5">Subir Plantilla Excel</a> 
             <!-- <a href="#" onclick="import_product_update_price();" class="dropdown-item bg-info text-white h5">Actualizar Precio Productos</a> -->
          </div> 
      </div> 

      
      <div class="col-sm-3">
        <a href="{{ route('products.create')}}" class="btn btn-primary float-md-right" role="button" aria-pressed="true">Registrar un Producto </a>
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
            <tr> 
                <th class="text-center negro">ID</th>
                <th class="text-center">Código Comercial</th>
                <th class="text-center">Descripción</th>
                <th class="text-center">Tipo</th>
                <th class="text-center">Precio</th>
                <th class="text-center">Moneda</th>
                <th class="text-center">Foto</th>
                <th class="text-center" width="1%">(S)</th>
              
                <th class="text-center" width="9%"></th>
            </tr>
            </thead>
            
            <tbody>
                @if (empty($products))
                @else  
                    @foreach ($products as $product)
                        <tr>
                            <td class="text-center">{{$product->id}}</td>
                            <td class="text-center">{{$product->code_comercial}}</td>
                            <td class="text-center">{{$product->description}}</td>
                            <td class="text-center">{{$product->type}}</td>
                            <td class="text-right">{{number_format($product->price, 2, ',', '.')}}</td>
                            
                            @if ($product->money == 'Bs')
                              <td class="text-center">Bolivares</td>
                            @else
                              <td class="text-center">Dolares</td>
                            @endif
                           
                           <!--  
                            <source srcset="{{ ''/*asset('storage/img/'.$company->login.'/productos/'.$product->photo_product) */}}" media="( max-width: 500px )">
                            <source srcset="{{ ''/*asset('storage/img/'.$company->login.'/productos/'.$product->photo_product) */}}" media="( max-width: 800px )">
                            <source srcset="{{ ''/*asset('storage/img/'.$company->login.'/productos/'.$product->photo_product) */}}" media="( max-width: 1000px )"> -->
            
                            <td class="text-center">
                                <!--<img style="width:60px; max-width:60px; height:80px; max-height:80px" class="img-responsive" src="{{ '' /*asset('storage/img/'.$company->login.'/productos/'.$product->photo_product) */}}" alt="" onclick="loadimg('{{''/*asset('storage/img/'.$company->login.'/productos/'.$product->photo_product) */}}')"> -->
                                @if(isset($product->photo_product))
                                <input class="fotop" style="width:60px; max-width:60px; height:80px; max-height:80px"  type="file" data-initial-preview="{{asset('storage/img/'.$company->login.'/productos/'.$product->photo_product)}}" accept="image/*">
                                @endif
                            </td>
                           
            
                            @if ($product->status == '0')
                            <td class="text-center" style="font-weight: bold; color: red">I</td>
                            @else
                            <td class="text-center" style="font-weight: bold; color: green">A</td>
                            @endif
                            <td class="text-center" width="9%">
                                <a href="{{ route('products.edit',$product->id) }}"  title="Editar"><i class="fa fa-edit"></i></a>
                                <a href="{{ route('products.productprices',$product->id) }}"  title="Listado de Precios"><i class="fa fa-list"></i></a>
                                <a href="#" class="delete" data-id-product={{$product->id}} data-toggle="modal" data-target="#deleteModal" title="Eliminar"><i class="fa fa-trash text-danger"></i></a>  
                            </td>
                        </tr>     
                    @endforeach   
                @endif
            </tbody>
        </table>
        </div>
    </div>
</div>

<!-- Delete Warning Modal -->
<div class="modal modal-danger fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Eliminar</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body">
          <form action="{{ route('products.delete') }}" method="post">
              @csrf
              @method('DELETE')
              <input id="id_product_modal" type="hidden" class="form-control @error('id_product_modal') is-invalid @enderror" name="id_product_modal" readonly required autocomplete="id_product_modal">
                     
              <h5 class="text-center">Seguro que desea eliminar?</h5>
              
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-danger">Eliminar</button>
          </div>
          </form>
      </div>
  </div>
</div>
<div class="modal modal-danger fade" id="movementModal" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Movimiento Contable</h5>
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
                    <label for="contrapartida" class="col-sm-12 col-form-label text-md-center">La carga de estos productos es de: {{number_format($total_amount_for_import ?? 0, 2, ',', '.')}}</label>
                </div>
                <div class="form-group row">
                    <label for="rate" class="col-sm-2 col-form-label text-md-right">Tasa:</label>
                    <div class="col-sm-6">
                        <input id="rate" type="text" class="form-control @error('rate') is-invalid @enderror" name="rate" value="{{  number_format(bcdiv($bcv ?? 0, '1', 2) , 2, ',', '.') }}" required autocomplete="rate">
                        @error('rate')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    @if (isset($contrapartidas))      
                    <label for="contrapartida" class="col-sm-4 col-form-label text-md-right">Contrapartida:</label>
                
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
  
@endsection

@section('javascript')

<!-- bootstrap 5.x or 4.x is supported. You can also use the bootstrap css 3.3.x versions -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" crossorigin="anonymous">

<!-- default icons used in the plugin are from Bootstrap 5.x icon library (which can be enabled by loading CSS below) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.min.css" crossorigin="anonymous">

<!-- alternatively you can use the font awesome icon library if using with `fas` theme (or Bootstrap 4.x) by uncommenting below. -->
<!-- link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" crossorigin="anonymous" -->

<!-- the fileinput plugin styling CSS file -->
<link href="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />

<!-- if using RTL (Right-To-Left) orientation, load the RTL CSS file after fileinput.css by uncommenting below -->
<!-- link href="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/css/fileinput-rtl.min.css" media="all" rel="stylesheet" type="text/css" /-->

<!-- the jQuery Library -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>

<!-- buffer.min.js and filetype.min.js are necessary in the order listed for advanced mime type parsing and more correct
     preview. This is a feature available since v5.5.0 and is needed if you want to ensure file mime type is parsed 
     correctly even if the local file's extension is named incorrectly. This will ensure more correct preview of the
     selected file (note: this will involve a small processing overhead in scanning of file contents locally). If you 
     do not load these scripts then the mime type parsing will largely be derived using the extension in the filename
     and some basic file content parsing signatures. -->
<script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/plugins/buffer.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/plugins/filetype.min.js" type="text/javascript"></script>

<!-- piexif.min.js is needed for auto orienting image files OR when restoring exif data in resized images and when you
    wish to resize images before upload. This must be loaded before fileinput.min.js -->
<script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/plugins/piexif.min.js" type="text/javascript"></script>

<!-- sortable.min.js is only needed if you wish to sort / rearrange files in initial preview. 
    This must be loaded before fileinput.min.js -->
<script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/plugins/sortable.min.js" type="text/javascript"></script>

<!-- bootstrap.bundle.min.js below is needed if you wish to zoom and preview file content in a detail modal
    dialog. bootstrap 5.x or 4.x is supported. You can also use the bootstrap js 3.3.x versions. -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

<!-- the main fileinput plugin script JS file -->
<script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/fileinput.min.js"></script>

<!-- following theme script is needed to use the Font Awesome 5.x theme (`fas`). Uncomment if needed. -->
<!-- script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/themes/fas/theme.min.js"></script -->

<!-- optionally if you need translation for your language then include the locale file as mentioned below (replace LANG.js with your language locale) -->
<script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/locales/LANG.js"></script>


<script src="{{asset("vendor/bootstrap-fileinput/js/fileinputcopy.min.js")}}" type="text/javascript"></script>
<script src="{{asset("vendor/bootstrap-fileinput/js/locales/es.js")}}" type="text/javascript"></script>
<script src="{{asset("vendor/bootstrap-fileinput/themes/fas/theme.min.js")}}" type="text/javascript"></script>
<link href="{{asset("vendor/bootstrap-fileinput/css/fileinput-copia.min.css")}}" rel="stylesheet" type="text/css"/>

    <script>
        if("{{isset($total_amount_for_import)}}"){
            $('#movementModal').modal('show');
        }
        
    </script>
     <script>
        $('#dataTable').DataTable({
            "ordering": true,
            "order": [],
            'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
        });

        $(document).ready(function () {
            $("#rate").mask('000.000.000.000.000,00', { reverse: true });
            
        });

        $(document).on('click','.delete',function(){
         
            let id_product = $(this).attr('data-id-product');
    
            $('#id_product_modal').val(id_product);
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

        function import_product(){
            document.getElementById("fileForm").submit();
        }

        function import_product_update_price(){
            document.getElementById("fileForm").action = "{{ route('import_product_update_price') }}";
            document.getElementById("fileForm").submit();
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

            $(".fotop").fileinput({
                language: 'es',
                allowedFileExtensions: ['jpg','jpeg','png'],
                maxFileSize: 1000,
                showUpload: false,
                showClose: false,
                initialPreviewAsData: true,
                dropZoneEnabled: false,
                //showZoom: false,
                theme: "fas"   
               
            });


        // Create a timestamp
        var timestamp = new Date().getTime();
  
        // Get the image element 
        var image = document.getElementById("gfgimage");
  
        // Adding the timestamp parameter to image src
        image.src = "bg.png?t=" + timestamp;
        console.log(image.src);

        </script> 
@endsection
