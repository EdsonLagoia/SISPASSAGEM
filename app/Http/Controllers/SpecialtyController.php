<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Specialty;

class SpecialtyController extends Controller
{
    public function index(Request $request) {
        $verify = AccessController::verify('specialty', 0);
        if($verify)
            return redirect($verify);

        return view('modules.specialty.index', [
            'menu' => ModuleController::menu(),
            'success' => $request->cookie('success'),
            'data' => Specialty::all()
        ]);
    }

    public function create(Request $request) {
        $verify = AccessController::verify('specialty', session()->get('viewer'));
        if($verify)
            return redirect($verify);

        return view('modules.specialty.create', [
            'menu' => ModuleController::menu(),
            'erro' => $request->cookie('erro'),
            'specialty' => Specialty::all()
        ]);
    }

    public function store(Request $request) {
        if(Specialty::where('specialty', $request->specialty)->count() > 0) {
            return redirect('specialty/create')->cookie('erro', 'Especialidade Já Cadastrada!', 0.03);
        } else {
            $create = new Specialty;
            $create->specialty = trim(mb_strtoupper($request->specialty));
            $create->active    = 1;
            $create->save();

            return redirect('specialty')->cookie('success', 'Especialidade Cadastrada com Sucesso!', 0.03);
        }
    }

    public function edit(Request $request, $id) {
        $verify = AccessController::verify('specialty', session()->get('viewer'));
        if($verify)
            return redirect($verify);
        
        if($id <= 0 || $id > Specialty::max('id'))
            return redirect('specialty');

        return view('modules.specialty.update', [
            'menu' => ModuleController::menu(),
            'erro' => $request->cookie('erro'),
            'data' => Specialty::findOrFail($id),
        ]);
    }

    public function update(Request $request, $id) {
        if(Specialty::where([['specialty', $request->specialty], ['id', '!=', $id]])->count() > 0) {
            return redirect('specialty/' . $id)->cookie('erro', 'Especialidade Já Cadastrada!', 0.03);
        } else {
            $update = Specialty::find($id);
            $update->specialty   = trim(mb_strtoupper($request->specialty));
            $update->active = 1;
            $update->save();

            return redirect('specialty')->cookie('success', 'Especialidade Atualizada com Sucesso!', 0.03);
        }
    }

    public function active(Request $request, $id) {
        if($request->disable) {
            $disable = Specialty::find($id);
            $disable->active = 0;
            $disable->save();

            return redirect('specialty')->cookie('success', 'Especialidade Desativada com Sucesso!', 0.03);

        } elseif($request->enable) {
            $enable = Specialty::find($id);
            $enable->active = 1;
            $enable->save();

            return redirect('specialty')->cookie('success', 'Especialidade Reativada com Sucesso!', 0.03);
        }
    }
}
