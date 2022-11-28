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
                <th>Nro</th>
                <th>Importacion</th>
                <th>Factura</th>
                @if (Auth::user()->role_id  == '1' || $agregarmiddleware  == '1')
                <th>Cerrar</th>
                @endif
            </tr>
            </thead>
            <tbody>
                @if (empty($imports))
                @else
                    @foreach ($imports as $key => $var)
                        <tr>
                            @if (Auth::user()->role_id  == '1' || $agregarmiddleware  == '1')
                            <td>
                                <a type="button" class="btn btn-info"  href="{{route('imports.selectimport',$var->id)}}">{{$var->id}}</a>
                            </td>
                            @else
                            <td>
                            {{$var->id}}
                            </td>
                            @endif
                            <td class="text-center">{{$var->id_purchases}}</td>
                            <td class="text-center">{{$var->fecha}}</td>
                            @if (Auth::user()->role_id  == '1' || $agregarmiddleware  == '1')
                            <td>
                                <a type="button" class="btn btn-success btn-lg btn-block"  href="{{route('imports.calcular',$var->id)}}">Calcular</a>
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

@endsection
