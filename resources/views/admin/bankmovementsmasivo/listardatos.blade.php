
<div align="center">
<span class="badge badge-pill badge-success bsfc" data-toggle="modal" data-target="#MatchModal" name="matchvalue" >Buscar Match Facturas Compras</span>
<span class="badge badge-pill badge-success bsfv" data-toggle="modal" data-target="#MatchModal" name="matchvalue" >Buscar Match Facturas Ventas</span>
</div>
    <table class="table table-light2 table-bordered dataTableclass" id="dataTable" >
                <thead>
                    <tr>
                        <th class="text-center"><small>Fecha</small></th>
                        <th class="text-center"><small>Referencia</small></th>
                        <th class="text-center"><small>Descripcion</small></th>
                        <th class="text-center"><small>Debe</small></th>
                        <th class="text-center"><small>Haber</small></th>
                        <th class="text-center"><small>Accion</small></th>
                        <th class="text-center"><small>Eliminar</small></th>
                    </tr>
                </thead>

                    <tbody id="pruebatbody">

                        @if (!empty($movimientosmasivoss))

                            @foreach ($movimientosmasivoss as $var)

                            @php
                                if($var->moneda == 'dolares'){
                                    $signo = '$';
                                }else{
                                    $signo = 'Bs';
                                }

                            @endphp

                            <tr id="{{$var->id_temp_movimientos}}">
                            <td><small>{{ date('d-m-Y', strtotime( $var->fecha ?? '')) }}</small></td>
                            <td class="text-center">{{$var->referencia_bancaria ?? ''}}</td>

                            <td><small>{{$var->descripcion ?? ''}}</small></td>
                            <td><small>{{ $var->debe.$signo }}
                                @if($var->tipofacc == 'compra')
                                @php

                                    if($var->conta == 'debe'){
                                        $plata = $var->debe;
                                    }else{
                                        $plata = $var->haber;
                                    }
                                @endphp

                                        @if($var->matchc == 1)

                                        <span class="badge badge-pill badge-success" data-toggle="modal" data-target="#MatchModal" name="matchvalue" data-id="{{$plata.'/'.$var->id_temp_movimientos.'/'.$var->fecha.'/'.$var->banco.'/match/'.$var->moneda.'/'.$var->conta.'/'.$var->tipofacc}}">Match Facturas Compras</span>

                                       @elseif($var->matchc == 0)
                                        <input type="checkbox" id="checkdebe" name="checkdebe" class="checkdebe" value="{{$var->debe}}">
                                       @else

                                        <span class="badge badge-pill badge-success procesarfactura"  data-id="{{$var->amount_with_ivac.'/'.$var->matchc.'/'.$plata.'/'.$var->idinvoicec.'/'.$var->id_temp_movimientos.'/'.$var->fecha.'/'.$var->banco.'/'.$var->bcvc.'/'.$var->conta.'/'.$var->moneda.'/'.$var->tipofacc}}">Match Factura Compra {{$var->matchc}}</span>

                                        @endif
                                @endif
                                </small>
                            </td>
                            <td><small>{{ $var->haber.$signo }}

                                @if($var->tipofac == 'venta')
                                @php

                                    if($var->conta == 'debe'){
                                        $plata = $var->debe;
                                    }else{
                                        $plata = $var->haber;
                                    }
                                @endphp

                                        @if($var->match == 1)

                                        <span class="badge badge-pill badge-success" data-toggle="modal" data-target="#MatchModal" name="matchvalue" data-id="{{$plata.'/'.$var->id_temp_movimientos.'/'.$var->fecha.'/'.$var->banco.'/match/'.$var->moneda.'/'.$var->conta.'/'.$var->tipofac}}">Match Facturas Ventas</span>

                                       @elseif($var->match == 0)
                                       <input type="checkbox" id="checkhaber" name="checkhaber" class="checkhaber" value="{{$var->haber}}">
                                       @else

                                        <span class="badge badge-pill badge-success procesarfactura"  data-id="{{$var->amount_with_iva.'/'.$var->match.'/'.$plata.'/'.$var->idinvoice.'/'.$var->id_temp_movimientos.'/'.$var->fecha.'/'.$var->banco.'/'.$var->bcv.'/'.$var->conta.'/'.$var->moneda.'/'.$var->tipofac}}">Match Factura Venta {{$var->match}}</span>

                                        @endif
                                @endif
                                </small>
                            </td>
                            <td>
                                <small><span class="badge badge-pill badge-warning" data-toggle="modal" data-target="#MatchModal" name="matchvalue" data-id="{{$var->debe.'/'.$var->id_temp_movimientos.'/'.$var->fecha.'/'.$var->banco.'/contra/'.$var->haber.'/'.$var->referencia_bancaria.'/'.$var->moneda.'/'.$var->descripcion}}">Contrapartida</span></small>
                                <small><span class="badge badge-pill badge-primary" data-toggle="modal" data-target="#MatchModal" name="matchvalue" data-id="{{$var->debe.'/'.$var->id_temp_movimientos.'/'.$var->fecha.'/'.$var->banco.'/transferencia/'.$var->haber.'/'.$var->referencia_bancaria.'/'.$var->moneda.'/'.$var->descripcion}}">Transferencia</span></small>
                                <small><span class="badge badge-pill badge-info" data-toggle="modal" data-target="#MatchModal" name="matchvalue" data-id="{{$var->debe.'/'.$var->id_temp_movimientos.'/'.$var->fecha.'/'.$var->banco.'/deposito/'.$var->haber.'/'.$var->referencia_bancaria.'/'.$var->moneda.'/'.$var->descripcion}}">Deposito</span></small>

                            </td>
                            <td><small><span class="badge badge-pill badge-danger"  name="matchvalueliminar" data-id="{{$var->id_temp_movimientos}}">Eliminar</span></small>
                            </td>
                            </tr>
                            @endforeach

                    @endif
                </tbody>
            </table>
            <div class="col-md-12 text-center">
                <ul class="pagination pagination-lg pager" id="developer_page"></ul>
            </div>
            <div class="modal modal-danger fade" id="MatchModal" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                    <div class="modal-content" id="modalfacturas">

                    </div>
                </div>
              </div>

<script type="text/javascript">


$(document).ready(function(){
    $('.bsfc').hide();
    $('.bsfv').hide();
/********MODAL CUANDO CONSIGUE MATCH**********/

    $('[name="matchvalue"]').click(function(e){
        e.preventDefault();
        var value = $(this).data('id');
        var url = "{{ route('facturasmovimientos') }}";

        $.post(url,{value: value,"_token": "{{ csrf_token() }}"},function(data){
            $("#modalfacturas").empty().append(data);
         });
        });

    $('.procesarfactura').click(function(e){
      e.preventDefault();

    var valor = $(this).data('id').split('/');
    var montoiva = valor[0];
    var nrofactura = valor[1];
    var montomovimiento = valor[2];
    var id = valor[3];
    var idmovimiento = valor[4];
    var fechamovimiento = valor[5];
    var bancomovimiento = valor[6];
    var tasa = valor[7];
    var conta = valor[8];
    var moneda = valor[9];
    var tipofac = valor[10];
    $.ajax({
        method: "POST",
        url: "{{ route('procesarfact') }}",
        data: {tipofac: tipofac,moneda: moneda,conta: conta,tasa: tasa,bancomovimiento: bancomovimiento,fechamovimiento: fechamovimiento,montoiva: montoiva, nrofactura: nrofactura,montomovimiento: montomovimiento,id: id,idmovimiento: idmovimiento, "_token": "{{ csrf_token() }}"},
             success:(response)=>{

                 if(response == true){
                    Swal.fire({
                        icon: 'success',
                        title: 'Exito!',
                        text: 'Factura '+nrofactura+' Procesada Exitosamente!',


                        })
                        $("#"+idmovimiento).empty().append();
                        $('#MatchModal').modal('hide');
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


    $('[name="matchvalueliminar"]').click(function(e){
                    e.preventDefault();
                   var idmov = $(this).data('id');

                   $.ajax({
            type: "post",
            url: "{{ route('eliminarmovimiento') }}",
            dataType: "json",
            data: {idmov: idmov, "_method": "DELETE", "_token": "{{ csrf_token() }}"},
            success:function(response){
             if(response.error == true){
                Swal.fire({
                        icon: 'info',
                        title: 'Exito!',
                        html: response.msg,


                        })
                        $("#"+idmov).empty().append();

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



                 });

/***********datatable*********************/
var tabladata = $('#dataTable').DataTable({
    'paging':         true,
'aLengthMenu': [[10], [10]]
});
/*******************************************/

var valihaber = 0; ///para verificar que hay check de haber activos
var validebe = 0; ///para verificar que hay check de debe activos

tabladata.$(".checkhaber").change(function () {
var sList = 0;
var totalhaber = 0;
tabladata.$(".checkhaber:checked").each(function () {

    sThisVal = this.checked;
    sList += sThisVal;
    totalhaber += parseFloat($(this).val());

});

if(sList > 0){
    valihaber = 1;
    tabladata.$('.checkdebe').hide();
    tabladata.$('.checkdebe').prop( "disabled", true );
}else{
    valihaber = 0;
    tabladata.$('.checkdebe').show();
    tabladata.$('.checkdebe').prop( "disabled", false );

}


/*****VALIDO QUE SI ESTAN ACTIVO 2 CHECK DE HABER Y DEBE SE LIMPIE TODO DE MANERA DE EVITAR ERRORES*****/

if(validebe > 0 && valihaber > 0){
    tabladata.$('.checkdebe').hide();
    tabladata.$('.checkdebe').prop( "disabled", true );

    tabladata.$('.checkhaber').hide();
    tabladata.$('.checkhaber').prop( "disabled", true );

    tabladata.$('.checkdebe:checked').prop( "checked", false );
    tabladata.$('.checkhaber:checked').prop( "checked", false );
    }
/**********************************************************/
});


tabladata.$(".checkdebe").change(function () {
var sList = 0;
var totaldebe = 0;
tabladata.$(".checkdebe:checked").each(function () {

    sThisVal = this.checked;
    sList += sThisVal;
    totaldebe += parseFloat($(this).val());

});
if(sList > 0){
    validebe = 1;
    tabladata.$('.checkhaber').hide();
    tabladata.$('.checkhaber').prop( "disabled", true );
    tabladata.$('.checkhaber:checked').prop( "checked", false );

}else{
    validebe = 0;
    tabladata.$('.checkhaber').show();
    tabladata.$('.checkhaber').prop( "disabled", false );
}


/*****VALIDO QUE SI ESTAN ACTIVO 2 CHECK DE HABER Y DEBE SE LIMPIE TODO DE MANERA DE EVITAR ERRORES*****/
if(validebe > 0 && valihaber > 0){
    tabladata.$('.checkdebe').hide();
    tabladata.$('.checkdebe').prop( "disabled", true );

    tabladata.$('.checkhaber').hide();
    tabladata.$('.checkhaber').prop( "disabled", true );

    tabladata.$('.checkdebe:checked').prop( "checked", false );
    tabladata.$('.checkhaber:checked').prop( "checked", false );
    }
/**********************************************************************/
});





});

    </script>
