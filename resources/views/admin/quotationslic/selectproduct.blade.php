@extends('admin.layouts.dashboard')

@section('content')

<!-- container-fluid -->
<div class="container-fluid">

  
  <!-- /.container-fluid -->
  {{-- VALIDACIONES-RESPUESTA--}}
  @include('admin.layouts.success')   {{-- SAVE --}}
  @include('admin.layouts.danger')    {{-- EDITAR --}}
  @include('admin.layouts.delete')    {{-- DELELTE --}}
  {{-- VALIDACIONES-RESPUESTA --}}
<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Seleccionar un Producto</h6>
    </div>
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
                <th>Seleccionar</th>
                <th>Descripción</th>
                <th>Segmento</th>
                <th>Sub Segmento</th>
                <th>Unidad de Medida</th>
                <th>Código Comercial</th>
                <th>Tipo</th>
                <th>Precio</th>
                <th>Precio de Compra</th>
                <th>Costo Promedio</th>
                
                <th>Moneda</th>
                <th>Exento</th>
                <th>ISLR</th>
                <th>Impuesto Especial</th>
                
            </tr>
            </thead>
            
            <tbody>
                @if (empty($products))
                @else  
                    @foreach ($products as $product)
                        <tr>
                            <td>
                                <a href="{{ route('quotations.createproduct',[$id_quotation,$product->id]) }}" title="Seleccionar"><i class="fa fa-check"></i></a>
                             </td>
                            <td>{{$product->description}}</td>
                            <td>{{$product->segments['description']}}</td>
                            <td>{{$product->subsegments['description']}}</td> 
                            <td>{{$product->unitofmeasures['description']}}</td> 
                            <td>{{$product->code_comercial}}</td>
                            <td>{{$product->type}}</td>
                            
                            <td>{{$product->price}}</td>
                            <td>{{$product->price_buy}}</td>
                            <td>{{$product->cost_average}}</td>
                            

                            @if($product->money == "D")
                                <td>Dolar</td>
                            @else
                                <td>Bolívar</td>
                            @endif
                            @if($product->exento == "1")
                                <td>Si</td>
                            @else
                                <td>No</td>
                            @endif
                            @if($product->islr == "1")
                                <td>Si</td>
                            @else
                                <td>No</td>
                            @endif
                            
                            <td>{{$product->special_impuesto}}</td>
                           
                            
                            
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
    $('#dataTable').DataTable({
        "ordering": false,
        "order": [],
        'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
    });

    </script> 

@endsection