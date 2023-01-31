<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 
<title>Nota de Entrega</title>
<style>
  table, td, th {
    border: 1px solid black;
  }
  
  table {
    border-collapse: collapse;
    width: 50%;
  }
  
  th {
    
    text-align: left;
  }

  @page {
                margin: 0cm 0cm;
            }


            body {
                margin-top: 1cm;
                margin-left: 1cm;
                margin-right: 1cm;
                margin-bottom: 4cm;
            }
   .footer {
    width: 100%;
    text-align: center;
    position: fixed;
    margin-top: -65px;
   } 

  .footer {
      bottom: 0px;
  }

  .pagenum:before {
      content: counter(page);
  }


  .pagenum2:after {
      content: counter(page);
  }
  


  footer {
                position: fixed; 
                bottom: 1cm; 
                left: 1cm; 
                right: 1cm;
                height: 4cm;
              
               
            } 

  </style>


</head>

<body>
  <table id="top">
    <tr>
      @if (Auth::user()->company->foto_company != '')     
      <th style="text-align: left; font-weight: normal; width: 10%; border-color: white; font-weight: bold;"> <img src="{{ asset(Auth::user()->company->foto_company ?? 'img/logo.jpg') }}" height="60" class="d-inline-block align-top" alt="">
      </th>
      @endif
      <th style="text-align: left; font-weight: normal; width: 90%; border-color: white; font-weight: bold;"><h4>{{Auth::user()->company->razon_social ?? ''}}  <h5>{{Auth::user()->company->code_rif ?? ''}}</h5> </h4></th>    </tr> 
  </table>
  <br>
  @if(Auth::user()->company->id != 22)
  <h4 style="color: black">NOTA DE ENTREGA NRO: {{ str_pad($quotation->number_delivery_note ?? $quotation->id, 6, "0", STR_PAD_LEFT)}}</h4>
  @else 
  <h4 style="color: black">NOTA DE ENTREGA NRO: <span style="color:red">{{ str_pad($quotation->number_delivery_note ?? $quotation->id, 6, "0", STR_PAD_LEFT)}} </span></h4>
  @endif
 
   
 
<table>
  @if (isset($company->franqueo_postal))
  <tr>
    <th style="font-weight: normal; width: 40%;">Concesión Postal:</th>
    <th style="font-weight: normal;">Nº {{ $company->franqueo_postal ?? ''}}</th>
   
  </tr>
  @endif

  <tr>
    <td style="width: 40%;">Fecha de Emisión:</td>
    @if (isset($quotation->date_delivery_note))
      <td>{{date_format(date_create($quotation->date_delivery_note),"d-m-Y")}}</td>
    @else
      <td></td>
    @endif
  </tr>
  
</table>


<table style="width: 100%;">
  <tr>
    <th style="font-weight: normal; font-size: medium;">Nombre / Razón Social: &nbsp;  {{ $quotation->clients['name'] ?? ''}}</th>
    
   
  </tr>
  <tr>
    <td>Domicilio Fiscal: &nbsp;  {{ $quotation->clients['direction'] ?? ''}}
    </td>
    
    
  </tr>
  
</table>




<table style="width: 100%;">
  <tr>
    <th style="text-align: center; width: 16%;">Teléfono</th>
    <th style="text-align: center; width: 15%;">RIF/CI</th>
    <th style="text-align: center; width: 15%;">N°Ctrl-Serie</th>
    
    @if(Auth::user()->company->id != 22)
    <th style="text-align: center;">N°-Pedido</th>
    @else
    <th style="text-align: center;">N°-Orden</th>
    @endif
    <th style="text-align: center;">Condición de Pago</th>
    <th style="text-align: center;">Transporte</th>
   
  </tr>
  <tr>
    <td style="text-align: center;">{{ $quotation->clients['phone1'] ?? ''}}</td>
    <td style="text-align: center;">{{ $quotation->clients['type_code'] ?? ''}} {{ $quotation->clients['cedula_rif'] ?? '' }}</td>
    @if(Auth::user()->company->id != 22)
    <td style="text-align: center;">{{ $quotation->serie }}</td>
    <td style="text-align: center;">{{ $quotation->number_pedido}}</td>
    <td style="text-align: center;">Nota de Entrega</td>
    @else
    <td style="text-align: center; color:red">{{ $quotation->serie }}</td>
    <td style="text-align: center; color:red">{{ $quotation->number_pedido}}</td>
    <td style="text-align: center; color:red">Contado</td>
    @endif
    <td style="text-align: center;">{{ $quotation->transports['placa'] ?? '' }}</td>
    
    
  </tr>
  
</table>

<table style="width: 100%;">
  <tr>
  @if($valor == 3 or $valor == 4)
  <th style="font-weight: normal; font-size: medium;">Observaciones: &nbsp; <span style="color: red;">{{ $quotation->observation ?? ''}}</span></th>
  @else
  <th style="font-weight: normal; font-size: medium;">Observaciones: &nbsp; {{ $quotation->observation ?? ''}} </th>
  @endif
</tr>
  
</table>

<table style="width: 100%;">
  <tr>
    <th style="text-align: center; width: 100%;">Productos</th>
  </tr> 
</table>

<table style="width: 100%;">

  <?php 
   $lote = '';
   $date_expirate = '';
   $discount = 0;  
   $conted = 0;
      foreach ($inventories_quotations as $var){

          $lote .= $var->lote;
          $date_expirate .= $var->date_expirate;

          $discount += $var->discount;
      }
  
  ?>


@if(Auth::user()->company->id != 22)
        <tr>
          <th style="text-align: center; ">Código</th>
          <th style="text-align: center; ">Descripción</th>
          @if($valor == 2 or $valor == 4)
          <th style="text-align: center; ">Foto</th>
          @endif
          <th style="text-align: center; ">Cantidad</th>
          @if($lote != '')
          <th style="text-align: center; ">Lote</th>
          @endif
          @if($date_expirate != '')
          <th style="text-align: center; ">Fecha Venc</th> 
          @endif
          <th style="text-align: center; ">P.V.J.</th>
          @if($discount > 0)
          <th style="text-align: center; ">Desc</th>
          @endif
          <th style="text-align: center; ">Total</th>
        </tr> 
@else
      <tr>
        <th style="text-align: center; background-color:#5CAB44; color: #fff">Código</th>
        <th style="text-align: center; background-color:#5CAB44; color: #fff">Descripción</th>
        @if($valor == 2 or $valor == 4)
        <th style="text-align: center; background-color:#5CAB44; color: #fff">Foto</th>
        @endif
        <th style="text-align: center; background-color:#5CAB44; color: #fff">Cantidad</th>
        @if($lote != '')
        <th style="text-align: center; background-color:#5CAB44; color: #fff">Lote</th>
        @endif
        @if($date_expirate != '')
        <th style="text-align: center; background-color:#5CAB44; color: #fff">Fecha Venc</th> 
        @endif
        <th style="text-align: center; background-color:#5CAB44; color: #fff">Precio</th>
        @if($discount > 0)
        <th style="text-align: center; background-color:#5CAB44; color: #fff">Desc</th>
        @endif
        <th style="text-align: center; background-color:#5CAB44; color: #fff">Total</th>
       </tr> 
 @endif

  @foreach ($inventories_quotations as $var)
      <?php
       
        $percentage = (($var->price * $var->amount_quotation) * $var->discount)/100;

        $total_less_percentage = ($var->price * $var->amount_quotation) - $percentage;

        $total_less_percentage = $total_less_percentage ;


      ?>
    <tr>
      <th style="text-align: center; font-weight: normal; ">{{ $var->code_comercial }}</th>
      <th style="text-align: center; font-weight: normal;">{{ $var->description }}</th>
      @if($valor == 2 or $valor == 4)
        @if(isset($var->photo_product))
        <th style="text-align: center; font-weight: normal;"><img style="width:60px; max-width:60px; height:80px; max-height:80px" src="{{asset('arya/storage/app/public/img/'.$company->login.'/productos/'.$var->photo_product)}}"></th>
        @else
        <th style="text-align: center; font-weight: normal;"></th>
        @endif
      @endif
      <th style="text-align: center; font-weight: normal;">{{ $var->amount_quotation }}</th>
      @if ($lote != '')
      <th style="text-align: center; font-weight: normal;">{{ $var->lote }}</th>
      @endif
      
      @if ($date_expirate != '')
      <th style="text-align: center; font-weight: normal;">{{ $var->date_expirate}}</th>
      @endif

      <th style="text-align: center; font-weight: normal;">{{ number_format($var->price , 2, ',', '.')  }}</th>
      @if($discount > 0)
      <th style="text-align: center; font-weight: normal;">{{ $var->discount }}%</th>
      @endif
      <th style="text-align: right; font-weight: normal;">{{ number_format($total_less_percentage, 2, ',', '.') }}</th>
    </tr> 
    <?php
            $conted += $var->amount_quotation;
    ?>
  @endforeach 
</table>


<?php
  $iva = ($quotation->base_imponible * $quotation->iva_percentage)/100;

  $total_bs = $quotation->total_factura + $iva;


  $iva = $iva ;

  $total = $total_bs ;
?>

<table style="width: 100%;">
  <tr>
    <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;"></th>
    <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white;"><span style="font-size:9pt;">Cant.({{$conted}})</span>&nbsp;&nbsp; Sub Total</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{  number_format(bcdiv($quotation->total_factura, '1', 2), 2, ',', '.')}}</th>
  </tr> 
  <tr>
    <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;"></th>
    <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white;">Base Imponible</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{  number_format(bcdiv($quotation->base_imponible , '1', 2), 2, ',', '.') }}</th>
  </tr> 
  <tr>
    <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;"></th>
    <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white;">Ventas Exentas</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{  number_format(bcdiv(($retiene_iva ?? 0) , '1', 2), 2, ',', '.')}}</th>
  </tr> 
  <tr>
    <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;"></th>
    <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white;">I.V.A.{{ $quotation->iva_percentage }}%</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format(bcdiv($iva, '1', 2), 2, ',', '.') }}</th>
  </tr> 
 
  
    @if (isset($coin) && ($coin == 'bolivares'))
      <tr>
        <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;"></th>
        <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">TOTAL Bs</th>
        <th style="text-align: right; font-weight: normal; width: 21%;">{{  number_format(bcdiv($total, '1', 2), 2, ',', '.') }}</th>
      </tr> 
      @if(Auth::user()->company->id != 22)
      <tr>
        <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;"> Tasa de cambio a la fecha: {{ number_format(bcdiv($quotation->bcv, '1', 2), 2, ',', '.') }} Bs.</th>
        <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white; font-size: small;">TOTAL $</th>
        <th style="text-align: right; font-weight: normal; width: 21%;">{{  number_format(bcdiv($total / $quotation->bcv, '1', 2), 2, ',', '.') }}</th>
      </tr> 
      @endif
    @else
      <tr>
        <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white; font-size: small;"></th>
        <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">TOTAL $</th>
        <th style="text-align: right; font-weight: normal; width: 21%;">{{  number_format(bcdiv($total, '1', 2), 2, ',', '.') }}</th>
      </tr> 
      @if(Auth::user()->company->id != 22)
      
      @if($valor == 5)
      <tr style="display: none;">
      @else
      <tr>
      @endif

        <th style="text-align: left; font-weight: normal; width: 38%; border-bottom-color: white; border-right-color: white;"> Tasa de cambio a la fecha: {{ number_format(bcdiv($quotation->bcv, '1', 2), 2, ',', '.') }} Bs.</th>
        <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;  border-left-color: black;">TOTAL Bs</th>
        <th style="text-align: right; font-weight: normal; width: 21%; border-bottom-color: white;">{{  number_format(bcdiv($total * $quotation->bcv , '1', 2), 2, ',', '.')}}</th>
      </tr>
      @endif 
    @endif

  <tr>
    <th style="text-align: left; width: 50%; border-bottom-color: black; border-right-color: white;" ></th>
    <th style="text-align: left; font-weight: normal; width: 15%; border-top-color: rgb(17, 9, 9); border-right-color: black; font-size: small;"></th>
    <th style="text-align: right; font-weight: normal; width: 15%; "></th>
  </tr> 
</table>
@if(isset($quotation->note))
<table style="width: 100%;">
  <tr style="width: 100%; border-color: white;">
    <td align="left" style="text-align: left; align: left; border-color: white;  font-weight: normal; font-size: small;">
     Nota: {{$quotation->note ?? ''}} 
    </td>
  </tr>
</table>
@endif
@if(Auth::user()->company->id != 22)
  <table style="border:#fff; width:100%">
      <tr style="border:#fff; width:100%">
          <td style="border:#fff" style="width:50%">Firma Persona que Entrega:</td>
          <td style="border:#fff" style="width:50%; margin-left:50px;">Firma Persona que Recibe:</td>
      </tr>
      <tr style="border:#fff; width:100%">
          <td style="border:#fff" style="width:50%">Nombre: {{$quotation->person_note_delivery}}</td>
          <td style="border:#fff" style="width:50%; margin-left:50px;">Nombre:</td>
      </tr>
      <tr style="border:#fff; width:100%">
          <td style="border:#fff" style="width:50%">CI: {{$quotation->ci_person_note_delivery}}</td>
          <td style="border:#fff" style="width:50%; margin-left:50px;">CI:</td>
      <tr style="border:#fff; width:100%">
          <td style="border:#fff" style="width:50%">______________________________</td>
          <td style="border:#fff" style="width:50%; margin-left:50px;">______________________________</td>
      </tr>

  </table>
  @else

  

<footer>

  <table style="width:100%; font-size: 10pt;">
    <tr>
      <td width="40%" style="border:#000" style="font-size: 10pt;"><b>DESPACHADO:</b></td>
      <td width="30%" align="center" style="border-top:#000" style="font-size: 10pt;"><b>SELLO</b></td>
      <td width="30%"align="center" style="border-top:#000" style="font-size: 10pt;"><b>RECIBIDO POR:</b></td>
    </tr>
    <tr>
      <td style="border_top:#fff " style="font-size: 10pt;">
        {{$quotation->person_note_delivery}}<br>
        CI: {{$quotation->ci_person_note_delivery}}<br>
        <span style="color: #fff">.</span><br>
        </span><br><span style="color: #fff">.</span>
      </td>
      <td style="border:#000" align="center" style="font-size: 10pt;">
        <span style="color: #fff">.</span><br>
        <span  style="color: #fff">.</span><br>
        <span style="color: #fff">.</span><br>
       </span><br><span style="color: #fff">.</span>
    </td>
      <td style="border:#000" align="center" style="font-size: 10pt;">
        <span style="color: #fff">.</span><br>
        <span style="color: #fff">.</span><br>
        <span style="color: #fff">.</span><br>
      </span><br><span style="color: #fff">.</span></td>

  </tr>
  </table>
</footer>

<div class="footer">
  Página <span class="pagenum"></span> / <span class="pagenum2"></span> 
</div>

@endif

</body>






</html>
