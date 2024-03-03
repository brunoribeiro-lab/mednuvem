<?php

# require '../vendor/phpmailer/phpmailer/src/DSNConfigurator.php';
require realpath(__DIR__) . '/../vendor/phpmailer/phpmailer/src/POP3.php';
require realpath(__DIR__) . '/../vendor/phpmailer/phpmailer/src/SMTP.php';
require realpath(__DIR__) . '/../vendor/phpmailer/phpmailer/src/OAuthTokenProvider.php';
require realpath(__DIR__) . '/../vendor/phpmailer/phpmailer/src/OAuth.php';
require realpath(__DIR__) . '/../vendor/phpmailer/phpmailer/src/Exception.php';
require realpath(__DIR__) . '/../vendor/phpmailer/phpmailer/src/DSNConfigurator.php';
require realpath(__DIR__) . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';

use PHPMailer\PHPMailer\PHPMailer;

/**
 * Gambiarra de notificações, usado na hospedagem PHP 7.4
 * 
 */
class Notificacoes {

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
    private $env = [];
    private $variaveis = [];
    private $error = '';

    /**
     * Execute the console command.
     */
    public function __construct() {
        $this->env = $this->loadEnv();
        // Obter as variáveis do sistema
        $this->variaveis = $this->getVariaveisDoSistema();
        # var_dump($this->env);
        # die();
        // Registrar a última execução do sistema para envio de e-mail
        $this->registerLastExecution('Email');

        // Obter os disparos de e-mail pendentes
        $pendentes = $this->getPendentes();
        if (empty($pendentes)) {
            die("Nenhum pendente");
            return;
        }

        // Iterar sobre os disparos pendentes
        foreach ($pendentes as $pendente) {
            $m = $this->enviarEmail($pendente['send_to'], $this->variaveis['nome'], $pendente['subject'], $pendente['email'], 0);
            if (!$m) {
                // Em caso de falha no envio, atualizar o registro com o erro
                $this->updatePendente($pendente['id'], "Falha ao autenticar email\n {$this->error}");
                continue;
            }

            // Marcar o disparo como enviado
            $this->markPendenteAsSended($pendente['id']);
        }
    }

    private function enviarEmail($user_email, $from_name, $subject, $body, $debug = 0) {
        $mail = new PHPMailer();
        // if you want to send mail via PHPMailer using SMTP credentials
        if ($this->variaveis['email_mode']) {
            // set PHPMailer to use SMTP
            $mail->IsSMTP();
            $mail->CharSet = 'UTF-8';
            // 0 = off, 1 = commands, 2 = commands and data, perfect to see SMTP errors
            $mail->SMTPDebug = $debug;
            // enable SMTP authentication
            $mail->SMTPAuth = 1;
            // encryption
            if ($this->variaveis['email_encrypt']) {
                $mail->SMTPSecure = str_replace("`", '', strtolower($this->variaveis['email_encrypt']));
            }
            // set SMTP provider's credentials
            $mail->Host = $this->variaveis['email_smtp'];
            $mail->Username = $this->variaveis['email_username'];
            $mail->Password = $this->decrypt(base64_decode($this->variaveis['email_password']));
            $mail->Port = $this->variaveis['email_port'];
        } else {
            $mail->IsMail();
        }
        $mail->Sender = $this->variaveis['email_username'];
        // fill mail with data
        $mail->From = $this->variaveis['email_username'];
        $mail->FromName = $from_name;
        $mail->AddAddress($user_email);
        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->msgHTML(stripslashes($body)); // fix tables
        // try to send mail, put result status (true/false into $wasSendingSuccessful)
        // I'm unsure if mail->send really returns true or false every time, tis method in PHPMailer is quite complex
        $wasSendingSuccessful = $mail->Send();
        # $this->header = $mail->getCustomHeaders();
        if ($wasSendingSuccessful) {
            return true;
        } else {
            // if not successful, copy errors into Mail's error property
            $this->error = $mail->ErrorInfo;
            return false;
        }
    }

    private function getVariaveisDoSistema() {
        $db_host = $this->env['DB_HOST'];
        $db_port = $this->env['DB_PORT']; // Adicionando a porta
        $db_database = $this->env['DB_DATABASE'];
        $db_username = $this->env['DB_USERNAME'];
        $db_password = $this->env['DB_PASSWORD'];
        try {
            $pdo = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_database", $db_username, $db_password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "SELECT * FROM variaveis_do_sistema WHERE id = 1";
            $statement = $pdo->prepare($query);
            $statement->execute();
            return $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Erro ao obter as variáveis do sistema: {$e->getMessage()}");
        }
    }

    public function decrypt($ciphertext, $Key = NULL, $Salt = NULL) {
        if (empty($ciphertext)) {
            throw new Exception("the string to decrypt can't be empty");
        }
        if (!function_exists('openssl_cipher_iv_length') || !function_exists('openssl_decrypt')) {
            throw new Exception("Encryption function don't exists");
        }
        if (empty($Key)) {
            $Key = $this->env['APP_KEY'];
        }
        if (empty($Salt)) {
            $Salt = 'AES-256-CBC';
        }
        // generate key used for authentication using ENCRYPTION_KEY & HMAC_SALT
        $key = mb_substr(hash('sha256', $Key . $Salt), 0, 32, '8bit');

        // split cipher into: hmac, cipher & iv
        $macSize = 64;
        $hmac = mb_substr($ciphertext, 0, $macSize, '8bit');
        $iv_cipher = mb_substr($ciphertext, $macSize, null, '8bit');

        // generate original hmac & compare it with the one in $ciphertext
        $originalHmac = hash_hmac('sha256', $iv_cipher, $key);
        if (!$this->hashEquals($hmac, $originalHmac)) {
            return false;
        }

        // split out the initialization vector and cipher
        $iv_size = openssl_cipher_iv_length('aes-256-cbc');
        $iv = mb_substr($iv_cipher, 0, $iv_size, '8bit');
        $cipher = mb_substr($iv_cipher, $iv_size, null, '8bit');

        return openssl_decrypt($cipher, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    }

    private function hashEquals($hmac, $compare) {
        if (function_exists('hash_equals')) {
            return hash_equals($hmac, $compare);
        }
        // if hash_equals() is not available,
        // then use the following snippet.
        // It's equivalent to hash_equals() in PHP 5.6.
        $hashLength = mb_strlen($hmac, '8bit');
        $compareLength = mb_strlen($compare, '8bit');
        if ($hashLength !== $compareLength) {
            return false;
        }
        $result = 0;
        for ($i = 0; $i < $hashLength; $i++) {
            $result |= (ord($hmac[$i]) ^ ord($compare[$i]));
        }
        return $result === 0;
    }

    private function registerLastExecution($tipo) {
        $db_host = $this->env['DB_HOST'];
        $db_port = $this->env['DB_PORT']; // Adicionando a porta
        $db_database = $this->env['DB_DATABASE'];
        $db_username = $this->env['DB_USERNAME'];
        $db_password = $this->env['DB_PASSWORD'];
        try {
            $pdo = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_database", $db_username, $db_password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "UPDATE _SYSTEM_CRON SET LAST_EXECUTION = :currentTime WHERE TASK = :taskIndex";
            $statement = $pdo->prepare($query);
            $statement->bindValue(':currentTime', date('Y-m-d H:i:s'));
            $statement->bindValue(':taskIndex', $tipo);
            $statement->execute();
        } catch (PDOException $e) {
            die("Erro ao atualizar a última execução da tarefa: {$e->getMessage()}");
        }
    }

    private function getPendentes() {
        $db_host = $this->env['DB_HOST'];
        $db_port = $this->env['DB_PORT']; // Adicionando a porta
        $db_database = $this->env['DB_DATABASE'];
        $db_username = $this->env['DB_USERNAME'];
        $db_password = $this->env['DB_PASSWORD'];
        try {
            $pdo = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_database", $db_username, $db_password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "SELECT * FROM notifier_list WHERE deletado = 0 AND sended = 0 ORDER BY id ASC";
            $statement = $pdo->prepare($query);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Erro ao obter os notifier_list pendentes: " . $e->getMessage());
        }
    }

    private function updatePendente($id, $error) {
        $db_host = $this->env['DB_HOST'];
        $db_port = $this->env['DB_PORT']; // Adicionando a porta
        $db_database = $this->env['DB_DATABASE'];
        $db_username = $this->env['DB_USERNAME'];
        $db_password = $this->env['DB_PASSWORD'];
        try {
            $pdo = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_database", $db_username, $db_password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "UPDATE notifier_list SET error = :error WHERE id = :id";
            $statement = $pdo->prepare($query);
            $statement->bindParam(':error', $error);
            $statement->bindParam(':id', $id);
            $statement->execute();
        } catch (PDOException $e) {
            $this->error("Erro ao atualizar o disparo pendente: " . $e->getMessage());
            die();
        }
    }

    private function markPendenteAsSended($id) {
        $db_host = $this->env['DB_HOST'];
        $db_port = $this->env['DB_PORT']; // Adicionando a porta
        $db_database = $this->env['DB_DATABASE'];
        $db_username = $this->env['DB_USERNAME'];
        $db_password = $this->env['DB_PASSWORD'];
        try {
            $pdo = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_database", $db_username, $db_password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "UPDATE notifier_list SET sended = 1, error = NULL WHERE id = :id";
            $statement = $pdo->prepare($query);
            $statement->bindParam(':id', $id);
            $statement->execute();
        } catch (PDOException $e) {
            $this->error("Erro ao marcar o disparo como enviado: " . $e->getMessage());
            die();
        }
    }

    private function loadEnv() {
        $envFile = __DIR__ . '/../.env';
        $envVariables = [];

        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && substr($line, 0, 1) !== '#') {
                    list($key, $value) = explode('=', $line, 2);
                    $envVariables[$key] = $value;
                }
            }
        }

        return $envVariables;
    }
}

new Notificacoes();
