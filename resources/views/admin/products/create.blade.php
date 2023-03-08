
@extends('admin.layouts.dashboard')

@section('header')
<style>
    /*!
 * bootstrap-fileinput v5.2.6
 * http://plugins.krajee.com/file-input
 *
 * Krajee default styling for bootstrap-fileinput.
 *
 * Author: Kartik Visweswaran
 * Copyright: 2014 - 2021, Kartik Visweswaran, Krajee.com
 *
 * Licensed under the BSD-3-Clause
 * https://github.com/kartik-v/bootstrap-fileinput/blob/master/LICENSE.md
 */.btn-file input[type=file],.file-caption-icon,.file-no-browse,.file-preview .fileinput-remove,.file-zoom-dialog .btn-navigate,.file-zoom-dialog .floating-buttons,.krajee-default .file-thumb-progress{position:absolute}.file-loading input[type=file],input[type=file].file-loading{width:0;height:0}.file-no-browse{left:50%;bottom:20%;width:1px;height:1px;font-size:0;opacity:0;border:none;background:0 0;outline:0;box-shadow:none}.file-caption-icon,.file-input-ajax-new .fileinput-remove-button,.file-input-ajax-new .fileinput-upload-button,.file-input-ajax-new .no-browse .input-group-btn,.file-input-new .close,.file-input-new .file-preview,.file-input-new .fileinput-remove-button,.file-input-new .fileinput-upload-button,.file-input-new .glyphicon-file,.file-input-new .no-browse .input-group-btn,.file-zoom-dialog .modal-header:after,.file-zoom-dialog .modal-header:before,.hide-content .kv-file-content,.is-locked .fileinput-remove-button,.is-locked .fileinput-upload-button,.kv-hidden{display:none}.file-caption-icon .kv-caption-icon{line-height:inherit}.btn-file,.file-caption,.file-input,.file-loading:before,.file-preview,.file-zoom-dialog .modal-dialog,.krajee-default .file-thumbnail-footer,.krajee-default.file-preview-frame{position:relative}.file-error-message pre,.file-error-message ul,.krajee-default .file-actions,.krajee-default .file-other-error{text-align:left}.file-error-message pre,.file-error-message ul{margin:0}.krajee-default .file-drag-handle,.krajee-default .file-upload-indicator{float:left;margin-top:10px;width:16px;height:16px}.file-thumb-progress .progress,.file-thumb-progress .progress-bar{font-family:Verdana,Helvetica,sans-serif;font-size:.7rem}.krajee-default .file-thumb-progress .progress,.kv-upload-progress .progress{background-color:#ccc}.krajee-default .file-caption-info,.krajee-default .file-size-info{display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;width:160px;height:15px;margin:auto}.file-zoom-content>.file-object.type-flash,.file-zoom-content>.file-object.type-image,.file-zoom-content>.file-object.type-video{max-width:100%;max-height:100%;width:auto}.file-zoom-content>.file-object.type-flash,.file-zoom-content>.file-object.type-video{height:100%}.file-zoom-content>.file-object.type-default,.file-zoom-content>.file-object.type-html,.file-zoom-content>.file-object.type-pdf,.file-zoom-content>.file-object.type-text{width:100%}.file-loading:before{content:" Loading...";display:inline-block;padding-left:20px;line-height:16px;font-size:13px;font-variant:small-caps;color:#999;background:url(../img/loading.gif) top left no-repeat}.file-object{margin:0 0 -5px;padding:0}.btn-file{overflow:hidden}.btn-file input[type=file]{top:0;left:0;min-width:100%;min-height:100%;text-align:right;opacity:0;background:none;cursor:inherit;display:block}.btn-file ::-ms-browse{font-size:10000px;width:100%;height:100%}.file-caption.icon-visible .file-caption-icon{display:inline-block}.file-caption.icon-visible .file-caption-name{padding-left:25px}.file-caption.icon-visible>.input-group-lg .file-caption-name{padding-left:30px}.file-caption.icon-visible>.input-group-sm .file-caption-name{padding-left:22px}.file-caption-name:not(.file-caption-disabled){background-color:transparent}.file-caption-name.file-processing{font-style:italic;border-color:#bbb;opacity:.5}.file-caption-icon{padding:7px 5px;left:4px}.input-group-lg .file-caption-icon{font-size:1.25rem}.input-group-sm .file-caption-icon{font-size:.875rem;padding:.25rem}.file-error-message{color:#a94442;background-color:#f2dede;margin:5px;border:1px solid #ebccd1;border-radius:4px;padding:15px}.file-error-message pre{margin:5px 0}.file-caption-disabled{background-color:#eee;cursor:not-allowed;opacity:1}.file-preview{border-radius:5px;border:1px solid #ddd;padding:8px;width:100%;margin-bottom:5px}.file-preview .btn-xs{padding:1px 5px;font-size:12px;line-height:1.5;border-radius:3px}.file-preview .fileinput-remove{top:1px;right:1px;line-height:10px}.file-preview .clickable{cursor:pointer}.file-preview-image{font:40px Impact,Charcoal,sans-serif;color:green;width:auto;height:auto;max-width:100%;max-height:100%}.krajee-default.file-preview-frame{margin:8px;border:1px solid rgba(0,0,0,.2);box-shadow:0 0 10px 0 rgba(0,0,0,.2);padding:6px;float:left;text-align:center}.krajee-default.file-preview-frame .kv-file-content{width:213px;height:160px}.krajee-default .file-preview-other-frame{display:flex;align-items:center;justify-content:center}.krajee-default.file-preview-frame .kv-file-content.kv-pdf-rendered{width:400px}.krajee-default.file-preview-frame[data-template=audio] .kv-file-content{width:240px;height:55px}.krajee-default.file-preview-frame .file-thumbnail-footer{height:70px}.krajee-default.file-preview-frame:not(.file-preview-error):hover{border:1px solid rgba(0,0,0,.3);box-shadow:0 0 10px 0 rgba(0,0,0,.4)}.krajee-default .file-preview-text{color:#428bca;border:1px solid #ddd;outline:0;resize:none}.krajee-default .file-preview-html{border:1px solid #ddd}.krajee-default .file-other-icon{font-size:6em;line-height:1}.krajee-default .file-footer-buttons{float:right}.krajee-default .file-footer-caption{display:block;text-align:center;padding-top:4px;font-size:11px;color:#777;margin-bottom:30px}.file-upload-stats{font-size:10px;text-align:center;width:100%}.kv-upload-progress .file-upload-stats{font-size:12px;margin:-10px 0 5px}.krajee-default .file-preview-error{opacity:.65;box-shadow:none}.krajee-default .file-thumb-progress{top:37px;left:0;right:0}.krajee-default.kvsortable-ghost{background:#e1edf7;border:2px solid #a1abff}.krajee-default .file-preview-other:hover{opacity:.8}.krajee-default .file-preview-frame:not(.file-preview-error) .file-footer-caption:hover{color:#000}.kv-upload-progress .progress{height:20px;margin:10px 0;overflow:hidden}.kv-upload-progress .progress-bar{height:20px;font-family:Verdana,Helvetica,sans-serif}.file-zoom-dialog .file-other-icon{font-size:22em;font-size:50vmin}.file-zoom-dialog .modal-dialog{width:auto}.file-zoom-dialog .modal-header{display:flex;align-items:center;justify-content:space-between}.file-zoom-dialog .btn-navigate{margin:0 .1rem;padding:0;font-size:1.2rem;width:2.4rem;height:2.4rem;top:50%;border-radius:50%;text-align:center}.btn-navigate *{width:auto}.file-zoom-dialog .floating-buttons{top:5px;right:10px}.file-zoom-dialog .btn-kv-prev{left:0}.file-zoom-dialog .btn-kv-next{right:0}.file-zoom-dialog .kv-zoom-caption{max-width:50%;overflow:hidden;white-space:nowrap;text-overflow:ellipsis}.file-zoom-dialog .kv-zoom-header{padding:.5rem}.file-zoom-dialog .kv-zoom-body{padding:.25rem .5rem .25rem 0}.file-zoom-dialog .kv-zoom-description{position:absolute;opacity:.8;font-size:.8rem;background-color:#1a1a1a;padding:1rem;text-align:center;border-radius:.5rem;color:#fff;left:15%;right:15%;bottom:15%}.file-zoom-dialog .kv-desc-hide{float:right;color:#fff;padding:0 .1rem;background:0 0;border:none}.file-zoom-dialog .kv-desc-hide:hover{opacity:.7}.file-zoom-dialog .kv-desc-hide:focus{opacity:.9}.file-input-ajax-new .no-browse .form-control,.file-input-new .no-browse .form-control{border-top-right-radius:4px;border-bottom-right-radius:4px}.file-caption{width:100%;position:relative}.file-thumb-loading{background:url(../img/loading.gif) center center no-repeat content-box!important}.file-drop-zone{border:1px dashed #aaa;min-height:260px;border-radius:4px;text-align:center;vertical-align:middle;margin:12px 15px 12px 12px;padding:5px}.file-drop-zone.clickable:hover{border:2px dashed #999}.file-drop-zone.clickable:focus{border:2px solid #5acde2}.file-drop-zone .file-preview-thumbnails{cursor:default}.file-drop-zone-title{color:#aaa;font-size:1.6em;text-align:center;padding:85px 10px;cursor:default}.file-highlighted{border:2px dashed #999!important;background-color:#eee}.file-uploading{background:url(../img/loading-sm.gif) center bottom 10px no-repeat;opacity:.65}.file-zoom-fullscreen .modal-dialog{min-width:100%;margin:0}.file-zoom-fullscreen .modal-content{border-radius:0;box-shadow:none;min-height:100vh}.file-zoom-fullscreen .kv-zoom-body{overflow-y:auto}.floating-buttons{z-index:3000}.floating-buttons .btn-kv{margin-left:3px;z-index:3000}.kv-zoom-actions .btn-kv{margin-left:3px}.file-zoom-content{text-align:center;white-space:nowrap;min-height:300px}.file-zoom-content:hover{background:0 0}.file-zoom-content>*{display:inline-block;vertical-align:middle}.file-zoom-content .kv-spacer{height:100%}.file-zoom-content .file-preview-image,.file-zoom-content .file-preview-video{max-height:100%}.file-zoom-content>.file-object.type-image{height:auto;min-height:inherit}.file-zoom-content>.file-object.type-audio{width:auto;height:30px}@media (min-width:576px){.file-zoom-dialog .modal-dialog{max-width:500px}}@media (min-width:992px){.file-zoom-dialog .modal-lg{max-width:800px}}@media (max-width:767px){.file-preview-thumbnails{display:flex;justify-content:center;align-items:center;flex-direction:column}.file-zoom-dialog .modal-header{flex-direction:column}}@media (max-width:350px){.krajee-default.file-preview-frame:not([data-template=audio]) .kv-file-content{width:160px}}@media (max-width:420px){.krajee-default.file-preview-frame .kv-file-content.kv-pdf-rendered{width:100%}}.file-loading[dir=rtl]:before{background:url(../img/loading.gif) top right no-repeat;padding-left:0;padding-right:20px}.clickable .file-drop-zone-title{cursor:pointer}.file-sortable .file-drag-handle:hover{opacity:.7}.file-sortable .file-drag-handle{cursor:grab;opacity:1}.file-grabbing,.file-grabbing *{cursor:not-allowed!important}.file-grabbing .file-preview-thumbnails *{cursor:grabbing!important}.file-preview-frame.sortable-chosen{background-color:#d9edf7;border-color:#17a2b8;box-shadow:none!important}.file-preview .kv-zoom-cache{display:none}
</style>
@endsection

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
                <div class="card-header text-center font-weight-bold h3">Registro de Productos</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                        @csrf

                        <input id="id_user" type="hidden" class="form-control @error('id_user') is-invalid @enderror" name="id_user" value="{{ Auth::user()->id }}" readonly required autocomplete="id_user">
                       
                        <div class="form-group row">
                            
                            <label for="description" class="col-md-2 col-form-label text-md-right">Descripción</label>

                            <div class="col-md-4">
                                <input id="description" type="text" class="form-control @error('description') is-invalid @enderror" name="description" value="{{ old('description') }}" required autocomplete="description">

                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            
                            <label for="type" class="col-md-2 col-form-label text-md-right">Tipo</label>
                            <div class="col-md-4">
                            <select class="form-control" name="type" id="type">
                                <option value="SERVICIO">Servicio</option>
                                <option selected value="MERCANCIA">Mercancía</option>
                                <option value="MATERIAP">Materia Prima</option>
                                <option value="COMBO">Combo</option>
                                
                            </select>
                            </div>

                        </div>
                       
                        <div class="form-group row">
                                    
                            <label for="segment" class="col-md-2 col-form-label text-md-right">Segmento</label>
                        
                            <div class="col-md-4">
                            <select id="segment"  name="segment" class="form-control" required>
                                <option value="">Seleccione un Segmento</option>
                                @foreach($segments as $index => $value)
                                    <option value="{{ $index }}" {{ old('Segment') == $index ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                                </select>

                                @if ($errors->has('segment_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('segment_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                       
                            <label for="subsegment" class="col-md-2 col-form-label text-md-right">Sub Segmento</label>
                        
                            <div class="col-md-4">
                                <select  id="subsegment"  name="Subsegment" class="form-control" >
                                    <option value="">Selecciona un Sub Segmento</option>
                                </select>

                                @if ($errors->has('subsegment_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('subsegment_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>  

                        <div class="form-group row">
                                    
                            <label for="segment" class="col-md-2 col-form-label text-md-right">Sub Segmento 2 (Opcional)</label>
                        
                            <div class="col-md-4">
                                <select  id="twosubsegment"  name="twoSubsegment" class="form-control" >
                                    <option value=""></option>
                                </select>

                                @if ($errors->has('subsegment_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('subsegment_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <label for="subsegment" class="col-md-2 col-form-label text-md-right">Sub Segmento 3 (Opcional)</label>
                        
                            <div class="col-md-4">
                                <select  id="threesubsegment"  name="threeSubsegment" class="form-control" >
                                    <option value=""></option>
                                </select>

                                @if ($errors->has('subsegment_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('subsegment_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>  

                       
                        <div class="form-group row">
                            <label for="unitofmeasure" class="col-md-2 col-form-label text-md-right">Unidad de Medida</label>

                            <div class="col-md-4">
                            <select class="form-control" id="unit_of_measure_id" name="unit_of_measure_id">
                                @foreach($unitofmeasures as $var)
                                    <option value="{{ $var->id }}">{{ $var->description }}</option>
                                @endforeach
                              
                            </select>
                            </div>
                            <label for="code_comercial" class="col-md-2 col-form-label text-md-right">Código Comercial</label>

                            <div class="col-md-4">
                                <input id="code_comercial" type="text" class="form-control @error('code_comercial') is-invalid @enderror" name="code_comercial" value="{{ old('code_comercial') }}" required autocomplete="code_comercial">

                                @error('code_comercial')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                       
                       
                        <div class="form-group row">
                            <label for="price" class="col-md-2 col-form-label text-md-right">Precio</label>

                            <div class="col-md-4">
                                <input onkeyup="noespac(this)" id="price" type="text" class="form-control @error('price') is-invalid @enderror" name="price" value="{{ 0 ?? old('price') }}" required autocomplete="price">

                                @error('price')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="price_buy" class="col-md-2 col-form-label text-md-right">Precio Compra</label>

                            <div class="col-md-4">
                                <input onkeyup="noespac(this)" id="price_buy" type="text" class="form-control @error('price_buy') is-invalid @enderror" name="price_buy" value="{{ 0 ?? old('price_buy') }}" required autocomplete="price_buy">

                                @error('price_buy')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>



                        <div class="form-group row">
                            <label for="lote" class="col-md-2 col-form-label text-md-right">Lote</label>

                            <div class="col-md-4">
                                <input id="lote" type="text" class="form-control @error('lote') is-invalid @enderror" name="lote" value="{{ old('lote') }}" autocomplete="lote">

                                @error('lote')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="fecha_vencimiento" class="col-md-2 col-form-label text-md-right">Fecha de Vencimiento</label>
                            <div class="col-md-4">
                                <input id="fecha_vencimiento" type="text" class="form-control @error('fecha_vencimiento') is-invalid @enderror" name="fecha_vencimiento" value="{{ old('fecha_vencimiento') }}" autocomplete="fecha_vencimiento" placeholder="00-00-2022">

                                @error('fecha_vencimiento')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>                    
                        </div>

                        
                        <div class="form-group row">
                           
                            <label for="money" class="col-md-2 col-form-label text-md-right">Moneda</label>

                            <div class="col-md-4">
                                <select class="form-control" name="money" id="money">
                                    <option value="D">Dolar</option>
                                    <option value="Bs">Bolívares</option>
                                </select>
                            </div>

                            <label for="exento" class="col-md-2 col-form-label text-md-right">exento</label>
                            
                            <div class="form-check">
                                <input class="form-check-input position-static" type="checkbox" id="exento" name="exento" value="1" aria-label="...">
                            </div>
                            <label for="islr" class="col-md-1 col-form-label text-md-right">Islr</label>
                            
                            <div class="form-check">
                                <input class="form-check-input position-static" type="checkbox" id="islr" name="islr" value="1" aria-label="...">
                            </div>
                            
                        </div>


                        @if ((Auth::user()->id_company  == '21'))
                        <div id="companylic">
                            <div class="form-group row">
                                <div class="col-sm-2">
                                    <label for="box">Cajas:</label>
                                </div>
                                <div class="col-sm-4">
                                    <input id="box" type="text" class="form-control @error('box') is-invalid @enderror" name="Cajas" value="{{ 1 ?? old('Cajas') }}" required autocomplete="box">
                                    @error('box')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-sm-2">
                                    <label for="degree">Grado de Alcohol:</label>
                                </div>
                                <div class="col-sm-4">
                                    <input id="degree" type="text" class="form-control @error('degree') is-invalid @enderror" name="Grado" value="{{ old('Grado') }}" required autocomplete="degree">
                                    @error('degree')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2">
                                    <label for="bottle">Botellas por Caja:</label>
                                </div>
                                <div class="col-sm-4">
                                    <input id="bottle" type="text" class="form-control @error('bottle') is-invalid @enderror" name="Botellas" value="{{ old('Botellas') }}" required autocomplete="bottle">
                                    @error('bottle')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-sm-2">
                                    <label for="liter">Litros por Botellas:</label>
                                </div>
                                <div class="col-sm-2">
                                    <input id="liter" type="text" class="form-control @error('liter') is-invalid @enderror" name="Litros" value="{{ old('Litros') }}"  required autocomplete="liter">
                                    @error('liter')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                           </div>
                      
                            <div class="form-group row">
                                <div class="col-sm-2">
                                    <label for="capacity">Capacidad de Litros:</label>
                                </div>
                                <div class="col-sm-4">
                                    <input id="capacity" type="text" class="form-control @error('capacity') is-invalid @enderror" name="Capacidad" value="0" required autocomplete="capacity" readonly>
                                    @error('capacity')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @endif
                       
                        <div class="form-group row">
                            <div style="display: none;">
                            <label for="special_impuesto" class="col-md-2 col-form-label text-md-right">Impuesto Especial</label>

                            <div class="col-md-4">
                                <input  onkeyup="noespac(this)" id="special_impuesto" type="text" class="form-control @error('special_impuesto') is-invalid @enderror" name="special_impuesto" value="{{ 0 ?? old('special_impuesto') }}" required autocomplete="special_impuesto">

                                @error('special_impuesto')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            </div>
                            <label id="AssociateAccount" for="account" class="col-md-2 col-form-label text-md-right">Asociar a Cuenta:</label>
                        
                            <div id="AssociateAccount2" class="col-md-4">
                            <select id="id_account"  name="id_account" class="form-control" required>
                                <option value="">Seleccione una Cuenta</option>
                                @foreach($accounts as $account)
                                    
                                    @if($account->description == 'Mercancia para la Venta')
                                    <option selected value="{{ $account->id }}">{{ $account->description }}</option>
                                    @else
                                    <option value="{{ $account->id }}">{{ $account->description }}</option>
                                    @endif
                                @endforeach
                                </select>

                                @if ($errors->has('account_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('account_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                                                        
                            <label for="cost_average" class="col-md-2 col-form-label text-md-right">Costo Promedio</label>

                            <div class="col-md-4">
                                <input onkeyup="noespac(this)" id="cost_average" type="text" class="form-control @error('cost_average') is-invalid @enderror" name="cost_average" value="{{ 0 ?? old('cost_average') }}" required autocomplete="cost_average">

                                @error('cost_average')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="imagen" class="col-md-2 col-form-label text-md-right">Subir Foto</label>
                            <div class="col-md-4">
                                <input id="fotop" style="border:0;" name="fotop" type="file" data-initial-preview="" accept="image/*">
                             
                           <br>

                        </div>
                        </div>
                        
                        <p id="valueInput"></p> 
                        <br>

                        <div class="form-group row mb-0">
                            <div class="col-md-3 offset-md-2">
                                <button type="submit" class="btn btn-primary">
                                   Registrar Producto
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('products') }}" id="btnfacturar" name="btnfacturar" class="btn btn-danger" title="facturar">Volver</a>  
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

        function litros(){
            var n1 = document.getElementById('bottle').value;
            var n2 = document.getElementById('liter').value;
            var n3 = document.getElementById('box').value;

            if ( n1 == '' || n1 == null ) {
                alert("Agregar la cantidad de botellas");
                exit;
            }  
            
            
            if ( n2 == '' || n2 == null ) {
                alert("Agregar la cantidad de litros o mililitros");
                exit;
            }  

            
            if ( n3 == '' || n3 == null ) {
                alert("Agregar la cantidad de cajas");
                exit;
            }  

            
            if ((n1 != null || n1 != '') && (n2 != null || n2 != '') && (n3 != null || n3 != '')) {
                // var n2 = document.getElementById('xponcetaje').value; // PORCENTAJE
                var n2_format  = n2.replace(",", "." );
                var resultado       = (parseFloat(n1) * parseFloat(n3)  * parseFloat(n2_format));
                document.getElementsByName("Capacidad")[0].value = resultado;
            }
        } 
     
        $("#liter").on('blur',function(){
            litros();
        }); 
        
        $("#bottle").on('blur',function(){
            litros();
        }); 
        
        $("#box").on('blur',function(){
            litros();
        }); 
        

        $(document).ready(function () {
            $("#liter").mask('000.000.000.000.000,00', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#degree").mask('000.000.000.000.000,00', { reverse: true });
            
        });

	
    </script>
@endsection
@section('javascript')

<script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.2.6/js/fileinput.min.js"></script>
<!-- following theme script is needed to use the Font Awesome 5.x theme (`fas`). Uncomment if needed. -->
<script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.2.6/themes/fas/theme.min.js"></script>
<!-- optionally if you need translation for your language then include the locale file as mentioned below (replace LANG.js with your language locale) -->
<script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.2.6/js/locales/LANG.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.5.0/js/locales/es.min.js" integrity="sha512-q2lXTQuccVsDwaOpJNHbGDL2c5DEK706u1MCjKuGAG4zz+q1Sja3l2RuymU3ySE6RfmTYZ/V4wY5Ol71sRvvWA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>  

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

        $("#type").on('change',function(){
            var type = $(this).val();
          
            if(type == 'SERVICIO'){
                $("#AssociateAccount").hide();
                
                $('#id_account').removeAttr('required');
                $("#AssociateAccount2").hide();
            }else{
                $("#AssociateAccount").show();
                $("#AssociateAccount2").show();
                $('#id_account').prop('required',true);
                

            }
        });

        $("#segment").on('change',function(){
            var segment_id = $(this).val();
            $("#subsegment").val("");
            
            // alert(segment_id);
            getSubsegment(segment_id);
        });


        function getSubsegment(segment_id){
            // alert(`../subsegment/list/${segment_id}`);
            $.ajax({
                url:`../subsegment/list/${segment_id}`,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                    let subsegment = $("#subsegment");
                    let htmlOptions = `<option value='' >Seleccione..</option>`;
                    // console.clear();
                    if(response.length > 0){
                        response.forEach((item, index, object)=>{
                            let {id,description} = item;
                            htmlOptions += `<option value='${id}' {{ old('Subsegment') == '${id}' ? 'selected' : '' }}>${description}</option>`

                        });
                    }
                    //console.clear();
                    // console.log(htmlOptions);
                    subsegment.html('');
                    subsegment.html(htmlOptions);
                
                    
                
                },
                error:(xhr)=>{
                    
                }
            })
        }

        $("#subsegment").on('change',function(){
                var subsegment_id = $(this).val();
                var segment_id    = document.getElementById("segment").value;

                get2Subsegment(subsegment_id);
            });


        function get2Subsegment(subsegment_id){
           
            $.ajax({
                url:"{{ route('twosubsegments.list','') }}" + '/' + subsegment_id,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                    let subsegment = $("#twosubsegment");
                    let htmlOptions = `<option value='' >Seleccione..</option>`;
                    // console.clear();
                    if(response.length > 0){
                        response.forEach((item, index, object)=>{
                            let {id,description} = item;
                            htmlOptions += `<option value='${id}' {{ old('Subsegment') == '${id}' ? 'selected' : '' }}>${description}</option>`

                        });
                    }
                    //console.clear();
                    // console.log(htmlOptions);
                    subsegment.html('');
                    subsegment.html(htmlOptions);
                
                    
                
                },
                error:(xhr)=>{
                    
                }
            })
        }



        $("#twosubsegment").on('change',function(){
                var subsegment_id = $(this).val();
                var segment_id    = document.getElementById("segment").value;

                get3Subsegment(subsegment_id);
            });


        function get3Subsegment(subsegment_id){
           
            $.ajax({
                url:"{{ route('threesubsegments.list','') }}" + '/' + subsegment_id,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                    let subsegment = $("#threesubsegment");
                    let htmlOptions = `<option value='' >Seleccione..</option>`;
                    // console.clear();
                    if(response.length > 0){
                        response.forEach((item, index, object)=>{
                            let {id,description} = item;
                            htmlOptions += `<option value='${id}' {{ old('Subsegment') == '${id}' ? 'selected' : '' }}>${description}</option>`

                        });
                    }
                    //console.clear();
                    // console.log(htmlOptions);
                    subsegment.html('');
                    subsegment.html(htmlOptions);
                
                    
                
                },
                error:(xhr)=>{
                    
                }
            })
        }
    </script>
@endsection

