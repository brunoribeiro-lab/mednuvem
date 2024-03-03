<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemMenu;
use App\Providers\AuthServiceProvider;
use App\Models\SystemSubmenu;
use App\Models\SystemSubSubmenu;
use App\Models\DynamicEmail;
use Illuminate\Support\Facades\DB;
use App\Providers\Notificacao;
use App\Models\Disparos;
use App\Models\VariavelDoSistema;
use App\Providers\Mail;
use Illuminate\Support\Facades\Auth;
use App\Providers\MenusCollection;

class EmailController extends Controller {

    /**
     * Display a listing of the resource.
     */
    public function indexDinamico() {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['emails'])))
            return view('error.404SGS');

        $menu = SystemMenu::__construir(); // carrega o menu do SGS
        $data = [// data usada nessa página
            'default' => MenusCollection::$menus['emails'],
            'identificador' => [
                'padrão' => SystemMenu::identificador(),
                'add' => SystemMenu::identificador(true)
            ],
        ];

        $data['actions'] = AuthServiceProvider::acoes(...array_values(MenusCollection::$menus['emails']));
        $data['menu'] = SystemMenu::find($data['default']['menu']);
        $data['submenu'] = SystemSubmenu::find($data['default']['submenu']);
        if ($data['default']['subsubmenu'])
            $data['subsubmenu'] = SystemSubsubmenu::find($data['default']['subsubmenu']);

        return view('configuracoes.email.dinamicos')
                        ->with('data', $data)
                        ->with('menu', $menu);
    }

    /**
     * Display a listing of the resource.
     */
    public function indexDisparos() {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['disparos'])))
            return view('error.404SGS');

        $menu = SystemMenu::__construir(); // carrega o menu do SGS
        $data = [// data usada nessa página
            'default' => MenusCollection::$menus['disparos'],
            'identificador' => [
                'padrão' => SystemMenu::identificador(),
                'add' => SystemMenu::identificador(true)
            ],
        ];

        $data['actions'] = AuthServiceProvider::acoes(...array_values(MenusCollection::$menus['disparos']));
        $data['menu'] = SystemMenu::find($data['default']['menu']);
        $data['submenu'] = SystemSubmenu::find($data['default']['submenu']);
        if ($data['default']['subsubmenu'])
            $data['subsubmenu'] = SystemSubsubmenu::find($data['default']['subsubmenu']);

        return view('configuracoes.email.disparos')
                        ->with('data', $data)
                        ->with('menu', $menu);
    }

    public function indexConfigurar() {
        if (!AuthServiceProvider::acao('ACCESS_FORM', ...array_values(MenusCollection::$menus['config'])))
            return view('error.404SGS');

        $menu = SystemMenu::__construir(); // carrega o menu do SGS
        $data = [// data usada nessa página
            'default' => MenusCollection::$menus['config'],
            'identificador' => [
                'padrão' => SystemMenu::identificador(),
                'add' => SystemMenu::identificador(true)
            ],
        ];

        $data['actions'] = AuthServiceProvider::acoes(...array_values(MenusCollection::$menus['config']));
        $data['menu'] = SystemMenu::find($data['default']['menu']);
        $data['submenu'] = SystemSubmenu::find($data['default']['submenu']);
        if ($data['default']['subsubmenu'])
            $data['subsubmenu'] = SystemSubsubmenu::find($data['default']['subsubmenu']);

        $config = VariavelDoSistema::find(1);
        return view('configuracoes.email.configurar')
                        ->with('data', $data)
                        ->with('menu', $menu)
                        ->with('config', $config);
    }

    /**
     *  Listar tabela de usuários
     */
    public function datatableDinamicos(Request $request) {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['emails'])))
            return view('error.404SGS');

        return DynamicEmail::datatable($request);
    }

    /**
     *  Listar tabela de usuários
     */
    public function datatableDisparos(Request $request) {
        if (!AuthServiceProvider::acao('ACCESS_LISTING', ...array_values(MenusCollection::$menus['disparos'])))
            return view('error.404SGS');

        return Disparos::datatable($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createDinamico() {
        if (!AuthServiceProvider::acao('ACCESS_ADD', ...array_values(MenusCollection::$menus['emails'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $prefix = Notificacao::prefix();
        return view('configuracoes.email.add.dinamico')
                        ->with('prefix', $prefix);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeDinamico(Request $request) {
        $email = new DynamicEmail();
        return $email->salvar($request, 0);
    }

    /**
     * Display the specified resource.
     */
    public function showDisparo(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_PREVIEW', ...array_values(MenusCollection::$menus['disparos'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $exame = Disparos::where([
                    'deletado' => 0,
                    'id' => $id
                ])
                ->first();

        if ($exame) {
            return view('configuracoes.email.ver.disparo', ['data' => $exame]);
        }
        return view('error.404Ajax');
    }

    /**
     * Display the specified resource.
     */
    public function showDinamico(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_PREVIEW', ...array_values(MenusCollection::$menus['emails'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $exame = DynamicEmail::where([
                    'dynamic_emails.deleted' => 0,
                    'dynamic_emails.id' => $id
                ])
                ->leftJoin('users as u_criado_por', 'dynamic_emails.created_by', '=', 'u_criado_por.user_id')
                ->leftJoin('users as u_atualizado_por', 'dynamic_emails.updated_by', '=', 'u_atualizado_por.user_id')
                ->select('dynamic_emails.*',
                        DB::raw("CONCAT(u_criado_por.user_first_name, ' ', IFNULL(u_criado_por.user_last_name, '')) as criado_por_name"), // Concatenated column for criado_por
                        DB::raw("CONCAT(u_atualizado_por.user_first_name, ' ', IFNULL(u_atualizado_por.user_last_name, '')) as atualizado_por_name")  // Concatenated column for atualizado_por
                )
                ->first();

        if ($exame) {
            return view('configuracoes.email.ver.dinamico', ['data' => $exame]);
        }
        return view('error.404Ajax');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function editDinamico(string $id) {
        if (!AuthServiceProvider::acao('ACCESS_UPDATE', ...array_values(MenusCollection::$menus['emails'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $data = DynamicEmail::where('id', $id)
                ->where('deleted', 0)
                ->first();
        if (!$data)
            return view('error.404Ajax');

        $prefix = Notificacao::prefix();
        return view('configuracoes.email.edit.dinamico')
                        ->with('data', $data)
                        ->with('prefix', $prefix);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateDinamico(Request $request) {
        $data = new DynamicEmail();
        return $data->salvar($request, $request->input('id'));
    }

    public function updateAuth(Request $request) {
        $data = new VariavelDoSistema();
        return $data->salvarAuth($request);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyDinamico(Request $request, string $id = null) {
        if (!AuthServiceProvider::acao('ACCESS_REMOVE', ...array_values(MenusCollection::$menus['emails'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $ids = [$id];
        if (!$id) {
            $ids = $request->input('checkbox');
            if (!count($ids))
                return response()->json(['error' => true, 'msg' => 'Erro ao excluir']);
        }

        return DynamicEmail::remover($ids);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyDisparo(Request $request, string $id = null) {
        if (!AuthServiceProvider::acao('ACCESS_REMOVE', ...array_values(MenusCollection::$menus['disparos'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $ids = [$id];
        if (!$id) {
            $ids = $request->input('checkbox');
            if (!count($ids))
                return response()->json(['error' => true, 'msg' => 'Erro ao excluir']);
        }
        return Disparos::remover($ids);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function reenviar(Request $request, string $id = null) {
        if (!AuthServiceProvider::acao('ACCESS_RESEND', ...array_values(MenusCollection::$menus['disparos'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $ids = [$id];
        if (!$id) {
            $ids = $request->input('checkbox');
            if (!count($ids))
                return response()->json(['error' => true, 'msg' => 'Erro ao excluir']);
        }

        return Disparos::reenviar($ids);
    }

    public function testar(Request $request) {
        $variaveis = VariavelDoSistema::find(1);

        if (!in_array($variaveis->email_mode, ['smtp', 'native']))
            return response()->json(['error' => true, 'msg' => 'Autenticação Desativada', 'log' => "Autenticação está desativada."]);

        if (!$variaveis->email_username)
            return response()->json(['error' => true, 'msg' => 'Digite seu email antes de testar', 'log' => "Precisando do email para autenticar"], 420);

        if (in_array($variaveis->email_mode, ['smtp'])) {
            if (!$variaveis->email_password)
                return response()->json(['error' => true, 'msg' => 'Digite a sua senha antes de testar', 'log' => "Precisando de senha para autenticar"]);

            $mail = new Mail($variaveis);
            $m = $mail->sendMailWithPHPMailer(Auth::user()->user_email, $variaveis->nome, 'Teste de Autenticação ' . date("d/m/Y H:i:s"), 'Email autenticado com sucesso.' . "<br>" . $variaveis->nome, 4);
            if (!$m)
                return response()->json(['error' => true, 'msg' => 'Falha ao autenticar email', 'log' => $mail->getError()]);

            return response()->json(['error' => false, 'msg' => 'Email autenticado com sucesso']);
        }
        if (in_array($variaveis->email_mode, ['native'])) {
            $mail = new Mail($variaveis);
            $m = $mail->sendMailWithNativeMailFunction(Auth::user()->user_email, $variaveis->email_username, $variaveis->nome, 'Teste de Autenticação ' . date("d/m/Y H:i:s"), 'Email autenticado com sucesso.' . "<br>" . $variaveis->nome, 2);
            if (!$m)
                return response()->json(['error' => true, 'msg' => 'Falha ao autenticar email', 'log' => $mail->getError()]);

            return response()->json(['error' => false, 'msg' => 'Email autenticado com sucesso']);
        }
    }

    public static function actionButton($query, $index = "default") {
        if ($index == 'default') {
            $buttons = [];
            if (self::$actions['preview'])
                $buttons[] = "<button class=\"btn btn-white goPreview\" type=\"button\"  data-id=\"{$query['ID']}\" title=\"Detalhes do Menu\"><i class=\"fa fa-eye\"></i> </button>";

            if (!$query->SUB && self::$actions['update'])
                $buttons[] = "<button class=\"btn btn-primary goUpdate\" type=\"button\" data-id=\"{$query['ID']}\" title=\"Editar Menu\"><i class=\"fas fa-pencil-alt\"></i> </button>";

            if ($query->SUB)
                $buttons[] = "<button class=\"btn btn-info goSubmenu\" type=\"button\"  data-id=\"{$query["ID"]}\" title=\"Gerenciar Submenus\"><i class=\"far fa-list-alt\"></i> </button>";

            return implode("\n", $buttons);
        }
    }
}
