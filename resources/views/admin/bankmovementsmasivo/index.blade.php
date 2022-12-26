@extends('admin.layouts.dashboard')

@section('content')


  <!-- /.container-fluid -->
  {{-- VALIDACIONES-RESPUESTA--}}
  @include('admin.layouts.success')   {{-- SAVE --}}
  @include('admin.layouts.danger')    {{-- EDITAR --}}
  @include('admin.layouts.delete')    {{-- DELELTE --}}
  {{-- VALIDACIONES-RESPUESTA --}}
<!-- DataTales Example -->


<div class="container-fluid">
    <form method="post" action="{{ route('pdflibro') }}"   target="print_popup" onsubmit="window.open('about:blank','print_popup','width=1000,height=800');">
        @csrf
    <div class="form-row">
        <div class="form-group col-md-2">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#deleteModal" >Subir Movimientos</button>
        </div>  
        <div class="form-group col-md-3">
            <select class="form-control" name="bancos" id="bancos">
                <option value="">Seleccione Banco.</option>
                @if($bancosmasivos)
                    @foreach($bancosmasivos as $bancosmasivos)
                        <option value="{{$bancosmasivos->banco}}">{{$bancosmasivos->banco}}</option>
                    @endforeach
                @endif
            </select>
        </div>

        <div class="form-group col-md-3">
            <select class="form-control" name="fechabancos" id="fechabancos">
                <option>Seleccione Fecha..</option>
            </select>
        </div>


        <div class="form-group col-md-4" >
            <input type="submit" id="libromayor" class="btn btn-primary" value="Libro Mayor">

          </div>
        </form>

        </div>
 
</div>
<br>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-center font-weight-bold h3">Movimientos Bancarios MASIVOS</div>

                <div class="card-body">
                        <div class="table-responsive" id="datosbancos">



                        </div>
    </div>
</div>
</div>
</div>
</div>




    <!-- Delete Warning Modal -->
    <div class="modal modal-danger fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Subir Movimientos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="fileForms" enctype="multipart/form-data" >
                        @csrf
                        <div class="form-group col-md-8">
                            <label for="inputState">Banco</label>
                            <select class="form-control" name="banco" id="banco">
                              <option value="">Seleccione..</option>
                              <option value="Bancamiga">Bancamiga</option>
                              <option value="Banco Banesco">Banesco</option>
                              <option value="Banco Banplus">Banplus</option>
                              <option value="Banplus Custodia">Banplus Custodia</option>
                              <option value="Banco del Tesoro">Banco del Tesoro</option>
                              <option value="Mercantil">Mercantil</option>
                              <option value="Chase">Chase</option>
                              <option value="BOFA">BOFA</option>
                            </select>
                            

                          </div>
                        <div id="muestrasbanco"></div>

                          <div class="form-group col-md-12">
                            <input required id="file" type="file" value="import" name="file" class="form-control-file" accept=".xlsx, .csv, .txt, .jsp">

                          </div>
                          <div id="muestrasfile" ></div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Importar</button>
                </div>
                </form>
            </div>
        </div>
      </div>



@endsection

@section('javascript')
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

<script type="text/javascript">


$(document).ready(function(){



/*********************************VALIDADOR DE FORMULARIO************************************/
$("#fileForms").validate({

        rules: {
            banco: "required",
            file: "required",

        },

        messages:{
            banco: "Seleccione un Banco",
            file: "Agregue un Archivo",


        },


/*MODIFICANDO PARA MOSTRAR LA ALERTA EN EL LUGAR QUE DESEO CON UN DIV*/
    errorPlacement: function(error, element) {

        if(element.attr("name") == "banco") {

        $("#muestrasbanco").append(error);

        }

        if(element.attr("name") == "file") {

        $("#muestrasfile").append(error);

        }

        },

        submitHandler: function (form) {



            $.ajax({
            type: "post",
            url: "{{ route('importmovimientos') }}",
            dataType: "json",
            data: new FormData( form ),
            processData: false,
            contentType: false,
            //success:(response)=>{
            success:function(response){
             if(response.error == true){
                Swal.fire({
                        icon: 'info',
                        title: 'Exito!',
                        html: response.msg,


                        })
                                        setTimeout("location.reload()", 2500);

             }else{

                Swal.fire({
                        icon: 'info',
                        title: 'Error..',
                        html: response.msg,
                        })
             }




         },
         error:(response)=>{


            Swal.fire({
                    icon: 'error',
                    title: 'Error...',
                    html: response.msg,
                    });
         }
            });



            return false; // required to block normal submit since you used ajax
        }
    }); ///fin $("#registro").validate({

        $("#libromayor").hide();

 $("#bancos").change(function () {

    var url = "{{ route('listarfecha') }}";

        $("#bancos option:selected").each(function () {
            bancos = $(this).val();
            $.post(url,{bancos: bancos,"_token": "{{ csrf_token() }}"}, function(data){
                    $("#fechabancos").empty().append(data);
                    $("#datosbancos").empty().append();
                    $("#libromayor").hide();
            });
        });
    })


    $("#fechabancos").change(function () {

var url = "{{ route('listardatos') }}";

    $("#fechabancos option:selected").each(function () {

        fechabancos = $(this).val();
        $.post(url,{fechabancos: fechabancos,bancos: bancos,"_token": "{{ csrf_token() }}"}, function(data){
                $("#datosbancos").empty().append(data);
                $("#libromayor").show();

        });
    });
})



});
    </script>

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection
