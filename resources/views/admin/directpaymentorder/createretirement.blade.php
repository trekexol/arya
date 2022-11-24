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
<div class="container">
    <div class="row justify-content-right">
        <div class="col-sm-1">
        </div>
        <div class="col-sm-10" style="text-align: right;">
            <a href="{{ route('bankmovements.indexorderpayment')}}" class="btn btn-info" title="Transferencia">Listar Orden de Pago</a>
        </div>
    </div>
    <br>
    <div class="row justify-content-center">
        
        <div class="col-md-12">
            <div class="card">
                
                <div class="card-header text-center font-weight-bold h3">Ordenes de Pago Directo</div>
                <div class="card-body" style="height: auto;">
                    <form id="formEnviar" method="POST" action="{{ route('directpaymentorders.store') }}" enctype="multipart/form-data">
                        @csrf
                       <input id="user_id" type="hidden" class="form-control @error('user_id') is-invalid @enderror" name="user_id" value="{{ Auth::user()->id }}" required autocomplete="user_id">
                       <input type="hidden" id="amount_of_payments" name="amount_of_payments"  readonly>

                        <div class="form-group row">
                            @if (isset($accounts))
                            <label for="account" class="col-md-2 col-form-label text-md-right">Retirar desde:</label>
                        
                            <div class="col-md-4">
                            <select id="account"  name="account" class="form-control" required>
                                <option value="">Seleccione una Cuenta</option>
                                @foreach($accounts as $index => $value)
                                    <option value="{{ $index }}" {{ old('account') == $index ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                                </select>

                                @if ($errors->has('account_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('account_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                            @endif
                            
                       
                            <label for="date_begin" class="col-md-3 col-form-label text-md-right">Fecha del Retiro:</label>

                            <div class="col-md-3">
                                <input id="date_begin" type="date" class="form-control @error('date_begin') is-invalid @enderror" name="date" value="{{ $datenow ?? old('date_begin') }}" required autocomplete="date_begin">

                                @error('date_begin')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                       
                       
                        <div class="form-group row">
                            
                            <label for="description" class="col-md-2 col-form-label text-md-right">Descripción</label>

                            <div class="col-md-4">
                                <input id="description" type="text" class="form-control @error('description') is-invalid @enderror" name="description" value="{{ old('description') }}"  autocomplete="description">

                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="reference" class="col-md-3 col-form-label text-md-right">Número de Referencia:</label>

                            <div class="col-md-3">
                                <input id="reference" type="text" class="form-control @error('reference') is-invalid @enderror" name="reference" value="{{ old('reference') }}"  autocomplete="reference">

                                @error('reference')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <br>
                        <div class="form-group row">
                            <label for="beneficiario" class="col-md-2 col-form-label text-md-right">Beneficiario:</label>
                        
                            <div class="col-md-4">
                            <select id="beneficiario"  name="beneficiario" class="form-control" required>
                                <option value="">Seleccione un Beneficiario</option>
                               
                                    <option value="1" {{ old('Beneficiario') == 'Cliente' ? 'selected' : '' }}>
                                        Clientes
                                    </option>
                                    <option value="2" {{ old('Beneficiario') == 'Proveedor' ? 'selected' : '' }}>
                                        Proveedores
                                    </option>
                                </select>

                                @if ($errors->has('beneficiario_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('beneficiario_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                       
                          <div class="col-md-4">
                                <select  id="subbeneficiario"  name="Subbeneficiario" class="form-control" required>
                                    <option value="">Seleccionar</option>
                                </select>

                                @if ($errors->has('subbeneficiario_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('subbeneficiario_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-sm-1">
                                <button id="agregar2" title="Agregar Bultos" type="button" onclick="addForm();" class=" btn btn-round btn-info"> + </button>
                            </div>
                        </div>  


          
            <div class="item clonar2">
                <div class="form-group">
                    
                 
                  <div class="form-group row">
                     
                      <label for="contrapartida" class="col-md-2 col-form-label text-md-right">Contrapartida:</label>
                      <div class="col-md-4">
                          <select id="type_form"  name="type_form" class="form-control" required>

                          <option value="-1">Seleccione una Contrapartida</option>
                          @foreach($contrapartidas as $index => $value)
                              
                              @if ($value != 'Bancos' && $value != 'Efectivo en Caja' && $value != 'Superavit o Deficit' && $value != 'Otros Ingresos' && $value != 'Resultado del Ejercicio' && $value != 'Resultados Anteriores')
                                  <option value="{{ $index }}" {{ old('type_form') == $index ? 'selected' : '' }}>
                                      {{ $value }}
                                  </option>
                              @endif


                          @endforeach
                          </select>

                      </div>
                      <div class="col-md-4">
                          <select  id="account_counterpart"  name="Account_counterpart" class="form-control" required>
                              <option value="">Seleccionar</option>
                              @if (isset($accounts_inventory))
                                  @foreach ($accounts_inventory as $var)
                                      <option value="{{ $var->id }}">{{ $var->description }}</option>
                                  @endforeach
                              @endif
                          </select>

                          @if ($errors->has('account'))
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $errors->first('account') }}</strong>
                              </span>
                          @endif
                      </div>
                    
                  </div>  
                  <div class="form-group row">
                      
                      <label for="amount" class="col-md-2 col-form-label text-md-right">Monto:</label>

                      <div class="col-md-4">
                          <input id="amount" type="text" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount') }}" required autocomplete="amount">

                          @error('amount')
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                      </div>
                      <label for="rate" class="col-md-2 col-form-label text-md-right">Tasa:</label>

                      <div class="col-md-2">
                          <input id="rate" type="text" class="form-control @error('rate') is-invalid @enderror" name="rate" value="{{ number_format($bcv, 2, ',', '.')}}"  autocomplete="rate">

                          @error('rate')
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                      </div>
                      <div class="col-sm-1">
                        <span class="badge badge-pill badge-danger puntero2 ocultar2" style="cursor: pointer" onclick="deleteForm();">Eliminar</span>
                      </div>
                  </div>
                
                </div>
              </div>
            
              
            <div id="form2" class="item clonar2">
                <div class="form-group">
                    
                 
                  <div class="form-group row">
                     
                      <label for="contrapartida" class="col-md-2 col-form-label text-md-right">Contrapartida:</label>
                      <div class="col-md-4">
                          <select id="type_form2"  name="type_form2" class="form-control" required>

                          <option value="-1">Seleccione una Contrapartida</option>
                          @foreach($contrapartidas as $index => $value)
                              
                              @if ($value != 'Bancos' && $value != 'Efectivo en Caja' && $value != 'Superavit o Deficit' && $value != 'Otros Ingresos' && $value != 'Resultado del Ejercicio' && $value != 'Resultados Anteriores')
                                  <option value="{{ $index }}" {{ old('type_form') == $index ? 'selected' : '' }}>
                                      {{ $value }}
                                  </option>
                              @endif


                          @endforeach
                          </select>

                      </div>
                      <div class="col-md-4">
                          <select  id="account_counterpart2"  name="Account_counterpart2" class="form-control" required>
                              <option value="">Seleccionar</option>
                              @if (isset($accounts_inventory))
                                  @foreach ($accounts_inventory as $var)
                                      <option value="{{ $var->id }}">{{ $var->description }}</option>
                                  @endforeach
                              @endif
                          </select>

                          @if ($errors->has('account'))
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $errors->first('account') }}</strong>
                              </span>
                          @endif
                      </div>
                    
                  </div>  
                  <div class="form-group row">
                      
                      <label for="amount2" class="col-md-2 col-form-label text-md-right">Monto del Retiro:</label>

                      <div class="col-md-4">
                          <input id="amount2" type="text" class="form-control @error('amount2') is-invalid @enderror" name="amount2" value="{{ old('amount2') }}" required autocomplete="amount2">

                          @error('amount2')
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                      </div>
                      <label for="rate2" class="col-md-2 col-form-label text-md-right">Tasa:</label>

                      <div class="col-md-2">
                          <input id="rate2" type="text" class="form-control @error('rate2') is-invalid @enderror" name="rate2" value="{{ number_format($bcv, 2, ',', '.')}}"  autocomplete="rate2">

                          @error('rate2')
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                      </div>
                      <div class="col-sm-1">
                        <span class="badge badge-pill badge-danger puntero2 ocultar2" style="cursor: pointer" onclick="deleteForm();">Eliminar</span>
                      </div>
                  </div>
                 
                </div>
              </div>



              <div id="form3" class="item clonar2">
                <div class="form-group">
                    
                 
                  <div class="form-group row">
                     
                      <label for="contrapartida" class="col-md-2 col-form-label text-md-right">Contrapartida:</label>
                      <div class="col-md-4">
                          <select id="type_form3"  name="type_form3" class="form-control" required>

                          <option value="-1">Seleccione una Contrapartida</option>
                          @foreach($contrapartidas as $index => $value)
                              
                              @if ($value != 'Bancos' && $value != 'Efectivo en Caja' && $value != 'Superavit o Deficit' && $value != 'Otros Ingresos' && $value != 'Resultado del Ejercicio' && $value != 'Resultados Anteriores')
                                  <option value="{{ $index }}" {{ old('type_form') == $index ? 'selected' : '' }}>
                                      {{ $value }}
                                  </option>
                              @endif


                          @endforeach
                          </select>

                      </div>
                      <div class="col-md-4">
                          <select  id="account_counterpart3"  name="Account_counterpart3" class="form-control" required>
                              <option value="">Seleccionar</option>
                              @if (isset($accounts_inventory))
                                  @foreach ($accounts_inventory as $var)
                                      <option value="{{ $var->id }}">{{ $var->description }}</option>
                                  @endforeach
                              @endif
                          </select>

                          @if ($errors->has('account'))
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $errors->first('account') }}</strong>
                              </span>
                          @endif
                      </div>
                    
                  </div>  
                  <div class="form-group row">
                      
                      <label for="amount3" class="col-md-2 col-form-label text-md-right">Monto del Retiro:</label>

                      <div class="col-md-4">
                          <input id="amount3" type="text" class="form-control @error('amount3') is-invalid @enderror" name="amount3" value="{{ old('amount3') }}" required autocomplete="amount3">

                          @error('amount3')
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                      </div>
                      <label for="rate3" class="col-md-2 col-form-label text-md-right">Tasa:</label>

                      <div class="col-md-2">
                          <input id="rate3" type="text" class="form-control @error('rate3') is-invalid @enderror" name="rate3" value="{{ number_format($bcv, 2, ',', '.')}}"  autocomplete="rate3">

                          @error('rate3')
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                      </div>
                      <div class="col-sm-1">
                        <a href="#" class="badge badge-pill badge-danger puntero2 ocultar2" style="cursor: pointer" onclick="deleteForm();">Eliminar</a>
                      </div>
                  </div>
                 
                </div>
              </div>

              <div id="form4" class="item clonar2">
                <div class="form-group">
                    
                 
                  <div class="form-group row">
                     
                      <label for="contrapartida" class="col-md-2 col-form-label text-md-right">Contrapartida:</label>
                      <div class="col-md-4">
                          <select id="type_form4"  name="type_form4" class="form-control" required>

                          <option value="-1">Seleccione una Contrapartida</option>
                          @foreach($contrapartidas as $index => $value)
                              
                              @if ($value != 'Bancos' && $value != 'Efectivo en Caja' && $value != 'Superavit o Deficit' && $value != 'Otros Ingresos' && $value != 'Resultado del Ejercicio' && $value != 'Resultados Anteriores')
                                  <option value="{{ $index }}" {{ old('type_form') == $index ? 'selected' : '' }}>
                                      {{ $value }}
                                  </option>
                              @endif


                          @endforeach
                          </select>

                      </div>
                      <div class="col-md-4">
                          <select  id="account_counterpart4"  name="Account_counterpart4" class="form-control" required>
                              <option value="">Seleccionar</option>
                              @if (isset($accounts_inventory))
                                  @foreach ($accounts_inventory as $var)
                                      <option value="{{ $var->id }}">{{ $var->description }}</option>
                                  @endforeach
                              @endif
                          </select>

                          @if ($errors->has('account'))
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $errors->first('account') }}</strong>
                              </span>
                          @endif
                      </div>
                    
                  </div>  
                  <div class="form-group row">
                      
                      <label for="amount" class="col-md-2 col-form-label text-md-right">Monto del Retiro:</label>

                      <div class="col-md-4">
                          <input id="amount4" type="text" class="form-control @error('amount4') is-invalid @enderror" name="amount4" value="{{ old('amount4') }}" required autocomplete="amount4">

                          @error('amount4')
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                      </div>
                      <label for="rate4" class="col-md-2 col-form-label text-md-right">Tasa:</label>

                      <div class="col-md-2">
                          <input id="rate4" type="text" class="form-control @error('rate4') is-invalid @enderror" name="rate4" value="{{ number_format($bcv, 2, ',', '.')}}"  autocomplete="rate4">

                          @error('rate4')
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                      </div>
                      <div class="col-sm-1">
                        <span class="badge badge-pill badge-danger puntero2 ocultar2" style="cursor: pointer" onclick="deleteForm();">Eliminar</span>
                      </div>
                  </div>
                 
                </div>
              </div>


             
              <div id="form5" class="item clonar2">
                <div class="form-group">
                    
                 
                  <div class="form-group row">
                     
                      <label for="contrapartida" class="col-md-2 col-form-label text-md-right">Contrapartida:</label>
                      <div class="col-md-4">
                          <select id="type_form5"  name="type_form5" class="form-control" required>

                          <option value="-1">Seleccione una Contrapartida</option>
                          @foreach($contrapartidas as $index => $value)
                              
                              @if ($value != 'Bancos' && $value != 'Efectivo en Caja' && $value != 'Superavit o Deficit' && $value != 'Otros Ingresos' && $value != 'Resultado del Ejercicio' && $value != 'Resultados Anteriores')
                                  <option value="{{ $index }}" {{ old('type_form') == $index ? 'selected' : '' }}>
                                      {{ $value }}
                                  </option>
                              @endif


                          @endforeach
                          </select>

                      </div>
                      <div class="col-md-4">
                          <select  id="account_counterpart5"  name="Account_counterpart5" class="form-control" required>
                              <option value="">Seleccionar</option>
                              @if (isset($accounts_inventory))
                                  @foreach ($accounts_inventory as $var)
                                      <option value="{{ $var->id }}">{{ $var->description }}</option>
                                  @endforeach
                              @endif
                          </select>

                          @if ($errors->has('account'))
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $errors->first('account') }}</strong>
                              </span>
                          @endif
                      </div>
                    
                  </div>  
                  <div class="form-group row">
                      
                      <label for="amount5" class="col-md-2 col-form-label text-md-right">Monto del Retiro:</label>

                      <div class="col-md-4">
                          <input id="amount5" type="text" class="form-control @error('amount5') is-invalid @enderror" name="amount5" value="{{ old('amount5') }}" required autocomplete="amount5">

                          @error('amount5')
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                      </div>
                      <label for="rate5" class="col-md-2 col-form-label text-md-right">Tasa:</label>

                      <div class="col-md-2">
                          <input id="rate5" type="text" class="form-control @error('rate5') is-invalid @enderror" name="rate5" value="{{ number_format($bcv, 2, ',', '.')}}"  autocomplete="rate5">

                          @error('rate5')
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                      </div>
                      <div class="col-sm-1">
                        <a href="#" class="badge badge-pill badge-danger puntero2 ocultar2" style="cursor: pointer" onclick="deleteForm();">Eliminar</a>
                      </div>
                  </div>
                 
                </div>
              </div>


              <div id="form6" class="item clonar2">
                <div class="form-group">
                    
                 
                  <div class="form-group row">
                     
                      <label for="contrapartida" class="col-md-2 col-form-label text-md-right">Contrapartida:</label>
                      <div class="col-md-4">
                          <select id="type_form6"  name="type_form6" class="form-control" required>

                          <option value="-1">Seleccione una Contrapartida</option>
                          @foreach($contrapartidas as $index => $value)
                              
                              @if ($value != 'Bancos' && $value != 'Efectivo en Caja' && $value != 'Superavit o Deficit' && $value != 'Otros Ingresos' && $value != 'Resultado del Ejercicio' && $value != 'Resultados Anteriores')
                                  <option value="{{ $index }}" {{ old('type_form') == $index ? 'selected' : '' }}>
                                      {{ $value }}
                                  </option>
                              @endif


                          @endforeach
                          </select>

                      </div>
                      <div class="col-md-4">
                          <select  id="account_counterpart6"  name="Account_counterpart6" class="form-control" required>
                              <option value="">Seleccionar</option>
                              @if (isset($accounts_inventory))
                                  @foreach ($accounts_inventory as $var)
                                      <option value="{{ $var->id }}">{{ $var->description }}</option>
                                  @endforeach
                              @endif
                          </select>

                          @if ($errors->has('account'))
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $errors->first('account') }}</strong>
                              </span>
                          @endif
                      </div>
                    
                  </div>  
                  <div class="form-group row">
                      
                      <label for="amount6 class="col-md-2 col-form-label text-md-right">Monto del Retiro:</label>

                      <div class="col-md-4">
                          <input id="amount6" type="text" class="form-control @error('amount6') is-invalid @enderror" name="amount6" value="{{ old('amount6') }}" required autocomplete="amount6">

                          @error('amount6')
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                      </div>
                      <label for="rate6" class="col-md-2 col-form-label text-md-right">Tasa:</label>

                      <div class="col-md-2">
                          <input id="rate6" type="text" class="form-control @error('rate6') is-invalid @enderror" name="rate6" value="{{ number_format($bcv, 2, ',', '.')}}"  autocomplete="rate6">

                          @error('rate6')
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                      </div>
                      <div class="col-sm-1">
                        <a href="#" class="badge badge-pill badge-danger puntero2 ocultar2" style="cursor: pointer" onclick="deleteForm();">Eliminar</a>
                      </div>
                  </div>
                 
                </div>
              </div>


              <div id="form7" class="item clonar2">
                <div class="form-group">
                    
                 
                  <div class="form-group row">
                     
                      <label for="contrapartida" class="col-md-2 col-form-label text-md-right">Contrapartida:</label>
                      <div class="col-md-4">
                          <select id="type_form7"  name="type_form7" class="form-control" required>

                          <option value="-1">Seleccione una Contrapartida</option>
                          @foreach($contrapartidas as $index => $value)
                              
                              @if ($value != 'Bancos' && $value != 'Efectivo en Caja' && $value != 'Superavit o Deficit' && $value != 'Otros Ingresos' && $value != 'Resultado del Ejercicio' && $value != 'Resultados Anteriores')
                                  <option value="{{ $index }}" {{ old('type_form') == $index ? 'selected' : '' }}>
                                      {{ $value }}
                                  </option>
                              @endif


                          @endforeach
                          </select>

                      </div>
                      <div class="col-md-4">
                          <select  id="account_counterpart7"  name="Account_counterpart7" class="form-control" required>
                              <option value="">Seleccionar</option>
                              @if (isset($accounts_inventory))
                                  @foreach ($accounts_inventory as $var)
                                      <option value="{{ $var->id }}">{{ $var->description }}</option>
                                  @endforeach
                              @endif
                          </select>

                          @if ($errors->has('account'))
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $errors->first('account') }}</strong>
                              </span>
                          @endif
                      </div>
                    
                  </div>  
                  <div class="form-group row">
                      
                      <label for="amount" class="col-md-2 col-form-label text-md-right">Monto del Retiro:</label>

                      <div class="col-md-4">
                          <input id="amount7" type="text" class="form-control @error('amount') is-invalid @enderror" name="amount7" value="{{ old('amount') }}" required autocomplete="amount7">

                          @error('amount7')
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                      </div>
                      <label for="rate7" class="col-md-2 col-form-label text-md-right">Tasa:</label>

                      <div class="col-md-2">
                          <input id="rate7" type="text" class="form-control @error('rate7') is-invalid @enderror" name="rate7" value="{{ number_format($bcv, 2, ',', '.')}}"  autocomplete="rate7">

                          @error('rate7')
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                      </div>
                      <div class="col-sm-1">
                        <a href="#" class="badge badge-pill badge-danger puntero2 ocultar2" style="cursor: pointer" onclick="deleteForm();">Eliminar</a>
                      </div>
                  </div>
                 
                </div>
              </div>
                        <div class="form-group row">
                            <label id="coinlabel" for="coin" class="col-md-2 col-form-label text-md-right">Moneda:</label>

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
                            @if (isset($branches))
                            <label for="branch" class="col-md-2 offset-md-2 col-form-label text-md-right">Centro de Costo:</label>
                            <div class="col-md-2">
                                <select id="branch"  name="branch" class="form-control" >
                                    <option value="ninguno">Ninguno</option>
                                    @foreach($branches as $var)
                                        <option value="{{ $var->id }}">{{ $var->description}}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                        </div>
                       
                        <br>
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary" onclick="enviar();">
                                   Guardar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
@section('javascript')
    <script>

        $("#form2").hide();
        $("#form3").hide();
        $("#form4").hide();
        $("#form5").hide();
        $("#form6").hide();
        $("#form7").hide();
        
    //AGREGAREMOS OTRO FORMULARIO DE PAGO
    
    function enviar(){
        document.getElementById("formEnviar").submit();
    }


    var number_form = 1; 
    document.getElementById("amount_of_payments").value = number_form;

    //AGREGAR FORMULARIOS
    function addForm() {
   
        if(number_form < 7){
            number_form += 1; 
        }
        if(number_form == 2){
            $('#form2').show()
            document.getElementById("form2").value = "";
        }
        if(number_form == 3){
            $('#form3').show()
            document.getElementById("form3").value = "";
        }
        if(number_form == 4){
            $('#form4').show()
            document.getElementById("form4").value = "";
        }
        if(number_form == 5){
            $('#form5').show()
            document.getElementById("form5").value = "";
        }
        if(number_form == 6){
            $('#form6').show()
            document.getElementById("form6").value = "";
        }
        if(number_form == 7){
            $('#form7').show()
            document.getElementById("form7").value = "";
        }
            
        document.getElementById("amount_of_payments").value = number_form;
    
    }


    //AGREGAR formS
    function deleteForm() {
        if(number_form <= 7 && number_form >=1){
            number_form -= 1; 
        }
        if(number_form == 1){
            $('#form2').hide()
            document.getElementById("form2").value = "";
        }
        if(number_form == 2){
            $('#form3').hide()
            document.getElementById("form3").value = "";
            
        }if(number_form == 3){
            $('#form4').hide()
            document.getElementById("form4").value = "";
            
        }if(number_form == 4){
            $('#form5').hide()
            document.getElementById("form5").value = "";
            
        }if(number_form == 5){
            $('#form6').hide()
            document.getElementById("form6").value = "";
            
        }if(number_form == 6){
            $('#form7').hide()
            document.getElementById("form7").value = "";
            
        }
        document.getElementById("amount_of_payments").value = number_form;
    
    }






        $(document).ready(function () {
            $("#amount").mask('000.000.000.000.000.000.000,00', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#rate").mask('000.000.000.000.000.000.000,00', { reverse: true });
            
        });

        $(document).ready(function () {
            $("#amount2").mask('000.000.000.000.000.000.000,00', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#rate2").mask('000.000.000.000.000.000.000,00', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#amount3").mask('000.000.000.000.000.000.000,00', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#rate3").mask('000.000.000.000.000.000.000,00', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#amount4").mask('000.000.000.000.000.000.000,00', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#rate4").mask('000.000.000.000.000.000.000,00', { reverse: true });
            
        });


        $(document).ready(function () {
            $("#amount5").mask('000.000.000.000.000.000.000,00', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#rate5").mask('000.000.000.000.000.000.000,00', { reverse: true });
            
        });

        $(document).ready(function () {
            $("#amount6").mask('000.000.000.000.000.000.000,00', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#rate6").mask('000.000.000.000.000.000.000,00', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#amount7").mask('000.000.000.000.000.000.000,00', { reverse: true });
            
        });
        $(document).ready(function () {
            $("#rate7").mask('000.000.000.000.000.000.000,00', { reverse: true });
            
        });
        $("#coin").on('change',function(){
            var coin = $(this).val();

            var amount = document.getElementById("amount").value;
            var montoFormat = amount.replace(/[$.]/g,'');
            var amountFormat = montoFormat.replace(/[,]/g,'.');

            var rate = document.getElementById("rate").value;
            var rateFormat = rate.replace(/[$.]/g,'');
            var rateFormat = rateFormat.replace(/[,]/g,'.');

            if(coin != 'bolivares'){

                var total = amountFormat / rateFormat;

                document.getElementById("amount").value = total.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});;
            }else{
                var total = amountFormat * rateFormat;

                document.getElementById("amount").value = total.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});;
           
            }
        });


    
    </script> 

@endsection     

@section('javascript2')
<script>
        
        $("#beneficiario").on('change',function(){
           
            var beneficiario_id = $(this).val();
            $("#subbeneficiario").val("");
           
            // alert(beneficiario_id);
            getSubbeneficiario(beneficiario_id);
        });

    function getSubbeneficiario(beneficiario_id){
       
       
        $.ajax({
            url:"{{ route('directpaymentorders.listbeneficiary') }}" + '/' + beneficiario_id,
           
            beforSend:()=>{
                alert('consultando datos');
            },
            success:(response)=>{
                let subbeneficiario = $("#subbeneficiario");
                let htmlOptions = `<option value='' >Seleccione..</option>`;
                // console.clear();

                if(response.length > 0){
                    if(beneficiario_id == "1"){
                        response.forEach((item, index, object)=>{
                            let {id,name} = item;
                            htmlOptions += `<option value='${id}' {{ old('Subbeneficiario') == '${id}' ? 'selected' : '' }}>${name}</option>`

                        });
                    }else{
                        response.forEach((item, index, object)=>{
                            let {id,razon_social} = item;
                            htmlOptions += `<option value='${id}' {{ old('Subbeneficiario') == '${id}' ? 'selected' : '' }}>${razon_social}</option>`

                        });
                    }
                }
                //console.clear();
                // console.log(htmlOptions);
                subbeneficiario.html('');
                subbeneficiario.html(htmlOptions);
            
                
            
            },
            error:(xhr)=>{
                alert('Presentamos inconvenientes al consultar los datos');
            }
        })
    }

    $("#subbeneficiario").on('change',function(){
            var subbeneficiario_id = $(this).val();
            var beneficiario_id    = document.getElementById("beneficiario").value;
            
        });


</script>
@endsection

@section('consultadeposito')
    <script>
       /*  $(".type_form").on('change',function(){
          
            type_var = $(this).val();
           
            searchCode(type_var);
        });
        function searchCode(type_var){

        
            $.ajax({
                
                url:"{{ route('expensesandpurchases.listaccount') }}"+'/'+type_var,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                
                    let account = $("#account_counterpart");
                    let htmlOptions = `<option value='' >Seleccione..</option>`;
                    // console.clear();
                    if(response.length > 0){
                        
                        response.forEach((item, index, object)=>{
                            let {id,description} = item;
                            htmlOptions += `<option value='${id}' {{ old('Account') == '${id}' ? 'selected' : '' }}>${description}</option>`

                        });
                    }
                    account.html('');
                    account.html(htmlOptions);
                
                    
                
                },
                error:(xhr)=>{
                }
            })
        }*/

        $("#type_form").on('change',function(){
           
            var contrapartida_id = $(this).val();
            $("#account_counterpart").val("");
            
            getSubcontrapartida(contrapartida_id);
        });

        function getSubcontrapartida(contrapartida_id){
            
            $.ajax({
                url:"{{ route('directpaymentorders.listcontrapartida') }}" + '/' + contrapartida_id,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                    let subcontrapartida = $("#account_counterpart");
                    let htmlOptions = `<option value='' >Seleccione..</option>`;
                    // console.clear();
                    if(response.length > 0){
                        response.forEach((item, index, object)=>{
                            let {id,description} = item;
                            htmlOptions += `<option value='${id}' {{ old('account_counterpart') == '${id}' ? 'selected' : '' }}>${description}</option>`

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

        $("#account_counterpart").on('change',function(){
            var subcontrapartida_id = $(this).val();
            var contrapartida_id    = document.getElementById("type_form").value;
            
        });


        $("#type_form2").on('change',function(){
            var contrapartida_id = $(this).val();
            $("#account_counterpart2").val("");
            
            getSubcontrapartida2(contrapartida_id);
        });

        function getSubcontrapartida2(contrapartida_id){
            
            $.ajax({
                url:"{{ route('directpaymentorders.listcontrapartida') }}" + '/' + contrapartida_id,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                    let subcontrapartida = $("#account_counterpart2");
                    let htmlOptions = `<option value='' >Seleccione..</option>`;
                    // console.clear();
                    if(response.length > 0){
                        response.forEach((item, index, object)=>{
                            let {id,description} = item;
                            htmlOptions += `<option value='${id}' {{ old('account_counterpart') == '${id}' ? 'selected' : '' }}>${description}</option>`

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


        
        $("#type_form3").on('change',function(){
            var contrapartida_id = $(this).val();
            $("#account_counterpart3").val("");
            
            getSubcontrapartida3(contrapartida_id);
        });

        function getSubcontrapartida3(contrapartida_id){
            
            $.ajax({
                url:"{{ route('directpaymentorders.listcontrapartida') }}" + '/' + contrapartida_id,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                    let subcontrapartida = $("#account_counterpart3");
                    let htmlOptions = `<option value='' >Seleccione..</option>`;
                    // console.clear();
                    if(response.length > 0){
                        response.forEach((item, index, object)=>{
                            let {id,description} = item;
                            htmlOptions += `<option value='${id}' {{ old('account_counterpart') == '${id}' ? 'selected' : '' }}>${description}</option>`

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

        

        $("#type_form4").on('change',function(){
            var contrapartida_id = $(this).val();
            $("#account_counterpart4").val("");
            
            getSubcontrapartida4(contrapartida_id);
        });

        function getSubcontrapartida4(contrapartida_id){
            
            $.ajax({
                url:"{{ route('directpaymentorders.listcontrapartida') }}" + '/' + contrapartida_id,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                    let subcontrapartida = $("#account_counterpart4");
                    let htmlOptions = `<option value='' >Seleccione..</option>`;
                    // console.clear();
                    if(response.length > 0){
                        response.forEach((item, index, object)=>{
                            let {id,description} = item;
                            htmlOptions += `<option value='${id}' {{ old('account_counterpart') == '${id}' ? 'selected' : '' }}>${description}</option>`

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


        $("#type_form5").on('change',function(){
            var contrapartida_id = $(this).val();
            $("#account_counterpart5").val("");
            
            getSubcontrapartida5(contrapartida_id);
        });

        function getSubcontrapartida5(contrapartida_id){
            
            $.ajax({
                url:"{{ route('directpaymentorders.listcontrapartida') }}" + '/' + contrapartida_id,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                    let subcontrapartida = $("#account_counterpart5");
                    let htmlOptions = `<option value='' >Seleccione..</option>`;
                    // console.clear();
                    if(response.length > 0){
                        response.forEach((item, index, object)=>{
                            let {id,description} = item;
                            htmlOptions += `<option value='${id}' {{ old('account_counterpart') == '${id}' ? 'selected' : '' }}>${description}</option>`

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

        $("#type_form6").on('change',function(){
            var contrapartida_id = $(this).val();
            $("#account_counterpart6").val("");
            
            getSubcontrapartida6(contrapartida_id);
        });

        function getSubcontrapartida6(contrapartida_id){
            
            $.ajax({
                url:"{{ route('directpaymentorders.listcontrapartida') }}" + '/' + contrapartida_id,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                    let subcontrapartida = $("#account_counterpart6");
                    let htmlOptions = `<option value='' >Seleccione..</option>`;
                    // console.clear();
                    if(response.length > 0){
                        response.forEach((item, index, object)=>{
                            let {id,description} = item;
                            htmlOptions += `<option value='${id}' {{ old('account_counterpart') == '${id}' ? 'selected' : '' }}>${description}</option>`

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

        $("#type_form7").on('change',function(){
            var contrapartida_id = $(this).val();
            $("#account_counterpart7").val("");
            
            getSubcontrapartida7(contrapartida_id);
        });

        function getSubcontrapartida7(contrapartida_id){
            
            $.ajax({
                url:"{{ route('directpaymentorders.listcontrapartida') }}" + '/' + contrapartida_id,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                    let subcontrapartida = $("#account_counterpart7");
                    let htmlOptions = `<option value='' >Seleccione..</option>`;
                    // console.clear();
                    if(response.length > 0){
                        response.forEach((item, index, object)=>{
                            let {id,description} = item;
                            htmlOptions += `<option value='${id}' {{ old('account_counterpart') == '${id}' ? 'selected' : '' }}>${description}</option>`

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

	$(function(){
        soloNumeros('xtelf_local');
        soloNumeros('xtelf_cel');
    });
    
 



    </script>
@endsection


