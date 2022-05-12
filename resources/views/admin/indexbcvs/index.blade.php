@extends('admin.layouts.dashboard')

@section('content')

   

<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
        <div class="col-md-6">
            <h2>Indices BCV</h2>
        </div>   
       
    </div>

  </div>

  {{-- VALIDACIONES-RESPUESTA--}}
@include('admin.layouts.success')   {{-- SAVE --}}
@include('admin.layouts.danger')    {{-- EDITAR --}}
@include('admin.layouts.delete')    {{-- DELELTE --}}
{{-- VALIDACIONES-RESPUESTA --}}

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Listado de Indices BCV</h6>
    </div>
   
    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
            <tr>
                <th>Fecha Emisión</th>
                <th>Periodo</th>
                <th>Mes</th>
                <th>Descripción</th>
                <th>Tasa Promedio A/P</th>
                <th>Tasa Activa</th>
            
              
            </tr>
            </thead>
            
            <tbody>
                @if (empty($indexbcvs))
                @else
                    @foreach ($indexbcvs as $key => $var)
                    <?php
                    $mes = str_pad($var->month, 2, "0", STR_PAD_LEFT);
                    /*$ano = substr($date_end, 6, 4); //fecha end
                    $mes = substr($date_end, 3, 2);*/
                    
                    if($mes == '01'){
                                $nro_mes    = "01";
                                $mes_nombre = "ENERO";
                            }elseif($mes == '02'){
                                $nro_mes    = "02";
                                $mes_nombre = "FEBRERO";
                            }elseif($mes == '03'){
                                $nro_mes    = "03";
                                $mes_nombre = "MARZO";
                            }elseif($mes == '04'){
                                $nro_mes    = "04";
                                $mes_nombre = "ABRIL";
                            }elseif($mes == '05'){
                                $nro_mes    = "05";
                                $mes_nombre = "MAYO";
                            }elseif($mes == '06'){
                                $nro_mes    = "06";
                                $mes_nombre = "JUNIO";
                            }elseif($mes == '07'){
                                $nro_mes    = "07";
                                $mes_nombre = "JULIO";
                            }elseif($mes == '08'){
                                $nro_mes    = "08";
                                $mes_nombre = "AGOSTO";
                            }elseif($mes == '09'){
                                $nro_mes    = "09";
                                $mes_nombre = "SEPTIEMBRE";
                            }elseif ($mes == '10'){
                                $nro_mes    = "10";
                                $mes_nombre = "OCTUBRE";
                            }elseif ($mes == '11'){
                                $nro_mes    = "11";
                                $mes_nombre = "NOVIEMBRE";
                            }else {
                                $nro_mes    = "12";
                                $mes_nombre = "DICIEMBRE";
                            }
                    
                    ?>
                   
                   
                   
                   
                    <tr>
                    <td>{{$var->date}}</td>
                    <td>{{$var->period}}</td>
                    <td>{{$mes}}</td>
                    <td>{{$mes_nombre}}</td>
                    <td>{{$var->rate_average_a_p}}</td>
                    <td>{{$var->rate_active}}</td>
                    @endforeach
                @endif
            </tbody>
        </table>
        </div>
    </div>
</div>

@endsection
@section('javascript')

    <script>
    $('#dataTable').DataTable({
        "ordering": false,
        "order": [],
        'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
    });

    </script> 

@endsection