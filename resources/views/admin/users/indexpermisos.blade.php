@extends('admin.layouts.dashboard')

@section('content')

<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
        <div class="col-sm-7">
            <h2>Asignar Modulo al usuario {{ $name_user ?? ''}}</h2>
        </div>
    </div>
  </div>
  <!-- /.container-fluid -->
  {{-- VALIDACIONES-RESPUESTA--}}
  @include('admin.layouts.success')   {{-- SAVE --}}
  @include('admin.layouts.danger')    {{-- EDITAR --}}
  @include('admin.layouts.delete')    {{-- DELELTE --}}
  {{-- VALIDACIONES-RESPUESTA --}}


  <div class="container-fluid">
  <div class="form-row">
  <div class="form-group col-md-3">
        <select id="sistemas" class="form-control">
          <option value="" >Seleccione Sistema</option>
          @foreach($sistemas as $sistemas)

          <option value="{{$sistemas->id_sistema.','.$id_user.','.$sistemas->sistema}}">{{$sistemas->sistema}}</option>
      
          @endforeach
        </select>
  </div>
  <div class="form-group col-md-3" id="btomax">
  
  </div>
  <div class="form-group col-md-3" id="btomaxeli">
  
</div>
  <div class="form-group col-md-2" >
    <a href="{{ route('users.createAssignModules',['id_user' => $id_user] ?? 'users') }}" class="btn btn-danger">
        Volver
    </a>
  </div>
  </div>
  </div>

  <div class="card-body">
    <div class="container">
    <div class="table-responsive" id="tablaids">
  
        </div>
    </div>
</div>


@endsection
@section('javascript')
    <script>

  
//envia el post
 $(function(){

function showPlanningFormProduct(){
    var value = this.value;
    
   if(value){
    var url = "{{ route('modulos.list') }}" + '/' + value;

    var datosselect = value.split(',');


    var buthml = "<button class='btn btn-outline-primary botonenviomodulototal' value="+datosselect[0]+','+datosselect[1]+">Permiso Total " +datosselect[2]+ "</button>";
    var buthmleli = "<button class='btn btn-outline-danger eliminarbotonenviomodulototal' value="+datosselect[0]+','+datosselect[1]+">Elimiar Todo " +datosselect[2]+ "</button>";

 $.get(url, function(data){
    $('#btomax').empty().append(buthml);
    $('#btomaxeli').empty().append(buthmleli);
    $('#tablaids').empty().append(data);
 });
   }else{
    $('#btomax').empty();
    $('#tablaids').empty();
  
   }
    
}

$('#sistemas').change(showPlanningFormProduct);


});


$('#btomax').on('click', '.botonenviomodulototal', function(e){
      e.preventDefault();
    
      var value = $(this).val();
      var nroactas = $(this).val().split(',');
      var idsistema = nroactas[0];
      var iduser = nroactas[1];
     
      var urls = "{{ route('modulos.list') }}" + '/' + value;

        
    $.ajax({
        method: "POST",
        url: "{{ route('modulos.insertmasivo') }}",
        data: {idsistema: idsistema, iduser: iduser, "_token": "{{ csrf_token() }}"},
             success:(response)=>{
             
                 if(response == true){
                    $.get(urls, function(data){
              
              $('#tablaids').empty().append(data);
          });
           
                 }else{
                    Swal.fire({
                        icon: 'info',
                        title: 'Exito!',
                        html: 'Error al Asignar Permiso',
                
                
                        })
 


                 }
                
             
                
             
             },
             error:(xhr)=>{
              
          
       
             
                Swal.fire({
                        icon: 'info',
                        title: 'Exito!',
                        html: 'Error al Asignar Permiso',
                
                
                        })
             }
         })





});





$('#btomaxeli').on('click', '.eliminarbotonenviomodulototal', function(e){
      e.preventDefault();
    
      var value = $(this).val();
      var nroactas = $(this).val().split(',');
      var idsistema = nroactas[0];
      var iduser = nroactas[1];
     
      var urls = "{{ route('modulos.list') }}" + '/' + value;

        
    $.ajax({
        method: "POST",
        url: "{{ route('modulos.eliminarmasivo') }}",
        data: {idsistema: idsistema, iduser: iduser, "_method": "DELETE", "_token": "{{ csrf_token() }}"},
             success:(response)=>{
             
                 if(response == true){
                    $.get(urls, function(data){
              
              $('#tablaids').empty().append(data);
          });
           
                 }else{
                    Swal.fire({
                        icon: 'info',
                        title: 'Exito!',
                        html: 'Error al Eliminar Permiso',
                
                
                        })
 


                 }
                
             
                
             
             },
             error:(xhr)=>{
              
          
       
             
                Swal.fire({
                        icon: 'info',
                        title: 'Exito!',
                        html: 'Error al Eliminar Permiso',
                
                
                        })
             }
         })





});


    </script> 

@endsection
