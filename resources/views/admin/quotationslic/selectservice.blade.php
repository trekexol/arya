@extends('admin.layouts.dashboard')

@section('content')

<div class="container-fluid">
    <div class="row py-lg-2">
       
        <div class="col-sm-10">
            <h2>Seleccione un Servicio</h2>
        </div>
        
        <div class="col-sm-2">
            <select class="form-control" name="type" id="type">
                @if(isset($type))
                    @if ($type == 'productos')
                        <option disabled selected value="{{ $type }}">{{ $type }}</option>
                        <option disabled  value="{{ $type }}">-----------</option>
                    @else
                        <option disabled selected value="servicios">servicios</option>
                        <option disabled  value="servicios">-----------</option>
                    @endif
                    
                @else
                    <option disabled selected value="productos">productos</option>
                @endif
                
                <option  value="productos">productos</option>
                <option value="servicios">servicios</option>
            </select>
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
                <th class="text-center"></th>
                <th class="text-center">SKU</th>
                <th class="text-center">Descripción</th>
                <th class="text-center">Precio Bs</th>
                <th class="text-center">Precio Moneda</th>
                <th class="text-center">Moneda</th>
                <th class="text-center">Foto del Producto</th>
                
              
                
                
            </tr>
            </thead>
            
            <tbody>
                @if (empty($services))
                @else  
                    @foreach ($services as $var)
                        <tr>
                            <td>
                                <a href="{{ route('quotations.createproduct',[$id_quotation,$coin,$var->id_inventory]) }}" title="Seleccionar"><i class="fa fa-check"></i></a>
                            </td>
                            <td>{{ $var->code_comercial }}</td>
                            <td>{{ $var->description}}</td>
                           
                           
                            @if($var->money != 'Bs')
                                <td style="text-align: right">{{number_format($var->price * $bcv_quotation_product, 2, ',', '.')}}</td>
                                <td style="text-align: right">{{number_format($var->price, 2, ',', '.')}}</td> 
                            @else
                                <td style="text-align: right">{{number_format($var->price, 2, ',', '.')}}</td> 
                                <td style="text-align: right"></td> 
                            @endif
                            
                           
                            @if($var->money == "D")
                                <td>Dolar</td>
                            @else
                                <td>Bolívar</td>
                            @endif

                            <td>{{ $var->photo_product ?? ''}}</td> 
                            
                            
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
            'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "Todo"]]
        });

        $("#type").on('change',function(){
            type = $(this).val();
            window.location = "{{route('quotations.selectproduct', [$id_quotation,$coin,''])}}"+"/"+type;
        });

        $("body").toggleClass("sidebar-toggled");
        $(".sidebar").toggleClass("toggled");
        if ($(".sidebar").hasClass("toggled")) {
            $('.sidebar .collapse').collapse('hide');
        };
    </script> 
@endsection
