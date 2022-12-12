
@if($movimientosmasivos->count() > 0)
<option value="">Seleccione Fecha..</option>
@foreach($movimientosmasivos as $movimientosmasivos)
<option value="{{ $movimientosmasivos->fecha }}">{{ $movimientosmasivos->fecha }}</option>
@endforeach
<input type="hidden" name="coin" value="{{$movimientosmasivos->moneda}}">
@else
<option value="">Seleccione Fecha..</option>
@endif
