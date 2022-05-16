@extends('admin.layouts.dashboard')

@section('content')


<ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
      <a class="nav-link font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('quotationslic') }}" role="tab" aria-controls="home" aria-selected="true">Cotizaciones<span style="color: green;">•</span></a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('invoiceslic') }}" role="tab" aria-controls="profile" aria-selected="false">Facturas<span style="color: green;">•</span></a>
    </li>

    <li class="nav-item" role="presentation">
      <a class="nav-link font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('quotationslic.indexdeliverynote') }}" role="tab" aria-controls="contact" aria-selected="false">Notas De Entrega<span style="color: green;">•</span></a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('clientslic') }}" role="tab" aria-controls="profile" aria-selected="false">Clientes<span style="color: green;">•</span></a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('vendorslic') }}" role="tab" aria-controls="contact" aria-selected="false">Vendedores<span style="color: green;">•</span></a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link active font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('saleslic') }}" role="tab" aria-controls="profile" aria-selected="false">Ventas<span style="color: green;">•</span></a>
      </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('anticiposlic') }}" role="tab" aria-controls="contact" aria-selected="false">Anticipos Clientes<span style="color: green;">•</span></a>
    </li>
  </ul>



<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
      <div class="col-md-6">
          <h2>Ventas</h2>
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
                <th class="text-center">Código</th>
                <th class="text-center">Descripción</th>
                <th class="text-center">Tipo</th>
                <th class="text-center">Cantidad</th>
                <th class="text-center">Moneda</th>
                
               
            </tr>
            </thead>
            
            <tbody>

                


                @if (empty($inventories_quotations))
                @else  
                    @foreach ($inventories_quotations as $var)
                        <?php 
                            $total = $var->amount_sales * $var->price * ($bcv ?? 1);
                        ?>
                        <tr>
                            <td class="text-center font-weight-bold">{{$var->code}}</td>
                            <td class="text-center font-weight-bold">{{$var->description}}</td>
                            <td class="text-center font-weight-bold">{{$var->type}}</td>
                            <td class="text-right font-weight-bold">{{number_format($var->amount_sales, 0, '', '.')}}</td>
                            @if ($var->money == 'Bs')
                                <td class="text-center">Bolivares</td>
                            @else
                                <td class="text-center">Dolares</td>
                            @endif
                            
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
