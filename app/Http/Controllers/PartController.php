<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Part;

class PartController extends Controller
{
    public function index(Request $request) {
        $verify = AccessController::verify('part', 0);
        if($verify)
            return redirect($verify);

        return view('modules.part.index', [
            'menu' => ModuleController::menu(),
            'success' => $request->cookie('success'),
            'data' => Part::all()
        ]);
    }

    public function create(Request $request) {
        $verify = AccessController::verify('part', session()->get('viewer'));
        if($verify)
            return redirect($verify);

        return view('modules.part.create', [
            'menu' => ModuleController::menu(),
            'erro' => $request->cookie('erro'),
            'part' => Part::all()
        ]);
    }

    public function store(Request $request) {
        if(Part::where('part', $request->part)->count() > 0) {
            return redirect('part/create')->cookie('erro', 'Trecho Já Cadastrado!', 0.03);
        } else {
            $create = new Part;
            $create->part   = trim(mb_strtoupper($request->part));
            $create->active = 1;
            $create->save();

            return redirect('part')->cookie('success', 'Trecho Cadastrado com Sucesso!', 0.03);
        }
    }

    public function edit(Request $request, $id) {
        $verify = AccessController::verify('part', session()->get('viewer'));
        if($verify)
            return redirect($verify);

        if($id <= 0 || $id > Part::max('id'))
            return redirect('part');

        return view('modules.part.update', [
            'menu' => ModuleController::menu(),
            'erro' => $request->cookie('erro'),
            'data' => Part::findOrFail($id),
        ]);
    }

    public function update(Request $request, $id) {
        if(Part::where([['part', $request->part], ['id', '!=', $id]])->count() > 0) {
            return redirect('part/' . $id)->cookie('erro', 'Trecho Já Cadastrado!', 0.03);
        } else {
            $update = Part::find($id);
            $update->part   = trim(mb_strtoupper($request->part));
            $update->active = 1;
            $update->save();

            return redirect('part')->cookie('success', 'Trecho Atualizado com Sucesso!', 0.03);
        }
    }

    public function active(Request $request, $id) {
        if($request->disable) {
            $disable = Part::find($id);
            $disable->active = 0;
            $disable->save();

            return redirect('part')->cookie('success', 'Trecho Desativado com Sucesso!', 0.03);

        } elseif($request->enable) {
            $enable = Part::find($id);
            $enable->active = 1;
            $enable->save();

            return redirect('part')->cookie('success', 'Trecho Reativado com Sucesso!', 0.03);
        }
    }
}
