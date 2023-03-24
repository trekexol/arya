@extends('admin.layouts.dashboard')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-sm-12">
            <div class="card">
                <form method="POST" action="{{ route('reports.store_accounts_bc') }}">
                    @csrf

                <input type="hidden" name="id_client" value="{{$client->id ?? null}}" readonly>

                <div class="card-header text-center h4">
                        Balance de Comprobación
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
                            <label for="date_end" class="col-sm-1 col-form-label text-md-right">hasta </label>

                            <div class="col-sm-3">
                                <input id="date_end" type="date" class="form-control @error('date_end') is-invalid @enderror" name="date_end" value="{{ date('Y-m-d', strtotime($date_end ?? $datenow))}}" required autocomplete="date_end">

                                @error('date_end')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-sm-2">
                                <select class="form-control" name="level" id="level">
                                @if (isset($level))
                                    <option selected value="{{ $level }}">Nivel {{ $level }}</option>
                                    <option disabled value="">---------</option>
                                    <option value="1">Nivel 1</option>
                                    <option value="2">Nivel 2</option>
                                    <option value="3">Nivel 3</option>
                                    <option  value="4">Nivel 4</option>
                                    <option  value="5">Nivel 5</option>
                                @else
                                    <option value="1">Nivel 1</option>
                                    <option value="2">Nivel 2</option>
                                    <option value="3">Nivel 3</option>
                                    <option value="4">Nivel 4</option>
                                    <option selected value="5">Nivel 5</option>
                                @endif
                                    
                               
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <select class="form-control" name="coin" id="coin" required>
                                    @if(isset($coin))
                                        @if($coin == 'bolivares')
                                        <option disabled selected value="{{ $coin }}">Bolívares</option>                                        
                                        @elseif($coin == 'dactual')
                                        <option disabled selected value="{{ $coin }}">Dólares a tasa BCV</option>
                                        @elseif($coin == 'dolares')
                                        <option disabled selected value="{{ $coin }}">Dólares a tasa Promedio</option>                                      
                                        @endif
                                        <option disabled  value="">-----------</option>
                                        <option  value="bolivares">Bolívares</option>
                                        <option  value="dactual">Dólares a tasa BCV</option>
                                        <option  value="dolares">Dólares a tasa Promedio</option>
                                    @else

                                        <option selected value="bolivares">Bolívares</option>
                                        <option value="dactual">Dólares a tasa BCV</option>
                                        <option value="dolares">Dólares a tasa Promedio</option>

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
                            <iframe class="embed-responsive-item" src="{{ route('reports.accounts_bc_pdf',[$coin ?? 'bolivares',$level ?? 5,$datebeginyear ?? $date_begin ?? $datenow,$date_end ?? $datenow]) }}" allowfullscreen></iframe>
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