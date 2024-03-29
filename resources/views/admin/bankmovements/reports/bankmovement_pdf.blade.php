
  
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="{{asset('vendor/sb-admin/css/sb-admin-2.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<title>Comprobante</title>
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

      <th style="text-align: left; font-weight: normal; width: 10%; border-color: white; font-weight: bold;"> <img src="{{ asset(Auth::user()->company->foto_company ?? 'img/logo.jpg') }}" style="max-width:93; max-height:60" class="d-inline-block align-top" alt="">
      </th>
      @endif
      <th style="text-align: left; font-weight: normal; width: 90%; border-color: white; font-weight: bold;"><h6>{{Auth::user()->company->razon_social ?? ''}}  <h6>{{Auth::user()->company->code_rif ?? ''}}</h6> </h6></th>    </tr> 
    </tr> 
  </table>
  <br><br>

  <div class="text-center h4">Comprobante Digital de {{$type}}</div>
  
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
         
          $date = $movement->date ?? '';

          $reference = $movement->reference ?? '';
          $description = $movement->description ?? '';
      ?>

  @endforeach
  </table>
   <br><br><br>
  <table style="width: 100%;">
    <tr>
      <th style="text-align: left; font-weight: normal; width: 25%; border-color: white;">Número de Cobro:</th>
      <th style="text-align: left; font-weight: normal; width: 70%; border-color: white;">{{ $header_id ?? ''}}</th>
    </tr> 
    <tr>
      <th style="text-align: left; font-weight: normal; width: 25%; border-color: white;">Referencia:</th>
      <th style="text-align: left; font-weight: normal; width: 70%; border-color: white;">{{ $reference ?? ''}}</th>
    </tr> 
    <tr>
      <th style="text-align: left; font-weight: normal; width: 25%; border-color: white;">Descripción:</th>
      <th style="text-align: left; font-weight: normal; width: 70%; border-color: white;">{{ $description ?? ''}}</th>
    </tr> 
 
    <tr>
      <th style="text-align: left; font-weight: normal; width: 25%; border-color: white;">Fecha del Cobro:</th>
      <th style="text-align: left; font-weight: normal; width: 70%; border-color: white;">{{ date_format(date_create($date ?? '00-00-0000'),"d-m-Y")}}</th>
    </tr> 
  </table>

</body>
</html>
