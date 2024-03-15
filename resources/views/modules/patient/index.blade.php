@extends('layouts.module')


@section('content')
    <div class="row">
        <div class="col-sm-9 text-start">
            <h2>Paciente</h2>
        </div>

        @include('layouts.insert', ['module' => 'patient/create'])
    </div>

    <hr>

    <table class="table table-responsive table-striped table-secondary" id="table">
        <thead>
            <th width=5%>#</th>
            <th width=45%>Nome</th>
            <th width=25%>CPF</th>
            <th width=5%>Ativo</th>
            <th width=20%>Ações</th>
        </thead>

        <tbody>
            @foreach($data as $data)
                <tr class="align-middle">
                    <td>{{ $data->id }}</td>
                    <td>{{ $data->name }}</td>
                    <td>{{ $data->cpf }}</td>
                    <th class="text-{{ $data->active ? 'success' : 'danger' }}">{{ $data->active ? 'Sim' : 'Não' }}</th>
                    <td class="text-end">
                        <form action="/patient/active/{{ $data->id }}" method="post">
                            @csrf
                            @method('PUT')

                            <a href="/travel/report/patients/{{ $data->id }}" class="btn btn-secondary" title="Relação de Atendimento Solicitado por {{ $data->name }}" target="_blank">
                                <i class="fa-solid fa-file"></i>
                            </a>

                            @if(!session()->get('viewer'))
                                @if(!$data->active)
                                    <button type="submit" class="btn btn-info" id="enable" name="enable" value="{{ $data->id }}" title="Reativar Paciente">
                                        <i class="fa-solid fa-recycle"></i>
                                    </button>
                                @else
                                    <a href="patient/{{ $data->id }}" class="btn btn-info" title="Editar Paciente">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <a href="companion/{{ $data->id }}" class="btn btn-warning" title="Acompanhantes">
                                        <i class="fa-solid fa-users"></i>
                                    </a>

                                    <button type="submit" class="btn btn-danger" id="disable" name="disable" value="{{ $data->id }}" title="Desativar Paciente">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                @endif
                            @endif
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
