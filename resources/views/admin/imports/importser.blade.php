
    <!-- container-fluid -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="row py-lg-2">
            <div class="col-md-6">
                <h2>Facturas de Servicios</h2>
            </div>
        </div>
    </div>


    <div class="card shadow mb-4">

        <div class="card-body">

            <div class="table-responsive" >
                <form method="POST" action="{{ route('imports.create') }}">
                    @csrf
                    <input type="hidden" name="idfact" id="idfact" value="{{ encrypt($idfact)}}"/>
                    <input type="hidden" name="serv" id="serv" value="true"/>
                <table class="table table-light2 table-bordered extablas"  >

                    <thead>
                    <tr>
                        <th class="text-center">Seleccione</th>
                        <th class="text-center">N° de Factura</th>
                        <th class="text-center">N° de Control/Serie</th>
                    </tr>
                    </thead>

                    <tbody>
                    @if (count($quotationss) > 0)


                        @foreach($quotationss as $quotation)

                                            <tr>
                                                <td>
                                                <input  name="idcheck" id="idcheck" class="idcheck" type="checkbox" value="{{ $quotation->invoice}}">
                                                </td>
                                                <td class="text-center">{{$quotation->invoice}}</td>
                                                <td class="text-center">{{$quotation->serie}}</td>
                                            </tr>
                        @endforeach

                    @endif
                    </tbody>

                </table>
                <div id="mostrar"></div>
                <div align="center">
                    <button  class="btn btn-primary" type="submit">Agregar</button>
                </div>
                <br>
            </form>
            </div>

        </div>

    </div>

    <script type='text/javascript' charset='utf-8'>


        $(document).ready(function() {


$('.idcheck').click(function(){
   activo = $(this).is(':checked');
   valor = $(this).val();


   if(activo == true){
    var camposopcionales = '<input  name="idservi[]" id="idservi[]" type="hidden" class="'+valor+'" value="'+valor+'">';
    $('#mostrar').append(camposopcionales);


   }

   if(activo == false){
    $( "."+valor ).remove( );

   }


});



           /*********************************DATATABLE************************************/
           $('.extablas').DataTable( {

                'scrollCollapse': true,
                'paging':         true,


                ordering:  true


              } );

           /*********************************FIN DATATABLE************************************/



         } );
       </script>
