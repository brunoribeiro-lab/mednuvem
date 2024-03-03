<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Http\Request;
use App\Providers\AuthServiceProvider;
use Illuminate\Support\Carbon;
use App\Providers\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Providers\Notificacao;

class Users extends Authenticatable {

    use HasApiTokens,
        HasFactory,
        Notifiable;

    protected $primaryKey = 'user_id';
    protected $fillable = [
        'user_id',
        'session_id',
        'group',
        'user_name',
        'deleted',
        'user_first_name',
        'user_last_name',
        'user_password_hash',
        'user_email',
        'user_sex',
        'user_active',
        'user_account_type',
        'user_has_avatar',
        'user_rememberme_token',
        'user_creation',
        'user_suspension_timestamp',
        'user_last_login',
        'user_failed_logins',
        'user_last_failed_login',
        'user_activation_hash',
        'user_registration_ip',
        'user_password_reset_hash',
        'user_password_reset_timestamp',
        'theme',
    ];
    public $timestamps = false;
    private $senhaAleatoria = '';

    // Para a autenticação
    public function getAuthPassword() {
        return $this->user_password_hash;
    }

    public function accountType() {
        return $this->belongsTo(AccountType::class, 'user_account_type', 'ID');
    }

    private static $menu = [
        "menu" => 5,
        "submenu" => 1,
        "subsubmenu" => null,
    ];
    private static $actions = []; // ações do CRUD

    public static function checkBox($id) {
        if ((int) Auth::user()->user_id == (int) $id)
            return '';

        return "<div class=\" check-default\">
  <input type=\"checkbox\" name=\"checkbox[]\" value=\"{$id}\" id=\"checkbox{$id}\">
  <label for=\"checkbox{$id}\"></label>
</div>";
    }

    /**
     * Criar a SQL de listar 
     * 
     * @static
     * @access private
     * @param Object $request
     * @param boolean $todos Se true Ignora o limite
     * @return array
     */
    private static function sql_listar($request, $todos = false) {
        $systemVariables = VariavelDoSistema::get()->first();
        $sortMap = [
            2 => \DB::raw("CONCAT(IFNULL(users.user_first_name,''), ' ', IFNULL(users.user_last_name,''))"),
            3 => "_ACCOUNT_TYPE.NAME",
            4 => "users.user_name",
            5 => 'users.user_creation'
        ];

        $sSearch = trim($request->input('sSearch'));

        $query = Users::join('_ACCOUNT_TYPE', 'users.user_account_type', '=', '_ACCOUNT_TYPE.ID')
                ->select('_ACCOUNT_TYPE.NAME',
                        \DB::raw("CONCAT(IFNULL(users.user_first_name,''), ' ', IFNULL(users.user_last_name,'')) AS fullName"),
                        'users.user_id',
                        'users.user_name',
                        'users.user_creation')
                ->where('users.deleted', 0)
                ->where('users.user_active', 1)
                ->where('users.user_account_type', '!=', $systemVariables->medico);

        if (!empty($sSearch)) {
            $query->where(function ($q) use ($sSearch) {
                $q->orWhere('users.user_first_name', 'like', "%$sSearch%")
                        ->orWhere('users.user_last_name', 'like', "%$sSearch%")
                        ->orWhere('users.user_name', 'like', "%$sSearch%")
                        ->orWhere('_ACCOUNT_TYPE.NAME', 'like', "%$sSearch%");
            });
        }

        $query->orderBy($sortMap[intval($request->input('iSortCol_0', 0))], $request->input('sSortDir_0', 'asc'));

        if (!$todos) {
            $query->limit($request->input('iDisplayLength'))
                    ->offset($request->input('iDisplayStart'));
        }

        $query->groupBy(
                'users.user_id',
                'users.user_name',
                '_ACCOUNT_TYPE.NAME',
                'users.user_first_name',
                'users.user_last_name',
                'users.user_creation'
        );
        return $query->get();
    }

    /**
     * Criar a SQL de listar 
     * 
     * @static
     * @access private
     * @param Object $request
     * @param boolean $todos Se true Ignora o limite
     * @return array
     */
    public static function sql_listar_historico($request, $id, $todos = false) {
        $systemVariables = VariavelDoSistema::get()->first();
        $sortMap = [
            1 => "TITLE",
            2 => "CREATED",
        ];

        $sSearch = trim($request->input('sSearch'));

        $query = Activity::select('_RECENT_ACTIVITY.*')
                ->where('DELETED', 0)
                ->where('USER', $id);

        if (!empty($sSearch)) {
            $query->where(function ($q) use ($sSearch) {
                $q->orWhere('TITLE', 'like', "%$sSearch%");
            });
        }

        $query->orderBy($sortMap[intval($request->input('iSortCol_0', 0))], $request->input('sSortDir_0', 'asc'));

        if (!$todos) {
            $query->limit($request->input('iDisplayLength'))
                    ->offset($request->input('iDisplayStart'));
        }
        return $query->get();
    }

    /**
     * Remover Usuário
     * 
     * @param array $ids Ids do usuários
     * @return json
     */
    public static function remover($ids) {
        // Atualizar os registros na tabela 'empresas' para 'deletado' = 1
        Users::whereIn('user_id', $ids)->update(['deleted' => 1]);

        // Registrar a atividade
        if (count($ids) > 1) {
            Activity::novo(sprintf("Remoção de %d Usuários do Sistema", count($ids)), "trash-alt");
        } else {
            Activity::novo("Remoção de um Usuário do Sistema", "trash-alt");
        }
        $msg = count($ids) > 1 ? "Usuários do Sistema Excluidos com sucesso !" : "Usuário do Sistema Excluido com sucesso !";
        return response()->json(['error' => false, 'msg' => $msg]);
    }

    /**
     * Cria um padrão de listagem para o plugin Jquery Datatable
     * 
     * @static
     * @access public
     * @param Object $request
     * @return json
     */
    public static function datatableHistorico(Request $request, $id) {
        $atividades = self::sql_listar_historico($request, $id);
        $total = count(self::sql_listar_historico($request, $id, true));
        self::$actions = AuthServiceProvider::acoes(self::$menu['menu'], self::$menu['submenu'], self::$menu['subsubmenu']);

        $row = array();
        foreach ($atividades as $atividade) {
            $nice = Carbon::parse($atividade->CREATED)->diffForHumans();
            $updated = Carbon::parse($atividade->CREATED)->format('d de F, Y à\s H:i:s');
            $row[] = [
                '',
                "<i class='fa fa-{$atividade->ICON}'></i> " . strlen($atividade->TITLE) >= 100 ? sprintf("<abbr title='%s'>%s</abbr>", $atividade->TITLE, Str::limit($atividade->TITLE, 100)) : $atividade->TITLE,
                "<abbr title='em {$updated}'>{$nice}</abbr>",
            ];
        }

        $output = array(
            "sEcho" => intval($request->input('sEcho')),
            "iTotalRecords" => $total,
            "iTotalDisplayRecords" => $total,
            "aaData" => $row
        );
        return response()->json($output);
    }

    /**
     * Cria um padrão de listagem para o plugin Jquery Datatable
     * 
     * @static
     * @access public
     * @param Object $request
     * @return json
     */
    public static function datatable(Request $request) {
        $usuarios = self::sql_listar($request);
        $total = count(self::sql_listar($request, true));
        self::$actions = AuthServiceProvider::acoes(self::$menu['menu'], self::$menu['submenu'], self::$menu['subsubmenu']);

        $row = array();
        foreach ($usuarios as $usuario) {
            $nice = Carbon::parse($usuario->user_creation)->diffForHumans();
            $updated = Utils::dataCompletaPTBR($usuario->user_creation);
            $row[] = [
                '',
                self::checkBox($usuario->user_id),
                strlen($usuario->fullName) >= 50 ? sprintf("<abbr title='%s'>%s</abbr>", $usuario->fullName, Str::limit($usuario->fullName, 50)) : $usuario->fullName,
                strlen($usuario->NAME) >= 50 ? sprintf("<abbr title='%s'>%s</abbr>", $usuario->NAME, Str::limit($usuario->NAME, 50)) : $usuario->NAME,
                strlen($usuario->user_name) == 11 ? Utils::mask($usuario->user_name, Utils::$MASK_CPF) : Utils::mask($usuario->user_name, Utils::$MASK_CNPJ),
                "<abbr title='em {$updated}'>{$nice}</abbr>",
                self::actionButton($usuario)
            ];
        }

        $output = array(
            "sEcho" => intval($request->input('sEcho')),
            "iTotalRecords" => $total,
            "iTotalDisplayRecords" => $total,
            "aaData" => $row
        );
        return response()->json($output);
    }

    public static function actionButton($query, $index = "usuarios") {
        if ($index == 'listing-cnae') {
            $desc = str_replace('"', '\"', $query['titulo']);
            $buttons = [];
            $buttons[0] = "<button class=\"btn btn-primary aplicarCodigo\" type=\"button\"  data-id=\"{$query['id']}\" data-cod=\"{$query['cod']}\" data-descricao=\"{$desc}\" title=\"Aplicar esse código\"><i class=\"fa fa-check\"></i> </button>";
            return implode("\n", $buttons);
        }
        if ($index == 'usuarios') {
            $buttons = [];
            if (self::$actions['preview'])
                $buttons[] = "<button class=\"btn btn-white goPreview\" type=\"button\"  data-id=\"{$query['user_id']}\" title=\"Detalhes do Usuário do Sistema\"><i class=\"fa fa-eye\"></i> </button>";

            if (self::$actions['historic'])
                $buttons[] = "<button class=\"btn btn-white goHistoric\" title=\"Histórico do Usuário\" type=\"button\" data-id=\"{$query['user_id']}\"><i class=\"far fa-shoe-prints\"></i> </button>";

            if (self::$actions['update'])
                $buttons[] = "<button class=\"btn btn-primary goUpdate\" type=\"button\" data-id=\"{$query['user_id']}\" title=\"Editar Usuário do Sistema\"><i class=\"fas fa-pencil-alt\"></i> </button>";

            if (self::$actions['remove'] && (int) Auth::user()->user_id !== (int) $query["user_id"])
                $buttons[] = "<button class=\"btn btn-danger goRem\" type=\"button\" data-id=\"{$query['user_id']}\" title=\"Excluir Usuário do Sistema\" data-toggle=\"modal\" data-target=\"#myModalRem\"><i class=\"fa fa-trash\"></i> </button>";

            return implode("\n", $buttons);
        }
    }

    /**
     * Cadastrar ou editar usuário do sistema
     * 
     * @param Request $request
     * @param int $id
     * @return json
     */
    public function salvar(Request $request, $id) {
        if (!AuthServiceProvider::acao($id ? 'ACCESS_UPDATE' : 'ACCESS_ADD', self::$menu['menu'], self::$menu['submenu'], self::$menu['subsubmenu']))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        if ($id && !$usuario = Users::find($id))
            return response()->json(['error' => true, 'msg' => 'Usuário não encontrada']);

        $this->_validate($request, (int) $id);
        $user = $this->createOrUpdateUser($request, $id);
        if (!$user->save())
            return response()->json(['error' => true, 'msg' => 'Não foi possível adicionar/editar o Usuário']);

        if (!$id) {
            // associar grupo do usuario como ele mesmo
            $user->group = $user->user_id;
            $user->save();
            // gerar email
            $userData = [
                'fullName' => $request->firstname,
                'user_email' => $request->email,
                'user_name' => Utils::extrairNum($request->username),
                'user_password' => $this->senhaAleatoria
            ];
            if (!Notificacao::formNovoClinica($userData, $request->email))
                return response()->json(['error' => true, 'msg' => 'Não foi possível disparar o email']);
        }


        $msg = $id ? 'O Usuário foi atualizado com sucesso' : 'Usuário cadastrado com sucesso';
        if ($id) {
            Activity::novo("Edição de Usuário do Sistema", "edit");
        } else {
            Activity::novo("Cadastro de Usuário do Sistema");
        }
        return response()->json(['error' => false, 'msg' => $msg]);
    }

    private function createOrUpdateUser($request, $id) {
        $user = $id ? Users::find($id) : new Users();
        $user->user_name = Utils::extrairNum($request->username);
        $config = VariavelDoSistema::first();
        if (!in_array($user->user_account_type, [$config->clinica, $config->medico])) {
            $user->user_first_name = $request->firstname;
            $user->user_last_name = $request->lname;
        }

        $user->user_email = $request->email;
        $user->user_account_type = $request->business;
        $user->user_active = 1;

        if (!$id || $request->input('gerar_senha'))
            $user->user_password_hash = bcrypt($this->senhaAleatoria = $request->password);

        // gerar senha aleatória
        if (!$id || !$request->input('gerar_senha')) {
            $this->senhaAleatoria = Str::random(8);
            $user->user_password_hash = bcrypt($this->senhaAleatoria);
        }

        if ($id && $request->input('mudar_senha'))
            $user->user_password_hash = bcrypt($this->senhaAleatoria = $request->password);

        // se for o cadastrar
        if (!$id) {
            $user->user_creation = now();
        }
        return $user;
    }

    /**
     * Validar Campos no formulário de Add/Edit Empresa
     * 
     * @param Request $request
     * @param boolean $eAtualiazar
     * @return Request
     * @throws \Illuminate\Validation\ValidationException
     */
    private function _validate(Request $request, $isUpdate) {
        $rules = [
            'firstname' => 'required|string|min:3|max:255',
            'tipo_login' => 'required|in:cnpj,cpf',
            'email' => [
                'required',
                'email',
                        Rule::unique('users', 'user_email')
                        ->where('deleted', 0)
                        ->where('user_active', 1)
                        ->ignore($isUpdate ? $request->input('id') : '', 'user_id')
            ],
            'username' => [
                'required',
                function ($attribute, $value, $fail) use ($request, $isUpdate) {
                    $onlyNumbers = preg_replace("/[^0-9]/", "", $value);

                    $query = DB::table('users')
                            ->whereRaw("REPLACE(user_name, ' ', '') = ?", [$onlyNumbers])
                            ->where('deleted', 0)
                            ->where('user_active', 1);

                    if ($isUpdate) {
                        $query->where('user_id', '<>', $request->input('id'));
                    }

                    if ($query->exists()) {
                        $fail("O username já existe.");
                    }
                }
            ],
            'business' => 'required|exists:_ACCOUNT_TYPE,ID',
            'lname' => 'nullable|string|max:255'
        ];
        if ($isUpdate && $request->input('mudar_senha')) {
            $rules['password'] = [
                'required',
                'string',
                'min:6',
                'max:18',
                'confirmed'
            ];
            $rules['password_confirmation'] = [
                'required',
                'string',
                'min:6',
                'max:18',
            ];
        }
        if (!$isUpdate && $request->input('gerar_senha')) {
            $rules['password'] = [
                'required',
                'string',
                'min:6',
                'max:18',
                'confirmed'
            ];
            $rules['password_confirmation'] = [
                'required',
                'string',
                'min:6',
                'max:18',
            ];
        }

        if ($request->input('tipo_login') == 'cnpj') {
            #$rules['username'][] = 'cnpj';
            $rules['username'][] = 'only_numbers';
        } elseif ($request->input('tipo_login') == 'cpf') {
            #$rules['username'][] = 'cpf';
            $rules['username'][] = 'only_numbers';
        }

        $messages = [
            '*.required' => 'Campo obrigatório',
            '*.min' => 'O campo deve ter no mínimo :min caracteres',
            '*.max' => 'O campo deve ter no máximo :max caracteres',
            '*.in' => 'Campo Inválido',
            '*.cpf' => 'Campo Inválido',
            '*.cnpj' => 'Campo Inválido',
            'email.email' => 'Campo Inválido',
            'password.confirmed' => 'As Senhas digitadas não coincidem',
            'username.unique' => 'Desculpe, o Login digitado não está disponível',
            'email.unique' => 'Desculpe, o Email digitado não está disponível',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $request->all();
    }

    /**
     * Recuperar senha
     */
    public static function recuperar(Request $request) {
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
            return response()->json(["error" => true, "message" => "Usuário não encontrado"]);

        if (!empty($user->user_password_reset_timestamp) && !empty($user->user_password_reset_hash) && $user->user_password_reset_timestamp + 3600 > time())
            return response()->json(["error" => true, "message" => "Você solicitou a recuperação da sua conta a pouco tempo, aguarde uma hora para fazer uma nova solicitação"]);

        $token = bin2hex(random_bytes(40));

        $user->user_password_reset_hash = $token;
        $user->user_password_reset_timestamp = time();
        $user->save();
        $userData = [
            'fullName' => trim("{$user->user_first_name} {$user->user_last_name}"),
            'user_email' => $user->user_email,
            'user_name' => Utils::extrairNum($user->user_name),
            'token' => $token
        ];
        Notificacao::formRecuperarSenha($userData);
        return response()->json(["error" => false, "message" => "Você receberá um e-mail com instruções sobre como alterar sua senha."]);
    }

    public function mudarSenha(Request $request) {
        $rules = [
            'token' => [
                'required',
                'string',
                'min:20',
                'max:200',
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'max:18',
                'confirmed'
            ],
            'password_confirmation' => [
                'required',
                'string',
                'min:6',
                'max:18',
            ]
        ];
        $messages = [
            '*.required' => 'Campo obrigatório',
            '*.min' => 'O campo deve ter no mínimo :min caracteres',
            '*.max' => 'O campo deve ter no máximo :max caracteres',
            'password.confirmed' => 'As Senhas digitadas não coincidem',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails())
            throw new ValidationException($validator);

        $user = Users::where('user_password_reset_hash', $request->input('token'))->where('deleted', 0)->where('user_active', 1)->first();
        if (!$user)
            return response()->json(['error' => true, 'message' => 'Não foi possível salvar a sua senha']);

        if (!empty($user->user_password_reset_timestamp) && ($user->user_password_reset_timestamp + 3600) < time())
            return response()->json(['error' => true, 'message' => 'O Token da URL foi expirado']);

        $user->user_password_hash = bcrypt($request->password);
        $user->user_password_reset_hash = NULL;
        $user->user_password_reset_timestamp = NULL;
        $user->save();
        return response()->json(['error' => false, 'message' => 'Sua senha foi salva com sucesso, você será redirecionado em breve.']);
    }

    public static function clinicas() {
        $config = VariavelDoSistema::first();

        return self::where('deleted', 0)
                        ->where('user_active', 1)
                        ->where('user_account_type', $config->clinica)
                        ->orderBy("user_first_name")
                        ->get();
    }
}
