@extends('admin.layouts.dashboard')

@section('content')

<?php
$suma_debe = 0;
$suma_haber = 0;
?>

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


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-center font-weight-bold h3">Registro Comprobante Detalles</div>

                <div class="card-body">
                    <form id="headerForm" method="POST" action="{{ route('headervouchers.store') }}" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="coin" value="{{$coin ?? 'bolivares'}}" readonly>
                        <input type="hidden" name="id_header" value="{{$header->id ?? null}}" readonly>
                        <input type="hidden" id="tasa_comp" name="tasa_comp" value="{{ number_format($tasa_calculada ?? $detailvouchers_last->tasa ?? $bcv, 10, ',', '.') }}">


                        @if (isset($tasa_calculada) && $tasa_calculada != 0)
                            <input type="hidden" name="tasa_calculada" value="{{bcdiv($tasa_calculada ?? $detailvouchers_last->tasa ?? $bcv, '1', 2)}}" readonly>
                        @else
                            <input type="hidden" name="tasa_calculada" value="{{bcdiv($bcv, '1', 2)}}" readonly>
                        @endif

                        <div class="form-group row">
                            <label for="reference" class="col-sm-2 col-form-label text-md-right">Número</label>

                            <div class="col-sm-3">
                                @if(isset($header))
                                    <input id="reference" type="text" class="form-control @error('reference') is-invalid @enderror" name="reference" value="{{ $header->id ?? '' }}" required autocomplete="reference">
                                @else
                                     <input id="reference" type="text" class="form-control @error('reference') is-invalid @enderror" name="reference" placeholder="Numero Disponible: {{ $header_number ?? 0 }}" required autocomplete="reference">
                                @endif
                                @error('reference')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-1">
                                <a id="btn_search_reference" class="btn btn-info " onclick="searchReference()" title="Buscar Referencia">Buscar</a>
                            </div>
                            @if (Auth::user()->role_id  == '1' || $agregarmiddleware  == '1')
                            <div class="col-sm-3">
                                <button type="submit" class="btn btn-primary" title="Agregarheader">Registrar Comprobante</button>
                            </div>
                            @endif
                            @if (Auth::user()->role_id  == '1' || $eliminarmiddleware  == '1')
                            <div class="col-sm-2">
                               <a class="btn btn-danger" href="#"  data-toggle="modal" data-target="#disableModal" title="Deshabilitar">Deshabilitar</a>
                            </div>
                            @endif
                            <div class="col-sm-1">
                                <a id="btn_clean" class="btn btn-light2" href="{{ route('detailvouchers.create','bolivares') }}" title="Limpiar Referencia">Limpiar</a>
                            </div>
                        </div>

                            @if (isset($header) && ($header->reference))
                                <div class="form-group row">
                                    <label for="reference_header" class="col-md-2 col-form-label text-md-right">Referencia</label>

                                    <div class="col-md-3">
                                            <input id="reference_header" type="text" class="form-control @error('reference_header') is-invalid @enderror" name="reference_header" value="{{ $header->reference ?? '' }}" required autocomplete="reference_header">

                                        @error('reference_header')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            @endif

                            <div class="form-group row">
                                <label for="description" class="col-md-2 col-form-label text-md-right">Descripción</label>

                                <div class="col-md-4">
                                    @if(isset($header))
                                        <input id="description" type="text" class="form-control @error('description') is-invalid @enderror" name="description" value="{{ $header->description ?? old('description') }} Factura({{$detailvouchers[0]->quotations['number_invoice'] ?? $detailvouchers[0]->expenses['invoice'] }})"  required autocomplete="description" >
                                    @else
                                        <input id="description" type="text" class="form-control @error('description') is-invalid @enderror" name="description"  required autocomplete="description" >
                                    @endif
                                    @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                </div>

                            </div>

                            <div class="form-group row">
                                <label for="date" class="col-md-2 col-form-label text-md-right">Fecha del Comprobante</label>

                                <div class="col-md-4">
                                    @if(isset($header))
                                        <input id="date_begin" type="date" class="form-control @error('date') is-invalid @enderror" name="date" value="{{ $header->date ?? '' }}"  required autocomplete="date">
                                    @else
                                        <input id="date_begin" type="date" class="form-control @error('date') is-invalid @enderror" name="date" value="{{ $datenow ?? '' }}" required autocomplete="date">
                                    @endif
                                    @error('date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                @if (isset($header))
                                <div class="col-sm-3">
                                    <button onclick="updateForm()" class="btn btn-success" title="Actualizar">Guardar Cambios</button>
                                </div>
                            @endif
                        </form>
                            <!--<label for="date" class="col-md-2 col-form-label text-md-right"><h5>Total</h5></label>
                            <div class="col-md-2 col-form-label text-md-left">
                                <label for="description" ><h6>{{ $suma_debe - $suma_haber }}</h6></label>
                            </div>-->
                        </div>


                <form method="POST" action="{{ route('detailvouchers.store') }}" id="fo" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row" id="formcoin">
                        <label id="coinlabel" for="coin" class="col-md-2 col-form-label text-md-right">Moneda:</label>

                        <div class="col-md-2">
                            <select class="form-control" name="coin" id="coin">
                                <option value="bolivares">Bolívares</option>
                                @if($coin == 'dolares')
                                    <option selected value="dolares">Dolares</option>
                                @else
                                    <option value="dolares">Dolares</option>
                                @endif
                            </select>
                        </div>
                        <label for="rate" class="col-md-1 col-form-label text-md-right">Tasa:</label>
                        <div class="col-md-2">
                            <input id="rate" type="text" class="form-control @error('rate') is-invalid @enderror" name="rate" value="{{ number_format($tasa_calculada ?? $detailvouchers_last->tasa ?? $bcv, 10, ',', '.') }}"  required autocomplete="rate">
                            @error('rate')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--<a href="#" onclick="refreshrate()" title="tasaactual"><i class="fa fa-redo-alt"></i></a> -->
                        <label  class="col-md-2 col-form-label text-md-right h6">Tasa actual:</label>
                        <div class="col-md-2 col-form-label text-md-left">
                            <label for="tasaactual" id="tasaacutal">{{ number_format($bcv, 2, ',', '.')}}</label>
                        </div>
                    </div>

                        <input type="hidden" name="id_header_voucher" value="{{$header->id ?? ''}}" readonly>
                        <input type="hidden" name="period" value="{{$account->period ?? ''}}" readonly>
                        <input type="hidden" name="id_account" value="{{$account->id ?? ''}}" readonly>
                        <input id="id_user" type="hidden" class="form-control @error('id_user') is-invalid @enderror" name="id_user" value="{{ Auth::user()->id }}" readonly required autocomplete="id_user">


                                <div class="form-row">

                                    <div class="form-group col-md-1">
                                        <label for="description" >Cuenta</label>
                                        <input id="code_one" type="text" class="form-control @error('code_one') is-invalid @enderror" name="code_one" value="{{ session()->get('detail')->code_one ?? $account->code_one ?? old('code_one') }}" required readonly autocomplete="code_one"  autofocus>
                                    </div>
                                    <div class="form-group col-md-1">
                                        <label for="description" >.</label>
                                        <input id="code_two" type="text" class="form-control @error('code_two') is-invalid @enderror" name="code_two" value="{{ session()->get('detail')->code_two ?? $account->code_two ?? old('code_two') }}" required readonly autocomplete="code_two"  autofocus>
                                    </div>
                                    <div class="form-group col-md-1">
                                        <label for="description" >.</label>
                                    <input id="code_three" type="text" class="form-control @error('code_three') is-invalid @enderror" name="code_three" value="{{ session()->get('detail')->code_three ?? $account->code_three ?? old('code_three') }}" required readonly autocomplete="code_three"  autofocus>
                                    </div>
                                    <div class="form-group col-md-1">
                                        <label for="description" >.</label>
                                        <input id="code_four" type="text" class="form-control @error('code_four') is-invalid @enderror" name="code_four" value="{{ session()->get('detail')->code_four ?? $account->code_four ?? old('code_four') }}" required readonly autocomplete="code_four"  autofocus>
                                    </div>
                                    <div class="form-group col-md-1">
                                        <label for="description" >.</label>
                                        <input id="code_five" type="text" class="form-control @error('code_five') is-invalid @enderror" name="code_five" value="{{ session()->get('detail')->code_five ?? $account->code_five ?? old('code_five') }}" required readonly autocomplete="code_five"  autofocus>
                                    </div>
                                    <div class="form-group ">
                                        <a href="{{ route('detailvouchers.selectaccount',[$coin,$header->id ?? -1,'detail']) }}" title="Editar"><i class="fa fa-eye"></i></a>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="description" >Descripción</label>
                                        <input id="description" type="text" class="form-control @error('description') is-invalid @enderror" name="description" value="{{ session()->get('accountdetail')->description ?? $account->description ?? '' }}" readonly required autocomplete="description">

                                        @error('description')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label for="debe" >Debe</label>
                                        <input id="debe" type="text" autocomplete="off" placeholder='0,00' value="0,00" class="form-control @error('debe') is-invalid @enderror" name="debe" value="{{ session()->get('detail')->haber ?? '0,00' }}"  required>


                                        @error('debe')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="haber" >Haber</label>
                                        <input id="haber" type="text" class="form-control @error('haber') is-invalid @enderror" name="haber" value="{{ session()->get('detail')->debe ?? '0,00' }}"  required autocomplete="haber">

                                        @error('haber')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" title="Agregar"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>

                        </form>
                       <div class="card-body">
                        <div class="table-responsive">
                        <table class="table table-light2 table-bordered"  width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th>Cuenta</th>
                                <th>Descripción</th>
                                <th>Debe</th>
                                <th>Haber</th>

                                <th>Opciones</th>

                            </tr>
                            </thead>

                            <tbody>
                                @if (empty($detailvouchers))
                                @else

                                    @foreach ($detailvouchers as $key => $var)
                                    <tr>

                                        @if($var->status == 'N')
                                            <td><i class="fa fa-circle" style="color: rgb(252, 128, 128)"></i> {{$var->code_one}}.{{$var->code_two}}.{{$var->code_three}}.{{$var->code_four}}.{{ str_pad($var->code_five, 3, "0", STR_PAD_LEFT)}}</td>
                                        @else
                                            <td><i class="fa fa-circle" style="color: rgb(84, 196, 84)"></i> {{$var->code_one}}.{{$var->code_two}}.{{$var->code_three}}.{{$var->code_four}}.{{ str_pad($var->code_five, 3, "0", STR_PAD_LEFT)}}</td>
                                        @endif

                                        <td>{{$var->accounts['description']}}</td>


                                        @if ((isset($coin)) && ($coin == 'bolivares'))
                                            <?php
                                                $suma_debe += $var->debe;
                                                $suma_haber += $var->haber;
                                            ?>
                                            <td>{{number_format($var->debe, 2, ',', '.')}}</td>
                                            <td>{{number_format($var->haber, 2, ',', '.')}}</td>

                                        @else
                                            <?php
                                                $suma_debe += $var->debe / $var->tasa;
                                                $suma_haber += $var->haber / $var->tasa;
                                            ?>
                                            @if ($var->tasa != 0)
                                                <td>{{number_format($var->debe / $var->tasa, 2, ',', '.')}}</td>
                                                <td>{{number_format($var->haber / $var->tasa, 2, ',', '.')}}</td>
                                            @else
                                                <td>La Tasa es Cero</td>
                                                <td>La Tasa es Cero</td>
                                            @endif

                                        @endif


                                        <td>
                                            @if (Auth::user()->role_id  == '1' || $actualizarmiddleware  == '1')
                                            <a href="{{route('detailvouchers.edit',[$coin,$var->id]) }}" title="Editar"><i class="fa fa-edit"></i></a>
                                            @endif
                                            @if (Auth::user()->role_id  == '1' || $eliminarmiddleware  == '1')
                                            <a href="#" class="delete" data-id-detail={{$var->id}} data-coin={{$coin ?? 'bolivares'}} data-header={{$header->id}} data-toggle="modal" data-target="#deleteModal" title="Eliminar"><i class="fa fa-trash text-danger"></i></a>
                                            @endif
                                        </td>

                                    </tr>
                                    @endforeach

                                @endif
                            </tbody>
                        <tfoot>
                            <tr>

                                @if(number_format($suma_debe,2) == number_format($suma_haber,2))
                                    <td style="color: rgb(84, 196, 84)">El comprobante está cuadrado</td>
                                    <td>Total</td>
                                    <td>{{ number_format($suma_debe, 2, ',', '.') }}</td>
                                    <td>{{ number_format($suma_haber, 2, ',', '.') }}</td>
                                @else
                                    <td style="color: red">El comprobante está descuadrado {{$suma_debe}}/{{ $suma_haber}}</td>
                                    <td>Total</td>
                                    @if (bcdiv($suma_debe, '1', 2) > bcdiv($suma_haber, '1', 2))
                                        <td>{{number_format($suma_debe, 2, ',', '.')}}  <br><div style="color: red"> - {{ number_format($suma_debe - $suma_haber, 2, ',', '.')}}</div></td>
                                        <td>{{number_format($suma_haber, 2, ',', '.')}}</td>
                                    @else
                                        <td>{{number_format($suma_debe, 2, ',', '.')}}</td>
                                        <td>{{number_format($suma_haber, 2, ',', '.')}} <br><div style="color: red"> - {{ number_format($suma_haber - $suma_debe, 2, ',', '.')}}</div></td>
                                    @endif

                                @endif
                                    <td>
                                  </td>

                                </tr>

                        </tfoot>
                        </table>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="reference" class="col-md-2 col-form-label text-md-right"><i class="fa fa-circle" style="color: rgb(84, 196, 84)"><strong> Contabilizado</strong></i></label>
                        <label for="reference" class="col-md-3 col-form-label text-md-right"><i class="fa fa-circle" style="color: rgb(255, 94, 94)"><strong> No Contabilizado</strong></i></label>
                    </div>
                    @if (Auth::user()->role_id  == '1' || $actualizarmiddleware  == '1')
                    <a href="{{route('detailvouchers.contabilizar',[$coin ?? 'bolivares',$header->id ?? -1]) }}" id="btncontabilizar" name="btncontabilizar" class="btn btn-success" title="Contabilizar">Contabilizar</a>
                    @endif
                </div>
            </div>
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
            <form action="{{ route('detailvouchers.deletedetail') }}" method="post">
                @csrf
                @method('DELETE')
                <input id="id_detail_modal" type="hidden"  class="form-control @error('id_detail_modal') is-invalid @enderror" name="id_detail_modal" readonly required autocomplete="id_detail_modal">
                <input id="coin_modal" type="hidden" class="form-control @error('coin_modal') is-invalid @enderror" name="coin_modal" readonly required autocomplete="coin_modal">
                <input id="header_modal" type="hidden" class="form-control @error('header_modal') is-invalid @enderror" name="header_modal" readonly autocomplete="header_modal">

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

<!-- Delete Warning Modal -->
<div class="modal modal-danger fade" id="disableModal" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Deshabilitar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form action="{{ route('detailvouchers.delete') }}" method="post">
                @csrf
                @method('DELETE')
                <input id="id_detail_disable_modal" value="{{$header->id ?? null}}" type="hidden" class="form-control @error('id_detail_modal') is-invalid @enderror" name="id_detail_modal" readonly required autocomplete="id_detail_modal">

                <h5 class="text-center">Seguro que desea deshabilitar todos los movimientos contables?</h5>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger">Deshabilitar</button>
            </div>
            </form>
        </div>
    </div>
  </div>

  <!-- Delete Warning Modal -->
<div class="modal modal-danger fade" id="disableAfterModal" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Eliminar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form action="{{ route('detailvouchers.disable') }}" method="post">
                @csrf
                @method('DELETE')
                <input id="id_detail_disable_modal" value="{{$id_delete?? null}}" type="hidden" class="form-control @error('id_detail_modal') is-invalid @enderror" name="id_modal" readonly required autocomplete="id_detail_modal">
                <input id="type_modal" value="{{$type_delete ?? null}}" type="hidden" class="form-control @error('type_modal') is-invalid @enderror" name="type_modal" readonly required autocomplete="type_modal">
                <input id="id_header_modal" value="{{$header->id ?? null}}" type="hidden" class="form-control @error('id_header_modal') is-invalid @enderror" name="id_header_modal" readonly required autocomplete="id_header_modal">

                <h5 class="text-center">{{$message_delete ?? ''}}</h5>

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

@section('validacion')

<script>
    $(document).ready(function () {
        $("#code_one").mask('0000', { reverse: true });

    });
    $(document).ready(function () {
        $("#code_two").mask('0000', { reverse: true });

    });
    $(document).ready(function () {
        $("#code_three").mask('0000', { reverse: true });

    });
    $(document).ready(function () {
        $("#code_four").mask('0000', { reverse: true });

    });

    $(document).ready(function () {
        $("#debe").mask('000.000.000.000.000,00', { reverse: true });

    });
    $(document).ready(function () {
        $("#haber").mask('000.000.000.000.000,00', { reverse: true });

    });

    $(document).ready(function () {
        $("#reference").mask('000000000000000', { reverse: true });

    });

    if("{{isset($message_delete)}}"){
        showModalDelete();
    }

    function showModalDelete() {
        $('#disableAfterModal').modal('show');
    }

    function updateForm(){
        document.getElementById("headerForm").action =  "{{route('headervouchers.update')}}";
        document.getElementById("headerForm").submit();
    }


    $("#rate").on('change',function(){
        $("#tasa_comp").val($(this).val());

    });

    $(document).on('click','.delete',function(){

         let id_detail = $(this).attr('data-id-detail');

         $('#id_detail_modal').val(id_detail);

         let coin = $(this).attr('data-coin');

        $('#coin_modal').val(coin);

        let header = $(this).attr('data-header');

        $('#header_modal').val(header);
     });

</script>
    @if (isset($header))
        <script>
            $("#coin").on('change',function(){
                coin = $(this).val();
                window.location = "{{route('detailvouchers.create', ['',''])}}"+"/"+coin+"/{{ $header->id }}";
            });
        </script>
    @else
        <script>
            $("#coin").on('change',function(){
                coin = $(this).val();
                window.location = "{{route('detailvouchers.create', [''])}}"+"/"+coin;
            });
        </script>
    @endif

@endsection

@section('javascript')

    @if(bcdiv($suma_debe, '1', 2) != bcdiv($suma_haber, '1', 2))
    <script>

        btncontabilizar.style.pointerEvents = 'none';
        btncontabilizar.style.color = '#bbb';

    </script>

    @else
    <script>

            btncontabilizar.style.pointerEvents = null;

    </script>

    @endif
    @if (isset($saldo_total_bs))
        @if (($saldo_total_bs > 0))
            <script>
                document.getElementById("debe").value = "{{ number_format($saldo_total_bs, 2, ',', '.') }}";
            </script>
        @else
            <script>
                document.getElementById("haber").value = "{{ number_format($saldo_total_bs * -1, 2, ',', '.') }}";
            </script>
        @endif
    @endif
@endsection

@section('consulta')
    <script>
      /*  $('#dataTable').DataTable({
            //"ordering": true,
            //"order": [[2],[3],[0,'asc']],
            'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
        });*/



        function searchReference(){

            let reference_id = document.getElementById("reference").value;
            //var reference_id = $(this).val();
                $("#description").val("");
                $("#date_begin").val("");


               // getSubsegment(reference_id);

            $.ajax({

                url:"{{ route('detailvouchers.listheader') }}" + '/' + reference_id,
                beforSend:()=>{
                    alert('consultando datos');
                },
                success:(response)=>{
                 /*   let subsegment = $("#subsegment");
                    let htmlOptions = `<option value='' >Seleccione..</option>`;*/
                    var inputDescription = document.getElementById("description");
                    var inputDate = document.getElementById("date_begin");

                    // console.clear();
                    if(response.length > 0){
                        response.forEach((item, index, object)=>{
                            let {id,description,date} = item;

                           window.location = "{{route('detailvouchers.create', [$coin,''])}}"+"/"+id;
                           //inputDescription.value = description;
                           //inputDate.value = date;
                        });
                    }else{
                        alert('No se Encontro este numero de Referencia');
                    }
                    //console.clear();
                    // console.log(htmlOptions);
                    subsegment.html('');
                    subsegment.html(htmlOptions);



                },
                error:(xhr)=>{
                    alert('No se encuentra el numero de cabecera');
                }
            })
        }

    </script>
@endsection

