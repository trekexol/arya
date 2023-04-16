
    <!-- container-fluid -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="row py-lg-2">
            <div class="col-md-6">
                <h2>Facturas de Inventario</h2>
            </div>
        </div>
    </div>

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
                <table class="table table-light2 table-bordered" id="extablaa" width="100%" cellspacing="0" >
                    <thead>
                    <tr>
                        <th class="text-center"></th>
                        <th class="text-center">N° de Factura</th>
                        <th class="text-center">N° de Control/Serie</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if (count($quotationss) > 0)
                        @foreach($quotationss as $quotation)

                                            <tr>
                                                <td>
                                                    <form method="POST" action="{{ route('imports.create') }}">
                                                        @csrf
                                                        <input type="hidden" name="id" id="id" value="{{ encrypt($quotation->invoice)}}"/>
                                                        <input type="hidden" name="inv" id="inv" value="true"/>



                                                        <button type="submit" class="btn btn-success">Seleccione.</button>
                                                    </form>
                                                </td>
                                                <td class="text-center">{{$quotation->invoice}}</td>
                                                <td class="text-center">{{$quotation->serie}}</td>
                                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<script>

$('#extablaa').DataTable({
                        "ordering": false,
                        "order": [],
                        'aLengthMenu': [[10], [10]]
                    });
</script>
