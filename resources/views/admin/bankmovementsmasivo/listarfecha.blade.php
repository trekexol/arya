@if($movimientosmasivos)
<option value="">Seleccione Fecha..</option>
@foreach($movimientosmasivos as $movimientosmasivos)
<option value="{{ $movimientosmasivos->fecha }}">{{ $movimientosmasivos->fecha }}</option>
@endforeach
@endif
