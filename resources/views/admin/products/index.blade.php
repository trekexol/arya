@extends('admin.layouts.dashboard')

@section('content')

<ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
      <a class="nav-link active font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('products') }}" role="tab" aria-controls="home" aria-selected="true">Productos</a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('inventories') }}" role="tab" aria-controls="profile" aria-selected="false">Inventarios</a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('combos') }}" role="tab" aria-controls="home" aria-selected="true">Combos</a>
  </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('inventories.movement') }}" role="tab" aria-controls="contact" aria-selected="false">Movimientos de Inventario</a>
    </li>
    
  </ul>

<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
      <div class="col-sm-3 offset-sm-4  dropdown mb-4">
          <button class="btn btn-dark" type="button"
              id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="false"
              aria-expanded="false">
              <i class="fas fa-bars"></i>
              Opciones 
          </button>
          <div class="dropdown-menu animated--fade-in" aria-labelledby="dropdownMenuButton">
              <a href="{{ route('export.product_template') }}" class="dropdown-item bg-success text-white h5">Descargar Plantilla</a> 
              <form id="fileForm" method="POST" action="{{ route('import_product') }}" enctype="multipart/form-data" >
                @csrf
                <input id="file" type="file" value="import" accept=".xlsx" name="file" class="file">
              </form>
              <br>
              <a href="#" onclick="import_product();" class="dropdown-item bg-warning text-white h5">Subir Productos</a> 
              <a href="#" onclick="import_product_update_price();" class="dropdown-item bg-info text-white h5">Actualizar Precio Productos</a> 
          </div> 
      </div> 

      
      <div class="col-sm-3">
        <a href="{{ route('products.create')}}" class="btn btn-primary float-md-right" role="button" aria-pressed="true">Registrar un Producto </a>
      </div>
    </div>
  </div>
  <!-- /.container-fluid -->
  {{-- VALIDACIONES-RESPUESTA--}}
  @include('admin.layouts.success')   {{-- SAVE --}}
  @include('admin.layouts.danger')    {{-- EDITAR --}}
  @include('admin.layouts.delete')    {{-- DELELTE --}}
  {{-- VALIDACIONES-RESPUESTA --}}
<!-- DataTales Example -->
<div class="card shadow mb-4">
   
    <div class="card-body">
        <div class="container">
            @if (session('flash'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{session('flash')}}
                <button type="button" class="close" data-dismiss="alert" aria-label="close">
                    <span aria-hidden="true">&times; </span>
                </button>
            </div>   
        @endif
        </div>
        <div class="table-responsive">
        <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
            <tr> 
                <th class="text-center">Código</th>
                <th class="text-center">Descripción</th>
                <th class="text-center">Tipo</th>
                <th class="text-center">Precio</th>
                <th class="text-center">Moneda</th>
                <th class="text-center">Foto del Producto</th>
              
                <th class="text-center" width="7%"></th>
            </tr>
            </thead>
            
            <tbody>
                @if (empty($products))
                @else  
                    @foreach ($products as $product)
                        <tr>
                           
                            <td class="text-center">{{$product->code_comercial}}</td>
                            <td class="text-center">{{$product->description}}</td>
                            <td class="text-center">{{$product->type}}</td>
                            <td class="text-right">{{number_format($product->price, 2, ',', '.')}}</td>
                            
                            @if ($product->money == 'Bs')
                              <td class="text-center">Bolivares</td>
                            @else
                              <td class="text-center">Dolares</td>
                            @endif
                            
                            <td class="text-center"><img src="{{ asset('/storage/descarga.jpg') }} " ></td>

                            <td class="text-center">
                                <a href="{{ route('products.edit',$product->id) }}"  title="Editar"><i class="fa fa-edit"></i></a>
                                <a href="#" class="delete" data-id-product={{$product->id}} data-toggle="modal" data-target="#deleteModal" title="Eliminar"><i class="fa fa-trash text-danger"></i></a>  
                            </td>
                        </tr>     
                    @endforeach   
                @endif
            </tbody>
        </table>
        </div>
    </div>
</div>

<!-- Delete Warning Modal -->
<div class="modal modal-danger fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Eliminar</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body">
          <form action="{{ route('products.delete') }}" method="post">
              @csrf
              @method('DELETE')
              <input id="id_product_modal" type="hidden" class="form-control @error('id_product_modal') is-invalid @enderror" name="id_product_modal" readonly required autocomplete="id_product_modal">
                     
              <h5 class="text-center">Seguro que desea eliminar?</h5>
              
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-danger">Eliminar</button>
          </div>
          </form>
      </div>
  </div>
</div>
<div class="modal modal-danger fade" id="movementModal" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Movimiento Contable</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form action="{{ route('import_product_procesar') }}" method="post"  enctype="multipart/form-data" >
                @csrf
                <div class="form-group row">
                    <div class="offset-sm-1">
                        <input id="file_form" type="file" value="import" accept=".xlsx" name="file_form" class="file">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="contrapartida" class="col-sm-12 col-form-label text-md-center">La carga de estos productos es de: {{number_format($total_amount_for_import ?? 0, 2, ',', '.')}}</label>
                </div>
                <div class="form-group row">
                    @if (isset($contrapartidas))      
                    <label for="contrapartida" class="col-sm-4 col-form-label text-md-right">Contrapartida:</label>
                
                    <div class="col-sm-4">
                    <select id="contrapartida"  name="contrapartida" class="form-control">
                        <option value="">Seleccionar</option>
                        @foreach($contrapartidas as $index => $value)
                            <option value="{{ $index }}" {{ old('Contrapartida') == $index ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                        </select>

                        @if ($errors->has('contrapartida_id'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('contrapartida_id') }}</strong>
                            </span>
                        @endif
                    </div>
                    @endif
                    <div class="col-sm-4">
                            <select  id="subcontrapartida"  name="Subcontrapartida" class="form-control">
                                <option value="">Seleccionar</option>
                            </select>

                            @if ($errors->has('subcontrapartida_id'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('subcontrapartida_id') }}</strong>
                                </span>
                            @endif
                        </div>
                </div>  
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-info">Aceptar</button>
            </div>
            </form>
        </div>
    </div>
  </div>
  
@endsection
@section('javascript')
    <script>
        if("{{isset($total_amount_for_import)}}"){
            $('#movementModal').modal('show');
        }
        
    </script>
     <script>
        $('#dataTable').DataTable({
            "ordering": false,
            "order": [],
            'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
        });

        $(document).on('click','.delete',function(){
         
            let id_product = $(this).attr('data-id-product');
    
            $('#id_product_modal').val(id_product);
        });

        $("#file").on('change',function(){
            
            var file = document.getElementById("file").value;

            /*Extrae la extencion del archivo*/
            var basename = file.split(/[\\/]/).pop(),  // extract file name from full path ...
                                               // (supports `\\` and `/` separators)
            pos = basename.lastIndexOf(".");       // get last position of `.`

            if (basename === "" || pos < 1) {
                alert("El archivo no tiene extension");
            }          
            /*-------------------------------*/     

            if(basename.slice(pos + 1) == 'xlsx'){
                
            }else{
                alert("Solo puede cargar archivos .xlsx");
            }            
               
        });

        function import_product(){
            document.getElementById("fileForm").submit();
        }

        function import_product_update_price(){
            document.getElementById("fileForm").action = "{{ route('import_product_update_price') }}";
            document.getElementById("fileForm").submit();
        }

        

        $("#file_form").on('change',function(){
            
            var file = document.getElementById("file_form").value;

            /*Extrae la extencion del archivo*/
            var basename = file.split(/[\\/]/).pop(),  // extract file name from full path ...
                                               // (supports `\\` and `/` separators)
            pos = basename.lastIndexOf(".");       // get last position of `.`

            if (basename === "" || pos < 1) {
                alert("El archivo no tiene extension");
            }          
            /*-------------------------------*/     

            if(basename.slice(pos + 1) == 'xlsx'){
              
            }else{
                alert("Solo puede cargar archivos .xlsx");
            }            
               
        });


        $("#contrapartida").on('change',function(){
            var contrapartida_id = $(this).val();
            $("#subcontrapartida").val("");
            
            getSubcontrapartida(contrapartida_id);
        });

        function getSubcontrapartida(contrapartida_id){
            
            $.ajax({
                url:"{{ route('directpaymentorders.listcontrapartida') }}" + '/' + contrapartida_id,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                    let subcontrapartida = $("#subcontrapartida");
                    let htmlOptions = `<option value='' >Seleccione..</option>`;
                    // console.clear();
                    if(response.length > 0){
                        response.forEach((item, index, object)=>{
                            let {id,description} = item;
                            htmlOptions += `<option value='${id}' {{ old('Subcontrapartida') == '${id}' ? 'selected' : '' }}>${description}</option>`

                        });
                    }
                    //console.clear();
                    // console.log(htmlOptions);
                    subcontrapartida.html('');
                    subcontrapartida.html(htmlOptions);
                
                    
                
                },
                error:(xhr)=>{
                    alert('Presentamos inconvenientes al consultar los datos');
                }
            })
        }

        $("#subcontrapartida").on('change',function(){
                var subcontrapartida_id = $(this).val();
                var contrapartida_id    = document.getElementById("contrapartida").value;
                
            });
        </script> 
@endsection
