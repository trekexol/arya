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
  <h4 style="color: black; text-align: center">NOTAS DE ENTREGA DETALLE</h4>
  <h5 style="color: black; text-align: center">Fecha de Desde: {{date_format(date_create($date_frist),"d-m-Y") ?? ''}}   /   Fecha de Hasta: {{ date_format(date_create($date_end),"d-m-Y")  ?? '' }}</h5>
 <?php 
    
    $total_por_facturar = 0;
    $total_por_cobrar = 0;
  
  ?>
<table style="width: 100%;">
  <tr>
    <th style="text-align: center; width:9%;">Fecha NE</th>
    <th style="text-align: center; width:5%;">NE</th>
    <th style="text-align: center; width:5%;">FAC</th>
    <th style="text-align: center; width:9%;">Fecha FAC</th>
    <th style="text-align: center; width:5%;">Status</th>
    <th style="text-align: center; width:1%;">Ctrl/Serie</th>
    <th style="text-align: center;">Cliente</th>
    <th style="text-align: center;">Vendedor</th>
    <th style="text-align: center;">Total</th>
    <th style="text-align: center;">Abono</th>
    <th style="text-align: center;">Por Cobrar</th>
  </tr> 

  @foreach ($quotations as $quotation)
    <?php 
    
      if(isset($coin) && $coin != 'bolivares'){

        $quotation->amount_with_iva = ($quotation->amount_with_iva - ($quotation->retencion_iva ?? 0) - ($quotation->retencion_islr ?? 0)) / ($quotation->bcv ?? 1);
        //$quotation->amount_anticipo = ($quotation->amount_anticipo ?? 0) / ($quotation->bcv ?? 1);

        $por_cobrar = (($quotation->amount_with_iva ?? 0) - ($quotation->amount_anticipo ?? 0));
        $total_por_cobrar += $por_cobrar;
        $total_por_facturar += $quotation->amount_with_iva;
      }else{
        $quotation->amount_with_iva = ($quotation->amount_with_iva - $quotation->retencion_iva - $quotation->retencion_islr);
        $por_cobrar = ($quotation->amount_with_iva ?? 0) - ($quotation->amount_anticipo ?? 0);
        $total_por_cobrar += $por_cobrar;
        $total_por_facturar += $quotation->amount_with_iva;
      }

      $tipo = '';
      if ($quotation->number_delivery_note > 0) {
        $tipo = 'Nota de Entrega';
      }
      if ($quotation->number_invoice > 0){
        $tipo = 'Factura';
      }

      if(isset($quotation->date_billing)){
        $quotation->date_billing = date_format(date_create($quotation->date_billing),"d-m-Y");
      }
      if(isset($quotation->date_delivery_note)){
        $quotation->date_delivery_note = date_format(date_create($quotation->date_delivery_note),"d-m-Y");
      }
      if(isset($quotation->date_quotation)){
        $quotation->date_quotation = date_format(date_create($quotation->date_quotation),"d-m-Y");
      }
    


      $quotations_product = DB::connection(Auth::user()->database_name)->table('quotation_products')
            ->where('quotation_products.id_quotation',$quotation->id)
            ->select('quotation_products.*')  
            ->get();     


    ?>
    <tr>
      <th style="text-align: center; font-weight: normal;">{{ $quotation->date_delivery_note}}</th>
      <th style="text-align: center; font-weight: normal;">{{ $quotation->number_delivery_note}}</th>
      <th style="text-align: center; font-weight: normal;">{{ $quotation->number_invoice}}</th>
      <th style="text-align: center; font-weight: normal;">{{ $quotation->date_billing}}</th>
      @if ($quotation->status == 'C')
      <th style="text-align: center; font-weight: normal; color:darkgreen">{{ $quotation->status}}</th>
      @endif
      @if ($quotation->status == '1')
      <th style="text-align: center; font-weight: normal;">NE</th>
      @endif
      @if ($quotation->status == 'P')
      <th style="text-align: center; font-weight: normal;color:blue">P</th>
      @endif
      @if ($quotation->status == 'X')
      <th style="text-align: center; font-weight: normal;color:red">X</th>
      @endif
      <th style="text-align: center; font-weight: normal;">{{ $quotation->serie ?? ''}}</th>
      <th style="text-align: center; font-weight: normal;">{{ $quotation->name_client ?? ''}}</th>
      <th style="text-align: center; font-weight: normal;">{{ $quotation->name_vendor ?? ''}} {{ $quotation->surname_vendor ?? ''}}</th>


      @if(isset($coin) && $coin == 'bolivares'){
        <th style="text-align: right; font-weight: normal;">{{ number_format(($quotation->amount_with_iva ?? 0), 2, ',', '.') }}</th>
        <th style="text-align: right; font-weight: normal;">{{ number_format(($quotation->amount_anticipo ?? 0), 2, ',', '.') }}</th>
        <th style="text-align: right; font-weight: normal;">{{ number_format($por_cobrar, 2, ',', '.') }}</th>
        @endif
        @if(isset($coin) && $coin == 'dolares'){
          <th style="text-align: right; font-weight: normal;">${{ number_format(($quotation->amount_with_iva ?? 0), 2, ',', '.') }}</th>
          <th style="text-align: right; font-weight: normal;">${{ number_format(($quotation->amount_anticipo ?? 0), 2, ',', '.') }}</th>
          <th style="text-align: right; font-weight: normal;">${{ number_format($por_cobrar, 2, ',', '.') }}</th>
        @endif 
        
        @if(!empty($quotations_product))

          @foreach ($quotations_product as $quotations_products)

             <?php

             
              $id_product = DB::connection(Auth::user()->database_name)
                    ->table('inventories')
                    ->where('id',$quotations_products->id_inventory)
                    ->select('product_id')
                    ->get()->first();

              $name_product = DB::connection(Auth::user()->database_name)
                    ->table('products')
                    ->where('id',$id_product->product_id)
                    ->select('description','code_comercial')
                    ->get()->first();

             ?>

          
            <tr>
              <th style="text-align: center; font-weight: normal;"></th>
              <th style="text-align: center; font-weight: normal;">{{ $quotation->number_delivery_note}}</th>
              <th style="text-align: center; font-weight: normal;">{{ $quotation->number_invoice}}</th>
              <th style="text-align: center; font-weight: normal;"></th>
              <th style="text-align: center; font-weight: normal;"></th>
              <th style="text-align: center; font-weight: normal;">{{$name_product->code_comercial ?? ''}}</th>
              <th style="text-align: center; font-weight: normal;">{{$name_product->description ?? ''}}</th>
              <th style="text-align: center; font-weight: normal;">{{$quotations_products->amount ?? ''}}</th>
              @if(isset($coin) && $coin == 'bolivares'){
              <th style="text-align: right; font-weight: normal;">{{number_format(($quotations_products->amount * $quotations_products->price ?? 0), 2, ',', '.')}}</th>
              @endif
              @if(isset($coin) && $coin == 'dolares'){
              <th style="text-align: right; font-weight: normal;">${{ number_format((($quotations_products->amount * $quotations_products->price)/$quotations_products->rate ?? 0), 2, ',', '.')}}</th>
              @endif  
              <th style="text-align: center; font-weight: normal;"></th>
              <th style="text-align: center; font-weight: normal;"></th>
            </tr>
          



          @endforeach
            

        @endif







   
    </tr> 
  @endforeach 




  <tr>
    <th style="text-align: center; font-weight: normal; border-color: white;">{{count($quotations)}} Regist.</th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: center; font-weight: normal; border-color: white;"></th>
    <th style="text-align: right; font-weight: normal; border-color: white; border-right-color: black;">TOTAL</th>


     
    @if(isset($coin) && $coin == 'bolivares'){
      <th style="text-align: right; font-weight: normal;">{{ number_format(($total_por_facturar ?? 0), 2, ',', '.') }}</th>
      <th style="text-align: right; font-weight: normal; border-color: white; border-right-color: black;"> Bs.</th>
      <th style="text-align: right; font-weight: normal;">{{ number_format($total_por_cobrar, 2, ',', '.') }}</th>
      @endif
      @if(isset($coin) && $coin == 'dolares'){
        <th style="text-align: right; font-weight: normal;">${{ number_format(($total_por_facturar ?? 0), 2, ',', '.') }}</th>
        <th style="text-align: right; font-weight: normal; border-color: white; border-right-color: black;">USD</th>
        <th style="text-align: right; font-weight: normal;">${{ number_format($total_por_cobrar, 2, ',', '.') }}</th>
        @endif 
  
  </tr> 
</table>

</body>
</html>