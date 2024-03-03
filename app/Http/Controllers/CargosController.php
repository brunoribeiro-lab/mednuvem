<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemMenu;
use App\Providers\AuthServiceProvider;
use App\Models\SystemSubmenu;
use App\Models\SystemSubSubmenu;
use App\Models\AccountType;
use Illuminate\Support\Facades\DB;

class CargosController extends Controller {

    private $menu = [
        "menu" => 5,
        "submenu" => 12,
        "subsubmenu" => NULL,
    ];

    /**
     * Display a listing of the resource.
     */
    public function index() {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', $this->menu['menu'], $this->menu['submenu'], $this->menu['subsubmenu']))
            return view('error.404SGS');

        $menu = SystemMenu::__construir(); // carrega o menu do SGS
        $data = [// data usada nessa página
            'default' => $this->menu,
            'identificador' => [
                'padrão' => SystemMenu::identificador(),
                'add' => SystemMenu::identificador(true)
            ],
        ];

        $data['actions'] = AuthServiceProvider::acoes($data['default']['menu'], $data['default']['submenu'], $data['default']['subsubmenu']);
        $data['menu'] = SystemMenu::find($data['default']['menu']);
        $data['submenu'] = SystemSubmenu::find($data['default']['submenu']);
        if ($data['default']['subsubmenu'])
            $data['subsubmenu'] = SystemSubsubmenu::find($data['default']['subsubmenu']);

        return view('configuracoes.cargos')
                        ->with('data', $data)
                        ->with('menu', $menu);
    }

    /**
     *  Listar tabela de usuários
     */
    public function datatable(Request $request) {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', $this->menu['menu'], $this->menu['submenu'], $this->menu['subsubmenu']))
            return view('error.404SGS');

        return AccountType::datatable($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        if (!AuthServiceProvider::acao('ACCESS_ADD', $this->menu['menu'], $this->menu['submenu'], $this->menu['subsubmenu']))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        return view('configuracoes.add.cargo');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $cargo = new AccountType();
        return $cargo->salvar($request, 0);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_PREVIEW', $this->menu['menu'], $this->menu['submenu'], $this->menu['subsubmenu']))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $cargo = AccountType::
                where([
                    '_ACCOUNT_TYPE.DELETED' => 0,
                    '_ACCOUNT_TYPE.ID' => $id
                ])
                ->leftJoin('users as u_criado_por', '_ACCOUNT_TYPE.USER_CREATED', '=', 'u_criado_por.user_id')
                ->leftJoin('users as u_atualizado_por', '_ACCOUNT_TYPE.USER_UPDATED', '=', 'u_atualizado_por.user_id')
                ->select('_ACCOUNT_TYPE.*',
                        DB::raw("CONCAT(u_criado_por.user_first_name, ' ', IFNULL(u_criado_por.user_last_name, '')) as criado_por_name"), // Concatenated column for criado_por
                        DB::raw("CONCAT(u_atualizado_por.user_first_name, ' ', IFNULL(u_atualizado_por.user_last_name, '')) as atualizado_por_name")  // Concatenated column for atualizado_por
                )
                ->first();

        if ($cargo)
            return view('configuracoes.ver.cargo', ['data' => $cargo]);

        return view('error.404Ajax');
    }

    /**
     * Display the specified resource.
     */
    public function showAcessos(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_ACCESS', $this->menu['menu'], $this->menu['submenu'], $this->menu['subsubmenu']))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $data = [];
        $data['account'] = AccountType::
                where([
                    '_ACCOUNT_TYPE.DELETED' => 0,
                    '_ACCOUNT_TYPE.ID' => $id
                ])
                ->leftJoin('users as u_criado_por', '_ACCOUNT_TYPE.USER_CREATED', '=', 'u_criado_por.user_id')
                ->leftJoin('users as u_atualizado_por', '_ACCOUNT_TYPE.USER_UPDATED', '=', 'u_atualizado_por.user_id')
                ->select('_ACCOUNT_TYPE.*',
                        DB::raw("CONCAT(u_criado_por.user_first_name, ' ', IFNULL(u_criado_por.user_last_name, '')) as criado_por_name"), // Concatenated column for criado_por
                        DB::raw("CONCAT(u_atualizado_por.user_first_name, ' ', IFNULL(u_atualizado_por.user_last_name, '')) as atualizado_por_name")  // Concatenated column for atualizado_por
                )
                ->first()
                ->toArray();
        if (!$data['account'])
            return view('error.404Ajax');

        // Pegar todos os menus
        $data['LIST'] = SystemMenu::where('STATUS', 1)->orderBy('POSITION', 'asc')->get()->toArray();
        $xml = AccountType::menuXML();
        // Listar todos os menus
        foreach ($data['LIST'] as $index => $m) {
            if ($m['SUB']) {
                $submenus = SystemSubmenu::where('STATUS', 1)->where('MENU', $m["ID"])->orderBy('POSITION', 'asc')->get()->toArray();
                foreach ($submenus as $index_sub => $submenu) {
                    $submenus[$index_sub]['XML'] = AccountType::XMLAcessoDoID($xml, $submenu["ID"]);

                    if ($submenu['SUB']) {
                        $subsubmenus = SystemSubsubmenu::where('STATUS', 1)->where('SUBMENU', $submenu["ID"])->orderBy('POSITION', 'asc')->get()->toArray();

                        foreach ($subsubmenus as $index_sub_sub => $subsub) {
                            $subsubmenus[$index_sub_sub]['XML'] = AccountType::XMLAcessoDoID($xml, $subsub["ID"], false, true);
                        }

                        $submenus[$index_sub]['submenus'] = $subsubmenus;
                    }
                }

                $data['LIST'][$index]['submenus'] = $submenus;
            } else {
                $data['LIST'][$index]['XML'] = AccountType::XMLAcessoDoID($xml, $m["ID"], $m["SUB"], false);
            }
        }
        #  var_dump($data['LIST']);
        return view('configuracoes.edit.acessos', ['data' => $data]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_UPDATE', $this->menu['menu'], $this->menu['submenu'], $this->menu['subsubmenu']))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $data = AccountType::where('ID', $id)
                ->where('DELETED', 0)
                ->first();
        if (!$data)
            return view('error.404Ajax');

        return view('configuracoes.edit.cargo')
                        ->with('data', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request) {
        $data = new AccountType();
        return $data->salvar($request, $request->input('id'));
    }

    public function updateAcessos(Request $request) {
        return AccountType::salvarAcessos($request);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {
        $ids = [$id];
        if (!$id) {
            $ids = $request->input('checkbox');
            if (!count($ids))
                return response()->json(['error' => true, 'msg' => 'Erro ao excluir']);
        }
        if (!AuthServiceProvider::acao('ACCESS_REMOVE', $this->menu['menu'], $this->menu['submenu'], $this->menu['subsubmenu']))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        return AccountType::remover($ids);
    }
}
