@extends('admin.layouts.dashboard')

@section('content')

<ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
    @if (Auth::user()->role_id  == '1')


      <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" href="{{ route('products') }}" role="tab" aria-controls="home" aria-selected="true">Productos</a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;"  href="{{ route('inventories') }}" role="tab" aria-controls="profile" aria-selected="false">Inventario</a>
      </li>
      <li class="nav-item" role="presentation">
          <a class="nav-link active font-weight-bold" style="color: black;" href="{{ route('combos') }}" role="tab" aria-controls="home" aria-selected="true">Combos</a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" href="{{ route('inventories.movement') }}" role="tab" aria-controls="contact" aria-selected="false">Movimientos de Inventario</a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" href="{{ route('warehouse') }}" role="tab" aria-controls="contact" aria-selected="false">Almacenes</a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" href="{{ route('warehouse.movement') }}" role="tab" aria-controls="contact" aria-selected="false">Transferencia de Almacén</a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" href="{{ route('warehouse.indexmovementwarehouse') }}" role="tab" aria-controls="contact" aria-selected="false">Movimiento de Almacén</a>
      </li>
    @else

    @foreach($sistemas as $sistemas)
    @if($namemodulomiddleware == $sistemas->name)
<li class="nav-item" role="presentation">
    <a class="nav-link active font-weight-bold" style="color: black;" id="home-tab"  href="{{ route($sistemas->ruta) }}" role="tab" aria-controls="home" aria-selected="false">{{$sistemas->name}}</a>
  </li>
  @else
  <li class="nav-item" role="presentation">
    <a class="nav-link font-weight-bold" style="color: black;" id="home-tab"  href="{{ route($sistemas->ruta) }}" role="tab" aria-controls="home" aria-selected="false">{{$sistemas->name}}</a>
  </li>
  @endif
  @if($sistemas->name == 'Inventario')
  <li class="nav-item" role="presentation">
      <a class="nav-link font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('inventories.movement') }}" role="tab" aria-controls="contact" aria-selected="false">Movimientos de Inventario</a>
    </li>
  @endif
@endforeach


  @endif
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
              <h6>Importación Masiva de Combos</h6>
              <a href="{{ route('export.product_template_combo') }}" class="dropdown-item bg-success text-white h5">Descargar Plantilla Excel</a> 
              <form id="fileForm" method="POST" action="{{ route('import_combo') }}" enctype="multipart/form-data" >
                @csrf
                <input id="file" type="file" value="import" accept=".xlsx" name="file" class="file">
              </form>
              <br>
              <a href="#" onclick="import_product();" class="dropdown-item bg-warning text-white h5">Subir Plantilla Excel</a> 
             <!-- <a href="#" onclick="import_product_update_price();" class="dropdown-item bg-info text-white h5">Actualizar Precio Productos</a> -->
          </div> 
      </div> 

    
      @if (Auth::user()->role_id  == '1' || $agregarmiddleware  == '1')
      <!--<div class="col-md-6">
        <a href="{{ ''/*route('combos.create')*/}}" class="btn btn-primary float-md-right" role="button" aria-pressed="true">Registrar un Combo</a>
      </div> -->

        <div class="col-sm-3">
            <a href="{{ route('products.create',['COMBO'])}}" class="btn btn-primary float-md-right" role="button" aria-pressed="true">Registrar un Combo </a>
        </div>
      @endif
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
               
              <th class="text-center" width="1%">ID</th>
              <th class="text-center" width="1%">Código Comercial</th>
              <th class="text-center">Descripción</th>
              <th class="text-center" width="1%">Tipo</th>
              <th class="text-center">Precio</th>
              <th class="text-center">Costo</th>
              <th class="text-center" width="1%">Moneda</th>
              <th class="text-center" width="9%"></th>
            </tr>
            </thead>
            
            <tbody>
                @if (empty($combos))
                @else  
                    @foreach ($combos as $combo)
                        <tr>
                            <td class="text-center">{{$combo->id}}</td>
                            <td class="text-center">{{$combo->code_comercial}}</td>
                            <td class="text-center">{{$combo->description}}</td>
                            <td class="text-center">{{$combo->type}}</td>
                            <td class="text-right">{{number_format($combo->price, 3, ',', '.')}}</td>  
                            <td class="text-right">{{number_format($combo->price_buy, 3, ',', '.')}}</td>
           
                            @if ($combo->money == 'Bs')
                            <td class="text-center">Bs</td>
                            @else
                            <td class="text-center">USD</td>
                            @endif
    
                            <td class="text-center">
                              @if (Auth::user()->role_id  == '1' || $agregarmiddleware  == '1')
                              <a href="{{ route('combos.create_assign',$combo->id) }}"  title="Asignar Productos"><i class="fa fa-check"></i></a>
                              @endif
                              @if (Auth::user()->role_id  == '1' || $actualizarmiddleware  == '1')
                              <a href="{{ route('combos.edit',$combo->id) }}"  title="Editar"><i class="fa fa-edit"></i></a>
                              @endif
                              @if (Auth::user()->role_id  == '1' || $eliminarmiddleware  == '1')  
                              <a href="#" class="delete" data-id-combo={{$combo->id}} data-toggle="modal" data-target="#deleteModal" title="Eliminar"><i class="fa fa-trash text-danger"></i></a>  
                              @endif
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
          <form action="{{ route('combos.delete') }}" method="post">
              @csrf
              @method('DELETE')
              <input id="id_combo_modal" type="hidden" class="form-control @error('id_combo_modal') is-invalid @enderror" name="id_combo_modal" readonly required autocomplete="id_combo_modal">
                     
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
  
@endsection
@section('javascript')
     <script>
        $('#dataTable').DataTable({
            "ordering": false,
            "order": [],
            'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
        });

        $(document).on('click','.delete',function(){
         
            let id_combo = $(this).attr('data-id-combo');
    
            $('#id_combo_modal').val(id_combo);
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

        /*function import_product_update_price(){
            document.getElementById("fileForm").action = "{{ route('import_product_update_price') }}";
            document.getElementById("fileForm").submit();
        }*/

        function loadimg (url){
        
                const domString = url
                //console.log(domString)
                var ctx = canvas.getContext('2d')
                var img = new Image()
                img.src = domString
                img.onload = function(){
                document.getElementById('myImage').setAttribute('src',domString)
                }
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


        </script> 
@endsection
