<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Providers\Utils;
use Illuminate\Support\Str;

class SystemCron extends Model {

    protected $table = '_SYSTEM_CRON';
    public $timestamps = false;

    use HasFactory;

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
            1 => "TASK",
            3 => "COMMAMD",
            4 => "LAST_EXECUTION"
        ];
        $sSearch = trim($request->input('sSearch'));
        $query = SystemCron::where('ENABLE', 1);

        if (!empty($sSearch)) {
            $query->where(function ($q) use ($sSearch) {
                $q->orWhere('TASK', 'like', "%$sSearch%")
                        ->orWhere('DESCRIPTION', 'like', "%$sSearch%")
                        ->orWhere('COMMAMD', 'like', "%$sSearch%");
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
        $tarefas = self::sql_listar($request);
        $total = count(self::sql_listar($request, true));
        $row = array();
        foreach ($tarefas as $tarefa) {
            $dados = [
                '',
                $tarefa->TASK,
                self::checkHealth($tarefa->INTER, $tarefa->LAST_EXECUTION),
                strlen($tarefa->COMMAMD) >= 50 ? sprintf("<abbr title='%s'>%s</abbr>", $tarefa->COMMAMD, Str::limit($tarefa->COMMAMD, 50)) : $tarefa->COMMAMD,
                !$tarefa->LAST_EXECUTION ? "Nunca" : sprintf("<abbr title='em %s'> %s</abbr>", date("d-m-Y H:i:s", strtotime($tarefa->LAST_EXECUTION)), Carbon::parse($tarefa->LAST_EXECUTION)->diffForHumans()),
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
     * Check health of task
     * 
     * @static
     * @access public
     * @param string $interval Interval time
     * @param datetime $lastExecution Last execution timestamp
     * @return string
     */
    private static function checkHealth($interval, $lastExecution) {
        $problem = "<label class=\"badge bg-danger\" title=\"Problema encontrado\"><i class=\"far fa-frown fa-2x\"></i></label>";
        $noProblem = "<label class=\"badge bg-success\" title=\"Sem Problema\"><i class=\"fas fa-smile fa-2x\"></i></label>";
        if (empty($lastExecution))
            return $problem;

        return self::checkTimeInterval($interval, $lastExecution, $noProblem, $problem);
    }

    private static function checkTimeInterval($interval, $lastExecution, $success, $error) {
        if (strtotime("+{$interval}", strtotime($lastExecution)) <= time())
            return $error;

        return $success;
    }

    /**
     * Update last execution time of task index
     * 
     * @static
     * @access public
     * @param ENUM $taskIndex Task Index
     * @return void
     */
    public static function lastExecution($taskIndex, $checkEnable = true) {
        date_default_timezone_set(config('app.timezone')); // Usando a configuração de fuso horário do Laravel.
        // Verifica se a tarefa está ativada
        if ($checkEnable && !(bool) SystemCron::where('TASK', $taskIndex)->value('ENABLE')) {
            die("Not actived");
        }

        // Atualiza a última execução da tarefa
        SystemCron::where('TASK', $taskIndex)->limit(1)->update(['LAST_EXECUTION' => Carbon::now()]);
    }
}