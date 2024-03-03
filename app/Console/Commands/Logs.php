<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Providers\Mail;
use App\Models\SystemCron;
use App\Models\Logs AS LogsE;
use App\Models\Activity;

class Logs extends Command {

    private $limite = [
        'logs' => 500,
        'atividade' => 50
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Apagar Logs de Erros e Atividades Recentes';

    /**
     * Execute the console command.
     */
    public function handle() {
        $this->removerLogs();
        $this->removerAtividades();
        SystemCron::lastExecution('Logs');
    }

    public function removerLogs() {
        $logs = LogsE::get();
        $total = $logs->count();
        if ($total >= $this->limite['logs'])
            LogsE::orderBy('id', 'ASC')->limit($total - $this->limite['logs'])->delete();
    }

    public function removerAtividades() {
        $atividades = Activity::join('users', function ($join) {
                    $join->on('_RECENT_ACTIVITY.USER', '=', 'users.user_id')
                    ->where('users.deleted', '=', 0)
                    ->where('users.user_active', '=', 1);
                })
                ->selectRaw('users.user_id, count(*) as atividades_por_usuario')
                ->groupBy("users.user_id")
                ->get();
        foreach ($atividades as $atividade)
            if ($atividade['atividades_por_usuario'] >= $this->limite['atividade'])
                Activity::orderBy('ID', 'ASC')->limit($atividade['atividades_por_usuario'] - $this->limite['atividade'])->delete();
    }
}
