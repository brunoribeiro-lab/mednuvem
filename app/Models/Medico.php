<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Providers\AuthServiceProvider;
use Illuminate\Support\Carbon;
use App\Providers\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\Users;
use App\Providers\Validar;
use App\Providers\MenusCollection;
use App\Providers\Notificacao;

class Medico extends Model {

    use HasFactory;

    protected $table = 'medicos';
    protected $fillable = [
        'id',
        'deletado',
        'id_usuario',
        'grupo',
        'criado_por',
        'atualizado_por',
        'nome',
        'telefone',
        'setor',
        'funcao',
        'criado_as',
        'atualizado_as',
    ];
    public $timestamps = false;
    private static $actions = []; // ações do CRUD

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
        $sortMap = [
            2 => "id",
            3 => "nome",
            4 => "telefone",
            5 => "meduser.user_name",
            6 => "criado_as",
        ];
        $sSearch = trim($request->input('sSearch'));
        $query = self::join('users as creator', function ($join) {
                    $join->on('medicos.criado_por', '=', 'creator.user_id')
                    ->where('creator.deleted', 0);
                })
                ->join('users as meduser', function ($join) {
                    $join->on('medicos.id_usuario', '=', 'meduser.user_id')
                    ->where('meduser.deleted', 0);
                })
                ->join('users as source', function ($join) {
                    $join->on('medicos.grupo', '=', 'source.user_id')
                    ->where('source.deleted', 0);
                })
                ->leftJoin('users as updater', function ($join) {
                    $join->on('medicos.atualizado_por', '=', 'updater.user_id')
                    ->where('updater.deleted', 0);
                })
                ->where('medicos.deletado', 0)
                ->select(
                        'medicos.*',
                        'creator.user_first_name as creator_first_name',
                        'creator.user_last_name as creator_last_name',
                        'updater.user_first_name as updater_first_name',
                        'updater.user_last_name as updater_last_name',
                        'meduser.user_name',
                        'meduser.user_email',
                )
                ->selectRaw('CONCAT(source.user_first_name, IFNULL(CONCAT(" ", source.user_last_name), "")) as source_fullname')
                ->selectRaw('CONCAT(creator.user_first_name, IFNULL(CONCAT(" ", creator.user_last_name), "")) as creator_fullname')
                ->selectRaw('CONCAT(updater.user_first_name, IFNULL(CONCAT(" ", updater.user_last_name), "")) as updater_fullname')
                ->selectRaw('CONCAT(meduser.user_first_name, IFNULL(CONCAT(" ", meduser.user_last_name), "")) as medico_fullname');

        # se for root, mostra todas os clientes
        if (!Session::get('is_root'))
            $query->where('medicos.grupo', Auth::user()->group);

        if (!empty($sSearch)) {
            $query->where(function ($q) use ($sSearch) {
                $q
                        ->orWhere('nome', 'like', "%$sSearch%");
                // pesquisar pelo código
                if (preg_match('/MED/i', $sSearch))
                    $q->orWhere(function ($query) use ($sSearch) {
                        $query->where('id', 'like', '%' . (int) Utils::extrairNum($sSearch) . '%');
                    });
            });
        }

        $query->orderBy($sortMap[intval($request->input('iSortCol_0', 0))], $request->input('sSortDir_0', 'asc'));
        if (!$todos)
            $query->limit($request->input('iDisplayLength'))->offset($request->input('iDisplayStart'));

        return $query->get();
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
        $clientes = self::sql_listar($request);
        $total = count(self::sql_listar($request, true));
        self::$actions = AuthServiceProvider::acoes(...array_values(MenusCollection::$menus['medicos']));
        $row = array();
        foreach ($clientes as $cliente) {
            $nice = Carbon::parse($cliente->criado_as)->diffForHumans();
            $updated = Utils::dataCompletaPTBR($cliente->criado_as);
            $dados = [
                '',
                self::checkBox($cliente->id),
                sprintf("MED%s", str_pad($cliente->id, 6, '0', STR_PAD_LEFT)),
                strlen($cliente->nome) >= 50 ? sprintf("<abbr title='%s'>%s</abbr>", $cliente->nome, Str::limit($cliente->nome, 50)) : $cliente->nome,
                $cliente->telefone ? Utils::mask($cliente->telefone, Utils::$MASK_PHONE) : '-',
                strlen($cliente->user_name) == 11 ? Utils::mask($cliente->user_name, Utils::$MASK_CPF) : Utils::mask($cliente['user_name'], Utils::$MASK_CNPJ),
                "<abbr title='em {$updated}'>{$nice}</abbr>",
                self::actionButton($cliente)
            ];

            $row[] = $dados;
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
    public static function datatableHistorico(Request $request, $id) {
        $atividades = Users::sql_listar_historico($request, $id);
        $total = count(Users::sql_listar_historico($request, $id, true));
        self::$actions = AuthServiceProvider::acoes(...array_values(MenusCollection::$menus['medicos']));

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

    public static function actionButton($query, $index = "default") {
        if ($index == 'default') {
            $buttons = [];
            if (self::$actions['preview'])
                $buttons[] = "<button class=\"btn btn-white goPreview\" type=\"button\"  data-id=\"{$query['id']}\" title=\"Detalhes do Médico\"><i class=\"fa fa-eye\"></i> </button>";

            if (self::$actions['historic'])
                $buttons[] = "<button class=\"btn btn-white goHistoric\" title=\"Histórico do Médico\" type=\"button\" data-id=\"{$query['id_usuario']}\"><i class=\"far fa-shoe-prints\"></i> </button>";

            if (self::$actions['update'])
                $buttons[] = "<button class=\"btn btn-primary goUpdate\" type=\"button\" data-id=\"{$query['id']}\" title=\"Editar Médico\"><i class=\"fas fa-pencil-alt\"></i> </button>";

            if (self::$actions['remove'])
                $buttons[] = "<button class=\"btn btn-danger goRem\" type=\"button\" data-id=\"{$query['id']}\" title=\"Excluir Médico\" data-toggle=\"modal\" data-target=\"#myModalRem\"><i class=\"fa fa-trash\"></i> </button>";


            return implode("\n", $buttons);
        }
    }

    public static function checkBox($id) {
        return "<div class=\" check-default\">
  <input type=\"checkbox\" name=\"checkbox[]\" value=\"{$id}\" id=\"checkbox{$id}\">
  <label for=\"checkbox{$id}\"></label>
</div>";
    }

    /**
     * Salvar/Cadastrar no banco de dados
     * 
     * @access public
     * @param Request $request
     * @param int|NULL $id
     * @return json
     * @throws ValidationException
     */
    public function salvar(Request $request, $id = NULL) {
        if (!AuthServiceProvider::acao($id ? 'ACCESS_UPDATE' : 'ACCESS_ADD', ...array_values(MenusCollection::$menus['medicos'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $campo = NULL;
        // se for root, verifica se o cliente existe
        if (Session::get('is_root') && $id && !$campo = self::where('id', $id)->where('deletado', 0)->first())
            return response()->json(['error' => true, 'msg' => 'Registro não encontrado']);

        // se não for root, verifica se o cliente existe com o mesmo grupo de usuário
        if (!Session::get('is_root') && $id && !$campo = self::where('id', $id)->where('grupo', Auth::user()->group)->where('deletado', 0)->first())
            return response()->json(['error' => true, 'msg' => 'Registro não encontrado']);

        $config = VariavelDoSistema::first();
        $setor = '';
        // setor apenas do grupo do usuário
        if (!Session::get('is_root'))
            $setor = sprintf(",grupo,%s", Auth::user()->group);

        $rules = [
            'nome' => [
                'required',
                'string',
                'min:3',
                'max:200',
                'nomeCompleto'
            ],
            'tipo_login' => 'required|in:cnpj,cpf',
            'email' => [
                'required',
                'email',
                'femail', // validação personalizada de email
                'unique:users,user_email,' . ($id ? $campo->id_usuario : 'NULL') . ",user_id,deleted,0,user_active,1",
            ],
            'setor' => [
                'required',
                'integer',
                'min:0',
                "exists_in:setor,id,deletado,0{$setor}"
            ],
            'funcao' => [
                'required',
                'integer',
                'min:0',
                "exists_in:funcao,id,deletado,0"
            ],
            'username' => [
                'required',
                function ($attribute, $value, $fail) use ($request, $id, $campo) {
                    $onlyNumbers = preg_replace("/[^0-9]/", "", $value);

                    $query = DB::table('users')
                            ->whereRaw("REPLACE(user_name, ' ', '') = ?", [$onlyNumbers])
                            ->where('deleted', 0)
                            ->where('user_active', 1);

                    if ($id) {
                        $query->where('user_id', '<>', $campo->id_usuario);
                    }

                    if ($query->exists()) {
                        $fail("O CPF/CNPJ já existe.");
                    }
                },
                function ($attribute, $value, $validator) use ($request) {
                    // Validar CPF ou CNPJ, dependendo do valor de tipo_login
                    $onlyNumbers = preg_replace('/\D/', '', $value);

                    // Verifica se tipo_login é cpf e valida CPF
                    if ($request->input('tipo_login') === 'cpf' && !Validar::CPF($onlyNumbers)) {
                        $validator('O CPF é inválido.');
                    }

                    // Verifica se tipo_login é cnpj e valida CNPJ
                    if ($request->input('tipo_login') === 'cnpj' && !Validar::CNPJ($onlyNumbers)) {
                        $validator('O CNPJ é inválido.');
                    }
                },
            ],
            'celular' => [
                'nullable',
                'min:16',
                'max:16',
                'Telefone',
            ],
        ];
        if ($id && $request->input('mudar_senha')) {
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
        if (Session::get('is_root') && !$id)
            $rules['clinica'] = [
                'required',
                'integer',
                'min:0',
                "exists_in:users,user_id,deleted,0,user_account_type,{$config->clinica}"
            ];


        if ($request->input('gerar_senha')) {
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

        $messages = [
            'celular.unique' => 'O Telefone do Motorista já está em uso',
            'email.unique' => 'O Email já está em uso',
            '*.*.required_with' => 'Campo obrigatório',
            '*.required' => 'Campo obrigatório',
            '*.min' => 'O campo deve ter no mínimo :min caracteres',
            '*.max' => 'O campo deve ter no máximo :max caracteres',
            '*.in' => 'Campo Inválido',
            '*.uf' => 'Campo inválido',
            '*.telefone' => 'Campo inválido',
            '*.integer' => 'Campo inválido, apenas números',
            '*.confirmed' => "As senhas não são iguais",
            '*.email' => "Email já existe",
            '*.nome_completo' => "Campo inválido",
            '*.femail' => "Email inválido",
            'clinica.exists_in' => "A Clinica não existe"
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails())
            throw new ValidationException($validator);

        // verifica se a função é do setor
        if (!Função::buscarSetor($request->input('funcao'), $request->input('setor')))
            return response()->json(['error' => true, 'msg' => 'Função inválida']);

        if ($id) { // atualizar no banco
            $medico = self::find($id);
            $medico->atualizado_por = Auth::user()->user_id;
            $medico->setor = $request->input('setor');
            $medico->funcao = $request->input('funcao');
            #$medico->nome = $request->input('nome');
            $medico->telefone = Utils::extrairNum($request->input('celular'));
            $medico->atualizado_as = now();
            $medico->save();
            // salvar usuario 
            $user = Users::find($medico->id_usuario);
            /* $extrairNome = explode(" ", $request->nome);
              $primeiro_nome = $extrairNome[0];
              unset($extrairNome[0]);
              $user->user_first_name = $primeiro_nome;
              $user->user_last_name = count($extrairNome) > 0 ? implode(' ', $extrairNome) : NULL; */
            $user->user_email = $request->email;
            $user->user_name = Utils::extrairNum($request->username);
            if ($id && $request->input('mudar_senha')) {
                $user->user_password_hash = bcrypt($request->password);
            }

            $user->save();
        } else { // cadastrar no banco
            // criar usuario do sistema
            $extrairNome = explode(" ", $request->nome);
            $primeiro_nome = $extrairNome[0];
            unset($extrairNome[0]);
            $senha_aleatoria = Str::random(8);
            $grupo = Auth::user()->group;
            if (Session::get('is_root'))
                $grupo = $request->input('clinica');

            $usuario = [
                'group' => $grupo,
                'user_name' => Utils::extrairNum($request->username),
                'user_first_name' => $primeiro_nome,
                'user_last_name' => count($extrairNome) > 0 ? implode(' ', $extrairNome) : NULL,
                'user_email' => $request->email,
                'user_account_type' => 3,
                'user_active' => 1,
                'user_password_hash' => bcrypt($senha_aleatoria),
                'user_creation_timestamp' => now()
            ];
            // senha personalizada
            if ($request->input('gerar_senha')) // futuramente adicionar um envio de email aqui
                $usuario['user_password_hash'] = bcrypt($senha_aleatoria = $request->password);

            $user = Users::create($usuario);
            $add = [
                'id_usuario' => $user->user_id,
                'grupo' => Auth::user()->group,
                'criado_por' => Auth::user()->user_id,
                'setor' => $request->input('setor'),
                'funcao' => $request->input('funcao'),
                'nome' => $request->input('nome'),
                'telefone' => Utils::extrairNum($request->input('celular')),
                "criado_as" => now()
            ];
            $medico = self::create($add);
            $user = [
                'fullName' => $request->nome,
                'user_email' => $request->email,
                'user_name' => Utils::extrairNum($request->username),
                'user_password' => $senha_aleatoria
            ];
            if (!Notificacao::formNovoMedico($user, $request->email))
                return response()->json(['error' => true, 'msg' => 'Não foi possível disparar o email']);
        }

        if (!$medico)
            return response()->json(['error' => true, 'msg' => 'Não foi possível salvar o Motorista']);

        if ($id) {
            $msg = 'Médico foi salvo com sucesso';
            Activity::novo("Edição do Médico", "edit");
        } else {
            $msg = 'Médico cadastrado com sucesso';
            Activity::novo("Cadastro do Médico - {$request->input('nome')}");
        }
        return response()->json(['error' => false, 'msg' => $msg]);
    }

    /**
     * buscar médico pela ID
     * 
     * @param int $idMed id do Médico
     * @return object Eloquent Database
     */
    public static function buscar($idMed) {
        $where = [
            'medicos.deletado' => 0,
            'medicos.id' => $idMed
        ];

        if (!Session::get('is_root'))
            $where['medicos.grupo'] = Auth::user()->group;

        $med = self::join('users as creator', 'medicos.criado_por', '=', 'creator.user_id')
                ->join('users as meduser', 'medicos.id_usuario', '=', 'meduser.user_id')
                ->join('users as source', 'medicos.grupo', '=', 'source.user_id')
                ->leftJoin('users as updater', 'medicos.atualizado_por', '=', 'updater.user_id')
                ->where($where)
                ->select(
                        'medicos.*',
                        'creator.user_first_name as creator_first_name',
                        'creator.user_last_name as creator_last_name',
                        'updater.user_first_name as updater_first_name',
                        'updater.user_last_name as updater_last_name',
                        'meduser.user_name',
                        'meduser.user_email',
                )
                ->selectRaw('CONCAT(source.user_first_name, IFNULL(CONCAT(" ", source.user_last_name), "")) as source_fullname')
                ->selectRaw('CONCAT(creator.user_first_name, IFNULL(CONCAT(" ", creator.user_last_name), "")) as creator_fullname')
                ->selectRaw('CONCAT(updater.user_first_name, IFNULL(CONCAT(" ", updater.user_last_name), "")) as updater_fullname')
                ->selectRaw('CONCAT(meduser.user_first_name, IFNULL(CONCAT(" ", meduser.user_last_name), "")) as medico_fullname')
                ->first();

        return $med;
    }

    /**
     * buscar médicos pela ID
     *  
     * @return object Eloquent Database
     */
    public static function listar() {
        $where = [
            'medicos.deletado' => 0
        ];

        if (!Session::get('is_root'))
            $where['medicos.grupo'] = Auth::user()->group;

        $med = self::join('users as creator', 'medicos.criado_por', '=', 'creator.user_id')
                ->join('users as meduser', 'medicos.id_usuario', '=', 'meduser.user_id')
                ->join('users as source', 'medicos.grupo', '=', 'source.user_id')
                ->leftJoin('users as updater', 'medicos.atualizado_por', '=', 'updater.user_id')
                ->where($where)
                ->select(
                        'medicos.*',
                        'creator.user_first_name as creator_first_name',
                        'creator.user_last_name as creator_last_name',
                        'updater.user_first_name as updater_first_name',
                        'updater.user_last_name as updater_last_name',
                        'meduser.user_name',
                        'meduser.user_email',
                )
                ->selectRaw('CONCAT(source.user_first_name, IFNULL(CONCAT(" ", source.user_last_name), "")) as source_fullname')
                ->selectRaw('CONCAT(creator.user_first_name, IFNULL(CONCAT(" ", creator.user_last_name), "")) as creator_fullname')
                ->selectRaw('CONCAT(updater.user_first_name, IFNULL(CONCAT(" ", updater.user_last_name), "")) as updater_fullname')
                ->selectRaw('CONCAT(meduser.user_first_name, IFNULL(CONCAT(" ", meduser.user_last_name), "")) as medico_fullname')
                ->get();

        return $med;
    }

    /**
     * Remover Usuário
     * 
     * @param array $ids Ids do usuários
     * @return json
     */
    public static function remover($ids) {
        # se for root pode excluir qualquer uma
        if (Session::get('is_root')) {
            // Extrai e salva os dados da consulta id_usuario
            $medicosDeletar = self::whereIn('id', $ids)->get();

            // Deleta os registros na tabela Medicos
            self::whereIn('id', $ids)->update(['deletado' => 1]);
        } else {
            // Extrai e salva os dados da consulta id_usuario
            $medicosDeletar = self::whereIn('id', $ids)
                    ->where('grupo', Auth::user()->group)
                    ->get();

            // Se não for root, verifica a segurança especial
            self::whereIn('id', $ids)
                    ->where('grupo', Auth::user()->group)
                    ->update(['deletado' => 1]);
        }

        // Deleta os registros correspondentes na tabela users
        foreach ($medicosDeletar as $medico) {
            Users::where('user_id', $medico->id_usuario)->update(['deleted' => 1]);
        }

        if (count($ids) > 1) {
            $msg = 'Médicos excluidos com sucesso';
            Activity::novo(sprintf("Remoção de %d Médicos", count($ids)), "trash-alt");
        } else {
            $msg = 'Médico excluido com sucesso ';
            Activity::novo("Remoção de um Médico", "trash-alt");
        }
        return response()->json(['error' => false, 'msg' => $msg]);
    }
}
