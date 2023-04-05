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
                <div class="card-header text-center font-weight-bold h3">Registro de Sub Segmentos</div>

                <div class="card-body">
                    <form method="POST" id="subsegmentoid">
                        @csrf


                        <div class="form-group row">
                            <label for="description" class="col-md-4 col-form-label text-md-right">Descripción</label>

                            <div class="col-md-6">
                                <input id="description" type="text" class="form-control @error('description') is-invalid @enderror" name="description" value="{{ old('description') }}" required autocomplete="description">

                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="segmento" class="col-md-4 col-form-label text-md-right">Segmento</label>

                            <div class="col-md-6">
                            <select class="form-control" id="segment_id" name="segment_id">
                                <option value="">Seleccione..</option>
                                @foreach($segments as $segment)
                                    <option value="{{ $segment->id }}" {{ old('Segments') }}>{{ $segment->description }}</option>
                                @endforeach

                            </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="status" class="col-md-4 col-form-label text-md-right">Status</label>

                            <div class="col-md-6">
                            <select class="form-control" name="status" id="status">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
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
$("#subsegmentoid").validate({

        rules: {
            description: {
                    required: true,
                    minlength: 3,
                    maxlength: 255,
                },
                segment_id: {
                    required: true,
                },


        },

        messages:{
            description: {
                    required: "Ingrese Descripcion",
                    maxlength: "La Descripcion no puede ser mayor a 255 digitos",
                    minlength: "La Descripcion no puede ser menor a 3 digitos"
                    },
                    segment_id: {
                    required: "Seleccione un Segmento",
                },


        },

        submitHandler: function (form) {



            $.ajax({
            type: "post",
            url: "{{ route('subsegment.store') }}",
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
