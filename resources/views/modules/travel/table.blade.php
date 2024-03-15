@foreach($data as $data)
    <tr>
        <td width=90% class="{{ !$data->active ? 'text-danger' : '' }}">
            @inject('users', 'App\Models\User')
            @inject('patients', 'App\Models\Patient')
            @inject('companions', 'App\Models\Companion')
            @inject('parts', 'App\Models\Part')
            @php
                $user = $users::findOrFail($data->user);
                $patient = $patients::findOrFail($data->patient);
                $companion = $companions::whereIn('id', $data->companions)->get();
                $part = $parts::findOrFail($data->part);
                $i = 1;
            @endphp
            <p class="mb-0">
                <b>Nº Requisição:</b> {{ $data->request }}
                <b class="ps-4">Data da Consulta:</b> {{ date('d/m/Y', strtotime($data->date_service)) }}
            </p>

            <p class="mb-0">
                <b>Agendado por:</b> {{ $user->name }}
            </p>

            <p class="mb-0">
                <b>Trecho:</b> {{ $part->part }} <b class="ps-4">Ida/Volta:</b> {{ $data->return ? 'VOLTA' : 'IDA' }}
                <b class="ps-4">Data da Viagem:</b> {{ date('d/m/Y', strtotime($data->return ? $data->date_return : $data->date_going)) }}
                <b class="ps-4">Data do Atendimento:</b> {{ $data->date_consult ? date('d/m/Y', strtotime($data->date_consult)) : '' }}
            </p>

            <p class="mb-0">
                <b>Paciente:</b> {{ !$data->companion ? $patient->name : '' }} <b class="ps-4">Nasc.:</b> {{ !$data->companion ? date('d/m/Y', strtotime($patient->birth_date)) : '' }}
                <b class="ps-4">CPF:</b> {{ !$data->companion ? $patient->cpf : '' }} <b class="ps-4">RG:</b> {{ !$data->companion ? $patient->rg : '' }}
            </p>
            <p class="mb-0"><b>Tel. Pac.:</b> {{ !$data->companion ? $patient->phone : '' }}</p>

            @foreach ($companion as $companion)
                <p class="mb-0">
                    <b>Acompanhante {{ $i }}:</b> {{ $companion->name }} <b class="ps-4">Nasc.:</b> {{ date('d/m/Y', strtotime($companion->birth_date)) }}
                    <b class="ps-4">CPF:</b> {{ $companion->cpf }} <b class="ps-4">RG:</b> {{ $companion->rg }}
                </p>

                <p class="mb-0"><b>Tel. Acomp. {{ $i }}: </b> {{ $companion->phone }}</p>

                @php $i = 2 @endphp
            @endforeach
        </td>

        <td class="text-end align-middle" width="10%">
            <form action="/travel/cancel/{{ $data->id }}" method="post">
                @csrf
                @method('PUT')
                
                @if($data->active && !session()->get('viewer'))
                    <a href="/travel/{{ $data->id }}" class="btn btn-secondary" title="Emitir Ficha de Passagem" target="_blank">
                        <i class="fa-solid fa-file"></i>
                    </a>

                    <button type="submit" class="btn btn-danger" id="cancel" name="cancel" value="{{ $data->id }}" title="Cancelar Requisição">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                @endif
            </form>
        </td>
    </tr>
    @endforeach
    </tbody>