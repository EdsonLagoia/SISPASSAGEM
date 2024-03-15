@extends('layouts.module')

@section('content')
    <div class="row">
        <div class="col-sm-12 text-start">
            <h2>Atualizar Usuário</h2>
        </div>
    </div>

    <hr>

    <form action="update/{{ $data->id }}" method="post">
        @csrf
        @method('PUT')
        <div class="row mb-3">
            <div class="col-sm-8">
                <label for="name" class="form-label">Nome <span class="text-danger fw-bold">*</span></label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $data->name }}" required>
            </div>

            <div class="col-sm-4">
                <label for="cpf" class="form-label">CPF <span class="text-danger fw-bold">*</span></label>
                <input type="text" class="form-control" id="cpf" name="cpf" value="{{ $data->cpf }}" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-sm-5">
                <label for="phone" class="form-label">Telefone <span class="text-danger fw-bold">*</span></label>
                <input type="text" class="form-control" id="phone" name="phone" maxlength=15 value="{{ $data->phone }}" required>
            </div>

            <div class="col-sm-5">
                <label for="email" class="form-label">Email <span class="text-danger fw-bold">*</span></label>
                <input type="email" class="form-control" id="email" name="email" value="{{ $data->email }}" required>
            </div>

            <div class="col-sm-2 mt-4 pt-2">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="viewer" name="viewer" {{ $data->viewer ? 'checked' : '' }}>
                    <label class="form-check-label" for="viewer">Visualizador</label>
                </div>
            </div>
        </div>

        <hr>

        <h3 class="mb-2">Módulos <span class="text-danger fw-bold">*</span></h3>

        <div class="row mb-3">
            @foreach($module as $module)
                <div class="col-sm-3 mb-1">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="{{ $module->id }}" name="module[]" value="{{ $module->id }}" {{ array_search($module->id, $data->modules) != '' ? 'checked' : '' }}>
                        <label class="form-check-label" for="{{ $module->id }}">{{ $module->title }}</label>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row">
            <div class="col-sm-12">
                <button type="submit" class="btn btn-success">
                    <i class="fa-solid fa-redo"></i> Atualizar
                </button>

                <button type="reset" class="btn btn-warning">
                    <i class="fa-solid fa-eraser"></i> Limpar
                </button>

                <a href="/user" class="btn btn-danger">
                    <i class="fa-solid fa-angles-left"></i> Voltar
                </a>
            </div>
        </div>
    </form>
@endsection
