@extends('admin.layouts.dashboard')

@section('content')

<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
      <div class="col-md-6">
          <h2>Histórico de Transportes</h2>
      </div>
      @if(Auth::user()->role_id  == '1' || $agregarmiddleware == 1)
      <div class="col-md-6">
        <a href="{{ route('historictransports.selecttransport')}}" class="btn btn-primary btn-lg float-md-right" role="button" aria-pressed="true">Registrar Histórico de Transporte</a>
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
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Histórico de Transportes</h6>
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
                <th>Empleado</th>
                <th>Transporte</th>
                <th>Fecha Inicio</th>
                <th>Fecha Final</th>
                @if(Auth::user()->role_id  == '1' || $eliminarmiddleware == 1)
                <th>Tools</th>
                @endif
            </tr>
            </thead>
          
            <tbody>
                @if (empty($employees))
                @else  
                     @foreach ($employees as $emp)

                        @foreach ($emp->transports as $var)
                        <tr>
                            <td>{{ $emp->nombres}}</td>
                            <td>{{ $var->placa}}</td>
                            <td>{{ $var->pivot->date_begin}}</td>
                            <td>{{ $var->pivot->date_end}}</td>
                           
                     
                        
                            @if(Auth::user()->role_id  == '1' || $eliminarmiddleware == 1)
                            <td>
                                <a href="#" class="delete" data-id-user={{$var->pivot->id}} data-toggle="modal" data-target="#deleteModal" title="Eliminar"><i class="fa fa-trash text-danger"></i></a>  
                               </td>
                               @endif
                        </tr>  
                        @endforeach   
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
            <form action="{{ route('historictransports.delete') }}" method="post">
                @csrf
                @method('DELETE')
                <input id="id_user_modal" type="hidden" class="form-control @error('id_user_modal') is-invalid @enderror" name="id_user_modal" readonly required autocomplete="id_user_modal">
                       
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
        "ordering": false,
        "order": [],
        'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
    });


    $(document).on('click','.delete',function(){
         
         let id_user = $(this).attr('data-id-user');
 
         $('#id_user_modal').val(id_user);
     });
    </script> 

@endsection