<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Patient;
use App\Models\Companion;

class CompanionController extends Controller
{
    public function create(Request $request, $patient) {
        if($patient <= 0 || $patient > Patient::max('id'))
            return redirect('patient');

        return view('modules.companion.create',[
            'menu' => ModuleController::menu(),
            'erro' => $request->cookie('erro'),
            'success' => $request->cookie('success'),
            'patient' => Patient::findOrFail($patient),
            'data' => Companion::where('patient', $patient)->get()
        ]);
    }
    public function store(Request $request, $patient) {
        if(Companion::where([['cpf', $request->cpf], ['patient', $patient]])->count() > 0) {
            return redirect('companion/'. $patient)->cookie('erro', 'Acompanhante Já Cadastrado!', 0.03);
        } else {
            $create = new Companion;
            $create->patient     = $patient;
            $create->name        = trim(ucwords(mb_strtoupper($request->name)));
            $create->social_name = trim(ucwords(mb_strtoupper($request->social_name)));
            $create->birth_date  = $request->birth_date;
            $create->sex         = $request->sex;
            $create->cpf         = $request->cpf;
            $create->rg          = $request->rg;
            $create->phone       = $request->phone;
            $create->active      = 1;
            $create->save();

            return redirect('companion/'. $patient)->cookie('success', 'Acompanhante Cadastrado com Sucesso!', 0.03);
        }
    }
    public function edit(Request $request, $patient, $id) {
        if($id <= 0 || $id > Companion::max('id'))
            return redirect('companion');

        return view('modules.companion.update', [
            'menu' => ModuleController::menu(),
            'erro' => $request->cookie('erro'),
            'data' => Companion::where('patient', $patient)->get(),
            'edit' => Companion::findOrFail($id),
            'patient' => Patient::findOrFail($patient)
        ]);
    }
    public function update(Request $request, $patient, $id) {
        if(Companion::where([['cpf', $request->cpf], ['patient', $patient], ['id', '!=', $id]])->count() > 0) {
            return redirect('companion/' . $patient . '/' . $id)->cookie('erro', 'Acompanhante Já Cadastrado!', 0.03);
        } else {
            $update = Companion::find($id);
            $update->name        = trim(ucwords(mb_strtoupper($request->name)));
            $update->social_name = trim(ucwords(mb_strtoupper($request->social_name)));
            $update->birth_date  = $request->birth_date;
            $update->sex         = $request->sex;
            $update->cpf         = $request->cpf;
            $update->rg          = $request->rg;
            $update->phone       = $request->phone;
            $update->save();

            return redirect('companion/'. $patient)->cookie('success', 'Acompanhante Atualizado com Sucesso!', 0.03);
        }
    }
    public function active(Request $request, $patient, $id) {
        if($request->disable) {
            $disable = Companion::find($id);
            $disable->active = 0;
            $disable->save();

            return redirect('companion/'. $patient)->cookie('success', 'Acompanhante Desativado com Sucesso!', 0.03);

        } elseif($request->enable) {
            $enable = Companion::find($id);
            $enable->active = 1;
            $enable->save();

            return redirect('companion/'. $patient)->cookie('success', 'Acompanhante Reativado com Sucesso!', 0.03);
        }
    }
}
