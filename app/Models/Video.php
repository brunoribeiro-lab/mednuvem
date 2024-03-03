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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Providers\MenusCollection;

class Video extends Model {

    use HasFactory;

    protected $table = 'videos';
    public $timestamps = false;
    protected $fillable = [
        'deleted', 'created_by', 'updated_by', 'title', 'description', 'doc_text', 'keywords', 'youtube', 'thumbmail', 'created_at', 'updated_at'
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
            2 => "thumbmail",
            3 => "title",
            4 => "description",
            5 => "updated_at"
        ];
        $sSearch = trim($request->input('sSearch'));
        $query = Video::where('deleted', 0);

        if (!empty($sSearch)) {
            $query->where(function ($q) use ($sSearch) {
                $q->orWhere('title', 'like', "%$sSearch%")
                        ->orWhere('description', 'like', "%$sSearch%")
                        ->orWhere('keywords', 'like', "%$sSearch%")
                        ->orWhere('youtube', 'like', "%$sSearch%");
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
        $videos = self::sql_listar($request);
        $total = count(self::sql_listar($request, true));
        self::$actions = AuthServiceProvider::acoes(...array_values(MenusCollection::$menus['videos-aulas']));
        $row = array();
        foreach ($videos as $video) {
            $nice = Carbon::parse($video->updated_at)->diffForHumans();
            $updated = Utils::dataCompletaPTBR($video->updated_at);
            $thumb = sprintf("storage/videos/%s", $video["thumbmail"]);
            $dados = [
                '',
                self::checkBox($video->id),
                "<a href='javascript:;' title='Clique aqui para ampliar essa imagem' data-bs-toggle='modal' data-bs-target='.bs-pic-modal-xl' data-path='$thumb'><img src='{$thumb}' width='70px' height='50px'></a>",
                strlen($video->title) >= 50 ? sprintf("<abbr title='%s'>%s</abbr>", $video->title, Str::limit($video->title, 50)) : $video->title,
                strlen($video->description) >= 50 ? sprintf("<abbr title='%s'>%s</abbr>", $video->description, Str::limit($video->description, 50)) : $video->description,
                "<abbr title='em {$updated}'>{$nice}</abbr>",
                self::actionButton($video)
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
        if (!AuthServiceProvider::acao($id ? 'ACCESS_UPDATE' : 'ACCESS_ADD', ...array_values(MenusCollection::$menus['videos-aulas'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        if ($id && !Video::find($id))
            return response()->json(['error' => true, 'msg' => 'Registro não encontrado']);

        $rules = [
            'title' => 'required|string|min:3|max:255',
            'youtube' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/youtube\.com|youtu\.be/', $value)) {
                        $fail("O campo <strong>Link do Vídeo</strong> é inválido !");
                    }
                },
                'unique:videos,youtube,' . ($id ? $id : '') // Checa se o link já existe na tabela de vídeos
            ],
            'keyword' => 'required|string|min:3|max:250',
            'text' => 'required|string|min:10|max:10000',
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

        // Extrai o video ID
        $videoID = Utils::extractID($request->input('youtube'));

        if (!$videoID)
            return response()->json(['error' => true, 'msg' => 'Não foi possível pegar o ID do <strong>Link do Vídeo</strong> !']);

        // salvar thumbnail
        $linkExtract = "http://i3.ytimg.com/vi/{$videoID}/0.jpg";
        $file = md5(uniqid()) . ".jpg";
        // Armazene na pasta de armazenamento e obtenha o caminho público
        $thumbmailContent = file_get_contents($linkExtract);
        Storage::disk('public')->put("videos/{$file}", $thumbmailContent);

        if (!Storage::disk('public')->exists("videos/{$file}")) {
            return response()->json(['error' => true, 'msg' => 'Não foi possível salvar a foto de capa do vídeo']);
        }
        // Realizando a inserção
        if ($id) {
            $doc = Video::find($id);
            $doc->title = $request->input('title');
            $doc->description = $request->input('text');
            $doc->keywords = $request->input('keyword');
            $doc->youtube = $request->input('youtube');
            $doc->thumbmail = $file;
            $doc->updated_by = Auth::user()->user_id;

            if ($request->input('pagina'))
                $doc->link = $request->input('pagina');

            $doc->save();
        } else {
            $add = [
                'title' => $request->input('title'),
                'description' => $request->input('text'),
                'keywords' => $request->input('keyword'),
                'youtube' => $request->input('youtube'),
                'thumbmail' => $file,
                "created_at" => now(),
                'deleted' => 0,
                'created_by' => Auth::user()->user_id
            ];
            if ($request->input('pagina'))
                $add['link'] = $request->input('pagina');

            $doc = Video::create($add);
        }

        if (!$doc)
            return response()->json(['error' => true, 'msg' => 'Não foi possível adicionar um novo Vídeo']);

        if ($id) {
            $msg = 'Vídeo foi salvo com sucesso';
            Activity::novo("Edição de Vídeo - {$request->input('index')}", "edit");
        } else {
            $msg = 'Vídeo cadastrado com sucesso';
            Activity::novo("Cadastro de Vídeo - {$request->input('index')}");
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
        Video::whereIn('id', $ids)->update(['deleted' => 1]);
        if (count($ids) > 1) {
            $msg = 'Vídeos Aulas excluidas com sucesso';
            Activity::novo(sprintf("Remoção de %d Vídeos Aulas", count($ids)), "trash-alt");
        } else {
            $msg = 'Vídeo Aula excluida com sucesso';
            Activity::novo("Remoção de um Vídeo Aula", "trash-alt");
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
