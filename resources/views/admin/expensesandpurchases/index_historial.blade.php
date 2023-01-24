@extends('admin.layouts.dashboard')

@section('content')

<ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('expensesandpurchases') }}" role="tab" aria-controls="home" aria-selected="true">Por Procesar</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('expensesandpurchases.indexdeliverynote') }}" role="tab" aria-controls="home" aria-selected="true">Ordenes de Compra</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link active font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('expensesandpurchases.index_historial') }}" role="tab" aria-controls="profile" aria-selected="false">Facturas de Compra / Gastos</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('anticipos.index_provider') }}" role="tab" aria-controls="profile" aria-selected="false">Anticipo a Proveedores</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('notas') }}" role="tab" aria-controls="profile" aria-selected="false">Notas Debito</a>
    </li>
</ul>
<form method="POST" action="{{ route('expensesandpurchases.multipayment') }}" enctype="multipart/form-data" >
    @csrf
<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
        <div class="col-md-6">
            <h2>Facturas de Compra / Gastos</h2>
        </div>
        <div class="col-md-2">
            <a href="{{ route('payment_expenses')}}" class="btn btn-info btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-hand-holding-usd"></i>
                </span>
                <span class="text">Pagos</span>
            </a>
        </div>
        <div class="col-sm-3  dropdown mb-4">
            <button class="btn btn-success" type="button"
                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="false"
                aria-expanded="false">
                <i class="fas fa-bars"></i>
                Exportaciones
            </button>
            <div class="dropdown-menu animated--fade-in"
                aria-labelledby="dropdownMenuButton">
                <a href="#" data-toggle="modal" data-target="#reportIvaTxtModal" class="dropdown-item bg-light">Retención de Iva a .txt</a>
                <a href="#" data-toggle="modal" data-target="#reportIslrModal" class="dropdown-item bg-light">Retención de ISLR a XML</a>
                <a href="#" data-toggle="modal" data-target="#reportIvaExcelModal" class="dropdown-item bg-light">Retención de Iva a Excel</a>
            </div>
        </div>
      <div class="col-md-4">
        <button type="submit" title="Agregar" id="btncobrar" class="btn btn-primary  float-md-right" >Cobrar Gastos o Compras</a>
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
        <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0" >
            <thead>
            <tr>
                <th>Orden</th>
                <th class="text-center">Factura de Compra</th>
                <th class="text-center">N° de Control/Serie</th>
                <th class="text-center">Proveedor</th>
                <th class="text-center">Fecha</th>
                <th class="text-center">REF</th>
                <th class="text-center">Total</th>
                <th ></th>
                <th ></th>

            </tr>
            </thead>

            <tbody>
                @if (empty($expensesandpurchases))
                @else
                    @foreach ($expensesandpurchases as $expensesandpurchase)
                        <?php
                            $amount_bcv = 1;

                             if ($expensesandpurchase->rate <= 0) {
                                 $rate = 1;
                             } else {
                                 $rate = $expensesandpurchase->rate;
                             }

                        ?>
                         <tr>
                           <td>{{$expensesandpurchase->id ?? ''}}</td>
                            <td class="text-center">
                                <a href="{{ route('expensesandpurchases.create_expense_voucher',[$expensesandpurchase->id,$expensesandpurchase->coin ?? 'bolivares']) }}" title="Ver Detalle" class="text-center text-dark font-weight-bold">
                                    {{$expensesandpurchase->invoice ?? ''}}
                                </a>
                            </td>
                            <td class="text-center">{{$expensesandpurchase->serie ?? ''}}</td>
                            <td class="text-center">{{$expensesandpurchase->providers['razon_social'] ?? ''}}</td>
                            <td class="text-center">{{ date('d-m-Y', strtotime( $expensesandpurchase->date ?? ''))  }} </td>
                            <td class="text-right">${{number_format($expensesandpurchase->amount_with_iva / $rate ?? 0, 2, ',', '.')}}</td>
                            <td class="text-right">{{number_format($expensesandpurchase->amount_with_iva, 2, ',', '.')}}</td>
                            @if ($expensesandpurchase->status == "C")
                            <td class="text-center font-weight-bold">
                                <a href="{{ route('expensesandpurchases.create_expense_voucher',[$expensesandpurchase->id,$expensesandpurchase->coin ?? 'bolivares']) }}" title="Ver Detalle" class="text-center text-success font-weight-bold">Pagado</a>
                            </td>
                            <td>
                            </td>
                            @elseif ($expensesandpurchase->status == "X")
                            <td class="text-center font-weight-bold">
                                Reversado
                            </td>
                            <td>
                            </td>
                            @else
                            <td class="text-center font-weight-bold">
                                @if (Auth::user()->role_id  == '1' || $agregarmiddleware  == '1' )
                                <a href="{{ route('expensesandpurchases.create_payment_after',[$expensesandpurchase->id,$expensesandpurchase->coin]) }}" title="Cobrar Factura" class="font-weight-bold text-dark">Click para Pagar</a>
                                @endif
                            </td>
                            <td>
                                @if (Auth::user()->role_id  == '1' || $agregarmiddleware  == '1' )
                                <a href="{{ route('expensesandpurchases.create_detail',[$expensesandpurchase->id,'bolivares']) }}" title="Editar"><i class="fa fa-edit"></i></a>
                                <input type="checkbox" name="check{{ $expensesandpurchase->id }}" value="{{ $expensesandpurchase->id }}" onclick="buttom();" id="flexCheckChecked">
                                @endif
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
</form>
<div class="modal fade" id="reportIvaTxtModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Seleccione el periodo</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('exportexpense.ivaTxt') }}"  >
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

                <div class="modal-footer">
                    <div class="form-group col-sm-2">
                        <button type="submit" class="btn btn-info" title="Buscar">Enviar</button>
                    </div>
            </form>
                    <div class="offset-sm-2 col-sm-3">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="reportIslrModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Seleccione el periodo</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('exportexpense.islrXml') }}"  >
                @csrf
            <div class="modal-body">
                <div class="form-group row">
                    <label for="date_end" class="col-sm-3 col-form-label text-md-right">Seleccionar</label>

                    <div class="col-sm-6">
                        <input id="date_begin" type="month" class="form-control @error('date_begin') is-invalid @enderror" name="date_begin" value="{{ date_format(date_create( $date_begin ?? $datenow  ?? "01-2021"),"Y-m") }}" required autocomplete="date_begin">

                        @error('date_begin')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="form-group col-sm-2">
                        <button type="submit" class="btn btn-info" title="Buscar">Enviar</button>
                    </div>
            </form>
                    <div class="offset-sm-2 col-sm-3">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="reportIvaExcelModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Seleccione el periodo</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('exportexpense.ivaExcel') }}"  >
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

                <div class="modal-footer">
                    <div class="form-group col-sm-2">
                        <button type="submit" class="btn btn-info" title="Buscar">Enviar</button>
                    </div>
            </form>
                    <div class="offset-sm-2 col-sm-3">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('javascript')

<script>
    $('#dataTable').dataTable( {
      "ordering": false,
      "order": [],
        'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
    } );



    $("#btncobrar").hide();

    function buttom(){

        $("#btncobrar").show();
    }



</script>

@endsection
