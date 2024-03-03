<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Providers\AuthServiceProvider;
use Illuminate\Support\Carbon;
use App\Models\Activity;
use App\Providers\Utils;
use Illuminate\Support\Str;
use App\Providers\MenusCollection;

class Logs extends Model {

    use HasFactory;

    private static $actions = []; // ações do CRUD
    protected $table = "logs";

    /**
     * Criar um padrão de listagem para o plugin Jquery Datatable
     * 
     * @static
     * @access public
     * @param Object $request
     * @return json
     */
    public static function datatable($request) {
        $logs = self::sql_listar($request);
        $total = count(self::sql_listar($request, true));
        self::$actions = AuthServiceProvider::acoes(...array_values(MenusCollection::$menus['logs']));

        $row = array();
        foreach ($logs as $log) {
            $nice = Carbon::parse($log->created_at)->diffForHumans();
            $updated = Carbon::parse($log->created_at)->format('d de F, Y à\s H:i:s');
            $row[] = [
                '',
                self::checkBox($log->id),
                sprintf("LOG%s", str_pad($log->id, 6, "0", STR_PAD_LEFT)),
                $log->channel,
                $log->level,
                strlen($log->message) >= 50 ? sprintf("<abbr title='%s'>%s</abbr>", htmlentities($log->message), Str::limit($log->message, 50)) : $log->message,
                strlen($log->context) >= 50 ? sprintf("<abbr title='%s'>%s</abbr>", $log->context, Str::limit($log->context, 50)) : $log->context,
                "<abbr title='em {$updated}'>{$nice}</abbr>",
                self::actionButton($log)
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
            3 => "channel",
            4 => "level",
            5 => "message",
            6 => "context",
            7 => "created_at"
        ];
        $sSearch = trim($request->input('sSearch'));
        $searchNumber = preg_replace('/\D/', '', $sSearch);

        $query = Logs::query();

        if (!$todos)
            $query->limit($request->input('iDisplayLength'));

        if (!empty($sSearch)) {
            $query->where(function ($q) use ($sSearch, $searchNumber) {
                $q->orWhere('message', 'like', "%$sSearch%")
                        ->orWhere('context', 'like', "%$sSearch%")
                        ->orWhere('level_name', 'like', "%$sSearch%")
                        ->orWhere('channel', 'like', "%$sSearch%");

                if (strlen($searchNumber) >= 2) {
                    $q->orWhere("level", 'like', "%$searchNumber%");
                }
            });
        }
        $query->orderBy($sortMap[intval($request->input('iSortCol_0', 0))], $request->input('sSortDir_0', 'asc'));
        if (!$todos)
            $query->offset($request->input('iDisplayStart'));

        return $query->get();
    }

    public static function actionButton($query) {
        $buttons = [];
        if (self::$actions['preview'])
            $buttons[] = "<button class=\"btn btn-white goPreview\" type=\"button\"  data-id=\"{$query['id']}\" title=\"Detalhes do Log\"><i class=\"fa fa-eye\"></i> </button>";

        if (self::$actions['remove'])
            $buttons[] = "<button class=\"btn btn-danger goRem\" type=\"button\" data-id=\"{$query['id']}\" title=\"Excluir Log\"><i class=\"fa fa-trash\"></i> </button>";

        return implode("\n", $buttons);
    }

    /**
     * Remover log(s)
     * 
     * @param array $ids Ids do Log(s)
     * @return json
     */
    public static function remover(array $ids) {
        Logs::whereIn('id', $ids)->delete();
        if (count($ids) > 1) {
            $msg = 'Logs excluidos com sucesso';
            Activity::novo(sprintf("Remoção de %d Logs", count($ids)), "trash-alt");
        } else {
            $msg = 'Log excluido com sucesso';
            Activity::novo("Remoção de um Log", "trash-alt");
        }
        return response()->json(['error' => false, 'msg' => $msg]);
    }

    public static function checkBox($id) {
        return "<div class=\" check-default\">
  <input type=\"checkbox\" name=\"checkbox[]\" value=\"{$id}\" id=\"checkbox{$id}\">
  <label for=\"checkbox{$id}\"></label>
</div>";
    }
}
