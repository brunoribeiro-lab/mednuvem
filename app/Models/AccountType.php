<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Providers\AuthServiceProvider;
use Illuminate\Support\Carbon;
use App\Providers\Utils;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\VariavelDoSistema;
use App\Models\SystemAccess;
use Illuminate\Support\Facades\Session;

class AccountType extends Model {

    use HasFactory;

    protected $primaryKey = 'ID';
    private static $menus = [
        "menu" => 5,
        "submenu" => 12,
        "subsubmenu" => null,
    ];
    protected $table = '_ACCOUNT_TYPE';
    private static $actions = []; // ações do CRUD
    public $timestamps = false;
    protected $fillable = [
        'DELETED',
        'USER_CREATED',
        'USER_UPDATED',
        'NAME',
        'DESCRIPTION',
        'ROOT_ACCESS',
        'UPDATED',
        'CREATED'
    ];

    public function users() {
        return $this->hasMany(Users::class, '_ACCOUNT_TYPE', 'ID');
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
            2 => "NAME",
            3 => "DESCRIPTION",
            4 => "CREATED"
        ];

        $sSearch = trim($request->input('sSearch'));

        $query = AccountType::where('DELETED', 0);

        if (!empty($sSearch)) {
            $query->where(function ($q) use ($sSearch) {
                $q->orWhere('NAME', 'like', "%$sSearch%")
                        ->orWhere('DESCRIPTION', 'like', "%$sSearch%");
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
     * Cria um padrão de listagem para o plugin Jquery Datatable
     * 
     * @static
     * @access public
     * @param Object $request
     * @return json
     */
    public static function datatable(Request $request) {
        $variaveisDoSistema = VariavelDoSistema::find(1);
        $cargos = self::sql_listar($request);
        $total = count(self::sql_listar($request, true));
        self::$actions = AuthServiceProvider::acoes(self::$menus['menu'], self::$menus['submenu'], self::$menus['subsubmenu']);

        $row = array();
        foreach ($cargos as $cargo) {
            $nice = Carbon::parse($cargo->CREATED)->diffForHumans();
            $updated = Utils::dataCompletaPTBR($cargo->CREATED);
            $row[] = [
                '',
                Auth::user()->user_account_type !== $cargo->ID && $variaveisDoSistema->CARGO_EMPRESA !== $cargo->ID ? self::checkBox($cargo->ID) : '', // ignorar cargo de empresa
                strlen($cargo->NAME) >= 50 ? sprintf("<abbr title='%s'>%s</abbr>", $cargo->NAME, Str::limit($cargo->NAME, 50)) : $cargo->NAME,
                strlen($cargo->DESCRIPTION) >= 50 ? sprintf("<abbr title='%s'>%s</abbr>", $cargo->DESCRIPTION, Str::limit($cargo->DESCRIPTION, 50)) : $cargo->DESCRIPTION,
                "<abbr title='em {$updated}'>{$nice}</abbr>",
                self::actionButton($cargo, $variaveisDoSistema)
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

    public function salvar(Request $request, $id) {
        if (!AuthServiceProvider::acao($id ? 'ACCESS_UPDATE' : 'ACCESS_ADD', self::$menus['menu'], self::$menus['submenu'], self::$menus['subsubmenu']))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        if ($id && !$campo = AccountType::find($id))
            return response()->json(['error' => true, 'msg' => 'Registro não encontrado']);

        $rules = [
            'name' => 'required|string|min:3|max:200',
            'text' => 'nullable|string|min:3|max:1000',
        ];

        $messages = [
            '*.required' => 'Campo obrigatório',
            '*.min' => 'O campo deve ter no mínimo :min caracteres',
            '*.max' => 'O campo deve ter no máximo :max caracteres'
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        if ($id) {
            $cargo = AccountType::find($id);
            $cargo->USER_UPDATED = Auth::user()->user_id;
            $cargo->NAME = $request->input('name');
            $cargo->DESCRIPTION = $request->input('text');
            $cargo->ROOT_ACCESS = $request->input('root_mode') ? 1 : 0;
            $cargo->UPDATED = now();
            $cargo->save();
        } else {
            $add = [
                'USER_CREATED' => Auth::user()->user_id,
                'NAME' => $request->input('name'),
                'DESCRIPTION' => $request->input('text'),
                'ROOT_ACCESS' => $request->input('root_mode') ? 1 : 0,
                "CREATED" => now()
            ];
            $cargo = AccountType::create($add);
        }

        if (!$cargo)
            return response()->json(['error' => true, 'msg' => 'Não foi possível adicionar um novo Cargo']);

        if ($id) {
            $msg = 'Cargo do Sistema foi salvo com sucesso';
            Activity::novo("Edição de Cargo do Sistema - {$request->input('name')}", "edit");
        } else {
            $msg = 'Cargo do Sistema cadastrado com sucesso';
            Activity::novo("Cadastro de Cargo do Sistema - {$request->input('name')}");
        }
        return response()->json(['error' => false, 'msg' => $msg]);
    }

    /**
     * Remover Usuário
     * 
     * @param array $ids Ids do usuários
     * @return json
     */
    public static function remover($ids) {
        // Atualizar os registros na tabela 'empresas' para 'deletado' = 1
        AccountType::whereIn('ID', $ids)->update(['DELETED' => 1]);

        // Registrar a atividade
        if (count($ids) > 1) {
            Activity::novo(sprintf("Remoção de %d Cargos do Sistema", count($ids)), "trash-alt");
        } else {
            Activity::novo("Remoção de um Cargo do Sistema", "trash-alt");
        }
        $msg = count($ids) > 1 ? "Cargos do Sistema Excluidos com sucesso !" : "Cargo do Sistema Excluido com sucesso !";
        return response()->json(['error' => false, 'msg' => $msg]);
    }

    public static function checkBox($id) {
        if ((int) Auth::user()->user_id == (int) $id)
            return '';

        return "<div class=\" check-default\">
  <input type=\"checkbox\" name=\"checkbox[]\" value=\"{$id}\" id=\"checkbox{$id}\">
  <label for=\"checkbox{$id}\"></label>
</div>";
    }

    public static function actionButton($data, $variaveisDoSistema) {
        $buttons = [];
        if (self::$actions['preview'])
            $buttons[] = "<button class=\"btn btn-white goPreview\" type=\"button\"  data-id=\"{$data->ID}\" title=\"Detalhes do Usuário do Sistema\"><i class=\"fa fa-eye\"></i> </button>";

        if (self::$actions['update'])
            $buttons[] = "<button class=\"btn btn-primary goUpdate\" type=\"button\" data-id=\"{$data->ID}\" title=\"Editar Usuário do Sistema\"><i class=\"fas fa-pencil-alt\"></i> </button>";

        if (self::$actions['remove'] && Auth::user()->user_account_type !== $data->ID && $variaveisDoSistema->CARGO_EMPRESA !== $data->ID)
            $buttons[] = "<button class=\"btn btn-danger goRem\" type=\"button\" data-id=\"{$data->ID}\" title=\"Excluir Usuário do Sistema\" data-toggle=\"modal\" data-target=\"#myModalRem\"><i class=\"fa fa-trash\"></i> </button>";

        if (self::$actions['access'])
            $buttons[] = "<button class=\"btn btn-warning goAccess\" type=\"button\"  data-id=\"{$data->ID}\" title=\"Editar acessos menu e submenus\"><i class=\"fa fa-key\"></i> </button>";

        return implode("\n", $buttons);
    }

    /**
     * Pega o XML do menu informado
     * 
     * @static
     * @param obj $xml
     * @param int $id
     * @param bool $sub
     * @param bool $subsub
     * @return array or NULL
     */
    public static function XMLAcessoDoID($xml, $id, $sub = true, $subsub = false) {
        foreach ($xml->menu as $v) {
            $comparador = "true";
            $v = (array) $v;
            $acess = (array) $v['access'];
            unset($v['access']);
            $v['access'] = $acess;

            if (!$subsub && $sub && (int) $v['id'] == (int) $id && (string) $v['sub'] == "true" && (string) $v['subsub'] == "false")
                return $v;

            if (!$sub && $subsub && (int) $v['id'] == (int) $id && (string) $v['subsub'] == "true" && (string) $v['sub'] == "false")
                return $v;

            if (!$sub && !$subsub && (int) $v['id'] == (int) $id && (string) $v['subsub'] == "false" && (string) $v['sub'] == "false")
                return $v;
        }
        return NULL;
    }

    public static function menuXML() {
        $systemVariables = VariavelDoSistema::get()->first();
        $xml = $systemVariables->acessos;
        $content = preg_replace("/<!--.*?-->/", '', $xml);
        return simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);
    }

    public static function item($cargo, $acessos, $menu, $submenu = false, $subsubmenu = false) {
        if (empty($acessos['access'])) {
            return dd("Erro ao ler XML de acessos menu: {$menu} submenu: {$submenu} subsubmenu: {$subsubmenu}");
        }

        $data = [
            'cargo' => $cargo,
            'acessos' => $acessos,
            'menu' => $menu,
            'submenu' => $submenu,
            'subsubmenu' => $subsubmenu
        ];
        return view('configuracoes.item.acesso')->with('data', $data);
    }

    /**
     * Verificar se tem acesso e marcar como checado
     * 
     * @static
     * @access public
     * @param int $account ID do cargo
     * @param ENUM $index Ação que vai validar
     * @param int $menu ID do menu
     * @param int $sub ID do submenu
     * @param int $subsub ID do subsubmenu
     * @return string
     */
    public static function checked($account, $index = 'ACCESS_LISTING', $menu, $sub = NULL, $subsub = NULL) {
        $conditions = [
            "ACCOUNT" => $account,
            "MENU" => $menu,
            "SUBMENU" => !$sub ? NULL : $sub,
            "SUBSUBMENU" => !$subsub ? NULL : $subsub,
            $index => 1
        ];
        if (SystemAccess::where($conditions)->count() > 0)
            return ' checked=""';

        return '';
    }

    public static function salvarAcessos(Request $request) {
        if (empty($id = (int) $request->input('id')))
            return response()->json(['error' => true, 'msg' => "O campo <strong>ID</strong> não pode está vázio !"]);

        SystemAccess::where('ACCOUNT', $id)->delete();
        $actions = [
            'listing' => 'ACCESS_LISTING',
            'form' => 'ACCESS_FORM',
            'add' => 'ACCESS_ADD',
            'preview' => 'ACCESS_PREVIEW',
            'update' => 'ACCESS_UPDATE',
            'remove' => 'ACCESS_REMOVE',
            'access' => 'ACCESS_ACCESS',
            'pdf' => 'ACCESS_PDF',
            'historic' => 'ACCESS_HISTORIC',
            'resend' => 'ACCESS_RESEND'
        ];
        // melhorar futuramente, hoje insere cada ação em um registro no banco, para cada menu, submenu e ou subsubmenu
        // futuramente agrupar para por todas as ações de um grupo de menu, submenu e ou subsubmenu em um registro.
        foreach ($actions as $action => $dbColumn) {
            if ($request->has($action)) {
                $data = $request->input($action);
                if (is_array($data)) {
                    foreach ($data as $menu => $submenuData) {
                        if (is_array($submenuData)) {
                            foreach ($submenuData as $submenu => $subsubmenuData) {
                                if (is_array($subsubmenuData)) {
                                    foreach ($subsubmenuData as $subsubmenu => $value) {
                                        self::insertIntoSystemAccess($id, $dbColumn, $menu, $submenu, $subsubmenu);
                                    }
                                } else {
                                    self::insertIntoSystemAccess($id, $dbColumn, $menu, $submenu, null);
                                }
                            }
                        } else {
                            self::insertIntoSystemAccess($id, $dbColumn, $menu, null, null);
                        }
                    }
                }
            }
        }
        Activity::novo("Edição de Acesso do Cargo", "edit");
        return response()->json(['error' => false, 'msg' => "Edição de acessos do cargo foi salva com sucesso"]);
    }

    private static function insertIntoSystemAccess($account, $dbColumn, $menu, $submenu = null, $subsubmenu = null) {
        $systemAccess = new SystemAccess();
        $systemAccess->USER = Auth::id();
        $systemAccess->MENU = $menu;
        $systemAccess->SUBMENU = $submenu;
        $systemAccess->SUBSUBMENU = $subsubmenu;
        $systemAccess->$dbColumn = 1;
        $systemAccess->ACCOUNT = $account;
        $systemAccess->UPDATED = now();
        $systemAccess->save();
    }
}
