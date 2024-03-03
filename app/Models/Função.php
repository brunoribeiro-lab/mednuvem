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
use App\Providers\Converter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use App\Providers\MenusCollection;

class Função extends Model {

    use HasFactory;

    protected $table = 'funcao';
    protected $fillable = [
        'deletado',
        'criado_por',
        'atualizado_as',
        'setor',
        'nome',
        'criado_em',
        'atualizado_em'
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
            2 => "funcao.id",
            3 => "setor.nome",
            4 => "funcao.nome",
            5 => "funcao.criado_em",
        ];
        $sSearch = trim($request->input('sSearch'));
        $query = self::join('setor', 'funcao.setor', '=', 'setor.id')
                ->where('funcao.deletado', 0);

        # se for root lista apenas do grupo
        if (!Session::get('is_root'))
            $query->where('setor.grupo', Auth::user()->group); // se não, mostra os bairros do usuário

        if (!empty($sSearch)) {
            $query->where(function ($q) use ($sSearch) {
                $q
                        ->orWhere('funcao.nome', 'like', "%$sSearch%")
                        ->orWhere('setor.nome', 'like', "%$sSearch%");
                // pesquisar pelo código
                if (preg_match('/FUN/i', $sSearch))
                    $q->orWhere(function ($query) use ($sSearch) {
                        $query->where('funcao.id', 'like', '%' . (int) Utils::extrairNum($sSearch) . '%');
                    });
            });
        }
        $query->select([
                    'funcao.*',
                    'setor.nome AS nomeSetor'
                ])
                ->orderBy($sortMap[intval($request->input('iSortCol_0', 0))], $request->input('sSortDir_0', 'asc'));
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
        self::$actions = AuthServiceProvider::acoes(...array_values(MenusCollection::$menus['setor']));
        $row = array();
        foreach ($clientes as $cliente) {
            $nice = Carbon::parse($cliente->criado_em)->diffForHumans();
            $updated = Utils::dataCompletaPTBR($cliente->criado_em);
            $dados = [
                '',
                self::checkBox($cliente->id),
                sprintf("FUN%s", str_pad($cliente->id, 6, '0', STR_PAD_LEFT)),
                strlen($cliente->nomeSetor) >= 50 ? sprintf("<abbr title='%s'>%s</abbr>", $cliente->nomeSetor, Str::limit($cliente->nomeSetor, 50)) : $cliente->nomeSetor,
                strlen($cliente->nome) >= 50 ? sprintf("<abbr title='%s'>%s</abbr>", $cliente->nome, Str::limit($cliente->nome, 50)) : $cliente->nome,
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

    public static function actionButton($query, $index = "default") {
        if ($index == 'default') {
            if (self::$actions['preview'])
                $buttons[] = "<button class=\"btn btn-white goPreview\" type=\"button\"  data-id=\"{$query['id']}\" title=\"Detalhes do Setor\"><i class=\"fa fa-eye\"></i> </button>";

            if (self::$actions['update'])
                $buttons[] = "<button class=\"btn btn-primary goUpdate\" type=\"button\" data-id=\"{$query['id']}\" title=\"Editar Setor\"><i class=\"fas fa-pencil-alt\"></i> </button>";

            if (self::$actions['remove'])
                $buttons[] = "<button class=\"btn btn-danger goRem\" type=\"button\" data-id=\"{$query['id']}\" title=\"Excluir Setor\" data-toggle=\"modal\" data-target=\"#myModalRem\"><i class=\"fa fa-trash\"></i> </button>";

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
        if (!AuthServiceProvider::acao($id ? 'ACCESS_UPDATE' : 'ACCESS_ADD', ...array_values(MenusCollection::$menus['setor'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        if (Session::get('is_root') && $id && !$campo = self::find($id)->where('deletado', 0))
            return response()->json(['error' => true, 'msg' => 'Registro não encontrado']);

        if (!Session::get('is_root') && $id && !$campo = self::find($id)->where('grupo', Auth::user()->group)->where('deletado', 0))
            return response()->json(['error' => true, 'msg' => 'Registro não encontrado']);

        $setor = '';
        // setor apenas do grupo do usuário
        if (!Session::get('is_root'))
            $setor = sprintf(",grupo,%s", Auth::user()->group);

        $rules = [
            'setor' => [
                'required',
                'integer',
                'min:0',
                "exists_in:setor,id,deletado,0{$setor}"
            ],
            'nome' => [
                'required',
                'string',
                'min:3',
                'max:200',
            ],
        ];

        $messages = [
            '*.*.required_with' => 'Campo obrigatório',
            '*.required' => 'Campo obrigatório',
            '*.min' => 'O campo deve ter no mínimo :min caracteres',
            '*.max' => 'O campo deve ter no máximo :max caracteres',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails())
            throw new ValidationException($validator);

        if ($id) { // atualizar no banco
            $formCampo = self::find($id);
            $formCampo->atualizado_por = Auth::user()->user_id;
            $formCampo->setor = $request->input('setor');
            $formCampo->nome = $request->input('nome');
            $formCampo->atualizado_em = now();
            $formCampo->save();
        } else { // cadastrar no banco
            $add = [
                'criado_por' => Auth::user()->user_id,
                'setor' => $request->input('setor'),
                'nome' => $request->input('nome'),
                "criado_em" => now()
            ];
            $formCampo = self::create($add);
        }

        if (!$formCampo)
            return response()->json(['error' => true, 'msg' => 'Não foi possível adicionar a Função']);

        if ($id) {
            $msg = 'Função foi salva com sucesso';
            Activity::novo("Edição da Função", "edit");
        } else {
            $msg = 'Função cadastrada com sucesso';
            Activity::novo("Cadastro da Função - {$request->input('nome')}");
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
        # se for root pode excluir qualquer uma
        if (Session::get('is_root'))
            self::whereIn('id', $ids)->update(['deletado' => 1]);

        # se não for root, tem uma segurança especial, só exclui as funções se for do mesmo grupo (clinica)
        if (!Session::get('is_root'))
            self::join('setor', 'funcao.setor', '=', 'setor.id')
                    ->whereIn('funcao.id', $ids)->where('setor.grupo', Auth::user()->group)->update(['funcao.deletado' => 1]);

        if (count($ids) > 1) {
            $msg = 'Funções excluidas com sucesso';
            Activity::novo(sprintf("Remoção de %d Funções", count($ids)), "trash-alt");
        } else {
            $msg = 'Função excluida com sucesso ';
            Activity::novo("Remoção de uma Função", "trash-alt");
        }
        return response()->json(['error' => false, 'msg' => $msg]);
    }

    public static function buscar($id) {
        $where = [
            'funcao.id' => $id,
            'funcao.deletado' => 0
        ];
        if (!Session::get('is_root'))
            $where['setor.grupo'] = Auth::user()->group;

        return self::join('users as creator', 'funcao.criado_por', '=', 'creator.user_id')
                        ->join('setor', 'funcao.setor', '=', 'setor.id')
                        ->join('users as source', 'setor.grupo', '=', 'source.user_id')
                        ->leftJoin('users as updater', 'funcao.atualizado_por', '=', 'updater.user_id')
                        ->where($where)
                        ->select(
                                'funcao.*',
                                'setor.nome AS setor_nome',
                                'creator.user_first_name as creator_first_name',
                                'creator.user_last_name as creator_last_name',
                                'updater.user_first_name as updater_first_name',
                                'updater.user_last_name as updater_last_name',
                        )
                        ->selectRaw('CONCAT(source.user_first_name, IFNULL(CONCAT(" ", source.user_last_name), "")) as source_fullname')
                        ->selectRaw('CONCAT(creator.user_first_name, IFNULL(CONCAT(" ", creator.user_last_name), "")) as creator_fullname')
                        ->selectRaw('CONCAT(updater.user_first_name, IFNULL(CONCAT(" ", updater.user_last_name), "")) as updater_fullname')
                        ->first();
    }

    public static function buscarSetor($idFuncao, $setor) {
        $where = [
            'funcao.id' => $idFuncao,
            'funcao.setor' => $setor,
            'funcao.deletado' => 0
        ];
        if (!Session::get('is_root'))
            $where['setor.grupo'] = Auth::user()->group;

        return self::join('users as creator', 'funcao.criado_por', '=', 'creator.user_id')
                        ->join('setor', 'funcao.setor', '=', 'setor.id')
                        ->join('users as source', 'setor.grupo', '=', 'source.user_id')
                        ->leftJoin('users as updater', 'funcao.atualizado_por', '=', 'updater.user_id')
                        ->where($where)
                        ->select(
                                'funcao.*',
                                'setor.nome AS setor_nome',
                                'creator.user_first_name as creator_first_name',
                                'creator.user_last_name as creator_last_name',
                                'updater.user_first_name as updater_first_name',
                                'updater.user_last_name as updater_last_name',
                        )
                        ->selectRaw('CONCAT(source.user_first_name, IFNULL(CONCAT(" ", source.user_last_name), "")) as source_fullname')
                        ->selectRaw('CONCAT(creator.user_first_name, IFNULL(CONCAT(" ", creator.user_last_name), "")) as creator_fullname')
                        ->selectRaw('CONCAT(updater.user_first_name, IFNULL(CONCAT(" ", updater.user_last_name), "")) as updater_fullname')
                        ->first();
    }

    /**
     * Listar funções por setor em ordem alfabetica
     * 
     * @static
     * @access public
     * @return Eloquent
     */
    public static function listarPorSetor($setor) {
        $where = [
            'funcao.setor' => $setor,
            'funcao.deletado' => 0
        ];
        if (!Session::get('is_root'))
            $where['setor.grupo'] = Auth::user()->group;

        return self::join('users as creator', 'funcao.criado_por', '=', 'creator.user_id')
                        ->join('setor', 'funcao.setor', '=', 'setor.id')
                        ->join('users as source', 'setor.grupo', '=', 'source.user_id')
                        ->leftJoin('users as updater', 'funcao.atualizado_por', '=', 'updater.user_id')
                        ->where($where)
                        ->select(
                                'funcao.*',
                                'setor.nome AS setor_nome',
                                'creator.user_first_name as creator_first_name',
                                'creator.user_last_name as creator_last_name',
                                'updater.user_first_name as updater_first_name',
                                'updater.user_last_name as updater_last_name',
                        )
                        ->selectRaw('CONCAT(source.user_first_name, IFNULL(CONCAT(" ", source.user_last_name), "")) as source_fullname')
                        ->selectRaw('CONCAT(creator.user_first_name, IFNULL(CONCAT(" ", creator.user_last_name), "")) as creator_fullname')
                        ->selectRaw('CONCAT(updater.user_first_name, IFNULL(CONCAT(" ", updater.user_last_name), "")) as updater_fullname')
                        ->get();
    }
}
