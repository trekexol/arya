<div id="otratabla">
<table class="table table-light2 table-bordered" id="dataTable" >
    <thead>
    <tr> 
        <th>Módulo</th>
        <th>Consultar</th>
        <th>Agregar</th>
        <th>Modificar</th>
        <th>Eliminar</th>   
        <th></th>       
    </tr>
    </thead>
    <tbody>
       <input type="hidden" class="idsistema" id="idsistema" name="idsistema" value="{{$idsistema.','.$idusuario}}"/>
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


                    echo "<td><button type='button' class='btn btn-outline-warning btn-sm eliminarpermiso' value='$modulos[id]/$idusuario'>Quitar Permiso</button></td>";

                       echo "</tr>";

                }
                
                ?>
    

          

      
      
    </tbody>
</table>
</div>


    <script>
 var value = $("#idsistema").val();

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
              

                    if(tipopermiso != 'consultar'){

                        var per = 'consultar';

                    $("#"+nombremodulo+iduser+per).hide();
                   $("#"+per+nombremodulo+iduser).html("<span class='badge badge-pill badge-success'>ACTIVO</span>");

                   $("#"+nombremodulo+iduser+tipopermiso).hide();
                   $("#"+tipopermiso+nombremodulo+iduser).html("<span class='badge badge-pill badge-success'>ACTIVO</span>");

                    }else{

                        $("#"+nombremodulo+iduser+tipopermiso).hide();
                   $("#"+tipopermiso+nombremodulo+iduser).html("<span class='badge badge-pill badge-success'>ACTIVO</span>");
                    }

                  
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




$('.eliminarpermiso').click(function(e){
      e.preventDefault();
    
      var nroactas = $(this).val().split('/');
      var id = nroactas[0];
      var iduser = nroactas[1];

     
      console.log(value);
      var urls = "{{ route('modulos.list') }}" + '/' + value;



    $.ajax({
        method: "POST",
        url: "{{ route('modulos.eliminarpermiso') }}",
        data: {id: id, iduser: iduser, "_method": "DELETE", "_token": "{{ csrf_token() }}"},
             success:(response)=>{
             
                 if(response == true){
              

                $.get(urls, function(data){
              
              $('#otratabla').empty().append(data);
                     })

                  
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

$('#dataTable').DataTable({
            "ordering": false,
            "order": [],
            'aLengthMenu': [[100], ["All"]]
        });
  
    </script> 
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

