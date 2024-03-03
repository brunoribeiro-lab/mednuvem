<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\SystemMenu;
use App\Models\SystemSubmenu;
use App\Providers\AuthServiceProvider;
use App\Models\VariavelDoSistema;
use App\Models\AccountType;

class UserController extends Controller {

    private $menu = [
        "menu" => 5,
        "submenu" => 1,
        "subsubmenu" => null,
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

        return view('configuracoes.usuarios')
                        ->with('data', $data)
                        ->with('menu', $menu);
    }

    public function novaSenha(string $token) {
        if (empty($token))
            return view('error.404SGS');

        $user = Users::where('user_password_reset_hash', $token)->where('deleted', 0)->where('user_active', 1)->first();
        if (!$user)
            return view('error.404SGS');

        // verifica se o link ainda é válido, 1h
        if (!empty($user->user_password_reset_timestamp) && ($user->user_password_reset_timestamp + 3600) < time())
            return view('error.404SGS');

        return view('site.nova-senha')
                        ->with('data', $user);
    }

    /**
     *  Listar tabela de usuários
     */
    public function datatable(Request $request) {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', $this->menu['menu'], $this->menu['submenu'], $this->menu['subsubmenu']))
            return view('error.404SGS');

        return Users::datatable($request);
    }

    /**
     *  Listar tabela de usuários
     */
    public function datatableHistorico(Request $request, string $id) {
        return Users::datatableHistorico($request, $id);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        if (!AuthServiceProvider::acao('ACCESS_ADD', $this->menu['menu'], $this->menu['submenu'], $this->menu['subsubmenu']))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        // Pegar a variável do sistema
        $variavel = VariavelDoSistema::get()->first();
        // Pegar todos os cargos
        $cargos = AccountType::where('DELETED', 0)->orderBy('NAME', 'ASC')->get();
        // Remover o cargo de medico
        $cargos = $cargos->reject(function ($cargo) use ($variavel) {
            return (int) $cargo->ID === (int) $variavel->medico;
        });
        return view('configuracoes.add.usuario', [
            'variavel' => $variavel,
            'cargos' => $cargos->values()->all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $empresa = new Users();
        return $empresa->salvar($request, 0);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_PREVIEW', $this->menu['menu'], $this->menu['submenu'], $this->menu['subsubmenu']))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        // Consulta para buscar a empresa com o id fornecido
        $usuario = Users::where([
                    'users.deleted' => 0,
                    'users.user_id' => $id
                ])
                ->join('_ACCOUNT_TYPE', 'users.user_account_type', '=', '_ACCOUNT_TYPE.ID')
                ->select('users.*',
                        '_ACCOUNT_TYPE.NAME',
                        DB::raw("CONCAT(users.user_first_name, ' ', IFNULL(users.user_last_name, '')) as nome_completo"), // Concatenated column for criado_por
                )
                ->first();

        if ($usuario) {
            return view('configuracoes.ver.usuario', ['data' => $usuario]);
        }
        return view('error.404Ajax');
    }

    public function historico(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_HISTORIC', $this->menu['menu'], $this->menu['submenu'], $this->menu['subsubmenu']))
            return view('error.404SGS');

        return view('configuracoes.table.historico', ['data' => $id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_UPDATE', $this->menu['menu'], $this->menu['submenu'], $this->menu['subsubmenu']))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $user = Users::where('user_id', $id)
                ->where('deleted', 0)
                ->first();
        if (!$user)
            return view('error.404Ajax');

        $accountType = AccountType::where('DELETED', 0)
                ->get();

        return view('configuracoes.edit.usuario', [
            'user' => $user,
            'accout' => $accountType,
            'config' => VariavelDoSistema::first()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request) {
        $empresa = new Users();
        return $empresa->salvar($request, $request->input('id'));
    }

    /**
     * Remover usuário(s) do sistema
     */
    public function destroy(Request $request, string $id = null) {
        if (!AuthServiceProvider::acao('ACCESS_REMOVE', $this->menu['menu'], $this->menu['submenu'], $this->menu['subsubmenu']))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $ids = [$id];
        if (!$id) {
            $ids = $request->input('checkbox');
            if (!count($ids))
                return response()->json(['error' => true, 'msg' => 'Erro ao excluir']);
        }
        return Users::remover($ids);
    }

    public function login(Request $request) {
        // 1. Extrair apenas os números do campo 'user_name'
        $cpf = preg_replace('/[^0-9]/', '', $request->input('user_name'));

        // 2. Buscar o usuário pelo CPF
        $user = Users::join('_ACCOUNT_TYPE', function ($join) {
                    $join->on('users.user_account_type', '=', '_ACCOUNT_TYPE.ID')
                    ->where('_ACCOUNT_TYPE.DELETED', '=', 0);
                })
                ->where('user_name', $cpf)
                ->where('users.deleted', 0)
                ->where('users.user_active', 1)
                ->select('users.*', '_ACCOUNT_TYPE.NAME as account_type_name', '_ACCOUNT_TYPE.ROOT_ACCESS AS is_root')
                ->first();
        if (!$user)
            return response()->json(["error" => true, "message" => "Falha na autenticação"]);

        // Verificar se o usuário excedeu tentativas falhas
        if ($user->user_failed_logins >= config("app.max_tentativas") && time() - strtotime($user->user_last_failed_login) < (config("app.max_tentativas_tempo") * 60)) {
            return response()->json([
                        "error" => true,
                        "message" => "Conta bloqueada devido a tentativas falhas. Tente novamente mais tarde."
            ]);
        }

        // Verificar se o usuário existe e se a senha é válida
        if (Hash::check($request->input('user_password'), $user->user_password_hash)) {
            $systemVariables = VariavelDoSistema::get()->first();
            // defini a autenticação do usuário
            Auth::login($user);
            Session::put('is_root', $user->is_root);
            Session::put('cargo', $user->account_type_name);
            // logo do menu
            $sgs_logo = [
                'light' => asset(sprintf("assets/uploads/theme/%s", $systemVariables->logo)),
                'dark' => asset(sprintf("assets/uploads/theme/%s", $systemVariables->logo_dark))
            ];
            if ($user->usuario)
                $sgs_logo = [
                    'light' => asset(sprintf("assets/uploads/theme/%s", $user->logo_menu)),
                    'dark' => asset(sprintf("assets/uploads/theme/%s", $user->logo_dark))
                ];

            Session::put('SGS_logo', $sgs_logo['light']);
            Session::put('SGS_logo_dark', $sgs_logo['dark']);

            // $user->user_last_login = now();
            // Reiniciar contagem de tentativas falhas
            $user->user_last_failed_login = NULL;
            $user->user_failed_logins = 0;
            $user->user_last_login = now();
            $user->save();
            return response()->json([
                        "error" => false,
                        "message" => "logado",
                        "redirect" => config("app.url") . "/SGS"
            ]);
        } else {
            // Incrementar contagem de tentativas falhas
            $user->user_failed_logins++;
            $user->user_last_failed_login = time();
            $user->save();
            return response()->json([
                        "error" => true,
                        "message" => "Falha na autenticação"
            ]);
        }
    }

    public function logout() {
        Auth::logout();
        return redirect('/');
    }
}
