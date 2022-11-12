
  
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="{{asset('vendor/sb-admin/css/sb-admin-2.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<title>Orden de Cobro</title>
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
      @if (Auth::user()->company->foto_company != '')  
      <th style="text-align: left; font-weight: normal; width: 10%; border-color: white; font-weight: bold;"> <img src="{{ asset(Auth::user()->company->foto_company ?? 'img/logo.jpg') }}" width="90" height="30" class="d-inline-block align-top" alt="">
      </th>
      @endif
      <th style="text-align: left; font-weight: normal; width: 90%; border-color: white; font-weight: bold;"><h6>{{Auth::user()->company->razon_social ?? ''}}  <h6>{{Auth::user()->company->code_rif ?? ''}}</h6> </h6></th>    </tr> 
    </tr> 
  </table>
  <div class="text-center h4">Orden de Cobro</div>
  
  <table style="width: 100%;">
    <tr>
      <th style="text-align: center;  font-size: medium;">Cuenta</th>
      <th style="text-align: center;  font-size: medium;">Descripción</th>
      <th style="text-align: center;  font-size: medium;">Debe</th>
      <th style="text-align: center;  font-size: medium;">Haber</th>
    </tr>
   
    @foreach ($movements as $movement)
      <tr>
        <td style="text-align: center;">{{ $movement->code_one }}.{{ $movement->code_two }}.{{ $movement->code_three }}.{{ $movement->code_four }}.{{ $movement->code_five }}</td>
        <td style="text-align: center;">{{ $movement->account_description }}</td>
        <td style="text-align: right;">{{ number_format($movement->debe / ($bcv ?? 1), 2, ',', '.')}}</td>
        <td style="text-align: right;">{{ number_format($movement->haber / ($bcv ?? 1), 2, ',', '.')}}</td>
      </tr>
   
      <?php
          $header_id = $movement->header_id ?? '';
        //  $id_order = $movement->id_order ?? '';
         // $expense_serie = $movement->expense_serie ?? '';
          
         // $provider_name = $movement->provider_name ?? '';
         // $provider_code_provider = $movement->code_provider ?? '';
         // $provider_type_provider = $movement->type_code ?? '';

          //$client_name = $movement->client_name ?? '';
          $order_reference = $movement->reference_order ?? '';
          $date_order = $movement->date_order ?? '';


      ?>

  @endforeach
  </table>
   <br><br><br>
  <table style="width: 100%;">
    <tr>
      <th style="text-align: left; font-weight: normal; width: 25%; border-color: white;">Comprobante:</th>
      <th style="text-align: left; font-weight: normal; width: 70%; border-color: white;">{{ $header_id ?? ''}}</th>
    </tr> 
    <!--<tr>
      <th style="text-align: left; font-weight: normal; width: 25%; border-color: white;">Número de Orden:</th>
      <th style="text-align: left; font-weight: normal; width: 70%; border-color: white;">{{''}}</th>
    </tr> -->
    <tr>
      <th style="text-align: left; font-weight: normal; width: 25%; border-color: white;">Referencia:</th>
      <th style="text-align: left; font-weight: normal; width: 70%; border-color: white;">{{ $order_reference ?? ''}}</th>
    </tr> 
   
    @if ($movement->provider_name)
      <tr>
        <th style="text-align: left; font-weight: normal; width: 25%; border-color: white;">Proveedor:</th>
        <th style="text-align: left; font-weight: normal; width: 70%; border-color: white;">{{ $provider_type_code ?? ''}}{{ $provider_code_provider ?? '' }} / {{ $provider_name ?? ''}}</th>
      </tr> 
    @endif
    @if ($movement->client_name)
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

</body>
</html>
