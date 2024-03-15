@extends('layouts.module')

@section('content')
    <div class="row">
        <div class="col-sm-12 text-start">
            <h2>Entradas</h2>
        </div>
    </div>
    
    <hr>

    <table class="table table-responsive">

        <tbody>
            <tr class="table-secondary text-center">
                <th colspan=4>IDENTIFICAÇÃO DA ENTRADA</th>
            </tr>
            
            <tr class="table-light">
                <td colspan=2><strong>NÚMERO BPA: </strong>{{ $data->bpa }}</td>
                <td width=25%><strong>DATA: </strong>{{ date('d/m/Y', strtotime($data->entry)) }}</td>
                <td width=25%><strong>HORA: </strong>{{ date('H:i:s', strtotime($data->entry)) }}</td>
            </tr>
            
            <tr class="table-secondary text-center">
                <th colspan=4>IDENTIFICAÇÃO DO PACIENTE</th>
            </tr>

            <tr class="table-light">
                <td colspan=3><strong>NOME: </strong>{{ $data->name }}</td>
                <td width=25%><strong>CNS: </strong>{{ $data->cns }}</td>
            </tr>

            <tr class="table-light">
                <td width=25%><strong>IDADE: </strong>{{ $age }}  ANOS</td>
                <td width=25%><strong>DATA DE NASCIMENTO: </strong>{{ date('d/m/Y', strtotime($data->birth_date)) }}</td>
                <td width=25%><strong>Sexo: </strong>{{ date('H:i:s', strtotime($data->entry)) }}</td>
            </tr>
            <tr class="table-light">
                <td colspan=2 ><strong>NÚMERO BPA: </strong>{{ $data->bpa }}</td>
                <td width=25%><strong>DATA: </strong>{{ date('d/m/Y', strtotime($data->entry)) }}</td>
                <td width=25%><strong>HORA: </strong>{{ date('H:i:s', strtotime($data->entry)) }}</td>
            </tr>
            <tr class="table-light">
                <td colspan=2 ><strong>NÚMERO BPA: </strong>{{ $data->bpa }}</td>
                <td width=25%><strong>DATA: </strong>{{ date('d/m/Y', strtotime($data->entry)) }}</td>
                <td width=25%><strong>HORA: </strong>{{ date('H:i:s', strtotime($data->entry)) }}</td>
            </tr>
            <tr class="table-light">
                <td colspan=2 ><strong>NÚMERO BPA: </strong>{{ $data->bpa }}</td>
                <td width=25%><strong>DATA: </strong>{{ date('d/m/Y', strtotime($data->entry)) }}</td>
                <td width=25%><strong>HORA: </strong>{{ date('H:i:s', strtotime($data->entry)) }}</td>
            </tr>
            <tr class="table-light">
                <td colspan=2 ><strong>NÚMERO BPA: </strong>{{ $data->bpa }}</td>
                <td width=25%><strong>DATA: </strong>{{ date('d/m/Y', strtotime($data->entry)) }}</td>
                <td width=25%><strong>HORA: </strong>{{ date('H:i:s', strtotime($data->entry)) }}</td>
            </tr>
            <tr class="table-light">
                <td colspan=2 ><strong>NÚMERO BPA: </strong>{{ $data->bpa }}</td>
                <td width=25%><strong>DATA: </strong>{{ date('d/m/Y', strtotime($data->entry)) }}</td>
                <td width=25%><strong>HORA: </strong>{{ date('H:i:s', strtotime($data->entry)) }}</td>
            </tr>
            <tr class="table-light">
                <td colspan=2 ><strong>NÚMERO BPA: </strong>{{ $data->bpa }}</td>
                <td width=25%><strong>DATA: </strong>{{ date('d/m/Y', strtotime($data->entry)) }}</td>
                <td width=25%><strong>HORA: </strong>{{ date('H:i:s', strtotime($data->entry)) }}</td>
            </tr>
            <tr class="table-light">
                <td colspan=2 ><strong>NÚMERO BPA: </strong>{{ $data->bpa }}</td>
                <td width=25%><strong>DATA: </strong>{{ date('d/m/Y', strtotime($data->entry)) }}</td>
                <td width=25%><strong>HORA: </strong>{{ date('H:i:s', strtotime($data->entry)) }}</td>
            </tr>
            <tr class="table-light">
                <td colspan=2 ><strong>NÚMERO BPA: </strong>{{ $data->bpa }}</td>
                <td width=25%><strong>DATA: </strong>{{ date('d/m/Y', strtotime($data->entry)) }}</td>
                <td width=25%><strong>HORA: </strong>{{ date('H:i:s', strtotime($data->entry)) }}</td>
            </tr>
        </tbody>
    </table>
@endsection