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
use Illuminate\Support\Facades\Auth;
use App\Providers\MenusCollection;

class DynamicEmail extends Model {

    use HasFactory;

    protected $table = "dynamic_emails";
    private static $actions = []; // ações do CRUD
    protected $fillable = [
        'deleted',
        'created_by',
        'updated_by',
        'index',
        'subject',
        'message'
    ];

    /**
     * Cria um padrão de listagem para o plugin Jquery Datatable
     * 
     * @static
     * @access public
     * @param Object $request
     * @return json
     */
    public static function datatable(Request $request) {
        $exames = self::sql_listar($request);
        $total = count(self::sql_listar($request, true));
        self::$actions = AuthServiceProvider::acoes(...array_values(MenusCollection::$menus['emails']));

        $row = array();
        foreach ($exames as $exame) {
            $nice = Carbon::parse($exame->created_at)->diffForHumans();
            $updated = Utils::dataCompletaPTBR($exame->created_at);
            $dados = [
                '',
                self::checkBox($exame->id),
                strlen($exame->index) >= 50 ? sprintf("<abbr title='%s'>%s</abbr>", $exame->index, Str::limit($exame->index, 50)) : $exame->index,
                strlen($exame->subject) >= 50 ? sprintf("<abbr title='%s'>%s</abbr>", $exame->subject, Str::limit($exame->subject, 50)) : $exame->subject,
                "<abbr title='em {$updated}'>{$nice}</abbr>",
                self::actionButton($exame)
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
    private static function sql_listar($request, $todos = false) {

        $sortMap = [
            2 => "index",
            3 => "subject",
            4 => "created_at"
        ];

        $sSearch = trim($request->input('sSearch'));

        $query = DynamicEmail::where('deleted', 0);

        if (!empty($sSearch)) {
            $query->where(function ($q) use ($sSearch, $cnae) {
                $q->orWhere('index', 'like', "%$sSearch%")
                        ->orWhere('message', 'like', "%$sSearch%")
                        ->orWhere('subject', 'like', "%$sSearch%");
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
                $buttons[] = "<button class=\"btn btn-white goPreview\" type=\"button\"  data-id=\"{$query['id']}\" title=\"Detalhes do Exame e Valor\"><i class=\"fa fa-eye\"></i> </button>";

            if (self::$actions['update'])
                $buttons[] = "<button class=\"btn btn-primary goUpdate\" type=\"button\" data-id=\"{$query['id']}\" title=\"Editar Exame e Valor\"><i class=\"fas fa-pencil-alt\"></i> </button>";

            if (self::$actions['remove'])
                $buttons[] = "<button class=\"btn btn-danger goRem\" type=\"button\" data-id=\"{$query['id']}\" title=\"Excluir Exame e Valor\" data-toggle=\"modal\" data-target=\"#myModalRem\"><i class=\"fa fa-trash\"></i> </button>";

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
     * Remover Email Dinâmico
     * 
     * @param array $ids Ids Dinâmicos
     * @return json
     */
    public static function remover($ids) {
        DynamicEmail::whereIn('id', $ids)->update(['deleted' => 1]);
        if (count($ids) > 1) {
            $msg = 'Emails Dinâmicos excluidos com sucesso';
            Activity::novo(sprintf("Remoção de %d Emails Dinâmicos", count($ids)), "trash-alt");
        } else {
            $msg = 'Email Dinâmico excluido com sucesso';
            Activity::novo("Remoção de um Email Dinâmico", "trash-alt");
        }
        return response()->json(['error' => false, 'msg' => $msg]);
    }

    public function salvar(Request $request, $id) {

        if (!AuthServiceProvider::acao($id ? 'ACCESS_UPDATE' : 'ACCESS_ADD', ...array_values(MenusCollection::$menus['emails'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        if ($id && !$campo = DynamicEmail::find($id))
            return response()->json(['error' => true, 'msg' => 'Registro não encontrado']);

        $rules = [
            'index' => 'required|string|min:3|max:200',
            'subject' => 'required|string|min:5|max:200',
            'email' => 'required|string|min:10|max:10000',
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
            $formCampo = DynamicEmail::find($id);
            $formCampo->updated_by = Auth::user()->user_id;
            $formCampo->index = $request->input('index');
            $formCampo->subject = $request->input('subject');
            $formCampo->message = $request->input('email');
            $formCampo->save();
        } else {
            $add = [
                'deleted' => 0,
                'created_by' => Auth::user()->user_id,
                'index' => $request->input('index'),
                'subject' => $request->input('subject'),
                'message' => $request->input('email'),
                    #   "cadastrado" => now()
            ];
            $formCampo = DynamicEmail::create($add);
        }

        if (!$formCampo)
            return response()->json(['error' => true, 'msg' => 'Não foi possível adicionar um novo Email Dinâmico']);

        if ($id) {
            $msg = 'Email Dinâmico foi salvo com sucesso';
            Activity::novo("Edição de Email Dinâmico - {$request->input('index')}", "edit");
        } else {
            $msg = 'Email Dinâmico cadastrado com sucesso';
            Activity::novo("Cadastro de Email Dinâmico - {$request->input('index')}");
        }
        return response()->json(['error' => false, 'msg' => $msg]);
    }

}
