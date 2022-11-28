@extends('admin.layouts.dashboard')

@section('content')



<!-- container-fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="row py-lg-2">
        <div class="col-md-6 h2">Empresas Registradas
        </div>

        @if (Auth::user()->role_id  == '1' || $agregarmiddleware  == '1' )
        <div class="col-md-6">
            <a href="{{ route('companies.create')}}" class="btn btn-primary btn-lg float-md-right" role="button" aria-pressed="true">Registrar Empresa</a>

        </div>
        @endif



    </div>

  </div>

  {{-- VALIDACIONES-RESPUESTA--}}
@include('admin.layouts.success')   {{-- SAVE --}}
@include('admin.layouts.danger')    {{-- EDITAR --}}
@include('admin.layouts.delete')    {{-- DELELTE --}}
{{-- VALIDACIONES-RESPUESTA --}}

<div class="card shadow mb-4">
    
    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-light2 table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
            <tr>
                <th>Nro</th>
                <th>Login</th>
                <th>Razon Social</th>
                @if (Auth::user()->role_id  == '1' || $actualizarmiddleware  == '1')
                <th>Opciones</th>
                @endif
                @if (Auth::user()->role_id  == '1' || $agregarmiddleware  == '1')
                <th>Logo</th>
                @endif
            </tr>
            </thead>

            <tbody>
                @if (empty($users))
                @else
                    @foreach ($users as $key => $user)
                    <tr>
                    <td>{{$user->id}}</td>
                    <td>{{$user->login}}</td>
                    <td>{!!$user->razon_social!!}</td>

                    @if (Auth::user()->role_id  == '1' || $actualizarmiddleware  == '1')
                        <td>
                        <a href="{{route('companies.edit',$user->id) }}" title="Editar"><i class="fa fa-edit"></i></a>
                        </td>
                    @endif
                    @if (Auth::user()->role_id  == '1' || $agregarmiddleware  == '1')
                        <td>
                           <button>Subir Logo</button>
                        </td>
                    @endif
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        </div>
    </div>
</div>

@endsection
@section('javascript')

    <script>
    $('#dataTable').DataTable({
        "ordering": false,
        "order": [],
        'aLengthMenu': [[50, 100, 150, -1], [50, 100, 150, "All"]]
    });

    </script> 

@endsection