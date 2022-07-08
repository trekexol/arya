@extends('admin.layouts.dashboard')

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



<div class="container" >
    <div class="row justify-content-center" >
        <div class="col-md-12" >
            <div class="card">
                
                @if($type == 'Nota de Entrega')
                <div class="card-header" ><h3>Registro de {{$type ?? 'Cotización'}} {{$quotation->number_delivery_note ?? ''}}</h3> </div>
                @endif
                @if($type == 'factura')
                <div class="card-header" ><h3>Registro de {{$type ?? 'Cotización'}}</h3> </div>
                @endif
                @if($type != 'Nota de Entrega' && $type != 'factura')            
                <div class="card-header" ><h3>Registro de {{'Cotización'}} {{$quotation->id ?? ''}}</h3> </div>
                @endif 
                <div class="card-body" >
                    <form  method="POST" id="formUpdate"  action="{{ route('quotations.updateQuotation',$quotation->id,$type) }}" enctype="multipart/form-data" >
                        @method('PATCH')
                        @csrf()
                        <input id="coinhidden2" type="hidden" class="form-control @error('coin') is-invalid @enderror" name="coin2" value="{{ $coin ?? 'bolivares' }}" readonly autocomplete="coin">
                        <input id="type_f" type="hidden" class="form-control @error('type_f') is-invalid @enderror" name="type_f" value="{{ $type ?? 'Cotización' }}" autocomplete="type_f">
            
                        <div class="form-group row">
                            <label for="date_quotation" class="col-sm-2 col-form-label text-md-right">Fecha de {{$type ?? 'Cotización'}}:</label>
                            <div class="col-sm-2">
                                <input id="date_quotation" type="date" class="form-control @error('date_quotation') is-invalid @enderror" name="date_quotation" value="{{ $quotation->date_quotation ?? $datenow }}"  required autocomplete="date_quotation">
    
                                @error('date_quotation')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="serie" class="col-sm-2 col-form-label text-md-right">N° de Control/Serie:</label>

                            <div class="col-sm-2">
                                <input id="serie" type="text" class="form-control @error('serie') is-invalid @enderror" name="serie" value="{{ $quotation->serie ?? '' }}"  autocomplete="serie">

                                @error('serie')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>  
                            @isset($branches)
                            <label for="serie" class="col-sm-2 col-form-label text-md-right">Sucursal:</label>

                            <div class="col-sm-2">
                                
                                
                                    @foreach($branches as $branch)
                                        @if ($user_branch->id == $branch->id) 
                                            <input id="id_branch" name="id_branch" type="text" class="form-control @error('id_branch') is-invalid @enderror" name="id_branch" value="{{ $branch->description ?? '' }}"  autocomplete="id_branch" disabled>
                                        @endif
                                    @endforeach
                                
                                    
                                    @error('id_branch')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror                              
                            </div>          
                            @endisset
                        </div>

                        <div class="form-group row">

                            <label for="client" class="col-md-2 col-form-label text-md-right">Cliente:</label>
                            <div class="col-md-3">
                                <input id="client" type="text" class="form-control @error('client') is-invalid @enderror" name="client" value="{{ $quotation->clients['name'] ?? '' }}" readonly autocomplete="client">
                                @error('client')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <a href="#" onclick="searchClient();" title="Cambiar Cliente"><i class="fa fa-eye"></i></a>  

                            <label for="vendor" class="col-md-2 col-form-label text-md-right">Vendedor:</label>
                            <div class="col-md-3">
                                <input id="vendor" type="text" class="form-control @error('vendor') is-invalid @enderror" name="vendor" value="{{ $quotation->vendors['name'] ?? old('vendor') }} {{ $quotation->vendors['surname'] ?? '' }}" readonly autocomplete="vendor">
                                @error('vendor')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        
                        <div class="form-group row">
                            <label for="transports" class="col-md-2 col-form-label text-md-right">Transporte:</label>
                            <div class="col-md-3">
                                <input id="transport" type="text" class="form-control @error('transport') is-invalid @enderror" name="transport" value="{{ $quotation->transports['placa'] ?? old('transport') }}" readonly autocomplete="transport"> 
                           
                                @error('transport')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-sm-1"></div>

                            <label for="note" class="col-md-2 col-form-label text-md-right">Nota Pie de Factura:</label>

                            <div class="col-md-4">
                                <input id="note" type="text" class="form-control @error('note') is-invalid @enderror" name="note" value="{{ $quotation->note ?? old('note') }}"  autocomplete="note">

                                @error('note')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            
                        </div>
                       
                        <div class="form-group row">
                            <label for="observation" class="col-md-2 col-form-label text-md-right">Observaciones:</label>

                            <div class="col-md-4">
                                <input id="observation" type="text" class="form-control @error('observation') is-invalid @enderror" name="observation" value="{{ $quotation->observation ?? old('observation') }}"   autocomplete="observation">

                                @error('observation')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label  class="col-md-2 col-form-label text-md-right"><h6>Total de la<br> {{$type ?? 'Cotización'}}:</h6></label>
                            <div class="col-md-2 col-form-label text-md-left">
                                <label for="totallabel" id="total"><h3></h3></label>
                            </div>
                            <div class="col-md-2">
                            <button type="submit" id="btnUpdateQuotation" name="btnUpdateQuotation" class="btn btn-success" title="Actualizar Datos">Guardar Cambios</button>  
                        </div>
                    </form>
                        <form id="formSendProduct" method="POST" action="{{ route('quotations.storeproduct') }}" enctype="multipart/form-data" onsubmit="return validacion()">
                            @csrf
                            <input id="id_quotation" type="hidden" class="form-control @error('id_quotation') is-invalid @enderror" name="id_quotation" value="{{ $quotation->id ?? -1}}" readonly required autocomplete="id_quotation">
                            <input id="id_inventory" type="hidden" class="form-control @error('id_inventory') is-invalid @enderror" name="id_inventory" value="{{ $inventory->id ?? -1 }}" readonly required autocomplete="id_inventory">
                            <input id="coinhidden" type="hidden" class="form-control @error('coin') is-invalid @enderror" name="coin" value="{{ $coin ?? 'bolivares' }}" readonly required autocomplete="coin">
                            <input id="bcv" type="hidden" class="form-control @error('bcv') is-invalid @enderror" name="bcv" value="{{ $bcv ?? $bcv_quotation_product }}" readonly required autocomplete="bcv">
                            <input id="id_user" type="hidden" class="form-control @error('id_user') is-invalid @enderror" name="id_user" value="{{ Auth::user()->id }}" readonly required autocomplete="id_user">
                       
                            <input id="type_quotation" type="hidden" class="form-control @error('type_quotation') is-invalid @enderror" name="type_quotation" value="{{ $type ?? null}}" readonly required autocomplete="type_quotation">
                            
                        
                        <div class="form-group row" id="formcoin">
                            <label id="coinlabel" for="coin" class="col-md-1 col-form-label text-md-right">Moneda:</label>

                            <div class="col-md-2">
                                <select class="form-control" name="coin" id="coin">
                                    <option value="bolivares">Bolívares</option>
                                    @if($coin == 'dolares')
                                        <option selected value="dolares">Dolares</option>
                                    @else 
                                        <option value="dolares">Dolares</option>
                                    @endif
                                </select>
                            </div>
                            <label for="rate" class="col-md-1 col-form-label text-md-right">Tasa:</label>
                            <div class="col-md-2">
                                <input id="rate" type="text" class="form-control @error('rate') is-invalid @enderror" name="rate" value="{{  number_format(bcdiv($quotation->bcv ?? $bcv, '1', 2) , 2, ',', '.') }}" required autocomplete="rate">
                                @error('rate')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <a href="#" onclick="refreshrate()" title="actualizar tasa"><i class="fa fa-redo-alt"></i></a>  
                            <label  class="col-md-2 col-form-label text-md-right h6">Tasa BCV actual:</label>
                            <div class="col-md-2 col-form-label text-md-left">
                                <label for="tasaactual" id="tasaacutal">{{ number_format(bcdiv(($bcv), '1', 2), 2, ',', '.')}}</label>
                            </div>

                        </div>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="customSwitches">
                            <label id="id_scan_auto" class="custom-control-label" for="customSwitches">Activar Agregar Automático</label>
                        </div>
                            
                                <div class="form-row col-md-12">

                                    <div class="form-group col-md-2">
                                        <label for="description" >Código</label>
                                        <input id="code" type="text" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ $inventory->code_comercial ?? old('code') ?? '' }}" required autocomplete="code" onblur="searchCode()">
                                 
                                    </div>


                                    <div class="form-group col-md-1">
                                        <a href="" title="Buscar Producto Por Codigo" onclick="searchCode()"><i class="fa fa-search"></i></a>  

                                        <a href="{{ route('quotations.selectproduct',[$quotation->id,$coin,'productos',$type]) }}" title="Productos"><i class="fa fa-eye"></i></a>  
                                        
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label for="description" >Descripción</label>
                                        <input id="description" type="text" class="form-control @error('description') is-invalid @enderror" name="description" value="{{ $inventory->description ?? old('description') ?? '' }}" required autocomplete="description">
        
                                        @error('description')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-1">
                                        <label for="amount" >Cantidad</label>
                                        <input id="amount_product"  type="text" class="form-control @error('amount') is-invalid @enderror" name="amount" value="1" required autocomplete="amount">
        
                                        @error('amount')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-1">
                                        @if (empty($inventory))
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="exento" id="gridCheck">
                                            <label class="form-check-label" for="gridCheck">
                                                Exento
                                            </label>
                                        </div>
                                        @else
                                        <div class="form-check">
                                            @if($inventory->exento == 1)
                                                <input class="form-check-input" type="checkbox" name="exento" checked id="gridCheck">
                                            @else
                                                <input class="form-check-input" type="checkbox" name="exento" id="gridCheck">
                                            @endif
                                            <label class="form-check-label" for="gridCheck">
                                                Exento
                                            </label>
                                        </div>
                                        @endif
                                            
                                    </div>
                                    <div class="form-group col-md-1">
                                        @if (empty($inventory))
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="exento" id="gridCheck">
                                            <label class="form-check-label" for="gridCheck">
                                                Retiene ISLR
                                            </label>
                                        </div>
                                        @else
                                            <div class="form-check">
                                                @if($inventory->exento == 1)
                                                    <input class="form-check-input" type="checkbox" name="islr" checked id="gridCheck2">
                                                @else
                                                    <input class="form-check-input" type="checkbox" name="islr" id="gridCheck2">
                                                @endif
                                                <label class="form-check-label" for="gridCheck2">
                                                    Retiene ISLR
                                                </label>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group col-md-2">
                                        @if(isset($inventory->price) && (isset($quotation->bcv)) && ($inventory->money != 'Bs') && ($coin == 'bolivares')) 
                                            <?php 
                                                
                                                $product_Bs = $inventory->price * $quotation->bcv;
                                               
                                            ?>
                                            <label for="cost" >Precio</label>
                                            <input id="cost" type="text" class="form-control @error('cost') is-invalid @enderror" name="cost" value="{{ number_format($product_Bs, 2, ',', '.') ?? '' }}"  required autocomplete="cost">
                                        @else
                                            <label for="cost" >Precio</label>
                                            <input id="cost" type="text" class="form-control @error('cost') is-invalid @enderror" name="cost" value="{{number_format($inventory->price ?? 0, 2, ',', '.') ?? '' }}"  required autocomplete="cost">
                                        @endif

                                        
                                        @error('cost')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-1">
                                        <label for="discount" >Descuento</label>
                                        <input id="discount_product" type="text" class="form-control  @error('discount') is-invalid @enderror" name="discount" value="0" required autocomplete="discount">
        
                                        @error('discount')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                   
                                    <div class="form-group col-md-1">
                                        @if (isset($inventory))
                                            <input type="button" title="Agregar" value=" + "  onclick="sendProduct()" >
                                        @endif
                                        
                                    </div>
                                </div>    
                        </form>      





                               <div class="card-body">
                                <div class="table-responsive">
                                <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                    <tr>
                                        <th class="text-center">Código</th>
                                        <th class="text-center">Descripción</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-center">Precio</th>
                                        <th class="text-center">Descuento</th>
                                        <th class="text-center">Sub Total</th>
                                        <th class="text-center"><i class="fas fa-cog"></i></th>
                                      
                                    </tr>
                                    </thead>
                                    
                                    <tbody>
                                        @if (empty($inventories_quotations))
                                        @else
                                        <?php
                                            $suma = 0.00;
                                        ?>
                                       
                                            @foreach ($inventories_quotations as $var)

                                            <?php
                                                if($coin != 'bolivares'){
                                                    $var->price = bcdiv(($var->price / ($var->rate ?? 1)), '1', 2);
                                                }
                                                
                                                $percentage = (($var->price * $var->amount_quotation) * $var->discount)/100;

                                                $total_less_percentage = ($var->price * $var->amount_quotation) - $percentage;


                                            ?>
                                                <tr>
                                                <td style="text-align: right">{{ $var->code}}</td>
                                                @if(isset($var->retiene_iva) && ($var->retiene_iva == 1))
                                                    @if($var->stock <= 0 || $var->stock < $var->amount_quotation)
                                                    <td style="text-align: right">{{ $var->description}} (E) <span style="color: red;">Stock {{ $var->stock}}</span></td>
                                                    @else
                                                    <td style="text-align: right">{{ $var->description}} (E)</td>
                                                    @endif
                                                @else
                                                     @if($var->stock <= 0 || $var->stock < $var->amount_quotation)
                                                     <td style="text-align: right">{{ $var->description}} <span style="color: red;">Stock {{ $var->stock}}</span></td>
                                                     @else
                                                     <td style="text-align: right">{{ $var->description}}</td>
                                                     @endif

                                                @endif
                                                
                                                <td style="text-align: right">{{ $var->amount_quotation}}</td>
                                                <td style="text-align: right">{{number_format($var->price, 2, ',', '.')}}</td>
                                                <td style="text-align: right">{{number_format($var->discount, 0, '', '.')}}%</td>
                                                
                                                <td style="text-align: right">{{number_format($total_less_percentage, 2, ',', '.')}}</td>
                                               

                                                <?php
                                                    $suma += $total_less_percentage;
                                                    
                                                ?>
                                                    <td style="text-align: right">
                                                        <a href="{{ route('quotations.productedit',[$var->quotation_products_id,$coin]) }}" title="Editar"><i class="fa fa-edit"></i></a>  
                                                        <a href="#" class="delete" data-id={{$var->quotation_products_id}} data-description={{$var->description}} data-id-quotation={{$quotation->id}} data-coin={{$coin}} data-toggle="modal" data-target="#deleteModal" title="Eliminar"><i class="fa fa-trash text-danger"></i></a>  
                                                    </td>
                                            
                                                </tr>
                                            @endforeach

                                            <?php
                                                
                                            ?>
                                            <tr>
                                                <td style="text-align: right">-------------</td>
                                                <td style="text-align: right">-------------</td>
                                                <td style="text-align: right">-------------</td>
                                                <td style="text-align: right">-------------</td>
                                                <td style="text-align: right">Total</td>
                                                <td style="text-align: right">{{number_format($suma, 2, ',', '.')}}</td>
                                                
                                                <td style="text-align: right"></td>
                                            
                                                </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-group row mb-0">
                               
                                <div id="divDeliveryNote" class="col-sm-3">
                                    @if($suma == 0)
                                        <a onclick="validate()" id="btnSendNote" name="btnfacturar" class="btn btn-info" title="facturar">Nota de Entrega</a>  
                                    @else
                                        <a onclick="deliveryNoteSend()" id="btnSendNote" name="btnfacturar" class="btn btn-info" title="facturar"> Nota de Entrega</a>  
                                    @endif
                                </div>
                          
                                <div id="divFacturar" class="col-sm-3">
                                    @if($suma == 0)
                                        <a onclick="validate()" id="btnfacturar" name="btnfacturar" class="btn btn-success" title="facturar">Facturar</a>
                                        @if (empty($quotation->date_order))
                                            <a onclick="validate()" id="btnorder" name="btnorder" class="btn btn-danger" title="order">Pedido</a>  
                                        @endif  
                                        
                                    @else
                                        <a href="{{ route('quotations.createfacturar',[$quotation->id,$coin,'factura']) }}" id="btnfacturar" name="btnfacturar" class="btn btn-success" title="facturar">Facturar</a>  
                                        @if (empty($quotation->date_order))
                                            <a href="{{ route('orders.create_order',[$quotation->id,$coin]) }}" id="btnorder" name="btnorder" class="btn btn-danger" title="order">Pedido</a>  
                                        @endif
                                    @endif
                                </div>
                               
                                @if ($type != "Nota de Entrega" && $type != "factura") 
                                <div id="divFacturar" class="col-sm-2">
                                <a href="{{ route('quotations') }}" id="btnvolver" name="btnvolver" class="btn btn-info" title="volver">Cotizar</a>  
                                </div>
                                @endif
                            
                                <div id="divOpciones" class="col-sm-3 dropdown mb-4">
                                    <button class="btn btn-dark" type="button"
                                        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="false"
                                        aria-expanded="false">
                                        <i class="fas fa-bars"></i>
                                        Opciones 
                                    </button>
                                    <div class="dropdown-menu animated--fade-in" aria-labelledby="dropdownMenuButton">
                                        <a href="{{ route('pdf.quotation',[$quotation->id,$coin]) }}" class="dropdown-item bg-light text-black h5">Imprimir Cotización</a> 
                                        <a href="#" data-toggle="modal" data-target="#emailModal" class="dropdown-item bg-light text-black h5">Enviar Cotización por Correo</a> 
                                    </div> 
                                </div> 
                                <div class="col-sm-3">
                                    @if ($type == "Nota de Entrega")
                                    <a href="{{ route('quotations.indexdeliverynote') }}" id="btnvolver" name="btnvolver" class="btn btn-danger" title="volver">Volver a Notas de Entrega</a>  
                                    @endif
                                    @if ($type == "factura")
                                    <a href="{{ route('invoices') }}" id="btnvolver" name="btnvolver" class="btn btn-danger" title="volver">Volver a Faturas</a>  
                                    @endif
                                 </div>
                            </div>
                            
                </div>
            </div>
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
            <form action="{{ route('quotations.deleteProduct') }}" method="post">
                @csrf
                @method('DELETE')
                <input id="id_quotation_product_modal" type="hidden" class="form-control @error('id_quotation_product_modal') is-invalid @enderror" name="id_quotation_product_modal" readonly required autocomplete="id_quotation_product_modal">
                <input id="id_quotation_modal" type="hidden" class="form-control @error('id_quotation_modal') is-invalid @enderror" name="id_quotation_modal" readonly required autocomplete="id_quotation_modal">
                <input id="coin_modal" type="hidden" class="form-control @error('coin_modal') is-invalid @enderror" name="coin_modal" readonly required autocomplete="coin_modal">
                       
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

<div class="modal modal-danger fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Enviar Cotización por Correo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form action="{{ route('mails.quotation',[$quotation->id,$coin]) }}" method="post">
                @csrf
                @method('POST')
                <h5 class="text-center">Email:</h5>
                <input id="email_modal" type="text" class="form-control @error('email_modal') is-invalid @enderror" name="email_modal" value="{{ $quotation->clients['email'] ?? '' }}" required autocomplete="email_modal">
                <br>
                <h5 class="text-center">Mensaje:</h5>
                <input id="message_modal" type="text" class="form-control @error('message_modal') is-invalid @enderror" name="message_modal" value="{{ $company->message_from_email ?? '' }}" required autocomplete="message_modal">
                       
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Enviar Correo</button> 
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('quotation_create')
    
    <script>
        var type_quotation = "{{$type ?? null}}";

        if(type_quotation == 'factura'){
            $("#divDeliveryNote").hide();
            $("#btnorder").hide();
            $("#divOpciones").hide();
            document.getElementById("divFacturar").classList.add('offset-sm-1');

        }

        function searchClient(){
            var old_action = document.getElementById("formUpdate").action;
            document.getElementById("formUpdate").action = "{{ route('quotations.selectclientQuotation',$quotation->id) }}";
            document.getElementById("formUpdate").submit();
            document.getElementById("formUpdate").action = old_action;
        }


        $(document).ready(function () {
            $("#discount_product").mask('000', { reverse: true });
            
        });
        
        $(document).ready(function () {
            $("#amount_product").mask('00000', { reverse: true });
            
        });
        
        let sum = "<?php echo number_format($suma, 2, ',', '.') ?>";
      
        document.querySelector('#total').innerText = sum.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});;

        $(document).ready(function () {
            $("#total").mask('000.000.000.000.000,00', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#rate").mask('000.000.000.000.000,00', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#cost").mask('000.000.000.000.000,00', { reverse: true });
            
        });
        $("#code").keydown(function(event){ 
            if(event.which == 13){   // teclear enter
                /*sendProduct(callback);
                //Una funcion anonima para retornar el resultado despues de 1 segundo
                searchCode();*/
                    //alert(12); 
                 searchCode(); 
                //alert(13);             
            }
       });


        /*$("#description").on('change',function(){
          alert('change');
        });*/

        checkbox = document.getElementById('customSwitches'); // retoma el valor anterior
        checkbox.checked = eval(window.localStorage.getItem(checkbox.id));
        checkbox.addEventListener('change', function(){
            if($("#customSwitches").is(':checked')) {
                document.getElementById("id_scan_auto").innerHTML = "Agregar Automático Activado";
            } else {
                document.getElementById("id_scan_auto").innerHTML = "Agregar Automático Desactivado";
            }
            window.localStorage.setItem(checkbox.id, checkbox.checked);
        })


        if($("#customSwitches").is(':checked')) {
            document.getElementById("id_scan_auto").innerHTML = "Agregar Automático Activado";
        } else {
            document.getElementById("id_scan_auto").innerHTML = "Agregar Automático Desactivado";
        }
  


        if( $('#customSwitches').prop('checked')) { // validar seleccionado
            var value=$.trim($("#description").val()); // valida el campo si esta lleno
            if(value.length>0 ){
                //alert('enviando');
                sendProduct();
                $("#description").val('');
            }

          document.getElementById("code").focus();   
        }

        $(document).on('click','.delete',function(){
         let id = $(this).attr('data-id');
         let id_quotation = $(this).attr('data-id-quotation');
         let coin = $(this).attr('data-coin');
         let description = $(this).attr('data-description');

         $('#id_quotation_product_modal').val(id);
         $('#id_quotation_modal').val(id_quotation);
         $('#coin_modal').val(coin);
         $('#description_modal').val(description);
        });
    </script> 

@endsection         

@section('validacion')
    <script>
     $('#dataTable').dataTable( {
        "ordering": false,
        "searching": false,
        "paging": false,


        "order": [],
            'aLengthMenu': [[200, 300, 400, 500, -1],[200, 300, 400, 500, "All"]],

    } );

        $("#coin").on('change',function(){
            coin = $(this).val();
            window.location = "{{route('quotations.create', [$quotation->id,''])}}"+"/"+coin;
        });


      

    function sendProduct(){
        if(validacion()){
            document.getElementById("formSendProduct").submit();
        }
        
    }
    function deliveryNoteSend() {
       
            window.location = "{{route('quotations.createdeliverynote', [$quotation->id,$coin,'Nota de Entrega'])}}";
            
    }

    function refreshrate() {
       
        let rate = document.getElementById("rate").value; 
        window.location = "{{ route('quotations.refreshrate',[$quotation->id,$coin,'']) }}"+"/"+rate;
       
    }

    function validate() {
       
        alert('Debe ingresar al menos un producto para poder continuar');           
    }


    function validacion() {

        let amount = document.getElementById("amount_product").value; 

        if (amount < 1) {
        
        alert('La cantidad del Producto debe ser mayor a 1');
        return false;
        }
        else {
            return true;
        }  
    }


    function alertad() {
       
       alert('envia');
        //console.log("enviar");          
   }



    function searchCode2(callback){
            
            let reference_id = document.getElementById("code").value; 
            
            if(reference_id != ""){
                $.ajax({
                
                url:"{{ route('quotations.listinventory') }}" + '/' + reference_id,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                 
                    
                    if(response.length > 0){
                        response.forEach((item, index, object)=>{
                            let {id,description,date} = item;
                          
                           window.location = "{{route('quotations.createproduct', [$quotation->id,$coin,''])}}"+"/"+id;
                           
                        });
                    }else{
                        window.location = "{{route('quotations.create', [$quotation->id,$coin,''])}}";
                       //alert('No se Encontro este numero de Referencia');
                    }
                   
                },
                error:(xhr)=>{
                   //alert('Presentamos Inconvenientes');
                }
            })
            }
           //alert('busca');
            //console.log("buscar");

           callback();
        }  
    
    </script> 

@endsection    

@section('consulta')
    <script>

        function searchCode(){ 
            let reference_id = document.getElementById("code").value; 
            if(reference_id != ""){
                $.ajax({
                url:"{{ route('quotations.listinventory','') }}" + '/' + reference_id,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                    
                    if(response.length > 0){
                        response.forEach((item, index, object)=>{
                            let {id,description,date} = item;
                            
                           window.location = "{{route('quotations.createproduct', [$quotation->id,$coin,'',''])}}"+"/"+id+"/"+"{{$type ?? null}}";
                           
                        });
                    }else{

                          window.location = "{{route('quotations.create', [$quotation->id,$coin,''])}}"+"/{{$type ?? null}}";
                       //alert('No se Encontro este numero de Referencia');
                    }
                   
                },
                error:(xhr)=>{
                   //alert('Presentamos Inconvenientes');
                }
            })
            }
           
        }
        

    </script>
@endsection

