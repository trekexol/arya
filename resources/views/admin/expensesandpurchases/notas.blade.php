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
        <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('expensesandpurchases.index_historial') }}" role="tab" aria-controls="profile" aria-selected="false">Facturas de Compra / Gastos</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('anticipos.index_provider') }}" role="tab" aria-controls="profile" aria-selected="false">Anticipo a Proveedores</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link active font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('notas') }}" role="tab" aria-controls="profile" aria-selected="false">Notas Debito/Credito</a>
    </li>
</ul>


<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
      <div class="col-md-6">
          <h2>Notas de Debito y Credito</h2>
      </div>
      @if (Auth::user()->role_id  == '1' || $agregarmiddleware  == '1' )
      <div class="col-md-6">
        <a href="{{ route('crearnota')}}" class="btn btn-primary  float-md-right" role="button" aria-pressed="true">Registrar Nota</a>
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
            <th ></th>

                <th class="text-center">Factura de Compra</th>
                <th class="text-center">Proveedor</th>
                <th class="text-center">Fecha</th>
                <th class="text-center">Nota</th>
                @if (Auth::user()->role_id  == '1' || $eliminarmiddleware  == '1' )
                <th ></th>
                @endif
            </tr>
            </thead>

            <tbody>
                @if (empty($expensesandpurchases))
                @else
                    @foreach ($expensesandpurchases as $expensesandpurchase)
                        <tr>
                          <td>
                                <a  href="{{ route('pdf.debitnotemediacartagastoscompras',[$expensesandpurchase->id_expense,'bolivares'])}}" Target="_blank" title="Seleccionar"><i class="fa fa-print" style="color: rgb(46, 132, 243);"></i></a>
                                <a  href="{{ route('pdf.debitnotemediacartagastoscompras',[$expensesandpurchase->id_expense,'dolares'])}}" Target="_blank" title="Seleccionar"><i class="fa fa-print" style="color: rgb(46, 243, 46);"></i></a>
                         </td>

                            <td>{{$expensesandpurchase->invoice}}</td>
                            <td>{{$expensesandpurchase->providers['razon_social']}}</td>
                            <td>{{ date('d-m-Y', strtotime( $expensesandpurchase->date ?? ''))  }}</td>
                            @if($expensesandpurchase->percentage == 0)
                            @php $headatos = 'NOTA DEBITO DE GASTOS Y COMPRA NRO '.$expensesandpurchase->invoice; @endphp

                            <td>NOTA DE DEBITO NRO {{$expensesandpurchase->id}}</td>
                            @else
                            @php $headatos = 'NOTA CREDITO DE GASTOS Y COMPRA NRO '.$expensesandpurchase->invoice; @endphp

                            <td>NOTA DE CREDITO NRO {{$expensesandpurchase->id}}</td>
                            @endif
                            @if (Auth::user()->role_id  == '1' || $eliminarmiddleware  == '1' )
                            <td>
                                <a href="#" class="delete" data-id-expense="{{$expensesandpurchase->id.'/'.$headatos}}" data-toggle="modal" data-target="#deleteModal" title="Eliminar"><i class="fa fa-trash text-danger"></i></a>
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
            <form action="{{ route('deletenota') }}" method="post">
                @csrf
                @method('DELETE')
                <input id="id_expense_modal" type="hidden" class="form-control @error('id_expense_modal') is-invalid @enderror" name="id_expense_modal" readonly required autocomplete="id_expense_modal">
                <input id="descripcion" type="hidden" class="form-control @error('id_expense') is-invalid @enderror" name="descripcion" readonly required autocomplete="id_expense">

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

@endsection

@section('javascript')

<script>
    $('#dataTable').dataTable( {
      "ordering": false,
      "order": [],
        'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
    } );
    $(document).on('click','.delete',function(){

        id_expense = $(this).attr('data-id-expense').split('/');

        $('#id_expense_modal').val(id_expense[0]);
        $('#descripcion').val(id_expense[1]);
    });
</script>

@endsection
