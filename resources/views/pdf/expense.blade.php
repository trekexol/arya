
  
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 
<title>Gasto o Compra</title>
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
  </style>
</head>

<body>


  <br><br><br><br><br><br><br><br><br>
  <h4 style="color: black">FACTURA DE COMPRA <span>N° de Orden</span> : {{ str_pad($expense->id, 6, "0", STR_PAD_LEFT)}}</h4>

 
   
 
<table>
  <tr>
    <th style="font-weight: normal; width: 40%;">Concesión Postal:</th>
    <th style="font-weight: normal;">Nº {{ $company->franqueo_postal ?? ''}}</th>
   
  </tr>
  <tr>
    
      <td style="width: 40%;">Fecha de Emisión:</td>
      <td>{{ $expense->date }}</td>
    
  
  </tr>
  
</table>




<table style="width: 100%;">
  <tr>
    <th style="font-weight: normal; font-size: medium;">Nombre / Razón Social: &nbsp;  {{ $expense->providers['razon_social'] }}</th>
    
   
  </tr>
  <tr>
    <td>Domicilio Fiscal: &nbsp;  {{ $expense->providers['direction'] }}
    </td>
    
    
  </tr>
  
</table>




<table style="width: 100%;">
  <tr>
    <th style="text-align: center;">Teléfono</th>
    <th style="text-align: center;">RIF/CI</th>
    <th style="text-align: center;">N° de Factura</th>
    <th style="text-align: center;">N° Control/Serie</th>
    <th style="text-align: center;">Condiciones de Pago</th>
   
  </tr>
  <tr>
    <td style="text-align: center;">{{ $expense->providers['phone1'] }}</td>
    <td style="text-align: center;">{{ $expense->providers['code_provider'] }}</td>
    <td style="text-align: center;">{{ $expense->invoice }}</td>
    <td style="text-align: center;">{{ $expense->serie }}</td>
    @if(isset($expense->credit_days))
      <td style="text-align: center;">Crédito {{ $expense->credit_days }} dias</td>
    @else
      <td></td>
    @endif
    
  </tr>
  
</table>

<table style="width: 100%;">
  <tr>
  <th style="font-weight: normal; font-size: medium;">Observaciones: &nbsp; {{ $expense->observation }} </th>
</tr>
  
</table>
  @if (!$payment_expenses->isEmpty())
      

      <br>
      <table style="width: 100%;">
        <tr>
          <th style="text-align: center; width: 100%;">Pagos</th>
        </tr> 
      </table>

      <table style="width: 100%;">
        <tr>
          <th style="text-align: center; ">Tipo de Pago</th>
          <th style="text-align: center; ">Cuenta</th>
          <th style="text-align: center; ">Referencia</th>
          <th style="text-align: center; ">Monto</th>
        </tr>

        @foreach ($payment_expenses as $var)

        <?php 
          if($coin != 'bolivares'){
            $var->amount = $var->amount / $expense->rate;
          }
        ?>
        <tr>
          <th style="text-align: center; font-weight: normal;">{{ $var->payment_type }}</th>
          @if (isset($var->accounts['description']))
            <th style="text-align: center; font-weight: normal;">{{ $var->accounts['description'] }}</th>
          @else    
            <th style="text-align: center; font-weight: normal;"></th>
          @endif
          <th style="text-align: center; font-weight: normal;">{{ $var->reference }}</th>
          <th style="text-align: center; font-weight: normal;">{{ number_format($var->amount, 2, ',', '.')}}</th>
        </tr> 
        @endforeach 
        
      </table>
  @endif
<br>
<table style="width: 100%;">
  <tr>
    <th style="text-align: center; width: 100%;">Productos</th>
  </tr> 
</table>
<table style="width: 100%;">
  <tr>
    <th style="text-align: center; ">Código</th>
    <th style="text-align: center; ">Descripción</th>
    <th style="text-align: center; ">Cantidad</th>
    <th style="text-align: center; ">P.V.J.</th>
    <th style="text-align: center; ">Total</th>
  </tr> 
  @foreach ($inventories_expenses as $var)
      <?php
        if($coin != 'bolivares'){
          $var->price = $var->price / $expense->rate;
        }

        $total_less_percentage = ($var->price * $var->amount);

      ?>
    <tr>
      @if (isset($var->id_inventory))
        <th style="text-align: center; font-weight: normal;">Inv: {{ $var->inventories['code'] }}</th> 
      @else
        <th style="text-align: center; font-weight: normal;">Cuenta: {{ $var->id_account }}</th>
      @endif
      <th style="text-align: center; font-weight: normal;">{{ $var->description }}</th>
      <th style="text-align: center; font-weight: normal;">{{ number_format($var->amount, 2, ',', '.') }}</th>
      <th style="text-align: center; font-weight: normal;">{{ number_format($var->price, 2, ',', '.')  }}</th>
      <th style="text-align: right; font-weight: normal;">{{ number_format($total_less_percentage, 2, ',', '.') }}</th>
    </tr> 
  @endforeach 
</table>


<?php
  //$iva = ($expense->base_imponible * $expense->iva_percentage)/100;

 // $total = $expense->sub_total + $iva;

 // $total_petro = ($total - $expense->anticipo)/ 159765192.04;

 $expense->sub_total = $expense->amount;

 $expense->amount_with_iva = ($expense->amount_with_iva / ($bcv ?? 1)); //+ ($expense->retencion_iva / ($bcv ?? 1)) + ($expense->retencion_islr / ($bcv ?? 1));
?>

<table style="width: 100%;">
  <tr>
    <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white;">Sub Total</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format($expense->sub_total / ($bcv ?? 1), 2, ',', '.') }}</th>
  </tr> 
  <tr>
    <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white;">Base Imponible</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format($expense->base_imponible / ($bcv ?? 1), 2, ',', '.') }}</th>
  </tr> 
  
 
  <tr>
    <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white;">I.V.A.{{ $expense->iva_percentage }}%</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format($expense->amount_iva / ($bcv ?? 1), 2, ',', '.') }}</th>
  </tr> 
  @if ($expense->anticipo != 0)
  <tr>
    <th style="text-align: right; font-weight: normal; width: 79%; border-bottom-color: white;">Anticipo</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format($expense->anticipo / ($bcv ?? 1), 2, ',', '.') }}</th>
  </tr> 
  @endif
 
  <tr>
    <th style="text-align: right; font-weight: normal; width: 79%; border-top-color: rgb(17, 9, 9); font-size: small;">MONTO TOTAL</th>
    <th style="text-align: right; font-weight: normal; width: 21%;">{{ number_format($expense->amount_with_iva, 2, ',', '.') }}</th>
  </tr> 
  
</table>

</body>
</html>
