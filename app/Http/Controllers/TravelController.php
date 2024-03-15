<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Travel;
use App\Models\User;
use App\Models\Patient;
use App\Models\Companion;
use App\Models\Part;
use App\Models\Specialty;

use Codedge\Fpdf\Fpdf\Fpdf;

class TravelController extends Controller
{
    protected $fpdf;

    public function __construct() {
        $this->fpdf = new Fpdf;
    }

    public function index(Request $request) {
        $verify = AccessController::verify('travel', 0);
        if($verify)
            return redirect($verify);

        return view('modules.travel.index', [
            'menu' => ModuleController::menu(),
            'success' => $request->cookie('success'),
            'data' => Travel::where('date_service', date('Y-m-d'))->get(),
            'part' => Part::all(),
            'specialty' => Specialty::all()
        ]);
    }

    public function loadPatient(Request $request) {
        if($request->search) {
            $cpf = '';

            if(is_numeric($request->search)) {
                $length = mb_strlen($request->search) < 11 ? mb_strlen($request->search) : 11;
                for($i = 0; $i < $length ; $i++) {
                    $cpf .= $request->search[$i];
                    if($i == 2 || $i == 5)
                        $cpf .= '.';
                    elseif($i == 8)
                        $cpf .= '-';
                }
            }

            $patient = Patient::orWhere('name', 'like', '%' . $request->search . '%');
            $patient = $cpf ? $patient->orWhere('cpf', 'like', '%' . $cpf . '%') : $patient;

            $result = $patient->count();
            $search = $patient->limit(3)->get();
        } else {
            $result = 0;
            $search = '';
        }

        return view('modules.travel.search', [
            'result' => $result,
            'search' => $search
        ]);
    }

    public function loadTable(Request $request) {
        return view('modules.travel.table', [
            'data' => Travel::where('date_service', $request->service)->get(),
        ]);
    }

    public function loadCompanion(Request $request) {
        if($request->id && Companion::where([['patient', $request->patient], ['id', '!=', $request->id], ['active', 1]])->count() > 0)
            $companion = Companion::where([['patient', $request->patient], ['id', '!=', $request->id], ['active', 1]])->get();
        else
            $companion = '';

        return view('modules.travel.companion', [
            'companion' => $companion
        ]);
    }

    public function loadNumber(Request $request) {
        if($request->going)
            $travel = array('going', $request->going);
        else
            $travel = array('return', $request->return);

        if(Travel::where($travel[0], $travel[1])->count() > 0){
            $number = Travel::where($travel[0], $travel[1])->orderBy('id', 'desc')->get('request')->first();
            $number = explode('/', $number->request);
            if($number[1] == date('Y')) {
                $number[0][3] = $number[0][3]+1;
                $number = $number[0];
            } else {
                $number = '0001';
            }
        } else {
            $number = '';
        }

        return view('modules.travel.request', [
            'number' => $number,
            'travel' => $travel[0]
        ]);
    }

    public function create(Request $request, $id) {
        $verify = AccessController::verify('travel', session()->get('viewer'));
        if($verify)
            return redirect($verify);

        if($id <= 0 || $id > Patient::max('id'))
            return redirect('travel');

        return view('modules.travel.create', [
            'menu' => ModuleController::menu(),
            'erro' => $request->cookie('erro'),
            'part' => Part::where('active', 1)->get(),
            'specialty' => Specialty::where('active', 1)->get(),
            'companion' => Companion::where([['patient', $id], ['active', 1]])->get(),
            'patient' => Patient::findOrFail($id)
        ]);
    }

    public function store(Request $request, $id) {
        if(isset($request->going)) {
            $create = new Travel;
            $create->user         = session()->get('id_user');
            $create->patient      = $request->id;
            $create->part         = $request->part;
            $create->specialty    = $request->specialty;
            $create->companions   = $request->companions;
            $create->date_service = date('Y-m-d');
            $create->request      = $request->going_request . date('/Y');
            $create->date_going   = $request->date_going;
            $create->date_consult = $request->date_consult;
            $create->process      = $request->process;
            $create->going        = 1;
            $create->return       = 0;
            $create->companion    = isset($request->companion) ? 1 : 0;
            $create->obs          = mb_strtoupper($request->obs);
            $create->active       = 1;
            $create->save();
        }

        if(isset($request->return)) {
            $create = new Travel;
            $create->user         = session()->get('id_user');
            $create->patient      = $request->id;
            $create->part         = $request->part;
            $create->specialty    = $request->specialty;
            $create->companions   = $request->companions;
            $create->date_service = date('Y-m-d');
            $create->request      = $request->return_request . date('/Y');
            $create->date_return  = $request->date_return;
            $create->process      = $request->process;
            $create->going        = 0;
            $create->return       = 1;
            $create->companion    = isset($request->companion) ? 1 : 0;
            $create->obs          = $request->obs;
            $create->active       = 1;
            $create->save();
        }

        return redirect('travel')->cookie('success', 'Passagem Cadastrada com Sucesso!', 0.03);
    }

    
    public function cancel(Request $request, $id) {
        if($request->cancel) {
            $disable = Travel::find($id);
            $disable->active = 0;
            $disable->save();

            return redirect('travel')->cookie('success', 'Passagem Cancelada com Sucesso!', 0.03);
        }
    }

    public function request($id) {
        $pdf = $this->fpdf;
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->SetMargins(15, 10, 15);
        $data = Travel::findOrfail($id);
        $part = Part::findOrFail($data->part);
        $patient = Patient::findOrFail($data->patient);

        // Layout
            $pdf->SetXY(15, 50); $pdf->Cell(180, 187, '', 1);

            $pdf->SetXY(15, 55); $pdf->Cell(180, 0, '', 'B');
            $pdf->SetXY(15, 61); $pdf->Cell(180, 0, '', 'B');
            $pdf->SetXY(15, 71); $pdf->Cell(180, 0, '', 'B');
            $pdf->SetXY(15, 76); $pdf->Cell(180, 0, '', 'B');
            $pdf->SetXY(15, 81); $pdf->Cell(180, 0, '', 'B');
            $pdf->SetXY(15, 91); $pdf->Cell(180, 0, '', 'B');
            $pdf->SetXY(126, 96); $pdf->Cell(32, 0, '', 'B');
            $pdf->SetXY(126, 101); $pdf->Cell(32, 0, '', 'B');
            $pdf->SetXY(15, 106); $pdf->Cell(180, 0, '', 'B');
            $pdf->SetXY(15, 123); $pdf->Cell(180, 0, '', 'B');
            $pdf->SetXY(15, 133); $pdf->Cell(180, 0, '', 'B');
            $pdf->SetXY(15, 143); $pdf->Cell(180, 0, '', 'B');
            $pdf->SetXY(15, 148); $pdf->Cell(180, 0, '', 'B');
            $pdf->SetXY(15, 158); $pdf->Cell(180, 0, '', 'B');
            $pdf->SetXY(15, 163); $pdf->Cell(180, 0, '', 'B');
            $pdf->SetXY(15, 168); $pdf->Cell(180, 0, '', 'B');
            $pdf->SetXY(15, 178); $pdf->Cell(180, 0, '', 'B');
            $pdf->SetXY(15, 183); $pdf->Cell(180, 0, '', 'B');
            $pdf->SetXY(15, 188); $pdf->Cell(180, 0, '', 'B');
            $pdf->SetXY(95, 193); $pdf->Cell(20, 0, '', 'B');
            $pdf->SetXY(15, 198); $pdf->Cell(180, 0, '', 'B');
            $pdf->SetXY(15, 208); $pdf->Cell(180, 0, '', 'B');
            $pdf->SetXY(135, 218); $pdf->Cell(60, 0, '', 'B');
            $pdf->SetXY(25, 228); $pdf->Cell(100, 0, '', 'B');
            $pdf->SetXY(25, 243); $pdf->Cell(160, 0, '', 'B');
            $pdf->SetXY(25, 248); $pdf->Cell(160, 0, '', 'B');
            $pdf->SetXY(25, 253); $pdf->Cell(160, 0, '', 'B');
            $pdf->SetXY(25, 258); $pdf->Cell(160, 0, '', 'B');

            $pdf->SetXY(40, 61); $pdf->Cell(0, 10, '', 'L');
            $pdf->SetXY(135, 61); $pdf->Cell(0, 10, '', 'L');
            $pdf->SetXY(126, 91); $pdf->Cell(0, 15, '', 'L');
            $pdf->SetXY(133, 91); $pdf->Cell(0, 15, '', 'L');
            $pdf->SetXY(158, 91); $pdf->Cell(0, 15, '', 'L');
            $pdf->SetXY(158, 98); $pdf->Cell(0, 25, '', 'L');
            $pdf->SetXY(75, 168); $pdf->Cell(0, 10, '', 'L');
            $pdf->SetXY(65, 178); $pdf->Cell(0, 5, '', 'L');
            $pdf->SetXY(135, 208); $pdf->Cell(0, 29, '', 'L');

        // Cabeçalho
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Image(public_path() . '/img/brasao.png', 95, 10, 20, 25);
            $pdf->SetXY(15, 38); $pdf->MultiCell(180, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'GOVERNO DO ESTADO DA AMAPÁ'), 0, 'C');
            $pdf->SetXY(15, 43); $pdf->MultiCell(180, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'SECRETARIA DE ESTADO DA SAÚDE'), 0, 'C');

        // Campos de Dados do Usuário
            $pdf->SetXY(15, 50); $pdf->MultiCell(180, 5, 'PASSAGEM', 0, 'C');

            $pdf->SetXY(58, 71); $pdf->MultiCell(140, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'FAB VIAGENS E TURISMO EIRELI ME - Contrato nº 031/2023 NGC/SESA'));

            $pdf->SetXY(126, 91); $pdf->MultiCell(7, 5, 'X', 0, 'C');
            $pdf->SetXY(133, 91); $pdf->MultiCell(25, 5, 'AEREA', 0, 'C');
            $pdf->SetXY(158, 91); $pdf->MultiCell(37, 5, 'TRECHO', 0, 'C');

            $pdf->SetXY(133, 96); $pdf->MultiCell(25, 5, 'TERRESTRE', 0, 'C');

            $pdf->SetXY(133, 101); $pdf->MultiCell(25, 5, 'FLUVIAL', 0, 'C');


            $pdf->SetXY(18, 106); $pdf->MultiCell(140, 5, 'FAVORECIDO:', 0);
            $pdf->SetXY(158, 106); $pdf->MultiCell(37, 5, 'ESPECIALIDADE', 0, 'C');

            $pdf->SetXY(25, 113); $pdf->MultiCell(120, 5, 'PAC.:');

            $pdf->SetXY(26, 118); $pdf->MultiCell(45, 5, 'CPF:');
            $pdf->SetXY(71, 118); $pdf->MultiCell(40, 5, 'RG:');
            $pdf->SetXY(111, 118); $pdf->MultiCell(47, 5, 'DATA.NASC.:');

            $pdf->SetXY(19, 123); $pdf->MultiCell(120, 5, 'ACOMP.:');

            $pdf->SetXY(26, 128); $pdf->MultiCell(45, 5, 'CPF:');
            $pdf->SetXY(71, 128); $pdf->MultiCell(40, 5, 'RG:');
            $pdf->SetXY(111, 128); $pdf->MultiCell(47, 5, 'DATA.NASC.:');

            $pdf->SetXY(16, 133); $pdf->MultiCell(120, 5, 'ACOMP. 2:');

            $pdf->SetXY(26, 138); $pdf->MultiCell(45, 5, 'CPF:');
            $pdf->SetXY(71, 138); $pdf->MultiCell(40, 5, 'RG:');
            $pdf->SetXY(111, 138); $pdf->MultiCell(47, 5, 'DATA.NASC.:');

            $pdf->SetXY(15, 143); $pdf->MultiCell(180, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'PREENCHIMENTO OBRIGATÓRIO'), 0, 'C');

            $pdf->SetXY(33, 148); $pdf->MultiCell(162, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Recurso do Convênio Federal (fonte 016)'));

            $pdf->SetXY(30, 153); $pdf->MultiCell(165, 5, '>');
            $pdf->SetXY(33, 153); $pdf->MultiCell(162, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Recurso do Programa (Fonte 016): T.F.D.'));

            $pdf->SetXY(15, 163); $pdf->MultiCell(180, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Protocolo Geral nº ' . $data->process), 0, 'C');

            $pdf->SetXY(15, 203); $pdf->MultiCell(179, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'EMITIDOS DE ACORDO COM A PRESENTE REQUISIÇÃO'));


            $pdf->SetFont('Arial', '', 10);
            $pdf->SetXY(15, 55); $pdf->MultiCell(180, 6, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'REQUISIÇÃO DE PASSAGEM'), 0, 'C');

            $pdf->SetXY(15, 61); $pdf->MultiCell(25, 5, 'Local:');
            $pdf->SetXY(40, 61); $pdf->MultiCell(95, 5, 'DATA:');
            $pdf->SetXY(135, 61); $pdf->MultiCell(60, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Número:'));

            $pdf->SetXY(15, 66); $pdf->MultiCell(25, 5, 'MACAPA-AP', 0, 'C');

            $pdf->SetXY(22, 71); $pdf->MultiCell(173, 5, 'Empresa Requisitada:');

            $pdf->SetXY(20, 76); $pdf->MultiCell(175, 5, 'Senhor Gerente,');

            $pdf->SetXY(20, 81); $pdf->MultiCell(175, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'De acordo com a lei nº 1.749 e Decreto nº 2804 de 21 de Maio de 2013, requisitamos o'));

            $pdf->SetXY(15, 86); $pdf->MultiCell(180, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'fornecimento do (s) seguinte (s) Transporte (s):'));

            $pdf->SetXY(15, 91); $pdf->MultiCell(111, 5, 'Ida/Volta');

            $pdf->SetXY(15, 158); $pdf->MultiCell(180, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Órgão de Lotação:'));

            $pdf->SetXY(15, 168); $pdf->MultiCell(60, 5, 'Data da Consulta:');
            $pdf->SetXY(75, 168); $pdf->MultiCell(120, 5, 'Data da Viagem:');

            $pdf->SetXY(15, 178); $pdf->MultiCell(50, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Empresa Aérea.'), 0, 'C');
            $pdf->SetXY(65, 178); $pdf->MultiCell(130, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Código da Reserva.'), 0, 'C');

            $pdf->SetXY(15, 183); $pdf->MultiCell(180, 5, 'REQUISITANTE', 0, 'C');

            $pdf->SetXY(15, 193); $pdf->MultiCell(180, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Assinatura / Cargo ou Função.'), 0, 'C');

            $pdf->SetXY(15, 198); $pdf->MultiCell(180, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'O (s) bilhetes de passagem (ns) número (s):'));

            $pdf->SetXY(15, 208); $pdf->MultiCell(120, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'CERTIFICADO QUE AS INFORMAÇÕES DESTA REQUISIÇÃO ESTÁ DE'), 0, 'C');
            $pdf->SetXY(135, 208); $pdf->MultiCell(60, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Órgão'), 0, 'C');

            $pdf->SetXY(135, 213); $pdf->MultiCell(60, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Secretaria de Estado da Saúde'), 0, 'C');
            $pdf->SetXY(15, 213); $pdf->MultiCell(120, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'ACORDO COM O PROCESSO ORIGINAL - EM ____/____/______'), 0, 'C');
            $pdf->SetXY(15, 228); $pdf->MultiCell(120, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Responsável pelo Setor de Passagem - PTFD/AP'), 0, 'C');
            $pdf->SetXY(15, 238); $pdf->MultiCell(180, 5, 'obs:');


        // Dados do Usuário
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetXY(40, 66); $pdf->MultiCell(95, 5, date('d/m/Y', strtotime($data->date_service)), 0, 'C');
            $pdf->SetXY(135, 66); $pdf->MultiCell(60, 5, $data->number, 0, 'C');

            $pdf->SetXY(15, 91); $pdf->MultiCell(111, 15, $data->going_return ? 'VOLTA' : 'IDA', 0, 'C');

            $pdf->SetXY(15, 173); $pdf->MultiCell(60, 5, date('d/m/Y', strtotime($data->date_consult)), 0, 'C');
            $pdf->SetXY(75, 173); $pdf->MultiCell(120, 5, date('d/m/Y', strtotime($data->date_travel)), 0, 'C');

            $pdf->SetFont('Arial', '', 10);
            $pdf->SetXY(158, 96); $pdf->MultiCell(37, 10, $part->part, 0, 'C');

            if(!$data->companion) {
                $pdf->SetXY(36, 113); $pdf->MultiCell(120, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $patient->name));

                $pdf->SetXY(36, 118); $pdf->MultiCell(45, 5, $patient->cpf);
                $pdf->SetXY(78, 118); $pdf->MultiCell(40, 5, $patient->rg);
                $pdf->SetXY(135, 118); $pdf->MultiCell(47, 5, date('d/m/Y', strtotime($patient->birth_date)));
            }

            if($data->specialty){
                $specialty = Specialty::findOrFail($data->specialty);
                $pdf->SetXY(158, 111); $pdf->MultiCell(37, 4, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $specialty->specialty), 0, 'C');
            }

            if($data->companions) {
                $y = 0;
                $companion = Companion::whereIn('id', $data->companions)->get();
                foreach($companion as $companion) {
                    $pdf->SetXY(36, 123+$y); $pdf->MultiCell(120, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $companion->name));
    
                    $pdf->SetXY(36, 128+$y); $pdf->MultiCell(45, 5, $companion->cpf);
                    $pdf->SetXY(78, 128+$y); $pdf->MultiCell(40, 5, $companion->rg);
                    $pdf->SetXY(135, 128+$y); $pdf->MultiCell(47, 5, date('d/m/Y', strtotime($companion->birth_date)));
                    $y = 10;
                }
            }

            $pdf->SetXY(98.5, 213); $pdf->MultiCell(8, 5, date('d', strtotime($data->date_service)), 0, 'C');
            $pdf->SetXY(107.5, 213); $pdf->MultiCell(8, 5, date('m', strtotime($data->date_service)), 0, 'C');
            $pdf->SetXY(116.5, 213); $pdf->MultiCell(12, 5, date('Y', strtotime($data->date_service)), 0, 'C');

            $pdf->SetXY(25, 238); $pdf->MultiCell(160, 5,  iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $data->obs));


        $pdf->setTitle('Requisição de Passagem de ' . ($data->companion ? 'Acompanhantes de ' : '') . $patient->name, 1);
        $pdf->Output('I', 'Requisição de Passagem de ' . ($data->companion ? 'Acompanhantes de ' : '') . $patient->name . '.pdf', 1);

        exit;
    }

    public function report(Request $request) {
        $pdf = $this->fpdf;
        $pdf->AddPage('L');
        $pdf->SetAutoPageBreak(true, 0);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetFillColor(0, 176, 240);

        // Cabeçalho
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Image(public_path() . '/img/brasao.png', 138.5, 5, 20, 25);
            $pdf->SetXY(15, 33); $pdf->MultiCell(267, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'GOVERNO DO ESTADO DA AMAPÁ'), 0, 'C');
            $pdf->SetXY(15, 38); $pdf->MultiCell(267, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'SECRETARIA DE ESTADO DA SAÚDE'), 0, 'C');

            if($request->type == 'daily') {
                $pdf->SetXY(15, 43); $pdf->MultiCell(267, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'RELAÇÃO DE ATENDIMENTO DO DIA ' . date('d/m/Y', strtotime($request->day)) . ' (PASSAGEM)'), 0, 'C');
                $data = Travel::where([['date_service', $request->day], ['active', 1]])->get();
            } elseif($request->type == 'monthly') {
                switch($request->month){
                    case 1:
                        $month = 'Janeiro';
                        break;
                    case 2:
                        $month = 'Fevereiro';
                        break;
                    case 3:
                        $month = 'Março';
                        break;
                    case 4:
                        $month = 'Abril';
                        break;
                    case 5:
                        $month = 'Maio';
                        break;
                    case 6:
                        $month = 'Junho';
                        break;
                    case 7:
                        $month = 'Julho';
                        break;
                    case 8:
                        $month = 'Agosto';
                        break;
                    case 9:
                        $month = 'Setembro';
                        break;
                    case 10:
                        $month = 'Outubro';
                        break;
                    case 11:
                        $month = 'Novembro';
                        break;
                    case 12:
                        $month = 'Dezembro';
                        break;
                }

                $date = date($request->year . '-' . $request->month);
                $pdf->SetXY(15, 43); $pdf->MultiCell(267, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'RELAÇÃO DE ATENDIMENTO DO MÊS DE ' . mb_strtoupper($month) . ' DE ' . $request->year . ' (PASSAGEM)'), 0, 'C');
                $data = Travel::where('active', 1)->whereBetween('date_service', [date($date . '-1'), date($date . '-t')])->get();
            } elseif($request->type == 'yearly'){
                $pdf->SetXY(15, 43); $pdf->MultiCell(267, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'RELAÇÃO DE ATENDIMENTO DO ANO DE ' . $request->year . ' (PASSAGEM)'), 0, 'C');
                $data = Travel::where('active', 1)->whereBetween('date_service', [date($request->year . '-1-1'), date($request->year . '-12-t')])->get();
            } elseif($request->type == 'parts'){
                $part = Part::findOrFail($request->id);
                $pdf->SetXY(15, 43); $pdf->MultiCell(267, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'RELAÇÃO DE ATENDIMENTO DO TRECHO ' . $part->part . ' (PASSAGEM)'), 0, 'C');
                $data = Travel::where([['part', $request->id], ['active', 1]])->get();
            } elseif($request->type == 'specialties'){
                $specialty = Specialty::findOrFail($request->id);
                $pdf->SetXY(15, 43); $pdf->MultiCell(267, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'RELAÇÃO DE ATENDIMENTO DA ESPECIALIDADE ' . $specialty->specialty . ' (PASSAGEM)'), 0, 'C');
                $data = Travel::where([['specialty', $request->id], ['active', 1]])->get();
            } elseif($request->type == 'users'){
                $user = User::findOrFail($request->id);
                $pdf->SetXY(15, 43); $pdf->MultiCell(267, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'RELAÇÃO DE ATENDIMENTO REALIZADO POR ' . mb_strtoupper($user->name) . ' (PASSAGEM)'), 0, 'C');
                $data = Travel::where([['user', $request->id], ['active', 1]])->get();
            } elseif($request->type == 'patients'){
                $patient = Patient::findOrFail($request->id);
                $pdf->SetXY(15, 43); $pdf->MultiCell(267, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'RELAÇÃO DE ATENDIMENTO SOLICITADO POR ' . $patient->name . ' (PASSAGEM)'), 0, 'C');
                $data = Travel::where([['patient', $request->id], ['active', 1]])->get();
            } else {
                $pdf->SetXY(15, 43); $pdf->MultiCell(267, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'RELAÇÃO DE ATENDIMENTO (PASSAGEM)'), 0, 'C');
                $data = Travel::where('active', 1)->get();
            }

        $c = 0;
        foreach($data as $data){
            if($c == 6 && $pdf->PageNo() == 1) {
                $c = 0;
                $pdf->SetFont('Arial', '', 11);
                $pdf->SetXY(287, 200); $pdf->MultiCell(10, 10, $pdf->PageNo(), 0, 'C');
                $pdf->addPage('L');
            } elseif($c == 8 && $pdf->PageNo() > 1) {
                $c = 0;
                $pdf->SetFont('Arial', '', 11);
                $pdf->SetXY(287, 200); $pdf->MultiCell(10, 10, $pdf->PageNo(), 0, 'C');
                $pdf->addPage('L');
            }

            // Layout
                $pdf->SetFont('Arial', '', 9);
                $pdf->SetTextColor(255, 0, 0);

                $pdf->SetXY(15, ($pdf->PageNo() == 1 ? 50 : 10)+($c * 24)); $pdf->MultiCell(267, 4, 'PACIENTE:', $c == 0 ? 1 : 'LBR');
                $pdf->SetXY(15, ($pdf->PageNo() == 1 ? 54 : 14)+($c * 24)); $pdf->MultiCell(267, 4, ' ACOMP. 1:', 'LBR');
                $pdf->SetXY(15, ($pdf->PageNo() == 1 ? 58 : 18)+($c * 24)); $pdf->MultiCell(267, 4, ' ACOMP. 2:', 'LBR');

                for($j = 0; $j < 3; $j++){
                    $pdf->SetXY(147, ($pdf->PageNo() == 1 ? 50 : 10)+($j * 4)+($c * 24)); $pdf->MultiCell(40, 4, 'NASC.:');
                    $pdf->SetXY(187, ($pdf->PageNo() == 1 ? 50 : 10)+($j * 4)+($c * 24)); $pdf->MultiCell(50, 4, 'CPF:');
                    $pdf->SetXY(237, ($pdf->PageNo() == 1 ? 50 : 10)+($j * 4)+($c * 24)); $pdf->MultiCell(45, 4, 'RG:');
                }

                $pdf->SetXY(15, ($pdf->PageNo() == 1 ? 62 : 22)+($c * 24)); $pdf->MultiCell(267, 4, '   TRECHO:', 'LBR');
                $pdf->SetXY(35, ($pdf->PageNo() == 1 ? 62 : 22)+($c * 24)); $pdf->MultiCell(25, 4, '', 'L', 'C');
                $pdf->SetXY(60, ($pdf->PageNo() == 1 ? 62 : 22)+($c * 24)); $pdf->MultiCell(35, 4, 'IDA/VOLTA:', 'L');
                $pdf->SetXY(95, ($pdf->PageNo() == 1 ? 62 : 22)+($c * 24)); $pdf->MultiCell(45, 4, 'D. ATEND.:', 'L');
                $pdf->SetXY(140, ($pdf->PageNo() == 1 ? 62 : 22)+($c * 24)); $pdf->MultiCell(45, 4, 'D. VIAGEM:', 'L');
                $pdf->SetXY(185, ($pdf->PageNo() == 1 ? 62 : 22)+($c * 24)); $pdf->MultiCell(50, 4, 'D. CONSULTA:', 'L');
                $pdf->SetXY(235, ($pdf->PageNo() == 1 ? 62 : 22)+($c * 24)); $pdf->MultiCell(47, 4, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Nº REQUISIÇÃO:'), 'L');

                $pdf->SetXY(15, ($pdf->PageNo() == 1 ? 66 : 26)+($c * 24)); $pdf->MultiCell(267, 4, 'TEL:', 'LBR');
                $pdf->SetXY(24, ($pdf->PageNo() == 1 ? 66 : 26)+($c * 24)); $pdf->MultiCell(80, 4, 'PAC.:', 'L');
                $pdf->SetXY(104, ($pdf->PageNo() == 1 ? 66 : 26)+($c * 24)); $pdf->MultiCell(89, 4, 'ACOMP. 1:', 'L');
                $pdf->SetXY(193, ($pdf->PageNo() == 1 ? 66 : 26)+($c * 24)); $pdf->MultiCell(89, 4, 'ACOMP. 2:', 'L');
                $pdf->SetXY(15, ($pdf->PageNo() == 1 ? 70 : 30)+($c * 24)); $pdf->MultiCell(267, 4, '', 1, '', 1);

            // Data
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->SetTextColor(0, 0, 0);

                if(!$data->companion) {
                    $patient = Patient::findOrFail($data->patient);
                    $pdf->SetXY(32.75, ($pdf->PageNo() == 1 ? 50 : 10)+($c * 24)); $pdf->MultiCell(114.25, 4, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $patient->name));
                    $pdf->SetXY(158.5, ($pdf->PageNo() == 1 ? 50 : 10)+($c * 24)); $pdf->MultiCell(28.5, 4, date('d/m/Y', strtotime($patient->birth_date)));
                    $pdf->SetXY(195, ($pdf->PageNo() == 1 ? 50 : 10)+($c * 24)); $pdf->MultiCell(42, 4, $patient->cpf);
                    $pdf->SetXY(243.5, ($pdf->PageNo() == 1 ? 50 : 10)+($c * 24)); $pdf->MultiCell(38.5, 4, $patient->rg);
                }

                if($data->companions) {
                    $companions = Companion::whereIn('id', $data->companions)->get();
                    $y = 0;
                    foreach ($companions as $companion) {
                        $pdf->SetXY(32.75, ($pdf->PageNo() == 1 ? 54+$y : 14+$y)+($c * 24)); $pdf->MultiCell(114.25, 4, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $companion->name));
                        $pdf->SetXY(158.5, ($pdf->PageNo() == 1 ? 54+$y : 14+$y)+($c * 24)); $pdf->MultiCell(28.5, 4, date('d/m/Y', strtotime($companion->birth_date)));
                        $pdf->SetXY(195, ($pdf->PageNo() == 1 ? 54+$y : 14+$y)+($c * 24)); $pdf->MultiCell(42, 4, $companion->cpf);
                        $pdf->SetXY(243.5, ($pdf->PageNo() == 1 ? 54+$y : 14+$y)+($c * 24)); $pdf->MultiCell(38.5, 4, $companion->rg);
                        
                        $y = 4;
                    }
                }

                $part = Part::findOrFail($data->part);
                $pdf->SetXY(35, ($pdf->PageNo() == 1 ? 62 : 22)+($c * 24)); $pdf->MultiCell(25, 4, $part->part, 'L', 'C');
                $pdf->SetXY(78.5, ($pdf->PageNo() == 1 ? 62 : 22)+($c * 24)); $pdf->MultiCell(17.5, 4, $data->return ? 'VOLTA' : 'IDA');
                $pdf->SetXY(112.5, ($pdf->PageNo() == 1 ? 62 : 22)+($c * 24)); $pdf->MultiCell(27.5, 4, date('d/m/Y', strtotime($data->date_service)));
                $pdf->SetXY(158.25, ($pdf->PageNo() == 1 ? 62 : 22)+($c * 24)); $pdf->MultiCell(26.75, 4, date('d/m/Y', strtotime($data->date_return ? $data->date_return : $data->date_going)));
                $pdf->SetXY(208, ($pdf->PageNo() == 1 ? 62 : 22)+($c * 24)); $pdf->MultiCell(27, 4, $data->date_consult ? date('d/m/Y', strtotime($data->date_consult)) : '');
                $pdf->SetXY(261, ($pdf->PageNo() == 1 ? 62 : 22)+($c * 24)); $pdf->MultiCell(21, 4, $data->number);

                $pdf->SetFont('Arial', 'B', 8);
                $pdf->SetXY(33, ($pdf->PageNo() == 1 ? 66 : 26)+($c * 24)); !$data->companion ? $pdf->MultiCell(71, 4, $patient->phone) : '';
                if($data->companions) {
                    $x = 0;
                    foreach ($companions as $companion) {
                        $pdf->SetXY(121+$x, ($pdf->PageNo() == 1 ? 66 : 26)+($c * 24)); $companion->phone ? $pdf->MultiCell(72, 4, $companion->phone) : '';
                        $x = 89;
                    }
                }

                $c++;
        }

        $pdf->SetFont('Arial', '', 11);
        $pdf->SetXY(287, 200); $pdf->MultiCell(10, 10, $pdf->PageNo(), 0, 'C');

        if($request->type == 'daily') {
            $pdf->setTitle('Relação de Atendimento do Dia ' . date('d/m/Y', strtotime($request->day)), true);
            $pdf->Output('I', 'Relação de Atendimento do Dia ' . date('d-m-Y', strtotime($request->day)) . '.pdf', true);
        } elseif($request->type == 'monthly') {
            $pdf->setTitle('Relação de Atendimento do Mês de ' . $month . ' de ' . $request->year, true);
            $pdf->Output('I', 'Relação de Atendimento do Mês de ' . $month . ' de ' . $request->year . '.pdf', true);
        } elseif($request->type == 'yearly'){
            $pdf->setTitle('Relação de Atendimento do Ano de ' . $request->year, true);
            $pdf->Output('I', 'Relação de Atendimento do Ano de ' . $request->year . '.pdf', true);
        } elseif($request->type == 'parts'){
            $pdf->setTitle('Relação de Atendimento do Trecho ' . $part->part, true);
            $part = str_replace('/', '-', $part->part);
            $pdf->Output('I', 'Relação de Atendimento do Trecho ' . $part . '.pdf', true);
        } elseif($request->type == 'specialties'){
            $pdf->setTitle('Relação de Atendimento da Especialidade ' . ucwords(mb_strtolower($specialty->specialty)), true);
            $pdf->Output('I', 'Relação de Atendimento da Especialidade ' . ucwords(mb_strtolower($specialty->specialty)) . '.pdf', true);
        } else {
            $pdf->setTitle('Relação de Atendimento', true);
            $pdf->Output('I', 'Relação de Atendimento.pdf', true);
        }

        exit;
    }    
}