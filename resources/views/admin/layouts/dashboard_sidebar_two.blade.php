<ul  class="navbar-nav  bg-gradient-secondary2 sidebar sidebar-dark accordion" id="accordionSidebar">
    <!--style="width:200px !important;"-->
    <!-- Sidebar - Brand -->
     <!-- Divider -->
     <a class="sidebar-brand d-flex align-items-center" href="{{ route('home') }}">
        <img src="{{asset('img/logo.png')}}"  style="width: 80px;height:50px;">
    </a>
<hr class="sidebar-divider my-0">
     <!-- Divider -->
    <hr class="sidebar-divider">


    @foreach($arreglo as $modulos)
    <li class="nav-item">

        @if($modulos['padre'] == 0)

            @foreach($modulos['modulo'] as $modulo)

                @php
                    $data = explode(',', $modulo['ruta']);

                    if(isset($data[0]) && isset($data[1]) && isset($data[2])){

                        $ruta = route($data[0],$data[1],$data[2]);

                    }elseif(isset($data[0]) && isset($data[1])){

                        $ruta =  route($data[0],$data[1]);

                    }else{

                        $ruta =  route($modulo['ruta']);

                        }
                @endphp


            <a class="nav-link collapsed" href="{{$ruta}}" aria-expanded="true" aria-controls="collapse">
            <i class="{{$modulos['iconosis']}}" ></i>
            <span>{{$modulos['sistema']}} </span>
            </a>

            @endforeach
        @else

            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse{{$modulos['idsistema']}}"
            aria-expanded="true" aria-controls="collapse">
            <i class="{{$modulos['iconosis']}}" ></i>
            <span>{{$modulos['sistema']}} </span>
            </a>


    <div id="collapse{{$modulos['idsistema']}}" class="collapse" aria-labelledby="heading{{$modulos['idsistema']}}" data-parent="#accordionSidebar">

    <div class="bg-white py-2 collapse-inner rounded">
        @php
        /****contador para los subtitulos***/
        $listado = 0;
        $cuentas = 0;
        $contabilidad = 0;
        $otros = 0;
        @endphp
        @foreach($modulos['modulo'] as $modulo)

                        @php

                        $data = explode(',', $modulo['ruta']);

                        if(isset($data[0]) && isset($data[1]) && isset($data[2])){

                            $ruta = route($data[0],$data[1],$data[2]);

                        }elseif(isset($data[0]) && isset($data[1])){

                            $ruta =  route($data[0],$data[1]);
                        }else{

                            $ruta =  route($modulo['ruta']);

                        }


        /********************COLOCANDO SUBTITULOS EN REPORTES***************************/
        if($modulo['id_sistema'] == 10 AND $modulo['nro_orden'] > 0 && $modulo['nro_orden'] < 6){

                    if($listado == 0){

                        echo "<a  class='collapse-header text-danger'>Listados</a>";
                        $listado++;
                    }

        }


        if($modulo['id_sistema'] == 10 AND $modulo['nro_orden'] > 5 && $modulo['nro_orden'] < 17){


                    if($cuentas == 0){

                        echo "<a  class='collapse-header text-danger'>Cuentas</a>";
                        $cuentas++;
                    }

        }

        if($modulo['id_sistema'] == 10 AND $modulo['nro_orden'] > 16 && $modulo['nro_orden'] < 24){


                    if($contabilidad == 0){

                        echo "<a  class='collapse-header text-danger'>Contabilidad</a>";
                        $contabilidad++;
                    }

        }

        if($modulo['id_sistema'] == 10 AND $modulo['nro_orden'] > 23 && $modulo['nro_orden'] < 26){


                    if($otros == 0){

                        echo "<a  class='collapse-header text-danger'>otros</a>";
                        $otros++;
                    }

        }

        /*******************************Cambiando un nombre de menur de sucursales a condominio*************************************/
                if($modulo['id_sistema'] == 1 AND Auth::user()->id_company  == '16' AND $modulo['modulo'] == 'Sucursales'){
                    $nombremodulo = 'Condominios';
                }else{
                    $nombremodulo = $modulo['modulo'];
                }
                        @endphp
        <a class="collapse-item"  href="{{$ruta}}" > <i class="{{$modulo['icono_modulo']}} mr-2 text-blue-400"></i><strong>{{$nombremodulo}}</strong></a>
        @endforeach

                </div>
       </div>
       @endif
    </li>
    @endforeach



<!-- <li class="nav-item">
    <a class="nav-link" href="#">
        <i class="fas fa-fw fa-wrench"></i>
        <span>Developer</span></a>
</li> -->


    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

    <!-- Sidebar Message -->


</ul>
