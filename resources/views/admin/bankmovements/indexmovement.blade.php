@extends('admin.layouts.dashboard')

@section('content')
<style>
.error{ color: red; font-size: 1em;  }
label.error{ color: red; font-size: 1em; }

</style>

  <!-- /.container-fluid -->
  {{-- VALIDACIONES-RESPUESTA--}}
  @include('admin.layouts.success')   {{-- SAVE --}}
  @include('admin.layouts.danger')    {{-- EDITAR --}}
  @include('admin.layouts.delete')    {{-- DELELTE --}}
  {{-- VALIDACIONES-RESPUESTA --}}
<!-- DataTales Example -->
<div class="container-fluid">
    <div class="row py-lg-2">
        <div class="col-sm-3 offset-sm-2  dropdown mb-2">
            <button class="btn btn-success" type="button"
                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="false"
                aria-expanded="false">
                <i class="fas fa-bars"></i>
                Exportaciones
            </button>
            <div class="dropdown-menu animated--fade-in"
                aria-labelledby="dropdownMenuButton">
                <a href="#" data-toggle="modal" data-target="#PDFModalAccount" class="dropdown-item bg-light">Exportar a PDF</a>
                <a href="#" data-toggle="modal" data-target="#ExcelModalAccount" class="dropdown-item bg-light">Exportar a Excel</a> 
            </div>
        </div> 
        <div class="col-md-2">
            <a href="{{ route('bankmovements') }}" class="btn btn-info" title="Transferencia">Bancos</a>
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary" data-toggle="modal" data-target="#deleteModal" >Subir Movimientos</button>
        </div>
 
    </div>
</div>
<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <nav>
                    <div class="nav nav-tabs justify-content-center" id="nav-tab" role="tablist">
                        <a class="nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Movimientos de Caja y Bancos</a>
                        <a class="nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Movimientos Carga Masiva</a>
                    </div>
                    </nav>

                <div class="card-body">
                        <div class="table-responsive">

              
            <!-- INICIO DE UNA SECCION DE MOVIMIENTOS -->
                <div class="tab-content" id="nav-tabContent">  
                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                    <table class="table table-light2 table-bordered dataTableclass"  width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="text-center">Fecha</th>
                                    <th class="text-center">Referencia</th>
                                    <th class="text-center">Nº</th>
                                    <th class="text-center">Cuenta</th>
                                    <th class="text-center">Descripción</th>
                                    <th class="text-center">Debe</th>
                                    <th class="text-center">Haber</th>
                                    <th class="text-center">Debe USD</th>
                                    <th class="text-center">Haber USD</th>
                                    <th class="text-center"></th>
                                </tr>
                                </thead>
                                
                                <tbody>
                                    @if (empty($detailvouchers))
                                    @else
                                        @foreach ($detailvouchers as $var)
                                        <tr>
                                        <td>{{ date('d-m-Y', strtotime( $var->header_date ?? '')) }}</td>
                                        <td class="text-center">{{$var->id_header_voucher ?? ''}}</td>
                                        <td>{{$var->account_code_one ?? ''}}.{{$var->account_code_two ?? ''}}.{{$var->account_code_three ?? ''}}.{{$var->account_code_four ?? ''}}</td>
                                        <td>{{$var->account_description ?? ''}}</td>
                                        <td>{{$var->header_description ?? ''}}</td>
                                       
                                        <td>{{ number_format($var->debe, 2, ',', '.')}}</td>
                                        <td>{{ number_format($var->haber, 2, ',', '.')}}</td>

                                        <td>{{ number_format($var->debe / $var->tasa, 2, ',', '.')}}</td>
                                        <td>{{ number_format($var->haber / $var->tasa, 2, ',', '.')}}</td>
                                      
                                        <td>
                                            <a href="{{ route('bankmovements.bankmovementPdfDetail',$var->id_header_voucher ?? null) }}" class="show" title="Ver Comprobante"><i class="fa fa-print"></i></a>  
                                            @if (Auth::user()->role_id  == '1' || $eliminarmiddleware  == '1')
                                            <a href="{{ route('bankmovements.delete',$var->id_header_voucher ?? null) }}" class="delete" title="Eliminar"><i class="fa fa-trash text-danger"></i></a>  
                                            @endif
                                        </td>  
                                        
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                
               
                <!-- FIN DE UNA SECCION DE MOVIMIENTOS -->    

                <!-- INICIO DE UNA SECCION DE MOVIMIENTOS MASIVOS -->  
                <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                <table class="table table-light2 table-bordered dataTableclass"  cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="text-center">Fecha</th>
                                    <th class="text-center">Referencia</th>
                                    <th class="text-center">Banco</th>
                                    <th class="text-center">Descripcion</th>
                                    <th class="text-center">Moneda</th>
                                    <th class="text-center">Haber</th>
                                    <th class="text-center">Debe</th>
                                    <th class="text-center">Accion</th>
                                </tr>
                                </thead>
                                
                                <tbody>
                                    @if (!empty($movimientosmasivos))
                                 
                                        @foreach ($movimientosmasivos as $var)
                                        <tr>
                                        <td>{{ date('d-m-Y', strtotime( $var->fecha ?? '')) }}</td>
                                        <td class="text-center">{{$var->referencia_bancaria ?? ''}}</td>
                                        <td>{{$var->banco ?? ''}}</td>
                                        <td>{{$var->descripcion ?? ''}}</td>
                                        <td>{{$var->moneda ?? ''}}</td>
                                     
                                       
                                        <td>{{ $var->haber}}</td>
                                        <td>{{ $var->debe}}</td>




                                        <td>
                                           @if (!empty($quotations))
                                                @foreach($quotations as $quotation)
                                                    @if($var->debe == $quotation->amount_with_iva AND $var->moneda == $quotation->coin)

                                                    <span class="badge badge-pill badge-success" data-toggle="modal" data-target="#MatchModal" name="matchvalue" data-id="{{$var->debe.'/'.$var->id_temp_movimientos.'/'.$var->fecha.'/'.$var->banco.'/match'.$var->moneda}}">Match</span>
                                                    
                                                    @endif
                                                    <span class="badge badge-pill badge-warning" data-toggle="modal" data-target="#MatchModal" name="matchvalue" data-id="{{$var->debe.'/'.$var->id_temp_movimientos.'/'.$var->fecha.'/'.$var->banco.'/contra/'.$var->haber.'/'.$var->referencia_bancaria.'/'.$var->moneda.'/'.$var->descripcion}}">Contrapartida</span>

                                                @endforeach
                                            @endif
                                            
                                        </td>  
                                        
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                </div>

                <!-- FIN DE UNA SECCION DE MOVIMIENTOS MASIVOS --> 
                </div>            
            </div>
    </div>
</div>
</div>
</div>
</div>
</div>

   
<div class="modal fade" id="PDFModalAccount" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Seleccione el periodo</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" id="formPostPdfAccountOrdenDePago" action="{{ route('bankmovements.pdfAccountBankMovement') }}"   target="print_popup" onsubmit="window.open('about:blank','print_popup','width=1000,height=800');">
                @csrf
            <div class="modal-body">
                <div class="form-group row">
                    <label for="account" class="col-md-2 col-form-label text-md-right">Cuenta:</label>
                        <div class="col-md-8">
                            <select class="form-control" id="id_account" name="id_account" >
                                <option value="">Selecciona una Cuenta</option>
                                @foreach($accounts as $var)
                                    <option value="{{ $var->id }}">{{ $var->description }}</option>
                                @endforeach
                              
                            </select>
                        </div>
                </div>
                <div class="form-group row">
                    <label id="coinlabel" for="coin" class="col-md-2 col-form-label text-md-right">Moneda:</label>
                    <div class="col-md-6">
                        <select class="form-control" name="coin" id="coin">
                            <option selected value="bolivares">Bolívares</option>
                            <option value="dolares">Dolares</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="date_end" class="col-sm-2 col-form-label text-md-right">Desde</label>
    
                    <div class="col-sm-6">
                        <input id="date_begin" type="date" class="form-control @error('date_begin') is-invalid @enderror" name="date_begin" value="{{  $date_begin ?? $datenow ?? '' }}" required autocomplete="date_begin">
    
                        @error('date_begin')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label for="date_end" class="col-sm-2 col-form-label text-md-right">hasta </label>
    
                    <div class="col-sm-6">
                        <input id="date_begin" type="date" class="form-control @error('date_end') is-invalid @enderror" name="date_end" value="{{ $date_end ?? $datenow ?? '' }}" required autocomplete="date_end">
    
                        @error('date_end')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
              
            </div>
                <div class="modal-footer">
                    <div class="form-group col-md-2">
                        <button type="submit" class="btn btn-info" title="Buscar">Enviar</button>  
                    </div>
            </form>
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    
<div class="modal fade" id="ExcelModalAccount" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Seleccione el periodo / Exportar a Excel</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" id="formPostPdfAccountOrdenDePago" action="{{ route('export_reports.bankmovements') }}"  >
                @csrf
            <div class="modal-body">
                <div class="form-group row">
                    <label for="account" class="col-md-2 col-form-label text-md-right">Cuenta:</label>
                        <div class="col-md-8">
                            <select class="form-control" id="id_account" name="id_account" >
                                <option value="">Selecciona una Cuenta</option>
                                @foreach($accounts as $var)
                                    <option value="{{ $var->id }}">{{ $var->description }}</option>
                                @endforeach
                              
                            </select>
                        </div>
                </div>
                <div class="form-group row">
                    <label id="coinlabel" for="coin" class="col-md-2 col-form-label text-md-right">Moneda:</label>
                    <div class="col-md-6">
                        <select class="form-control" name="coin" id="coin">
                            <option selected value="bolivares">Bolívares</option>
                            <option value="dolares">Dolares</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="date_end" class="col-sm-2 col-form-label text-md-right">Desde</label>
    
                    <div class="col-sm-6">
                        <input id="date_begin" type="date" class="form-control @error('date_begin') is-invalid @enderror" name="date_begin" value="{{  $date_begin ?? $datenow ?? '' }}" required autocomplete="date_begin">
    
                        @error('date_begin')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label for="date_end" class="col-sm-2 col-form-label text-md-right">hasta </label>
    
                    <div class="col-sm-6">
                        <input id="date_begin" type="date" class="form-control @error('date_end') is-invalid @enderror" name="date_end" value="{{ $date_end ?? $datenow ?? '' }}" required autocomplete="date_end">
    
                        @error('date_end')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
              
            </div>
                <div class="modal-footer">
                    <div class="form-group col-md-2">
                        <button type="submit" class="btn btn-info" title="Buscar">Enviar</button>  
                    </div>
            </form>
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Delete Warning Modal -->
<div class="modal modal-danger fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Subir Movimientos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="fileForms" enctype="multipart/form-data" >
                    @csrf
                    <div class="form-group col-md-8">
                        <label for="inputState">Banco</label>
                        <select class="form-control" name="banco" id="banco">
                          <option value="">Seleccione..</option>
                          <option value="Bancamiga">Bancamiga</option>
                          <option value="Banco Banesco">Banesco</option>
                          <option value="Mercantil">Mercantil</option>
                          <option value="Chase">Chase</option>
                          <option value="BOFA">BOFA</option>
                        </select>

                        
                      </div>
                    <div id="muestrasbanco"></div>

                      <div class="form-group col-md-12">
                        <input required id="file" type="file" value="import" name="file" class="form-control-file" accept=".xlsx, .csv, .txt">
                        
                      </div>
                      <div id="muestrasfile" ></div>
               
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Importar</button>
            </div>
            </form>
        </div>
    </div>
  </div>





  <div class="modal modal-danger fade" id="MatchModal" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content" id="modalfacturas">
          
        </div>
    </div>
  </div>


@endsection
@section('javascript')
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

    <script type="text/javascript">



$(document).ready(function(){



/*********************************VALIDADOR DE FORMULARIO************************************/
$("#fileForms").validate({
  
        rules: {
            banco: "required",
            file: "required",

        },

        messages:{
            banco: "Seleccione un Banco",
            file: "Agregue un Archivo",


        },
     
       
/*MODIFICANDO PARA MOSTRAR LA ALERTA EN EL LUGAR QUE DESEO CON UN DIV*/
    errorPlacement: function(error, element) {

        if(element.attr("name") == "banco") {
        
        $("#muestrasbanco").append(error);

        }

        if(element.attr("name") == "file") {

        $("#muestrasfile").append(error);

        }

        },

        submitHandler: function (form) {

           

            $.ajax({
            type: "post",
            url: "{{ route('importmovimientos') }}",
            dataType: "json",
            data: new FormData( form ),
            processData: false,
            contentType: false,
            //success:(response)=>{
            success:function(response){
             if(response.error == true){
                Swal.fire({
                        icon: 'info',
                        title: 'Exito!',
                        html: response.msg,
                
                
                        })
             }else{
               
                Swal.fire({
                        icon: 'info',
                        title: 'Error..',
                        html: response.msg,
                        })
             }
            
         
            
         
         },
         error:(response)=>{
      
        
            Swal.fire({
                    icon: 'error',
                    title: 'Error...',
                    html: response.msg,
                    });
         }
            });



            return false; // required to block normal submit since you used ajax
        }
    }); ///fin $("#registro").validate({

 /********MODAL CUANDO CONSIGUE MATCH**********/

$('[name="matchvalue"]').click(function(e){
    e.preventDefault();
   var value = $(this).data('id'); 
   var url = "{{ route('facturasmovimientos') }}";

 $.post(url,{value: value,"_token": "{{ csrf_token() }}",},function(data){
        $("#modalfacturas").empty().append(data);
   
      });



 });

 

 $('.dataTableclass').DataTable({
        "ordering": false,
        "order": [],
        'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
    });

 });




 






    </script> 
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection