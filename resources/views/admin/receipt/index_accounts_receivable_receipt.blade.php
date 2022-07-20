@extends('admin.layouts.dashboard')

@section('content')

<ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
    @if(Auth::user()->role_id  != '11')
    <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('receipt.accounts_receivable','index') }}" role="tab" aria-controls="profile" aria-selected="false">Relaciones de Gastos</a>
    </li>
    @endif
    <li class="nav-item" role="presentation">
        <a class="nav-link active font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('receipt.accounts_receivable_receipt','index') }}" role="tab" aria-controls="profile" aria-selected="false">Recibos de Condominio</a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('receipt.accounts_receivable_receipt_resumen','index') }}" role="tab" aria-controls="profile" aria-selected="false">Resumen de Recibos</a>
    </li>
  </ul>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-sm-12">
            <div class="card">
                <form id="formPost" method="POST" action="{{ route('receipt.store_accounts_receivable_receipt') }}">
                    @csrf

                <input type="hidden" name="id_client" value="{{$client->id ?? null}}" readonly>
                <input type="hidden" name="id_vendor" value="{{$vendor->id ?? null}}" readonly>


                <div class="card-body">
                        <div class="form-group row">
                            <label for="date_end" class="col-sm-1 col-form-label text-md-right">Hasta:</label>

                            <div class="col-sm-3">
                                <input id="date_end" type="date" class="form-control @error('date_end') is-invalid @enderror" name="date_end" value="{{ date('Y-m-d', strtotime($date_end ?? $datenow))}}" required autocomplete="date_end">

                                @error('date_end')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            @if(Auth::user()->role_id  != '11')
                                <div class="col-sp-2 offset-sp-1">
                                    <select class="form-control" name="type" id="type">
                                        @if (isset($client))
                                            <option value="todo">Todo</option>
                                            <option selected value="cliente">Por Propietario</option>
                                        @elseif (isset($vendor))
                                            <option value="todo">Todo</option>
                                            <option value="cliente">Por Propietario</option>
                                        @else
                                            <option selected value="todo">Todo</option>
                                            <option value="cliente">Por Propietario</option>
                                        @endif
                                    </select>
                                </div>
                         
                            @if (isset($client))
                                <label id="client_label1" for="clients" class="col-sm-2 text-md-right">Propietario:</label>
                                <label id="client_label2" name="id_client" value="{{ $client->id }}" for="clients" class="col-sm-3">{{ $client->name }}</label>
                            @endif
                            @if (isset($vendor))
                                <label id="vendor_label2" name="id_vendor" value="{{ $vendor->id }}" for="vendors" class="col-sm-3">{{ $vendor->name }}</label>
                            @endif
                            
                            <div id="client_label3" class="form-group col-sm-1">
                                <a id="route_select" href="{{ route('receipt.selectownersreceipt') }}" title="Seleccionar Propietario"><i class="fa fa-eye"></i></a>  
                            </div>
                           @endif
                            <!--<div class="col-sm-3  dropdown mb-4">
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
                            </div> -->
                        </div>

                        <div class="form-group row">

                            <div class="col-sm-3">
                                <select class="form-control" name="typeinvoice" id="typeinvoice">
                                    @if (isset($typeinvoice))
                                        @if ($typeinvoice == 'notas')
                                            <option selected value="notas">Cobrados</option>
                                        @elseif($typeinvoice == 'facturas')
                                            <option selected value="facturas">Recibos de Condominio</option>
                                        @else
                                            <option selected value="todo">Todos</option>
                                        @endif
                                        <option disabled value="todo">-----------------</option>
                                        <option value="todo">Todos</option>
                                        <option value="notas">Cobrados</option>
                                        <option value="facturas">Recibos de Condominio</option>
                                    @else
                                        <option selected value="todo">Todos</option>
                                        <option value="notas">Cobrados</option>
                                        <option value="facturas">Recibos de Condominio</option>
                                    @endif
                                </select>
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
                            <div class="col-sm-2">
                                <button type="submit" class="btn btn-primary ">
                                    Buscar
                                </button>
                            </div>
                        </div>
                    </form>
                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item" src="{{ route('receipt.accounts_receivable_pdf_receipt',[$coin ?? 'bolivares',$date_end ?? $datenow,$typeinvoice ?? 'todo',$typeperson ?? 'ninguno',$client->id ?? $vendor->id ?? null]) }}" allowfullscreen></iframe>
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
        var old_action = document.getElementById("formPost").action;
        /*document.getElementById("formPost").action = "{{ route('export_reports.accountsreceivable') }}";*/
        document.getElementById("formPost").submit();
        document.getElementById("formPost").action = old_action;
    }

    let client  = "<?php echo $client->name ?? 0 ?>";  
    let vendor  = "<?php echo $vendor->name ?? 0 ?>"; 

    if(client != 0){
        $("#client_label1").show();
        $("#client_label2").show();
        $("#client_label3").show();
    }else if(vendor != 0){
        $("#client_label1").show();
        $("#client_label2").show();
        $("#client_label3").show();
    }else{
        $("#client_label1").hide();
        $("#client_label2").hide();
        $("#client_label3").hide();
    }
    

    $("#type").on('change',function(){
            type = $(this).val();
            
            if(type == 'todo'){
                $("#client_label1").hide();
                $("#client_label2").hide();
                $("#client_label3").hide();
            }else {
                $("#client_label1").show();
                $("#client_label2").show();
                $("#client_label3").show();
            }
        });

    </script> 

@endsection