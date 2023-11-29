
<div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
        <div class="card shadow mb-4">

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>Nombre / Razón Social</th>
                            <th>Cedula o Rif</th>
                            <th>Telefono</th>
                        </tr>
                        </thead>

                        <tbody>
                            @if (empty($clientes))
                            @else
                                @foreach ($clientes as $client)
                                    <tr>
                                        <td>{{$client->name}}</td>
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
