<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Providers\Utils;
use App\Providers\Captchar;
use App\Providers\AuthServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\Activity;
use App\Providers\MenusCollection;

class FormContato extends Model {

    use HasFactory;

    protected $fillable = [
        'deletado',
        'lida',
        'IP',
        'navegador',
        'plataforma',
        'nome',
        'telefone',
        'email',
        'mensagem',
        'cadastrado'
    ];
    protected $table = 'form_contact';
    public $timestamps = false;
    private static $actions = []; // ações do CRUD

    /**
     * Cria um padrão de listagem para o plugin Jquery Datatable
     * 
     * @static
     * @access public
     * @param Object $request
     * @return json
     */
    public static function datatable(Request $request) {
        $forms = self::sql_listar($request);
        $total = count(self::sql_listar($request, true));
        self::$actions = AuthServiceProvider::acoes(...array_values(MenusCollection::$menus['mensagens']));
        $row = array();
        foreach ($forms as $form) {
            $nice = Carbon::parse($form->cadastrado)->diffForHumans();
            $updated = Utils::dataCompletaPTBR($form->cadastrado);
            $dados = [
                '',
                self::checkBox($form->id),
                sprintf("MEN%s", str_pad($form->id, 6, "0", STR_PAD_LEFT)),
                strlen($form->nome) >= 50 ? sprintf("<abbr title='%s'>%s</abbr>", $form->nome, Str::limit($form->nome, 50)) : $form->nome,
                Utils::mask($form->telefone, Utils::$MASK_PHONE),
                strlen($form->email) >= 50 ? sprintf("<abbr title='%s'>%s</abbr>", $form->email, Str::limit($form->email, 50)) : $form->email,
                strlen($form->mensagem) >= 50 ? sprintf("<abbr title='%s'>%s</abbr>", $form->mensagem, Str::limit($form->mensagem, 50)) : $form->mensagem,
                "<abbr title='em {$updated}'>{$nice}</abbr>",
                self::actionButton($form)
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
            2 => "id",
            3 => "nome",
            4 => "telefone",
            5 => "email",
            6 => 'mensagem',
            7 => 'cadastrado'
        ];
        $sSearch = trim($request->input('sSearch'));
        $query = self::where('deletado', 0)->where('lida', 0);
        if (!empty($sSearch)) {
            $query->where(function ($q) use ($sSearch) {
                $q->orWhere('IP', 'like', "%$sSearch%")
                        ->orWhere('navegador', 'like', "%$sSearch%")
                        ->orWhere('nome', 'like', "%$sSearch%")
                        ->orWhere('email', 'like', "%$sSearch%")
                        ->orWhere('plataforma', 'like', "%$sSearch%");
                
                // algum número de telefone
                if (strlen($num = Utils::extrairNum(trim($sSearch))) >= 3) {
                    $q->orWhere(function ($q) use ($sSearch, $num) {
                        $q->where('telefone', 'like', "%$num%");
                    });
                }
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
     * Enviar mensagem do formulário do site
     * 
     * @access public
     * @param Request $request
     * @return json
     * @throws ValidationException
     */
    public function enviar(Request $request) {
        Captchar::reCaptchar();
        $rules = [
            'clinica' => 'required|string|min:3|max:200',
            'telefone' => 'required|string|min:16|max:16|telefone',
            'email' => 'required|string|min:4|max:1000',
            'mensagem' => 'required|string|min:5|max:10000',
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
        // Se passar na validação, continue
        $browser = Utils::get_browser();
        $platform = $navegador = "Desconhecido";
        if (!empty($browser->name) && !empty($browser->version))
            $navegador = "{$browser->name} {$browser->version}";

        if (!empty($browser->platform) && !empty($browser->device_type))
            $platform = "{$browser->platform} - {$browser->device_type}";


        $formContact = self::create([
                    "IP" => request()->ip(),
                    "navegador" => $navegador,
                    "plataforma" => $platform,
                    "nome" => $request->clinica,
                    "telefone" => preg_replace('/\D/', '', $request->telefone),
                    "email" => $request->email,
                    "mensagem" => $request->mensagem,
                    "cadastrado" => now()
        ]);
        return response()->json(['error' => false, 'msg' => 'Formulário enviado com sucesso! <br> Vamos responde-lo o mais rápido possível.']);
    }

    /**
     * Remover Mensagens de Solicitação de Contato
     * 
     * @param array $ids Ids das mensagens
     * @return json
     */
    public static function remover($ids) {
        self::whereIn('id', $ids)->update(['deletado' => 1]);

        if (count($ids) > 1) {
            Activity::novo(sprintf("Remoção de %d Mensagens", count($ids)), "trash-alt");
        } else {
            Activity::novo("Remoção de uma Mensagem", "trash-alt");
        }
        $msg = count($ids) > 1 ? "Mensagens Excluidas com sucesso !" : "Mensagem Excluida com sucesso !";
        return response()->json(['error' => false, 'msg' => $msg]);
    }

    private static function checkBox($id) {
        return "<div class=\" check-default\">
  <input type=\"checkbox\" name=\"checkbox[]\" value=\"{$id}\" id=\"checkbox{$id}\">
  <label for=\"checkbox{$id}\"></label>
</div>";
    }

    private static function actionButton($query, $index = "default") {
        if ($index == 'default') {
            $buttons = [];
            if (self::$actions['preview'])
                $buttons[] = "<button class=\"btn btn-white goPreview\" type=\"button\"  data-id=\"{$query['id']}\" title=\"Detalhes da Solicitação de Contato\"><i class=\"fa fa-eye\"></i> </button>";

            if (self::$actions['remove'])
                $buttons[] = "<button class=\"btn btn-danger goRem\" type=\"button\" data-id=\"{$query['id']}\" title=\"Remover Solicitação de Contato\"><i class=\"fa fa-trash\"></i> </button>";


            return implode("\n", $buttons);
        }
    }
}
