@extends('admin.layouts.dashboard')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-sm-12">
            <div class="card">
                <form id="formPost" method="POST" action="{{ route('report_payment_expenses.store') }}">
                    @csrf

                <input type="hidden" name="id_provider" value="{{$provider->id ?? null}}" readonly>

                <div class="card-header text-center h4">
                    Reporte Pagos de Compras
                </div>

                <div class="card-body">
                        <div class="form-group row">
                            <label for="date_begin" class="col-sm-1 col-form-label text-md-right">Desde:</label>

                            <div class="col-sm-3">
                                <input id="date_begin" type="date" class="form-control @error('date_begin') is-invalid @enderror" name="date_begin" value="{{  date('Y-m-d', strtotime($date_begin ?? $datenow ?? '')) }}" required autocomplete="date_begin">
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
                            @if (isset($provider))
                                <label id="provider_label1" for="providers" class="col-sm-1 text-md-right">Proveedor:</label>
                                <label id="provider_label2" name="id_provider" value="{{ $provider->id }}" for="providers" class="col-sm-3">{{ $provider->razon_social ?? ''}}</label>
                            @endif


                            <div id="client_label3" class="form-group col-sm-1">
                                <a id="route_select" href="{{ route('report_payment_expenses.selectProvider') }}" title="Seleccionar Proveedor"><i class="fa fa-eye"></i></a>
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
                                    @if (isset($provider))
                                        <option value="todo">Todo</option>
                                        <option selected value="Proveedor">Por Proveedor</option>
                                    @else
                                        <option selected value="todo">Todo</option>
                                        <option value="Proveedor">Por Proveedor</option>
                                    @endif
                                </select>
                            </div>

                            <div class="col-sm-2 offset-sm-1">
                                <select class="form-control" name="coin" id="coin">
                                    @if (isset($coin) and $coin == 'bolivares')
                                        <option selected value="bolivares">bolivares</option>
                                        <option value="dolares">dolares</option>
                                    @elseif (isset($coin) and $coin == 'dolares')
                                    <option value="bolivares">bolivares</option>
                                    <option selected value="dolares">dolares</option>
                                    @else
                                    <option selected value="bolivares">bolivares</option>
                                    <option  value="dolares">dolares</option>
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
                        </div>
                    </form>
                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item" src="{{ route('report_payment_expenses.pdf',[$coin ?? 'bolivares',$date_begin ?? $datenow,$date_end ?? $datenow,$typeperson ?? 'ninguno',$provider->id ?? null]) }}" allowfullscreen></iframe>
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
        document.getElementById("formPost").action = "{{ route('export_reports.payment_expense') }}";
        document.getElementById("formPost").submit();
        document.getElementById("formPost").action = old_action;
    }

    let provider  = "<?php echo $provider->razon_social ?? 0 ?>";

    if(provider != 0){
        $("#provider_label1").show();
        $("#provider_label2").show();
        $("#provider_label3").show();
    }else{
        $("#provider_label1").hide();
        $("#provider_label2").hide();
        $("#provider_label3").hide();
    }


    $("#type").on('change',function(){
        type = $(this).val();

        if(type == 'todo'){
            $("#client_label1").hide();
            $("#client_label2").hide();
            $("#client_label3").hide();
        }else if(type == 'provider'){
            document.getElementById("route_select").href = "{{ route('report_payment_expenses.selectProvider') }}";
            $("#client_label1").show();
            $("#client_label2").show();
            $("#client_label3").show();
        }
    });

    </script>

@endsection
