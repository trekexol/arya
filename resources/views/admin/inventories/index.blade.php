@extends('admin.layouts.dashboard')

@section('header')

<style> 
    .btn-file input[type=file],.file-caption-icon,.file-no-browse,.file-preview .fileinput-remove,.file-zoom-dialog .btn-navigate,.file-zoom-dialog .floating-buttons,.krajee-default .file-thumb-progress{position:absolute}
     .file-loading input[type=file],input[type=file].file-loading{width:0;height:0}
     .file-no-browse{left:50%;bottom:20%;width:1px;height:1px;font-size:0;opacity:0;border:none;background:0 0;outline:0;box-shadow:none}
     .file-caption-icon,.file-input-ajax-new .fileinput-remove-button,.file-input-ajax-new .fileinput-upload-button,.file-input-ajax-new .no-browse .input-group-btn,.file-input-new .close,.file-input-new .file-preview,.file-input-new .fileinput-remove-button,.file-input-new .fileinput-upload-button,.file-input-new .glyphicon-file,.file-input-new .no-browse .input-group-btn,.file-zoom-dialog .modal-header:after,.file-zoom-dialog .modal-header:before,.hide-content .kv-file-content,.is-locked .fileinput-remove-button,.is-locked .fileinput-upload-button,.kv-hidden{display:none}
     .file-caption .input-group{align-items:center;display: none;}
     .file-caption-icon .kv-caption-icon{line-height:inherit}
     .btn-file,.file-caption,.file-input,.file-loading:before,.file-preview,.file-zoom-dialog .modal-dialog,.krajee-default .file-thumbnail-footer,.krajee-default.file-preview-frame{position:relative}
     .file-error-message pre,.file-error-message ul,.krajee-default .file-actions,.krajee-default .file-other-error{text-align:left}
     .file-error-message pre,.file-error-message ul{margin:0}
      .fa-arrows-alt{display: none;}
     .file-thumb-progress .progress,.file-thumb-progress .progress-bar{font-family:Verdana,Helvetica,sans-serif;font-size:.7rem}
     .krajee-default .file-caption-info,.krajee-default .file-size-info{display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;width:0px;height:0px;margin:auto}
     .file-zoom-content>.file-object.type-flash,.file-zoom-content>.file-object.type-image,.file-zoom-content>.file-object.type-video{max-width:100%;max-height:100%;width:auto}
     .file-zoom-content>.file-object.type-flash,.file-zoom-content>.file-object.type-video{height:100%}
     .file-zoom-content>.file-object.type-default,.file-zoom-content>.file-object.type-html,.file-zoom-content>.file-object.type-pdf,.file-zoom-content>.file-object.type-text{width:100%}
     .file-loading:before{content:" Loading...";display:none;padding-left:20px;line-height:16px;font-size:13px;font-variant:small-caps;color:#999;background:url(../img/loading.gif) top left no-repeat}
     .file-object{margin:0 0 -5px;padding:0}
     .btn-file{overflow:hidden}
     .btn-file input[type=file]{top:0;left:0;min-width:100%;min-height:100%;text-align:right;opacity:0;background:none;cursor:inherit;display:none}
     .btn-file ::-ms-browse{font-size:10000px;width:100%;height:100%}
     .file-caption.icon-visible .file-caption-icon{display:none}
     .file-caption.icon-visible .file-caption-name{padding-left:25px}
     .file-caption.icon-visible>.input-group-lg .file-caption-name{padding-left:30px}
     .file-caption.icon-visible>.input-group-sm .file-caption-name{padding-left:22px}
     .file-caption-name:not(.file-caption-disabled){background-color:transparent}
     .file-caption-name.file-processing{font-style:italic;border-color:#bbb;opacity:.5}
     .file-caption-icon{padding:7px 5px;left:4px; display:none}
     .input-group-lg .file-caption-icon{font-size:1.25rem}
     .input-group-sm .file-caption-icon{font-size:.875rem;padding:.25rem}
     .file-error-message{color:#a94442;margin:5px;border:1px solid #ebccd1;border-radius:4px;padding:15px}
     .file-error-message pre{margin:5px 0}
     .file-caption-disabled{cursor:not-allowed;opacity:1}
     .file-preview{border-radius:5px;padding:0px;width:100%;margin-bottom:0px}
     .file-preview .btn-xs{padding:1px 5px;font-size:12px;line-height:1.5;border-radius:3px}
     .file-preview .fileinput-remove{top:1px;right:1px;line-height:10px}
     .file-preview .clickable{cursor:pointer}
     .file-preview-image{font:40px Impact,Charcoal,sans-serif;color:green;width:60;height:80;max-width:60;max-height:80;}
     .krajee-default.file-preview-frame{margin:0px;border:0px solid rgba(0,0,0,.2);box-shadow:0 0 10px 0 rgba(0,0,0,.2);padding:0px;float:left;text-align:center}
     .krajee-default.file-preview-frame .kv-file-content{width:60;height:80;max-width:60;max-height:80;}
     .krajee-default.file-preview-frame .kv-file-content.kv-pdf-rendered{width:400px}
     .krajee-default.file-preview-frame[data-template=audio] .kv-file-content{width:240px;height:55px}
     .file-thumbnail-footer{background-color: none; margin: 0px; padding: 0px;}
     .file-footer-buttons{background-color: none;}
     .file-actions{background-color: none;}
     .krajee-default.file-preview-frame:not(.file-preview-error):hover{border:1px solid rgba(0,0,0,.3);box-shadow:0 0 10px 0 rgba(0,0,0,.4)}
     .krajee-default .file-preview-text{color:#428bca;border:1px solid #ddd;outline:0;resize:none}
     .krajee-default .file-preview-html{border:1px solid #ddd}
     .krajee-default .file-other-icon{font-size:6em;line-height:1}
     .krajee-default .file-footer-buttons{float:right}
     .file-footer-buttons{margin-top: -31px;}
     .file-upload-stats{font-size:10px;text-align:center;width:100%}
     .kv-upload-progress .file-upload-stats{font-size:12px;margin:-10px 0 5px}
     .krajee-default .file-preview-error{opacity:.65;box-shadow:none}
     .krajee-default .file-thumb-progress{top:37px;left:0;right:0}
     .krajee-default.kvsortable-ghost{background:#e1edf7;border:2px solid #a1abff}
     .krajee-default .file-preview-other:hover{opacity:.8}
     .krajee-default .file-preview-frame:not(.file-preview-error) .file-footer-caption:hover{color:#000}
     .kv-upload-progress .progress{height:20px;margin:10px 0;overflow:hidden}
     .kv-upload-progress .progress-bar{height:20px;font-family:Verdana,Helvetica,sans-serif}
     .file-zoom-dialog .file-other-icon{font-size:22em;font-size:50vmin}
     .file-zoom-dialog .modal-dialog{width:auto}
     .file-zoom-dialog .modal-header{display:flex;align-items:center;justify-content:space-between}
     .file-zoom-dialog .btn-navigate{margin:0 .1rem;padding:0;font-size:1.2rem;width:2.4rem;height:2.4rem;top:50%;border-radius:50%;text-align:center}
     .btn-navigate *{width:auto}
     .file-zoom-dialog .floating-buttons{top:5px;right:10px}
     .file-zoom-dialog .btn-kv-prev{left:0}
     .file-zoom-dialog .btn-kv-next{right:0}
     .file-zoom-dialog .kv-zoom-header{padding:.5rem}
     .file-zoom-dialog .kv-zoom-body{padding:.25rem}
     .file-zoom-dialog .kv-zoom-description{position:absolute;opacity:.8;font-size:.8rem;background-color:#1a1a1a;padding:1rem;text-align:center;border-radius:.5rem;color:#fff;left:15%;right:15%;bottom:15%}
     .file-zoom-dialog .kv-desc-hide{float:right;padding:0 .1rem;background:0 0;border:none}
     .file-zoom-dialog .kv-desc-hide:hover{opacity:.7}
     .file-zoom-dialog .kv-desc-hide:focus{opacity:.9}
     .file-input-ajax-new .no-browse .form-control,.file-input-new .no-browse .form-control{border-top-right-radius:4px;border-bottom-right-radius:4px}
     .file-caption{width:100%;position:relative}
     .file-thumb-loading{background:url(../img/loading.gif) center center no-repeat content-box!important}
     .file-drop-zone{border:1px dashed #aaa;min-height:260px;border-radius:4px;text-align:center;vertical-align:middle;margin:12px 15px 12px 12px;padding:5px}
     .file-drop-zone.clickable:hover{border:2px dashed #999}
     .file-drop-zone.clickable:focus{border:2px solid #5acde2}
     .file-drop-zone .file-preview-thumbnails{cursor:default}
     .file-drop-zone-title{color:#aaa;font-size:1.6em;text-align:center;padding:85px 10px;cursor:default}
     .file-highlighted{border:2px dashed #999!important;}
     .file-uploading{background:url(../img/loading-sm.gif) center bottom 10px no-repeat;opacity:.65}
     .file-zoom-fullscreen .modal-dialog{min-width:100%;margin:0}
     .file-zoom-fullscreen .modal-content{border-radius:0;box-shadow:none;min-height:100vh}
     .file-zoom-fullscreen .kv-zoom-body{overflow-y:auto}
     .floating-buttons{z-index:3000}
     .floating-buttons .btn-kv{margin-left:3px;z-index:3000}
     .kv-zoom-actions{min-width:140px}
     .kv-zoom-actions .btn-kv{margin-left:3px}
     .file-zoom-content{text-align:center;white-space:nowrap;min-height:300px}
     .file-zoom-content:hover{background:0 0}
     .file-zoom-content .file-preview-image,.file-zoom-content .file-preview-video{max-height:100%}
     .file-zoom-content>.file-object.type-image{height:auto;min-height:inherit}
     .file-zoom-content>.file-object.type-audio{width:auto;height:30px}@media (min-width:576px){.file-zoom-dialog .modal-dialog{max-width:500px}}@media (min-width:992px){.file-zoom-dialog .modal-lg{max-width:800px}}@media (max-width:767px){.file-preview-thumbnails{display:flex;justify-content:center;align-items:center;flex-direction:column}
     .file-zoom-dialog .modal-header{flex-direction:column}}@media (max-width:350px){.krajee-default.file-preview-frame:not([data-template=audio]) .kv-file-content{width:160px}}@media (max-width:420px){.krajee-default.file-preview-frame .kv-file-content.kv-pdf-rendered{width:100%}}
     .file-loading[dir=rtl]:before{background:url(../img/loading.gif) top right no-repeat;padding-left:0;padding-right:20px}
     .clickable .file-drop-zone-title{cursor:pointer}
     .file-sortable .file-drag-handle:hover{display:none; opacity:.7}
     .file-sortable .file-drag-handle{display:none; cursor:grab;opacity:1}
     .file-grabbing,.file-grabbing *{cursor:not-allowed!important}
     .file-grabbing .file-preview-thumbnails *{cursor:grabbing!important}
     .file-preview-frame.sortable-chosen{border-color:#17a2b8;box-shadow:none!important}
     .file-preview .kv-zoom-cache{display:none}
     .file-preview-object,.file-preview-other-frame,.kv-zoom-body{display:flex;align-items:center;justify-content:center}
     .kv-file-remove i {display: none; position: fixed;}
     .fa-trash-alt i {display: none; position: fixed;}
    </style>
@endsection


@section('content')

<ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
      <a class="nav-link font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('products') }}" role="tab" aria-controls="home" aria-selected="true">Productos</a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link active font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('inventories') }}" role="tab" aria-controls="profile" aria-selected="false">Inventarios</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('combos') }}" role="tab" aria-controls="home" aria-selected="true">Combos</a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('inventories.movement') }}" role="tab" aria-controls="contact" aria-selected="false">Movimientos de Inventario</a>
    </li>
    
  </ul>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
       
        <div class="col-md-4">
            <a href="{{ route('products.create')}}" class="btn btn-info  float-md-center"  role="button" aria-pressed="true">Registrar un Producto Nuevo</a>
          </div>
       
        <div class="col-md-2 dropdown mb-4">
            <button class="btn btn-info dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                Imprimir
            </button>
            <div class="dropdown-menu animated--fade-in" aria-labelledby="dropdownMenuButton">
                
                <a class="dropdown-item" onclick="pdfinventory();" style="color: rgb(4, 119, 252)"> <i class="fas fa-download fa-sm fa-fw mr-2 text-blue-400"></i><strong>Imprimir Inventario Actual</strong></a>
            </div>
        </div>
     
    
        
        
       
         <!--
            <div class="col-md-6">
                <a href="{{ route('inventories.select')}}" class="btn btn-success  float-md-right " role="button" aria-pressed="true">Registrar un Inventario</a>
              </div>  -->
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
                <th class="text-center">Cantidad</th>
                <th class="text-center">Costo</th>
                
                <th class="text-center">Moneda</th>
              
                <th class="text-center">Foto del Producto.</th>
                
                <th class="text-center"></th>
            </tr>
            </thead>
            
            <tbody>
                @if (empty($inventories))
                @else  
                    @foreach ($inventories as $var)
                        <tr>
                            <td class="text-center">{{ $var->id ?? '' }}</td>
                            <td class="text-center">{{ $var->code_comercial ?? '' }}</td>
                            <td class="text-center">{{ $var->description ?? '' }}</td>
                            <td class="text-right">{{ number_format($var->amount ?? 0, 2, ',', '.')}}</td> 
                            <td class="text-right">{{number_format($var->price ?? 0, 2, ',', '.') }}</td>
                            
                            @if($var->money == "D")
                            <td class="text-center">Dolar</td>
                            @else
                            <td class="text-center">Bolívar</td>
                            @endif

                            <td class="text-center">
                                @if(isset($var->photo_product))
                                <input class="fotop" style="width:60px; max-width:60px; height:80px; max-height:80px"  type="file" data-initial-preview="{{asset('arya/storage/app/public/img/'.$company->login.'/productos/'.$var->photo_product)}}" accept="image/*">
                                @endif    
                            </td> 
                            
                            <td class="text-center">
                                <a href="{{ route('inventories.create_increase_inventory',$var->id_inventory) }}" style="color: blue;" title="Aumentar Inventario"><i class="fa fa-plus"></i></a>
                                <a href="{{ route('inventories.create_decrease_inventory',$var->id_inventory) }}" style="color: rgb(248, 62, 62);" title="Disminuir Inventario"><i class="fa fa-minus"></i></a>
                            </td>
                        </tr>     
                    @endforeach   
                @endif
            </tbody>
        </table>
        </div>
    </div>
</div>
  
@endsection

@section('javascript')

<script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.2.6/js/fileinput.min.js"></script>
<!-- following theme script is needed to use the Font Awesome 5.x theme (`fas`). Uncomment if needed. -->
<script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.2.6/themes/fas/theme.min.js"></script>
<!-- optionally if you need translation for your language then include the locale file as mentioned below (replace LANG.js with your language locale) -->
<script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.2.6/js/locales/LANG.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.5.0/js/locales/es.min.js" integrity="sha512-q2lXTQuccVsDwaOpJNHbGDL2c5DEK706u1MCjKuGAG4zz+q1Sja3l2RuymU3ySE6RfmTYZ/V4wY5Ol71sRvvWA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script type="text/javascript">
            function pdfinventory() {
                
                var nuevaVentanainventory = window.open("{{ route('pdf.inventory')}}","ventana","left=800,top=800,height=800,width=1000,scrollbar=si,location=no ,resizable=si,menubar=no");
        
            }
     
        $('#dataTable').DataTable({
            "ordering": true,
            "order": [],
            'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
        });

        $("#fotop").fileinput({
            language: 'es',
            allowedFileExtensions: ['jpg','jpeg','png'],
            maxFileSize: 1000,
            showUpload: false,
            showClose: false,
            initialPreviewAsData: true,
            dropZoneEnabled: false,
            theme: "fas"    
        });

        $(".fotop").fileinput({
          language: 'es',
          allowedFileExtensions: ['jpg','jpeg','png'],
          maxFileSize: 1000,
          showUpload: false,
          showClose: false,
          initialPreviewAsData: true,
          dropZoneEnabled: false,
          theme: "fas"    
      });   
    

        </script> 
@endsection
