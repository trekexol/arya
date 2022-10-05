<table class="table table-light2 table-bordered" id="dataTable" >
    <thead>
    <tr> 
        <th>Nro. Factura</th>
        <th>Cliente</th>
        <th>Monto</th>
        <th>Accion</th>

        
    </tr>
    </thead>
    <tbody>
       
            <?php 
                
                foreach($quotations as $quotations){

                    echo "<tr>
                        <td>".$quotations['number_invoice']."</td>
                        <td>".$quotations->clients['name']."</td>
                        <td>".$quotations['amount_with_iva']."</td>";

                        echo "<td>
                            <button type='button' class='btn btn-outline-primary procesarfactura' value='$quotations[amount_with_iva]/$quotations[number_invoice]/$valormovimiento/$quotations[id]/$idmovimiento/$fechamovimiento/$bancomovimiento/$quotations[bcv]'>Procesar</button>
                            </td>";
            
                       echo "</tr>";

                }
                
                ?>
    

          

      
      
    </tbody>
</table>



    <script>


$('.procesarfactura').click(function(e){
      e.preventDefault();
    
    var valor = $(this).val().split('/');
    var montoiva = valor[0];
    var nrofactura = valor[1];
    var montomovimiento = valor[2];
    var id = valor[3];
    var idmovimiento = valor[4];
    var fechamovimiento = valor[5];
    var bancomovimiento = valor[6];
    var tasa = valor[7];
    $.ajax({
        method: "POST",
        url: "{{ route('procesarfact') }}",
        data: {tasa: tasa,bancomovimiento: bancomovimiento,fechamovimiento: fechamovimiento,montoiva: montoiva, nrofactura: nrofactura,montomovimiento: montomovimiento,id: id,idmovimiento: idmovimiento, "_token": "{{ csrf_token() }}"},
             success:(response)=>{
             
                 if(response == true){
                    Swal.fire({
                        icon: 'success',
                        title: 'Exito!',
                        text: 'Factura Procesada Exitosamente!',
                
                
                        })
                 }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Error...',
                        text: 'Error a Procesar Factura!',
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
         })




     


});

$('#dataTable').DataTable({
            "ordering": false,
            "order": [],
            'aLengthMenu': [[10, 20, 30, -1], [10, 20, 30, "All"]]
        });
  
    </script> 
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

