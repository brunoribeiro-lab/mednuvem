<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Providers\AuthServiceProvider;
use App\Providers\Utils;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\Activity;
use App\Providers\MenusCollection;

class Disparos extends Model {

    use HasFactory;

    protected $table = 'notifier_list';
    private static $actions = []; // ações do CRUD
    public $timestamps = false;

    /**
     * Cria um padrão de listagem para o plugin Jquery Datatable
     * 
     * @static
     * @access public
     * @param Object $request
     * @return json
     */
    public static function datatable(Request $request) {
        $disparos = self::sql_listar($request);
        $total = count(self::sql_listar($request, true));
        self::$actions = AuthServiceProvider::acoes(...array_values(MenusCollection::$menus['disparos']));

        $row = array();
        foreach ($disparos as $disparo) {
            $nice = Carbon::parse($disparo->created_at)->diffForHumans();
            $updated = Utils::dataCompletaPTBR($disparo->created_at);
            $dados = [
                '',
                self::checkBox($disparo->id),
                strlen($disparo->ref) >= 50 ? sprintf("<abbr title='%s'>%s</abbr>", $disparo->ref, Str::limit($disparo->ref, 50)) : $disparo->ref,
                strlen($disparo->send_to) >= 50 ? sprintf("<abbr title='%s'>%s</abbr>", $disparo->send_to, Str::limit($disparo->send_to, 50)) : $disparo->send_to,
                strlen($disparo->subject) >= 50 ? sprintf("<abbr title='%s'>%s</abbr>", $disparo->subject, Str::limit($disparo->subject, 50)) : $disparo->subject,
                $disparo->sended ? "<label class='badge rounded-pill bg-success'>Enviado</label>" : "<label class='badge rounded-pill bg-danger'>Pendente</label>",
                "<abbr title='em {$updated}'>{$nice}</abbr>",
                self::actionButton($disparo)
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
            2 => "ref",
            3 => "send_to",
            4 => "subject",
            5 => "sended",
            6 => "created_at"
        ];

        $sSearch = trim($request->input('sSearch'));

        $query = Disparos::where('deletado', 0);

        if (!empty($sSearch)) {
            $query->where(function ($q) use ($sSearch, $cnae) {
                $q->orWhere('ref', 'like', "%$sSearch%")
                        ->orWhere('send_to', 'like', "%$sSearch%")
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

    /**
     * Remover Disparo de Email
     * 
     * @param array $ids Ids de disparos
     * @return json
     */
    public static function remover($ids) {
        Disparos::whereIn('id', $ids)->where('sended', 0)->update(['deletado' => 1]);
        if (count($ids) > 1) {
            $msg = 'Disparos de Email excluidos com sucesso';
            Activity::novo(sprintf("Remoção de %d Disparos de Email", count($ids)), "trash-alt");
        } else {
            $msg = 'Disparo de Email excluido com sucesso';
            Activity::novo("Remoção de um Disparo de Email", "trash-alt");
        }
        return response()->json(['error' => false, 'msg' => $msg]);
    }

    /**
     * Reenviar Disparo de Email
     * 
     * @param array $ids Ids de disparos
     * @return json
     */
    public static function reenviar($ids) {
        $disparos = Disparos::where('deletado',0)->whereIn('id', $ids)->where('sended', 1)->get();

        if ($disparos->isEmpty()) {
            return response()->json(['error' => true, 'msg' => "Nenhum email enviado foi selecionado"]);
        }

        Disparos::whereIn('id', $ids)->where('sended', 1)->update(['sended' => 0]);
        if (count($ids) > 1) {
            $msg = 'Emails reenviados com sucesso';
            Activity::novo(sprintf("Reenviamentos de %d Emails", count($ids)), "trash-alt");
        } else {
            $msg = 'Email reenviado com sucesso';
            Activity::novo("Reenviamento de um Email", "trash-alt");
        }
        return response()->json(['error' => false, 'msg' => $msg]);
    }

    public static function actionButton($query, $index = "default") {
        if ($index == 'default') {
            $buttons = [];
            if (self::$actions['preview'])
                $buttons[] = "<button class=\"btn btn-white goPreview\" type=\"button\"  data-id=\"{$query['id']}\" title=\"Detalhes do Email\"><i class=\"fa fa-eye\"></i> </button>";

            if (self::$actions['resend'] && $query->sended)
                $buttons[] = "<button class=\"btn btn-primary goUpdate\" type=\"button\" data-id=\"{$query['id']}\" title=\"Reenviar Email\"><i class=\"fas fa-redo\"></i> </button>";

            if (self::$actions['remove'] && !$query->sended)
                $buttons[] = "<button class=\"btn btn-danger goRem\" type=\"button\" data-id=\"{$query['id']}\" title=\"Excluir Email\" data-toggle=\"modal\" data-target=\"#myModalRem\"><i class=\"fa fa-trash\"></i> </button>";

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
