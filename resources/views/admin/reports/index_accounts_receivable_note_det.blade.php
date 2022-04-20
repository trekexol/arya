@extends('admin.layouts.dashboard')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-sm-12">
            <div class="card">
                <form id="formPost" method="POST" action="{{ route('reports.store_accounts_receivable_note_det') }}">
                    @csrf

                <input type="hidden" name="id_client" value="{{$client->id ?? null}}" readonly>
                <input type="hidden" name="id_vendor" value="{{$vendor->id ?? null}}" readonly>
                <input type="hidden" name="coin_form" id="coin_form" value="{{$coin ?? "bolivares"}}" readonly>


                <div class="card-header text-center h4">
                        Notas de Entrega Detalle
                </div>

                <div class="card-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="date_begin" class="col-sm-1 col-form-label text-md-right">Desde:</label>

                            <div class="col-sm-3">
                                <input id="date_begin" type="date" class="form-control @error('date_begin') is-invalid @enderror" name="date_begin" value="{{  date('Y-m-d', strtotime($date_frist ?? '')) }}" required autocomplete="date_begin">
                            </div>
                            <div class="col-sm-2">
                                <select class="form-control" name="type" id="type">
                                    @if ($typepersone == 'cliente')
                                        <option value="todo">Todos</option>
                                        <option selected value="cliente">Por Cliente</option>
                                        <option value="vendor">Por Vendedor</option>
                                    @endif
                                    
                                    @if ($typepersone == 'vendor')
                                        <option value="todo">Todos</option>
                                        <option value="cliente">Por Cliente</option>
                                        <option selected value="vendor">Por Vendedor</option>
                                    @endif
                                    
                                    @if ($typepersone == 'todo' || $typepersone == null)
                                        <option selected value="todo">Todos</option>
                                        <option value="cliente">Por Cliente</option>
                                        <option value="vendor">Por Vendedor</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-sm-3  dropdown mb-4">
                                <button class="btn btn-success" type="button"
                                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="false"
                                    aria-expanded="false">
                                    <i class="fas fa-bars"></i>
                                    Exportaciones
                                </button>
                                <div class="dropdown-menu animated--fade-in"
                                    aria-labelledby="dropdownMenuButton">
                                    <a href="#" onclick="exportToExcel();" class="dropdown-item bg-light">Exportar a Excel</a> 
                                </div>
                            </div> 
                            @if ($typepersone == 'cliente' && isset($client->id))
                            <label id="client_label1" for="clients" class="col-sm-2">Cliente:</label>
                            <label id="client_label2" name="id_client" value="{{ $client->id }}" for="clients" class="col-2">{{ $client->name }} {{ $client->surname }}</label>
                            @endif
                            @if ($typepersone == 'vendor' && isset($vendor->id))
                            <label id="client_label1" for="clients" class="col-sm-2">Vendedor:</label>
                                <label id="vendor_label2" name="id_vendor" value="{{ $vendor->id }}" for="vendors" class="col-2">{{ $vendor->name }} {{ $vendor->surname }}</label>
                            @endif
                            
                            <div id="client_label3" class="form-group col-sm-1 text-md-left">
                                <a id="route_select" href="{{ route('reports.select_client_note') }}" title="Seleccionar Cliente"><i class="fa fa-eye"></i></a>  
                            </div>
                            
                        </div>

                        <div class="form-group row">
                            <label for="date_end" class="col-sm-1 col-form-label text-md-right">Hasta:</label>

                            <div class="col-sm-3">
                                <input id="date_end" type="date" class="form-control @error('date_end') is-invalid @enderror" name="date_end" value="{{ date('Y-m-d', strtotime($date_end ?? ''))}}" required autocomplete="date_end">

                                @error('date_end')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
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
                            <div class="col-sm-4">
                                <select class="form-control" name="typeinvoice" id="typeinvoice">
                                    @if (isset($typeinvoice))
                                        @if ($typeinvoice == 'notast')
                                            <option selected value="notast">Notas de Entrega</option>
                                        @elseif($typeinvoice == 'notas')
                                            <option selected value="notas">Notas Sin Facturar</option>                                            
                                        @elseif($typeinvoice == 'notase')
                                            <option selected value="notase">Notas Eliminadas</option>
                                        @elseif($typeinvoice == 'facturasc')
                                            <option selected value="facturasc">Notas Facturadas y Cobradas</option>
                                        @elseif($typeinvoice == 'facturas')
                                            <option selected value="facturas">Notas Facturadas Pendientes</option>
                                        @endif
                                        <option disabled value="todo">-----------------</option>
                                        <option value="todo">Todo</option>
                                        <option value="notast">Notas de Entrega</option>
                                        <option value="notas">Notas Sin Facturar</option> 
                                        <option value="notase">Notas Eliminadas</option>
                                        <option value="facturasc">Notas Facturadas y Cobradas</option>
                                        <option value="facturas">Notas Facturadas Pendientes</option>
                                    @else
                                        <option selected value="todo">Todo</option>
                                        <option value="notast">Notas de Entrega</option>
                                        <option value="notas">Notas Sin Facturar</option> 
                                        <option value="notase">Notas Eliminadas</option>
                                        <option value="facturasc">Notas Facturadas y Cobradas</option>
                                        <option value="facturas">Notas Facturadas Pendientes</option>
                                        
                                    @endif
                                </select>
                            </div>
                            <div class="col-sm-1">
                                <button type="submit" class="btn btn-primary ">
                                    Buscar
                                 </button>
                                </div>
                        </div>
                    </form>
                        <div class="embed-responsive embed-responsive-16by9">

                            <iframe class="embed-responsive-item" src="{{route('reports.accounts_receivable_note_det_pdf',[$coin ?? 'bolivares',$date_end ?? '',$typeinvoice ?? 'todo',$typepersone ?? 'todo', $id_client_or_vendor ?? 'nada-index',$date_frist ?? '0000-00-00'])}}" allowfullscreen></iframe>
                            -
                            </div>                                      
                        
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection


@section('javascript')

<script>
    $('#dataTable').DataTable({
        "ordering": false,
        "order": [],
        'aLengthMenu': [[-1, 50, 100, 150, 200], ["Todo",50, 100, 150, 200]]
    });
    
    function exportToExcel(){
  
        document.getElementById("coin_form").value = document.getElementById("coin").value;
        var old_action = document.getElementById("formPost").action;
        
        document.getElementById("formPost").action = "{{ route('export_reports.account_receivable_note_det') }}";
        document.getElementById("formPost").submit();
        document.getElementById("formPost").action = old_action;
    }

    let client  = "<?php echo $client->name ?? 0 ?>";  
    let vendor  = "<?php echo $vendor->name ?? 0 ?>"; 

    if(client != 0){
        
        $("#client_label1").show();
        $("#client_label1").html('Cliente:');
        $("#vendor_label2").html('');
        $("#client_label2").show();
        $("#client_label3").show();
        document.getElementById("route_select").href = "{{ route('reports.select_client_note_det') }}";
    }else if(vendor != 0){
        
        $("#client_label1").show();
        $("#client_label1").html('Vendedor:');
        $("#client_label2").show();
        $("#client_label3").show();
        document.getElementById("route_select").href = "{{ route('reports.select_vendor_note_det') }}";
    }else{
        $("#client_label2").html('');
        $("#client_label2").val('');
        $("#vendor_label2").html('');
        $("#client_label1").show();
        $("#client_label1").html('Todo:');
        //$("#client_label1").hide();
        $("#client_label2").hide();
        $("#client_label3").hide();
    }
    

    $("#type").on('change',function(){
            type = $(this).val();
            
            if(type == 'todo'){
                $("#client_label2").html('');
                $("#client_label2").val('');
                $("#vendor_label2").html('');
                $("#client_label1").show();
                $("#client_label1").html('Todo:');
                $("#client_label2").hide();
                $("#client_label3").hide();
            } 
            
            if(type == 'vendor'){
                $("#client_label2").html('');
                $("#client_label2").val('');
                $("#client_label1").show();
                $("#client_label1").html('Vendedor:');
                $("#client_label2").show();
                $("#client_label3").show();
                document.getElementById("route_select").href = "{{ route('reports.select_vendor_note_det') }}";
            }
            
            if(type == 'cliente'){
                $("#client_label2").html('');
                $("#client_label2").val('');
                $("#vendor_label2").html('');
                $("#client_label1").show();
                $("#client_label1").html('Cliente:');
                $("#client_label2").show();
                $("#client_label3").show();
                document.getElementById("route_select").href = "{{ route('reports.select_client_note_det') }}";
            }
        });

    </script> 




@endsection