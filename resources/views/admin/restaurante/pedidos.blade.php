@extends('admin.layouts.dashboard')
@section('content')
  {{-- VALIDACIONES-RESPUESTA--}}
  @include('admin.layouts.success')   {{-- SAVE --}}
  @include('admin.layouts.danger')    {{-- EDITAR --}}
  @include('admin.layouts.delete')    {{-- DELELTE --}}
  {{-- VALIDACIONES-RESPUESTA --}}
<style>
/* Estilos generales para todas las pantallas */
body {
  font-size: 16px;
}

/* Estilos para pantallas pequeñas (hasta 600px de ancho) */
@media only screen and (max-width: 600px) {
  body {
    font-size: 12px;
  }
}

/* Estilos para pantallas medianas (entre 600px y 900px de ancho) */
@media only screen and (min-width: 600px) and (max-width: 900px) {
  body {
    font-size: 14px;
  }
}

/* Estilos para pantallas grandes (más de 900px de ancho) */
@media only screen and (min-width: 900px) {
  body {
    font-size: 14px;
  }
}


.circulo {
    width: 3rem;
    height: 3rem;
    border-radius: 20%;
    justify-content: center;
    align-items: center;
    text-align: center;
    padding: 1%;
    color: #fff;
    margin: 5px;
    display: inline-block;
    font-size: 10px;
}

#accordionSidebar {
 display: none;
}

</style>
<nav class="nav nav-tabs justify-content-center">
    <ul class="nav nav-dark" id="myTab" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" id="nav-home-tab" data-toggle="tab" data-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Pedidos Actuales</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="nav-profile-tab" data-toggle="tab" data-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Mesas</a>
        </li>
</nav>

  <div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active container" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
        <br>
        @if(count($arreglo))
        <div class="container">
            <div class="row">
                @foreach ($arreglo as $cantidadmesas2)
                <div class="col-md-6 mb-2">
                    <div class="card border-dark ">
                        <div class="card border-dark  mb-2 card-header"><h5 class="card-title">Pedido Mesa {{ $cantidadmesas2['numero'] }}</h5></div>
                        <div class="card-body text-dark ">
                            <ul>
                                @foreach ($cantidadmesas2['producto'] as $producto)
                                <li >{{ $producto['cantidad'].' '.$producto['producto'] }}</li>
                                @endforeach
                              </ul>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        @endif

    </div>


    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">

        <div class="modal modal-danger fade bd-example-modal-md" id="MatchModal" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered modal-md" role="document">
                <div class="modal-content" id="modalfacturas">

                </div>
            </div>
          </div>
          <div align="center" class="container">
            <div class="row">
              <div class="col-sm-6 col-md-12">
                <?php $numero = 0; ?>
            @foreach ($cantidadmesas as $cantidadmesas)

            @if($numero == 10)
            <br>
            <?php $numero = 0; ?>
            @endif

            @if($cantidadmesas->estatus == 0)
           <span class="circulo pedido" data-toggle="modal" data-target="#MatchModal" data-nro="{{ $cantidadmesas->numero.'/editar' }}" style="background-color: #a10909;"> M {{ $cantidadmesas->numero }}</span>

            @else

            <span class="circulo pedido" data-toggle="modal" data-target="#MatchModal" data-nro="{{ $cantidadmesas->numero.'/agregar' }}" style="background-color: rgb(9, 161, 9);"> M {{ $cantidadmesas->numero }}</span>

            @endif
            <?php $numero++; ?>

            @endforeach

              </div>
            </div>
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
