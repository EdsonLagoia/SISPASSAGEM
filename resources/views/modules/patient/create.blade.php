@extends('layouts.module')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <h2>Cadastrar Paciente</h2>
        </div>
    </div>

    <hr>

    <form action="store" method="post">
        @csrf
        <div class="row mb-3">
            <div class="col-sm-6">
                <label for="name" class="form-label">Nome <span class="text-danger fw-bold">*</span></label>
                <input type="text" class="form-control text-uppercase" id="name" name="name" required>
            </div>

            <div class="col-sm-3">
                <label for="birth_date" class="form-label">Data de Nascimento <span class="text-danger fw-bold">*</span></label>
                <input type="date" class="form-control" id="birth_date" name="birth_date" max="{{ date('Y-m-d') }}" required>
            </div>

            <div class="col-sm-3">
                <label for="cpf" class="form-label">CPF <span class="text-danger fw-bold">*</span></label>
                <input type="text" class="form-control" id="cpf" name="cpf" minlength=14 required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-sm-4 d-flex align-items-center">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="use_sn" name="use_sn" onchange="Social_Name()">
                    <label class="form-check-label" for="use_sn">Tratamento pelo nome social?</label>
                </div>
            </div>

            <div class="col-sm-8">
                <label for="social_name" class="form-label">Nome Social</label>
                <input type="text" class="form-control text-uppercase" id="social_name" name="social_name" required disabled>
                <p class="mb-0" style="font-size: 9pt">Nome social: designação pela qual a pessoa travesti ou transexual se identifica e é socialmente reconhecida;<br>
                Conforme o Decreto Federal Nº 8.727, de 28 de Abril de 2016.</p>
            </div>
        </div>


        <div class="row mb-3">
            <div class="col-sm-3">
                <label for="rg" class="form-label">RG <span class="text-danger fw-bold">*</span></label>
                <input type="text" class="form-control text-uppercase" id="rg" name="rg" minlength="6" required>
            </div>

            <div class="col-sm-3">
                <label for="sex" class="form-label">Sexo <span class="text-danger fw-bold">*</span></label>
                <select class="form-select" id="sex" name="sex" required>
                    <option value="">---</option>
                    <option value="MASCULINO" >MASCULINO</option>
                    <option value="FEMININO" >FEMININO</option>
                    <option value="INDIFERENTE" >INDIFERENTE</option>
                </select>
            </div>

            <div class="col-sm-6">
                <label for="phone" class="form-label">Telefone <span class="text-danger fw-bold">*</span></label>
                <input type="text" class="form-control text-uppercase" id="phone" name="phone" minlength="15" required>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <button type="submit" class="btn btn-success">
                    <i class="fa-solid fa-plus"></i> Cadastrar
                </button>

                <button type="reset" class="btn btn-warning">
                    <i class="fa-solid fa-eraser"></i> Limpar
                </button>

                <a href="/travel" class="btn btn-danger">
                    <i class="fa-solid fa-angles-left"></i> Voltar
                </a>
            </div>
        </div>
    </form>
@endsection
