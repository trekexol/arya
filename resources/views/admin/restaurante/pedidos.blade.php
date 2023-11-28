@extends('admin.layouts.dashboard')

@section('content')
  <!-- /.container-fluid -->
  {{-- VALIDACIONES-RESPUESTA--}}
  @include('admin.layouts.success')   {{-- SAVE --}}
  @include('admin.layouts.danger')    {{-- EDITAR --}}
  @include('admin.layouts.delete')    {{-- DELELTE --}}
  {{-- VALIDACIONES-RESPUESTA --}}
<?php
    $numero = 0;
?>

<style>
/* Estilos generales para todas las pantallas */
body {
  font-size: 16px;
}

/* Estilos para pantallas pequeñas (hasta 600px de ancho) */
@media only screen and (max-width: 600px) {
  body {
    font-size: 14px;
  }
}

/* Estilos para pantallas medianas (entre 600px y 900px de ancho) */
@media only screen and (min-width: 600px) and (max-width: 900px) {
  body {
    font-size: 18px;
  }
}

/* Estilos para pantallas grandes (más de 900px de ancho) */
@media only screen and (min-width: 900px) {
  body {
    font-size: 16px;
  }
}


.circulo {
    width: 8rem;
    height: 8rem;
    border-radius: 50%;
    justify-content: center;
    align-items: center;
    text-align: center;
    padding: 2rem;
    color: #fff;
    margin: 10px;
    display: inline-block;
}

#accordionSidebar {
    display: none;
}

</style>
<div align="center">
    <?php $numero = 0; ?>
    @foreach ($cantidadmesas as $cantidadmesas)

    @if($numero == 3)
    <br>
    <?php $numero = 0; ?>
    @endif

    @if($cantidadmesas->estatus == 0)
   <span class="circulo pedido" data-toggle="modal" data-target="#MatchModal" data-nro="{{ $cantidadmesas->numero.'/editar' }}" style="background-color: #a10909;"> Mesa {{ $cantidadmesas->numero }}</span>

    @else

    <span class="circulo pedido" data-toggle="modal" data-target="#MatchModal" data-nro="{{ $cantidadmesas->numero.'/agregar' }}" style="background-color: rgb(9, 161, 9);"> Mesa {{ $cantidadmesas->numero }}</span>

    @endif
    <?php $numero++; ?>

    @endforeach

</div>
<div class="modal modal-danger fade bd-example-modal-xl" id="MatchModal" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content" id="modalfacturas">

        </div>
    </div>
  </div>
@endsection
@section('javascript')
    <script>

$(document).ready(function(){
    $('.pedido').click(function(e){
        e.preventDefault();
            var value = $(this).data('nro');
            var url = "{{ route('pedidosmesas') }}";
            $.post(url,{value: value,"_token": "{{ csrf_token() }}"},function(data){
                $("#modalfacturas").empty().append(data);
            });
    });
});

    </script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
@endsection
