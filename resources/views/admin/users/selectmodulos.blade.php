@extends('admin.layouts.dashboard')

@section('content')

<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
        <div class="col-sm-7">
            <h2>Módulos en los que podrá acceder el usuario {{ $user->name ?? ''}}</h2>
        </div>
        <div class="col-sm-3">
           
           <a href="{{ route('users.indexpermisos',['id_user' => $user->id, 'name_user' => $user->name]) }}" class="btn btn-primary float-md-right" >Asignar Permiso</a>  

        </div>
        <div class="col-sm-2">
            <a href="{{ route($route_return ?? 'users') }}" class="btn btn-danger">
                Volver
            </a>
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
<form id="formSend" method="POST" action="{{ route('users.assignModules') }}" enctype="multipart/form-data">
    @csrf

    <input id="id_user" type="hidden" class="form-control @error('id_user') is-invalid @enderror" name="id_user" value="{{ $user->id }}" readonly required autocomplete="id_user">
    
    <input id="modulos_news" type="hidden" class="form-control @error('modulos_news') is-invalid @enderror" name="modulos_news" readonly required autocomplete="modulos_news">
    <input id="modulos_olds" type="hidden" class="form-control @error('modulos_olds') is-invalid @enderror" name="modulos_olds" readonly required autocomplete="modulos_olds">
            
    
    
<div class="card shadow mb-4">
    
    <div class="card-body">
        <div class="container">
        <div class="table-responsive">
        <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
            <tr> 
                <th>Sistema</th>
                <th>Módulo</th>
                <th>Permisos</th>
                <th>Acción</th>
            </tr>
            </thead>
            <tbody>
                @if (empty($user_access))
                @else  
            
                    @foreach ($user_access as $modulo)
                    
                        <tr>
                            <td>{{$modulo->sistema ?? ''}}</td>
                            <td>{{$modulo->name ?? ''}}</td>
                            <td>
                                
                            <span class="badge badge-info">Consultar</span>
                            
                            @php 
                            
                            if($modulo->agregar == 1) {
                            
                            echo "<span class='badge badge-primary'>Agregar</span>";

                            }

                            if($modulo->actualizar == 1) {
                            
                                echo "<span class='badge badge-success'>Actualizar</span>";

                            }

                            if($modulo->eliminar == 1) {

                                echo "<span class='badge badge-danger'>Eliminar</span>";

                            }

                            @endphp
                              

                            
                            </td>
                            <td class="text-center">
                                <a href="#" class="delete" data-id-user="{{$modulo->id.'/'.$modulo->id_user}}" data-toggle="modal" data-target="#deleteModal" title="Eliminar"><i class="fa fa-trash text-danger"></i></a>  
                            </td>
                        
                            
                        </tr>     
                    @endforeach   
                @endif
            </tbody>
        </table>
            </div>
        </div>
    </div>
</div>

</form>


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
            <form action="{{ route('modulos.delete') }}" method="post">
                @csrf
                @method('DELETE')
                <input id="id_user_modal" type="hidden" class="form-control @error('id_user_modal') is-invalid @enderror" name="id_user_modal" readonly required autocomplete="id_user_modal">
                <input id="idacces" type="hidden" class="form-control @error('idacces') is-invalid @enderror" name="idacces" readonly required autocomplete="idacces">

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
         
         let data = $(this).attr('data-id-user').split('/');
 
         $('#id_user_modal').val(data[1]);
         $('#idacces').val(data[0]);

        
     });
    


    </script> 

@endsection
