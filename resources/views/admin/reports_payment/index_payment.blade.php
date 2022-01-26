@extends('admin.layouts.dashboard')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-sm-12">
            <div class="card">
                <form method="POST" action="{{ route('reportspayment.store_payment') }}">
                    @csrf

                <input type="hidden" name="id_client" value="{{$client->id ?? null}}" readonly>
                <input type="hidden" name="id_provider" value="{{$provider->id ?? null}}" readonly>
                <input type="hidden" name="id_vendor" value="{{$vendor->id ?? null}}" readonly>

                <div class="card-header text-center h4">
                        Pagos Realizados;
                </div>

                <div class="card-body">
                        <div class="form-group row">
                            <label for="date_end" class="col-sm-1 col-form-label text-md-right">desde:</label>
                            <div class="col-sm-3">
                                <input id="date_begin" type="date" class="form-control @error('date_begin') is-invalid @enderror" name="date_begin" value="{{  date('Y-m-d', strtotime($datebeginyear ?? $date_begin ?? $datenow)) }}" required autocomplete="date_begin">

                                @error('date_begin')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="date_end" class="col-sm-1 col-form-label text-md-right">hasta:</label>

                            <div class="col-sm-3">
                                <input id="date_end" type="date" class="form-control @error('date_end') is-invalid @enderror" name="date_end" value="{{ date('Y-m-d', strtotime($date_end ?? $datenow))}}" required autocomplete="date_end">

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
                            <div class="col-sm-1">
                            <button type="submit" class="btn btn-primary ">
                                Buscar
                             </button>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2 offset-sm-1">
                                <select class="form-control" name="type" id="type">
                                    @if (isset($client))
                                        <option value="todo">Todo</option>
                                        <option selected value="cliente">Cliente</option>
                                        <option value="provider">Proveedor</option>
                                        <option value="vendor">Vendedor</option>
                                    @elseif (isset($provider))
                                        <option value="todo">Todo</option>
                                        <option value="cliente">Cliente</option>
                                        <option selected value="provider">Proveedor</option>
                                        <option value="vendor">Vendedor</option>
                                    @elseif (isset($vendor))
                                        <option value="todo">Todo</option>
                                        <option value="cliente">Cliente</option>
                                        <option value="provider">Proveedor</option>
                                        <option selected value="vendor">Vendedor</option>
                                    @else
                                        <option selected value="todo">Todo</option>
                                        <option value="cliente">Cliente</option>
                                        <option value="provider">Proveedor</option>
                                        <option value="vendor">Vendedor</option>
                                    @endif
                                </select>
                            </div>
                            @if (isset($client))
                            <label id="client_label1" for="clients" class="col-sm-1 text-md-right">Cliente:</label>
                                <label id="client_label2" name="id_client" value="{{ $client->id }}" for="clients" class="col-sm-3">{{ $client->name }}</label>
                            @endif
                            @if (isset($provider))
                                <label id="provider_label2" name="id_provider" value="{{ $provider->id }}" for="providers" class="col-sm-3">{{ $provider->razon_social ?? ''}}</label>
                            @endif
                            @if (isset($vendor))
                                <label id="vendor_label2" name="id_vendor" value="{{ $vendor->id }}" for="vendors" class="col-sm-3">{{ $vendor->name ?? ''}} {{ $vendor->surname ?? ''}}</label>
                            @endif
                            <div id="client_label3" class="form-group col-sm-1">
                                <a id="route_select" href="{{ route('reportspayment.select_client') }}" title="Seleccionar"><i class="fa fa-eye"></i></a>  
                            </div>
                        </div>
                       
                    </form>
                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item" src="{{ route('reportspayment.payment_pdf',[$coin ?? 'bolivares',$date_begin ?? $datenow,$date_end ?? $datenow,$typeperson ?? 'ninguno',$client->id ?? $provider->id ?? $vendor->id ?? null]) }}" allowfullscreen></iframe>
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

     /*if(type == 'todo'){
                $("#client_label1").hide();
                $("#client_label2").hide();
                $("#client_label3").hide();
            }else if(type == 'provider'){
               
                $("#client_label1").show();
                $("#client_label2").show();
                $("#client_label3").show();
            }else if(type == 'vendor'){
                
                $("#client_label1").show();
                $("#client_label2").show();
                $("#client_label3").show();
            }else{
               
                $("#client_label1").show();
                $("#client_label2").show();
                $("#client_label3").show();
            } */




    $('#dataTable').DataTable({
        "ordering": false,
        "order": [],
        'aLengthMenu': [[-1, 50, 100, 150, 200], ["Todo",50, 100, 150, 200]]
    });

    
    
    let client  = "<?php echo $client->name ?? 0 ?>";  
    let vendor  = "<?php echo $vendor->name ?? 0 ?>"; 
    let provider  = "<?php echo $provider->name ?? 0 ?>";



    if(client != 0){
        document.getElementById("route_select").href = "{{ route('reportspayment.select_client') }}";
        $("#client_label1").show();
        $("#client_label2").show();
        $("#client_label3").show();
    }
    if(vendor != 0){
        document.getElementById("route_select").href = "{{ route('reportspayment.select_vendor') }}";
        $("#client_label1").show();
        $("#client_label2").show();
        $("#client_label3").show();
    }
    
    if(provider != 0){
        document.getElementById("route_select").href = "{{ route('reportspayment.select_provider') }}";
        $("#client_label1").show();
        $("#client_label2").show();
        $("#client_label3").show();
    }
           
           type = $('#type').val();
          
          
          if(type == 'todo'){
                $("#client_label1").hide();
                $("#client_label2").hide();
                $("#client_label3").hide();
          }
          if(type == 'provider'){
            document.getElementById("route_select").href = "{{ route('reportspayment.select_provider') }}";
            $("#client_label1").show();
            $("#client_label2").show();
            $("#client_label3").show();
          }


    $("#type").on('change',function(){
            type = $(this).val();
            
            if(type == 'todo'){
                document.getElementById("route_select").href = "#";
                $("#client_label1").hide();
                $("#client_label2").hide();
                $("#client_label3").hide();
            }
            if(type == 'provider'){
                document.getElementById("route_select").href = "{{ route('reportspayment.select_provider') }}";
                $("#client_label1").show();
                $("#client_label2").show();
                $("#client_label3").show();
            }
            if(type == 'vendor'){
                document.getElementById("route_select").href = "{{ route('reportspayment.select_vendor') }}";
                $("#client_label1").show();
                $("#client_label2").show();
                $("#client_label3").show();
            }
            
            if(type == 'cliente'){
                document.getElementById("route_select").href = "{{ route('reportspayment.select_client') }}";
                $("#client_label1").show();
                $("#client_label2").show();
                $("#client_label3").show();
            }
        });

    </script> 

@endsection