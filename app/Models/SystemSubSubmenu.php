<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Providers\AuthServiceProvider;
use Illuminate\Support\Carbon;
use App\Providers\Utils;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class SystemSubSubmenu extends Model {

    use HasFactory;

    protected $primaryKey = 'ID';
    protected $table = '_SYSTEM_SUBSUBMENU';
    private static $actions = []; // ações do CRUD
    public $timestamps = false;
    protected $fillable = [
        'USER',
        'CODE',
        'CODE_ADD',
        'UPDATED'
    ];

    public function submenu() {
        return $this->belongsTo(SystemSubmenu::class, 'SUBMENU', 'ID');
    }

    /**
     * Cria um padrão de listagem para o plugin Jquery Datatable
     * 
     * @static
     * @access public
     * @param Object $request
     * @return json
     */
    public static function datatable(Request $request, $idMenu) {
        $menus = self::sql_listar($request, $idMenu);
        $total = count(self::sql_listar($request, $idMenu, true));
        self::$actions = AuthServiceProvider::acoes(5, 5, 10);
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

    /**
     * Criar a SQL de listar 
     * 
     * @static
     * @access private
     * @param Object $request
     * @param boolean $todos Se true Ignora o limite
     * @return array
     */
    private static function sql_listar($request, $idMenu, $todos = false) {
        $sortMap = [
            1 => "CODE",
            2 => "NAME",
            3 => "LINK",
            4 => "POSITION"
        ];
        $sSearch = trim($request->input('sSearch'));
        $query = SystemSubSubmenu::where('STATUS', 1)->where('SUBMENU', $idMenu);
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

    public static function actionButton($query, $index = "default") {
        if ($index == 'default') {
            $buttons = [];
            if (self::$actions['preview'])
                $buttons[] = "<button class=\"btn btn-white goPreviewSubSubmenu\" type=\"button\"  data-id=\"{$query['ID']}\" title=\"Detalhes do SubSubMenu\"><i class=\"fa fa-eye\"></i> </button>";

            if (!$query->SUB && self::$actions['update'])
                $buttons[] = "<button class=\"btn btn-primary goUpdateSubSubmenu\" type=\"button\" data-id=\"{$query['ID']}\" title=\"Editar SubSubMenu\"><i class=\"fas fa-pencil-alt\"></i> </button>";


            return implode("\n", $buttons);
        }
    }

    public function salvar(Request $request, $id) {
        if (!AuthServiceProvider::acao('ACCESS_UPDATE', 5, 17, 10))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        if (!SystemSubSubmenu::find($id))
            return response()->json(['error' => true, 'msg' => 'Registro não encontrado']);

        $rules = [
            'id' => 'required|integer|exists:_SYSTEM_SUBSUBMENU,ID',
            'cod' => 'required|integer|min:100|max:999|unique:_SYSTEM_SUBSUBMENU,CODE,' . $request->id,
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

        if ($menuQuery = SystemMenu::where('CODE', $request->cod)->first()) {
            return response(['error' => true, 'msg' => "O campo Código esta sendo usado pelo Menu <strong>{$menuQuery->NAME}</strong>"]);
        }

        if ($submenuQuery = SystemSubmenu::where('CODE', $request->cod)->first()) {
            return response(['error' => true, 'msg' => "O campo Código esta sendo usado pelo submenu <strong>{$submenuQuery->NAME}</strong>"]);
        }

        $subsubmenu = SystemSubSubmenu::find($id);
        $subsubmenu->USER = Auth::user()->user_id;
        $subsubmenu->CODE = $request->input('cod');
        $subsubmenu->POSITION = $request->input('pos');
        $subsubmenu->UPDATED = now();
        if ($subsubmenu->CODE_ADD) // se disponível
            $subsubmenu->CODE_ADD = $request->input('cod_add');

        $subsubmenu->save();

        if (!$subsubmenu)
            return response()->json(['error' => true, 'msg' => 'Não foi possível salvar o Subsubmenu']);


        $msg = 'Subsubmenu foi salvo com sucesso';
        Activity::novo("Edição de Subsubmenu - {$subsubmenu->NAME}", "edit");
        return response()->json(['error' => false, 'msg' => $msg]);
    }

}
