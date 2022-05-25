
  
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="{{asset('vendor/sb-admin/css/sb-admin-2.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<title>Cobro</title>
<style>
  body{
    background: white;
  }
  table, td, th {
    border: 1px solid black;
    background: white;
  }
  
  table {
    border-collapse: collapse;
  }
  
  th {
    
    text-align: left;
  }
  </style>
</head>

<body>


  <table>
    <tr>
      <th style="text-align: left; font-weight: normal; width: 10%; border-color: white; font-weight: bold;"> <img src="{{ asset(Auth::user()->company->foto_company ?? 'img/northdelivery.jpg') }}" width="90" height="30" class="d-inline-block align-top" alt="">
      </th>
      <th style="text-align: left; font-weight: normal; width: 90%; border-color: white; font-weight: bold;"><h6>{{Auth::user()->company->code_rif ?? ''}} </h6></th>
    </tr> 
  </table>
  <div class="text-center h4">Orden de Pago</div>
  @php
  $contador_por_pagina = 0;
@endphp
@for ($i = 0; $i < count($movements);$i++)
  <table style="width: 100%;">
    <tr>
      <th style="text-align: center;  font-size: medium;">Cuenta</th>
      <th style="text-align: center;  font-size: medium;">Descripción</th>
      <th style="text-align: center;  font-size: medium;">Debe</th>
      <th style="text-align: center;  font-size: medium;">Haber</th>
    </tr>
   
      <tr>
        <td style="text-align: center;">{{ $movements[$i]->code_one }}.{{ $movements[$i]->code_two }}.{{ $movements[$i]->code_three }}.{{ $movements[$i]->code_four }}.{{ $movements[$i]->code_five }}</td>
        <td style="text-align: center;">{{ $movements[$i]->account_description }}</td>
        <td style="text-align: right;">{{ number_format($movements[$i]->debe / ($bcv ?? 1), 2, ',', '.')}}</td>
        <td style="text-align: right;">{{ number_format($movements[$i]->haber / ($bcv ?? 1), 2, ',', '.')}}</td>
      </tr>
      <tr>
        <td style="text-align: center;">{{ $movements[$i+1]->code_one }}.{{ $movements[$i+1]->code_two }}.{{ $movements[$i+1]->code_three }}.{{ $movements[$i+1]->code_four }}.{{ $movements[$i+1]->code_five }}</td>
        <td style="text-align: center;">{{ $movements[$i+1]->account_description }}</td>
        <td style="text-align: right;">{{ number_format($movements[$i+1]->debe / ($bcv ?? 1), 2, ',', '.')}}</td>
        <td style="text-align: right;">{{ number_format($movements[$i+1]->haber / ($bcv ?? 1), 2, ',', '.')}}</td>
      </tr>
      <?php
          $header_id = $movements[$i]->header_id ?? '';
          $id_order = $movements[$i]->id_order ?? '';
          $expense_serie = $movements[$i]->expense_serie ?? '';
          
          $provider_name = $movements[$i]->provider_name ?? '';
          $provider_code_provider = $movements[$i]->code_provider ?? '';
          $provider_type_provider = $movements[$i]->type_code ?? '';

          $client_name = $movements[$i]->client_name ?? '';
          $order_reference = $movements[$i]->reference_order ?? '';
          $date_order = $movements[$i]->date_order ?? '';

          $contador_por_pagina += 1;

      ?>

    
  </table>
   <br><br><br>
  <table style="width: 100%;">
    <tr>
      <th style="text-align: left; font-weight: normal; width: 25%; border-color: white;">Número de Cobro:</th>
      <th style="text-align: left; font-weight: normal; width: 70%; border-color: white;">{{ $header_id ?? ''}}</th>
    </tr> 
    <tr>
      <th style="text-align: left; font-weight: normal; width: 25%; border-color: white;">Número de Orden:</th>
      <th style="text-align: left; font-weight: normal; width: 70%; border-color: white;">{{ $id_order ?? ''}}</th>
    </tr> 
    <tr>
      <th style="text-align: left; font-weight: normal; width: 25%; border-color: white;">Referencia:</th>
      <th style="text-align: left; font-weight: normal; width: 70%; border-color: white;">{{ $order_reference ?? ''}}</th>
    </tr> 
   
    @if ($movements[$i]->provider_name)
      <tr>
        <th style="text-align: left; font-weight: normal; width: 25%; border-color: white;">Proveedor:</th>
        <th style="text-align: left; font-weight: normal; width: 70%; border-color: white;">{{ $provider_type_code ?? ''}}{{ $provider_code_provider ?? '' }} / {{ $provider_name ?? ''}}</th>
      </tr> 
    @endif
    @if ($movements[$i]->client_name)
      <tr>
        <th style="text-align: left; font-weight: normal; width: 25%; border-color: white;">Cliente:</th>
        <th style="text-align: left; font-weight: normal; width: 70%; border-color: white;">{{ $client_name ?? ''}}</th>
      </tr> 
    @endif
    <tr>
      <th style="text-align: left; font-weight: normal; width: 25%; border-color: white;">Fecha del Cobro:</th>
      <th style="text-align: left; font-weight: normal; width: 70%; border-color: white;">{{ $date_order ?? ''}}</th>
    </tr> 
  </table>

  @if($contador_por_pagina == 1)
      <?php
        $contador_por_pagina = 0;
      ?>
      <div class="page-break"></div>
      @endif
    @endfor
</body>
</html>
