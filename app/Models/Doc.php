<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Providers\AuthServiceProvider;
use App\Providers\Utils;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Providers\MenusCollection;

class Doc extends Model {

    use HasFactory;

    protected $table = "doc";
    protected $fillable = [
        'doc_index', 'doc_title', 'doc_text', 'created_at', 'updated_at'
    ];
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
            2 => "doc_index",
            3 => "doc_title",
            4 => "doc_text",
            5 => "updated_at"
        ];
        $sSearch = trim($request->input('sSearch'));
        $query = Doc::query();

        if (!empty($sSearch)) {
            $query->where(function ($q) use ($sSearch) {
                $q->orWhere('doc_title', 'like', "%$sSearch%")
                        ->orWhere('doc_index', 'like', "%$sSearch%")
                        ->orWhere('doc_text', 'like', "%$sSearch%");
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
        $docs = self::sql_listar($request);
        $total = count(self::sql_listar($request, true));
        self::$actions = AuthServiceProvider::acoes(...array_values(MenusCollection::$menus['documentacao']));
        $row = array();
        foreach ($docs as $doc) {
            $nice = Carbon::parse($doc->updated_at)->diffForHumans();
            $updated = Utils::dataCompletaPTBR($doc->updated_at);
            $dados = [
                '',
                self::checkBox($doc->id),
                strlen($doc->doc_index) >= 50 ? sprintf("<abbr title='%s'>%s</abbr>", $doc->doc_index, Str::limit($doc->doc_index, 50)) : $doc->doc_index,
                strlen($doc->doc_title) >= 50 ? sprintf("<abbr title='%s'>%s</abbr>", $doc->doc_title, Str::limit($doc->doc_title, 50)) : $doc->doc_title,
                strlen($doc->doc_text) >= 50 ? sprintf("<abbr title='%s'>%s</abbr>", $doc->doc_text, Str::limit($doc->doc_text, 50)) : $doc->doc_text,
                "<abbr title='em {$updated}'>{$nice}</abbr>",
                self::actionButton($doc)
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
        if (!AuthServiceProvider::acao($id ? 'ACCESS_UPDATE' : 'ACCESS_ADD', ...array_values(MenusCollection::$menus['documentacao'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        if ($id && !Doc::find($id))
            return response()->json(['error' => true, 'msg' => 'Registro não encontrado']);

        $rules = [
            'index' => 'required|min:3|max:100',
            'title' => 'required|min:3|max:100',
            'text' => 'required|min:10|max:10000',
        ];
        $messages = [
            '*.required' => 'Campo obrigatório',
            '*.min' => 'O campo deve ter no mínimo :min caracteres',
            '*.max' => 'O campo deve ter no máximo :max caracteres',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Realizando a inserção
        if ($id) {
            $doc = Doc::find($id);
            $doc->doc_index = $request->input('index');
            $doc->doc_title = $request->input('title');
            $doc->doc_text = $request->input('text');
            $doc->created_at = now();
            $doc->save();
        } else {
            $add = [
                'doc_index' => $request->input('index'),
                'doc_title' => $request->input('title'),
                'doc_text' => $request->input('text'),
                "updated_at" => now()
            ];
            $doc = Doc::create($add);
        }

        if (!$doc)
            return response()->json(['error' => true, 'msg' => 'Não foi possível adicionar uma nova Documentação']);

        if ($id) {
            $msg = 'Documentação foi salva com sucesso';
            Activity::novo("Edição de Documentação - {$request->input('index')}", "edit");
        } else {
            $msg = 'Documentação cadastrada com sucesso';
            Activity::novo("Cadastro de Documentação - {$request->input('index')}");
        }
        return response()->json(['error' => false, 'msg' => $msg]);
    }

    /**
     * Remover Documentações
     * 
     * @param array $ids
     * @return json
     */
    public static function remover($ids) {
        Doc::whereIn('id', $ids)->delete();
        if (count($ids) > 1) {
            $msg = 'Documentações excluidas com sucesso';
            Activity::novo(sprintf("Remoção de %d Documentações", count($ids)), "trash-alt");
        } else {
            $msg = 'Documentação excluida com sucesso';
            Activity::novo("Remoção de um Documentação", "trash-alt");
        }
        return response()->json(['error' => false, 'msg' => $msg]);
    }

    public static function actionButton($query, $index = "default") {
        if ($index == 'default') {
            $buttons = [];
            if (self::$actions['preview'])
                $buttons[] = "<button class=\"btn btn-white goPreview\" type=\"button\"  data-id=\"{$query['id']}\" title=\"Detalhes da Documentação\"><i class=\"fa fa-eye\"></i> </button>";

            if (self::$actions['update'])
                $buttons[] = "<button class=\"btn btn-primary goUpdate\" type=\"button\" data-id=\"{$query['id']}\" title=\"Editar Documentação\"><i class=\"fas fa-pencil-alt\"></i> </button>";

            if (self::$actions['remove'])
                $buttons[] = "<button class=\"btn btn-danger goRem\" type=\"button\" data-id=\"{$query['id']}\" title=\"Excluir Documentação\" data-toggle=\"modal\" data-target=\"#myModalRem\"><i class=\"fa fa-trash\"></i> </button>";

            return implode("\n", $buttons);
        }
    }

    public static function checkBox($id) {
        return "<div class=\" check-default\">
  <input type=\"checkbox\" name=\"checkbox[]\" value=\"{$id}\" id=\"checkbox{$id}\">
  <label for=\"checkbox{$id}\"></label>
</div>";
    }
}
