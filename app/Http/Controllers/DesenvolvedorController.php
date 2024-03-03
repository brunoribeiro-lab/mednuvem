<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemMenu;
use App\Providers\AuthServiceProvider;
use App\Models\SystemSubmenu;
use App\Models\SystemSubSubmenu;
use App\Models\Doc;
use App\Models\Video;
use Illuminate\Support\Facades\DB;
use App\Models\SystemCron;
use App\Models\Logs;
use App\Models\VariavelDoSistema;
use App\Providers\MenusCollection;
use App\Models\FinanceiroConfig;
use App\Models\DynamicEmail;

class DesenvolvedorController extends Controller {

    public function indexLogos() {
        if (!AuthServiceProvider::acao('ACCESS_FORM', ...array_values(MenusCollection::$menus['logos'])))
            return view('error.404SGS');

        $menu = SystemMenu::__construir(); // carrega o menu do SGS
        $data = [// data usada nessa página
            'default' => MenusCollection::$menus['logos'],
            'identificador' => [
                'padrão' => SystemMenu::identificador(),
                'add' => SystemMenu::identificador(true)
            ],
        ];

        $data['menu'] = SystemMenu::find($data['default']['menu']);
        $data['submenu'] = SystemSubmenu::find($data['default']['submenu']);
        if ($data['default']['subsubmenu'])
            $data['subsubmenu'] = SystemSubsubmenu::find($data['default']['subsubmenu']);

        $data['config'] = VariavelDoSistema::where('id', 1)->first();

        return view('configuracoes.desenvolvedor.logos')
                        ->with('data', $data)
                        ->with('menu', $menu);
    }

    public function indexAcesso() {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['acessos'])))
            return view('error.404SGS');

        $menu = SystemMenu::__construir(); // carrega o menu do SGS
        $data = [// data usada nessa página
            'default' => MenusCollection::$menus['acessos'],
            'identificador' => [
                'padrão' => SystemMenu::identificador(),
                'add' => SystemMenu::identificador(true)
            ],
        ];

        $data['actions'] = AuthServiceProvider::acoes(...array_values(MenusCollection::$menus['acessos']));
        $data['menu'] = SystemMenu::find($data['default']['menu']);
        $data['submenu'] = SystemSubmenu::find($data['default']['submenu']);
        if ($data['default']['subsubmenu'])
            $data['subsubmenu'] = SystemSubsubmenu::find($data['default']['subsubmenu']);

        $acesso = VariavelDoSistema::first();
        return view('configuracoes.desenvolvedor.acesso')
                        ->with('data', $data)
                        ->with('menu', $menu)
                        ->with('acesso', $acesso);
    }

    public function indexLogs() {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['logs'])))
            return view('error.404SGS');

        $menu = SystemMenu::__construir(); // carrega o menu do SGS
        $data = [// data usada nessa página
            'default' => MenusCollection::$menus['logs'],
            'identificador' => [
                'padrão' => SystemMenu::identificador(),
                'add' => SystemMenu::identificador(true)
            ],
        ];

        $data['actions'] = AuthServiceProvider::acoes(...array_values(MenusCollection::$menus['logs']));
        $data['menu'] = SystemMenu::find($data['default']['menu']);
        $data['submenu'] = SystemSubmenu::find($data['default']['submenu']);
        if ($data['default']['subsubmenu'])
            $data['subsubmenu'] = SystemSubsubmenu::find($data['default']['subsubmenu']);

        return view('configuracoes.desenvolvedor.logs')
                        ->with('data', $data)
                        ->with('menu', $menu);
    }

    /**
     * Display a listing of the resource.
     */
    public function indexDoc() {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['documentacao'])))
            return view('error.404SGS');

        $menu = SystemMenu::__construir(); // carrega o menu do SGS
        $data = [// data usada nessa página
            'default' => MenusCollection::$menus['documentacao'],
            'identificador' => [
                'padrão' => SystemMenu::identificador(),
                'add' => SystemMenu::identificador(true)
            ],
        ];

        $data['actions'] = AuthServiceProvider::acoes(...array_values(MenusCollection::$menus['documentacao']));
        $data['menu'] = SystemMenu::find($data['default']['menu']);
        $data['submenu'] = SystemSubmenu::find($data['default']['submenu']);
        if ($data['default']['subsubmenu'])
            $data['subsubmenu'] = SystemSubsubmenu::find($data['default']['subsubmenu']);

        return view('configuracoes.desenvolvedor.documentacao')
                        ->with('data', $data)
                        ->with('menu', $menu);
    }

    public function indexTarefas() {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['tarefas'])))
            return view('error.404SGS');

        $menu = SystemMenu::__construir(); // carrega o menu do SGS
        $data = [// data usada nessa página
            'default' => MenusCollection::$menus['tarefas'],
            'identificador' => [
                'padrão' => SystemMenu::identificador(),
                'add' => SystemMenu::identificador(true)
            ],
        ];

        $data['actions'] = AuthServiceProvider::acoes(...array_values(MenusCollection::$menus['tarefas']));
        $data['menu'] = SystemMenu::find($data['default']['menu']);
        $data['submenu'] = SystemSubmenu::find($data['default']['submenu']);
        if ($data['default']['subsubmenu'])
            $data['subsubmenu'] = SystemSubsubmenu::find($data['default']['subsubmenu']);

        return view('configuracoes.desenvolvedor.tarefa')
                        ->with('data', $data)
                        ->with('menu', $menu);
    }

    /**
     * Display a listing of the resource.
     */
    public function indexMenu() {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['menus'])))
            return view('error.404SGS');

        $menu = SystemMenu::__construir(); // carrega o menu do SGS
        $data = [// data usada nessa página
            'default' => MenusCollection::$menus['menus'],
            'identificador' => [
                'padrão' => SystemMenu::identificador(),
                'add' => SystemMenu::identificador(true)
            ],
        ];

        $data['actions'] = AuthServiceProvider::acoes(...array_values(MenusCollection::$menus['menus']));
        $data['menu'] = SystemMenu::find($data['default']['menu']);
        $data['submenu'] = SystemSubmenu::find($data['default']['submenu']);
        if ($data['default']['subsubmenu'])
            $data['subsubmenu'] = SystemSubsubmenu::find($data['default']['subsubmenu']);

        return view('configuracoes.desenvolvedor.menu')
                        ->with('data', $data)
                        ->with('menu', $menu);
    }

    /**
     * Display a listing of the resource.
     */
    public function indexSubMenu(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['menus'])))
            return view('error.404SGS');

        $menu = $id;
        return view('configuracoes.desenvolvedor.tabela.submenu')
                        ->with('menu', $menu);
    }

    public function indexSubSubMenu(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['menus'])))
            return view('error.404SGS');

        $submenu = $id;
        return view('configuracoes.desenvolvedor.tabela.subsubmenu')
                        ->with('submenu', $submenu);
    }

    /**
     * Display a listing of the resource.
     */
    public function indexVideo() {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['videos-aulas'])))
            return view('error.404SGS');

        $menu = SystemMenu::__construir(); // carrega o menu do SGS
        $data = [// data usada nessa página
            'default' => MenusCollection::$menus['videos-aulas'],
            'identificador' => [
                'padrão' => SystemMenu::identificador(),
                'add' => SystemMenu::identificador(true)
            ],
        ];

        $data['actions'] = AuthServiceProvider::acoes(...array_values(MenusCollection::$menus['videos-aulas']));
        $data['menu'] = SystemMenu::find($data['default']['menu']);
        $data['submenu'] = SystemSubmenu::find($data['default']['submenu']);
        if ($data['default']['subsubmenu'])
            $data['subsubmenu'] = SystemSubsubmenu::find($data['default']['subsubmenu']);

        return view('configuracoes.desenvolvedor.videos')
                        ->with('data', $data)
                        ->with('menu', $menu);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createDoc() {
        if (!AuthServiceProvider::acao('ACCESS_ADD', ...array_values(MenusCollection::$menus['documentacao'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        return view('configuracoes.desenvolvedor.add.documentacao');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createVid() {
        if (!AuthServiceProvider::acao('ACCESS_ADD', ...array_values(MenusCollection::$menus['videos-aulas'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $menu = SystemMenu::__construir(); // carrega o menu do SGS
        return view('configuracoes.desenvolvedor.add.video')
                        ->with('menu', $menu);
    }

    /**
     *  Listar tabela de usuários
     */
    public function datatableDoc(Request $request) {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['documentacao'])))
            return view('error.404SGS');

        return Doc::datatable($request);
    }

    public function datatableVid(Request $request) {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['videos-aulas'])))
            return view('error.404SGS');

        return Video::datatable($request);
    }

    public function datatableMenu(Request $request) {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['menus'])))
            return view('error.404SGS');

        return SystemMenu::datatable($request);
    }

    public function datatableSubMenu(Request $request, string $id) {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['menus'])))
            return view('error.404SGS');

        return SystemSubmenu::datatable($request, $id);
    }

    public function datatableSubSubMenu(Request $request, string $id) {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['menus'])))
            return view('error.404SGS');

        return SystemSubSubmenu::datatable($request, $id);
    }

    public function datatableTarefas(Request $request) {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['tarefas'])))
            return view('error.404SGS');

        return SystemCron::datatable($request);
    }

    public function datatableLogs(Request $request) {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['documentacao'])))
            return view('error.404SGS');

        return Logs::datatable($request);
    }

    public function storeFinanceiro(Request $request) {
        $var = new FinanceiroConfig();
        return $var->store($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeLogos(Request $request) {
        $var = new VariavelDoSistema();
        return $var->storeLogos($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeDoc(Request $request) {
        $doc = new Doc();
        return $doc->salvar($request, 0);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeVid(Request $request) {
        $doc = new Video();
        return $doc->salvar($request, 0);
    }

    /**
     * Display the specified resource.
     */
    public function showDoc(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_PREVIEW', ...array_values(MenusCollection::$menus['documentacao'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        // Consulta para buscar a empresa com o id fornecido
        $data = Doc::where([
                    'id' => $id
                ])
                ->first();

        if ($data) {
            return view('configuracoes.desenvolvedor.ver.documentacao', ['data' => $data]);
        }
        return view('error.404Ajax');
    }

    /**
     * Display the specified resource.
     */
    public function showSubMenu(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_PREVIEW', ...array_values(MenusCollection::$menus['menus'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $data = SystemSubmenu::where([
                    'ID' => $id
                ])
                ->first();

        if ($data) {
            return view('configuracoes.desenvolvedor.ver.submenu', ['data' => $data]);
        }
        return view('error.404Ajax');
    }

    /**
     * Display the specified resource.
     */
    public function showSubSubMenu(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_PREVIEW', ...array_values(MenusCollection::$menus['menus'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $data = SystemSubSubmenu::where([
                    'ID' => $id
                ])
                ->first();

        if ($data) {
            return view('configuracoes.desenvolvedor.ver.subsubmenu', ['data' => $data]);
        }
        return view('error.404Ajax');
    }

    /**
     * Display the specified resource.
     */
    public function showMenu(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_PREVIEW', ...array_values(MenusCollection::$menus['menus'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $data = SystemMenu::where([
                    'ID' => $id
                ])
                ->first();

        if ($data) {
            return view('configuracoes.desenvolvedor.ver.menu', ['data' => $data]);
        }
        return view('error.404Ajax');
    }

    /**
     * Display the specified resource.
     */
    public function showLog(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_PREVIEW', ...array_values(MenusCollection::$menus['logs'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $data = Logs::where([
                    'id' => $id
                ])
                ->first();

        if (!$data)
            return view('error.404Ajax');

        return view('configuracoes.desenvolvedor.ver.log', ['data' => $data]);
    }

    /**
     * Display the specified resource.
     */
    public function showVid(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_PREVIEW', ...array_values(MenusCollection::$menus['videos-aulas'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $data = Video::where([
                    'videos.deleted' => 0,
                    'videos.id' => $id
                ])
                ->join('users as u_criado_por', 'videos.created_by', '=', 'u_criado_por.user_id')
                ->leftJoin('users as u_atualizado_por', 'videos.updated_by', '=', 'u_atualizado_por.user_id')
                ->select('videos.*',
                        DB::raw("CONCAT(u_criado_por.user_first_name, ' ', IFNULL(u_criado_por.user_last_name, '')) as criado_por_name"), // Concatenated column for criado_por
                        DB::raw("CONCAT(u_atualizado_por.user_first_name, ' ', IFNULL(u_atualizado_por.user_last_name, '')) as atualizado_por_name")  // Concatenated column for atualizado_por
                )
                ->first();

        if ($data) {
            return view('configuracoes.desenvolvedor.ver.video', ['data' => $data]);
        }
        return view('error.404Ajax');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function editDoc(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_UPDATE', ...array_values(MenusCollection::$menus['documentacao'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $data = Doc::where('id', $id)
                ->first();
        if (!$data)
            return view('error.404Ajax');

        return view('configuracoes.desenvolvedor.edit.documentacao')
                        ->with('data', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function editMenu(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_UPDATE', ...array_values(MenusCollection::$menus['menus'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $data = SystemMenu::where('ID', $id)
                ->first();
        if (!$data)
            return view('error.404Ajax');

        $usados = [];
        $usadosQuery = SystemMenu::where('STATUS', 1)->get();
        foreach ($usadosQuery as $usado) {
            $usados[] = $usado->POSITION;
        }

        return view('configuracoes.desenvolvedor.edit.menu')
                        ->with('data', $data)
                        ->with('usados', $usados);
    }

    public function editSubMenu(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_UPDATE', ...array_values(MenusCollection::$menus['menus'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $data = SystemSubmenu::where('ID', $id)
                ->first();
        if (!$data)
            return view('error.404Ajax');

        $usados = [];
        $usadosQuery = SystemSubmenu::where('STATUS', 1)->where('MENU', $data->MENU)->get();
        foreach ($usadosQuery as $usado) {
            $usados[] = $usado->POSITION;
        }

        return view('configuracoes.desenvolvedor.edit.submenu')
                        ->with('data', $data)
                        ->with('usados', $usados);
    }

    public function editSubSubMenu(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_UPDATE', ...array_values(MenusCollection::$menus['menus'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $data = SystemSubSubmenu::where('ID', $id)
                ->first();
        if (!$data)
            return view('error.404Ajax');

        $usados = [];
        $usadosQuery = SystemSubSubmenu::where('STATUS', 1)->where('SUBMENU', $data->SUBMENU)->get();
        foreach ($usadosQuery as $usado) {
            $usados[] = $usado->POSITION;
        }

        return view('configuracoes.desenvolvedor.edit.subsubmenu')
                        ->with('data', $data)
                        ->with('usados', $usados);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function editVid(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_UPDATE', ...array_values(MenusCollection::$menus['videos-aulas'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $data = Video::where('id', $id)
                ->where('deleted', 0)
                ->first();
        if (!$data)
            return view('error.404Ajax');

        $menu = SystemMenu::__construir(); // carrega o menu do SGS
        return view('configuracoes.desenvolvedor.edit.video')
                        ->with('data', $data)
                        ->with('menu', $menu);
    }

    public function updateAcesso(Request $request) {
        $var = new VariavelDoSistema();
        return $var->salvarAcessoXML($request);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateMenu(Request $request) {
        $doc = new SystemMenu();
        return $doc->salvar($request, $request->input('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateSubMenu(Request $request) {
        $doc = new SystemSubmenu();
        return $doc->salvar($request, $request->input('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateSubSubMenu(Request $request) {
        $doc = new SystemSubSubmenu();
        return $doc->salvar($request, $request->input('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateDoc(Request $request) {
        $doc = new Doc();
        return $doc->salvar($request, $request->input('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateVid(Request $request) {
        $video = new Video();
        return $video->salvar($request, $request->input('id'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyVideo(Request $request, string $id = NULL) {
        if (!AuthServiceProvider::acao('ACCESS_REMOVE', ...array_values(MenusCollection::$menus['videos-aulas'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $ids = [$id];
        if (!$id) {
            $ids = $request->input('checkbox');
            if (!count($ids))
                return response()->json(['error' => true, 'msg' => 'Erro ao excluir']);
        }

        return Video::remover($ids);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyLogs(Request $request, string $id = NULL) {
        if (!AuthServiceProvider::acao('ACCESS_REMOVE', ...array_values(MenusCollection::$menus['logs'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $ids = [$id];
        if (!$id) {
            $ids = $request->input('checkbox');
            if (!count($ids))
                return response()->json(['error' => true, 'msg' => 'Erro ao excluir']);
        }

        return Logs::remover($ids);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyDoc(Request $request, string $id = NULL) {
        if (!AuthServiceProvider::acao('ACCESS_REMOVE', ...array_values(MenusCollection::$menus['documentacao'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $ids = [$id];
        if (!$id) {
            $ids = $request->input('checkbox');
            if (!count($ids))
                return response()->json(['error' => true, 'msg' => 'Erro ao excluir']);
        }

        return Doc::remover($ids);
    }
}
