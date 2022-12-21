<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Arya Software 
      
    </title>

    @yield('header')

    <!-- Custom fonts for this template INDEX-->
    <link href="{{asset('vendor/sb-admin/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="{{asset('vendor/sb-admin/css/sb-admin-2.css')}}" rel="stylesheet">
    <!--End INDEX-->

    <link href="{{asset('vendor/sb-admin/css/carlos.css')}}" rel="stylesheet">

    <link href="{{asset('css/watch.css')}}" rel="stylesheet">
      <!-- Custom fonts for this template TABLES-->

    <link href="{{asset('vendor/sb-admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
    <!--End TABLES-->

</head>

<body id="page-top">
    <body onload="startTime()">
    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        @include('admin.layouts.dashboard_sidebar_two')
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content" >

                <!-- Topbar -->
                @include('admin.layouts.dashboard_topbar')
                <!-- End of Topbar -->
               
                <!-- Begin Page Content -->

                @yield('content')

                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->

            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel"
     aria-hidden="true">
     <div class="modal-dialog" role="document">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="logoutModalLabel">Seguro que desea Cerrar Sesión?</h5>
                 <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">×</span>
                 </button>
             </div>
             <div class="modal-body">Seleccione "Cerrar Sesión" si desea salir de Arya Software</div>
             <div class="modal-footer">
                 <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                 <a class="btn btn-primary" href="{{ route('logout') }}"onclick="event.preventDefault();
                 document.getElementById('logout-form').submit();">
                 Cerrar Sesión 
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                  @csrf
               </form>
             </div>
         </div>
    </div>
 </div>

 
   <!-- END SCRIPTS INDEX -->
        <!-- Bootstrap core JavaScript-->
        <script src="{{asset('vendor/sb-admin/vendor/jquery/jquery.min.js')}}"></script>
        <script src="{{asset('vendor/sb-admin/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

        <!-- Core plugin JavaScript-->
        <script src="{{asset('vendor/sb-admin/vendor/jquery-easing/jquery.easing.min.js')}}"></script>

        <!-- Custom scripts for all pages-->
        <script src="{{asset('vendor/sb-admin/js/sb-admin-2.min.js')}}"></script>

        <!-- Page level plugins -->
        <script src="{{asset('vendor/sb-admin/vendor/chart.js/Chart.min.js')}}"></script>
    <!-- END SCRIPTS INDEX -->

     <!-- SCRIPTS FOR TABLES-->
        <!-- Page level plugins -->
        <script src="{{asset('vendor/sb-admin/vendor/datatables/jquery.dataTables.min.js')}}"></script>
        <script src="{{asset('vendor/sb-admin/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>

        <!-- Page level custom scripts -->
        <script src="{{asset('vendor/sb-admin/js/demo/datatables-demo.js')}}"></script>
    <!-- END SCRIPTS FOR TABLES -->

        <script src="{{asset('js/formulario.js')}}"></script>

        <!-- Para las mascaras -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js" integrity="sha512-pHVGpX7F/27yZ0ISY+VVjyULApbDlD0/X0rgGbTqCE7WFW5MezNTWG/dnhtbBuICzsd0WQPgpE4REBLv+UqChw==" crossorigin="anonymous"></script>
      

        <script>
          $("body").toggleClass("sidebar-toggled");
              $(".sidebar").toggleClass("toggled");
              if ($(".sidebar").hasClass("toggled")) {
              $('.sidebar .collapse').collapse('hide');
          };
          /* suspender
          id_company = $("#id_company").val();
          if (id_company == '1') {
          window.location.href = "{{ route('suspended')}}";
          }*/

      </script>


  @yield('piechart')
  @yield('javascript1')
  @yield('javascript2')
  @yield('javascript')
  @yield('validacionbtn')
  @yield('validacionbtn2')
  @yield('consulta')
  @yield('javascript_edit')
  @yield('js_charts')

  @yield('consultadeposito')

  @yield('validacion_usuario')



  @yield('validacion')
  @yield('validacionExpense')

  @yield('validacion_time')
  @yield('product_edit')
  @yield('quotation_create')
  @yield('quotation_facturar')
  @yield('quotation_facturar_after')
  @yield('validacion_transport')
  @yield('validacion_vendor')
  @yield('javascript_iva_payment')
  @yield('imports')
  

  <script>
    function soloNumeros(idCampo){
    $('#'+idCampo).keyup(function (){
          this.value = (this.value + '').replace(/[^0-9]/g, '');
      });
    }
  </script>
  <script>
    function soloLetras(idCampo){
    $('#'+idCampo).keyup(function (){
          this.value = (this.value + '').replace(/[^a-zA-Z\s]/g, '');
      });
    }
  </script>
    <script>
        function soloAlfaNumerico(idCampo){
        $('#'+idCampo).keyup(function (){
              this.value = (this.value + '').replace(/[^a-zA-Z0-9\s]/g, '');
          });
        }
      </script>
      <script>
        function soloNumeroPunto(idCampo){
        $('#'+idCampo).keyup(function (){
              this.value = (this.value + '').replace(/[^0-9.]/g, '');
          });
        }
      </script>
</body>

</html>
