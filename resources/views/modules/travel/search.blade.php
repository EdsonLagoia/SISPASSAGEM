<div class="col-sm-12 mb-2">
    <h3>Resultados ({{ $result }})</h3>
</div>

<div class="col-sm-12">
    <table class="table table-responsive table-striped table-secondary" width=100%>
        @if($search)
            @foreach($search as $search)
                <tr class="align-middle">
                    <td width=75%>
                        <table width=100%>
                            <tr>
                                <td>
                                    <p class="mb-0"><b>Nome: </b> {{ $search->social_name ? $search->social_name : $search->name }}
                                    <p class="mb-0">
                                        <b>D. Nasc.:</b> {{ date('d/m/Y', strtotime($search->birth_date)) }}
                                        <b class="ps-4">CPF: </b> {{ $search->cpf }}
                                        <b class="ps-4">RG: </b> {{ $search->rg }}
                                    </p>
                                    <p class="mb-0"><b>Telefone: </b> {{ $search->phone }}</p>
                                </td>
                            </tr>
                        </table>
                    </td>

                    <td width=25% class="text-end">
                        <form action="/patient/active/{{ $search->id }}" method="post">
                            @csrf
                            @method('PUT')

                            <a href="/travel/report/patients/{{ $search->id }}" class="btn btn-secondary" title="Relação de Atendimento de {{ $search->name }}" target="_blank">
                                <i class="fa-solid fa-file"></i>
                            </a>

                            @if(!session()->get('viewer'))
                                @if(!$search->active)
                                    <button type="submit" class="btn btn-info" id="enable" name="enable" value="{{ $search->id }}" title="Reativar Paciente">
                                        <i class="fa-solid fa-recycle"></i>
                                    </button>
                                @else
                                    <a href="patient/{{ $search->id }}" class="btn btn-info" title="Editar Paciente">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <a href="companion/{{ $search->id }}" class="btn btn-warning" title="Acompanhantes">
                                        <i class="fa-solid fa-users"></i>
                                    </a>

                                    <button type="submit" class="btn btn-danger" id="disable" name="disable" value="{{ $search->id }}" title="Desativar Paciente">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>

                                    <a href="travel/create/{{ $search->id }}" class="btn btn-success" title="Selecionar Paciente">
                                        <i class="fa-solid fa-circle-check"></i>
                                    </a>
                                @endif
                            @endif
                        </form>
                    </td>
                </tr>
            @endforeach
        @endif
    </table>
</div>
