@extends('admin.layouts.dashboard')

@section('content')

<div class="container-fluid">
    <div class="row py-lg-2">
       
        <div class="col-md-6">
            <h2>Seleccione un Proveedor</h2>
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
        <div class="table-responsive">
            <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                <tr> 
                    <th></th>
                   
                    <th>Nombre / Razón Social</th>
                    <th>Código Proveedor</th>
                    <th>Dirección</th>
                    <th>Ciudad</th>
                    <th>Pais</th>
                    <th>Telefono</th>
                    <th>Telefono 2</th>
                    
                </tr>
                </thead>
                
                <tbody>
                    @if (empty($providers))
                    @else  
                        @foreach ($providers as $provider)
                            <tr>
                                <td >
                                    <a href="{{ route('expensesandpurchases.updateproviderexpense',[$id_expense,$provider->id,$coin ?? 'Bolivares']) }}"  title="Seleccionar Proveedor"><i class="fa fa-check" style="color: orange"></i></a>
                               </td>
                                <td >{{$provider->razon_social}}</td>
                                <td >{{$provider->code_provider}}</td>
                                <td >{{$provider->direction}}</td>
                                <td >{{$provider->city}}</td>
                                <td >{{$provider->country}}</td>
                                <td >{{$provider->phone1}}</td>
                                <td >{{$provider->phone2}}</td>
                                
                            </tr>     
                        @endforeach   
                    @endif
                </tbody>
            </table>
            
        </div>
        <br>
        <div class="form-group row col-md-4">
                
            <div class="col-md-2">
                <a href="{{ route('expensesandpurchases.create') }}" id="btnfacturar" name="btnfacturar" class="btn btn-danger" title="facturar">Volver</a>  
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
</script>
    
@endsection
