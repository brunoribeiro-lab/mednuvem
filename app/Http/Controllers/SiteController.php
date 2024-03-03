<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemMenu;
use App\Providers\AuthServiceProvider;
use App\Models\SystemSubmenu;
use App\Models\SystemSubSubmenu;
use App\Providers\MenusCollection;
use App\Models\FormContato;

class SiteController extends Controller {

    public function index() {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['mensagens'])))
            return view('error.404SGS');

        $menu = SystemMenu::__construir(); // carrega o menu do SGS
        $data = [// data usada nessa página
            'default' => MenusCollection::$menus['mensagens'],
            'identificador' => [
                'padrão' => SystemMenu::identificador(),
                'add' => SystemMenu::identificador(true)
            ],
        ];
        $data['actions'] = AuthServiceProvider::acoes(...array_values(MenusCollection::$menus['mensagens']));
        $data['menu'] = SystemMenu::find($data['default']['menu']);
        if ($data['default']['submenu'])
            $data['submenu'] = SystemSubmenu::find($data['default']['submenu']);

        if ($data['default']['subsubmenu'])
            $data['subsubmenu'] = SystemSubsubmenu::find($data['default']['subsubmenu']);

        return view('configuracoes.site.contato')
                        ->with('data', $data)
                        ->with('menu', $menu);
    }

    public function datatable(Request $request) {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['mensagens'])))
            return view('error.404SGS');

        return FormContato::datatable($request);
    }

    public function store(Request $request) {
        $formCampo = new Medico();
        return $formCampo->salvar($request, 0);
    }

    public function show(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_PREVIEW', ...array_values(MenusCollection::$menus['mensagens'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $data = FormContato::where([
                    'deletado' => 0,
                    'id' => $id
                ])
                ->first();
        if (!$data)
            return view('error.404Ajax');

        FormContato::where('id', $id)->update(['lida' => 1]);
        return view('configuracoes.site.ver.contato', ['data' => $data]);
    }

    public function destroy(Request $request, string $id = null) {
        if (!AuthServiceProvider::acao('ACCESS_REMOVE', ...array_values(MenusCollection::$menus['mensagens'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $ids = [$id];
        if (!$id) {
            $ids = $request->input('checkbox');
            if (!count($ids))
                return response()->json(['error' => true, 'msg' => 'Erro ao excluir']);
        }

        return FormContato::remover($ids);
    }
}
