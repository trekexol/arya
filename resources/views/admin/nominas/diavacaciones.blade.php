@php

    $feini = new DateTime($fechaex[0].'-'.$fechaex[1]);
    $fefin = new DateTime($fechaexplode[0].'-'.$fechaexplode[1]);

    $diferencia = $feini->diff($fefin);
    $años = $diferencia->format('%Y');

    $diasdevaca = $años + 14;
@endphp

{{ $diasdevaca }}
