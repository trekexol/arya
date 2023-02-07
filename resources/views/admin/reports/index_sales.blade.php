@extends('admin.layouts.dashboard')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-sm-12">
            <div class="card">
                <form id="formPost" method="POST" action="{{ route('reports.store_sales') }}">
                    @csrf

                <div class="card-header text-center h4">
                    Ventas
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
                            <label for="date_end" class="col-sm-1 col-form-label text-md-right">Hasta:</label>

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
                                    @if(isset($coin) AND $coin == 'bolivares')
                                        <option selected value="{{ $coin }}">{{ $coin }}</option>
                                        <option value="dolares">dólares</option>
                                    @elseif(isset($coin) AND $coin == 'dolares')
                                    <option selected  value="{{ $coin }}">{{ $coin }}</option>
                                    <option  value="bolivares">bolivares</option>
                                    @else
                                    <option selected  value="bolivares">bolívares</option>
                                    <option  value="dolares">dólares</option>
                                    @endif


                                </select>
                            </div>

                        </div>
                        <div class="form-group row">

                            <label for="name" class="col-md-2 col-form-label text-md-right">Descripción:</label>

                            <div class="col-md-3">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $name ?? '' }}" autocomplete="name">

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-sm-2">
                                <select class="form-control" name="type" id="type">
                                    @if (isset($type))
                                        @if ($type == 'notas')
                                            <option selected value="notas">Notas de Entrega</option>
                                            <option  value="facturas">Facturas</option>
                                            <option value="todo">Todo</option>
                                        @elseif($type == 'facturas')
                                            <option selected value="facturas">Facturas</option>
                                            <option value="todo">Todo</option>
                                            <option value="notas">Notas de Entrega</option>
                                        @elseif($type == 'todo')
                                        <option selected value="todo">Todo</option>
                                        <option value="notas">Notas de Entrega</option>
                                        <option value="facturas">Facturas</option>
                                        @endif

                                    @else
                                        <option value="todo">Todo</option>
                                        <option value="notas">Notas de Entrega</option>
                                        <option selected value="facturas">Facturas</option>

                                    @endif
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <button type="submit" class="btn btn-primary ">
                                    Buscar
                                 </button>
                            </div>
                            <div class="col-sm-2">
                                <a href="#" class="btn btn-success" type="button" onclick="exportToExcel();">
                                    Exportar a Excel
                                </a>

                            </div>
                        </div>
                        </div>
                    </form>

                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item" src="{{ route('reports.sales_pdf',[$coin ?? 'bolivares',$datebeginyear ?? $date_begin ?? $datenow,$date_end ?? $datenow,$name ?? 'nada',$type ?? 'facturas']) }}" allowfullscreen></iframe>
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
        document.getElementById("formPost").action = "{{ route('ventasreporte') }}";
        document.getElementById("formPost").submit();
        document.getElementById("formPost").action = old_action;
    }


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


    /*$("#type").on('change',function(){
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
        }); */

    </script>

@endsection
