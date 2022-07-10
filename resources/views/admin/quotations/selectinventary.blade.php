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

<div class="container-fluid">
    <div class="row py-lg-2">
       
        <div class="col-sm-10">
            <h2>Seleccione un Producto del Inventario</h2>
        </div>
        <div class="col-sm-2">
            <select class="form-control" name="type" id="type">
                @if(isset($type))
                    @if ($type == 'productos')
                        <option disabled selected value="{{ $type }}">{{ $type }}</option>
                        <option disabled  value="{{ $type }}">-----------</option>
                    @else
                        <option disabled selected value="servicios">servicios</option>
                        <option disabled  value="servicios">-----------</option>
                    @endif
                    
                @else
                    <option disabled selected value="productos">productos</option>
                @endif
                
                <option  value="productos">productos</option>
                <option value="servicios">servicios</option>
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
            <tr> 
                <th class="text-center"></th>
                <th class="text-center">ID</th>
                <th class="text-center">Código Comercial</th>
                <th class="text-center">Descripción</th>
                <th class="text-center">Cantidad</th>
                <th class="text-center">Precio Bs</th>
                <th class="text-center">Precio Moneda</th>
                <th class="text-center">Moneda</th>
                <th class="text-center">Foto del Producto</th>
                
              
                
                
            </tr>
            </thead>
            
            <tbody>
                @if (empty($inventories))
                @else  
                    @foreach ($inventories as $var)
                        <tr>
                            <td>
                                <a href="{{ route('quotations.createproduct',[$id_quotation,$coin,$var->id_inventory,$type_quotation ?? null]) }}" title="Seleccionar"><i class="fa fa-check"></i></a>
                            </td>
                            <td>{{ $var->id }}</td>
                            <td>{{ $var->code_comercial }}</td>
                            <td>{{ $var->description}}</td>
                            <td>{{ $var->amount ?? 0}}</td>
                           
                            @if($var->money != 'Bs')
                                <td style="text-align: right">{{number_format($var->price * $bcv_quotation_product, 2, ',', '.')}}</td>
                                <td style="text-align: right">{{number_format($var->price, 2, ',', '.')}}</td> 
                            @else
                                <td style="text-align: right">{{number_format($var->price, 2, ',', '.')}}</td> 
                                <td style="text-align: right"></td> 
                            @endif
                            
                           
                            @if($var->money == "D")
                                <td>Dolar</td>
                            @else
                                <td>Bolívar</td>
                            @endif

                            <td>
                                @if(isset($var->photo_product))
                                <!--arya/storage/app/public/img/-->
                                <img style="width:60px; max-width:60px; height:80px; max-height:80px" src="{{asset('arya/storage/app/public/img/'.$company->login.'/productos/'.$var->photo_product)}}">
                                <div class="file-footer-buttons">
                                <button type="button" class="btnimg btn-sm" title="Ver detalles" data-toggle="modal" data-target="#imagenModal" onclick="loadimg('{{asset('arya/storage/app/public/img/'.$company->login.'/productos/'.$var->photo_product)}}')"><i class="fas fa-search-plus"></i></button>     </div>  
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
        $('#dataTable').DataTable({
            "ordering": true,
            "order": [],
            'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "Todo"]]
        });

        $("#type").on('change',function(){
            type = $(this).val();
            window.location = "{{route('quotations.selectproduct', [$id_quotation,$coin,''])}}"+"/"+type;
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
        
    </script> 
@endsection
