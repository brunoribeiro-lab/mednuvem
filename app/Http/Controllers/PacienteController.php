<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemMenu;
use App\Providers\AuthServiceProvider;
use App\Models\SystemSubmenu;
use App\Models\SystemSubSubmenu;
use App\Models\Paciente;
use App\Models\Rua;
use App\Models\Bairro;
use Illuminate\Support\Facades\Auth;
use App\Models\VariaveisDaOperacao;
use Illuminate\Support\Facades\Session;
use App\Providers\MenusCollection;
use App\Models\VariavelDoSistema;
use App\Models\TiposDocumentos;
use App\Models\Exame;
use App\Models\Documentos;

class PacienteController extends Controller {

    /**
     * Display a listing of the resource.
     */
    public function index() {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['pacientes'])))
            return view('error.404SGS');

        $menu = SystemMenu::__construir(); // carrega o menu do SGS
        $data = [// data usada nessa página
            'default' => MenusCollection::$menus['pacientes'],
            'identificador' => [
                'padrão' => SystemMenu::identificador(),
                'add' => SystemMenu::identificador(true)
            ],
        ];
        $data['actions'] = AuthServiceProvider::acoes(...array_values(MenusCollection::$menus['pacientes']));
        $data['menu'] = SystemMenu::find($data['default']['menu']);
        if ($data['default']['submenu'])
            $data['submenu'] = SystemSubmenu::find($data['default']['submenu']);

        if ($data['default']['subsubmenu'])
            $data['subsubmenu'] = SystemSubsubmenu::find($data['default']['subsubmenu']);

        return view('paciente.index')
                        ->with('data', $data)
                        ->with('menu', $menu);
    }

    /**
     *  Listar tabela de usuários
     */
    public function datatable(Request $request) {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['pacientes'])))
            return view('error.404SGS');

        return Paciente::datatable($request);
    }

    public function download(string $id) {
        return Documentos::baixar($id);
    }

    /**
     *  Listar tabela de usuários
     */
    public function datatableUpload(string $id, Request $request) {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['pacientes'])))
            return view('error.404SGS');

        return Documentos::datatable($request, $id);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request) {
        if (!AuthServiceProvider::acao('ACCESS_ADD', ...array_values(MenusCollection::$menus['pacientes'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $data = [
            'modal' => $request->modal
        ];
        return view('paciente.add.index', ['data' => $data, 'config' => VariavelDoSistema::first()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $formCampo = new Paciente();
        return $formCampo->salvar($request, 0);
    }

    public function prontuario(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_PREVIEW', ...array_values(MenusCollection::$menus['pacientes'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $where = [
            'pacientes.deletado' => 0,
            'pacientes.id' => $id
        ];

        if (!Session::get('is_root'))
            $where['pacientes.grupo'] = Auth::user()->group;

        $cliente = Paciente::join('users as creator', 'pacientes.criado_por', '=', 'creator.user_id')
                ->join('users as source', 'pacientes.grupo', '=', 'source.user_id')
                ->leftJoin('users as updater', 'pacientes.atualizado_por', '=', 'updater.user_id')
                ->where($where)
                ->select(
                        'pacientes.*',
                        'creator.user_first_name as creator_first_name',
                        'creator.user_last_name as creator_last_name',
                        'updater.user_first_name as updater_first_name',
                        'updater.user_last_name as updater_last_name',
                )
                ->selectRaw('CONCAT(source.user_first_name, IFNULL(CONCAT(" ", source.user_last_name), "")) as source_fullname')
                ->selectRaw('CONCAT(creator.user_first_name, IFNULL(CONCAT(" ", creator.user_last_name), "")) as creator_fullname')
                ->selectRaw('CONCAT(updater.user_first_name, IFNULL(CONCAT(" ", updater.user_last_name), "")) as updater_fullname')
                ->first();

        if (!$cliente)
            return view('error.404Ajax');

        $tipos_documentos = TiposDocumentos::where('deletado', 0)->orderBy("nome", "ASC")->get();
        $exames = Exame::listar();
        return view('paciente.ver.prontuario', ['data' => $cliente, 'tipo_documentos' => $tipos_documentos, 'exames' => $exames]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_PREVIEW', ...array_values(MenusCollection::$menus['pacientes'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $where = [
            'pacientes.deletado' => 0,
            'pacientes.id' => $id
        ];

        if (!Session::get('is_root'))
            $where['pacientes.grupo'] = Auth::user()->group;

        $cliente = Paciente::join('users as creator', 'pacientes.criado_por', '=', 'creator.user_id')
                ->join('users as source', 'pacientes.grupo', '=', 'source.user_id')
                ->leftJoin('users as updater', 'pacientes.atualizado_por', '=', 'updater.user_id')
                ->where($where)
                ->select(
                        'pacientes.*',
                        'creator.user_first_name as creator_first_name',
                        'creator.user_last_name as creator_last_name',
                        'updater.user_first_name as updater_first_name',
                        'updater.user_last_name as updater_last_name',
                )
                ->selectRaw('CONCAT(source.user_first_name, IFNULL(CONCAT(" ", source.user_last_name), "")) as source_fullname')
                ->selectRaw('CONCAT(creator.user_first_name, IFNULL(CONCAT(" ", creator.user_last_name), "")) as creator_fullname')
                ->selectRaw('CONCAT(updater.user_first_name, IFNULL(CONCAT(" ", updater.user_last_name), "")) as updater_fullname')
                ->first();

        if ($cliente) {
            return view('paciente.ver.index', ['data' => $cliente]);
        }
        return view('error.404Ajax');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_UPDATE', ...array_values(MenusCollection::$menus['pacientes'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $data = Paciente::where('id', $id)
                ->where('deletado', 0)
                ->first();
        if (!$data)
            return view('error.404Ajax');


        return view('paciente.edit.index')
                        ->with('data', $data)
                        ->with('config', VariavelDoSistema::first());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request) {
        $data = new Paciente();
        return $data->salvar($request, $request->input('id'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id = null) {
        if (!AuthServiceProvider::acao('ACCESS_REMOVE', ...array_values(MenusCollection::$menus['pacientes'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $ids = [$id];
        if (!$id) {
            $ids = $request->input('checkbox');
            if (!count($ids))
                return response()->json(['error' => true, 'msg' => 'Erro ao excluir']);
        }

        return Paciente::remover($ids);
    }

    public function upload(Request $request) {
        $paciente = new Documentos();
        return $paciente->enviar($request);
    }

    public function showFiltrar(Request $request) {
        $tipo = $request->input('tipo');
        $date = $request->input('date');
        $custom = $request->input('custom');
        $start = $request->input('start');
        $end = $request->input('end');
        $tipos = TiposDocumentos::where('deletado', 0)->orderBy("nome", "ASC")->get();
        return view('paciente.filtrar.documentos')
                        ->with('tipos', $tipos)
                        ->with('tipo', $tipo)
                        ->with('date', $date)
                        ->with('custom', $custom)
                        ->with('start', $start)
                        ->with('end', $end);
    }
}
