<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use PHPMailer\PHPMailer\PHPMailer;
use App\Providers\Encryption;
use Illuminate\Support\Facades\Config;
use App\Models\NotifierList;
use Illuminate\Support\Carbon;

class Mail extends ServiceProvider {

    /** @var mixed variable to collect errors */
    private $error;
    private $header;

    /**
     * Auth Collection data
     * @var array 
     */
    public $auth = array(
        "EMAIL_USE_SMTP" => FALSE,
        "EMAIL_SMTP_AUTH" => FALSE,
        "EMAIL_SMTP_ENCRYPTION" => NULL,
        "EMAIL_SMTP_HOST" => NULL,
        "EMAIL_SMTP_USERNAME" => NULL,
        "EMAIL_SMTP_PASSWORD" => NULL,
        "EMAIL_SMTP_PORT" => NULL
    );

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
        /*
         */
    }

    public function __construct($systemVariables) {
        $this->auth['EMAIL_USE_SMTP'] = $systemVariables->email_mode;

        if ($systemVariables->email_mode == "smtp") {
            $this->auth['EMAIL_SMTP_ENCRYPTION'] = str_replace("`", '', strtolower($systemVariables->email_encrypt));
            $this->auth['EMAIL_SMTP_HOST'] = $systemVariables->email_smtp;
            $this->auth['EMAIL_SMTP_USERNAME'] = $systemVariables->email_username;
            $this->auth['EMAIL_SMTP_PORT'] = $systemVariables->email_port;
            if ($systemVariables->email_password)
                $this->auth['EMAIL_SMTP_PASSWORD'] = Encryption::decrypt(base64_decode($systemVariables->email_password));
        }
    }

    /**
     * Try to send a mail by using PHP's native mail() function.
     * Please note that not PHP itself will send a mail, it's just a wrapper for Linux's sendmail or other mail tools
     *
     * Good guideline on how to send mails natively with mail():
     * @see http://stackoverflow.com/a/24644450/1114320
     * @see http://www.php.net/manual/en/function.mail.php
     */
    public function sendMailWithNativeMailFunction($user_email, $from_email, $from_name, $subject, $body) {
        $headers = "From: {$from_name} <{$from_email}>" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        return mail($user_email, $subject, $body, $headers);
    }

    /**
     * Try to send a mail by using PHPMailer.
     * Make sure you have loaded PHPMailer via Composer.
     * Depending on your EMAIL_USE_SMTP setting this will work via SMTP credentials or via native mail()
     *
     * @param $user_email
     * @param $from_email
     * @param $from_name
     * @param $subject
     * @param $body
     *
     * @return bool
     * @throws Exception
     * @throws phpmailerException
     */
    public function sendMailWithPHPMailer($user_email, $from_name, $subject, $body, $debug = 0) {
        $mail = new PHPMailer();
        // if you want to send mail via PHPMailer using SMTP credentials
        if ($this->auth['EMAIL_USE_SMTP']) {
            // set PHPMailer to use SMTP
            $mail->IsSMTP();
            $mail->CharSet = 'UTF-8';
            // 0 = off, 1 = commands, 2 = commands and data, perfect to see SMTP errors
            $mail->SMTPDebug = $debug;
            // enable SMTP authentication
            $mail->SMTPAuth = 1;
            // encryption
            if ($this->auth['EMAIL_SMTP_ENCRYPTION']) {
                $mail->SMTPSecure = $this->auth['EMAIL_SMTP_ENCRYPTION'];
            }
            // set SMTP provider's credentials
            $mail->Host = $this->auth['EMAIL_SMTP_HOST'];
            $mail->Username = $this->auth['EMAIL_SMTP_USERNAME'];
            $mail->Password = $this->auth['EMAIL_SMTP_PASSWORD'];
            $mail->Port = $this->auth['EMAIL_SMTP_PORT'];
        } else {
            $mail->IsMail();
        }
        $mail->Sender = $this->auth['EMAIL_SMTP_USERNAME'];
        // fill mail with data
        $mail->From = $this->auth['EMAIL_SMTP_USERNAME'];
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

    /**
     * The main mail sending method, this simply calls a certain mail sending method depending on which mail provider
     * you've selected in the application's config.
     *
     * @param $user_email string email
     * @param $from_email string sender's email
     * @param $from_name string sender's name
     * @param $subject string subject
     * @param $body string full mail body text
     * @return bool the success status of the according mail sending method
     */
    public function sendMail($user_email, $from_email, $from_name, $subject, $body, $sendBy = "phpmailer") {
        if ($sendBy == "phpmailer") {
            // returns true if successful, false if not
            return $this->sendMailWithPHPMailer($user_email, $from_email, $from_name, $subject, $body);
        }
        if ($sendBy == "native") {
            return $this->sendMailWithNativeMailFunction($user_email, $from_email, $from_name, $subject, $body);
        }
        return false;
    }

    /**
     * Register the sending email request on Database
     * 
     * @access public
     * @param string $user_email Email to send
     * @param string $subject Email Subject
     * @param string $body Email Message
     * @return bool
     */
    public function registerSendEmailRequest($user_email, $subject, $body, $ref = NULL) {
        if ($this->detectDuplicate($user_email, $subject, $body, $ref))
            return false; // notificação duplicada

        $notifier = NotifierList::create([
                    'ref' => $ref,
                    'send_to' => $user_email,
                    'subject' => $subject,
                    'email' => $body,
                    'created_at' => now()
        ]);
        return $notifier ? true : false;
    }

    private function detectDuplicate($user_email, $subject, $body, $ref = NULL) {
        $lasts = NotifierList::where([
                    'deletado' => 0,
                    'send_to' => $user_email,
                    'subject' => $subject,
                    'ref' => $ref
                ])->get();

        if (!$lasts->count())
            return false;

        foreach ($lasts as $l) {
            // este e-mail é de hoje
            if (Carbon::parse($l->created_at)->toDateString() == now()->toDateString()) {
                // este e-mail é exatamente como outro já enviado
                if (base64_encode($l->email) == base64_encode($body))
                    return true; // e-mail duplicado
            }
        }

        return false;
    }

    /**
     * The different mail sending methods write errors to the error property $this->error,
     * this method simply returns this error / error array.
     *
     * @return mixed
     */
    public function getError() {
        return $this->error;
    }

    public function getHeaderInfo() {
        return $this->header;
    }
}
