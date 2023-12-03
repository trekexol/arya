
<div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
        <div class="card shadow mb-4">

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-light2 table-bordered table-sm" id="dataTablees" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>Nombre / Razón Social</th>
                            <th>Cedula o Rif</th>
                            <th>Telefono</th>
                        </tr>
                        </thead>
                        <input id="idfactura" type="hidden" value="{{$data}}">
                        <tbody>
                            @if (empty($clientes))
                            @else
                                @foreach ($clientes as $client)
                                    <tr>
                                        <td><button class="btn btn-link cambio" value="{{$client->id}}">{{$client->name}}</button></td>
                                        <td>{{$client->type_code}} {{$client->cedula_rif}}</td>
                                        <td>{{$client->phone1}}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

    <script>

var tabladata = $('#dataTablees').dataTable( {
        "ordering": false,
        "order": [],
            'aLengthMenu': [[10, 20, -1], [10, 20, "All"]]
    } );


    tabladata.$('.cambio').click(function(e){
        e.preventDefault();
        var value = $(this).val();
        var idfac = $("#idfactura").val();
        $.ajax({
        method: "POST",
        url: "{{ route('cambiocliente') }}",
        data: {value: value,idfac: idfac, "_token": "{{ csrf_token() }}"},
             success:(response)=>{
                 if(response.error == true){
                    Swal.fire({
                        icon: 'success',
                        title: 'Exito!',
                        text: response.msg,
                        })
                        setTimeout("location.reload()", 1800);

                 }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Error...',
                        text: response.msg,
                        })
                 }
             },
             error:(response)=>{
                Swal.fire({
                        icon: 'error',
                        title: 'Error...',
                        text: response.msg,
                        });
             }
         })





    });
    </script>
