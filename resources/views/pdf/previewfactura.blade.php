

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>Factura</title>
    <style>
        table {
            border-collapse: collapse;
        }
        td {
            font-family: Arial, Helvetica, sans-serif;
        }
        th{
            font-family: Arial, Helvetica, sans-serif;
        }
    </style>
</head>
<body>
<br><br><br><br><br><br><br>
<br>
<h4 style="color: black">FACTURA NRO: L{{ str_pad(0, 4, "0", STR_PAD_LEFT)}}</h4>
<table width="50%" style=" border: 1px solid black;" >
    <tr>
        @if (isset($quotation->credit_days))
            <td style="width: 10%; font-size: 12px; border: 1px solid black;">Fecha de Emisión:</td>
            <td style="width: 10%; font-size: 12px; border: 1px solid black;"> {{date('d-m-Y',strtotime($newDate))}} </td>

        @else
            <td style="width: 10%; font-size: 12px; border: 1px solid black;">Fecha de Emisión:</td>
            <td style="width: 10%; font-size: 12px; border: 1px solid black;">{{date('d-m-Y',strtotime($newDate))}} </td>
        @endif
    </tr>
</table>
<table width="100%" style=" border: 1px solid black;" >
    <tr>
        <td style="font-size: 12px">Nombre / Razón Social:  {{ $quotation->clients['name'] }}</td>
    </tr>
</table>
<table width="100%" style=" border: 1px solid black;" >
    <tr>
        <td style="font-size: 12px;border: 1px solid black;" width="70%">Domicilio Fiscal: {{ $quotation->clients['direction'] }}</td>
        <td style="font-size: 12px;border: 1px solid black;">LICENCIA DE LICORES: &nbsp;  {{ $quotation->clients['licence'] }}</td>
    </tr>
</table>
<table width="100%" style=" border: 1px solid black;" >
    <tr>
        <th style="text-align: center;font-size: 12px;width: 8%;" >Teléfono</th>
        <th style="text-align: center;font-size: 12px;width: 9%;border: 1px solid black;">RIF/CI</th>
        <th style="text-align: center;font-size: 12px;width: 9%;border: 1px solid black;">Días de Credito</th>
        <th style="text-align: center;font-size: 12px;width: 9%;border: 1px solid black;">Fecha Venc.</th>
        <th style="text-align: center;font-size: 12px;width: 12%;border: 1px solid black;">Condiciones Pago.</th>
        <th style="text-align: center;font-size: 12px;border: 1px solid black;">Transporte.</th>
        <th style="text-align: center;font-size: 12px;border: 1px solid black;">Chofer</th>
        <th style="text-align: center;font-size: 12px;border: 1px solid black;">Cedula</th>
        <th style="text-align: center;font-size: 12px;border: 1px solid black;">Placa</th>
    <tr>
        <td style="font-size: 12px;text-align: center;border: 1px solid black;">{{ $quotation->clients['phone1'] }}</td>
        <td style="font-size: 12px;text-align: center;border: 1px solid black;">{{ $quotation->clients['type_code']}}{{ $quotation->clients['cedula_rif'] }}</td>
        @if(empty($quotation->credit_days))
            <td style="font-size: 12px;text-align: center;border: 1px solid black;"></td>
        @else
            <td style="font-size: 12px;text-align: center;border: 1px solid black;">{{ $quotation->credit_days}}</td>
        @endif
        @if(empty($quotation->credit_days))
            <td style="font-size: 12px;text-align: center;border: 1px solid black;"></td>
        @else
            <td style="font-size: 12px;text-align: center;border: 1px solid black;">{{$newVenc}}</td>
        @endif
        @if(empty($quotation->credit_days))

            <td style="font-size: 12px;text-align: center;border: 1px solid black;">Contado</td>
        @else
            <td style="font-size: 12px;text-align: center;border: 1px solid black;">Credito</td>
        @endif
        
        <td style="font-size: 12px;text-align: center;border: 1px solid black;">{{$transport->type}}--{{$modelo->description}}</td>
        <td style="font-size: 12px;text-align: center;border: 1px solid black;">{{ $drivers->name }} {{ $drivers->last_name }}</td>
        <td style="font-size: 12px;text-align: center;border: 1px solid black;">{{ $drivers->type_code }}{{ $drivers->cedula }}</td>
        <td style="font-size: 12px;text-align: center;border: 1px solid black;">{{ $transport->placa}}</td>


    
    </tr>
</table>
<table width="100%" style=" border: 1px solid black;" >
    <tr>
        <td style="font-size: 12px"> Observaciones: {{$quotation->observation}} </td>
    </tr>
</table>
<table width="100%" style="border: 1px solid black;" >
    <tr>
        <td style="font-size: 12px;border: 1px solid black;" width="70%">Destino: {{$quotation->destiny}}</td>
        <td style="font-size: 12px;border: 1px solid black;">LICENCIA DE LICORES: {{$quotation->licence}}</td>
    </tr>
</table>
<table width="100%" style="border: 1px solid black;" >
    <tr>
        <td style="font-size: 12px;border: 1px solid black;" width="70%"> Dirección Entrega: {{$quotation->delivery}}</td>
    </tr>
</table>
<table width="100%" style="border: 0px"  >
    <thead>
    <tr >
        <th style="font-size: 10px;border: 1px solid black;" >COD PRO</th>
        <th style="font-size: 10px;border: 1px solid black;" >CAJAS</th>
        <th style="font-size: 10px;border: 1px solid black;" >B.x.C</th>
        <th style="font-size: 10px;border: 1px solid black;" >Capac</th>
        <th style="font-size: 10px;border: 1px solid black;" >Lts-Gr</th>
        <th style="font-size: 10px;border: 1px solid black;" >°Alcohol</th>
        <th style="font-size: 10px;border: 1px solid black;" >Descripcion Producto</th>
        <th style="font-size: 10px;border: 1px solid black;" >P.V.P.</th>
        <th style="font-size: 10px;border: 1px solid black;" >Desc.</th>
        <th style="font-size: 10px;border: 1px solid black;" >SubTotal</th>
        <th style="font-size: 10px;border: 1px solid black;" >Base Impo.</th>
        <th style="font-size: 10px;border: 1px solid black;" >IVA.</th>
        <th style="font-size: 10px;border: 1px solid black;" >Base Impo. Pcb</th>
        <th style="font-size: 10px;border: 1px solid black;" >IVA Percibido</th>
        <th style="font-size: 10px;border: 1px solid black;" >Total de Venta</th>
        @if ($quotation->clients['coin'] == '0')
        <th style="font-size: 10px;border: 1px solid black;" >Total $</th>
        @endif
    </tr>
    </thead>
    <tbody style="font-size: 10px;border: 1px solid black;">
    @if (empty($inventories_quotations))
    @else
       <?php
                       $total= 0;
            $base_imponible= 0;
            $price_cost_total= 0;

            //este es el total que se usa para guardar el monto de todos los productos que estan exentos de iva, osea retienen iva
            $total_retiene_iva     = 0;
            $retiene_iva           = 0;
            $total_retiene_islr    = 0;
            $total_retiene         = 0;
            $total_iva             = 0;
            $total_base_impo_pcb   = 0;
            $total_iva_pcb         = 0;
            $total_venta           = 0;
            $retiene_islr          = 0;
            $variable_total        = 0;
            $base_imponible_pcb    = $tax_3;
            $iva                   = $tax_1;
            $rate                  = $quotation->bcv;
       ?>
       @foreach ($inventories_quotations as $inventories_quotation)
            @php
                $codigo = $inventories_quotation->code_comercial ?? '' ;        //CODIGO
        
            @endphp

            <tr>
                <td style="font-size: 10px;text-align: center;border: 1px solid black;" >{{$codigo ?? ''}}</td>
                <td style="font-size: 10px;text-align: center;border: 1px solid black;">{{$inventories_quotation->amount_quotation ?? ''}}</td>
                <td style="font-size: 10px;text-align: center;border: 1px solid black;">{{$inventories_quotation->bottle ?? ''}}</td>
                <td style="font-size: 10px;text-align: center;border: 1px solid black;">{{$inventories_quotation->capacity ?? ''}}</td>
                <td style="font-size: 10px;text-align: center;border: 1px solid black;">{{$inventories_quotation->liter ?? ''}}</td>
                <td style="font-size: 10px;text-align: center;border: 1px solid black;">{{$inventories_quotation->degree ?? ''}}</td>
                <td style="font-size: 10px;text-align: left;border: 1px solid black;">{{$inventories_quotation->description ?? ''}}</td>
                <td style="font-size: 10px;text-align: right;border: 1px solid black;">{{number_format($inventories_quotation->price ?? 0,2,",",".")}}</td>
                <td style="font-size: 10px;text-align: center;border: 1px solid black;">{{$inventories_quotation->discount ?? ''}}</td>
                
                <td style="font-size: 10px;text-align: right;border: 1px solid black;">{{number_format(($inventories_quotation->price ?? 0) * $inventories_quotation->amount_quotation,2,",",".") ?? ''}}</td> <!-- Sub-TOTAL-->
             
                <?php
                if($inventories_quotation->retiene_iva_quotation == "1"){
                 
                    $iva = 0;
                    $base_imponible_pcb =0; 

                } 
                ?>

                @if($inventories_quotation->retiene_iva_quotation == "0") <!-- BASE IMPONIBLE-->
                <td style="font-size: 10px;text-align: right;border: 1px solid black; ">{{number_format($inventories_quotation->price * $inventories_quotation->amount_quotation,2,",",".")}}</td>
                @else
                    <td style="font-size: 10px;text-align: right;border: 1px solid black;">0.00</td>
                @endif
                @if($inventories_quotation->retiene_iva_quotation == "0") <!-- IVA-->
                <td style="font-size: 10px;text-align: right;border: 1px solid black;">{{number_format($inventories_quotation->price * $inventories_quotation->amount_quotation * ($iva / 100),2,",",".")}}</td>
                @else
                    <td style="font-size: 10px;text-align: right;border: 1px solid black;">0.00</td>
                @endif
                @if($inventories_quotation->retiene_iva_quotation == "0") <!-- BASE.IMPONIBLE.IVA-->
                <td style="font-size: 10px;text-align: right;border: 1px solid black;">{{number_format($inventories_quotation->price * $inventories_quotation->amount_quotation * $base_imponible_pcb / 100  ,2,",",".")}}</td>
                @else
                    <td style="font-size: 10px;text-align: right;border: 1px solid black;">0.00</td>
                @endif
                @if($inventories_quotation->retiene_iva_quotation == "0") <!-- .IVA PERCIBIDO-->
                <td style="font-size: 10px;text-align: right;border: 1px solid black;">{{number_format($inventories_quotation->price * $inventories_quotation->amount_quotation * ($base_imponible_pcb / 100) * ($iva / 100),2,",",".")}}</td>
                @else
                    <td style="font-size: 10px;text-align: right;border: 1px solid black;">0.00</td>
                @endif
                              <!-- TOTAL DE VENTA -->
                              <td style="font-size: 10px;text-align: right;border: 1px solid black;">{{number_format(number_format($inventories_quotation->price * $inventories_quotation->amount_quotation,2,".","") + number_format($inventories_quotation->price * $inventories_quotation->amount_quotation * ($iva / 100),2,".","") + number_format($inventories_quotation->price * $inventories_quotation->amount_quotation * ($base_imponible_pcb / 100) * ($iva / 100),2,".",""),2,",",".")}}</td>
                              @if ($quotation->clients['coin'] == '0')
                              <!-- TOTAL DE VENTA DOLARES -->
                               <td style="font-size: 10px;text-align: right;border: 1px solid black;">${{number_format((($inventories_quotation->price * $inventories_quotation->amount_quotation) + $inventories_quotation->price * $inventories_quotation->amount_quotation * ($iva / 100) + $inventories_quotation->price * $inventories_quotation->amount_quotation * ($base_imponible_pcb / 100) * ($iva / 100)) / $inventories_quotation->rate ,2,",",".")}}</td>
                            @endif
                           </tr>
                         <?php

                            //Se calcula restandole el porcentaje de descuento (discount)
                            $percentage = (($inventories_quotation->price * $inventories_quotation->amount_quotation) * $inventories_quotation->discount)/100;
                            $total += number_format(($inventories_quotation->price * $inventories_quotation->amount_quotation) - $percentage,2,".","");
                           
                            //$total_venta           +=  $inventories_quotation->price * $inventories_quotation->amount_quotation ;
            
                            
                            
                            
                            if( $inventories_quotation->retiene_iva_quotation == 1) {
                                $total_retiene         +=  0;
                                $total_iva             +=  0;
                                $iva = 0;
                                $base_imponible_pcb =0;
            
                            } else {
            
                                $total_retiene         +=  number_format(($inventories_quotation->price * $inventories_quotation->amount_quotation) - $percentage,2,".","");
                                $total_iva             +=  number_format((($inventories_quotation->price * $inventories_quotation->amount_quotation) - $percentage) * ($iva / 100),2,".","");
            
                            }
                            
                            
                            $base_imponible        = $total_retiene;
                           
                            $total_base_impo_pcb   =  $total_retiene * ($base_imponible_pcb /100);
            
                            $total_iva_pcb         =  ($total_retiene * ($base_imponible_pcb /100)) * ($iva / 100);
                           
                            $total_venta           =   $total + $total_retiene + $total_iva + $total_iva_pcb;
            
                        
                        ?>
                           
                       @endforeach
                   @endif
                   </tbody>
               <tfoot style="border: none; border-bottom: 0px" border="5" >
                   <tr>
                       <td style="color: #fff">.</td>
                       <td style="color: #fff">.</td>
                       <td style="color: #fff">.</td>
                       <td style="color: #fff">.</td>
                       <td style="color: #fff">.</td>
                       <td style="color: #fff">.</td>
                       <td style="color: #fff">.</td>
                       <td style="color: #fff">.</td>
                       <td style="color: #fff">.</td>
                       <td style="color: #fff">.</td>
                       <td style="color: #fff">.</td>
                       <td style="color: #fff">.</td>
                       <td style="color: #fff">.</td>
                       <td style="color: #fff">.</td>
                       <td style="color: #fff">.</td>
                       @if ($quotation->clients['coin'] == '0')
                       <td style="color: #fff">.</td>
                       @endif
                   </tr>
                   
                   <tr style="border: none;">
                       <td style="font-size: 8px;text-align: left;"></td>
                       <td style="font-size: 8px;text-align: center;"></td>
                       <td style="font-size: 8px;text-align: center;"></td>
                       <td style="font-size: 8px;text-align: center;"></td>
                       <td style="font-size: 8px;text-align: center;"></td>
                       <td style="font-size: 8px;text-align: center;"></td>
                       <td style="font-size: 8px;text-align: center;"></td>
                       <td style="font-size: 8px;text-align: center;"></td>
                       <td style="font-size: 10px;text-align: right;"><strong>Bs.</strong></td>
                       <td style="font-size: 10px;text-align: right;" ><strong>{{number_format($total ,2,",",".")}}</strong></td>
                       <td style="font-size: 10px;text-align: right;" ><strong>{{number_format($total_retiene ,2,",",".")}}</strong></td>
                       <td style="font-size: 10px;text-align: right;" ><strong>{{number_format($total_iva ,2,",",".")}}</strong></td>
                       <td style="font-size: 10px;text-align: right;" ><strong>{{number_format($total_base_impo_pcb ,2,",",".")}}</strong></td>
                       <td style="font-size: 10px;text-align: right;" ><strong>{{number_format($total_iva_pcb ,2,",",".")}}</strong></td>
                       <td style="font-size: 10px;text-align: right;" ><strong>{{number_format($total + $total_iva + $total_iva_pcb ,2,",",".")}}</strong></td>
                       @if ($quotation->clients['coin'] == '0')
                       <td style="font-size: 10px;text-align: right;"><strong>${{number_format(($total + $total_iva + $total_iva_pcb) / $rate,2,",",".")}}</strong></td>
                      @endif
                   </tr>
                   @if ($quotation->clients['coin'] == '0')
                    <tr style="border: none;">
                       <td style="font-size: 8px;text-align: left;"></td>
                       <td style="font-size: 8px;text-align: center;"></td>
                       <td style="font-size: 8px;text-align: center;"></td>
                       <td style="font-size: 8px;text-align: center;"></td>
                       <td style="font-size: 8px;text-align: center;"></td>
                       <td style="font-size: 8px;text-align: center;"></td>
                       <td style="font-size: 8px;text-align: center;"></td>
                       <td style="font-size: 8px;text-align: center;"></td>
                       <td style="font-size: 10px;text-align: right;"><strong>REF</strong></td>
                       <td style="font-size: 10px;text-align: right;" ><strong>${{number_format($total / $rate ,2,",",".")}}</strong></td>
                       <td style="font-size: 10px;text-align: right;"><strong>${{number_format($total_retiene / $rate  ,2,",",".")}}</strong></td>
                       <td style="font-size: 10px;text-align: right;"><strong>${{number_format($total_iva / $rate ,2,",",".")}}</strong></td>
                       <td style="font-size: 10px;text-align: right;"><strong>${{number_format($total_base_impo_pcb / $rate ,2,",",".")}}</strong></td>
                       <td style="font-size: 10px;text-align: right;"><strong>${{number_format($total_iva_pcb / $rate ,2,",",".")}}</strong></td>
                       <td style="font-size: 10px;text-align: right;"><strong>${{number_format(($total + $total_iva + $total_iva_pcb) / $rate ,2,",",".")}}</strong></td>
                       <td style="font-size: 10px;text-align: right;"><strong>${{number_format(($total + $total_iva + $total_iva_pcb)/ $rate ,2,",",".")}}</strong></td> 
                   </tr>
                   @endif 
               </tfoot>
</table>
@if ($quotation->clients['coin'] == '0')
<table width="50%" style="margin-top: -30px;">
@else
<table width="50%" style="margin-top: -15px;">   
@endif 
    <tr>
     <td style="font-size: 10px;text-align: left center;" ><strong>ESTA FACTURA VA SIN TACHADURAS NI ENMIENDAS</strong></td>
     <td style="font-size: 10px;text-align: center center;" ></td>
     <td style="font-size: 10px;text-align: left center;" ><strong>TOTAL FACTURA Bs.</strong></td>
 </tr>
 @if ($quotation->clients['coin'] == '0')
 <tr>
     <td style="font-size: 10px;text-align: left;" ><strong>TASA A LA FECHA: {{number_format($rate ,2,",",".")}} BS.</strong></td>
     <td style="font-size: 10px;text-align: center;" ></td>
     <td style="font-size: 10px;text-align: left;" ><strong>TOTAL FACTURA USD.</strong></td>       
 </tr>
 @endif
</table>
</body>
</html>
