<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Patient;
use App\Models\Companion;

class PatientController extends Controller
{
    public function index(Request $request) {
        $verify = AccessController::verify('patient', 0);
        if($verify)
            return redirect($verify);

        return view('modules.patient.index', [
            'menu' => ModuleController::menu(),
            'success' => $request->cookie('success'),
            'data' => Patient::all()
        ]);
    }

    public function create(Request $request) {
        $verify = AccessController::verify('patient', session()->get('viewer'));
        if($verify)
            return redirect($verify);

        return view('modules.patient.create', [
            'menu' => ModuleController::menu(),
            'erro' => $request->cookie('erro')
        ]);
    }

    public function store(Request $request) {
        if(Patient::where('cpf', $request->cpf)->count() > 0) {
            return redirect('patient/create')->cookie('erro', 'Paciente Já Cadastrado!', 0.03);
        } else {
            $create = new Patient;
            $create->name            = trim(ucwords(mb_strtoupper($request->name)));
            $create->social_name     = trim(ucwords(mb_strtoupper($request->social_name)));
            $create->birth_date      = $request->birth_date;
            $create->sex             = $request->sex;
            $create->cpf             = $request->cpf;
            $create->rg              = $request->rg;
            $create->phone           = $request->phone;
            $create->active          = 1;
            $create->save();

            return redirect('companion/' . $create->id)->cookie('success', 'Pacient Cadastrado com Sucesso!', 0.03);
        }
    }

    public function edit(Request $request, $id) {
        $verify = AccessController::verify('patient', session()->get('viewer'));
        if($verify)
            return redirect($verify);

        if($id <= 0 || $id > Patient::max('id'))
            return redirect('travel');

        return view('modules.patient.update', [
            'menu' => ModuleController::menu(),
            'erro' => $request->cookie('erro'),
            'data' => Patient::findOrFail($id),
        ]);
    }

    public function update(Request $request, $id) {
        if(Patient::where([['cpf', $request->cpf], ['id', '!=', $id]])->count() > 0) {
            return redirect('patient/' . $id)->cookie('erro', 'Paciente Já Cadastrado!', 0.03);
        } else {
            $update = Patient::find($id);
            $update->name            = trim(ucwords(mb_strtoupper($request->name)));
            $update->social_name     = isset($request->social_name) ? trim(ucwords(mb_strtoupper($request->social_name))) : NULL;
            $update->birth_date      = $request->birth_date;
            $update->sex             = $request->sex;
            $update->cpf             = $request->cpf;
            $update->rg              = $request->rg;
            $update->phone           = $request->phone;
            $update->save();

            return redirect('companion/' . $id)->cookie('success', 'Paciente Atualizado com Sucesso!', 0.03);
        }
    }

    public function active(Request $request, $id) {
        if($request->disable) {
            $disable = Patient::find($id);
            $disable->active = 0;
            $disable->save();

            return redirect('travel')->cookie('success', 'Paciente Desativado com Sucesso!', 0.03);

        } elseif($request->enable) {
            $enable = Patient::find($id);
            $enable->active = 1;
            $enable->save();

            return redirect('travel')->cookie('success', 'Paciente Reativado com Sucesso!', 0.03);
        }
    }
}
