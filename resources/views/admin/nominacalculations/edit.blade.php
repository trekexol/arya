@extends('admin.layouts.dashboard')

@section('content')
  
    <!-- container-fluid -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="row py-lg-2">
            <div class="col-md-6">
                <h2>Editar Nómina</h2>
            </div>

        </div>
    </div>
    <!-- /container-fluid -->

    {{-- VALIDACIONES-RESPUESTA--}}
@include('admin.layouts.success')   {{-- SAVE --}}
@include('admin.layouts.danger')    {{-- EDITAR --}}
@include('admin.layouts.delete')    {{-- DELELTE --}}
{{-- VALIDACIONES-RESPUESTA --}}

@if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{$error}}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <form  method="POST"   action="{{ route('nominacalculations.update',$nomina_calculation->id) }}" enctype="multipart/form-data" >
                @method('PATCH')
                @csrf()
                <div class="container py-2">
                    <div class="row">
                        <div class="col-12 ">
                            <input type="hidden" name="id_nomina" value="{{$nomina->id}}" readonly>
                            <input type="hidden" name="id_employee" value="{{$employee->id}}" readonly>
                           
                            <div class="form-group row">
                                <label for="nominaconcept" class="col-md-2 col-form-label text-md-right">Concepto:</label>
                                <div class="col-md-4">
                                    <select  id="id_nomina_concept"  name="id_nomina_concept" class="form-control" disabled>
                                        <option selected value="{{ $nomina_concept->id }}">{{ $nomina_concept->abbreviation  }} - {{ $nomina_concept->description }}</option>
                                            @foreach($nominaconcepts as $var)
                                                <option  value="{{$var->id}}">{{ $var->abbreviation  }} - {{ $var->description }}</option>
                                            @endforeach
                                       
                                    </select>
                                </div>
                                
                            </div>
                                    <!-- if($nomina->type == "Primera Quincena" || $nomina->type == "Segunda Quincena")
                                        <div class="form-group row">
                                            <label for="nominaconcept" class="col-md-2 col-form-label text-md-right">Formula Quincenal:</label>
                                            <div class="col-md-6">
                                                <input id="formula_q" type="text" readonly class="form-control error('formula_q') is-invalid enderror" name="formula_q"  required autocomplete="formula_q">
                                            </div>
                                        </div>
                                    endif
                                    if($nomina->type == "Mensual" || $nomina->type == "Especial")
                                        <div class="form-group row">
                                            <label for="nominaconcept" class="col-md-2 col-form-label text-md-right">Formula Mensual:</label>
                                            <div class="col-md-6">
                                                <input id="formula_m" type="text" readonly class="form-control error('formula_m') is-invalid enderror" name="formula_m"  required autocomplete="formula_m">
                                            </div>
                                        </div>
                                    endif
                                    if($nomina->type == "Semanal")
                                        <div class="form-group row">
                                            <label for="nominaconcept" class="col-md-2 col-form-label text-md-right">Formula Semanal:</label>
                                            <div class="col-md-6">
                                                <input id="formula_s" type="text" readonly class="form-control error('formula_s') is-invalid enderror" name="formula_s"  required autocomplete="formula_s">
                                            </div>
                                        </div>
                                    endif -->
                                    
                                    <div id="days_form" class="form-group row">
                                        <label for="nominaconcept" class="col-md-2 col-form-label text-md-right">Dias:</label>
                                        <div class="col-md-4">
                                            <input id="days" type="text" value="{{ $nomina_calculation->days ?? 0 }}" class="form-control @error('days') is-invalid @enderror" name="days"  autocomplete="days">
                                        </div>
                                    </div>
                                    <div id="hours_form" class="form-group row">
                                        <label for="nominaconcept" class="col-md-2 col-form-label text-md-right">Horas:</label>
                                        <div class="col-md-4">
                                            <input id="hours" type="text" value="{{ $nomina_calculation->hours ?? 0 }}" class="form-control @error('hours') is-invalid @enderror" name="hours"  autocomplete="hours">
                                        </div>
                                    </div>
                                    <div id="cantidad_form" class="form-group row">
                                        <label for="nominaconcept" class="col-md-2 col-form-label text-md-right">Cantidad:</label>
                                        <div class="col-md-4">
                                            <input id="cantidad" type="text" value="{{ number_format($nomina_calculation->cantidad, 2, ',', '.') ?? 0 }}" class="form-control @error('cantidad') is-invalid @enderror" name="cantidad"  autocomplete="cantidad" required>
                                        </div>
                                    </div>
                                    <div id="amount_form" class="form-group row">
                                        <label for="amount" class="col-md-2 col-form-label text-md-right">Monto:</label>
                                        <div class="col-md-4">
                                            <input id="amount" type="text" value="{{ number_format($nomina_calculation->amount, 2, ',', '.') ?? 0 }}" class="form-control @error('amount') is-invalid @enderror" name="amount"  autocomplete="amount" required>
                                        </div>
                                    </div>
                               
                            <br>
                                <div class="form-group row justify-content-center">
                                    <div class="form-group col-sm-2">
                                        <button type="submit" class="btn btn-info btn-block"><i class="fa fa-send-o"></i>Registrar</button>
                                    </div>
                                    <div class="form-group col-sm-2">
                                        <a href="{{ route('nominacalculations',[$nomina->id,$employee->id]) }}" name="danger" type="button" class="btn btn-danger btn-block">Cancelar</a>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>

                    @endsection
                
@section('validacion')
<script>
   /* $("#days_form").hide();
    $("#hours_form").hide();
    $("#cantidad_form").hide();*/
                                
    $(document).ready(function () {
        $("#amount").mask('000.000.000.000.000,00', { reverse: true });      
    });
    $(document).ready(function () {
        $("#hours").mask('000000', { reverse: true });
    });
    $(document).ready(function () {
        $("#days").mask('000000', { reverse: true });               
    });
    $(document).ready(function () {
        $("#cantidad").mask('000.000.000.000.000,00', { reverse: true });
    });            
    
    var id_concept = "<?php echo $nomina_concept->id ?>";  
</script>
    
    @if($nomina->type == "Primera Quincena" || $nomina->type == "Segunda Quincena")
        <script>
            //getFormulaQ(id_concept);
        </script>
    @endif
    @if($nomina->type == "Mensual" || $nomina->type == "Especial")
        <script>
            //getFormulaM(id_concept);
        </script>
    @endif
    @if($nomina->type == "Semanal")
        <script>
            //getFormulaS(id_concept);
        </script>
    @endif
@endsection 

@section('javascript')
    <script>
        /*    
            $("#id_nomina_concept").on('change',function(){
                var id_nomina_concept = $(this).val();
                $("#formula_q").val("");
                $("#formula_m").val("");
                $("#formula_s").val("");
                
                getFormulaQ(id_nomina_concept);
                getFormulaM(id_nomina_concept);
                getFormulaS(id_nomina_concept);
            });

        function getFormulaQ(id_nomina_concept){
            $.ajax({
                url:"{{ route('nominacalculations.listformula') }}" + '/' + id_nomina_concept,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                    
                    // console.clear();
                    if(response.length > 0){
                       
                        response.forEach((item, index, object)=>{
                            let {id,description} = item;
                            
                            document.getElementById("formula_q").value = description; 

                            var validate_dia = -1;
                            var validate_hora = -1;
                            var validate_cantidad = -1;

                            validate_dia = description.indexOf("dia");
                            validate_hora = description.indexOf("hora");
                            validate_cantidad = description.indexOf("cesta");
                            
                            if(validate_dia != -1){
                                if(description.charAt(validate_dia) == 'd'){
                                    $("#days_form").show();
                                    document.getElementById("days").value = 0;
                                }
                            }else{
                                $("#days_form").hide();
                                document.getElementById("days").value = 0;
                            }

                            if(validate_hora != -1){
                                if(description.charAt(validate_hora) == 'h'){
                                    $("#hours_form").show();
                                    document.getElementById("hours").value = 0;
                                }
                                
                            }else{
                                $("#hours_form").hide();
                                document.getElementById("hours").value = 0;
                            }

                            if(validate_cantidad != -1){
                               
                                if(description.charAt(validate_cantidad) == 'c'){
                                    $("#cantidad_form").show();
                                    document.getElementById("cantidad").value = 0;
                                }
                                
                            }else{
                                $("#cantidad_form").hide();
                                document.getElementById("cantidad").value = 0;
                            }
                            
                        });
                    }
                   
                
                },
                error:(xhr)=>{
                    alert('Presentamos inconvenientes al consultar los datos');
                }
            })
        }
        function getFormulaM(id_nomina_concept){
            $.ajax({
                url:"{{ route('nominacalculations.listformulamensual') }}" + '/' + id_nomina_concept,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                    
                    // console.clear();
                    if(response.length > 0){
                       
                        response.forEach((item, index, object)=>{
                            let {id,description} = item;
                            
                            document.getElementById("formula_m").value = description; 

                            var validate_dia = -1;
                            var validate_hora = -1;
                            var validate_cantidad = -1;

                            validate_dia = description.indexOf("dia");
                            validate_hora = description.indexOf("hora");
                            validate_cantidad = description.indexOf("cesta");
                            
                            if(validate_dia != -1){
                                if(description.charAt(validate_dia) == 'd'){
                                    $("#days_form").show();
                                    document.getElementById("days").value = 0;
                                }
                            }else{
                                $("#days_form").hide();
                                document.getElementById("days").value = 0;
                            }

                            if(validate_hora != -1){
                                if(description.charAt(validate_hora) == 'h'){
                                    $("#hours_form").show();
                                    document.getElementById("hours").value = 0;
                                }
                                
                            }else{
                                $("#hours_form").hide();
                                document.getElementById("hours").value = 0;
                            }

                            if(validate_cantidad != -1){
                                if(description.charAt(validate_cantidad) == 'c'){
                                    $("#cantidad_form").show();
                                    document.getElementById("cantidad").value = 0;
                                }
                                
                            }else{
                                $("#cantidad_form").hide();
                                document.getElementById("cantidad").value = 0;
                            }
                        });
                    }
                   
                
                },
                error:(xhr)=>{
                    alert('Presentamos inconvenientes al consultar los datos');
                }
            })
        }
        function getFormulaS(id_nomina_concept){
           
            $.ajax({
                url:"{{ route('nominacalculations.listformulasemanal') }}" + '/' + id_nomina_concept,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                    
                    // console.clear();
                    if(response.length > 0){
                       
                        response.forEach((item, index, object)=>{
                            let {id,description} = item;
                            
                            document.getElementById("formula_s").value = description; 

                            var validate_dia = -1;
                            var validate_hora = -1;
                            var validate_cantidad = -1;

                            validate_dia = description.indexOf("dia");
                            validate_hora = description.indexOf("hora");
                            validate_cantidad = description.indexOf("cesta");
                            
                            if(validate_dia != -1){
                                if(description.charAt(validate_dia) == 'd'){
                                    $("#days_form").show();
                                    document.getElementById("days").value = 0;
                                }
                            }else{
                                $("#days_form").hide();
                                document.getElementById("days").value = 0;
                            }

                            if(validate_hora != -1){
                                if(description.charAt(validate_hora) == 'h'){
                                    $("#hours_form").show();
                                    document.getElementById("hours").value = 0;
                                }
                                
                            }else{
                                $("#hours_form").hide();
                                document.getElementById("hours").value = 0;
                            }

                            if(validate_cantidad != -1){
                                if(description.charAt(validate_cantidad) == 'c'){
                                    $("#cantidad_form").show();
                                    document.getElementById("cantidad").value = 0;
                                    
                                }
                                
                            }else{
                                $("#cantidad_form").hide();
                                document.getElementById("cantidad").value = 0;
                            }
                        });
                    }
                   
                
                },
                error:(xhr)=>{
                    alert('Presentamos inconvenientes al consultar los datos');
                }
            })
        } */
    </script>
@endsection