@extends('admin.layouts.dashboard')

@section('content')
<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
        <div class="col-md-6">
            <h2>Importaciones</h2>
        </div>
        @if (Auth::user()->role_id  == '1' || $agregarmiddleware  == '1')
        <div class="col-sm-6">
            <a href="{{route('imports.create')}}" class="btn btn-primary btn-lg float-md-right" role="button" aria-pressed="true">Registrar una Importacion</a>
        </div>
        @endif
    </div>
  </div>

  {{-- VALIDACIONES-RESPUESTA--}}
@include('admin.layouts.success')   {{-- SAVE --}}
@include('admin.layouts.danger')    {{-- EDITAR --}}
@include('admin.layouts.delete')    {{-- DELELTE --}}
{{-- VALIDACIONES-RESPUESTA --}}

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Listado de Importaciones</h6>
    </div>

    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
            <tr>
                <th>Nro de Importacion</th>
                <th>Descripción</th>
                <th>Fecha</th>
                @if (Auth::user()->role_id  == '1' || $agregarmiddleware  == '1')
                <th>Cerrar</th>
                @endif
            </tr>
            </thead>
            <tbody>
                @if (count($imports) > 0)

                    @foreach ($imports as $key => $var)
                        <tr>

                            <td>
                                {{$var->id}}
                            </td>

                            <td class="text-center">{{$var->observaciones}}</td>
                            <td class="text-center">{{$var->fecha}}</td>
                            @if (Auth::user()->role_id  == '1' || $agregarmiddleware  == '1')
                            <td>
                                <a type="button" data-toggle="modal" data-id="calcular" data-valor="{{$var->id}}" data-target="#MatchModal" name="matchvalue" class="btn btn-success btn-sm"  href="#">Calcular</a>
                                <a type="button" data-toggle="modal" data-id="eliminar" data-valor="{{$var->id}}" data-target="#MatchModal" name="matchvalue" class="btn btn-danger btn-sm"  href="#">Eliminar</a>
                            </td>
                            @endif
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
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

  @section('validacion')
  <script>
    $('[name="matchvalue"]').click(function(e){
        e.preventDefault();
        idvalor = $(this).attr('data-id');
        id = $(this).attr('data-valor');
        var url = "{{route('imports.cargaropciones')}}";


     $.post(url,{"_token": "{{ csrf_token() }}",id: id,idvalor: idvalor},function(data){
            $("#modalfacturas").empty().append(data);

          });



     });



    </script>
@endsection
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
