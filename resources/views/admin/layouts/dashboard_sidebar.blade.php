<ul  class="navbar-nav  bg-gradient-secondary2 sidebar sidebar-dark accordion" id="accordionSidebar">
    <!--style="width:200px !important;"-->
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center" href="{{ route('home') }}">

            <div class="sidebar-brand-text float-left">
                <img src="{{asset('img/logo.png')}}"  style="width: 100px;height:50px;" alt="Google">

            </div>

    </a>
        <!-- first is the link in your navbar -->

        <button type="button" class="btn btn-secondary rounded-sm m-2 pb-2" id="servicesDropdown" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false">Acceso Rápido</button>

        <!-- your mega menu starts here! -->
    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="servicesDropdown">

        <!-- finally, using flex to create your layout -->
        <div class="d-md-flex align-items-start justify-content-start">

            <div>
            <div class="dropdown-header text-dark font-weight-bold">Facturación</div>
                <a class="dropdown-item" href="{{ route('quotations.createquotation') }}">Nueva Cotización</a>
                <a class="dropdown-item" href="{{ route('clients.create') }}">Nuevo Cliente</a>
                <a class="dropdown-item" href="{{ route('vendors.create') }}">Nuevo Vendedor</a>
            </div>

            <div>
            <div class="dropdown-header text-dark font-weight-bold">Gastos o Compras</div>
                <a class="dropdown-item" href="{{ route('expensesandpurchases.create') }}">Registrar Compra</a>
                <a class="dropdown-item" href="{{ route('providers.create') }}">Nuevo Proveedor</a>
                <a class="dropdown-item" href="{{ route('directpaymentorders.create') }}">Registrar Orden de Pago</a>
                <a class="dropdown-item" href="{{ route('products.create') }}">Nuevo Producto</a>
            </div>
            @if (Auth::user()->role_id  == '1')
            <div>
            <div class="dropdown-header text-dark font-weight-bold">Nóminas</div>
                <a class="dropdown-item" href="{{ route('nominas.create') }}">Nueva Nómina</a>
                <a class="dropdown-item" href="{{ route('nominaconcepts.create') }}">Registrar Concepto de Nómina</a>
                <a class="dropdown-item" href="{{ route('employees.create') }}">Nuevo Empleado</a>
            </div>
            <div>
            <div class="dropdown-header text-dark font-weight-bold">Transación</div>
                <a class="dropdown-item" href="{{ route('bankmovements') }}">Registrar Depósito</a>
                <a class="dropdown-item" href="{{ route('bankmovements') }}">Registrar Retiro</a>
                <a class="dropdown-item" href="{{ route('bankmovements') }}">Registrar Transferencia</a>
            </div>
            @endif

        </div>
    </div>



    <!-- Divider -->
    <hr class="sidebar-divider my-0">


    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading
    <div class="sidebar-heading">
        Interface
    </div>-->
@if ((Auth::user()->role_id  == '1'))

<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAdminitracion"
        aria-expanded="true" aria-controls="collapseAdminitracion">
        <i class="fas fa-fw fa-user" ></i>
        <span>Administración</span>
    </a>
    <div id="collapseAdminitracion" class="collapse" aria-labelledby="headingAdminitracion" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">

            
            <a class="collapse-item" href="{{ route('users')}}" > <i class="fas fa-user fa-sm fa-fw mr-2 text-blue-400"></i><strong>Usuarios</strong></strong></a>
            
           
            <a class="collapse-item" href="{{ route('branches')}}" > <i class="fas fa-code-branch fa-sm fa-fw mr-2 text-blue-400"></i><strong>Sucursales</strong></a>
            @if ((Auth::user()->id_company  != '16'))
            <a class="collapse-item" href="{{ route('positions')}}" > <i class="fas fa-user-plus fa-sm fa-fw mr-2 text-blue-400"></i><strong>Cargos</strong></a>
            <a class="collapse-item" href="{{ route('academiclevels')}}" > <i class="fas fa-graduation-cap fa-sm fa-fw mr-2 text-blue-400"></i><strong>Niveles Académicos</strong></a>
            <a class="collapse-item" href="{{ route('professions') }}" > <i class="fas fa-user-tie fa-sm fa-fw mr-2 text-blue-400"></i><strong>Profesiones</strong></a>
            <a class="collapse-item" href="{{ route('salarytypes') }}" > <i class="fas fa-business-time fa-sm fa-fw mr-2 text-blue-400"></i><strong>Tipos de Salarios</strong></a>
            <a class="collapse-item" href="{{ route('nominatypes') }}" > <i class="fas fa-book fa-sm fa-fw mr-2 text-blue-400"></i><strong>Tipos de Nóminas</strong></a>
            <a class="collapse-item" href="{{ route('comisiontypes') }}" > <i class="fas fa-address-card fa-sm fa-fw mr-2 text-blue-400"></i><strong>Tipos de Comisión</strong></a>
            <a class="collapse-item" href="{{ route('paymenttypes') }}" > <i class="fas fa-credit-card fa-sm fa-fw mr-2 text-blue-400"></i><strong>Tipos de Pagos</strong></a>
            <a class="collapse-item" href="{{ route('segments') }}" > <i class="fas fa-cog fa-sm fa-fw mr-2 text-blue-400"></i><strong>Segmentos</strong></a>
            <a class="collapse-item" href="{{ route('subsegment') }}" > <i class="fas fa-cogs fa-sm fa-fw mr-2 text-blue-400"></i><strong>Sub Segmentos</strong></a>
            <a class="collapse-item" href="{{ route('twosubsegments') }}" > <i class="fas fa-cogs fa-sm fa-fw mr-2 text-blue-400"></i><strong>Segundo <br><div style="text-indent: 22px;">Sub Segmento</div></strong></a>
            <a class="collapse-item" href="{{ route('threesubsegments') }}" > <i class="fas fa-cogs fa-sm fa-fw mr-2 text-blue-400"></i><strong>Tercer<br><div style="text-indent: 22px;">Sub Segmento</div></strong></a>
            <a class="collapse-item" href="{{ route('unitofmeasures') }}" > <i class="fas fa-balance-scale fa-sm fa-fw mr-2 text-blue-400"></i><strong>Unidades de Medida</strong></a>
            <a class="collapse-item" href="{{ route('receiptvacations') }}" > <i class="fas fa-plane-departure fa-sm fa-fw mr-2 text-blue-400"></i><strong>Recibo de Vacaciones</strong></a>
            <a class="collapse-item" href="{{ route('modelos') }}" > <i class="fas fa-clipboard-list fa-sm fa-fw mr-2 text-blue-400"></i><strong>Modelos</strong></a>
            <a class="collapse-item" href="{{ route('colors') }}" > <i class="fas fa-palette fa-sm fa-fw mr-2 text-blue-400"></i><strong>Colores</strong></a>
            <a class="collapse-item" href="{{ route('transports') }}" > <i class="fas fa-car-side fa-sm fa-fw mr-2 text-blue-400"></i><strong>Transportes</strong></a>
            <a class="collapse-item" href="{{ route('historictransports') }}" > <i class="fas fa-archive fa-sm fa-fw mr-2 text-blue-400"></i><strong>Historial de<br> <div style="text-indent: 22px;">Transporte</div></strong></a>
            <a class="collapse-item" href="{{ route('tasas') }}" > <i class="fas fa-dollar-sign fa-sm fa-fw mr-2 text-blue-400"></i><strong>Tasa del Día</strong></a>
            <a class="collapse-item" href="{{ route('inventarytypes') }}" > <i class="fas fa-boxes fa-sm fa-fw mr-2 text-blue-400"></i><strong>Tipos de<br> <div style="text-indent: 22px;">Inventario</div></strong></a>
            <a class="collapse-item" href="{{ route('ratetypes') }}" > <i class="fas fa-donate fa-sm fa-fw mr-2 text-blue-400"></i><strong>Tipos de Tasas</strong></a>
            <a class="collapse-item" href="{{ route('nominaformulas') }}" > <i class="fas fa-calculator fa-sm fa-fw mr-2 text-blue-400"></i><strong>Formulas de Nómina</strong></a>
            @endif
        </div>
    </div>
</li>

@endif

@if (Auth::user()->id_company  == '16' & (Auth::user()->role_id  == '1'))
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapgastoscon"
            aria-expanded="true" aria-controls="collapgastoscon">
            <i class="fas fa-fw fa-file-alt" ></i>
            <span>Gastos de Condominio</span>
        </a>
        <div id="collapgastoscon" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{route('receipt')}}" > <i class="fas fa-file-alt fa-sm fa-fw mr-2 text-blue-400"></i><strong>Relación Gastos de Condominio</strong></a>
                <a class="collapse-item" href="{{route('receiptr')}}" > <i class="fas fa-dollar-sign fa-sm fa-fw mr-2 text-black-400"></i><strong>Recibos de Condominio</strong></a>
               <!-- <a class="collapse-item" href="{{''/*route('receiptr')*/}}" > <i class="fas fa-coins fa-sm fa-fw mr-2 text-blue-400"></i><strong>Cobros de Recibos</strong></a>-->
                <a class="collapse-item" href="{{route('condominiums')}}" ><i class="fas fa-user fa-sm fa-fw mr-2 text-blue-400"></i><strong>Condominios</strong></a>
                <a class="collapse-item" href="{{route('owners')}}" ><i class="fas fa-user fa-sm fa-fw mr-2 text-blue-400"></i><strong>Propietarios</strong></a>
<!--<a class="collapse-item" href="{{''/*route('receiptr')*/}}" ><i class="fas fa-hand-holding-usd fa-sm fa-fw mr-2 text-blue-400"></i><strong>Anticipos/Abonos Propietarios</strong></a>-->
                <a class="collapse-item" href="{{ route('products')}}" ><i class="fab fa-product-hunt fa-sm fa-fw mr-2 text-black-400"></i><strong>Productos y Servicios</strong></a>
                <a class="collapse-item" href="{{ route('receipt.accounts_receivable','index')}}" > <i class="fas fa-coins fa-sm fa-fw mr-2 text-blue-400"></i><strong>Resumen y Estados de Cuentas</strong></a>
            </div>
        </div>
    </li>
    @endif

    @if ((Auth::user()->id_company  == '16') & (Auth::user()->role_id  == '11'))
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapgastoscon"
            aria-expanded="true" aria-controls="collapgastoscon">
            <i class="fas fa-fw fa-file-alt" ></i>
            <span>Gastos de Condominio</span>
        </a>
        <div id="collapgastoscon" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{route('receiptr')}}" > <i class="fas fa-dollar-sign fa-sm fa-fw mr-2 text-black-400"></i><strong>Recibos de Condominio</strong></a>
                <!-- <a class="collapse-item" href="{{''/*route('receiptr')*/}}" > <i class="fas fa-coins fa-sm fa-fw mr-2 text-blue-400"></i><strong>Cobros de Recibos</strong></a>-->
                <!--<a class="collapse-item" href="{{''/*route('receiptr')*/}}" ><i class="fas fa-hand-holding-usd fa-sm fa-fw mr-2 text-blue-400"></i><strong>Anticipos/Abonos Propietarios</strong></a>-->
                <a class="collapse-item" href="{{route('receiptr')}}" > <i class="fas fa-coins fa-sm fa-fw mr-2 text-blue-400"></i><strong>Resumen y estados de cuentas</strong></a>
            </div>
        </div>
    </li>
    @endif


@if ((Auth::user()->role_id  == '1') || (Auth::user()->role_id  == '2') || (Auth::user()->role_id  == '3'))
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
            aria-expanded="true" aria-controls="collapseTwo">
            <i class="fas fa-fw fa-file-alt" ></i>
            <span>Facturación</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">

                <a class="collapse-item" href="{{route('quotations')}}" > <i class="fas fa-pencil-alt fa-sm fa-fw mr-2 text-blue-400"></i><strong>Cotizaciones</strong></a>
                <a class="collapse-item" href="{{route('invoices')}}" > <i class="fas fa-file-alt fa-sm fa-fw mr-2 text-blue-400"></i><strong>Facturas</strong></a>
                <a class="collapse-item" href="{{route('quotations.indexdeliverynote')}}" > <i class="fas fa-sort-amount-up-alt fa-sm fa-fw mr-2 text-blue-400"></i><strong>Notas de Entrega</strong></a>
                <a class="collapse-item" href="{{route('orders.index')}}" > <i class="fab fa-product-hunt fa-sm fa-fw mr-2 text-blue-400"></i><strong>Pedidos</strong></a>
                <a class="collapse-item" href="{{route('creditnotes')}}" > <i class="fas fa-credit-card fa-sm fa-fw mr-2 text-blue-400"></i><strong>Notas de Crédito</strong></a>
                <a class="collapse-item" href="{{route('payments')}}" > <i class="fas fa-coins fa-sm fa-fw mr-2 text-blue-400"></i><strong>Cobros</strong></a>
                <a class="collapse-item" href="{{route('directchargeorders.create')}}" > <i class="fas fa-dollar-sign fa-sm fa-fw mr-2 text-black-400"></i><strong>Orden de Cobro</strong></a>
                <a class="collapse-item" href="{{route('receipt')}}" > <i class="fas fa-dollar-sign fa-sm fa-fw mr-2 text-black-400"></i><strong>Recibos de Cobro</strong></a>
                <a class="collapse-item" href="{{route('clients')}}" ><i class="fas fa-user fa-sm fa-fw mr-2 text-blue-400"></i><strong>Clientes</strong></a>
                <a class="collapse-item" href="{{route('vendors')}}" ><i class="fas fa-user fa-sm fa-fw mr-2 text-blue-400"></i><strong>Vendedores</strong></a>
                <a class="collapse-item" href="{{route('sales')}}" ><i class="fas fa-dollar-sign fa-sm fa-fw mr-2 text-blue-400"></i><strong>Ventas</strong></a>
                <a class="collapse-item" href="{{route('anticipos')}}" ><i class="fas fa-hand-holding-usd fa-sm fa-fw mr-2 text-blue-400"></i><strong>Anticipos Clientes</strong></a>
            </div>
        </div>
    </li>
    @endif
    @if ((Auth::user()->role_id  == '1') || (Auth::user()->role_id  == '2')  || (Auth::user()->role_id  == '3'))
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseGastos"
            aria-expanded="true" aria-controls="collapseGastos">
            <i class="fas fa-fw fa-address-book" ></i>
            <span>Gastos y Compras</span>
        </a>
        <div id="collapseGastos" class="collapse" aria-labelledby="headingGastos" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">

             <!-- <a  class="collapse-header" href="buttons.html">Gastos y Compras</a> -->
                <a class="collapse-item" href="{{ route('expensesandpurchases')}}" > <i class="fas fa-file-invoice-dollar fa-sm fa-fw mr-2 text-black-400"></i><strong>Gastos y Compras</strong></a>
                <a class="collapse-item" href="{{ route('providers')}}" > <i class="fas fa-user fa-sm fa-fw mr-2 text-black-400"></i><strong>Proveedores</strong></a>
                <a class="collapse-item" href="{{ route('bankmovements.indexorderpayment')}}" > <i class="fas fa-dollar-sign fa-sm fa-fw mr-2 text-black-400"></i><strong>Ordenes de Pago</strong></a>
                <a class="collapse-item" href="{{ route('products')}}" ><i class="fab fa-product-hunt fa-sm fa-fw mr-2 text-black-400"></i><strong>Productos y Servicios</strong></a>
                <a class="collapse-item" href="{{ route('combos')}}" ><i class="fas fa-boxes fa-sm fa-fw mr-2 text-black-400"></i><strong>Combos</strong></a>
                <a class="collapse-item" href="{{ route('anticipos.index_provider')}}" > <i class="fas fa-hand-holding-usd fa-sm fa-fw mr-2 text-black-400"></i><strong>Anticipos<br><div style="text-indent: 22px;">a Proveedores</div></strong></a>
            </div>
        </div>
    </li>
    @endif
    @if (Auth::user()->role_id  == '1')
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseNomina"
            aria-expanded="true" aria-controls="collapseNomina">
            <i class="fas fa-fw fa-user-check" ></i>
            <span>Nómina</span>
        </a>
        <div id="collapseNomina" class="collapse" aria-labelledby="collapseNomina" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('nominas') }}" > <i class="fas fa-book fa-sm fa-fw mr-2 text-black-400"></i><strong>Nóminas</strong></a>
                <a class="collapse-item" href="{{ route('nominaconcepts') }}" > <i class="fas fa-book fa-sm fa-fw mr-2 text-black-400"></i><strong>Concepto de Nóminas</strong></a>
                <a class="collapse-item" href="{{ route('employees') }}" > <i class="fas fa-users fa-sm fa-fw mr-2 text-black-400"></i><strong>Empleados</strong></a>
                <a class="collapse-item" href="{{ route('indexbcvs') }}" > <i class="fas fa-money-bill fa-sm fa-fw mr-2 text-black-400"></i><strong>Indices BCV</strong></a>
            </div>
        </div>
    </li>
    @endif
    @if (Auth::user()->role_id  == '1')
    <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTransaccion"
                aria-expanded="true" aria-controls="collapseTransaccion">
                <i class="fas fa-fw fa-credit-card" ></i>
                <span>Transacción</span>
            </a>
            <div id="collapseTransaccion" class="collapse" aria-labelledby="headingTransaccion" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                <a  class="collapse-item" href="{{ route('bankmovements') }}" > <i class="fas fa-coins fa-sm fa-fw mr-2 text-black-400"></i><strong>Bancos</strong></a>
                <a  class="collapse-item" href="{{ route('bankmovements.indexmovement') }}" > <i class="fas fa-hand-holding-usd fa-sm fa-fw mr-2 text-black-400"></i><strong>Movimientos <br> <div style="text-indent: 22px;"> Bancarios</div></strong></a>
            </div>
        </div>
    </li>
    @endif
    @if (Auth::user()->role_id  == '1')
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseContabilidad"
                aria-expanded="true" aria-controls="collapseContabilidad">
                <i class="fas fa-fw fa-book" ></i>
                <span>Contabilidad</span>
            </a>
            <div id="collapseContabilidad" class="collapse" aria-labelledby="headingContabilidad" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="{{ route('accounts')}}" > <i class="fas fa-coins fa-sm fa-fw mr-2 text-black-400"></i><strong>Plan de Cuentas</strong></a>
                    <a class="collapse-item" href="{{ route('detailvouchers.create','bolivares')}}" > <i class="fas fa-cogs fa-sm fa-fw mr-2 text-black-400"></i><strong>Ajustes Contables</strong></a>
                    <a class="collapse-item" href="{{ route('accounting_adjustments.index')}}" > <i class="fas fa-archive fa-sm fa-fw mr-2 text-black-400"></i><strong>Histórico de <br><div style="text-indent: 22px;">Ajustes Contables</div></strong></a>
                    <a class="collapse-item" href="{{ route('balancegenerals') }}" > <i class="fas fa-clipboard-check fa-sm fa-fw mr-2 text-blue-400"></i><strong>Balance General</strong></a>
                    <a class="collapse-item" href="{{ route('balanceingresos') }}" > <i class="fas fa-compress-alt fa-sm fa-fw mr-2 text-blue-400"></i><strong>Ingresos y Egresos</strong></a>
                    <a class="collapse-item" href="{{ route('daily_listing') }}" > <i class="fas fa-book-reader fa-sm fa-fw mr-2 text-blue-400"></i><strong>Listado Diario</strong></a>
                </div>
            </div>
        </li>
    @endif
    @if (Auth::user()->role_id  == '2' || Auth::user()->role_id  == '3') <!-- reportes limitados -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReportes"
            aria-expanded="true" aria-controls="collapseReportes">
            <i class="fas fa-fw fa-book" ></i>
            <span>Reportes</span>
        </a>
        <div id="collapseReportes" class="collapse" aria-labelledby="headingReportes" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">

                <a  class="collapse-header text-danger" href="{{ route('accounts') }}">Contabilidad</a>      
                <a class="collapse-item" href="{{ route('reports.accounts_receivable','index')}}" > <i class="fas fa-hand-holding-usd fa-sm fa-fw mr-2 text-black-400"></i><strong>Cuentas por Cobrar</strong></a>
                <a class="collapse-item" href="{{ route('reports.debtstopay')}}" > <i class="fas fa-coins fa-sm fa-fw mr-2 text-black-400"></i><strong>Cuentas por Pagar</strong></a>
                <a class="collapse-item" href="{{ route('reports.sales_books') }}" > <i class="fas fa-book fa-sm fa-fw mr-2 text-blue-400"></i><strong>Libro de Ventas</strong></a>

        </div>
    </li>
    @endif
    @if ((Auth::user()->role_id  == '1') || (Auth::user()->role_id  == '10'))
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseImpuestos"
            aria-expanded="true" aria-controls="collapseImpuestos">
            <i class="fas fa-fw fa-book" ></i>
            <span>Impuestos</span>
        </a>
        <div id="collapseImpuestos" class="collapse" aria-labelledby="headingImpuestos" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">

            <a class="collapse-item" href="{{ route('taxes.iva_paymentindex')}}" > <i class="fas fa-coins fa-sm fa-fw mr-2 text-black-400"></i><strong>Pago de Iva</strong></a>
            <a class="collapse-item" href="{{ route('taxes.iva_retenido_payment')}}" > <i class="fas fa-coins fa-sm fa-fw mr-2 text-black-400"></i><strong>Pago de Iva<br><div style="text-indent: 22px;">Retenido Terceros</div></strong></a>

            </div>
        </div>
    </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReportes"
                aria-expanded="true" aria-controls="collapseReportes">
                <i class="fas fa-fw fa-book" ></i>
                <span>Reportes</span>
            </a>
            <div id="collapseReportes" class="collapse" aria-labelledby="headingReportes" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a  class="collapse-header text-danger" href="#">Listados</a>
                    <a class="collapse-item" href="{{ route('reports.clients')}}" > <i class="fas fa-user fa-sm fa-fw mr-2 text-black-400"></i><strong>Clientes</strong></a>
                    <a class="collapse-item" href="{{ route('reports.providers')}}" > <i class="fas fa-user fa-sm fa-fw mr-2 text-black-400"></i><strong>Proveedores</strong></a>
                    <a class="collapse-item" href="{{ route('reports.employees')}}" > <i class="fas fa-users fa-sm fa-fw mr-2 text-black-400"></i><strong>Empleados</strong></a>

                    <a  class="collapse-header text-danger" href="{{ route('accounts') }}">Cuentas</a>
                    <a class="collapse-item" href="{{ route('reports.accounts')}}" > <i class="fas fa-list-ul fa-sm fa-fw mr-2 text-black-400"></i><strong>Listado de Cuentas</strong></a>
                    <a class="collapse-item" href="{{ route('reports.accounts_receivable','index')}}" > <i class="fas fa-hand-holding-usd fa-sm fa-fw mr-2 text-black-400"></i><strong>Cuentas por Cobrar</strong></a>
                    <a class="collapse-item" href="{{ route('reports.debtstopay')}}" > <i class="fas fa-coins fa-sm fa-fw mr-2 text-black-400"></i><strong>Cuentas por Pagar</strong></a>
                    <a class="collapse-item" href="{{ route('reports.accounts_receivable_note','todo','todo')}}" > <i class="fas fa-coins fa-sm fa-fw mr-2 text-black-400"></i><strong>Notas de Entrega</strong></a>
                    <a class="collapse-item" href="{{ route('reports.accounts_receivable_note_det','todo','todo')}}" > <i class="fas fa-coins fa-sm fa-fw mr-2 text-black-400"></i><strong>Notas de Entrega Det</strong></a>
                    <a class="collapse-item" href="{{ route('reports.bankmovements')}}" > <i class="fas fa-money-bill-wave fa-sm fa-fw mr-2 text-black-400"></i><strong>Movimientos<br> <div style="text-indent: 22px;">Bancarios</div></strong></a>
                    <a class="collapse-item" href="{{ route('reportspayment.payments','index')}}" > <i class="fas fa-hand-holding-usd fa-sm fa-fw mr-2 text-black-400"></i><strong>Pagos Realizados</strong></a>
                    
                    <a  class="collapse-header text-danger" href="{{ route('accounts') }}">Contabilidad</a>
                    <a class="collapse-item" href="{{ route('balancegenerals') }}" > <i class="fas fa-clipboard-check fa-sm fa-fw mr-2 text-blue-400"></i><strong>Balance General</strong></a>
                    <a class="collapse-item" href="{{ route('balanceingresos') }}" > <i class="fas fa-compress-alt fa-sm fa-fw mr-2 text-blue-400"></i><strong>Ingresos y Egresos</strong></a>

                    <a class="collapse-item" href="{{ route('daily_listing') }}" > <i class="fas fa-book-reader fa-sm fa-fw mr-2 text-blue-400"></i><strong>Listado Diario</strong></a>                            
                    <a class="collapse-item" href="{{ route('reports.sales_books') }}" > <i class="fas fa-book fa-sm fa-fw mr-2 text-blue-400"></i><strong>Libro de Ventas</strong></a> 
                    <a class="collapse-item" href="{{ route('reports.purchases_book') }}" > <i class="fas fa-bookmark fa-sm fa-fw mr-2 text-blue-400"></i><strong>Libro de Compras</strong></a>                       
                    <a class="collapse-item" href="{{ route('reports.operating_margin') }}" > <i class="fas fa-chart-bar fa-sm fa-fw mr-2 text-blue-400"></i><strong>Margen Operativo</strong></a>                       
                    <a class="collapse-item" href="{{ route('inventories') }}" > <i class="fas fa-boxes fa-sm fa-fw mr-2 text-blue-400"></i><strong>Inventario</strong></a>                         
                    
                    <a  class="collapse-header text-danger" href="#">Otros</a> 

                    <a class="collapse-item" href="{{ route('daily_listing') }}" > <i class="fas fa-book-reader fa-sm fa-fw mr-2 text-blue-400"></i><strong>Listado Diario</strong></a>
                    <a class="collapse-item" href="{{ route('reports.sales_books') }}" > <i class="fas fa-book fa-sm fa-fw mr-2 text-blue-400"></i><strong>Libro de Ventas</strong></a>
                    <a class="collapse-item" href="{{ route('reports.purchases_book') }}" > <i class="fas fa-bookmark fa-sm fa-fw mr-2 text-blue-400"></i><strong>Libro de Compras</strong></a>
                    <a class="collapse-item" href="{{ route('reports.operating_margin') }}" > <i class="fas fa-chart-bar fa-sm fa-fw mr-2 text-blue-400"></i><strong>Margen Operativo</strong></a>
                    <a class="collapse-item" href="{{ route('inventories') }}" > <i class="fas fa-boxes fa-sm fa-fw mr-2 text-blue-400"></i><strong>Inventario</strong></a>

                    <a  class="collapse-header text-danger" href="#">Otros</a>
                    <a class="collapse-item" href="{{ route('reports.sales')}}" > <i class="fas fa-dollar-sign fa-sm fa-fw mr-2 text-black-400"></i><strong>Ventas</strong></a>
                    <a class="collapse-item" href="{{ route('reports.shopping')}}" > <i class="fas fa-file-invoice-dollar fa-sm fa-fw mr-2 text-black-400"></i><strong>Compras</strong></a>

                </div>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseHistorial"
                aria-expanded="true" aria-controls="collapseHistorial">
                <i class="fas fa-fw fa-archive" ></i>
                <span>Historial</span>
            </a>
            <div id="collapseHistorial" class="collapse" aria-labelledby="headingHistorial" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
    
                    <a class="collapse-item" href="{{ route('historial_quotation')}}" > <i class="fas fa-archive fa-sm fa-fw mr-2 text-black-400"></i><strong>Historial Cotización</strong></a>
                    <a class="collapse-item" href="{{ route('historial_expense')}}" > <i class="fas fa-archive fa-sm fa-fw mr-2 text-black-400"></i><strong>Historial Compras</strong></a>
                    <a class="collapse-item" href="{{ route('historial_anticipo')}}" > <i class="fas fa-archive fa-sm fa-fw mr-2 text-black-400"></i><strong>Historial Anticipos</strong></a>
    
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('inventories')}}">
                <i class="fas fa-fw fa-boxes" ></i>
                <span>Inventario</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('imports')}}">
                <i class="fas fa-fw fa-boxes" ></i>
                <span>Importaciones</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('companies.create') }}">
                <i class="fas fa-fw fa-wrench"></i>
                <span>General</span></a>
        </li>

    @endif

    @if (Auth::user()->role_id  == '3')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('inventories')}}">
            <i class="fas fa-fw fa-boxes" ></i>
            <span>Inventario</span></a>
    </li>
    @endif
    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

    <!-- Sidebar Message -->


</ul>
