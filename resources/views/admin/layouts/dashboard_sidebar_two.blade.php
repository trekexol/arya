<ul  class="navbar-nav  bg-gradient-secondary2 sidebar sidebar-dark accordion" id="accordionSidebar">
    <!--style="width:200px !important;"-->
    <!-- Sidebar - Brand -->
     <!-- Divider -->
     <hr class="sidebar-divider my-0">


     <!-- Divider -->
     <hr class="sidebar-divider">


   

    <li class="nav-item">
  
    @foreach($arreglo as $modulos)

      
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


                <a class="nav-link collapsed" href="{{$ruta}}"
                aria-expanded="true" aria-controls="collapse">
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
        <a class="collapse-item" href="{{$ruta}}" > <i class="{{$modulo['icono_modulo']}} mr-2 text-blue-400"></i><strong>{{$modulo['modulo']}}</strong></a>
        @endforeach
                    
                </div>
       </div>
       @endif
    @endforeach

   
</li>

    
       

            
            

    

    

    

 
 








  


    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

    <!-- Sidebar Message -->


</ul>