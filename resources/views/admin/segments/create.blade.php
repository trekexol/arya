@extends('admin.layouts.dashboard')

@section('content')
<style>
    .error {

   color: #dc3545;
   font-size:100%;


   }
   </style>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header text-center font-weight-bold h3">Registro de Segmentos</div>

                <div class="card-body">
                    <form method="POST" id="segmentoid">
                        @csrf


                        <div class="form-group row">
                            <label for="description" class="col-md-4 col-form-label text-md-right">Descripción</label>

                            <div class="col-md-6">
                                <input id="description" type="text" class="form-control" name="description" value="{{ old('description') }}" required autocomplete="description">


                            </div>
                        </div>


                    <br>
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                   Registrar Segmento
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('validacion')
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

<script type="text/javascript">

    $(function(){
        soloAlfaNumerico('description');

    });

$(document).ready(function(){

/*********************************VALIDADOR DE FORMULARIO************************************/
$("#segmentoid").validate({

        rules: {
            description: {
                    required: true,
                    minlength: 3,
                    maxlength: 255,
                },


        },

        messages:{
            description: {
                    required: "Ingrese Descripcion",
                    maxlength: "La Descripcion no puede ser mayor a 255 digitos",
                    minlength: "La Descripcion no puede ser menor a 3 digitos"
                    },



        },

        submitHandler: function (form) {



            $.ajax({
            type: "post",
            url: "{{ route('segments.store') }}",
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



});
    </script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection
