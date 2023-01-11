@extends('admin.layouts.dashboard')

@section('content')



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

    <?php
    if(isset($_POST['tasa']) && isset($_POST['fac']) && isset($_POST['serie']) && isset($_POST['id']) && isset($_POST['idp'])){
        $tasa = decrypt($_POST['tasa']);
        $fac = decrypt($_POST['fac']);
        $serie = decrypt($_POST['serie']);
        $id = decrypt($_POST['id']);
        $idp = decrypt($_POST['idp']);

        $activo = true;


    }
        else {
            $tasa = '0.00';
            $fac = '';
            $serie = '';
            $id = '';
            $idp = '';
            $activo = false;
        }


        ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header text-center font-weight-bold h3">Registro de Nota de Debito</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('notastore') }}" enctype="multipart/form-data">
                        @csrf
                       
                        <input id="id_user" type="hidden" class="form-control @error('id_user') is-invalid @enderror" name="id_user" value="{{ Auth::user()->id }}" required autocomplete="id_user">
                        <input id="id_provider" type="hidden" class="form-control @error('id_provider') is-invalid @enderror" name="id_provider" value="{{ $idp ?? null  }}" required autocomplete="id_client">
                        <input id="id_invoice" type="hidden" class="form-control @error('id_invoice') is-invalid @enderror" name="id_invoice" value="{{ $id ?? null  }}" required autocomplete="id_invoice">
                       
                        
                        <div class="form-group row">
                            <label for="invoices" class="col-sm-6 col-form-label text-md-right">Seleccione una Factura:</label>
                            <div class="col-sm-">
                                <input id="invoice" type="text" class="form-control form-control-sm @error('invoice') is-invalid @enderror" name="invoice" value="{{ $fac ?? '' }}" readonly required autocomplete="invoice">
    
                                @error('invoice')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-sm-1">
                                <a href="#" title="Seleccionar Factura" data-toggle="modal" data-target="#MatchModal" name="matchvalue"><i class="fa fa-eye"></i></a>  
                            </div>

                        </div>
                            
                        @if($activo == TRUE) 
                        <div class="form-group row">
                            <label for="date" class="col-sm-1 col-form-label text-sm-right">Fecha:</label>
                            <div class="col-sm-2">
                                <input id="date_begin" type="date" class="form-control form-control-sm @error('date') is-invalid @enderror" name="date" value="{{ $datenow }}" required autocomplete="date">
    
                                @error('date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <label for="serie" class="col-sm-3 col-form-label text-sm-right">N° de Control/Serie (Opcional):</label>
                            <div class="col-sm-2">
                                <input id="serie" type="text" class="form-control form-control-sm @error('serie') is-invalid @enderror" name="serie" value="{{$serie ?? old('serie') }}" autocomplete="serie">

                                @error('serie')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <label for="invoices" class="col-sm-2 col-form-label text-md-right">Tipo de Cuenta:</label>
                            <div class="col-sm-2">
                                <select class="form-control form-control-sm @error('tipocuenta') is-invalid @enderror" id="tipocuenta" name="tipocuenta">
                                    <option value=" ">Seleccione..</option>
                                    <option  value="devolucion" {{ old('tipocuenta') == 'devolucion' ? 'selected' : '' }}>Devolución</option>
                                    <option  value="descuento"  {{ old('tipocuenta') == 'descuento' ? 'selected' : '' }}>Descuento</option>
                                </select>
                                @error('tipocuenta')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                      
                        <div class="form-group row">
                            <label for="importe" class="col-sm-2 col-form-label text-md-right">Monto:</label>

                            <div class="col-sm-2">
                                <input id="importe" type="text" class="form-control form-control-sm @error('importe') is-invalid @enderror" name="importe" value="{{ old('importe') }}" autocomplete="importe">

                                @error('importe')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div> 
                            
                            <label for="moneda" class="col-sm-1 col-form-label text-md-right">Moneda:</label>
                            <div class="col-sm-2">
                            <select class="form-control form-control-sm" id="coin" name="coin" required>
                                <option selected value="bolivares">Bolivares</option>
                                <option value="dolares">Dolares</option>
                            </select>
                           </div>   

                            <label for="rate" class="col-sm-2 col-form-label text-md-right">Tasa:</label>

                            <div class="col-sm-2">
                                <input onkeyup="numeric(this)" id="rate" type="text" class="form-control form-control-sm @error('rate') is-invalid @enderror" name="rate" value="{{ number_format($tasa ?? old('rate'), 10, ',', '.') }}" autocomplete="rate">

                                @error('rate')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>  

 
                        </div>

        
            
                        <div class="form-group row">

                            <label for="observation" class="col-sm-2 col-form-label text-md-right">Observaciones:</label>

                            <div class="col-sm-8">
                                <input id="observation" type="text" class="form-control form-control-sm @error('observation') is-invalid @enderror" name="observation" value="{{ old('observation') }}" autocomplete="observation">

                                @error('observation')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            
                        </div>

                        <div class="form-group row">
                          
                            <label for="note" class="col-sm-4 col-form-label text-md-right">Nota al Pie de Nota de Débito:</label>

                            <div class="col-sm-4">
                                <input id="note" type="text" class="form-control form-control-sm @error('note') is-invalid @enderror" name="note" value="{{ old('note') }}"  autocomplete="note">

                                @error('note')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <div class="table-responsive-md">
                            <table class="table">
                             <tbody>
                                <th>Prueba</th>
                             </tbody>

                             <tr>
                                <td>{{$prueba}}</td>
                             </tr>
                            </table>
                          </div>
                          
                        
                        <br>
                       
                        <div class="form-group row">
                            <div class="col-sm-3 offset-sm-4">
                                <button type="submit" class="btn btn-info">
                                  Registrar
                                </button>
                            </div>
                            <div class="col-sm-2">
                                <a href="{{ route('notas') }}" id="btnvolver" name="btnvolver" class="btn btn-danger" title="volver">Volver</a>  
                            </div>

                            <div class="col-sm-2">
                                <a href="{{ route('crearnota') }}" id="btnvolver" name="btnvolver" class="btn btn-warning" title="limpiar">Limpiar</a>  
                            </div>
                        </div>




                        </form>      
                @else
                <div class="form-group row">
                    <div class="col-sm-2">
                        <a href="{{ route('notas') }}" id="btnvolver" name="btnvolver" class="btn btn-danger" title="volver">Volver</a>  
                    </div>
                </div>
                @endif
                
                   
                </div>

                <div class="modal modal-danger fade" id="MatchModal" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                        <div class="modal-content" id="modalfacturas">
                
                        </div>
                    </div>
                  </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('validacion')
    <script>    
    $("#invoiceform").hide();
       
    $(function(){
            soloAlfaNumerico('code_comercial');
            soloAlfaNumerico('description');
        });

       
       $("#tiponota").on('change',function(){
           
           var tiponota = $(this).val();

           if(tiponota == "debito"){
               $("#invoiceform").show();
     
           }else{
            $("#invoiceform").hide();
           }
           
      });

       $(document).ready(function () {
            $("#importe").mask('000.000.000.000.000.000.000,00', { reverse: true });



            
            $('[name="matchvalue"]').click(function(e){
                    e.preventDefault();
               
                   var url = "{{ route('selectfacturas') }}";

                 $.post(url,{"_token": "{{ csrf_token() }}"},function(data){
                        $("#modalfacturas").empty().append(data);

                      });



                 });
            
        });


        function numeric(e) {
            
            e.value = e.value.replace(/\./g, ',');
            e.value = e.value.replace(/[A-Z]/g, '');
            e.value = e.value.replace(/[a-z]/g, '');
        
            return e.value;
            
        }
    </script>
@endsection
