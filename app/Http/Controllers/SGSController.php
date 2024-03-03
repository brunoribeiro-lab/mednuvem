<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemMenu;
use App\Models\SystemSubmenu;
use App\Models\SystemSubSubmenu;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Users;
use App\Models\Notificacao;
use App\Providers\AuthServiceProvider;
use App\Models\Doc;

class SGSController extends Controller {

    public function mudarThema(Request $request) {
        // Pegar o tema da requisição
        $thema = $request->input('theme');

        // Validação
        if (!in_array($thema, ['dark', 'light'])) {
            return response()->json(['error' => 'Thema Inválido'], 400);
        }

        // Verificar se o usuário está logado
        $userId = Auth::user()->user_id;
        if (empty($userId)) {
            return response()->json(['error' => 'Usuário não logado'], 400);
        }

        // Atualizar o tema do usuário no banco de dados
        Users::where('user_id', $userId)->update(['theme' => $thema]);

        // Atualizar o tema na sessão
        session(['user_theme' => $thema]);

        // Responder
        return response()->json(['message' => 'OK']);
    }

    public function doc(string $index = null) {
        $response = [
            "error" => true,
            "title" => "",
            "text" => ""
        ];
        if (!empty($index)) {
            $doc = Doc::where('doc_index', $index)->first();
            if ($doc && !empty($doc->doc_title) && !empty($doc->doc_text)) {
                $response = [
                    "error" => false,
                    "title" => $doc->doc_title,
                    "text" => $doc->doc_text
                ];
            }
        }

        return response()->json($response);
    }

    /**
     * Display a listing of the resource.
     */
    public function buscarPagina(Request $request) {
        // Validação
        $validatedData = $request->validate([
            'cod' => 'required|min:3|integer',
        ]);

        $cod = $request->input('cod');
        $baseUrl = config('app.url');  // supondo que a URL base esteja em 'app.url'
        // Consultas
        $menu = SystemMenu::where('CODE', $cod)->where('STATUS', 1)->first();
        $submenu = SystemSubmenu::where('CODE', $cod)->where('STATUS', 1)->first();
        $subsubmenu = SystemSubsubmenu::where('CODE', $cod)->where('STATUS', 1)->first();

        $menuAdd = SystemMenu::where('CODE_ADD', $cod)->where('STATUS', 1)->first();
        $submenuAdd = SystemSubmenu::where('CODE_ADD', $cod)->where('STATUS', 1)->first();
        $subsubmenuAdd = SystemSubsubmenu::where('CODE_ADD', $cod)->where('STATUS', 1)->first();

        // Respostas
        if ($menu) {
            return response()->json([
                        'error' => false,
                        'url' => "{$baseUrl}/{$menu->LINK}"
            ]);
        }

        if ($submenu) {
            return response()->json([
                        'error' => false,
                        'url' => "{$baseUrl}/{$submenu->LINK}"
            ]);
        }

        if ($subsubmenu) {
            return response()->json([
                        'error' => false,
                        'url' => "{$baseUrl}/{$subsubmenu->LINK}"
            ]);
        }

        if ($menuAdd) {
            return response()->json([
                        'error' => false,
                        'url' => "{$baseUrl}/{$menuAdd->LINK}#add"
            ]);
        }

        if ($submenuAdd) {
            return response()->json([
                        'error' => false,
                        'url' => "{$baseUrl}/{$submenuAdd->LINK}#add"
            ]);
        }

        if ($subsubmenuAdd) {
            return response()->json([
                        'error' => false,
                        'url' => "{$baseUrl}/{$subsubmenuAdd->LINK}#add"
            ]);
        }

        return response()->json([
                    'error' => true,
                    'message' => "Página não encontrada com o código $cod."
        ]);
    }
}
