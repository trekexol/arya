@extends('admin.layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row py-lg-2">
       
        <div class="col-sm-6">
            <h2>Seleccione una Cuenta.</h2>
        </div>
        <div class="col-sm-2">
            <select class="form-control" name="coin" id="coin">
                @if(isset($coin))
                    <option disabled selected value="{{ $coin }}">{{ $coin }}</option>
                    <option disabled  value="{{ $coin }}">-----------</option>
                @else
                    <option disabled selected value="bolivares">Moneda</option>
                @endif
                
                <option  value="bolivares">Bolívares</option>
                <option value="dolares">Dólares</option>
            </select>
        </div>
    
    </div>
</div>
  <!-- /.container-fluid -->
  {{-- VALIDACIONES-RESPUESTA--}}
  @include('admin.layouts.success')   {{-- SAVE --}}
  @include('admin.layouts.danger')    {{-- EDITAR --}}
  @include('admin.layouts.delete')    {{-- DELELTE --}}
  {{-- VALIDACIONES-RESPUESTA --}}
<!-- DataTales Example -->
<!-- container-fluid -->
<div class="container-fluid">
<div class="card shadow mb-4">
    
    <div class="card-body">
        <div class="container">
            @if (session('flash'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{session('flash')}}
                <button type="button" class="close" data-dismiss="alert" aria-label="close">
                    <span aria-hidden="true">&times; </span>
                </button>
            </div>   
        @endif
        </div>
        
        <div class="table-responsive">
            <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                <tr > 
                    <th style="text-align: right;"></th>
                    <th style="text-align: right;">Código</th>
                    <th style="text-align: right;">Descripción</th>
                    <th style="text-align: right;">Nivel</th>

                   
                    
                </tr>
                </thead>
                
                <tbody>
                    @if (empty($accounts))
                    @else  
                        @foreach ($accounts as $account)
                        @if(isset($level))
                            @if($level >= $account->level)
                            <tr>
                                <td style="text-align:right; color:black; ">  
                                    @if ($account->level == 5)
                                        @if (isset($id_detail))
                                            <a href="{{ route('detailvouchers.edit',[$coin,$id_detail,$account->id]) }}" title="Seleccionar"><i class="fa fa-check"></i></a>
                                        @else
                                            <a href="{{ route('detailvouchers.create',[$coin,$header->id,$account->id]) }}" title="Seleccionar"><i class="fa fa-check"></i></a>
                                        @endif
                                    @endif
                                </td>
                                <td style="text-align:right; color:black; font-weight: bold;">{{$account->code_one}}.{{$account->code_two}}.{{$account->code_three}}.{{$account->code_four}}.{{ str_pad($account->code_five, 3, "0", STR_PAD_LEFT)}}</td>
                                <td style="text-align:left; color:black;">
                                    @if(isset($account->coin))
                                        <a href="{{ route('accounts.edit',$account->id) }}" style="color: black; font-weight: bold;" title="Ver Movimientos">{{$account->description}} ({{ $account->coin }})</a>
                                    @else
                                        <a href="{{ route('accounts.edit',$account->id) }}" style="color: black; font-weight: bold;" title="Ver Movimientos">{{$account->description}}</a>
                                   @endif
                                </td>
                                <td style="text-align:right; color:black; "></td>

                            </tr>   
                            @endif
    
    
    
    
                        @else
                        <tr>
                            <td style="text-align:right; color:black; ">  
                                @if ($account->level == 5)
                                    @if (isset($id_detail))
                                        <a href="{{ route('detailvouchers.edit',[$coin,$id_detail,$account->id]) }}" title="Seleccionar"><i class="fa fa-check"></i></a>
                                    @else
                                        <a href="{{ route('detailvouchers.create',[$coin,$header->id,$account->id]) }}" title="Seleccionar"><i class="fa fa-check"></i></a>
                                    @endif
                                @endif
                            </td>
                            <td style="text-align:right; color:black; font-weight: bold;">{{$account->code_one}}.{{$account->code_two}}.{{$account->code_three}}.{{$account->code_four}}.{{ str_pad($account->code_five, 3, "0", STR_PAD_LEFT)}}</td>
                            <td style="text-align:left; color:black;">
                                @if(isset($account->coin))
                                    <a href="{{ route('accounts.edit',$account->id) }}" style="color: black; font-weight: bold;" title="Ver Movimientos">{{$account->description}} ({{ $account->coin }})</a>
                                @else
                                    <a href="{{ route('accounts.edit',$account->id) }}" style="color: black; font-weight: bold;" title="Ver Movimientos">{{$account->description}}</a>
                               @endif
                            </td>
                            <td style="text-align:right; color:black; "></td>
                           
                               
                        </tr>   
                        
                        @endif
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
        $("#coin").on('change',function(){
            var coin = $(this).val();
            window.location = "{{route('detailvouchers.selectaccount', ['','',''])}}"+"/"+coin+"/"+"{{ $header->id }}"+"/"+"{{ $id_detail }}";
        });
    </script>
@endsection                      
