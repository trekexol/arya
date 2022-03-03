@extends('admin.layouts.dashboard')

@section('content')

<div class="container-fluid">
    <div class="row py-lg-2">
       
        <div class="col-md-10">
            @if(isset($type) && ($type == "MERCANCIA"))
                <h2>Seleccione un Producto del Inventario</h2>
            @endif
            
            @if(isset($type) && ($type == "MATERIAP"))
            <h2>Seleccione un Producto Materia Prima</h2>
            @endif
            
            @if(isset($type) && ($type == "SERVICIO"))
            <h2>Seleccione un Servicio</h2>
            @endif
        </div>
        
        <div class="col-md-2">
            <a href="{{ route('expensesandpurchases.create_detail',[$id_expense,$coin]) }}" id="btnfacturar" name="btnfacturar" class="btn btn-danger" title="facturar">Volver</a>  
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
                <th></th>
                <th>Código Comercial</th>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>Precio de Compra</th>
                <th>Moneda</th>
                <th>Foto del Producto</th>
              
            </tr>
            </thead>
            
            <tbody>
                @if (empty($inventories))
                @else  
                    @foreach ($inventories as $var)
                        <tr>
                            <td>
                                <a href="{{ route('expensesandpurchases.create_detail',[$id_expense,$coin,$type ?? 'MERCANCIA',$var->id]) }}" title="Seleccionar"><i class="fa fa-check"></i></a>
                            </td>
                            <td>{{ $var->code_comercial }}</td>
                            <td>{{ $var->description}}</td>
                            <td style="text-align: right">{{ $var->amount }}</td> 
                            <td style="text-align: right">{{number_format($var->price_buy, 2, ',', '.')}}</td>
                            
                            @if($var->money == "D")
                            <td>Dolar</td>
                            @else
                            <td>Bolívar</td>
                            @endif

                            <td>{{ $var->photo_product}}</td> 
                           
                            
                        </tr>     
                    @endforeach   
                @endif
            </tbody>
        </table>
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
</script>
    
@endsection