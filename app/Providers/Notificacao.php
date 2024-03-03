<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\VariavelDoSistema;
use App\Models\DynamicEmail;
use App\Providers\Mail;
use App\Providers\Utils;

class Notificacao extends ServiceProvider {

    public static $user_password = NULL;

    /**
     * String prefix to replace it
     * 
     * @access public
     * @var array
     */
    public static function prefix() {
        $systemVariables = VariavelDoSistema::find(1)->first();
        return [
            ':logo:' => [
                'title' => "Link da Logomarca",
                'replace' => asset("assets/uploads/theme/{$systemVariables->logo_email}")
            ],
            ':logoResponsivo:' => [
                'title' => "Link da Logomarca Responsivo",
                'replace' => asset("assets/uploads/theme/{$systemVariables->logo_email}")
            ],
            ':link:' => [
                'title' => "Link do Site",
                'replace' => Config('app.url')
            ],
            ':nomeDoSite:' => [
                'title' => "Nome do Site",
                'replace' => $systemVariables->nome
            ],
            ':codigoDeAtivacao:' => [
                'title' => "URL com Código de Ativação de Conta",
                'replace' => FALSE,
                'index' => 'activeToken'
            ],
            ':codigoDeRecuperacao:' => [
                'title' => "URL com Código de Recuperação de Conta",
                'replace' => FALSE,
                'index' => 'recoveryToken'
            ],
            ':nomeDoUsuario:' => [
                'title' => "Nome do Usuário",
                'replace' => FALSE,
                'index' => 'user_full_name'
            ],
            ':emailDoUsuario:' => [
                'title' => "Email do Usuário",
                'replace' => FALSE,
                'index' => 'user_email'
            ],
            # GerenciaNET notifications
            ':methodoDePagamento:' => [
                'title' => "Método de Pagamento da Cobrança Ex: PIX ou Cartão de Crédito",
                'replace' => FALSE,
                'index' => 'payment_method'
            ],
            ':valorDaConta:' => [
                'title' => "Valor da Conta",
                'replace' => FALSE,
                'index' => 'charge_total'
            ],
            ':QRCodeDoPedido:' => [
                'title' => "QR Code do código do Atendimento",
                'replace' => FALSE,
                'index' => 'qrcode_code'
            ],
            ':numeroDoPedido:' => [
                'title' => "Código do Atendimento",
                'replace' => FALSE,
                'index' => 'codigo_atendimento'
            ],
            ':codigoRecuperarSenha:' => [
                'title' => "Código Gerado Para Recuperar a senha (Gatilho Recuperar Senha)",
                'replace' => FALSE,
                'index' => 'recovery_token'
            ],
            ':IPUsado:' => [
                'title' => "IP capturado na sessão",
                'replace' => FALSE,
                'index' => 'ip'
            ],
            ':navegadorUsado:' => [
                'title' => "Navegador usado na sessão",
                'replace' => FALSE,
                'index' => 'browser'
            ],
            ':plataformaUsada:' => [
                'title' => "Plataforma usada na sessão",
                'replace' => FALSE,
                'index' => 'platform'
            ],
            ':dispositivoUsado:' => [
                'title' => "Dispositivo usado na sessão",
                'replace' => FALSE,
                'index' => 'device'
            ],
            ':LoginDoCadastro:' => [
                'title' => "Login do Cadastro",
                'replace' => FALSE,
                'index' => 'user_name'
            ],
            ':senhaDoCadastro:' => [
                'title' => "Senha do Cadastro",
                'replace' => FALSE,
                'index' => 'user_password'
            ],
        ];
    }

    /**
     * Register services.
     */
    public function register(): void {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {
        //
    }

    public static function formRecuperarSenha($user) {
        $systemVariables = VariavelDoSistema::first();
        if (empty($systemVariables->email_recuperar_senha)) {
            return false;
        }

        $dynamicEmail = DynamicEmail::where('id', $systemVariables->email_recuperar_senha)->where('deleted', 0)->first();
        if (!$dynamicEmail) {
            return false;
        }
        $mail = new Mail($systemVariables);
        $subject = self::replaceMessage($dynamicEmail->subject, $user);
        $body = self::replaceMessage($dynamicEmail->message, $user);
        return $mail->registerSendEmailRequest($user['user_email'], $subject, $body, "Formulário Recuperar Senha");
    }

    public static function formNovoClinica($user, $email) {
        $systemVariables = VariavelDoSistema::first();
        if (empty($systemVariables->email_dinamico_novo_cliente)) {
            return false;
        }

        $dynamicEmail = DynamicEmail::where('id', $systemVariables->email_dinamico_novo_cliente)->where('deleted', 0)->first();
        if (!$dynamicEmail) {
            return false;
        }
        $mail = new Mail($systemVariables);
        $subject = self::replaceMessage($dynamicEmail->subject, $user);
        $body = self::replaceMessage($dynamicEmail->message, $user);
        return $mail->registerSendEmailRequest($email, $subject, $body, "Formulário Novo Clínica SGS");
    }

    public static function formNovoMedico($user, $email) {
        $systemVariables = VariavelDoSistema::first();
        if (empty($systemVariables->email_dinamico_novo_medico)) {
            print 1;
            return false;
        }

        $dynamicEmail = DynamicEmail::where('id', $systemVariables->email_dinamico_novo_medico)->where('deleted', 0)->first();
        if (!$dynamicEmail) {
            print 2;
            return false;
        }
        $mail = new Mail($systemVariables);
        $subject = self::replaceMessage($dynamicEmail->subject, $user);
        $body = self::replaceMessage($dynamicEmail->message, $user);
        return $mail->registerSendEmailRequest($email, $subject, $body, "Formulário Novo Médico SGS");
    }

    public static function formContato($user) {
        $config = DatabaseFactory::fetchTable('variaveis_do_sistema');
        if (empty($config['email_dinamico_contato']))
            return false;

        $dynamicEmail = DatabaseFactory::fetchTable('dynamic_emails', ['id' => $config['email_dinamico_contato'], 'deleted' => 0]);
        if (!(count($dynamicEmail) > 0))
            return false;

        $mail = new Mail();
        $subject = self::replaceMessage($dynamicEmail['subject'], $user);
        $body = self::replaceMessage($dynamicEmail['message'], $user);
        $mail->registerSendEmailRequest($config['email_contato'], $subject, $body, "Formulário de Contato");
    }

    public static function emailDeAtendimento(array $user) {
        $systemVariables = VariavelDoSistema::find(1)->first();
        if ($systemVariables->EMAIL_ATENDIMENTO_CONFIRMADO)
            return false;

        $dynamicEmail = DynamicEmail::where('id', $systemVariables->EMAIL_ATENDIMENTO_CONFIRMADO)->where('deleted', 0)->first();
        if (!$dynamicEmail)
            return false;

        $mail = new Mail();
        $subject = self::replaceMessage($dynamicEmail->subject, $user);
        $body = self::replaceMessage($dynamicEmail->message, $user);
        $mail->registerSendEmailRequest($user['user_email'], $subject, $body, 'Guia de Encaminhamento');
    }

    public static function replaceMessage($message, array $userData) {
        $exclude = ['device', 'platform', 'browser', 'ip'];
        foreach (self::prefix() as $prefix => $props) {
            if (!$props['replace']) {
                $replace = NULL;
                // extract email from sync data collection
                switch ($props['index']) {
                    case 'user_full_name':
                        $replace = empty($userData['fullName']) ? $userData['user_first_name'] : $userData['fullName'];
                        break;
                    case 'user_email':
                        $replace = $userData['user_email'];
                        break;
                    case 'user_name':
                        if (empty($userData['user_name']))
                            break;

                        $replace = strlen($userData['user_name']) == 14 ? Utils::mask($userData['user_name'], Utils::$MASK_CNPJ) : Utils::mask($userData['user_name'], Utils::$MASK_CPF);
                        break;
                    case 'user_password':
                        if (empty($userData['user_password']))
                            break;

                        $replace = $userData['user_password'];
                        break;
                    case 'recovery_token':
                        if (empty($userData['token']))
                            break;

                        $replace = config('app.url') . "/recuperar-senha/token/{$userData['token']}";
                        break;
                }
                if (!empty($replace))
                    $message = preg_replace("/{$prefix}/", $replace, $message);
            } else {
                $message = preg_replace("/{$prefix}/", $props['replace'], $message);
            }
        }
        return $message;
    }
}
