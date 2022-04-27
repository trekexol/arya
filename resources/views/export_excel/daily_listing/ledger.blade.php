

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 
<title>Factura</title>
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


  <br>
  <h5 style="color: black; text-align: center;">{{ $company->razon_social ?? ''}} / Rif: {{ $company->code_rif ?? ''}} / Fecha de Emisión: {{ $datenow }}</h5>
  
  <h4 style="color: black; text-align: center;">LIBRO MAYOR</h4>
  <h4 style="color: black; text-align: center; font-weight: bold;">Periodo desde {{ date('d-m-Y', strtotime( $date_begin ?? $detail_old->created_at ?? '')) }} al {{ date('d-m-Y', strtotime( $date_end ?? $datenow)) }}</h4>
   
 
  @if (isset($details))
      <?php 
      $total_debe = 0;
      $total_haber = 0;
      
      $controlador_inicial = true;
      $controlador = true;
     ?>
    <table style="width: 100%;">
      <tr>
        <th style="text-align: center; width: 20%;">Fecha</th>
        <th style="text-align: center; width: 10%;">Ref</th>
        <th style="text-align: center; width: 30%;">Descripcion</th>
        <th style="text-align: center; width: 20%;">Debe</th>
        <th style="text-align: center; width: 20%;">Haber</th>
      </tr>
      
      @foreach ($details as $detail)
        <?php 
          if((isset($detail->debe)) && ($detail->debe != 0)){
            $total_debe += $detail->debe;
          }else if((isset($detail->haber)) && ($detail->haber != 0)){
            $total_haber += $detail->haber;
          }

          if(($controlador == true)){
            $code_one_old = $detail->code_one;
            $code_two_old = $detail->code_two;
            $code_three_old = $detail->code_three;
            $code_four_old = $detail->code_four;
            $code_five_old = $detail->code_five;
            $controlador = false;
          }
        ?>
        @if ($controlador_inicial)
          <?php 
            $controlador_inicial = false;
          ?>
          </table>
          <table style="width: 100%;">
            <tr>
              <th style="text-align: left;"> &nbsp;&nbsp;{{  $detail->account_description }} - {{ $detail->code_one ?? ''}}.{{ $detail->code_two ?? ''}}.{{ $detail->code_three ?? ''}}.{{ $detail->code_four ?? ''}}.{{ $detail->code_five ?? ''}}</th>
            </tr>
          </table>
          <table style="width: 100%;">
        @endif




        @if (($code_one_old == $detail->code_one) && ($code_two_old == $detail->code_two) && ($code_three_old == $detail->code_three)
            && ($code_four_old == $detail->code_four) && ($code_five_old == $detail->code_five))
             <tr>
              <td style="text-align: center; width: 20%;">{{ $detail->date ?? ''}}</td>
              <td style="text-align: center; width: 10%;">{{ $detail->id_header ?? ''}}</td>
              <td style="text-align: center; width: 30%;">{{ $detail->header_description ?? ''}}</td>
              <td style="text-align: center; width: 20%;">{{ number_format($detail->debe ?? 0, 2, ',', '.')}}</td>
              <td style="text-align: center; width: 20%;">{{ number_format($detail->haber ?? 0, 2, ',', '.')}}</td>
            </tr>
            
        @else
          <?php 
            //Aqui se realiza un cambio, por ende el controlador pasa a true.
            $controlador = true;
          ?>
        
          </table>
          <table style="width: 100%;">
            <tr>
              <th style="text-align: left;">&nbsp;&nbsp;{{  $detail->account_description }} - {{ $detail->code_one ?? ''}}.{{ $detail->code_two ?? ''}}.{{ $detail->code_three ?? ''}}.{{ $detail->code_four ?? ''}}.{{ $detail->code_five ?? ''}}</th>
            </tr>
          </table>
          <table style="width: 100%;">
            
          <tr>
            <td style="text-align: center; width: 20%;">{{ $detail->date ?? ''}}</td>
            <td style="text-align: center; width: 10%;">{{ $detail->id_header ?? ''}}</td>
            <td style="text-align: center; width: 30%;">{{ $detail->header_description ?? ''}}</td>
            <td style="text-align: center; width: 20%;">{{ number_format($detail->debe ?? 0, 2, ',', '.')}}</td>
            <td style="text-align: center; width: 20%;">{{ number_format($detail->haber ?? 0, 2, ',', '.')}}</td>
            
          </tr>
        @endif
       
      @endforeach
    
  </table>
@endif
    <table style="width: 100%;">
      <tr>
        <th style="text-align: center; width: 20%; border-color: white;"></th>
        <th style="text-align: center; width: 10%; border-color: white;"></th>
        <th style="text-align: center; width: 30%; border-color: white; border-right-color: black;"></th>
        <th style="text-align: center; width: 20%;">{{ number_format($total_debe ?? 0, 2, ',', '.')}}</th>
        <th style="text-align: center; width: 20%;">{{ number_format($total_haber ?? 0, 2, ',', '.')}}</th>
      </tr>
    </table>
    <table style="width: 100%;">
      <tr>
        <th style="text-align: center; width: 20%; border-color: white;"></th>
        <th style="text-align: center; width: 10%; border-color: white;"></th>
        <th style="text-align: center; width: 30%; border-color: white;"></th>
        <th style="text-align: center; width: 20%; border-color: white; border-right-color: black;">Saldo del periodo</th>
        <th style="text-align: center; width: 20%;">{{ number_format($total_debe - $total_haber, 2, ',', '.')}}</th>
      </tr>
    </table>
    
</body>
</html>
