@extends('admin.layouts.dashboard')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-sm-12">
            <div class="card">
                <form id="formPost" method="POST" action="{{ route('balancegenerals.store') }}">
                    @csrf

                <div class="card-header text-center h4">
                    Estado de Situación Financiera
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

                            <div class="col-sm-1">
                            <button type="submit" class="btn btn-primary ">
                                Buscar
                             </button>
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
                        <div class="form-group row">
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
                            <label for="date_end" class="col-sm-1 col-form-label text-md-right">Moneda:</label>
                            <div class="col-sm-2">

                                <select class="form-control" name="coin" id="coin">
                                    @if(isset($coin))

                                        @if($coin == 'bolivares')
                                        <option selected value="bolivares">Bolívares</option>
                                        <option value="dolares">Dólares</option>
                                        @else
                                        <option selected value="dolares">Dólares</option>
                                        <option value="bolivares">Bolívares</option>
                                        @endif

                                    @else
                                        <option selected value="bolivares">Bolívares</option>
                                        <option value="dolares">Dólares</option>
                                    @endif

                                </select>
                            </div>
                            <label for="date_end" class="col-sm-1 col-form-label text-md-right">Tasa:</label>
                            <div class="col-sm-2">

                                <select class="form-control" name="type" id="type">
                                    @if(isset($type))

                                        @if($type == '1')
                                        <option selected value="1">Actual</option>
                                        <option value="0">Normal</option>
                                        @else
                                        <option selected value="0">Normal</option>
                                        <option value="1">Actual</option>
                                        @endif
                                    @else
                                        <option selected value="0">Normal</option>
                                        <option value="1">Actual</option>
                                    @endif



                                </select>
                            </div>
                        </div>
                    </form>
                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item" src="{{ route('balancegenerals.balance_pdf',[$coin ?? 'bolivares',$datebeginyear ?? $date_begin ?? $datenow,$date_end ?? $datenow,$level ?? 5,$type ?? 0]) }}" allowfullscreen></iframe>
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

    function exportToExcel(){
        var old_action = document.getElementById("formPost").action;
        document.getElementById("formPost").action = "{{ route('export_reports.balance') }}";
        document.getElementById("formPost").submit();
        document.getElementById("formPost").action = old_action;
    }



    </script>

@endsection
