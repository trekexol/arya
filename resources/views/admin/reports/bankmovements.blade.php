
  
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
      @if (Auth::user()->company->foto_company != '')  
      <th style="text-align: left; font-weight: normal; width: 10%; border-color: white; font-weight: bold;"> <img src="{{ asset(Auth::user()->company->foto_company ?? 'img/logo.jpg') }}" style="max-width:93; max-height:60" class="d-inline-block align-top" alt="">
      </th>
      @endif
      <th style="text-align: left; font-weight: normal; width: 90%; border-color: white; font-weight: bold;"><h4>{{Auth::user()->company->razon_social ?? ''}}  <h4>{{Auth::user()->company->code_rif ?? ''}}</h4> </h4></th>    </tr> 
    </tr> 
  </table>
  <h4 style="color: black; text-align: center">MOVIMIENTOS BANCARIOS</h4>
  <h5 style="color: black; text-align: center">Fecha de Emisión: {{ $date_end ?? $datenow ?? '' }}</h5>
   
 
<table style="width: 100%;">
  <tr>
    <th style="text-align: center; ">Fecha</th>
    <th style="text-align: center; ">Ref</th>
    <th style="text-align: center; ">Cuenta</th>
    <th style="text-align: center; ">Contrapartida</th>
    <th style="text-align: center; ">Descripción</th>
    <th style="text-align: center; ">Comprobante</th>
    <th style="text-align: center; ">Monto</th>
  </tr> 
  
  @for ($i = 0; $i < count($details_banks); $i++)
    <tr>
      <td style="text-align: center; ">{{ $details_banks[$i]->header_date ?? '' }}</td>
      <td style="text-align: center; ">{{ $details_banks[$i]->header_reference ?? '' }}</td>
      <td style="text-align: center; ">{{ $details_banks[$i]->account_description ?? '' }}</td>
      <?php 
        $i += 1;
      ?>
      <td style="text-align: center; ">{{ $details_banks[$i]->account_description ?? '' }}</td>
      <td style="text-align: center; ">{{ $details_banks[$i]->header_description ?? '' }}</td>
      <td style="text-align: center; ">{{ $details_banks[$i]->header_id ?? '' }}</td>
      @if (isset($coin) && ($coin == 'bolivares'))
        @if ($details_banks[$i]->debe != 0)
        <td style="text-align: center; ">{{ number_format(($details_banks[$i]->debe ?? 0), 2, ',', '.')}}</td>
        @else
          <td style="text-align: center; ">{{ number_format(($details_banks[$i]->haber ?? 0), 2, ',', '.')}}</td>
        @endif
      @else
        @if ($details_banks[$i]->debe != 0)
          <td style="text-align: center; ">{{ number_format(($details_banks[$i]->debe / $details_banks[$i]->tasa), 2, ',', '.')}}</td>
        @else
          <td style="text-align: center; ">{{ number_format(($details_banks[$i]->haber / $details_banks[$i]->tasa), 2, ',', '.')}}</td>
        @endif
      @endif
      
      
    </tr> 
  @endfor

  
</table>

</body>
</html>
