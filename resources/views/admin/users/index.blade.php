@extends('admin.layouts.dashboard')

@section('content')

   

<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
        <div class="col-md-6">
            <h2>Usuarios Registrados</h2>
        </div>
       
        @if (Auth::user()->role_id  == '1' || $agregarmiddleware == 1)
        <div class="col-md-6">
            <a href="{{ route('users.create')}}" class="btn btn-primary btn-lg float-md-right" role="button" aria-pressed="true">Registrar Usuario</a>
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
                <th>Id</th>
                <th>Usuario</th>
                <th>Email</th>
                <th>Sucursal</th>
                <th>Rol</th>
                <th>Status</th>
                @if (Auth::user()->role_id  == '1' || $agregarmiddleware == 1 || $actualizarmiddleware == 1 || $eliminarmiddleware == 1)
                    <th>Tools</th>
                @endif
            </tr>
            </thead>
            
            <tbody>
                @if (empty($users))
                @else
                    @foreach ($users as $key => $user)
                    <tr>
                    <td>{{$user->id}}</td>
                    <td>{!!$user->name!!}</td>
                    <td>{!!$user->email!!}</td>
                    <td>{!!$user->branches['description'] ?? ''!!}</td>
                    <td>{{ $user->roles['description'] ?? ''}}</td>
                    @if($user->status == 1)
                        <td>Activo</td>
                    @else
                        <td>Inactivo</td>
                    @endif
                    @if (Auth::user()->role_id  == '1' || $agregarmiddleware == 1 || $actualizarmiddleware == 1 || $eliminarmiddleware == 1)
                        <td class="text-center">
                            @if (Auth::user()->role_id  == '1' || $agregarmiddleware == 1)
                            <a href="{{ route('users.createAssignModules',$user->id)}}" title="Seleccionar"><i class="fa fa-check"></i></a>
                            @endif
                            @if (Auth::user()->role_id  == '1' || $actualizarmiddleware == 1)
                            <a href="{{ route('users.edit',$user->id) }}"  title="Editar"><i class="fa fa-edit"></i></a>
                            @endif
                            @if (Auth::user()->role_id  == '1' || $eliminarmiddleware == 1)
                            <a href="#" class="delete" data-id-user={{$user->id}} data-toggle="modal" data-target="#deleteModal" title="Eliminar"><i class="fa fa-trash text-danger"></i></a>  
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
            <form action="{{ route('users.delete') }}" method="post">
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