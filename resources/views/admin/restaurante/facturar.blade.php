
    <form method="POST" id="carrito">

        <div class="tab-pane fade show active table-responsive-lg" id="home" role="tabpanel" aria-labelledby="home-tab">

            <table class="table table-lg">
                <thead>
                    <tr>
                      <th scope="col">Producto</th>
                      <th scope="col">Cantida</th>
                      <th scope="col">Precio</th>
                      <th scope="col">Total</th>
                    </tr>
                  </thead>

                  <tbody>
                    <?php $total = 0; ?>
                    @foreach ($quotations as $quotations)
                    <tr>
                        <td>{{ $quotations->nombreproducto }}</td>
                        <td>{{ $quotations->amount }}</td>
                        <td>{{ number_format($quotations->price, 2, ',', '.') }}</td>
                        <td>{{number_format($quotations->price * $quotations->amount, 2, ',', '.')}}</td>
                    </tr>
                    <?php $total += $quotations->price * $quotations->amount; ?>
                    @endforeach
                    <?php
                        $iva = $total * 16 / 100;
                        $totalconiva = $total + $iva;
                    ?>
                    <tr class="alert alert-success">
                        <td colspan="2" align="center">TOTAL CON iva (16%)</td>
                        <td>{{number_format($totalconiva, 2, ',', '.')}} Bs</td>
                        <td>{{number_format($totalconiva / $quotations->rate, 2, ',', '.')}} $</td>
                    </tr>
                  </tbody>
            </table>


    </div>
    <div class="card-footer">
        <div class="form-row">
            <div class="col-7">
                <select id="payment_type" required="" name="payment_type" class="form-control form-control-sm">
                    <option selected="" value="">Forma de Pago 1</option>
                    <option value="1">Cheque</option>
                    <option value="2">Contado</option>
                    <option value="5">Depósito Bancario</option>
                    <option value="6">Efectivo</option>
                    <option value="7">Indeterminado</option>
                    <option value="9">Tarjeta de Crédito</option>
                    <option value="10">Tarjeta de Débito</option>
                    <option value="11">Transferencia</option>
                </select>
            </div>
            <div class="col">
                <input type="submit" class="btn btn-primary btn-sm" value="Facturar" />
            </div>

          </div>


    </div>
</form>


    <script>

$(document).ready(function(){


});



    </script>



<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

