<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use PHPMailer\PHPMailer\PHPMailer;
use App\Providers\Encryption;
use Illuminate\Support\Facades\Config;
use App\Models\NotifierList;
use Illuminate\Support\Carbon;

/**
 * Armazenar Todos os IDS de identificação de menus
 * usado para consultar acessos
 * 
 */
class MenusCollection extends ServiceProvider {

    /**
     * Ids dos menus 
     * 
     * @var array 
     */
    public static $menus = array(
        // site 
        "mensagens" => [
            "menu" => 5,
            "submenu" => 15,
            "subsubmenu" => 12,
        ],
        // medico 
        "setor" => [
            "menu" => 5,
            "submenu" => 10,
            "subsubmenu" => 18,
        ],
        "função" => [
            "menu" => 5,
            "submenu" => 10,
            "subsubmenu" => 17,
        ],
        "exames" => [
            "menu" => 5,
            "submenu" => 10,
            "subsubmenu" => 19,
        ],
        "pacientes" => [
            "menu" => 3,
            "submenu" => NULL,
            "subsubmenu" => NULL,
        ],
        "medicos" => [
            "menu" => 7,
            "submenu" => NULL,
            "subsubmenu" => NULL,
        ],
        "acessos" => [
            "menu" => 5,
            "submenu" => 19,
            "subsubmenu" => 16,
        ],
        // email
        'emails' => [
            "menu" => 5,
            "submenu" => 11,
            "subsubmenu" => 2,
        ],
        'disparos' => [
            "menu" => 5,
            "submenu" => 11,
            "subsubmenu" => 1,
        ],
        'config' => [
            "menu" => 5,
            "submenu" => 11,
            "subsubmenu" => 3,
        ],
        // desenvolvedor
        "variaveis-do-sistema" => [
            "menu" => 5,
            "submenu" => 19,
            "subsubmenu" => 13,
        ],
        "acesso-xml" => [
            "menu" => 5,
            "submenu" => 19,
            "subsubmenu" => 16,
        ],
        "documentacao" => [
            "menu" => 5,
            "submenu" => 19,
            "subsubmenu" => 8,
        ],
        "videos-aulas" => [
            "menu" => 5,
            "submenu" => 19,
            "subsubmenu" => 9,
        ],
        "logs" => [
            "menu" => 5,
            "submenu" => 19,
            "subsubmenu" => 11,
        ],
        "tarefas" => [
            "menu" => 5,
            "submenu" => 19,
            "subsubmenu" => 7,
        ],
        "menus" => [
            "menu" => 5,
            "submenu" => 19,
            "subsubmenu" => 10,
        ],
        "logos" => [
            "menu" => 5,
            "submenu" => 19,
            "subsubmenu" => 15,
        ]
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
}
