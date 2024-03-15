<table class="table table-responsive table-striped table-secondary" id="table">
    <thead>
        <th width=5%>#</th>
        <th width=54%>Nome</th>
        <th width=25%>CPF</th>
        <th width=5%>Ativo</th>
        <th width=11%>Ações</th>
    </thead>

    <tbody>
        @foreach($data as $data)
            <tr>
                <td>{{ $data->id }}</td>
                <td>{{ $data->name }}</td>
                <td>{{ $data->cpf }}</td>
                <th class="text-{{ $data->active ? 'success' : 'danger' }}">{{ $data->active ? 'Sim' : 'Não' }}</th>
                <td class="text-end">
                    <form action="active/{{ $patient->id }}/{{ $data->id }}" method="post">
                        @csrf
                        @method('PUT')
                        @if(!$data->active)
                            <button type="submit" class="btn btn-info" id="enable" name="enable" value="{{ $data->id }}" title="Reativar Acompanhante">
                                <i class="fa-solid fa-recycle"></i>
                            </button>
                        @else
                            <a href="/companion/{{ $patient->id }}/{{ $data->id }}" class="btn btn-info" title="Editar Acompanhante">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>

                            <button type="submit" class="btn btn-danger" id="disable" name="disable" value="{{ $data->id }}" title="Desativar Acompanhante">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        @endif
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
