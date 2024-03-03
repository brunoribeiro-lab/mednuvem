<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemMenu;
use App\Providers\AuthServiceProvider;
use App\Models\SystemSubmenu;
use App\Models\VariavelDoSistema;
use App\Models\AccountType;
use App\Models\DynamicEmail;
use App\Models\Activity;
use Validator;
use App\Rules\GreaterThanTime;
use Illuminate\Support\Facades\DB;
use App\Providers\MenusCollection;
use App\Models\SystemSubSubmenu;

class VariaveisDoSistemaController extends Controller {

    /**
     * Display a listing of the resource.
     */
    public function index() {

        if (!AuthServiceProvider::acao('ACCESS_FORM', ...array_values(MenusCollection::$menus['variaveis-do-sistema'])))
            return view('error.404SGS');

        $menu = SystemMenu::__construir(); // carrega o menu do SGS
        $data = [// data usada nessa página
            'default' => MenusCollection::$menus['variaveis-do-sistema'],
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
        $data['cargos'] = AccountType::where('DELETED', 0)
                ->where('ROOT_ACCESS', 0)
                ->orderBy('NAME', 'ASC')
                ->get();
        $data['emails'] = DynamicEmail::where('deleted', 0)
                ->orderBy('index', 'ASC')
                ->get();

        return view('configuracoes.desenvolvedor.variaveis')
                        ->with('data', $data)
                        ->with('menu', $menu);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $config = new VariavelDoSistema();
        return $config->salvar($request);
    }
}
