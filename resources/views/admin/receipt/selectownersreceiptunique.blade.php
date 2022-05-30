@extends('admin.layouts.dashboard')

@section('content')

<div class="container-fluid">
    <div class="row py-lg-2">
       
        <div class="col-md-6">
            <h2>Seleccione un Propietario</h2>
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
                    <th>ID Propietario</th>
                    <th>Propietario</th>
                    <th>ID Apart.</th>
                    <th>Apartamento/Local</th>
                    <th>Telefono</th>
                    <th>Telefono 2</th>
                    
                </tr>
                </thead>
                
                <tbody>
                    @if (empty($owners))
                    @else  
                        @foreach ($owners as $owner)
                            <tr>
                                <td >
                                    <a href="{{ route('receipt.createreceiptunique',[$client ?? '1' ,$type ?? '2' ,$datenow ?? '3' ,$owner->id ?? '4']) }}"  title="Seleccionar"><i class="fa fa-check" style="color: orange"></i></a>
                               </td>
                               <td >{{$owner->cedula_rif}}</td>
                               <td >{{$owner->name}}</td>
                               <td >{{$owner->personcontact}}</td>
                                <td >{{$owner->direction}}</td>
                                <td >{{$owner->phone1}}</td>
                                <td >{{$owner->phone2}}</td>
                                
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