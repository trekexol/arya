@extends('admin.layouts.dashboard')

@section('content')


<!-- container-fluid -->
<div class="container-fluid">
    <div class="row py-lg-2">
        <form method="POST" action="{{ route('accounting_adjustments.store') }}">
            @csrf
            <input type="hidden" name="coin" value="{{$coin}}" readonly>
            <div class="card-body">
                <div class="form-group row">
                    <label for="date_end" class="col-sm-1 col-form-label text-md-right">Desde</label>

                    <div class="col-sm-3">
                        <input id="date_begin" type="date" class="form-control @error('date_begin') is-invalid @enderror" name="date_begin" value="{{  $date_begin ?? $datenow ?? '' }}" required autocomplete="date_begin">

                        @error('date_begin')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <label for="date_end" class="col-sm-1 col-form-label text-md-right">hasta </label>

                    <div class="col-sm-3">
                        <input id="date_begin" type="date" class="form-control @error('date_end') is-invalid @enderror" name="date_end" value="{{ $date_end ?? $datenow ?? '' }}" required autocomplete="date_end">

                        @error('date_end')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group col-sm-2">
                        <button type="submit" class="btn btn-info" title="Buscar">Buscar</button>  
                    </div>
                    <div class="form-group col-sm-2">
                        <div class="custom-control custom-switch">
                            @if (isset($ver_ajuste) && ($ver_ajuste == "on"))
                                <input type="checkbox" class="custom-control-input" name="switch" checked id="customSwitches">
                            @else
                                <input type="checkbox" class="custom-control-input" name="switch" id="customSwitches">
                            @endif
                            <label class="custom-control-label" for="customSwitches">Ver Ajustes</label>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- Page Heading -->
</div>
  {{-- VALIDACIONES-RESPUESTA--}}
@include('admin.layouts.success')   {{-- SAVE --}}
@include('admin.layouts.danger')    {{-- EDITAR --}}
@include('admin.layouts.delete')    {{-- DELELTE --}}
{{-- VALIDACIONES-RESPUESTA --}}
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Cuenta</th>
                    <th>Tipo de Movimiento</th>
                    <th>Comprobante</th>
                    <th>Descripción</th>
                    <th>Debe</th>
                    <th>Haber</th>

                    <th class="text-center"></th>
                </tr>
                </thead>
                
                <tbody>

                    @if (empty($detailvouchers))
                    @else
                        @foreach ($detailvouchers as $var)
                        <tr>
                        <td>{{$var->date ?? ''}}</td>
                        
                        <td>{{$var->account_description ?? ''}}</td>
                        
                        @if(isset($var->id_invoice))
                            <td class="text-center">{{ $var->id_invoice }}</td>
   
                        @elseif(isset($var->id_expense))
                            <td class="text-center">Compra ({{ $var->id_expense }})</td>

                        @elseif(isset($var->id_header_voucher)) 
                            <td class="text-center">Otro</td>

                        @endif


                            <td>
                            <a href="{{ route('detailvouchers.create',[$coin,$var->id_header_voucher]) }}" title="Crear">{{ $var->id_header_voucher }}</a>
                            </td>       
                                       
        
                         <td>{{$var->description ?? ''}}</td>

                        @if(isset($var->accounts['coin']))
                            @if(($var->debe != 0) && ($var->tasa))
                                <td class="text-right font-weight-bold">{{number_format($var->debe, 2, ',', '.')}}<br>{{number_format($var->debe/$var->tasa, 2, ',', '.')}}{{ $var->accounts['coin'] }}</td>
                            @else
                                <td class="text-right font-weight-bold">{{number_format($var->debe, 2, ',', '.')}}</td>
                            @endif
                            @if($var->haber != 0 && ($var->tasa))
                                <td class="text-right font-weight-bold">{{number_format($var->haber, 2, ',', '.')}}<br>{{number_format($var->haber/$var->tasa, 2, ',', '.')}}{{ $var->accounts['coin'] }}</td>
                            @else
                                <td class="text-right font-weight-bold">{{number_format($var->haber, 2, ',', '.')}}</td>
                            @endif
                        @else

                                <td class="text-right font-weight-bold">{{number_format($var->debe, 2, ',', '.')}}<br>${{number_format($var->debe/($var->tasa ?? 1), 2, ',', '.')}}</td>
                                <td class="text-right font-weight-bold">{{number_format($var->haber, 2, ',', '.')}}<br>${{number_format($var->haber/($var->tasa ?? 1), 2, ',', '.')}}</td>
                            
                        @endif
                        <td>    
                            <a href="{{ route('detailvouchers.create',[$coin,$var->id_header_voucher]) }}" title="Editar"><i class="fa fa-edit"></i></a>
                        </td>
                        </tr>
                        @endforeach
                    @endif

                </tbody>
            </table>
        </div>
    </div>
</div>



<div class="modal fade" id="libroMayorCuentasModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Seleccione el periodo</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('daily_listing.print_diary_book_detail') }}"   target="print_popup" onsubmit="window.open('about:blank','print_popup','width=1000,height=800');">
                @csrf
            <div class="modal-body">
                <div class="form-group row">
                    <label for="account" class="col-md-2 col-form-label text-md-right">Cuenta:</label>
                        <div class="col-md-8">
                            <select class="form-control" id="id_account" name="id_account" required>
                                <option value="">Selecciona una Cuenta</option>
                                @foreach($accounts as $var)
                                    <option value="{{ $var->id }}">{{ $var->description }}</option>
                                @endforeach
                              
                            </select>
                        </div>
                </div>
                <div class="form-group row">
                    <label id="coinlabel" for="coin" class="col-md-2 col-form-label text-md-right">Moneda:</label>
                    <div class="col-md-6">
                        <select class="form-control" name="coin" id="coin">
                            <option selected value="bolivares">Bolívares</option>
                            <option value="dolares">Dolares</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="date_end" class="col-sm-2 col-form-label text-md-right">Desde</label>
    
                    <div class="col-sm-6">
                        <input id="date_begin" type="date" class="form-control @error('date_begin') is-invalid @enderror" name="date_begin" value="{{  $date_begin ?? $datenow ?? '' }}" required autocomplete="date_begin">
    
                        @error('date_begin')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label for="date_end" class="col-sm-2 col-form-label text-md-right">hasta </label>
    
                    <div class="col-sm-6">
                        <input id="date_begin" type="date" class="form-control @error('date_end') is-invalid @enderror" name="date_end" value="{{ $date_end ?? $datenow ?? '' }}" required autocomplete="date_end">
    
                        @error('date_end')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
                <div class="modal-footer">
                    <div class="form-group col-md-2">
                        <button type="submit" class="btn btn-info" title="Buscar">Enviar</button>  
                    </div>
            </form>
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
<div class="modal fade" id="libroModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Seleccione el periodo</h5>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
        <form method="POST" action="{{ route('daily_listing.print_journalbook') }}"   target="print_popup" onsubmit="window.open('about:blank','print_popup','width=1000,height=800');">
            @csrf
        <div class="modal-body">
            <div class="form-group row">
                <label for="date_end" class="col-sm-2 col-form-label text-md-right">Desde</label>

                <div class="col-sm-6">
                    <input id="date_begin" type="date" class="form-control @error('date_begin') is-invalid @enderror" name="date_begin" value="{{  $date_begin ?? $datenow ?? '' }}" required autocomplete="date_begin">

                    @error('date_begin')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="form-group row">
                <label for="date_end" class="col-sm-2 col-form-label text-md-right">hasta </label>

                <div class="col-sm-6">
                    <input id="date_begin" type="date" class="form-control @error('date_end') is-invalid @enderror" name="date_end" value="{{ $date_end ?? $datenow ?? '' }}" required autocomplete="date_end">

                    @error('date_end')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
        </div>
            <div class="modal-footer">
                <div class="form-group col-md-2">
                    <button type="submit" class="btn btn-info" title="Buscar">Enviar</button>  
                </div>
        </form>
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="libroModalAccount" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Seleccione el periodo</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('daily_listing.print_journalbook') }}"   target="print_popup" onsubmit="window.open('about:blank','print_popup','width=1000,height=800');">
                @csrf
            <div class="modal-body">
                <div class="form-group row">
                    <label for="account" class="col-md-2 col-form-label text-md-right">Cuenta:</label>
                        <div class="col-md-8">
                            <select class="form-control" id="id_account" name="id_account" required>
                                <option value="">Selecciona una Cuenta</option>
                                @foreach($accounts as $var)
                                    <option value="{{ $var->id }}">{{ $var->description }}</option>
                                @endforeach
                              
                            </select>
                        </div>
                </div>
                <div class="form-group row">
                    <label id="coinlabel" for="coin" class="col-md-2 col-form-label text-md-right">Moneda:</label>
                    <div class="col-md-6">
                        <select class="form-control" name="coin" id="coin">
                            <option selected value="bolivares">Bolívares</option>
                            <option value="dolares">Dolares</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="date_end" class="col-sm-2 col-form-label text-md-right">Desde</label>
    
                    <div class="col-sm-6">
                        <input id="date_begin" type="date" class="form-control @error('date_begin') is-invalid @enderror" name="date_begin" value="{{  $date_begin ?? $datenow ?? '' }}" required autocomplete="date_begin">
    
                        @error('date_begin')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label for="date_end" class="col-sm-2 col-form-label text-md-right">hasta </label>
    
                    <div class="col-sm-6">
                        <input id="date_begin" type="date" class="form-control @error('date_end') is-invalid @enderror" name="date_end" value="{{ $date_end ?? $datenow ?? '' }}" required autocomplete="date_end">
    
                        @error('date_end')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
                <div class="modal-footer">
                    <div class="form-group col-md-2">
                        <button type="submit" class="btn btn-info" title="Buscar">Enviar</button>  
                    </div>
            </form>
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    
@endsection

@section('javascript')
    <script>
        $('#dataTable').DataTable({
            "ordering": false,
            "order": [],
            'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "Todo"]]
        });

        
    </script> 
@endsection