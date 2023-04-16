@if($valor == 'calcular')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-center text-primary">CALCULAR IMPORTACION</h6>
    </div>

    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
            <tr>
                <th>Producto</th>
                <th>Costo Actual</th>
                <th>Precio de Venta Actual</th>
                <th>Costo de Importacion</th>
                <th>Precio de Venta de Importacion </th>

            </tr>
            </thead>
            <tbody>
                @if (count($calcular) > 0)

                    @foreach ($calcular as  $var)
                        <tr>

                        <td class="text-center">{{$var->description}}</td>
                        <td class="text-center text-danger">{{$var->price_buy}}</td>
                        <td class="text-center text-danger">{{$var->price}}</td>
                        <td class="text-center text-primary">{{$var->costo}}</td>
                        <td class="text-center text-primary">{{$var->precioventa}}</td>

                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        </div>
    </div>
    <div class="card-footer py-3">
        <h6 class="m-0 font-weight-bold text-center text-danger">Si Acepta la actualizacion de los nuevos montos de costo y venta de los productos de esta importacion no se podra reversar</h6>
    </div>
    <div class="card-footer py-3 text-center">
        <button class="btn btn-primary btn-sm procesarcalculos">Aceptar</button>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function(){

$('.procesarcalculos').click(function(e){
      e.preventDefault();
      idimport = "{{ $id }}";



    Swal.fire({
        title: 'Estas Seguro de Continuar?',
        text: "Si Acepta no Hay Reversa de Montos!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, Continuar!'
    }).then((result) => {
    if (result.isConfirmed) {

    $.ajax({
        method: "POST",
        url: "{{ route('imports.procesaropciones') }}",
        data: {"_token": "{{ csrf_token() }}",idimport: idimport,idvalor: 'calcular'},
             success:(response)=>{

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
             error:(xhr)=>{
                Swal.fire({
                        icon: 'error',
                        title: 'Error...',
                        text: 'Error a Procesar!',
                        });
             }
         })//fin ajax
  }//fin  if (result.isConfirmed) {
})//fin de .then((result) =>

}); //fin click

    });//fin document
    </script>
@endif

@if($valor == 'eliminar')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-center text-danger">ELIMINAR IMPORTACION</h6>
    </div>

    <div class="card-body">
        <div class="table-responsive">

        </div>
    </div>
    <div class="card-footer py-3">
        <h6 class="m-0 font-weight-bold text-center text-danger">Desea Eliminar la Importacion Numero : {{ $id }}</h6>
    </div>
    <div class="card-footer py-3 text-center">
        <button class="btn btn-primary btn-sm procesarcalculos">Aceptar</button>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){

$('.procesarcalculos').click(function(e){
      e.preventDefault();
      idimport = "{{ $id }}";

    $.ajax({
        method: "POST",
        url: "{{ route('imports.procesaropciones') }}",
        data: {"_token": "{{ csrf_token() }}",idimport: idimport,idvalor: 'eliminar'},
             success:(response)=>{

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
             error:(xhr)=>{
                Swal.fire({
                        icon: 'error',
                        title: 'Error...',
                        text: 'Error a Procesar!',
                        });
             }
         })//fin ajax

}); //fin click

    });//fin document
    </script>
@endif
