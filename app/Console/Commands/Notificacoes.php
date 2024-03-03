<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DynamicEmail;
use App\Providers\Mail;
use App\Models\VariavelDoSistema;
use App\Models\SystemCron;
use App\Models\Disparos;

class Notificacoes extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tarefa para enviar emails';

    /**
     * Execute the console command.
     */
    public function handle() {
        $variaveis = VariavelDoSistema::find(1);

        SystemCron::lastExecution('Email');
        $pendentes = $this->pendentes();
        if (!$pendentes)
            die("Nenhum pendente");

        foreach ($pendentes as $pendente) {
            $mail = new Mail($variaveis);
            $m = $mail->sendMailWithPHPMailer($pendente->send_to, $variaveis->nome, $pendente->subject, $pendente->email, 0);
            if (!$m) {
                $pendente->error = "Falha ao autenticar email\n {$mail->getError()}";
                $pendente->save();
                continue;
            }

            $pendente->sended = 1;
            $pendente->error = NULL;
            $pendente->save();
        }
    }

    private function pendentes() {
        return Disparos::where("deletado", 0)
                        ->where('sended', 0)
                        ->orderBy("id", "ASC")
                        ->get();
    }
}
