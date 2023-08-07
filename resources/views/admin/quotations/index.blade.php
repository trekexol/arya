@extends('admin.layouts.dashboard')

@section('content')


<ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist" style="font-size: 10pt;">
    <li class="nav-item" role="presentation">
      <a class="nav-link active font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('quotations') }}" role="tab" aria-controls="home" aria-selected="true">Cotizaciones</a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('invoices') }}" role="tab" aria-controls="profile" aria-selected="false">Facturas</a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('quotations.indexdeliverynote') }}" role="tab" aria-controls="contact" aria-selected="false">Notas De Entrega</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('orders.index') }}" role="tab" aria-controls="contact" aria-selected="false">Pedidos</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('creditnotes') }}" role="tab" aria-controls="home" aria-selected="true">Notas de Crédito</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('debitnotes') }}" role="tab" aria-controls="home" aria-selected="true">Notas de Débito</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('sales') }}" role="tab" aria-controls="profile" aria-selected="false">Ventas</a>
      </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('anticipos') }}" role="tab" aria-controls="contact" aria-selected="false">Anticipos Clientes</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('clients') }}" role="tab" aria-controls="profile" aria-selected="false">Clientes</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('vendors') }}" role="tab" aria-controls="contact" aria-selected="false">Vendedores</a>
    </li>
  </ul>

<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
      <div class="col-sm-1">
          <h2>Cotizaciones {{$coin}}</h2>
      </div>
        <div class="col-sm-3 offset-sm-2  dropdown mb-4">
            <button class="btn btn-success" type="button"
                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="false"
                aria-expanded="false">
                <i class="fas fa-bars"></i>
                Exportaciones
            </button>
            <div class="dropdown-menu animated--fade-in"
                aria-labelledby="dropdownMenuButton">
                <a href="#" data-toggle="modal" data-target="#PDFModalAccount" class="dropdown-item bg-light">Exportar a PDF</a>
                <a href="#" data-toggle="modal" data-target="#ExcelModalAccount" class="dropdown-item bg-light">Exportar a Excel</a>
            </div>
        </div>
        <div class="col-sm-2">
            <select class="form-control" name="coin" id="coin">
                @if(isset($coin))
                    @if($coin == 'bolivares')
                        <option selected value="bolivares">Bolívares</option>
                        <option value="dolares">Dólares</option>
                    @else
                        <option value="bolivares">Bolívares</option>
                        <option selected value="dolares">Dólares</option>
                    @endif
                @else
                        <option selected value="bolivares">Bolívares</option>
                        <option value="dolares">Dólares</option>

                @endif



            </select>
        </div>
        @if (Auth::user()->role_id  == '1' || $agregarmiddleware  == '1')
      <div class="col-sm-3">
        <a href="{{ route('quotations.createquotation')}}" class="btn btn-primary  float-md-right" role="button" aria-pressed="true">Registrar una Cotización</a>
      </div>
      @endif
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
        <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0" >
            <thead>
            <tr>
                <th class="text-center"></th>
                <th class="text-center">ID</th>
                <th class="text-center" style="width: 11%">Fecha de Cotización</th>
                <th class="text-center">Cliente</th>
                <th class="text-center">Vendedor</th>
                <th class="text-center">Observaciones</th>
                <th class="text-center">Monto</th>
                <th class="text-center">Moneda</th>
                @if (Auth::user()->role_id  == '1' || $eliminarmiddleware  == '1')
                <th class="text-center"></th>
                @endif

            </tr>
            </thead>

            <tbody>
                @if (empty($quotations))
                @else
                @foreach ($quotations as $quotation)
                <tr>
                    <td>
                        @if (Auth::user()->role_id  == '1' || $agregarmiddleware  == '1')
                            
                                <a href="{{ route('quotations.create',[$quotation->id,'dolares']) }}" title="Seleccionar"><i class="fa fa-check" style="color: orange;"></i></a>
                        @endif
                        @if($quotation->photo == true)
                        
                        
                                <a href="{{ route('pdf.quotation',[$quotation->id,$coin ?? 'dolares','false']) }}" title="Imprimir" target="_blank"><i class="fa fa-print"  style="color: rgb(46, 132, 243);"></i></a>
                                <a href="{{ route('pdf.quotation',[$quotation->id,$coin ?? 'dolares',$quotation->photo]) }}" title="Imprimir" target="_blank"><i class="fa fa-print" style="color: rgb(243, 46, 46);"></i></a>


                        @else
                               <a href="{{ route('pdf.quotation',[$quotation->id,$coin ?? 'dolares','false']) }}" title="Imprimir" target="_blank"><i class="fa fa-print" style="color: rgb(46, 132, 243);"></i></a>

                        @endif


                        <a href="#" class="send" data-toggle="modal" data-id-quotation-send={{$quotation->id}} data-target="#emailModal" title="Enviar por Correo"><i class="fa fa-paper-plane" style="color: rgb(128, 119, 119);"></i></a>
                    </td>

                    <td class="text-center">{{ $quotation->id ?? ''}}</td>
                    <td class="text-center">{{ date_format(date_create($quotation->date_quotation),"d-m-Y") ?? ''}}</td>
                    <td class="text-center">{{ $quotation->clients['name'] ?? ''}}</td>
                    <td class="text-center">{{ $quotation->vendors['name'] ?? ''}}</td>
                    <td class="text-center">{{ $quotation->observation ?? ''}}</td>
                    @if ($coin == 'dolares')
                    <td class="text-center">{{ number_format($quotation->amount_with_iva, 2, ',', '.') ?? 0}}</td>
                    <td class="text-center">{{ 'USD' }}</td>
                    @else
                    <td class="text-center">{{ number_format($quotation->amount_with_iva, 2, ',', '.') ?? 0}}</td>
                    <td class="text-center">{{ 'BS' }}</td>
                    @endif
                    @if (Auth::user()->role_id  == '1' || $eliminarmiddleware  == '1')
                    <td>
                    <a href="#" class="delete" data-id-quotation={{$quotation->id}} data-toggle="modal" data-target="#deleteModal" title="Eliminar"><i class="fa fa-trash text-danger"></i></a>
                    </td>
                    @endif
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
          <form action="{{ route('quotations.deleteQuotation') }}" method="post">
              @csrf
              @method('DELETE')
              <input id="id_quotation_modal" type="hidden" class="form-control @error('id_quotation_modal') is-invalid @enderror" name="id_quotation_modal" readonly required autocomplete="id_quotation_modal">

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
            <form action="{{ route('mails.quotationIndex',[$coin ?? 'bolivares']) }}" method="post">
                @csrf
                @method('POST')

                <input id="id_quotation_send_modal" type="hidden" class="form-control @error('id_quotation_send_modal') is-invalid @enderror" name="id_quotation_send_modal" readonly required autocomplete="id_quotation_send_modal">

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


<div class="modal fade" id="PDFModalAccount" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Seleccione el periodo</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" id="formPostPdfAccountOrdenDePago" action="{{ route('quotations.pdfQuotations') }}"   target="print_popup" onsubmit="window.open('about:blank','print_popup','width=1000,height=800');">
                @csrf
            <div class="modal-body">
                <div class="form-group row">
                    <label for="client" class="col-md-2 col-form-label text-md-right">Cliente:</label>
                        <div class="col-md-8">
                            <select class="form-control" id="id_client" name="id_client" >
                                <option value="">Selecciona un Cliente</option>
                                @foreach($clients as $var)
                                    <option value="{{ $var->id }}">{{ $var->name }}</option>
                                @endforeach

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


<div class="modal fade" id="ExcelModalAccount" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Seleccione el periodo / Exportar a Excel</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" id="formPostPdfAccountOrdenDePago" action="{{ route('export_reports.quotations') }}"  >
                @csrf
            <div class="modal-body">
                <div class="form-group row">
                    <label for="client" class="col-md-2 col-form-label text-md-right">Cliente:</label>
                        <div class="col-md-8">
                            <select class="form-control" id="id_client" name="id_client" >
                                <option value="">Selecciona un Cliente</option>
                                @foreach($clients as $var)
                                    <option value="{{ $var->id }}">{{ $var->name }}</option>
                                @endforeach

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
        'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
    });


    $(document).on('click','.send',function(){

        let id_quotation_send = $(this).attr('data-id-quotation-send');

        $('#id_quotation_send_modal').val(id_quotation_send);
     });

    $("#coin").on('change',function(){

        var coin = $(this).val();
        window.location = "{{route('quotations', '')}}"+"/"+coin;
    });

    $(document).on('click','.delete',function(){

        let id_quotation = $(this).attr('data-id-quotation');

        $('#id_quotation_modal').val(id_quotation);
    });
    </script>

@endsection
