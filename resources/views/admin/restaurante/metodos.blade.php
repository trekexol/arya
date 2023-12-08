<div class="container campos{{ $numero }}">
<div class="form-row">
    <div class="col-3">
        <small>
            <select onchange="cambio(this,{{ $numero }})" id="tipopago"  name="tipopago[]" class="form-control form-control-sm tipopago">
                <option selected="" value="">Forma de Pago</option>
                <option value="1">Cheque</option>
                <option value="2">Contado</option>
                <option value="5">Depósito Bancario</option>
                <option value="6">Efectivo</option>
                <option value="9">Tarjeta de Crédito</option>
                <option value="10">Tarjeta de Débito</option>
                <option value="11">Transferencia</option>
            </select>
        </small>
    </div>
    <div class="col-3 bank{{$numero}}" style="display: none">
        <small>
            <select name="banco[]" class="form-control form-control-sm">
                <option selected="" value="">Seleccione..</option>
                  @foreach ($accounts_bank as $accounts_bank)
                    <option value="{{ $accounts_bank->id }}">{{ $accounts_bank->description }}</option>
                  @endforeach
            </select>
        </small>
    </div>
    <div class="col-3 check{{$numero}}" style="display: none">
        <small>
            <select name="caja[]" class="form-control form-control-sm">
                <option selected="" value="">Seleccione..</option>
                  @foreach ($accounts_efectivo as $accounts_efectivo)
                    <option value="{{ $accounts_efectivo->id }}">{{ $accounts_efectivo->description }}</option>
                  @endforeach
            </select>
        </small>
    </div>
    <div class="col-3 referencia{{$numero}}" style="display: none">
        <small>
            <input type="text" name="referencia[]" class="form-control form-control-sm" placeholder="REFERENCIA" value="">
        </small>
    </div>
    <div class="col-3">
        <small>
            <input type="text" name="monto[]" class="form-control form-control-sm monto" value="{{number_format($monto, 2)}}">
        </small>
    </div>
</div>
<br>
<script>
    $(".monto").mask('000000000000000.00', { reverse: true });
function cambio(select,numero) {

    var valor = select.value;
            if(valor == 1 || valor == 5 || valor == 11){
                $(".bank"+numero).show();
                $(".check"+numero).hide();
                $(".referencia"+numero).show();

                console.log("valor "+ valor);
                console.log("numero "+ numero);
            }
            else  if(valor == 6){
                $(".bank"+numero).hide();
                $(".check"+numero).show();
                $(".referencia"+numero).hide();

            }else{
                $(".bank"+numero).hide();
                $(".check"+numero).hide();
                $(".referencia"+numero).hide();
            }


}

</script>
</div>
