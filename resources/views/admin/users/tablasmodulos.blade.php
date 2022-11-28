<table class="table table-light2 table-bordered" id="dataTable" >
    <thead>
    <tr> 
        <th>Módulo</th>
        <th>Consultar</th>
        <th>Agregar</th>
        <th>Modificar</th>
        <th>Eliminar</th>
        
    </tr>
    </thead>
    <tbody>
       
            <?php 
                
                foreach($arreglom as $modulos){

                    echo "<tr>
                        <td>".$modulos['name']."</td>";

                        if($modulos['consulta'] == 1){
                            echo "<td>
                                <span class='badge badge-pill badge-success'>ACTIVO</span>
                                    </td>"; 
                        }else{

                            $valor = 'consultar';

                            echo "<td id='$valor$modulos[id]$idusuario'> 
                                <button type='button' id='$modulos[id]$idusuario$valor' class='btn btn-info btn-sm botonenviomodulonew' value='$modulos[id]/$idusuario/1/consultar'>Asignar</button>
                              
                                </td>";
                        }
                        
                        if($modulos['agregar2'] == 1){
                            echo "<td>
                                <span class='badge badge-pill badge-success'>ACTIVO</span>
                                    </td>"; 
                        }elseif($modulos['agregar'] == 1){
                            $valor = 'agregar';
                            echo "<td id='$valor$modulos[id]$idusuario'> 
                                <button type='button' id='$modulos[id]$idusuario$valor' class='btn btn-info btn-sm botonenviomodulonew' value='$modulos[id]/$idusuario/$modulos[agregar]/agregar'>Asignar</button>                           
                              
                                </td>";
                        }else{
                            echo "<td></td>";
                        }



                        if($modulos['actualizar2'] == 1){
                            echo "<td>
                                <span class='badge badge-pill badge-success'>ACTIVO</span>
                                </td>";
                        }elseif($modulos['actualizar'] == 1){
                            $valor = 'actualizar';
                            echo "<td id='$valor$modulos[id]$idusuario'> 
                                <button type='button' id='$modulos[id]$idusuario$valor' class='btn btn-info btn-sm botonenviomodulonew' value='$modulos[id]/$idusuario/$modulos[actualizar]/actualizar'>Asignar</button>                           
                              
                                </td>";
                        }else{
                            echo "<td></td>";
                        }




                        if($modulos['eliminar2'] == 1){
                            echo "<td>
                                <span class='badge badge-pill badge-success'>ACTIVO</span>
                                </td>";
                        }elseif($modulos['eliminar'] == 1){
                            $valor = 'eliminar';
                            echo "<td id='$valor$modulos[id]$idusuario'> 
                                <button type='button' id='$modulos[id]$idusuario$valor' class='btn btn-info btn-sm botonenviomodulonew' value='$modulos[id]/$idusuario/$modulos[eliminar]/eliminar'>Asignar</button>                           
                              
                                </td>";
                        }else{
                            echo "<td></td>";
                        }




                       echo "</tr>";

                }
                
                ?>
    

          

      
      
    </tbody>
</table>



    <script>


$('.botonenviomodulonew').click(function(e){
      e.preventDefault();
    
      var nroactas = $(this).val().split('/');
      var nombremodulo = nroactas[0];
      var iduser = nroactas[1];
      var valor = nroactas[2];
      var tipopermiso = nroactas[3];

    $.ajax({
        method: "POST",
        url: "{{ route('modulos.insert') }}",
        data: {nombremodulo: nombremodulo, iduser: iduser,valor: valor,tipopermiso: tipopermiso, "_token": "{{ csrf_token() }}"},
             success:(response)=>{
             
                 if(response == true){
                    alert('Asignado con Exito');
                   $("#"+nombremodulo+iduser+tipopermiso).hide();
                   $("#"+tipopermiso+nombremodulo+iduser).html("<span class='badge badge-pill badge-success'>ACTIVO</span>");
                 }else{
                     alert('Error');
                 }
                
             
                
             
             },
             error:(xhr)=>{
                 alert('Error');
             }
         })




     


});

$('#dataTable').DataTable({
            "ordering": false,
            "order": [],
            'aLengthMenu': [[10, 20, 30, -1], [10, 20, 30, "All"]]
        });
  
    </script> 

