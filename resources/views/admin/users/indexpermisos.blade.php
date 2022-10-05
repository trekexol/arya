@extends('admin.layouts.dashboard')

@section('content')

<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
        <div class="col-sm-7">
            <h2>Asignar Modulo al usuario {{ $name_user ?? ''}}</h2>
        </div>
        <div class="col-sm-2">
            <a href="{{ route('users.createAssignModules',['id_user' => $id_user] ?? 'users') }}" class="btn btn-danger">
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

    <div class="form-group col-md-4">
        <label for="inputState">Sistemas</label>
        <select id="sistemas" class="form-control">
          <option value="" >Seleccione...</option>
          @foreach($sistemas as $sistemas)

          <option value="{{$sistemas->id_sistema.','.$id_user}}">{{$sistemas->sistema}}</option>
      
          @endforeach
        </select>
      </div>
<div class="card shadow mb-4">
    
    <div class="card-body">
        <div class="container">
        <div class="table-responsive" id="tablaids">
      
            </div>
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
  
        //envia el post
        $(function(){

function showPlanningFormProduct(){

    var value = this.value;
   if(value){
    var url = "{{ route('modulos.list') }}" + '/' + value;
 

 $.get(url, function(data){
     $('#tablaids').empty().append(data);
 });
   }else{
    $('#tablaids').empty();
   }
    
}

$('#sistemas').change(showPlanningFormProduct);   


});
    </script> 

@endsection
