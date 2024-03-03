<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Acesso;
use App\Models\VariavelDoSistema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthServiceProvider extends ServiceProvider {

    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
            //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void {
        //
    }

    public static function acoes($menu, $sub = NULL, $subsub = NULL) {
        $xml = self::getMenuXML();
        if (!empty($subsub)) {
            $id = $subsub;
            $_sub = false;
            $_subsub = true;
        } elseif (!empty($sub)) {
            $id = $sub;
            $_sub = true;
            $_subsub = false;
        } else {
            $id = $menu;
            $_sub = false;
            $_subsub = false;
        }
        $acessos = [];
        $acessosXML = self::getXMLAcessFromID($xml, $id, $_sub, $_subsub);
        if (!$acessosXML)
            die("Acesso não encontrado id:{$id}, menu:{$menu} submenu:{$sub} subsubmenu:{$subsub}");

        foreach ($acessosXML->access as $k => $objAcessos) {
            foreach ($objAcessos as $acao => $nUsa) {
                if ($acao == "detail")
                    $acao = "preview";
                $acessos[$acao] = self::acao(sprintf("ACCESS_%s", strtoupper($acao)), $menu, $sub, $subsub);
            }
        }

        return $acessos;
    }

    public static function getMenuXML() {
        $systemVariables = VariavelDoSistema::get()->first();
        $xml = $systemVariables->acessos;
        if (empty($xml))
            die("XML MENU NOT FOUND");

        $content = preg_replace("/<!--.*?-->/", '', $xml);
        return simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);
    }

    /**
     * Verificar se o usuario tem acesso a uma ação
     * 
     * @static
     * @access public
     * @param ENUM $acao Ação que vai validar
     * @param int $menu ID do menu
     * @param int $sub ID do submenu
     * @param int $subsub ID do subsubmenu
     * @return boolean
     */
    public static function acao($acao = 'ACCESS_LISTING', $menu, $sub = NULL, $subsub = NULL) {
        if (Session::get('is_root'))
            return true;

        return empty(self::checked((int) Auth::user()->user_account_type, $acao, $menu, $sub, $subsub)) ? false : true;
    }

    /**
     * Pega o XML do menu informado
     * 
     * @static
     * @param obj $xml
     * @param int $id
     * @param bool $sub
     * @param bool $subsub
     * @return obj or NULL
     */
    private static function getXMLAcessFromID($xml, $id, $sub = true, $subsub = false) {
        foreach ($xml->menu as $v) {
            $comparador = "true";
            if (!$subsub && $sub && (int) $v->id == (int) $id && (string) $v->sub == "true" && (string) $v->subsub == "false") {
               # print "SUBMENU";
               # var_dump($v);
                return $v;
            }
            if (!$sub && $subsub && (int) $v->id == (int) $id && (string) $v->subsub == "true" && (string) $v->sub == "false") {
              #  print "SUBSUBMENU";
               # var_dump($v);
                return $v;
            }
            if (!$sub && !$subsub && (string) $v->subsub == "false" && (string) $v->sub == "false" && (int) $v->id == (int) $id) {
                #print "MENU {$id}";
                #var_dump($v);
                return $v;
            } 
        }
        print $xml;
        return NULL;
    }

    /**
     * Verificar se tem acesso e marcar como checado
     * 
     * @static
     * @access public
     * @param int $account ID do cargo
     * @param ENUM $index Ação que vai validar
     * @param int $menu ID do menu
     * @param int $sub ID do submenu
     * @param int $subsub ID do subsubmenu
     * @return string
     */
    public static function checked($account, $index = 'ACCESS_LISTING', $menu, $sub = NULL, $subsub = NULL) {
        $equal = [
            "ACCOUNT" => $account,
            "MENU" => $menu,
            "SUBMENU" => $sub,
            "SUBSUBMENU" => $subsub,
            $index => 1
        ];

        // Remover itens vazios/null
        $equal = array_filter($equal, function ($value) {
            return !is_null($value);
        });

        if (Acesso::where($equal)->count() > 0)
            return ' checked=""';

        return '';
    }
}
