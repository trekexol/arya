@extends('admin.layouts.dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="row py-lg-2">
      <div class="col-md-2">
          <h2>Facturas</h2>
      </div>
    </div>
    <div class="row py-lg-2">
      <div class="col-sm-4">
        Cantidad de Mesas: {{ count($cantidadmesas) }}
    </div>
    <div class="col-sm-4">
        Mesas Disponibles: {{ count($mesas) }}
    </div>
        <div class="col-sm-4">
            <button type="button" data-toggle="modal" data-target="#exampleModalCenter" class="btn btn-info  float-md-right" >Gestionar Mesas</button>
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
        <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0" >
            <thead>
            <tr>
                <th class="text-center">Fecha</th>
                <th class="text-center">MESA</th>
                <th class="text-center">Cliente</th>
                <th class="text-center">Vendedor</th>
                <th class="text-center">REF</th>
                <th class="text-center">Monto</th>
                <th class="text-center">---</th>
            </tr>
            </thead>

            <tbody>

                @foreach ($quotations as $quotation)
                <?php
                    $amount_bcv = 1;
                    $totalbs = $quotation->amount_with_iva + $quotation->IGTF_amount - $quotation->retencion_iva - $quotation->retencion_islr;
                    $amount_bcv = $totalbs / $quotation->bcv;

                ?>

                    <tr>
                        <td class="text-center font-weight-bold" style="width:11%;">{{date_format(date_create($quotation->date_billing),"d-m-Y") ?? ''}} </td>
                        <td class="text-right font-weight-bold">{{$quotation->mesa}}</td>
                        <td class="text-center font-weight-bold"><a data-toggle="modal" data-nro="{{$quotation->id}}" data-target="#ModalCenter" class="btn btn-link cliente">{{$quotation->clients['name'] ?? ''}} </a> </td>
                        <td class="text-center font-weight-bold">{{$quotation->vendors['name'] ?? ''}}</td>
                        <td class="text-right font-weight-bold">${{number_format($amount_bcv, 2, ',', '.')}}</td>
                        <td class="text-center font-weight-bold">{{number_format($totalbs, 2, ',', '.')}} Bs</td>
                        <td class="text-center font-weight-bold"><button data-toggle="modal" data-nro="{{$quotation->id}}" data-target="#ModalCenter" class="btn btn-outline-primary factura">Facturar</button></td>
                    </tr>
                @endforeach

            </tbody>
        </table>


        </div>
    </div>
</div>
  <!-- Modal de gestionar mesas -->
  <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Gestion de Mesas</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="number" name="cantidadmesas" id="cantidadmesas" value="0">
              </div>
              <div class="form-check form-check-inline">
                <button type="button" class="btn btn-info  float-md-right procesarmesa" >Procesar</button>
            </div>
        </div>
      </div>
    </div>
  </div>
    <!--FIN Modal de gestionar mesas -->

    <!-- Modal de Facturacion -->
  <div class="modal fade bd-example-modal-lg" id="ModalCenter" tabindex="-1" role="dialog" aria-labelledby="ModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Facturar</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="modalfacturas">

        </div>
      </div>
    </div>
  </div>
    <!--FIN Modal de Facturacion -->
@endsection
@section('javascript')
<script type="text/javascript">
        $('#dataTable').dataTable( {
        "ordering": false,
        "order": [],
            'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
    } );
$(document).ready(function(){

    $('.factura').click(function(e){
        e.preventDefault();
            var value = $(this).data('nro');
            var url = "{{ route('facturar') }}";
            $.post(url,{value: value,"_token": "{{ csrf_token() }}"},function(data){
                $("#modalfacturas").empty().append(data);
            });
    });


    $('.cliente').click(function(e){
        e.preventDefault();
            var value = $(this).data('nro');
            var url = "{{ route('cliente') }}";
            $.post(url,{value: value,"_token": "{{ csrf_token() }}"},function(data){
                $("#modalfacturas").empty().append(data);
            });
    });

    $('.procesarmesa').click(function(e){
      e.preventDefault();

    var cantidad = $("#cantidadmesas").val();
        if(cantidad == 0){
            Swal.fire({
                        icon: 'error',
                        title: 'Error...',
                        text: 'Error Debe Ingresar una Cantidad Mayor o Menor a Cero (0)!',
                        })
        }
        if(cantidad > 0  || cantidad < 0){

        $.ajax({
        method: "POST",
        url: "{{ route('procesarmesas') }}",
        data: {cantidad: cantidad, "_token": "{{ csrf_token() }}"},
             success:(response)=>{
                 if(response.error == true){
                    Swal.fire({
                        icon: 'success',
                        title: 'Exito!',
                        text: response.msg,
                        })
                        setTimeout("location.reload()", 1500);
                 }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Error...',
                        text: response.msg,
                        })
                 }
             },
             error:(xhr)=>{
                Swal.fire({
                        icon: 'error',
                        title: 'Error...',
                        text: response.msg,
                        });
             }
         })
        }


});


});

    </script>

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
@endsection
