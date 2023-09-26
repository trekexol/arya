
  
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 
<title></title>
<style>
  table, td, th {
    border: 1px solid black;
    font-size: x-small;
  }
  
  table {
    border-collapse: collapse;
    width: 100%;
  }
  
  th {
    
    text-align: left;
  }
  </style>
</head>

<body>

  <br>
  <h4 style="color: black; text-align: center">Reporte Cobros</h4>
 
  <h5 style="color: black; text-align: center">Fecha de Emisión: {{ $date_end ?? $datenow ?? '' }}</h5>
   
  <?php 
    
    $total = 0;
    $total_dolar = 0;
   
  ?>
<table style="width: 100%;">
  <tr>
    <th class="text-center">Nº</th>
    <th class="text-center">Fecha</th>
    <th class="text-center">Nº Factura</th>

    <th class="text-center">{{$typeperson ?? 'Cliente'}}</th>
    <th class="text-center">Referencia del Pago</th>
    <th class="text-center">Tipo de Pago</th>
    <th class="text-center">Cuenta</th>
    <th class="text-center">Monto</th>
    <th class="text-center">Monto $</th>
  </tr> 
  @if (isset($quotation_payments))
      @foreach ($quotation_payments as $quotation_payment)
        @php
          $total += $quotation_payment->amount;
          $total_dolar += bcdiv(($quotation_payment->amount / $quotation_payment->rate), '1', 2);
        @endphp
          <tr>
              <td class="text-center ">
                  {{ $quotation_payment->id }}
              </td>
              <td class="text-center ">{{date_format(date_create($quotation_payment->date ?? '00-00-0000'),"d-m-Y")}}</td>
              <td class="text-center ">{{$quotation_payment->number_invoice ?? ''}}</td>
              <td class="text-center ">{{$quotation_payment->name_client ?? ''}}</td>
              <td class="text-center ">{{ $quotation_payment->reference ?? ''}}</td>
              <td class="text-center ">{{ $quotation_payment->payment_type ?? ''}}</td>
              <td class="text-center ">{{ $quotation_payment->description_account ?? ''}}</td>
              <td style="text-align: right; font-weight: normal;">{{ number_format($quotation_payment->amount ?? 0, 2, ',', '.')}}</td>
              <td style="text-align: right; font-weight: normal;">${{ number_format(bcdiv(($quotation_payment->amount / $quotation_payment->rate), '1', 2), 2, ',', '.')}}</td>
             
              
          </tr>     
      @endforeach   
  @endif
  <tr>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white; border-right-color: black;"></th>
    <th style="text-align: right; font-weight: normal;">{{ number_format(($total ?? 0), 2, ',', '.') }}</th> 
    <th style="text-align: right; font-weight: normal;">${{ number_format(($total_dolar ?? 0), 2, ',', '.') }}</th> 
  </tr> 
</table>

</body>
</html>
