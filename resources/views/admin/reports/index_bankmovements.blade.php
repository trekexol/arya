@extends('admin.layouts.dashboard')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-sm-12">
            <div class="card">
                <form method="POST" action="{{ route('reports.store_bankmovements') }}">
                    @csrf

                <input type="hidden" name="id_bank" value="{{$bank->id ?? null}}" readonly>

                <div class="card-header text-center h4">
                        Movimientos Bancarios
                </div>

                <div class="card-body">
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <input id="date_begin" type="date" class="form-control @error('date_begin') is-invalid @enderror" name="date_begin" value="{{  date('Y-m-d', strtotime($datebeginyear ?? $date_begin ?? $datenow)) }}" required autocomplete="date_begin">

                                @error('date_begin')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                           
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
                            <div class="col-sm-2">
                                <button type="submit" class="btn btn-primary ">
                                    Buscar 
                                 </button>
                                </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <select class="form-control" name="account_bank" id="account_bank">
                                @if (isset($account_bank))
                                    <option selected value="{{ $account_bank->id }}">{{ $account_bank->description }}</option>
                                    <option disabled value="">---------</option>
                                @else
                                    <option selected >Seleccione un Banco</option>
                                @endif
                                
                                @foreach ($accounts_banks as $accounts_bank)
                                    <option value="{{ $accounts_bank->id }}">{{ $accounts_bank->description }}</option>
                                @endforeach
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <select class="form-control" name="type" id="type">
                                    @if(isset($type))
                                        <option disabled selected value="{{ $type }}">{{ $type }}</option>
                                        <option disabled  value="{{ $type }}">-----------</option>
                                    @endif
                                   
                                    <option value="Todo">Todo</option>
                                    <option value="Orden de Pago">Orden de Pago</option>
                                    <option value="Deposito">Deposito</option>
                                    <option value="Retiro">Retiro</option>
                                    <option value="Transferencia">Transferencia</option>
                                </select>
                            </div>
                            
                        </div>
                    </div>
                    </form>
                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item" src="{{ route('reports.bankmovements_pdf',[$type ?? 'Todo',$coin ?? 'bolivares',$datebeginyear ?? $date_begin ?? $datenow,$date_end ?? $datenow,$account_bank ?? null]) }}" allowfullscreen></iframe>
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

    
    

    let client  = "<?php echo $client->name ?? 0 ?>";  

    if(client != 0){
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
            }else{
                $("#client_label1").show();
                $("#client_label2").show();
                $("#client_label3").show();
            }
        });

    </script> 

@endsection