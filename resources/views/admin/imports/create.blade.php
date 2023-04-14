@extends('admin.layouts.dashboard')

@section('content')

@if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{$error}}</li>
                @endforeach
            </ul>
        </div>
    @endif
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-center font-weight-bold h3">Registro Factura Importacion</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('imports.store') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <a href="#" data-toggle="modal" data-id="inv" data-target="#MatchModal" name="matchvalue"><i class="fa fa-search" style="font-size:24px"></i> Buscar Factura Inventario</a>
                            </div>
                            <div class="col-sm-2">

                                <input id="nro_factura" type="hidden" class="form-control @error('nro_factura') is-invalid @enderror" name="Nro_Factura" value="{{ $id ?? null }}" required autocomplete="nro_factura" readonly>

                            </div>

                            @if($id)
                                <div class="col-sm-4">
                                    <a href="#" type="btn btn-primary" data-toggle="modal" data-target="#MatchModal" data-id="ser" name="matchvalue"><i class="fa fa-search" style="font-size:24px"></i>Buscar Factura Servicio</a>

                            </div>
                            @endif
                        </div>
                    @if($id)

                        <div class="form-group row">

                            <div class="col-sm-2">
                                <label id="date_begin" for="type" >Fecha:</label>
                            </div>
                            <div class="col-sm-4">
                                <input id="date_begin" type="date" class="form-control @error('date_begin') is-invalid @enderror" name="Fecha" value="{{ old('Fecha') }}" required autocomplete="date_begin" >
                                @error('date_begin')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-sm-2">
                                <label id="observaciones" for="type" >Observaciones:</label>
                            </div>

                            <div class="col-sm-4">
                                <input id="observaciones" type="text" class="form-control @error('observaciones') is-invalid @enderror" name="Observaciones" value="{{ old('Observaciones') }}" required autocomplete="Observaciones" >

                                @error('observaciones')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        @if (count($datosinv) > 0)
                        <div class="table-responsive">
                            <table class="table table-light2 table-bordered" id="ext" >
                                <thead>
                                    <tr>
                                        <th class="text-center" colspan="4">Productos de Factura</th>
                                    </tr>
                                <tr>
                                    <th class="text-center">Producto</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-center">Precio Unitario</th>
                                    <th class="text-center">Precio</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $total = 0;
                                    @endphp
                                    @foreach($datosinv as $datosinv)

                                                        <tr>
                                                            <td class="text-center">{{$datosinv->description}}</td>
                                                            <td class="text-center">{{$datosinv->amount}}</td>
                                                            <td class="text-center">{{$datosinv->price}}</td>
                                                            <td class="text-center">{{number_format($datosinv->amount * $datosinv->price,2,'.','')}}</td>
                                                        </tr>
                                   <?php
                                    $total += number_format($datosinv->amount * $datosinv->price,2,'.','');

                                    $arreglo[] = ['descripcion' => $datosinv->description, 'cantidad' => $datosinv->amount, 'precio' => $datosinv->price];

                                   ?>

                                    @endforeach
                                                        <tr>
                                                            <td class="text-center" colspan="3">Precio total de productos</td>
                                                            <td class="text-center">{{number_format($total,2,'.','')}}</td>
                                                            <td style="display: none"></td>
                                                            <td style="display: none"></td>
                                                        </tr>
                                </tbody>
                            </table>
                        </div>
                        @endif


                        <br>

                        @if (count($datosserv) > 0)
                        <div class="table-responsive">
                            <table class="table table-light2 table-bordered" id="ext2" width="100%" cellspacing="0" >
                                <thead>
                                    <tr>
                                        <th class="text-center" colspan="4">Servicios de Facturas</th>
                                    </tr>
                                <tr>
                                    <th class="text-center">Factura</th>
                                    <th class="text-center">Servicio</th>
                                    <th class="text-center">Precio</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $totalser = 0;
                                    @endphp
                                    @foreach($datosserv as $datosserv)

                                                        <tr>
                                                            <td class="text-center">{{$datosserv->invoice}}</td>
                                                            <td class="text-center">{{$datosserv->description}}</td>
                                                            <td class="text-center">{{$datosserv->price}}</td>
                                                        </tr>
                                    @php
                                    $totalser += number_format($datosserv->price,2,'.','')
                                    @endphp

                                    @endforeach
                                    <tr>
                                        <td class="text-center" colspan="2">Precio total de servicios</td>
                                        <td class="text-center">{{number_format($totalser,2,'.','')}}</td>
                                        <td style="display: none"></td>
                                        <td style="display: none"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <?php

                        $precioserviciofact = number_format($total + $totalser,2,'.','');
                        $costo = number_format($precioserviciofact / $total,2,'.','');
                        ?>

                        <div class="table-responsive">
                            <table class="table table-light2 table-bordered" id="ext2" width="100%" cellspacing="0" >
                                <thead>
                                <tr>
                                    <tr>
                                        <th class="text-center" colspan="4">Total Costo</th>
                                    </tr>
                                    <th class="text-center">Producto</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-center">Precio Nuevo</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach ($arreglo as $arreglo)
                                    <tr>
                                        <td>{{$arreglo['descripcion']}}</td>
                                        <td>{{$arreglo['cantidad']}}</td>
                                        <td>{{$totalprecioaumentado = number_format($arreglo['precio'] * $costo,2,'.','')}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @endif
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                   Registrar Importacion
                                </button>
                            </div>
                        </div>
                    @endif
                    </form>
                </div>
            </div>
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

	$(function(){
        soloAlfaNumerico('description');
    });


    $('[name="matchvalue"]').click(function(e){
        e.preventDefault();
        idvalor = $(this).attr('data-id');

        if(idvalor == 'inv'){
            idfact = $("#nro_factura").val();
            var url = "{{route('imports.cargar')}}";

        }

        if(idvalor == 'ser'){
            idfact = $("#nro_factura").val();
            var url = "{{route('imports.cargarservicio')}}";

        }

     $.post(url,{"_token": "{{ csrf_token() }}",idfact: idfact},function(data){
            $("#modalfacturas").empty().append(data);

          });



     });



    </script>
@endsection
