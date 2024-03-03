<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemMenu;
use App\Models\Video;

class VideoAulasController extends Controller {

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        $menu = SystemMenu::__construir(); // carrega o menu do SGS
        $keyword = $request->get('keyword', '');
        $perPage = 6;  // Itens por página
        // Se você tiver algum filtro de pesquisa, pode adicionar aqui. Por enquanto, estou apenas filtrando por 'deleted'.
        $query = Video::where('deleted', 0);

        // Se você deseja ordenar por título:
        $query = $query->orderBy('title', 'ASC');

        // Pegando os resultados com paginação
        $videos = $query->paginate($perPage);

        $data = [// data usada nessa página
            'default' => [
                "menu" => 1,
                'submenu' => NULL,
                "subsubmenu" => NULL
            ],
            'identificador' => [
                'padrão' => SystemMenu::identificador(),
                'add' => SystemMenu::identificador(true)
            ],
        ];

        return view('videos.videos')
                        ->with('videos', $videos)
                        ->with('data', $data)
                        ->with('menu', $menu);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request) {
        $keyword = $request->input('keyword', '');
        $perPage = 6;  // Itens por página
        // Se você tiver algum filtro de pesquisa, pode adicionar aqui. Por enquanto, estou apenas filtrando por 'deleted'.
        $query = Video::where('deleted', 0);
        if ($keyword) {
            $query->where('title', 'LIKE', "%{$keyword}%");
        }
        // Se você deseja ordenar por título:
        $query = $query->orderBy('title', 'ASC');

        // Pegando os resultados com paginação
        $videos = $query->paginate($perPage);

        $data = [// data usada nessa página
            'default' => [
                "menu" => 1,
                'submenu' => NULL,
                "subsubmenu" => NULL
            ],
            'identificador' => [
                'padrão' => SystemMenu::identificador(),
                'add' => SystemMenu::identificador(true)
            ],
        ];

        return view('videos.reload')
                        ->with('videos', $videos)
                        ->with('data', $data);
    }

}
