<?php 

if($tipo == 'match'){
?>

<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Facturas</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body" >

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

</div> 

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


<?php 

}
elseif($tipo == 'contra'){

    ?>
<div class="modal-header">
 
    <button type="button" class="add_button btn btn-secondary btn-sm">Agregar Contrapartida</button>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body" >

    <div id="form4">

            <form id='pruebaform'>
                @csrf

        <input type="hidden" name="valordebe" value='{{$valormovimiento}}'>
        <input type="hidden" name="valorhaber" value='{{$montohaber}}'>
        <input type="hidden" name="referenciabanco" value='{{$referenciamovimiento}}'>
        <input type="hidden" name="banco" value='{{$bancomovimiento}}'>
        <input type="hidden" name="moneda" value='{{$moneda}}'>
        <input type="hidden" name="fechamovimiento" value='{{$fechamovimiento}}'>
        <input type="hidden" name="descripcionbanco" value='{{$descripcionbanco}}'>
        
        <div class="form-group row clonardiv">
             
              <label for="contrapartida" class="col-md-2 col-form-label text-md-right">
           Contrapartida:
              </label>

              <div class="col-md-4 field_wrapper" >
                
              
            
             
    
              </div>

          </div> 
     

          <button type="button" class="btn btn-primary btn-sm procesarcontrapartida" >Procesar Contrapartida</button>

            </form>
      </div>

 

    </div>
</div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        var x = 1; 
        var contadordiv = 0;
        var maxField = 10; 
        var addButton = $('.add_button'); 
        var wrapper = $('.field_wrapper'); 
      
        $('.procesarcontrapartida').hide(); 
      
        $(addButton).click(function(){ //funcion para cuando agregue un campo
        
        if(contadordiv < maxField){  //si son mayor a 10 no permite mas campos
        var camposelect = "#selecontra"+x;
        var valor = x;
        var fieldHTML = '<div class="col" id='+x+'><br><select  name="contra[]" id="selecontra'+x+'" class="form-control selecontra" required><option value="-1">Seleccione una Contrapartida</option>@foreach($contrapartidas as $index => $value) @if ($value != "Bancos" && $value != "Efectivo en Caja" && $value != "Superavit o Deficit" && $value != "Otros Ingresos" && $value != "Resultado del Ejercicio" && $value != "Resultados Anteriores") <option value="{{ $index }}" {{ old("type_form") == $index ? "selected" : "" }}>{{ $value }} </option> @endif @endforeach</select>';
        var fieldca = '<br><select  id="account_counterpart'+x+'"  name="valorcontra[]" class="form-control account_counterpart" required> <option value="">Seleccionar</option> @if (isset($accounts_inventory)) @foreach ($accounts_inventory as $var) <option value="{{ $var->id }}">{{ $var->description }}</option> @endforeach @endif</select> <br> <a href="javascript:void(0);" class="remove_button btn btn-outline-danger" title="Eliminar Campo">Eliminar</a></div>';
        $('.procesarcontrapartida').show(); 
        $(wrapper).append(fieldHTML+fieldca);
        
        $(camposelect).on('change',function(){
             
           var contrapartida_id = $(this).val();
          
           getSubcontrapartida(contrapartida_id,valor);
       
       });
       $('.add_button').prop( 'disabled', false );
       contadordiv++;
            }else{
              
                $('.add_button').prop( 'disabled', true );

            }
            
            
            x++; 
           
            function getSubcontrapartida(contrapartida_id,valor){
           
           $.ajax({
               url:"{{ route('listcontrapartidanew') }}" + '/' + contrapartida_id,
               beforSend:()=>{
                   alert('consultando datos');
               },
               success:(response)=>{
              
                    var camposelect2 = "#account_counterpart"+valor;
                   let subcontrapartida = $(camposelect2);
                   let htmlOptions = `<option value='' >Seleccione..</option>`;
                
                   if(response.length > 0){
                       response.forEach((item, index, object)=>{
                           let {id,description} = item;
                           htmlOptions += `<option value='${id}'>${description}</option>`

                       });
                   }
                  
                   subcontrapartida.html('');
                   subcontrapartida.html(htmlOptions);
               
 
               },
               error:(xhr)=>{
                   alert('Presentamos inconvenientes al consultar los datos');
               }
           })
       }


        });

        $(wrapper).on('click', '.remove_button', function(e){ 
            e.preventDefault();
            $(this).parent('div').remove(); 
           
           contadordiv--;
           if(contadordiv == 0){
                $('.procesarcontrapartida').hide();
                $('.add_button').prop( 'disabled', false );
 
            }
        });



        
$('.procesarcontrapartida').click(function(e){
      e.preventDefault();
    
    var valor = $('[name="contra"]').val();

    $.ajax({
        method: "POST",
        url: "{{ route('procesarcontrapartidanew') }}",
        data: $('#pruebaform').serialize(),
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


      
    });
    </script>

<?php 

}
?>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>