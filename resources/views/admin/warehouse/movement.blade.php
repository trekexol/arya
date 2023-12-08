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
    @if (Auth::user()->role_id  == '1')


      <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" href="{{ route('products') }}" role="tab" aria-controls="home" aria-selected="true">Productos</a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;"  href="{{ route('inventories') }}" role="tab" aria-controls="profile" aria-selected="false">Inventario</a>
      </li>
      <li class="nav-item" role="presentation">
          <a class="nav-link font-weight-bold" style="color: black;" href="{{ route('combos') }}" role="tab" aria-controls="home" aria-selected="true">Combos</a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" href="{{ route('inventories.movement') }}" role="tab" aria-controls="contact" aria-selected="false">Movimientos de Inventario</a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" href="{{ route('warehouse') }}" role="tab" aria-controls="contact" aria-selected="false">Almacenes</a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link active font-weight-bold" style="color: black;" href="{{ route('warehouse.movement') }}" role="tab" aria-controls="contact" aria-selected="false">Transferencia de Almacén</a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" href="{{ route('warehouse.indexmovementwarehouse') }}" role="tab" aria-controls="contact" aria-selected="false">Movimiento de Almacén</a>
      </li>

    @else

    @foreach($sistemas as $sistemas)
    @if($namemodulomiddleware == $sistemas->name)
<li class="nav-item" role="presentation">
    <a class="nav-link active font-weight-bold" style="color: black;" id="home-tab"  href="{{ route($sistemas->ruta) }}" role="tab" aria-controls="home" aria-selected="false">{{$sistemas->name}}</a>
  </li>
  @else
  <li class="nav-item" role="presentation">
    <a class="nav-link font-weight-bold" style="color: black;" id="home-tab"  href="{{ route($sistemas->ruta) }}" role="tab" aria-controls="home" aria-selected="false">{{$sistemas->name}}</a>
  </li>
  @endif
  @if($sistemas->name == 'Inventario')
  <li class="nav-item" role="presentation">
      <a class="nav-link font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('inventories.movement') }}" role="tab" aria-controls="contact" aria-selected="false">Movimientos de Inventario</a>
    </li>
  @endif
@endforeach


  @endif
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
                        <input id="amountp" type="hidden" class="form-control @error('amountp') is-invalid @enderror" name="amountp" value="{{ number_format($total_amount_for_import_materiap ?? 0, 2, '.', '') }}" readonly required autocomplete="amountp">
                        <div class="form-group row">
                            <div class="offset-sm-1">
                                <input id="file_form" type="file" value="import" accept=".xlsx" name="file" class="file" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <table style="width: 100%">
                                <tr>
                                    <td style="width: 60%">
                                        <span class="col-sm-12 text-md-left">Costo Mercancia para la Venta:</span>
                                    </td>
                                    <td style="text-align:right; width:20%">
                                        ${{number_format($total_amount_for_import ?? 0, 2, '.', '')}}
                                    </td>
                                    <td style="text-align:right; width:20%">
                                        {{number_format($total_amount_for_import ?? 0, 2, '.', '') * number_format($bcv ?? 1, 2, '.', '')}} Bs
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 60%">
                                        <span class="col-sm-12 text-md-left">Costo Materia Prima:</span>
                                    </td>
                                    <td style="text-align:right; width:20%">
                                        ${{number_format($total_amount_for_import_materiap ?? 0, 2, '.', '')}}
                                    </td>
                                    <td style="text-align:right; width:20%">
                                        {{number_format($total_amount_for_import_materiap ?? 0, 2, '.', '') * number_format($bcv ?? 1, 2, '.', '')}} Bs
                                    </td>
                                </tr>
                            </table>
                        </div>
                           <div class="form-group row">
                                <label for="rate" class="col-sm-2 col-form-label text-md-right">Tasa:</label>
                                <div class="col-sm-3">
                                    <input id="rate" type="text" class="form-control @error('rate') is-invalid @enderror" name="rate" value="{{  number_format($bcv ?? 1, 2, '.', '') }}" required autocomplete="rate">
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

    

  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Actualizar Cantidad de Productos Masivamente</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="fileForm" method="POST" action="{{ route('import_inventary_cantidad') }}" enctype="multipart/form-data" >
                @csrf

                <div class="form-row">

                    <div class="form-group col-md-12">
                      <label for="inputState">Tipo</label>
                      <select id="tipo" name="tipo" class="form-control form-control-sm" >
                        <option value="">Seleccione..</option>
                        <option value="AI">Aumentar Inventario</option>
                        <option value="DI">Disminuir Inventario</option>
                      </select>
                    </div>
                    <div class="form-group col-md-12 contradiv">
                        <label for="inputState">Contrapartida</label>
                        <select id="contrapartida2" class="form-control form-control-sm">
                          <option selected>Seleccione..</option>
                        @foreach($contrapartidas as $index => $value)
                          <option value="{{ $index }}" {{ old('contrapartida2') == $index ? 'selected' : '' }}>
                              {{ $value }}
                          </option>
                        @endforeach
                        </select>
                      </div>
                      <div class="form-group col-md-12 contradiv">
                        <select id="subcontrapartida2" name="subcontrapartida2" class="form-control form-control-sm">
                          <option value="">Seleccione..</option>
                        </select>
                      </div>

                      <div class="form-group col-md-12 procediv">
                        <label for="exampleFormControlFile1">Seleccionar Archivo</label>
                        <input id="file" type="file" value="import" accept=".xlsx" name="file" class="file">
                    </div>

                      <div class="col-md-12 text-center procediv">

                    <button type="submit" class="btn btn-sm btn-primary procediv">Proceder</button>
                      </div>
                  </div>
              </form>
        </div>
      </div>
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

        <div class="form-group row">

            <label for="begin" class="col-sm-2 col-form-label text-md-right">Tipo Transferencia:</label>  
            <div class="col-sm-3">
                <?php
                  
                  if(isset($typet)) {

                    if($typet == 1){
                       $atributo1 = 'selected';
                       $atributo2 = '';
                       $atributo3 = '';
                       $atributo4 = '';
                       $atributo5 = '';
                       $atributo6 = '';
                    }
                    if($typet == 2){
                       $atributo1 = '';
                       $atributo2 = 'selected';
                       $atributo3 = '';
                       $atributo4 = '';
                       $atributo5 = '';
                       $atributo6 = '';
                    }
                    if($typet == 3){
                       $atributo1 = '';
                       $atributo2 = '';
                       $atributo3 = 'selected';
                       $atributo4 = '';
                       $atributo5 = '';
                       $atributo6 = '';
                    }
                    if($typet == 4){
                       $atributo1 = '';
                       $atributo2 = '';
                       $atributo3 = '';
                       $atributo4 = 'selected';
                       $atributo5 = '';
                       $atributo6 = '';
                    }
                    if($typet == 5){
                       $atributo1 = '';
                       $atributo2 = '';
                       $atributo3 = '';
                       $atributo4 = '';
                       $atributo5 = 'selected';
                       $atributo6 = '';
                    }
                    if($typet == 6){
                       $atributo1 = '';
                       $atributo2 = '';
                       $atributo3 = '';
                       $atributo4 = '';
                       $atributo5 = '';
                       $atributo6 = 'selected';
                    }
                  } else {
                       $atributo1 = 'selected';
                       $atributo2 = '';
                       $atributo3 = '';
                       $atributo4 = '';
                       $atributo5 = '';
                       $atributo6 = '';
                  }
                    

                ?>
 
                <select id="type_transf"  name="type_transf" autocomplete="off" class="form-select sm-3 form-control @error('type_transf') is-invalid @enderror">
                    <option {{$atributo1}} value="1">Almacén a Almacén</option>
                    <option {{$atributo2}} value="2">Almacén a Sucursal</option>
                    <option {{$atributo3}} value="3">Sucursal a Almacén</option>
                    <option {{$atributo4}} value="4">Sucursal a Sucursal</option>
                    <option {{$atributo5}} value="5">Devolución a Almacén</option>
                    <option {{$atributo6}} value="6">Devolución a Sucursal</option>
                </select>
                @error('type_transf')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>


            <label style="display:none;" for="begin" class="col-sm-2 col-form-label text-md-right">Tipo:</label>  
            <div class="col-sm-3">
               
                <select style="display:none;" id="type" name="type" class="form-select sm-3 form-control @error('type_transf') is-invalid @enderror">
                    <option selected value="1">Mercancia y Materia Prima</option>
                    <option value="2">Mercancia</option>
                    <option value="3">Matria Prima</option>
                </select>

                @error('type')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

        </div>

        <div class="form-group row">

            <label for="begin" class="col-sm-2 col-form-label text-md-right">Origen:</label>  
            <div class="col-sm-3">
                
                <select style="display:none;" class="form-select sm-3 form-control" id="textdevolution">
                    <option selected  value="" disabled>DEVOLUCIÓN</option> 
                </select>

                <select id="id_branch" name="id_branch" class="form-select sm-3 form-control @error('id_branch') is-invalid @enderror">
                    @isset($branches)
                        @foreach($branches as $branchs)
                        
                                @if ($branch == $branchs->id)
                                     <option selected value="{{$branchs->id}}">{{ $branchs->description ?? '' }}</option>
                                @else
                                    <option value="{{$branchs->id}}">{{ $branchs->description ?? '' }}</option>
                                @endif
    
                        @endforeach
                    @endisset
                </select>
                @error('id_branch')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <label for="end" class="col-sm-2 col-form-label text-md-right">Destino:</label>  
            <div class="col-sm-3">
               
                <select id="id_branch_end"  name="id_branch_end" class="form-select mb-3 form-control @error('id_branch') is-invalid @enderror">
                    @isset($branches)
                        @foreach($branches as $branchs)
                        
                                @if ($branch == $branchs->id)
                                     <option selected value="{{$branchs->id}}">{{ $branchs->description ?? '' }}</option>
                                @else
                                    <option value="{{$branchs->id}}">{{ $branchs->description ?? '' }}</option>
                                @endif
    
                        @endforeach
                    @endisset
                </select>
                @error('id_branch')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="col-sm-2" style="text-align: right;">
               <!-- <a type="button" href="#" class="btn btn-primary">Transferir Todo</a> -->
            </div>
        </div>

        <div class="table-responsive">
        <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
                <th style="width:1%;" class="text-center">ID</th>
                <th style="width:1%;" class="text-center">Código Comercial</th>
                <th class="text-center">Descripción</th>
                <th style="width:1%; display:none;"class="text-center">Tipo</th>
                <th class="text-center">Origen</th>
                <th class="text-center"  style="width: 1%">Inventario Actual</th>
                <th class="text-center">Destino</th>
                <th class="text-center"  style="width: 1%">Cantidad a Transferir</th>
                <th style="width:1%; display:none;" class="text-center">Foto</th>
                <th class="text-center" style="width: 1%">Acción</th>
       
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
                            <td style="width:1%;"class="text-center">{{ $var->id ?? '' }}</td>
                            <td style="width:1%;" class="text-center">{{ $var->code_comercial ?? '' }}</td>
                            <td class="text-center">{{$descripcion }}</td>
                            <td style="display:none;" class="text-center">{{ $var->type ?? '' }}</td>
                            <td class="text-right">
                                <?php echo $var->origen ?>
                            </td>
                            <td class="text-right" style="width: 1%"><span id='amountext{{$var->id}}'>{{ $var->amount }}</span></td>

                            <td class="text-center">
                                <?php echo $var->destino ?>

                            </td>
                       
                            <td class='text-right' style='width: 1%'><input onkeyup="noespac(this)" id='inputransf{{$var->id}}' type='text' class='form-control' style='text-align: right;' value='0'></td>
                            <td class="text-center" style="display:none;">

                                @if(isset($var->photo_product))
                                <!--arya/storage/app/public/img/-->
                                <img style="width:60px; max-width:60px; height:80px; max-height:80px" src="{{asset('arya/storage/app/public/img/'.$company->login.'/productos/'.$var->photo_product)}}">
                                <div class="file-footer-buttons">
                                <button type="button" class="btnimg btn-sm" title="Ver detalles" data-toggle="modal" data-target="#imagenModal" onclick="loadimg('{{asset('arya/storage/app/public/img/'.$company->login.'/productos/'.$var->photo_product)}}')"><i class="fas fa-search-plus"></i></button>     </div>
                                @endif
                            </td>

                            <td class='text-center' style='width: 1%'>
                                <a type='button' data-origen='1' data-destino='1' data-typet='1' data-product='{{$var->id}}' href='#' class='btn btn-primary btn_transferir'>Transferir</a>
                                <span id="mensajet{{$var->id}}" style="display: flex; align-items: center; font-style: normal; font-weight: normal; display:none; white-space: nowrap;" class="text-success">
                                    <i class="fas fa-check-circle"></i> Transferido
                                </span>
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



</div>

@endsection
@section('validacion')
    <script>
    
    </script>
@endsection
@section('javascript')

    <script type="text/javascript">


        $('#dataTable').DataTable({
            "ordering": true,
            "order": [],
            'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
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

        $(document).on('click','.btn_transferir',function(event){ // Añade el parámetro event

            event.preventDefault(); // Añade esta línea para prevenir la acción predeterminada

            var origen = $(this).attr('data-origen');
            var destino = $(this).attr('data-destino');
            var producto = $(this).attr('data-product');
            var selectdestino = $('#selectdestino'+producto).val();
            var inputmonto = $('#inputransf'+producto);
            var monto = $('#inputransf'+producto).val();
            var typet = $(this).attr('data-typet');
            var amountext = $('#amountext'+producto);
            var mensajet = $('#mensajet'+producto);

            monto = Number(monto);

            if (monto <= 0){
                alert("La Cantidad a Transferir debe ser mayor a 0 \nProducto ID "+producto);
            } else {
                if (selectdestino == origen && (typet == 1 || typet == 4)){
                    alert("No es posible transferir al mismo almacén");
                } else {
                    $.ajax({
                        url: `../warehouse/transferencia`,
                        method: 'GET',
                        data: { origen: origen, producto: producto, typet: typet, selectdestino: selectdestino, monto: monto },
                        success:(response)=>{
                            amountext.text(response.amount);
                            mensajet.show();
                            inputmonto.val(0);
                        },
                        error: (xhr, status, error) => {
                            alert('La Transferencia no se pudo completar recargar la página: '+ error);
                        }
                    });
                }
            }
        });
    
    $("#type_transf").on('change',function(){
        var type_transf = $(this).val();
        var type = document.getElementById("type").value;

        if (type_transf == 1){
            $("#id_branch").show();
            $("#textdevolution").hide();
            get_select(1);
            get_select_end(1);
        }

        if (type_transf == 2){
            $("#id_branch").show();
            $("#textdevolution").hide();
            get_select(1);
            get_select_end(2);
        }
        if (type_transf == 3){
            $("#id_branch").show();
            $("#textdevolution").hide();
            get_select(2);
            get_select_end(1);
        }
        if (type_transf == 4){
            $("#id_branch").show();
            $("#textdevolution").hide();
            get_select(2);
            get_select_end(2);
        }

        if (type_transf == 5){
            
            $("#id_branch").hide();
            $("#textdevolution").show();
            get_select_end(1);
        }
        if (type_transf == 6){
            $("#id_branch").hide();
            $("#textdevolution").show();
            get_select_end(2);
        }


        $.ajax({
                url: `../warehouse/refreshtable`, // Ruta a la función en tu controlador
                method: 'GET',
                data: { type_transf: type_transf, type:type },
                success: function(data) {

                    $("#dataTable tbody").empty();

                    data.forEach(function(row) {
                        var photoProduct = row.photo_product; // Asegúrate de tener esta variable disponible en tu código JS
                        var companyLogin = '<?php $company->login;?>';

                        var newRow = "<tr>";
                        newRow += "<td class='text-center'>" + row.id + "</td>";
                        newRow += "<td style='width:1%;' class='text-center'>" + row.code_comercial + "</td>";
                       
                        if (typeof row.description !== null) {
                            newRow += "<td class='text-center'>" + row.description + "</td>";
                        } else{
                            newRow += "<td class='text-center'></td>";
                        }

                        newRow += "<td class='text-center' style='display:none;'>" + row.type + "</td>";
                        newRow += "<td class='text-right'>" + row.origen + "</td>";
                        newRow += "<td class='text-right' style='width: 1%'><span id='amountext"+row.id+"'>" + row.amount + "</span></td>";
                        newRow += "<td class='text-center'>" + row.destino + "</td>";
                        newRow += "<td class='text-right' style='width: 1%'><input onkeyup='noespac(this)' id='inputransf"+row.id+"' type='text' class='form-control' style='text-align: right;' value='0'></td>";

                        if (photoProduct !== null) {
                            var imgSrc = 'arya/storage/app/public/img/' + companyLogin + '/productos/' + photoProduct;
                            newRow += "<td class='text-center' style='display:none;'>";
                            newRow += "<img style='width:60px; max-width:60px; height:80px; max-height:80px' src='" + imgSrc + "'>";
                            newRow += "<div class='file-footer-buttons'>";
                            newRow += "<button type='button' class='btnimg btn-sm' title='Ver detalles' data-toggle='modal' data-target='#imagenModal' onclick='loadimg(\"" + imgSrc + "\")'><i class='fas fa-search-plus'></i></button>";
                            newRow += "</div></td>";
                        } else {
                            newRow += "<td class='text-center' style='display:none;'></td>";
                        }
                        
                        newRow += "<td class='text-center' style='width: 1%'><a type='button' data-origen='"+row.id_origen+"' data-destino='"+row.id_destino+"' data-typet='"+type_transf+"' data-product='"+row.id+"' href='#' class='btn btn-primary btn_transferir'>Transferir</a>"
                        newRow += "<span id='mensajet"+row.id+"' style='display: flex; align-items: center; font-style: normal; font-weight: normal; display:none; white-space: nowrap;' class='text-success'><i class='fas fa-check-circle'></i> Transferido</span></td>";
                        newRow += "</tr>";
                        $("#dataTable").append(newRow);
                    });
                }
            });

    });



    $("#id_branch").on('change',function(){
        var branch = $(this).val();
        var type_transf = document.getElementById("type_transf").value;
        var type = document.getElementById("type").value;

        $.ajax({
                url: `../warehouse/refresorigen`, // Ruta a la función en tu controlador
                method: 'GET',
                data: { type_transf: type_transf, type: type, branch: branch },
                success: function(data) {

                    $("#dataTable tbody").empty();

                    data.forEach(function(row) {
                        var photoProduct = row.photo_product; // Asegúrate de tener esta variable disponible en tu código JS
                        var companyLogin = '<?php $company->login;?>';

                        var newRow = "<tr>";
                        newRow += "<td class='text-center'>" + row.id + "</td>";
                        newRow += "<td style='width:1%;' class='text-center'>" + row.code_comercial + "</td>";
                       
                        if (typeof row.description !== null) {
                            newRow += "<td class='text-center'>" + row.description + "</td>";
                        } else{
                            newRow += "<td class='text-center'></td>";
                        }

                        newRow += "<td class='text-center' style='display:none;'>" + row.type + "</td>";
                        newRow += "<td class='text-right'>" + row.origen + "</td>";
                        newRow += "<td class='text-right' style='width: 1%'><span id='amountext"+row.id+"'>" + row.amount + "</span></td>";
                        newRow += "<td class='text-center'>" + row.destino + "</td>";
                        newRow += "<td class='text-right' style='width: 1%'><input onkeyup='noespac(this)' id='inputransf"+row.id+"' type='text' class='form-control' style='text-align: right;' value='0'></td>";

                        if (photoProduct !== null) {
                            var imgSrc = 'arya/storage/app/public/img/' + companyLogin + '/productos/' + photoProduct;
                            newRow += "<td class='text-center' style='display:none;'>";
                            newRow += "<img style='width:60px; max-width:60px; height:80px; max-height:80px' src='" + imgSrc + "'>";
                            newRow += "<div class='file-footer-buttons'>";
                            newRow += "<button type='button' class='btnimg btn-sm' title='Ver detalles' data-toggle='modal' data-target='#imagenModal' onclick='loadimg(\"" + imgSrc + "\")'><i class='fas fa-search-plus'></i></button>";
                            newRow += "</div></td>";
                        } else {
                            newRow += "<td class='text-center' style='display:none;'></td>";
                        }
                        
                        newRow += "<td class='text-center' style='width: 1%'><a type='button' data-origen='"+row.id_origen+"' data-destino='"+row.id_destino+"' data-typet='"+type_transf+"' data-product='"+row.id+"' href='#' class='btn btn-primary btn_transferir'>Transferir</a>"
                        newRow += "<span id='mensajet"+row.id+"' style='display: flex; align-items: center; font-style: normal; font-weight: normal; display:none; white-space: nowrap;' class='text-success'><i class='fas fa-check-circle'></i> Transferido</span></td>";
                        newRow += "</tr>";
                        $("#dataTable").append(newRow);
                    });
                }
            });

    });




    $("#id_branch_end").on('change',function(){
        var branch_end = $(this).val();
        var branch = document.getElementById("id_branch").value;
        var type_transf = document.getElementById("type_transf").value;
        var type = document.getElementById("type").value;

        $.ajax({
                url: `../warehouse/refresdestino`, // Ruta a la función en tu controlador
                method: 'GET',
                data: { type_transf: type_transf, type: type, branch: branch, branch_end: branch_end },
                success: function(data) {

                    $("#dataTable tbody").empty();

                    data.forEach(function(row) {
                        var photoProduct = row.photo_product; // Asegúrate de tener esta variable disponible en tu código JS
                        var companyLogin = '<?php $company->login;?>';

                        var newRow = "<tr>";
                        newRow += "<td class='text-center'>" + row.id + "</td>";
                        newRow += "<td style='width:1%;' class='text-center'>" + row.code_comercial + "</td>";
                       
                        if (typeof row.description !== null) {
                            newRow += "<td class='text-center'>" + row.description + "</td>";
                        } else{
                            newRow += "<td class='text-center'></td>";
                        }

                        newRow += "<td class='text-center' style='display:none;'>" + row.type + "</td>";
                        newRow += "<td class='text-right'>" + row.origen + "</td>";
                        newRow += "<td class='text-right' style='width: 1%'><span id='amountext"+row.id+"'>" + row.amount + "</span></td>";
                        newRow += "<td class='text-center'>" + row.destino + "</td>";
                        newRow += "<td class='text-right' style='width: 1%'><input onkeyup='noespac(this)' id='inputransf"+row.id+"' type='text' class='form-control' style='text-align: right;' value='0'></td>";

                        if (photoProduct !== null) {
                            var imgSrc = 'arya/storage/app/public/img/' + companyLogin + '/productos/' + photoProduct;
                            newRow += "<td class='text-center' style='display:none;'>";
                            newRow += "<img style='width:60px; max-width:60px; height:80px; max-height:80px' src='" + imgSrc + "'>";
                            newRow += "<div class='file-footer-buttons'>";
                            newRow += "<button type='button' class='btnimg btn-sm' title='Ver detalles' data-toggle='modal' data-target='#imagenModal' onclick='loadimg(\"" + imgSrc + "\")'><i class='fas fa-search-plus'></i></button>";
                            newRow += "</div></td>";
                        } else {
                            newRow += "<td class='text-center' style='display:none;'></td>";
                        }
                        
                        newRow += "<td class='text-center' style='width: 1%'><a type='button' data-origen='"+row.id_origen+"' data-destino='"+row.id_destino+"' data-typet='"+type_transf+"' data-product='"+row.id+"' href='#' class='btn btn-primary btn_transferir'>Transferir</a>"
                        newRow += "<span id='mensajet"+row.id+"' style='display: flex; align-items: center; font-style: normal; font-weight: normal; display:none; white-space: nowrap;' class='text-success'><i class='fas fa-check-circle'></i> Transferido</span></td>";
                        newRow += "</tr>";
                        $("#dataTable").append(newRow);
                    });
                }
            });
    });


    $(document).on('change', '.selectdestino', function() {
        var producto = $(this).attr('data-producto');
        var mensajet = $('#mensajet' + producto);
        mensajet.hide();
    });
    
    function get_select(typet){
        $.ajax({
            url:`../warehouse/getselect/${typet}`,
        
            success:(response)=>{
                let select_origen = $("#id_branch");
                let htmlOptions = '';
                if(response.length > 0){
                    response.forEach((item, index, object)=>{
                        let {id,description} = item;
                        if(id == 1) {
                            htmlOptions += `<option value='${id}' selected>${description}</option>`;
                        } else {
                            htmlOptions += `<option value='${id}'>${description}</option>`;
                        }
                    });
                } else {
                    htmlOptions = `<option value='todos' >No Tiene Registros</option>`;
                }
                select_origen.html('');
                select_origen.html(htmlOptions);
            },
            error:(xhr)=>{
                alert('error '+xhr);
            }
        })
    }

    function get_select_end(typet){

            $.ajax({
                url:`../warehouse/getselect/${typet}`,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                    let select_destino = $("#id_branch_end");
                    let htmlOptions = '';
                    // console.clear();
                    if(response.length > 0){

                        response.forEach((item, index, object)=>{
                            let {id,description} = item;
                            if(id == 1) {
                                htmlOptions += `<option value='${id}' selected>${description}</option>`;
                            } else {
                                htmlOptions += `<option value='${id}'>${description}</option>`;
                            }

                        });
                    } else {
                         htmlOptions = `<option value='todos' >No Tiene Registros</option>`;
                    }
                    //console.clear();
                    // console.log(htmlOptions);
                    select_destino.html('');
                    select_destino.html(htmlOptions);

                },
                error:(xhr)=>{
                    alert('Presentamos inconvenientes al consultar los datos');
                }
            })
    }


    

        /*$("#type").on('change',function(){
            type = $(this).val();
            window.location = "{{route('inventories', [''])}}"+"/"+type;
        }); */

        </script>
@endsection
