@extends('admin.layouts.dashboard')

@section('content')

 {{-- VALIDACIONES-RESPUESTA--}}
 @include('admin.layouts.success')   {{-- SAVE --}}
 @include('admin.layouts.danger')    {{-- EDITAR --}}
 @include('admin.layouts.delete')    {{-- DELELTE --}}
 {{-- VALIDACIONES-RESPUESTA --}}
 
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-center font-weight-bold h3">
                    Agregar Concepto
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('nominacalculations.store') }}">
                        @csrf

                        <input type="hidden" name="id_nomina" value="{{$nomina->id}}" readonly>
                        <input type="hidden" name="id_employee" value="{{$employee->id}}" readonly>
                        

                        <div class="form-group row">
                            <label for="nominaconcept" class="col-md-2 col-form-label text-md-right">Concepto:</label>
                            <div class="col-md-4">
                                <select  id="id_nomina_concept"  name="id_nomina_concept" class="form-control" required> 
                                    <option selected value="">Seleccione un Concepto</option>
                                        @foreach($nominaconcepts as $nominaconcept)
                                            <option  value="{{$nominaconcept->id}}">{{ $nominaconcept->abbreviation  }} - {{ $nominaconcept->description }}</option>
                                        @endforeach
                                   
                                </select>
                            </div>
                            
                        </div>
                      
                            <div class="form-group row" id="formula_div_q" style="display: none;">
                                <label for="nominaconcept" class="col-md-2 col-form-label text-md-right">Formula Quincenal:</label>
                                <div class="col-md-6">
                                    <input id="formula_q" type="text" readonly class="form-control @error('formula_q') is-invalid @enderror" name="formula_q"  required autocomplete="formula_q">
                                </div>
                                <div class="col-md-1">
                                    @if($nomina->type == "Primera Quincena" || $nomina->type == "Segunda Quincena")
                                    <input type="radio" id="selector_q" name="selector" value="q" checked>
                                    @else
                                    <input type="radio" id="selector_q" name="selector" value="q">
                                    @endif
                                </div>
                            </div>


                            <div class="form-group row" id="formula_div_m" style="display: none;">
                                <label for="nominaconcept" class="col-md-2 col-form-label text-md-right">Formula Mensual:</label>
                                <div class="col-md-6">
                                    <input id="formula_m" type="text" readonly class="form-control @error('formula_m') is-invalid @enderror" name="formula_m"  required autocomplete="formula_m">
                                </div>
                                <div class="col-md-1">
                                    @if($nomina->type == "Mensual")
                                    <input type="radio" id="selector_m" name="selector" value="m" checked>
                                    @else
                                    <input type="radio" id="selector_m" name="selector" value="m">
                                    @endif
                                </div>
                            </div>


                            <div class="form-group row" id="formula_div_s" style="display: none;">
                                <label for="nominaconcept" class="col-md-2 col-form-label text-md-right">Formula Semanal:</label>
                                <div class="col-md-6">
                                    <input id="formula_s" type="text" readonly class="form-control @error('formula_s') is-invalid @enderror" name="formula_s"  required autocomplete="formula_s">
                                </div>
                                <div class="col-md-1">
                                    @if($nomina->type == "Semanal")
                                    <input type="radio" id="selector_s" name="selector" value="s" checked>
                                    @else
                                    <input type="radio" id="selector_s" name="selector" value="s">
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row" id="formula_div_e" style="display: none;">
                                <label for="nominaconcept" class="col-md-2 col-form-label text-md-right">Formula Especial:</label>
                                <div class="col-md-6">
                                    <input id="formula_e" type="text" readonly class="form-control @error('formula_e') is-invalid @enderror" name="formula_e"  required autocomplete="formula_e">
                                </div>
                                <div class="col-md-1">
                                    @if($nomina->type == "Especial")
                                    <input type="radio" id="selector_e" name="selector" value="e" checked>
                                    @else
                                    <input type="radio" id="selector_e" name="selector" value="e">
                                    @endif
                                </div>
                            </div>
                            
                        <div id="days_form" class="form-group row">
                            <label for="nominaconcept" class="col-md-2 col-form-label text-md-right">Dias:</label>
                            <div class="col-md-4">
                                <input id="days" type="text" value="0" class="form-control @error('days') is-invalid @enderror" name="days"  autocomplete="days">
                            </div>
                        </div>

                        <div id="hours_form" class="form-group row">
                            <label for="nominaconcept" class="col-md-2 col-form-label text-md-right">Horas:</label>
                            <div class="col-md-4">
                                <input id="hours" type="text" value="0" class="form-control @error('hours') is-invalid @enderror" name="hours"  autocomplete="hours">
                            </div>
                        </div>
                        <div id="cantidad_form" class="form-group row">
                            <label for="nominaconcept" class="col-md-2 col-form-label text-md-right">Cantidad:</label>
                            <div class="col-md-4">
                                <input id="cantidad" type="text" placeholder="0,00" class="form-control @error('cantidad') is-invalid @enderror" name="cantidad"  autocomplete="cantidad" value="1" required>
                            </div>
                        </div>
                        <div id="cantidad_form" class="form-group row">
                            <label for="nominaconcept" class="col-md-2 col-form-label text-md-right">Monto:</label>
                            <div class="col-md-4">
                                <input id="monto" type="text" placeholder="0,00" class="form-control @error('monto') is-invalid @enderror" name="monto" autocomplete="monto" required>
                            </div>
                        </div>
                    <br>
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                   Registrar Concepto
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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
            $("#monto").mask('000.000.000.000.000,00', { reverse: true });
            
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
        
        
    </script>
@endsection 

@section('javascript')
    <script>
                
            let id_nomina =  "<?php echo $nomina->id; ?>";
            let id_empleado =  "<?php echo $employee->id; ?>";

            
            $("#id_nomina_concept").on('change',function(){
                var id_nomina_concept = $(this).val();
                $("#formula_q").val("");
                $("#formula_m").val("");
                $("#formula_s").val("");
                $("#formula_e").val("");
                $("#monto").val("");
                $("#formula_div_q").hide();
                $("#formula_div_m").hide();
                $("#formula_div_s").hide();
                $("#formula_div_e").hide();
                
                document.getElementById("selector_q").checked = false;
                document.getElementById("selector_m").checked = false;
                document.getElementById("selector_s").checked = false;
                document.getElementById("selector_e").checked = false;
                /*$("#formula_a").val("");*/
                
                getFormulaQ(id_nomina_concept,id_nomina,id_empleado);
                getFormulaM(id_nomina_concept,id_nomina,id_empleado);
                getFormulaS(id_nomina_concept,id_nomina,id_empleado);
                getFormulaE(id_nomina_concept,id_nomina,id_empleado);
                /*getFormulaA(id_nomina_concept);*/
            });

        function getFormulaQ(id_nomina_concept,id_nomina,id_empleado){
            $.ajax({
                url:"{{ route('nominacalculations.listformula') }}" + '/' + id_nomina_concept+ '/' +id_nomina+ '/' +id_empleado,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                     
                    // console.clear();
                    if(response.length > 0){
                       
                        
                        response.forEach((item, index, object)=>{
                            let {id,description,amount} = item;
               
                            if (description ) {
                                $("#formula_div_q").show();
                                document.getElementById("selector_q").checked = true;

                            } else {
                                document.getElementById("selector_q").checked = false;
                                $("#formula_div_q").hide();
                            }
                            
                            document.getElementById("formula_q").value = description;
                            var amount_fromat = amount.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});

                            document.getElementById("monto").value = amount_fromat;
                             

                            var validate_dia = -1;
                            var validate_hora = -1;
                            var validate_cantidad = -1;

                            validate_dia = description.indexOf("dia");
                            validate_hora = description.indexOf("hora");
                            validate_cantidad = description.indexOf("cesta");
                            
                           /* if(validate_dia != -1){
                                if(description.charAt(validate_dia) == 'd'){
                                    $("#days_form").show();
                                    document.getElementById("days_form").value = 0;
                                }
                            }else{
                                $("#days_form").hide();
                                document.getElementById("days_form").value = 0;
                            }

                            if(validate_hora != -1){
                                if(description.charAt(validate_hora) == 'h'){
                                    $("#hours_form").show();
                                    document.getElementById("hours_form").value = 0;
                                }
                                
                            }else{
                                $("#hours_form").hide();
                                document.getElementById("hours_form").value = 0;
                            }

                            if(validate_cantidad != -1){
                                if(description.charAt(validate_cantidad) == 'c'){
                                    $("#cantidad_form").show();
                                    document.getElementById("cantidad_form").value = 0;
                                }
                                
                            }else{
                                $("#cantidad_form").hide();
                                document.getElementById("cantidad_form").value = 0;
                            } */
                            
                        });
                    }
                   
                
                },
                error:(xhr)=>{
                    $("#formula_div_q").hide();
                    document.getElementById("selector_q").checked = false;
                    /* alert('Presentamos inconvenientes al consultar los datos');*/
                }
            })
        }
        function getFormulaM(id_nomina_concept,id_nomina,id_empleado){
            $.ajax({
                url:"{{ route('nominacalculations.listformulamensual') }}" + '/' + id_nomina_concept+ '/' +id_nomina+ '/' +id_empleado,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                    

                    if(response.length > 0){
                      
                        response.forEach((item, index, object)=>{
                         
                            let {id,description,amount} = item;

                            if (description ) {
                                $("#formula_div_m").show();
                                document.getElementById("selector_m").checked = true;
                            } else {
                                $("#formula_div_m").hide();
                                document.getElementById("selector_m").checked = false;
                            }
  
                            document.getElementById("formula_m").value = description; 
                            var amount_fromat = amount.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});

                            document.getElementById("monto").value = amount_fromat;
                             
                            var validate_dia = -1;
                            var validate_hora = -1;
                            var validate_cantidad = -1;

                            validate_dia = description.indexOf("dia");
                            validate_hora = description.indexOf("hora");
                            validate_cantidad = description.indexOf("cesta");
                            
                           /* if(validate_dia != -1){
                                if(description.charAt(validate_dia) == 'd'){
                                    $("#days_form").show();
                                    document.getElementById("days_form").value = 0;
                                }
                            }else{
                                $("#days_form").hide();
                                document.getElementById("days_form").value = 0;
                            }

                            if(validate_hora != -1){
                                if(description.charAt(validate_hora) == 'h'){
                                    $("#hours_form").show();
                                    document.getElementById("hours_form").value = 0;
                                }
                                
                            }else{
                                $("#hours_form").hide();
                                document.getElementById("hours_form").value = 0;
                            }

                            if(validate_cantidad != -1){
                                if(description.charAt(validate_cantidad) == 'c'){
                                    $("#cantidad_form").show();
                                    document.getElementById("cantidad_form").value = 0;
                                }
                                
                            }else{
                                $("#cantidad_form").hide();
                                document.getElementById("cantidad_form").value = 0;
                            }*/
                        });
                    } 
                
                },
                
                error:(xhr)=>{
                    $("#formula_div_m").hide();
                    document.getElementById("selector_m").checked = false;
                    /*alert('Presentamos inconvenientes al consultar los datos');*/
                }
            })
        }
        function getFormulaS(id_nomina_concept,id_nomina,id_empleado){
            $.ajax({
                url:"{{ route('nominacalculations.listformulasemanal') }}" + '/' + id_nomina_concept+ '/' +id_nomina+ '/' +id_empleado,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{

                    // console.clear();
                    if(response.length > 0){
                        
                        response.forEach((item, index, object)=>{
                             let {id,description,amount} = item;
                           
                            if (description ) {
                                $("#formula_div_s").show();
                                document.getElementById("selector_s").checked = true;
                            } else {
                                $("#formula_div_s").hide();
                                document.getElementById("selector_s").checked = false;
                            }

                            document.getElementById("formula_s").value = description; 
                            var amount_fromat = amount.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});

                            document.getElementById("monto").value = amount_fromat;
                             
                            var validate_dia = -1;
                            var validate_hora = -1;
                            var validate_cantidad = -1;

                            validate_dia = description.indexOf("dia");
                            validate_hora = description.indexOf("hora");
                            validate_cantidad = description.indexOf("cesta");
                            
                            /*if(validate_dia != -1){
                                if(description.charAt(validate_dia) == 'd'){
                                    $("#days_form").show();
                                    document.getElementById("days_form").value = 0;
                                }
                            }else{
                                $("#days_form").hide();
                                document.getElementById("days_form").value = 0;
                            }

                            if(validate_hora != -1){
                                if(description.charAt(validate_hora) == 'h'){
                                    $("#hours_form").show();
                                    document.getElementById("hours_form").value = 0;
                                }
                                
                            }else{
                                $("#hours_form").hide();
                                document.getElementById("hours_form").value = 0;
                            }

                            if(validate_cantidad != -1){
                                if(description.charAt(validate_cantidad) == 'c'){
                                    $("#cantidad_form").show();
                                    document.getElementById("cantidad_form").value = 0;
                                }
                                
                            }else{
                                $("#cantidad_form").hide();
                                document.getElementById("cantidad_form").value = 0;
                            }*/
                        });
                    } 
                
                },
                error:(xhr)=>{
                    $("#formula_div_s").hide();
                    document.getElementById("selector_s").checked = false;
                    /*alert('Presentamos inconvenientes al consultar los datos');*/
                }
            })
        }
        function getFormulaE(id_nomina_concept,id_nomina,id_empleado){
            $.ajax({
                url:"{{ route('nominacalculations.listformulaespecial') }}" + '/' + id_nomina_concept+ '/' +id_nomina+ '/' +id_empleado,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{

                    // console.clear();
                    if(response.length > 0){
                        
                        response.forEach((item, index, object)=>{
                            let {id,description,amount} = item;
                           
                            if (description ) {
                                $("#formula_div_e").show();
                                document.getElementById("selector_e").checked = true;
                            } else {
                                $("#formula_div_s").hide();
                                document.getElementById("selector_e").checked = false;
                            }
                            document.getElementById("formula_e").value = description; 
                            var amount_fromat = amount.toLocaleString('de-DE', {minimumFractionDigits: 2,maximumFractionDigits: 2});

                            document.getElementById("monto").value = amount_fromat;
                             
                            
                            var validate_dia = -1;
                            var validate_hora = -1;
                            var validate_cantidad = -1;

                            validate_dia = description.indexOf("dia");
                            validate_hora = description.indexOf("hora");
                            validate_cantidad = description.indexOf("cesta");
                            
                            /*if(validate_dia != -1){
                                if(description.charAt(validate_dia) == 'd'){
                                    $("#days_form").show();
                                    document.getElementById("days_form").value = 0;
                                }
                            }else{
                                $("#days_form").hide();
                                document.getElementById("days_form").value = 0;
                            }

                            if(validate_hora != -1){
                                if(description.charAt(validate_hora) == 'h'){
                                    $("#hours_form").show();
                                    document.getElementById("hours_form").value = 0;
                                }
                                
                            }else{
                                $("#hours_form").hide();
                                document.getElementById("hours_form").value = 0;
                            }

                            if(validate_cantidad != -1){
                                if(description.charAt(validate_cantidad) == 'c'){
                                    $("#cantidad_form").show();
                                    document.getElementById("cantidad_form").value = 0;
                                }
                                
                            }else{
                                $("#cantidad_form").hide();
                                document.getElementById("cantidad_form").value = 0;
                            }*/
                        });
                    } 
                
                },
                error:(xhr)=>{
                    $("#formula_div_e").hide();
                    document.getElementById("selector_e").checked = false;
                    /*alert('Presentamos inconvenientes al consultar los datos');*/
                }
            })
        }
        

    </script>
@endsection
