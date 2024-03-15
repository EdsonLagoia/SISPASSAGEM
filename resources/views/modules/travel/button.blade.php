<button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#report">
    <i class="fa-solid fa-file"></i> Relatórios
</button>

@if(!session()->get('viewer'))
    <a href="patient/create" class="btn btn-success">
        <i class="fa-solid fa-plus"></i> Cadastrar Paciente
    </a>
@endif

<form action="/travel/report/0/0" method="get" target="_blank">
    @csrf
    <div class="modal fade" id="report" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="reportLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-dark text-start">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="reportLabel">Relatórios</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12" id="types">
                            <label for="type" class="form-label">Tipo: <span class="text-danger fw-bold">*</span></label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">---</option>
                                <option value="daily">Diário</option>
                                <option value="monthly">Mensal</option>
                                <option value="yearly">Anual</option>
                                <option value="parts">Trechos</option>
                                <option value="specialties">Especialidades</option>
                                <option value="general">Geral</option>
                            </select>
                        </div>

                        <div class="d-none reports" id="days">
                            <label for="day" class="form-label">Dia <span class="text-danger fw-bold">*</span></label>
                            <input type="date" class="form-control report" id="day" name="day" max="{{ date('Y-m-d') }}" required disabled>
                        </div>

                        <div class="d-none reports" id="months">
                            <label for="month" class="form-label">Mês: <span class="text-danger fw-bold">*</span></label>
                            <select class="form-select report" id="month" name="month" required disabled>
                                <option value="">---</option>
                                <option value="1">Janeiro</option>
                                <option value="2">Fevereiro</option>
                                <option value="3">Março</option>
                                <option value="4">Abril</option>
                                <option value="5">Maio</option>
                                <option value="6">Junho</option>
                                <option value="7">Julho</option>
                                <option value="8">Agosto</option>
                                <option value="9">Setembro</option>
                                <option value="10">Outubro</option>
                                <option value="11">Novembro</option>
                                <option value="12">Dezembro</option>
                            </select>
                        </div>

                        <div class="d-none reports" id="years">
                            <label for="year" class="form-label">Ano: <span class="text-danger fw-bold">*</span></label>
                            <select class="form-select report" id="year" name="year" required disabled>
                                <option value="">---</option>
                                @for ($i = 2015; $i <= date('Y'); $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="d-none reports" id="parts">
                            <label for="part" class="form-label">Trecho: <span class="text-danger fw-bold">*</span></label>
                            <select class="form-select report" id="part" name="id" required disabled>
                                <option value="">---</option>
                                @foreach ($part as $part)
                                    <option value="{{ $part->id }}">{{ $part->part }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-none reports" id="specialties">
                            <label for="specialty" class="form-label">Trecho: <span class="text-danger fw-bold">*</span></label>
                            <select class="form-select report" id="specialty" name="id" required disabled>
                                <option value="">---</option>
                                @foreach ($specialty as $specialty)
                                    <option value="{{ $specialty->id }}">{{ $specialty->specialty }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-success">Gerar</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
    $(document).ready(function(){
        $('#type').change(function(){
            if($('#type').val() == 'daily') {
                $('.report').attr('disabled', 'disabled');
                $('.reports').addClass('d-none');
                $('#days').attr('class', 'col-sm-6 reports');
                $('#day').removeAttr('disabled')
                $('#types').attr('class', 'col-sm-6');
            } else if($('#type').val() == 'monthly') {
                $('.report').attr('disabled', 'disabled');
                $('.reports').addClass('d-none');
                $('#months').attr('class', 'col-sm-4 reports');
                $('#month').removeAttr('disabled')
                $('#years').attr('class', 'col-sm-4 reports');
                $('#year').removeAttr('disabled')
                $('#types').attr('class', 'col-sm-4');
            } else if($('#type').val() == 'yearly') {
                $('.report').attr('disabled', 'disabled');
                $('.reports').addClass('d-none');
                $('#specialties').attr('class', 'col-sm-6 reports');
                $('#specialty').removeAttr('disabled')
                $('#types').attr('class', 'col-sm-6');
            } else if($('#type').val() == 'parts') {
                $('.report').attr('disabled', 'disabled');
                $('.reports').addClass('d-none');
                $('#parts').attr('class', 'col-sm-6 reports');
                $('#part').removeAttr('disabled')
                $('#types').attr('class', 'col-sm-6');
            } else if($('#type').val() == 'specialties') {
                $('.report').attr('disabled', 'disabled');
                $('.reports').addClass('d-none');
                $('#specialties').attr('class', 'col-sm-6 reports');
                $('#specialty').removeAttr('disabled')
                $('#types').attr('class', 'col-sm-6');
            } else {
                $('#types').attr('class', 'col-sm-12');
                $('.report').attr('disabled', 'disabled');
                $('.reports').addClass('d-none');
            }
        });
    });
</script>
