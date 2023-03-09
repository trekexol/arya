
<div class="container-fluid">
    <div class="row py-lg-2">

        <div class="col-md-6">
            <h2>Facturas de Compra / Gastos por pagar</h2>
        </div>


    </div>
</div>


<div class="card shadow mb-4">

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th class="text-center">Factura</th>
                        <th class="text-center">N° Serie</th>
                        <th class="text-center">Proveedor</th>
                        <th class="text-center">Fecha</th>
                        <th class="text-center">REF</th>
                        <th class="text-center">Total con IVA</th>
                        <th class="text-center">Seleccione.</th>

                    </tr>
                    </thead>

                    <tbody>
                            @foreach ($expensesandpurchases as $expensesandpurchases)
                            @if($expensesandpurchases->credit == FALSE OR $expensesandpurchases->debit == FALSE)

                            <?php

                                $amount_bcv = $expensesandpurchases->amount_with_iva / $expensesandpurchases->rate;
                            ?>

                                <tr>

                                    <td class="text-center font-weight-bold font-weight-bold text-dark">{{ $expensesandpurchases->invoice }}</td>
                                    <td class="text-center font-weight-bold">{{$expensesandpurchases->serie ?? ''}}</td>
                                    <td class="text-center font-weight-bold">{{$expensesandpurchases->providers['razon_social'] ?? ''}}</td>
                                    <td class="text-center font-weight-bold">{{date_format(date_create($expensesandpurchases->date),"d-m-Y")}}</td>
                                    <td class="text-right font-weight-bold">${{number_format($amount_bcv, 2, ',', '.')}}</td>
                                    <td class="text-right font-weight-bold">{{number_format($expensesandpurchases->amount_with_iva, 2, ',', '.')}}</td>
                                    <td class="text-center font-weight-bold">
                                        <form method="POST" action="{{ route($route)}}">
                                            @csrf
                                            <input type="hidden" name="idp" id="idp" value="{{ encrypt($expensesandpurchases->providers['id'])}}"/>
                                            <input type="hidden" name="id" id="id" value="{{ encrypt($expensesandpurchases->id)}}"/>
                                            <input type="hidden" name="fac" id="fac" value="{{ encrypt($expensesandpurchases->invoice)}}"/>
                                            <input type="hidden" name="tasa" id="fac" value="{{ encrypt($expensesandpurchases->rate)}}"/>
                                            <input type="hidden" name="serie" id="fac" value="{{ encrypt($expensesandpurchases->serie)}}"/>
                                            <input type="hidden" name="coin" id="coin" value="{{ encrypt($expensesandpurchases->coin)}}"/>
                                            <input type="hidden" name="credit" id="credit" value="{{ encrypt($expensesandpurchases->credit)}}"/>
                                            <input type="hidden" name="debit" id="debit" value="{{ encrypt($expensesandpurchases->debit)}}"/>


                                            <button type="submit"><i class="fa fa-check"></i></button>
                                        </form>

                                    </td>

                                </tr>
                                @endif
                            @endforeach

                    </tbody>
                </table>
        </div>
    </div>
</div>





    <script>

       $(document).ready(function () {



    $('#dataTable').DataTable({
        "ordering": false,
        "order": [],
        'aLengthMenu': [[10, 100, 150, -1], [10, 100, 150, "All"]]
    });



        });





    </script>

