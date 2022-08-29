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
                <a href="{{ ''/*route('inventories.select')*/}}" class="btn btn-success  float-md-right " role="button" aria-pressed="true">Registrar un Inventario</a>
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
                            <td class="text-right">{{number_format($var->price_buy ?? 0, 2, ',', '.') }}</td>
                            
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


    <script type="text/javascript">
            function pdfinventory() {
                
                var nuevaVentanainventory = window.open("{{ route('pdf.inventory')}}","ventana","left=800,top=800,height=800,width=1000,scrollbar=si,location=no ,resizable=si,menubar=no");
        
            }
     
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
        $('#dataTable').DataTable({
            "ordering": true,
            "order": [],
            'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
        });


        </script> 
@endsection
