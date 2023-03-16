@extends('admin.layouts.dashboard')

@section('content')


<div class="row py-lg-2">
    <div class="col-sm-4 h5 ">
        Chequear Comprobantes
    </div>
<!-- Page Heading -->
</div>
  {{-- VALIDACIONES-RESPUESTA--}}
@include('admin.layouts.success')   {{-- SAVE --}}
@include('admin.layouts.danger')    {{-- EDITAR --}}
@include('admin.layouts.delete')    {{-- DELELTE --}}
{{-- VALIDACIONES-RESPUESTA --}}


<div class="card shadow mb-4">
    <div class="card-body">
        <form id="formPost" method="POST" action="{{ route('check_movements.comprobanteschks') }}">
            @csrf
    
            <div class="form-group row">
        
                    <div class="col-sm-2">
                    </div>
                    <label for="date_begin" class="col-sm-1 col-form-label text-md-right">Desde</label>
                   
                    <div class="col-sm-3">
                        <input id="date_begin" type="date" class="form-control @error('date_begin') is-invalid @enderror" name="date_begin" value="{{  date('Y-m-d', strtotime($date_begin ?? '')) }}" required autocomplete="date_begin">
    
                        @error('date_begin')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <label for="date_end" class="col-sm-1 col-form-label text-md-right">hasta </label>
    
                    <div class="col-sm-3">
                        <input id="date_end" type="date" class="form-control @error('date_end') is-invalid @enderror" name="date_end" value="{{ date('Y-m-d', strtotime($date_end ?? ''))}}" required autocomplete="date_end">
    
                        @error('date_end')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group col-md-2">
                        <button type="submit" class="btn btn-info" title="Buscar">Checkear</button>  
                    </div>
               
                </div>
          
            </div>
        </form>    
        <div class="table-responsive">
            <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th class="text-center">Comp.</th>
                    <th class="text-center">Fecha</th>
                    <th class="text-center">Debe</th>
                    <th class="text-center">Haber</th>
                    <th class="text-center">Diferencia</th>
                </tr>
                </thead>
                
                <tbody>
            <?php
                         $total_debe = 0;
                         $total_haber = 0;
                         $total_diferencia = 0;

                         $total_debe_1 = 0;
                         $total_debe_2 = 0;
                         $total_debe_3 = 0;
                         $total_debe_4 = 0;
                         $total_debe_5 = 0;
                         $total_debe_6 = 0;
                         $total_debe_7 = 0;
                         
                         $total_haber_1 = 0;
                         $total_haber_2 = 0;
                         $total_haber_3 = 0;
                         $total_haber_4 = 0;
                         $total_haber_5 = 0;
                         $total_haber_6 = 0;
                         $total_haber_7 = 0;


                        
            ?>
                    @for ($q=0; $q < count($a_headers);$q++)
                        
                        @if(($a_headers[$q][2]-$a_headers[$q][3]) <> 0)
                            <tr>
                                <td class="text-center"><a href="{{ route('detailvouchers.create',['bolivares',$a_headers[$q][0] ?? '']) }}" title="Ver comprobante contable">{{ $a_headers[$q][0] ?? '' }}</a></td>
                                <td class="text-center">{{$a_headers[$q][1]}}</td>
                                <td class="text-right">{{number_format($a_headers[$q][2], 2, '.', '')}}</td>
                                <td class="text-right">{{number_format($a_headers[$q][3], 2, '.', '')}}</td>
                                <td class="text-right">{{number_format($a_headers[$q][2], 2, '.', '') - number_format($a_headers[$q][3], 2, '.', '')}}</td>

                            </tr>    
                        @endif  
                        
                        <?php
                         $total_debe += number_format($a_headers[$q][2], 2, '.', '');
                         $total_haber += number_format($a_headers[$q][3], 2, '.', '');
                         $total_diferencia += number_format($a_headers[$q][2], 2, '.', '') - number_format($a_headers[$q][3], 2, '.', '');
                         
                         if ($a_headers[$q][4] == 1){
                             
                            $total_debe_1 += number_format($a_headers[$q][2], 2, '.', '');
                            $total_haber_1 += number_format($a_headers[$q][3], 2, '.', '');

                         }
                        
                         if ($a_headers[$q][4] == 2){
                             
                             $total_debe_2 += number_format($a_headers[$q][2], 2, '.', '');
                             $total_haber_2 += number_format($a_headers[$q][3], 2, '.', '');
 
                          }
                          if ($a_headers[$q][4] == 3){
                             
                             $total_debe_3 += number_format($a_headers[$q][2], 2, '.', '');
                             $total_haber_3 += number_format($a_headers[$q][3], 2, '.', '');
 
                          }
                          if ($a_headers[$q][4] == 4){
                             
                             $total_debe_4 += number_format($a_headers[$q][2], 2, '.', '');
                             $total_haber_4 += number_format($a_headers[$q][3], 2, '.', '');
 
                          }
                          if ($a_headers[$q][4] == 5){
                             
                             $total_debe_5 += number_format($a_headers[$q][2], 2, '.', '');
                             $total_haber_5 += number_format($a_headers[$q][3], 2, '.', '');
 
                          }
                          if ($a_headers[$q][4] == 6){
                             
                             $total_debe_6 += number_format($a_headers[$q][2], 2, '.', '');
                             $total_haber_6 += number_format($a_headers[$q][3], 2, '.', '');
 
                          }
                          if ($a_headers[$q][4] == 7){
                             
                             $total_debe_7 += number_format($a_headers[$q][2], 2, '.', '');
                             $total_haber_7 += number_format($a_headers[$q][3], 2, '.', '');
 
                          }

                        ?>
                        
                    @endfor

                </tbody>




                <tfoot>
                    <tr>
                        <th class="text-right"></th>
                        <th class="text-right">ACTIVO</th>
                        <th class="text-right font-weight-bold">{{$total_debe_1}}</th>
                        <th class="text-right font-weight-bold">{{$total_haber_1}}</th>
                        <th class="text-right font-weight-bold">{{$total_debe_1 - $total_haber_1}}</th>
                    </tr>
                    <tr>
                        <th class="text-right"></th>
                        <th class="text-right">PASIVO</th>
                        <th class="text-right font-weight-bold">{{$total_debe_2}}</th>
                        <th class="text-right font-weight-bold">{{$total_haber_2}}</th>
                        <th class="text-right font-weight-bold">{{$total_debe_2 - $total_haber_2}}</th>
                    </tr>
                    <tr>
                        <th class="text-right"></th>
                        <th class="text-right">PATRIMONIO</th>
                        <th class="text-right font-weight-bold">{{$total_debe_3}}</th>
                        <th class="text-right font-weight-bold">{{$total_haber_3}}</th>
                        <th class="text-right font-weight-bold">{{$total_debe_3 - $total_haber_3}}</th>
                    </tr>
                    <tr>
                        <th class="text-right"></th>
                        <th class="text-right">INGRESOS</th>
                        <th class="text-right font-weight-bold">{{$total_debe_4}}</th>
                        <th class="text-right font-weight-bold">{{$total_haber_4}}</th>
                        <th class="text-right font-weight-bold">{{$total_debe_4 - $total_haber_4}}</th>
                    </tr>
                    <tr>
                        <th class="text-right"></th>
                        <th class="text-right">COSTOS</th>
                        <th class="text-right font-weight-bold">{{$total_debe_5}}</th>
                        <th class="text-right font-weight-bold">{{$total_haber_5}}</th>
                        <th class="text-right font-weight-bold">{{$total_debe_5 - $total_haber_5}}</th>
                    </tr>
                    <tr>
                        <th class="text-right"></th>
                        <th class="text-right">GASTOS</th>
                        <th class="text-right font-weight-bold">{{$total_debe_6}}</th>
                        <th class="text-right font-weight-bold">{{$total_haber_6}}</th>
                        <th class="text-right font-weight-bold">{{$total_debe_6 - $total_haber_6}}</th>
                    </tr>
                    <tr>
                        <th class="text-right"></th>
                        <th class="text-right">OTROS INGRESOS Y EGRESOS</th>
                        <th class="text-right font-weight-bold">{{$total_debe_7}}</th>
                        <th class="text-right font-weight-bold">{{$total_haber_7}}</th>
                        <th class="text-right font-weight-bold">{{$total_debe_7 - $total_haber_7}}</th>
                    </tr>


                    <tr>
                        <th class="text-right"></th>
                        <th class="text-right">Total</th>
                        <th class="text-right font-weight-bold">{{$total_debe}}</th>
                        <th class="text-right font-weight-bold">{{$total_haber}}</th>
                        <th class="text-right font-weight-bold">{{$total_diferencia}}</th>
                    </tr>
                </tfoot>
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
            'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "Todo"]]
        });

        
    </script> 
@endsection