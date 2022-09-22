
  
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
  
  <h4 style="color: black; text-align: center; font-weight: bold;">Balance General</h4>

  <h5 style="color: black; text-align: center; font-weight: bold;">Periodo desde {{ date('d-m-Y', strtotime( $date_begin ?? $detail_old->created_at ?? '')) }} al {{ date('d-m-Y', strtotime( $date_end ?? $datenow)) }}</h5>

    <table>
      <tr>
        <th style="text-align: center; font-weight: normal; width: 58%; border-color: white; font-weight: bold;"></th>
        <th style="text-align: right; font-weight: normal; width: 21%; border-color: white; font-weight: bold;">{{ $coin ?? 'Bs' }}</th>
        <th style="text-align: center; font-weight: normal; width: 21%; border-color: white; font-weight: bold;">Petro</th>
      </tr> 
      <?php 
          //Variable booleana que me ayuda a controlar el cambio entre cuentas nivel 1
          $controlador_level1 = true;
          $total_level1 = 0;

          $controlador_level2 = true;
          $total_level2 = 0;

          $controlador_level3 = true;
          $total_level3 = 0;

          $controlador_level4 = true;
          $total_level4 = 0;

          $description4 = null;
          $description3 = null;
          $description2 = null;
          $description = null;
          
        ?>

        

      @foreach ($accounts as $account)

        @if ($account->level == 1 )
          @if ($controlador_level1 == false)
            @if(isset($description4))
              <tr>
                <th style="text-align: left; font-weight: normal; width: 58%; border-color: white; font-weight: bold; ">Total {{ $description4 }} : </th>
                <th style="text-align: right; font-weight: normal; width: 21%; border-color: white; font-weight: bold;">{{ number_format($total_level4, 2, ',', '.') }}</th>
                <th style="text-align: center; font-weight: normal; width: 21%; border-color: white; font-weight: bold;"></th>
              </tr> 
              <?php 
                $controlador_level4 = true;
              ?>
            @endif
            @if(isset($description3))
            <tr>
              <th style="text-align: left; font-weight: normal; width: 58%; border-color: white; font-weight: bold; ">Total {{ $description3 }} : </th>
              <th style="text-align: right; font-weight: normal; width: 21%; border-color: white; font-weight: bold;">{{ number_format($total_level3, 2, ',', '.') }}</th>
              <th style="text-align: center; font-weight: normal; width: 21%; border-color: white; font-weight: bold;"></th>
            </tr> 
            <?php 
              $controlador_level3 = true;
            ?>
            @endif
            @if(isset($description2))
            <tr>
              <th style="text-align: left; font-weight: normal; width: 58%; border-color: white; font-weight: bold; ">Total {{ $description2 }} : </th>
              <th style="text-align: right; font-weight: normal; width: 21%; border-color: white; font-weight: bold;">{{ number_format($total_level2, 2, ',', '.') }}</th>
              <th style="text-align: center; font-weight: normal; width: 21%; border-color: white; font-weight: bold;"></th>
            </tr> 
            <?php 
              $controlador_level2 = true;
            ?>
            @endif
            
            <tr>
              <th style="text-align: left; font-weight: normal; width: 58%; border-left-color: white; border-right-color: white; background:rgb(171, 224, 255); font-weight: bold; ">Total {{ $description }} : </th>
              <th style="text-align: right; font-weight: normal; width: 21%; border-color: white; font-weight: bold;">{{ number_format($total_level1, 2, ',', '.') }}</th>
              <th style="text-align: center; font-weight: normal; width: 21%; border-color: white; font-weight: bold;"></th>
            </tr> 
          @endif
            <tr>
              <th style="text-align: center; font-weight: normal; width: 58%; border-color: white; font-weight: bold;">{{ $account->description }}</th>
              <th style="text-align: center; font-weight: normal; width: 21%; border-color: white; font-weight: bold;"></th>
              <th style="text-align: center; font-weight: normal; width: 21%; border-color: white; font-weight: bold;"></th>
            </tr> 
            <?php 
              $description = $account->description;
              $total_level1 = $account->balance_previus + $account->debe - $account->haber;
            //si vuelve a encontrar otra cuenta nivel 1, nos imprimira el total de la cuenta anterior nivel 1
              $controlador_level1 = false;
            ?>
          
        @elseif ($account->level == 2 && ($level >= 2))
          @if ($controlador_level2 == false)
              @if(isset($description4))
                <tr>
                  <th style="text-align: left; font-weight: normal; width: 58%; border-color: white; font-weight: bold; padding-left: 20px;">Total {{ $description4 }} : </th>
                  <th style="text-align: right; font-weight: normal; width: 21%; border-color: white; font-weight: bold;">{{ number_format($total_level4, 2, ',', '.') }}</th>
                  <th style="text-align: center; font-weight: normal; width: 21%; border-color: white; font-weight: bold;"></th>
                </tr> 
                <?php 
                  $controlador_level4 = true;
                ?>
              @endif
              @if(isset($description3))
              <tr>
                <th style="text-align: left; font-weight: normal; width: 58%; border-color: white; font-weight: bold; padding-left: 20px;">Total {{ $description3 }} : </th>
                <th style="text-align: right; font-weight: normal; width: 21%; border-color: white; font-weight: bold;">{{ number_format($total_level3, 2, ',', '.') }}</th>
                <th style="text-align: center; font-weight: normal; width: 21%; border-color: white; font-weight: bold;"></th>
              </tr> 
              <?php 
                $controlador_level3 = true;
              ?>
              @endif
              @if(isset($description2))
              <tr>
                <th style="text-align: left; font-weight: normal; width: 58%; border-color: white; font-weight: bold; padding-left: 20px;">Total {{ $description2 }} : </th>
                <th style="text-align: right; font-weight: normal; width: 21%; border-color: white; font-weight: bold;">{{ number_format($total_level2, 2, ',', '.') }}</th>
                <th style="text-align: center; font-weight: normal; width: 21%; border-color: white; font-weight: bold;"></th>
              </tr> 
              @endif
          @endif
          <tr>
            <th style="text-align: left; font-weight: normal; width: 58%; border-color: white; font-weight: bold; padding-left: 20px;">{{ $account->description }}</th>
            <th style="text-align: left; font-weight: normal; width: 21%; border-color: white; font-weight: bold; padding-left: 20px;"></th>
            <th style="text-align: left; font-weight: normal; width: 21%; border-color: white; font-weight: bold; padding-left: 20px;"></th>
          </tr> 
          <?php 
              $description2 = $account->description;
              $total_level2 = $account->balance_previus + $account->debe - $account->haber;
            //si vuelve a encontrar otra cuenta nivel 1, nos imprimira el total de la cuenta anterior nivel 1
              $controlador_level2 = false;
            ?>
        @elseif ($account->level == 3 && ($level >= 3))
          @if ($controlador_level3 == false)
              @if(isset($description4))
                <tr>
                  <th style="text-align: left; font-weight: normal; width: 58%; border-color: white; font-weight: bold; padding-left: 20px;">Total {{ $description4 }} : </th>
                  <th style="text-align: right; font-weight: normal; width: 21%; border-color: white; font-weight: bold;">{{ number_format($total_level4, 2, ',', '.') }}</th>
                  <th style="text-align: center; font-weight: normal; width: 21%; border-color: white; font-weight: bold;"></th>
                </tr> 
                <?php 
                  $controlador_level4 = true;
                ?>
              @endif
              @if(isset($description3))
              <tr>
                <th style="text-align: left; font-weight: normal; width: 58%; border-color: white; font-weight: bold; padding-left: 40px;">Total {{ $description3 }} : </th>
                <th style="text-align: right; font-weight: normal; width: 21%; border-color: white; font-weight: bold;">{{ number_format($total_level3, 2, ',', '.') }}</th>
                <th style="text-align: center; font-weight: normal; width: 21%; border-color: white; font-weight: bold;"></th>

              </tr> 
              @endif
          @endif
            <tr>
              <th style="text-align: left; font-weight: normal; width: 58%; border-color: white; font-weight: bold; padding-left: 40px;">{{ $account->description }}</th>
              <th style="text-align: left; font-weight: normal; width: 21%; border-color: white; font-weight: bold; padding-left: 40px;"></th>
              <th style="text-align: left; font-weight: normal; width: 21%; border-color: white; font-weight: bold; padding-left: 40px;"></th>
            </tr> 
            
          <?php 
              $description3 = $account->description;
              $total_level3 = $account->balance_previus + $account->debe - $account->haber;
            //si vuelve a encontrar otra cuenta nivel 1, nos imprimira el total de la cuenta anterior nivel 1
              $controlador_level3 = false;
            ?>
          @elseif ($account->level == 4 && ($level >= 4))
          @if ($controlador_level4 == false)
              @if(isset($description4))
              <tr>
                <th style="text-align: left; font-weight: normal; width: 58%; border-color: white; font-weight: bold; padding-left: 40px;">Total {{ $description4 }} : </th>
                <th style="text-align: right; font-weight: normal; width: 21%; border-color: white; font-weight: bold;">{{ number_format($total_level4, 2, ',', '.') }}</th>
                <th style="text-align: center; font-weight: normal; width: 21%; border-color: white; font-weight: bold;"></th>

              </tr> 
              @endif
          @endif
            <tr>
              <th style="text-align: left; font-weight: normal; width: 58%; border-color: white; font-weight: bold; padding-left: 40px;">{{ $account->description }}</th>
              <th style="text-align: left; font-weight: normal; width: 21%; border-color: white; font-weight: bold; padding-left: 40px;"></th>
              <th style="text-align: left; font-weight: normal; width: 21%; border-color: white; font-weight: bold; padding-left: 40px;"></th>
            </tr> 
            
          <?php 
              $description4 = $account->description;
              $total_level4 = $account->balance_previus + $account->debe - $account->haber;
            //si vuelve a encontrar otra cuenta nivel 1, nos imprimira el total de la cuenta anterior nivel 1
              $controlador_level4 = false;
            ?>
          @elseif($level == 5)
            <?php 
              $total_level5 = $account->balance_previus + $account->debe - $account->haber;
            ?>
            <tr>
              <th style="text-align: left; font-weight: normal; width: 58%; border-color: white; padding-left: 60px;">{{ $account->description }}</th>
              <th style="text-align: right; font-weight: normal; width: 21%; border-color: white; ">{{ number_format($total_level5, 2, ',', '.') }}</th>
              <th style="text-align: left; font-weight: normal; width: 21%; border-color: white; "></th>
            </tr> 
          @endif
      
      @endforeach
      <!-- Imprimir los ultimos totales -->
      @if(isset($description4))
      <tr>
        <th style="text-align: left; font-weight: normal; width: 58%; border-color: white; font-weight: bold; padding-left: 20px;">Total {{ $description4 }} : </th>
        <th style="text-align: right; font-weight: normal; width: 21%; border-color: white; font-weight: bold;">{{ number_format($total_level4, 2, ',', '.') }}</th>
        <th style="text-align: center; font-weight: normal; width: 21%; border-color: white; font-weight: bold;"></th>
      </tr> 
      @endif
      @if(isset($description3))
      <tr>
        <th style="text-align: left; font-weight: normal; width: 58%; border-color: white; font-weight: bold; padding-left: 20px;">Total {{ $description3 }} : </th>
        <th style="text-align: right; font-weight: normal; width: 21%; border-color: white; font-weight: bold;">{{ number_format($total_level3, 2, ',', '.') }}</th>
        <th style="text-align: center; font-weight: normal; width: 21%; border-color: white; font-weight: bold;"></th>
      </tr> 
      @endif
      @if(isset($description2))
      <tr>
        <th style="text-align: left; font-weight: normal; width: 58%; border-color: white; font-weight: bold; ">Total {{ $description2 }} : </th>
        <th style="text-align: right; font-weight: normal; width: 21%; border-color: white; font-weight: bold;">{{ number_format($total_level2, 2, ',', '.') }}</th>
        <th style="text-align: center; font-weight: normal; width: 21%; border-color: white; font-weight: bold;"></th>
      </tr> 
      @endif
      <tr>
        <th style="text-align: left; font-weight: normal; width: 58%; border-left-color: white; border-right-color: white; background:rgb(171, 224, 255); font-weight: bold; ">Total {{ $description ?? '' }} : </th>
        <th style="text-align: right; font-weight: normal; width: 21%; border-color: white; font-weight: bold;">{{ number_format($total_level1, 2, ',', '.') }}</th>
        <th style="text-align: center; font-weight: normal; width: 21%; border-color: white; font-weight: bold;"></th>
      </tr> 


    
    </table>
@if($type == '1')
<table>
  <tr>
    <th style="text-align: left; font-weight: normal; width: 58%; border: white; font-weight: bold; ">Tasa Actual del BCV a la Fecha {{ number_format($tasa, 2, ',', '.') }} Bs.</th>
    <th style="text-align: right; font-weight: normal; width: 21%; border-color: white; font-weight: bold;"></th>
    <th style="text-align: center; font-weight: normal; width: 21%; border-color: white; font-weight: bold;"></th>
  </tr> 
</table>
@endif
</body>
</html>
