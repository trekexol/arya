@extends('admin.layouts.dashboard')

@section('content')

<ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('expensesandpurchases') }}" role="tab" aria-controls="home" aria-selected="true">Gastos y Compras</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('expensesandpurchases.indexdeliverynote') }}" role="tab" aria-controls="home" aria-selected="true">Ordenes de Compra</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('expensesandpurchases.index_historial') }}" role="tab" aria-controls="profile" aria-selected="false">Historial</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link active font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('anticipos.index_provider') }}" role="tab" aria-controls="profile" aria-selected="false">Anticipo a Proveedores</a>
    </li>
</ul>
<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
        <div class="col-md-4 h4">
            Anticipos a Proveedores
        </div>
        @if ($control != 'index')
            <div class="col-md-2 offset-md-3">
                <a href="{{ route('anticipos.index_provider')}}" class="btn btn-info btn-icon-split">
                    <span class="icon text-white-50">
                        <i class="fas fa-hand-holding-usd"></i>
                    </span>
                    <span class="text">Anticipos</span>
                </a>
            </div>
        @else
            <div class="col-md-4 offset-md-1">
                <a href="{{ route('anticipos.historic_provider')}}" class="btn btn-success btn-icon-split">
                    <span class="icon text-white-50">
                        <i class="fas fa-book"></i>
                    </span>
                    <span class="text">Ver Historico de Anticipos</span>
                </a>
            </div>
        @endif
        
        
       
        @if (Auth::user()->role_id  == '1' || Auth::user()->role_id  == '2' )
        <div class="col-md-3">
            <a href="{{ route('anticipos.create_provider')}}" class="btn btn-primary" role="button" aria-pressed="true">Registrar un Anticipo</a>
         
        </div>
        @endif
       
            
       
    </div>

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
                <th class="text-center">ID</th>
                <th class="text-center" width="11%">Fecha del Anticipo</th>               
                <th class="text-center">Proveedor</th>
                <th class="text-center">Caja/Banco</th>
                <th class="text-center">Comp.</th>

                <th class="text-center">Referencia</th>
                <th class="text-center">Monto REF</th>
                <th class="text-center">Monto Bs.</th>
                <th class="text-center">Moneda</th>
               <th class="text-center" width="7%"></th>
              
            </tr>
            </thead>
            
            <tbody>
                @if (empty($anticipos))
                @else
                    @foreach ($anticipos as $key => $anticipo)
                    <?php 

                        $amount_bcv = 0;

                        $amount_bcv = $anticipo->amount / $anticipo->rate;

                    ?>
                    <tr>
                        <td>{{ $anticipo->id }} {{ (isset($anticipo->id_anticipo_restante)) ? 'Restante de: '.$anticipo->id_anticipo_restante : '' }}</td>
                        <td class="text-center">{{$anticipo->date}}</td>
                        @if (isset($anticipo->expenses['serie']))
                        <td class="text-center">{{$anticipo->providers['razon_social'] ?? ''}} , fact({{$anticipo->expenses['serie'] ?? ''}})</td>
                    @else
                        <td class="text-center">{{$anticipo->providers['razon_social'] ?? ''}}</td>
                    @endif
                    
                    <td class="text-center">{{$anticipo->accounts['description'] ?? ''}}</td>
                    <td class="text-center"><a href="{{ route('detailvouchers.create',[$anticipo->coin,$anticipo->comprobante ?? '']) }}" title="Ver comprobante contable">{{ $anticipo->comprobante ?? '' }}</a></td>
                    
                    <td class="text-center">{{$anticipo->reference ?? ''}}</td>
                    <td class="text-right">${{number_format($amount_bcv ?? 0, 2, ',', '.')}}</td>
                    <td class="text-right">{{number_format($anticipo->amount, 2, ',', '.')}}</td>
                    <td class="text-center">{{$anticipo->coin}}</td>
                   
                    @if (Auth::user()->role_id  == '1')
                        <td>
                            <a href="{{ route('anticipos.edit',$anticipo->id) }}"  title="Editar"><i class="fa fa-edit"></i></a>
                            <a href="#" class="delete" data-id-anticipo={{$anticipo->id}} data-toggle="modal" data-target="#deleteModal" title="Eliminar"><i class="fa fa-trash text-danger"></i></a>  
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
            <form action="{{ route('anticipos.delete_provider') }}" method="post">
                @csrf
                @method('DELETE')
                <input id="id_anticipo_modal" type="hidden" class="form-control @error('id_anticipo_modal') is-invalid @enderror" name="id_anticipo_modal" readonly required autocomplete="id_anticipo_modal">
                       
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
    $('#dataTable').DataTable({
        "ordering": true,
        "order": [],
        'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
    });

    $(document).on('click','.delete',function(){
         
         let id_anticipo = $(this).attr('data-id-anticipo');

         $('#id_anticipo_modal').val(id_anticipo);
        });
    </script> 
@endsection