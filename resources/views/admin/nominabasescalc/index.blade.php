@extends('admin.layouts.dashboard')

@section('content')

    <ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
        <a class="nav-link  font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('nominas') }}" role="tab" aria-controls="home" aria-selected="true">Nóminas</a>
        </li>
        <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('nominaconcepts') }}" role="tab" aria-controls="profile" aria-selected="false">Concepto de Nómina</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link active font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('nominabasescalc') }}" role="tab" aria-controls="profile" aria-selected="false">Bases de Cálculo</a>
        </li>
    </ul>

  {{-- VALIDACIONES-RESPUESTA--}}
@include('admin.layouts.success')   {{-- SAVE --}}
@include('admin.layouts.danger')    {{-- EDITAR --}}
@include('admin.layouts.delete')    {{-- DELELTE --}}
{{-- VALIDACIONES-RESPUESTA --}}



<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="col-md-8">
                <h2>Bases de Cálculo (Datos Generales)</h2>
            </div> 
            <br> 
            <div class="card">
            <div class="card-body">
                <div class="form-group row">
                    <label for="login" class="col-sm-3 col-form-label">Unidad Tributaria Valor en Bs: </label>
        
                    <div class="col-sm-2">
                        <input id="unit_tribute" type="text" class="form-control @error('unit_tribute') is-invalid @enderror" name="unit_tribute" value="{{ number_format($bases->tax_unit ?? 0, 2, ',', '.') ?? 0 }}" autocomplete="unit_tribute" autofocus disabled>
    
                        @error('unit_tribute')
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <label for="login" class="col-sm-3 col-form-label">Unidad Tributaria Valor USD: </label>
    
                    <div class="col-sm-2">
                        <input id="unit_tribute_USD" type="text" class="form-control @error('unit_tribute_USD') is-invalid @enderror" name="unit_tribute_USD" value="{{ number_format($bases->tax_unit/$bases->rate_bcv ?? 0, 2, ',', '.') ?? 0 }}"  autocomplete="unit_tribute_USD" autofocus disabled>
    
                        @error('unit_tribute_USD')
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
               </div>

               <div class="form-group row">
                <label for="login" class="col-sm-3 col-form-label">Sueldo Mínimo Actual en Bs: </label>

                <div class="col-sm-2">
                    <input id="salary_min_g" type="text" class="form-control @error('salary_min_g') is-invalid @enderror" name="salary_min_g" value="{{ number_format($bases->salary_min ?? 0, 2, ',', '.') ?? '' }}" autocomplete="salary_min_g" autofocus disabled>

                    @error('salary_min_g')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                    <label for="login" class="col-sm-3 col-form-label">Unidad Tributaria Cestatikets: </label>
        
                    <div class="col-sm-1">
                        <input id="unit_tribute_cesta" type="text" class="form-control @error('unit_tribute_cesta') is-invalid @enderror" name="unit_tribute_cesta" value="{{ $bases->tax_unit_cesta ?? 0 }}" autocomplete="unit_tribute_cesta" autofocus disabled>
    
                        @error('unit_tribute_cesta')
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                   

            </div>


            </div>
        </div>
        <br>

            <div class="card">    
                <div class="card-body">
                    <form method="POST" action="{{ route('nominabasescalc.store') }}">
                        @csrf
                       
                         <div class="form-group row">
                            <label for="login" class="col-sm-3 col-form-label">Sueldo Mínimo (Bajo) en Bs: </label>
            
                            <div class="col-sm-2">
                                <input id="salary_min" type="text" class="form-control @error('salary_min') is-invalid @enderror" name="salary_min" value="{{number_format($nominabases->salary_min ?? 0, 2, ',', '.') ?? ''}}" required autocomplete="salary_min" autofocus>
            
                                @error('salary_min')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="login" class="col-sm-3 col-form-label">Sueldo Mínimo (Bajo) USD: </label>
            
                            <div class="col-sm-2">
                                <input id="salary_min_USD" type="text" class="form-control @error('salary_min_USD') is-invalid @enderror" name="salary_min_USD" value="{{ number_format($nominabases->salary_min_USD ?? 0, 2, ',', '.') ?? '' }}" autocomplete="salary_min_USD" autofocus>
            
                                @error('salary_min_USD')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="login" class="col-sm-3 col-form-label">Sueldo Máximo (Alto) en Bs: </label>
            
                            <div class="col-sm-2">
                                <input id="salary_max" type="text" class="form-control @error('salary_max') is-invalid @enderror" name="salary_max" value="{{ number_format($nominabases->salary_max ?? 0, 2, ',', '.') ?? '' }}" autocomplete="salary_max" autofocus>
            
                                @error('salary_max')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="login" class="col-sm-3 col-form-label">Sueldo Máximo (Alto) USD: </label>
            
                            <div class="col-sm-2">
                                <input id="salary_max_USD" type="text" class="form-control @error('salary_max_USD') is-invalid @enderror" name="salary_max_USD" value="{{ number_format($nominabases->salary_max_USD ?? 0, 2, ',', '.') ?? '' }}" autocomplete="salary_max_USD" autofocus>
            
                                @error('salary_max_USD')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>





                           <div class="form-group row">
                            <label for="login" class="col-sm-3 col-form-label">Monto de Cestatickets en Bs: </label>
                
                            <div class="col-sm-2">
                                <input id="amount_cesta" type="text" class="form-control @error('amount_cesta') is-invalid @enderror" name="amount_cesta" value="{{ number_format($nominabases->amount_cestatickets ?? 0, 2, ',', '.') ?? '' }}" required autocomplete="amount_cesta" autofocus>
            
                                @error('amount_cesta')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="login" class="col-sm-3 col-form-label">Monto de Cestatickets USD: </label>
                
                            <div class="col-sm-2">
                                <input id="amount_cesta_USD" type="text" class="form-control @error('amount_cesta_USD') is-invalid @enderror" name="amount_cesta_USD" value="{{ number_format($nominabases->amount_cestatickets_USD ?? 0, 2, ',', '.') ?? '' }}" autocomplete="amount_cesta_USD" autofocus>
            
                                @error('amount_cesta_USD')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                           </div>
                    
    
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Dias de Vacaciones: </label>
            
                            <div class="col-sm-1">
                                <input id="days_vacations" type="text" class="form-control @error('days_vacations') is-invalid @enderror" name="days_vacations" value="{{ $nominabases->days_vacations ?? 0 }}" required autocomplete="days_vacations" autofocus>
            
                                @error('days_vacations')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label class="col-sm-3 col-form-label ">Dias Bono Vacacional:</label>
            
                            <div class="col-sm-1">
                                <input id="days_bond_vacations" type="days_bond_vacations" class="form-control @error('days_bond_vacations') is-invalid @enderror" name="days_bond_vacations" value="{{ $nominabases->days_bond_vacations ?? 0 }}" required autocomplete="days_bond_vacations" autofocus>
            
                                @error('days_bond_vacations')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label ">Dias de Utilidades Minimo:</label>
                            <div class="col-sm-1">
                                <input id="days_utility_min" type="text" class="form-control @error('days_utility_min') is-invalid @enderror" name="days_utility_min" value="{{ $nominabases->days_utility_min ?? 0 }}" required autocomplete="days_utility_min" autofocus>
                                @error('days_utility_min')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label class="col-sm-3 col-form-label ">Dias de Utilidades Maximo:</label>
                            <div class="col-sm-1">
                                <input id="days_utility_max" type="text" class="form-control @error('days_utility_max') is-invalid @enderror" name="days_utility_max" value="{{ $nominabases->days_utility_max ?? 0 }}" required autocomplete="days_utility_max" autofocus>
                                @error('days_utility_max')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label ">Dias Prestaciones Sociales:</label>
                            <div class="col-sm-1">
                                <input id="days_social_benefits" type="text" class="form-control @error('days_social_benefits') is-invalid @enderror" name="days_social_benefits" value="{{ $nominabases->days_social_benefits ?? 0 }}" required autocomplete="days_social_benefits" autofocus >
            
                                @error('days_social_benefits')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="franqueo_postal" class="col-sm-3 col-form-label">Tipo de Tasa para el cálculo de Prestaciones Sociales:</label>
            
                            <div class="col-sm-4">
            
                                <select class="form-control" name="rate_social_benefits" id="rate_social_benefits">
                                    @if ($nominabases->rate_social_benefits == 1)
                                    <option selected value="1">Promedio entre Activa y Pasiva</option>    
                                    <option value="2">Activa</option>
                                    @endif
                                    
                                    @if ($nominabases->rate_social_benefits == 2)
                                        <option value="1">Promedio entre Activa y Pasiva</option>
                                        <option selected value="2">Activa</option>
                                    @endif
                                    
                                </select>
                            </div>
                        </div>


                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">% S.S.O: </label>
            
                            <div class="col-sm-2">
                                <input id="sso" type="text" class="form-control @error('sso') is-invalid @enderror" name="sso" value="{{ number_format($nominabases->sso ?? 0, 2, ',', '.') ?? '' }}" required autocomplete="sso" autofocus>
            
                                @error('sso')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label class="col-sm-3 col-form-label ">% S.S.O - Empresa:</label>
            
                            <div class="col-sm-2">
                                <input id="sso_company" type="sso_company" class="form-control @error('sso_company') is-invalid @enderror" name="sso_company" value="{{ number_format($nominabases->sso_company ?? 0, 2, ',', '.') ?? '' }}" required autocomplete="sso_company" autofocus>
            
                                @error('sso_company')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label ">% F.A.O.V:</label>
                            <div class="col-sm-2">
                                <input id="faov" type="text" class="form-control @error('faov') is-invalid @enderror" name="faov" value="{{ number_format($nominabases->faov ?? 0, 2, ',', '.') ?? '' }}" required autocomplete="faov" autofocus>
                                @error('faov')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label class="col-sm-3 col-form-label ">% F.A.O.V - Empresa:</label>
                            <div class="col-sm-2">
                                <input id="faov_company" type="text" class="form-control @error('faov_company') is-invalid @enderror" name="faov_company" value="{{ number_format($nominabases->faov_company ?? 0, 2, ',', '.') ?? '' }}" required autocomplete="faov_company" autofocus>
                                @error('faov_company')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label ">% P.I.E:</label>
                            <div class="col-sm-2">
                                <input id="pie" type="text" class="form-control @error('pie') is-invalid @enderror" name="pie" value="{{ number_format($nominabases->pie ?? 0, 2, ',', '.') ?? '' }}" required autocomplete="pie" autofocus>
            
                                @error('pie')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label class="col-sm-3 col-form-label ">% P.I.E - Company:</label>
                            <div class="col-sm-2">
                                <input id="pie_company" type="text" class="form-control @error('pie_company') is-invalid @enderror" name="pie_company" value="{{ number_format($nominabases->pie_company ?? 0, 2, ',', '.') ?? '' }}" required autocomplete="pie_company" autofocus>
            
                                @error('pie_company')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>
            
                    </div>
            </div>

        </div>
        <br>
        <div class="form-group row">
            <div class="form-group  col-sm-2 offset-sm-4">
                <button type="submit" class="btn btn-info btn-block"><i class="fa fa-send-o"></i>Guardar</button>
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
        'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
    });


    
    $(document).ready(function () {

    $("#salary_min").mask('000.000.000.000.000,00', { reverse: true });    
    $("#salary_min_USD").mask('000.000.000.000.000,00', { reverse: true });
    $("#salary_max").mask('000.000.000.000.000,00', { reverse: true });    
    $("#salary_max_USD").mask('000.000.000.000.000,00', { reverse: true });
    $("#amount_cesta").mask('000.000.000.000.000,00', { reverse: true });
    $("#amount_cesta_USD").mask('000.000.000.000.000,00', { reverse: true });
    $("#sso").mask('000.000.000.000.000,00', { reverse: true });
    $("#faov").mask('000.000.000.000.000,00', { reverse: true });
    $("#pie").mask('000.000.000.000.000,00', { reverse: true });
    $("#sso_company").mask('000.000.000.000.000,00', { reverse: true });
    $("#faov_company").mask('000.000.000.000.000,00', { reverse: true });
    $("#pie_company").mask('000.000.000.000.000,00', { reverse: true });
    

    });
    </script> 
@endsection
