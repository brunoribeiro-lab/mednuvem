<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemMenu;
use App\Providers\AuthServiceProvider;
use App\Models\SystemSubmenu;
use App\Models\SystemSubSubmenu;
use App\Models\Medico;
use App\Providers\MenusCollection;
use App\Models\Setor;
use App\Models\Função;
use App\Models\Exame;

class MedicosController extends Controller {

    public function index() {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['medicos'])))
            return view('error.404SGS');

        $menu = SystemMenu::__construir(); // carrega o menu do SGS
        $data = [// data usada nessa página
            'default' => MenusCollection::$menus['medicos'],
            'identificador' => [
                'padrão' => SystemMenu::identificador(),
                'add' => SystemMenu::identificador(true)
            ],
        ];
        $data['actions'] = AuthServiceProvider::acoes(...array_values(MenusCollection::$menus['medicos']));
        $data['menu'] = SystemMenu::find($data['default']['menu']);
        if ($data['default']['submenu'])
            $data['submenu'] = SystemSubmenu::find($data['default']['submenu']);

        if ($data['default']['subsubmenu'])
            $data['subsubmenu'] = SystemSubsubmenu::find($data['default']['subsubmenu']);

        return view('medico.index')
                        ->with('data', $data)
                        ->with('menu', $menu);
    }

    public function historico(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_HISTORIC', ...array_values(MenusCollection::$menus['medicos'])))
            return view('error.404SGS');

        return view('configuracoes.table.historico', ['data' => $id]);
    }

    /**
     *  Listar tabela de usuários
     */
    public function datatableHistorico(Request $request, string $id) {
        return Medico::datatableHistorico($request, $id);
    }

    public function indexSetor() {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['setor'])))
            return view('error.404SGS');

        $menu = SystemMenu::__construir(); // carrega o menu do SGS
        $data = [// data usada nessa página
            'default' => MenusCollection::$menus['setor'],
            'identificador' => [
                'padrão' => SystemMenu::identificador(),
                'add' => SystemMenu::identificador(true)
            ],
        ];
        $data['actions'] = AuthServiceProvider::acoes(...array_values(MenusCollection::$menus['setor']));
        $data['menu'] = SystemMenu::find($data['default']['menu']);
        if ($data['default']['submenu'])
            $data['submenu'] = SystemSubmenu::find($data['default']['submenu']);

        if ($data['default']['subsubmenu'])
            $data['subsubmenu'] = SystemSubsubmenu::find($data['default']['subsubmenu']);

        return view('configuracoes.medico.setor')
                        ->with('data', $data)
                        ->with('menu', $menu);
    }

    public function indexFunção() {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['função'])))
            return view('error.404SGS');

        $menu = SystemMenu::__construir(); // carrega o menu do SGS
        $data = [// data usada nessa página
            'default' => MenusCollection::$menus['função'],
            'identificador' => [
                'padrão' => SystemMenu::identificador(),
                'add' => SystemMenu::identificador(true)
            ],
        ];
        $data['actions'] = AuthServiceProvider::acoes(...array_values(MenusCollection::$menus['função']));
        $data['menu'] = SystemMenu::find($data['default']['menu']);
        if ($data['default']['submenu'])
            $data['submenu'] = SystemSubmenu::find($data['default']['submenu']);

        if ($data['default']['subsubmenu'])
            $data['subsubmenu'] = SystemSubsubmenu::find($data['default']['subsubmenu']);

        return view('configuracoes.medico.funcao')
                        ->with('data', $data)
                        ->with('menu', $menu);
    }

    public function indexExames() {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['exames'])))
            return view('error.404SGS');

        $menu = SystemMenu::__construir(); // carrega o menu do SGS
        $data = [// data usada nessa página
            'default' => MenusCollection::$menus['exames'],
            'identificador' => [
                'padrão' => SystemMenu::identificador(),
                'add' => SystemMenu::identificador(true)
            ],
        ];
        $data['actions'] = AuthServiceProvider::acoes(...array_values(MenusCollection::$menus['exames']));
        $data['menu'] = SystemMenu::find($data['default']['menu']);
        if ($data['default']['submenu'])
            $data['submenu'] = SystemSubmenu::find($data['default']['submenu']);

        if ($data['default']['subsubmenu'])
            $data['subsubmenu'] = SystemSubsubmenu::find($data['default']['subsubmenu']);

        return view('configuracoes.medico.exame')
                        ->with('data', $data)
                        ->with('menu', $menu);
    }

    public function datatable(Request $request) {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['medicos'])))
            return view('error.404SGS');

        return Medico::datatable($request);
    }

    public function datatableSetor(Request $request) {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['setor'])))
            return view('error.404SGS');

        return Setor::datatable($request);
    }

    public function datatableFunção(Request $request) {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['função'])))
            return view('error.404SGS');

        return Função::datatable($request);
    }

    public function datatableExame(Request $request) {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['exames'])))
            return view('error.404SGS');

        return Exame::datatable($request);
    }

    public function create(Request $request) {
        if (!AuthServiceProvider::acao('ACCESS_ADD', ...array_values(MenusCollection::$menus['medicos'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $data = [
            'modal' => $request->modal
        ];
        $setores = Setor::listar();
        return view('medico.add.index', ['data' => $data, 'setores' => $setores]);
    }

    public function createSetor(Request $request) {
        if (!AuthServiceProvider::acao('ACCESS_ADD', ...array_values(MenusCollection::$menus['setor'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        return view('configuracoes.medico.add.setor');
    }

    public function createFunção(Request $request) {
        if (!AuthServiceProvider::acao('ACCESS_ADD', ...array_values(MenusCollection::$menus['função'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $data = [
            'setores' => Setor::listar()
        ];

        return view('configuracoes.medico.add.funcao', ['data' => $data]);
    }

    public function createExames(Request $request) {
        if (!AuthServiceProvider::acao('ACCESS_ADD', ...array_values(MenusCollection::$menus['exames'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $setores = Setor::listar();
        return view('configuracoes.medico.add.exame', ['setores' => $setores]);
    }

    public function store(Request $request) {
        $formCampo = new Medico();
        return $formCampo->salvar($request, 0);
    }

    public function storeSetor(Request $request) {
        $setor = new Setor();
        return $setor->salvar($request, 0);
    }

    public function storeFunção(Request $request) {
        $formCampo = new Função();
        return $formCampo->salvar($request, 0);
    }

    public function storeExame(Request $request) {
        $formCampo = new Exame();
        return $formCampo->salvar($request, 0);
    }

    public function show(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_PREVIEW', ...array_values(MenusCollection::$menus['medicos'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $motorista = Medico::buscar($id);
        if ($motorista)
            return view('medico.ver.index', ['data' => $motorista]);

        return view('error.404Ajax');
    }

    public function showSetor(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_PREVIEW', ...array_values(MenusCollection::$menus['setor'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $setor = Setor::buscar($id);
        if ($setor)
            return view('configuracoes.medico.ver.setor', ['data' => $setor]);

        return view('error.404Ajax');
    }

    public function showFunção(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_PREVIEW', ...array_values(MenusCollection::$menus['função'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $motorista = Função::buscar($id);
        if ($motorista)
            return view('configuracoes.medico.ver.funcao', ['data' => $motorista]);

        return view('error.404Ajax');
    }

    public function showExame(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_PREVIEW', ...array_values(MenusCollection::$menus['exames'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $motorista = Exame::buscar($id);
        if ($motorista)
            return view('configuracoes.medico.ver.exame', ['data' => $motorista]);

        return view('error.404Ajax');
    }

    public function pegarFuncoes(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_PREVIEW', ...array_values(MenusCollection::$menus['exames'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $data = Função::listarPorSetor($id);
        if (!$data)
            return response()->json(['error' => true, 'msg' => 'Nenhuma função foi encontrada']);


        return response()->json(['error' => false, 'msg' => 'OK', 'resultado' => $data->toArray()]);
    }

    public function edit(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_UPDATE', ...array_values(MenusCollection::$menus['medicos'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $data = Medico::buscar($id);
        if (!$data)
            return view('error.404Ajax');

        $setores = Setor::listar();
        return view('medico.edit.index')
                        ->with('data', $data)
                        ->with('setores', $setores)
                        ->with('funcoes', Função::listarPorSetor($data->setor));
    }

    public function editSetor(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_UPDATE', ...array_values(MenusCollection::$menus['setor'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $setor = Setor::buscar($id);
        if (!$setor)
            return view('error.404Ajax');

        return view('configuracoes.medico.edit.setor')
                        ->with('data', $setor);
    }

    public function editFunção(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_UPDATE', ...array_values(MenusCollection::$menus['função'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $data = Função::buscar($id);
        if (!$data)
            return view('error.404Ajax');

        $setores = Setor::listar();
        return view('configuracoes.medico.edit.funcao')
                        ->with('data', $data)
                        ->with('setores', $setores);
    }

    public function editExame(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_UPDATE', ...array_values(MenusCollection::$menus['exames'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $data = Exame::buscar($id);
        if (!$data)
            return view('error.404Ajax');

        return view('configuracoes.medico.edit.exame')
                        ->with('data', $data)
                        ->with('setores', Setor::listar())
                        ->with('funcoes', Função::listarPorSetor($data->setor_id));
    }

    public function update(Request $request) {
        $data = new Medico();
        return $data->salvar($request, $request->input('id'));
    }

    public function updateSetor(Request $request) {
        $data = new Setor();
        return $data->salvar($request, $request->input('id'));
    }

    public function updateFunção(Request $request) {
        $data = new Função();
        return $data->salvar($request, $request->input('id'));
    }

    public function updateExame(Request $request) {
        $data = new Exame();
        return $data->salvar($request, $request->input('id'));
    }

    public function destroyExame(Request $request, string $id = null) {
        if (!AuthServiceProvider::acao('ACCESS_REMOVE', ...array_values(MenusCollection::$menus['exames'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $ids = [$id];
        if (!$id) {
            $ids = $request->input('checkbox');
            if (!count($ids))
                return response()->json(['error' => true, 'msg' => 'Erro ao excluir']);
        }

        return Exame::remover($ids);
    }

    public function destroySetor(Request $request, string $id = null) {
        if (!AuthServiceProvider::acao('ACCESS_REMOVE', ...array_values(MenusCollection::$menus['setor'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $ids = [$id];
        if (!$id) {
            $ids = $request->input('checkbox');
            if (!count($ids))
                return response()->json(['error' => true, 'msg' => 'Erro ao excluir']);
        }

        return Setor::remover($ids);
    }

    public function destroyFunção(Request $request, string $id = null) {
        if (!AuthServiceProvider::acao('ACCESS_REMOVE', ...array_values(MenusCollection::$menus['função'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $ids = [$id];
        if (!$id) {
            $ids = $request->input('checkbox');
            if (!count($ids))
                return response()->json(['error' => true, 'msg' => 'Erro ao excluir']);
        }

        return Função::remover($ids);
    }

    public function destroy(Request $request, string $id = null) {
        if (!AuthServiceProvider::acao('ACCESS_REMOVE', ...array_values(MenusCollection::$menus['medicos'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $ids = [$id];
        if (!$id) {
            $ids = $request->input('checkbox');
            if (!count($ids))
                return response()->json(['error' => true, 'msg' => 'Erro ao excluir']);
        }

        return Medico::remover($ids);
    }
}
