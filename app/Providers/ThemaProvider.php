<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Models\Video;
use App\Providers\Utils;

class ThemaProvider extends ServiceProvider {

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

    public static function tamanho() {
        $sidebar = !empty($_COOKIE['theme_sidebar']) ? $_COOKIE['theme_sidebar'] : "lg";
        return $sidebar;
    }

    public static function sidebar() {
        return Auth::user()->theme == "dark" ? 'dark' : 'light';
    }

    /**
     * Puxar botão de Assistir aula exclusiva para páginas
     * 
     * @static
     * @access public
     * @return string
     */
    public static function videoAulaPagina() {
        $currentURL = str_replace(Config("app.url") . "/", '', url()->current());
        $videoAula = Video::where('link', '=', $currentURL)
                ->where('deleted', 0)
                ->first();
        if (!$videoAula)
            return '';
        
        $IDYT = Utils::extractID($videoAula->youtube);
        if (!$IDYT)
            return '';

        return <<<EOF
<div class="card-toolbar padding-right-20">
    <a href="javascript:;" title="Ver Vídeo Tutorial dessa página" data-video="{$IDYT}" class="btn btn-outline-danger btn-sm waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#modal-assistir-aula" id="btn-assistir-aula"><i class="fab fa-youtube"></i></a>
</div>
<div class="modal" id="modal-assistir-aula" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Assistir Vídeo Aula</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white font-weight-bold" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
EOF;
    }

}
