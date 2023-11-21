@extends('admin.layouts.dashboard')

@section('content')

<ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
    @if (Auth::user()->role_id  == '1')


      <li class="nav-item" role="presentation">
        <a class="nav-link font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('products') }}" role="tab" aria-controls="home" aria-selected="true">Productos</a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link  font-weight-bold" style="color: black;" id="profile-tab"  href="{{ route('inventories') }}" role="tab" aria-controls="profile" aria-selected="false">Inventarios</a>
      </li>
      <li class="nav-item" role="presentation">
          <a class="nav-link font-weight-bold" style="color: black;" id="home-tab"  href="{{ route('combos') }}" role="tab" aria-controls="home" aria-selected="true">Combos</a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link active font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('inventories.movement') }}" role="tab" aria-controls="contact" aria-selected="false">Movimientos de Inventario</a>
      </li>


    @else

      @foreach($sistemas as $sistemas)
        <li class="nav-item" role="presentation">
          <a class="nav-link font-weight-bold" style="color: black;" id="home-tab"  href="{{ route($sistemas->ruta) }}" role="tab" aria-controls="home" aria-selected="false">{{$sistemas->name}}</a>
        </li>

      @endforeach
      @if($namemodulomiddleware == 'Inventario')
      <li class="nav-item" role="presentation">
          <a class="nav-link active font-weight-bold" style="color: black;" id="contact-tab"  href="{{ route('inventories.movement') }}" role="tab" aria-controls="contact" aria-selected="false">Movimientos de Inventario</a>
        </li>
      @endif

  @endif
  </ul>

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
        <div class="col-sm-12">
            <div class="card">
                <form id="formPost" method="POST" action="{{ route('reports.storemovements') }}">
                    @csrf



                <div class="card-header text-center h4">
                       Historial de Inventario
                </div>

                <div class="card-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="date_begin" class="col-sm-1 col-form-label text-md-right">Desde:</label>

                            <div class="col-sm-3">
                                <input id="date_begin" type="date" class="form-control @error('date_begin') is-invalid @enderror" name="date_begin" value="{{  date('Y-m-d', strtotime($date_frist ?? '')) }}" required autocomplete="date_begin">
                            </div>
                            <div class="col-sm-2">
                                <select class="form-control" name="type" id="type">
                                   <?php
                                   $typearray[] = array('todo','Todo');
                                   $typearray[] = array('nota','Nota');
                                   $typearray[] = array('venta','Ventas');
                                   $typearray[] = array('compra','Compras');
                                   $typearray[] = array('pedido','Pedidos');
                                   $typearray[] = array('combo','Combos');
                                   $typearray[] = array('aju_nota','Ajuste de Nota');
                                   $typearray[] = array('rev_nota','Reverso de Nota');
                                   $typearray[] = array('rev_venta','Reverso de Venta');
                                   $typearray[] = array('entrada','Entrada de Inventario');
                                   $typearray[] = array('salida','Salida de Inventario');
                                   ?>

                                   @if (isset($type))
                                        @for ($q=0;$q<count($typearray);$q++)
                                                @if ($type == $typearray[$q][0])
                                                <option selected value="{{$typearray[$q][0]}}">{{$typearray[$q][1]}}</option>
                                                @else
                                                <option value="{{$typearray[$q][0]}}">{{$typearray[$q][1]}}</option>
                                                @endif
                                        @endfor
                                    @else
                                        @for ($q=0;$q<count($typearray);$q++)

                                            <option value="{{$typearray[$q][0]}}">{{$typearray[$q][1]}}</option>
                                        @endfor
                                    @endif

                                </select>
                            </div>
                            <div class="col-sm-3">
                                <select class="form-control" name="id_account" id="id_account">


                                            @if (isset($accounts))
                                                   <option value="todas">Todas</option>
                                                    @foreach ($accounts as $var)
                                                        @if($id_account == $var->id)
                                                        <option selected value="{{$var->id}}">{{$var->code_one.'.'.$var->code_two.'.'.$var->code_three.'.'.$var->code_four.'.'.str_pad($var->code_five, 3, "0", STR_PAD_LEFT)}} - {{$var->description}}</option>
                                                        @else
                                                       <option value="{{$var->id}}">{{$var->code_one.'.'.$var->code_two.'.'.$var->code_three.'.'.$var->code_four.'.'.str_pad($var->code_five, 3, "0", STR_PAD_LEFT)}} - {{$var->description}}</option>
                                                       @endif
                                                    @endforeach
                                            @else
                                                    <option selected value="todas">Todas</option>
                                                    @foreach ($accounts as $var)
                                                    <option value="{{$var->id}}">{{$var->code_one.'.'.$var->code_two.'.'.$var->code_three.'.'.$var->code_four.'.'.str_pad($var->code_five, 3, "0", STR_PAD_LEFT)}} - {{$var->description}}</option>
                                                    @endforeach

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

                        <div class="form-group row">
                            <label for="date_end" class="col-sm-1 col-form-label text-md-right">Hasta:</label>

                            <div class="col-sm-3">
                                <input id="date_end" type="date" class="form-control @error('date_end') is-invalid @enderror" name="date_end" value="{{ date('Y-m-d', strtotime($date_end ?? ''))}}" required autocomplete="date_end">

                                @error('date_end')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>




                            <div class="col-sm-2">
                                <select class="form-control" name="coin" id="coin">
                                    @if(isset($coin))

                                        <option selected value="dolares">Dolares</option>
                                        <option  value="bolivares">Bolívares</option>
                                    @else
                                        <option selected value="dolares">Dolares</option>
                                        <option  value="bolivares">Bolívares</option>
                                    @endif


                                </select>
                            </div>
                            <div class="col-sm-4">
                                <select class="form-control" name="id_inventories" id="id_inventories">


                                            @if (isset($id_inventory))
                                                   <option value="todos">Todos</option>
                                                    @foreach ($inventories as $var)
                                                        @if($id_inventory == $var->id_inventory)
                                                        <option selected value="{{$var->id_inventory}}">{{$var->code_comercial}} - {{$var->description}}</option>
                                                        @else
                                                       <option value="{{$var->id_inventory}}">{{$var->code_comercial}} - {{$var->description}}</option>
                                                       @endif
                                                    @endforeach
                                            @else
                                                    <option selected value="todos">Todos</option>
                                                    @foreach ($inventories as $var)
                                                    <option value="{{$var->id_inventory}}">{{$var->code_comercial}} - {{$var->description}}</option>
                                                    @endforeach

                                            @endif
                                </select>
                            </div>
                            <div class="col-sm-1">
                                <button type="submit" class="btn btn-primary ">
                                    Buscar:
                                 </button>
                                </div>
                        </div>
                    </form>
                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item" src="{{route('reports.movements_pdf',[$coin ?? 'dolares',$date_frist ?? 'todo',$date_end ?? 'todo',$type ?? 'todo',$id_inventory,$id_account ?? 'todas'])}}" allowfullscreen></iframe>
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
            'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
        });

        function exportToExcel(){
            var old_action = document.getElementById("formPost").action;
            document.getElementById("formPost").action = "{{ route('export_reports.inventoriesmovement') }}";
            document.getElementById("formPost").submit();
            document.getElementById("formPost").action = old_action;
        }

           $("#id_account").on('change',function(){
                var id_account = $(this).val();
                $("#id_inventories").val("");
                getinventory(id_account);


                //alert(id_account);
            });

        function getinventory(id_account){
            // alert(`../municipio/list/${estado_id}`);
            $.ajax({
                url:`../inventories/getinventory/${id_account}`,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                    let inventories = $("#id_inventories");
                    let htmlOptions = '';

                    // console.clear();
                    if(response.length > 0){

                        htmlOptions = `<option value='todos'>Todos</option>`;

                        response.forEach((item, index, object)=>{
                            let {id,description,code_comercial} = item;
                            htmlOptions += `<option value='${id}'>${code_comercial} - ${description}</option>`

                        });
                    } else {
                         htmlOptions = `<option value='todos' >No Tiene Registros</option>`;
                    }
                    //console.clear();
                    // console.log(htmlOptions);
                    inventories.html('');
                    inventories.html(htmlOptions);



                },
                error:(xhr)=>{
                    alert('Presentamos inconvenientes al consultar los datos');
                }
            })
        }



        </script>


@endsection
