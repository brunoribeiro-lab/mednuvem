<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SystemSubmenu;
use App\Models\SystemSubSubmenu;
use App\Models\Notificacao;
use App\Models\FormContact;
use App\Models\FormRequisicaoExame;
use App\Providers\AuthServiceProvider;
use Illuminate\Support\Carbon;
use App\Providers\Utils;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Providers\MenusCollection;

class SystemMenu extends Model {

    use HasFactory;

    protected $primaryKey = 'ID';
    protected $table = '_SYSTEM_MENU';
    private static $actions = []; // ações do CRUD
    public $timestamps = false;
    protected $fillable = [
        'USER',
        'CODE',
        'CODE_ADD',
        'UPDATED'
    ];

    public static function __construir() {
        $result = [];

        $menus = SystemMenu::where('STATUS', 1)
                ->orderBy('POSITION', 'asc')
                ->get();

        foreach ($menus as $menu) {
            $subMenuItems = [];
            if ($menu->SUB > 0) {
                $submenus = SystemSubmenu::join('_SYSTEM_MENU', '_SYSTEM_SUBMENU.MENU', '=', '_SYSTEM_MENU.ID')
                        ->where('_SYSTEM_SUBMENU.STATUS', 1)
                        ->where('_SYSTEM_SUBMENU.MENU', $menu->ID)
                        ->select('_SYSTEM_SUBMENU.*', '_SYSTEM_MENU.NAME as origem_menu')
                        ->orderBy('_SYSTEM_SUBMENU.POSITION', 'asc')
                        ->get();

                foreach ($submenus as $submenu) {
                    $subsubmenus = [];

                    if ($submenu->SUB) {
                        $subsubmenus = SystemSubsubmenu::join('_SYSTEM_SUBMENU', '_SYSTEM_SUBSUBMENU.SUBMENU', '=', '_SYSTEM_SUBMENU.ID')
                                ->where('_SYSTEM_SUBSUBMENU.SUBMENU', $submenu->ID)
                                ->where('_SYSTEM_SUBSUBMENU.STATUS', 1)
                                ->select('_SYSTEM_SUBSUBMENU.*', '_SYSTEM_SUBMENU.NAME as origem_submenu')
                                ->orderBy('_SYSTEM_SUBSUBMENU.POSITION', 'asc')
                                ->get()
                                ->toArray();
                    }
                    $subMenuItems[] = array_merge(['submenus' => $subsubmenus], $submenu->toArray());
                }
            }

            if (!$menu->SUB || count($subMenuItems) > 0) {
                $result[] = [
                    'id' => $menu->ID,
                    'title' => $menu->NAME,
                    'icon' => $menu->ICON,
                    'sub' => $menu->SUB,
                    'link' => $menu->LINK,
                    'submenu' => $subMenuItems
                ];
            }
        }

        #return $result;
        return self::detectAccess($result);
    }

    private static function detectAccess(array $result) {
        if (Session::get('is_root')) // Se tem acesso root, retorna tudo.
            return $result;

        $final = [];
        foreach ($result as $v) {
            if (!$v['sub'] && SystemAccess::where('ACCOUNT', Auth::user()->user_account_type)->where('MENU', $v["id"])->count() > 0) {
                $final[] = $v;
            } elseif ($v['sub']) {
                $sub = [];
                foreach ($v['submenu'] as $s) {
                    if (SystemAccess::where('ACCOUNT', Auth::user()->user_account_type)->where('SUBMENU', $s["ID"])->count() > 0) {
                        if ($s['submenus']) { // esse submenu tem subsubmenu
                            $subsub = [];
                            foreach ($s['submenus'] as $ss) {
                                if (SystemAccess::where('ACCOUNT', Auth::user()->user_account_type)->where('SUBSUBMENU', $ss["ID"])->count() > 0)
                                    $subsub[] = $ss;
                            }

                            unset($s['submenus']); // remove os subsubmenus da lista
                            $s['submenus'] = $subsub; // adiciona os novos subsubmenus que tem acesso
                            $sub[] = $s;
                        }

                        if (!$s['submenus']) // esse submenu não tem subsubmenu
                            $sub[] = $s;
                    }
                }

                if (count($sub) > 0) {
                    $final[] = [
                        'id' => $v["id"],
                        'title' => $v['title'],
                        'icon' => $v['icon'],
                        'sub' => (int) $v['sub'],
                        'link' => config('app.url') . $v['link'],
                        'submenu' => $sub
                    ];
                }
            }
        }

        return $final;
    }

    /**
     * Pegar Identificador de página
     * 
     * @static
     * @access public
     * @param bool $codAdd Pegar página Adicionar
     * @return string
     */
    public static function identificador($codAdd = false) {
        $url = str_replace("/SGS", "SGS", request()->path());
        $extrair = explode("/", $url);
        $vazio = "";
        // Menu
        if (count($extrair) == 1 || count($extrair) == 2) {
            $menu = SystemMenu::where('LINK', $url)->first();
            if (!$menu)
                return $vazio;

            return $codAdd ? "{$menu->CODE_ADD} - {$menu->NAME} > Adicionar" : "{$menu->CODE} - {$menu->NAME}";
        }

        // Submenu
        if (count($extrair) == 3) {
            $submenu = SystemSubmenu::with('menu')
                    ->where('LINK', $url)
                    ->first();
            if (!$submenu)
                return $vazio;

            return $codAdd ? "{$submenu->CODE_ADD} - {$submenu->menu->NAME} > {$submenu->NAME} > Adicionar" : "{$submenu->CODE} - {$submenu->menu->NAME} > {$submenu->NAME}";
        }

        // Submenu do submenu
        if (count($extrair) == 4) {
            $subsubmenu = SystemSubsubmenu::with(['submenu', 'submenu.menu'])
                    ->where('LINK', $url)
                    ->first();
            if (!$subsubmenu)
                return $vazio;

            return $codAdd ? "{$subsubmenu->CODE_ADD} - {$subsubmenu->submenu->menu->NAME} > {$subsubmenu->submenu->NAME} > {$subsubmenu->NAME} > Adicionar" : "{$subsubmenu->CODE} - {$subsubmenu->submenu->menu->NAME} > {$subsubmenu->submenu->NAME} > {$subsubmenu->NAME}";
        }

        return $codAdd ? NULL : $vazio;
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
        $sortMap = [
            1 => "CODE",
            2 => "NAME",
            3 => "LINK",
            4 => "POSITION"
        ];
        $sSearch = trim($request->input('sSearch'));
        $query = SystemMenu::where('STATUS', 1);

        if (!empty($sSearch)) {
            $query->where(function ($q) use ($sSearch) {
                $q->orWhere('CODE', 'like', "%$sSearch%")
                        ->orWhere('LINK', 'like', "%$sSearch%")
                        ->orWhere('NAME', 'like', "%$sSearch%");
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
        $menus = self::sql_listar($request);
        $total = count(self::sql_listar($request, true));
        self::$actions = AuthServiceProvider::acoes(...array_values(MenusCollection::$menus['menus']));
        $row = array();
        foreach ($menus as $menu) {
            $dados = [
                '',
                !$menu->SUB ? $menu->CODE : "-",
                strlen($menu->NAME) >= 50 ? sprintf("<abbr title='%s'>%s</abbr>", $menu->NAME, Str::limit($menu->NAME, 50)) : $menu->NAME,
                !$menu->SUB ? $menu->LINK : "-",
                $menu->POSITION,
                self::actionButton($menu)
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

    public function salvar(Request $request, $id) {
        if (!AuthServiceProvider::acao('ACCESS_UPDATE', ...array_values(MenusCollection::$menus['menus'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        if (!SystemMenu::find($id))
            return response()->json(['error' => true, 'msg' => 'Registro não encontrado']);

        $rules = [
            'id' => 'required|integer|exists:_SYSTEM_MENU,ID',
            'cod' => 'required|integer|min:100|max:999|unique:_SYSTEM_MENU,CODE,' . $request->id,
            'pos' => 'required|integer',
        ];
        $messages = [
            '*.required' => 'Campo obrigatório',
            '*.unique' => 'Código em Uso',
            '*.min' => 'O campo deve ter no mínimo :min caracteres',
            '*.max' => 'O campo deve ter no máximo :max caracteres',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        if ($submenuQuery = SystemSubmenu::where('CODE', $request->cod)->first()) {
            return response(['error' => true, 'msg' => "O campo Código esta sendo usado pelo submenu <strong>{$submenuQuery->NAME}</strong>"]);
        }

        if ($subsubmenuQuery = SystemSubsubmenu::where('CODE', $request->cod)->first()) {
            return response(['error' => true, 'msg' => "O campo Código esta sendo usado pelo submenu do submenu <strong>{$subsubmenuQuery->NAME}</strong>"]);
        }

        $menu = SystemMenu::find($id);
        $menu->USER = Auth::user()->user_id;
        $menu->CODE = $request->input('cod');
        $menu->POSITION = $request->input('pos');
        $menu->UPDATED = now();
        if ($menu->CODE_ADD) // se disponível
            $menu->CODE_ADD = $request->input('cod_add');

        $menu->save();

        if (!$menu)
            return response()->json(['error' => true, 'msg' => 'Não foi possível salvar o Menu']);


        $msg = 'Menu foi salvo com sucesso';
        Activity::novo("Edição de Menu - {$menu->NAME}", "edit");
        return response()->json(['error' => false, 'msg' => $msg]);
    }

    public static function actionButton($query, $index = "default") {
        if ($index == 'default') {
            $buttons = [];
            if (self::$actions['preview'])
                $buttons[] = "<button class=\"btn btn-white goPreview\" type=\"button\"  data-id=\"{$query['ID']}\" title=\"Detalhes do Menu\"><i class=\"fa fa-eye\"></i> </button>";

            if (!$query->SUB && self::$actions['update'])
                $buttons[] = "<button class=\"btn btn-primary goUpdate\" type=\"button\" data-id=\"{$query['ID']}\" title=\"Editar Menu\"><i class=\"fas fa-pencil-alt\"></i> </button>";

            if ($query->SUB)
                $buttons[3] = "<button class=\"btn btn-info goSubmenu\" type=\"button\"  data-id=\"{$query["ID"]}\" title=\"Gerenciar Submenus\"><i class=\"far fa-list-alt\"></i> </button>";

            return implode("\n", $buttons);
        }
    }
}
