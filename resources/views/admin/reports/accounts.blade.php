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
  <table>
    <tr>
      <th style="text-align: left; font-weight: normal; width: 10%; border-color: white; font-weight: bold;"> <img src="{{ asset(Auth::user()->company->foto_company ?? 'img/northdelivery.jpg') }}" width="90" height="30" class="d-inline-block align-top" alt="">
      </th>
      <th style="text-align: left; font-weight: normal; width: 90%; border-color: white; font-weight: bold;"><h4>{{Auth::user()->company->code_rif ?? ''}} </h4></th>
    </tr> 
  </table>
  <h4 style="color: black; text-align: center">CUENTAS</h4>
  <h5 style="color: black; text-align: center">Fecha de Emisión: {{ $date_end ?? $datenow ?? '' }}</h5>
   
 
<table style="width: 100%;">
  <tr>
    <th style="text-align: center; width:1%;">Código</th>
    <th style="text-align: center; ">Descripción</th>
    <th style="text-align: center; width:1%;">Nivel</th>
    <th style="text-align: center; width:1%;">Tipo</th>
    <th style="text-align: center; ">Saldo Anterior</th>
    <th style="text-align: center; ">Debe</th>
    <th style="text-align: center; ">Haber</th>
    <th style="text-align: center; ">Saldo Actual</th>
  </tr> 
  @foreach ($accounts as $account)
    
      @if ($account->level <= 4)
      <tr>
          <th style="text-align: center; ">{{ $account->code_one ?? ''}}.{{ $account->code_two ?? ''}}.{{ $account->code_three ?? ''}}.{{ $account->code_four ?? ''}}.{{ $account->code_five ?? ''}}</th>
          <th style="text-align: center; ">{{ $account->description ?? ''}}</th>
          <th style="text-align: center; ">{{ $account->level ?? ''}}</th>
          <th style="text-align: center; ">{{ $account->type ?? ''}}</th>
          <th style="text-align: right; ">{{ number_format(($account->balance_previus ?? 0), 2, ',', '.')}}</th>
          <th style="text-align: right; ">{{ number_format(($account->debe ?? 0), 2, ',', '.') }}</th>
          <th style="text-align: right; ">{{ number_format(($account->haber ?? 0), 2, ',', '.') }}</th>
          <th style="text-align: right; ">{{ number_format(($account->balance_previus ?? 0)+($account->debe ?? 0)-($account->haber ?? 0), 2, ',', '.') }}</th>
      </tr> 
      @else
      <tr>
        <th style="text-align: center; font-weight: normal;">{{ $account->code_one ?? ''}}.{{ $account->code_two ?? ''}}.{{ $account->code_three ?? ''}}.{{ $account->code_four ?? ''}}.{{ $account->code_five ?? ''}}</th>
        <th style="text-align: center; font-weight: normal;">{{ $account->description ?? ''}}</th>
        <th style="text-align: center; font-weight: normal;">{{ $account->level ?? ''}}</th>
        <th style="text-align: center; font-weight: normal;">{{ $account->type ?? ''}}</th>
        <th style="text-align: right; font-weight: normal;">{{ number_format(($account->balance_previus ?? 0), 2, ',', '.')}}</th>
        <th style="text-align: right; font-weight: normal;">{{ number_format(($account->debe ?? 0), 2, ',', '.') }}</th>
        <th style="text-align: right; font-weight: normal;">{{ number_format(($account->haber ?? 0), 2, ',', '.') }}</th>
        <th style="text-align: right; font-weight: normal;">{{ number_format(($account->balance_previus ?? 0)+($account->debe ?? 0)-($account->haber ?? 0), 2, ',', '.') }}</th>
      </tr> 
      @endif
      
      
  @endforeach 

  <tfoot>

      <th style="text-align: center; font-weight: normal; width: 10%; border-color: white; font-weight: bold;border: 0;"></th>
      <th style="text-align: center; font-weight: normal; width: 10%; border-color: white; font-weight: bold;border: 0;"><th>
      <th style="text-align: center; font-weight: normal; width: 10%; border-color: white; font-weight: bold;border: 0;"></th>
      <th style="text-align: right; font-weight: normal; width: 10%; border-color: white; font-weight: bold;border: 0;">{{ number_format(0, 2, ',', '.')}}</th>
      <th style="text-align: right; font-weight: normal; width: 10%; border-color: white; font-weight: bold;border: 0;">{{ number_format(0, 2, ',', '.') }}</th>
      <th style="text-align: right; font-weight: normal; width: 10%; border-color: white; font-weight: bold;border: 0;">{{ number_format(0, 2, ',', '.') }}</th>
      <th style="text-align: right; font-weight: normal; width: 10%; border-color: white; font-weight: bold;border: 0;">{{ number_format(0, 2, ',', '.') }}</th>
  
  </tfoot>  
</table>

</body>
</html>
