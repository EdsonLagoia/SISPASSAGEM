@extends('layouts.module')

@section('content')
    <div class="row">
        <div class="col-sm-9 text-start">
            <h2>Especialidades</h2>
        </div>

        @include('layouts.insert', ['module' => '/specialty/create'])
    </div>

    <hr>

    <table class="table table-responsive table-striped table-secondary" id="table">
        <thead>
            <th width=5%>#</th>
            <th width=75%>Especialidade</th>
            <th width=5%>Ativo</th>
            <th width=15%>Ações</th>
        </thead>

        <tbody>
            @foreach($data as $data)
                <tr class="align-middle">
                    <td>{{ $data->id }}</td>
                    <td>{{ $data->specialty }}</td>
                    <th class="text-{{ $data->active ? 'success' : 'danger' }}">{{ $data->active ? 'Sim' : 'Não' }}</th>
                    <td class="text-end">
                        <form class="px-0" action="specialty/active/{{ $data->id }}" method="post">
                            @csrf
                            @method('PUT')

                            <a href="/travel/report/specialties/{{ $data->id }}" class="btn btn-secondary" title="Relação de Atendimento de {{ $data->part }}" target="_blank">
                                <i class="fa-solid fa-file"></i>
                            </a>

                            @if(!session()->get('viewer'))
                                @if(!$data->active)
                                    <button type="submit" class="btn btn-info" id="enable" name="enable" value="{{ $data->id }}" title="Reabilitar Especialidade">
                                        <i class="fa-solid fa-recycle"></i>
                                    </button>
                                @else
                                    <a href="/specialty/{{ $data->id }}" class="btn btn-info" title="Editar Especialidade">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <button type="submit" class="btn btn-danger" id="disable" name="disable" value="{{ $data->id }}" title="Desabilitar Especialidade">
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
