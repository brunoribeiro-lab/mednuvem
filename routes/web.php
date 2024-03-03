<?php

use Illuminate\Support\Facades\Route;
use App\Models\SiteFormContato;
use App\Http\Controllers\UserController;
use App\Models\SystemMenu;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\APIController;
use App\Http\Controllers\SGSController;
use App\Http\Controllers\VariaveisDoSistemaController;
use App\Http\Controllers\FinanceiroController;
use App\Http\Controllers\VideoAulasController;
use App\Http\Controllers\GlobaisController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\AssinaturasController;
use App\Http\Controllers\CamposController;
use App\Http\Controllers\DesenvolvedorController;
use App\Http\Controllers\CargosController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\RomaneioController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\VariaveisDaOperacaoConfigController;
use App\Http\Controllers\ComissoesConfigController;
use Illuminate\Http\Request;
use App\Http\Controllers\MedicosController;
use App\Http\Controllers\SaidasFixasController;
use App\Http\Controllers\ContasAvulsaController;
use App\Http\Controllers\FluxoCaixaController;
use App\Http\Controllers\PlanosController;
use App\Models\Planos;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\RecursosController;
use App\Models\FormContato;
use App\Models\Users;

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider and all of them will
  | be assigned to the "web" middleware group. Make something great!
  |
 */
Route::get('/', function (Request $request) {
    $params = [
    ];
    return view('site.index', $params);
});

Route::post('/enviar-mensagem', [SiteController::class, 'enviarMensagem']);
Route::get('/login', function () {
    return redirect('/area-cliente');
})->name('login');

Route::group(['prefix' => 'recuperar-senha'], function () {
    Route::get('/', function () {
        return view('site.recuperar-senha');
    });
    Route::get('/token/{id}', [UserController::class, 'novaSenha']);
    Route::post('/recuperar', [Users::class, 'recuperar']);
    Route::post('/nova-senha', [Users::class, 'mudarSenha']);
});
Route::get('/area-cliente', function () {
    return view('site.login');
});

Route::group(['prefix' => 'login'], function () {
    Route::post('/login', [UserController::class, 'login']);
    Route::get('/logout', [UserController::class, 'logout']);
});
Route::group(['prefix' => 'contato'], function () {
    Route::post('/enviar', [FormContato::class, 'enviar']);
});
// API
Route::group(['prefix' => 'API/v1'], function () {
    Route::get('/cep/{id}', [APIController::class, 'cep']);
});

// SGS
Route::group(['prefix' => 'SGS', 'middleware' => 'auth'], function () {
    // index do SGS
    Route::get('/', function () {
        $menu = SystemMenu::__construir(); // carrega o menu do SGS
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
        return view('index')
                ->with('data', $data)
                ->with('menu', $menu);
    });
    // perfil
    Route::get('/videos-aulas', [VideoAulasController::class, 'index']);
    Route::post('/videos-aulas', [VideoAulasController::class, 'show']);

    Route::group(['prefix' => 'perfil', 'middleware' => 'auth'], function () {
        Route::get('/', [PerfilController::class, 'index']);
        Route::post('/salvar', [PerfilController::class, 'update']);
    });

    # SGS/clientes
    Route::group(['prefix' => 'pacientes', 'middleware' => 'auth'], function () {
        Route::get('/', [PacienteController::class, 'index']);
        # CRUD AJAX
        Route::get('/listar', [PacienteController::class, 'datatable']);
        Route::get('/listar-prontuario/{id}', [PacienteController::class, 'datatableUpload']);
        Route::get('/baixar-documento/{id}', [PacienteController::class, 'download']);
        Route::get('/filtrar', [PacienteController::class, 'showFiltrar']);
        Route::get('/ver/{id}', [PacienteController::class, 'show']);
        Route::get('/prontuario/{id}', [PacienteController::class, 'prontuario']);
        Route::get('/edit/{id}', [PacienteController::class, 'edit']);
        Route::post('/edit', [PacienteController::class, 'update']);
        Route::get('/add', [PacienteController::class, 'create']);
        Route::get('/get-import-export', [PacienteController::class, 'importExport']);
        Route::post('/add', [PacienteController::class, 'store']);
        Route::get('/remover/{id}', [PacienteController::class, 'destroy']);
        Route::post('/remover', [PacienteController::class, 'destroy']);
        Route::post('/upload', [PacienteController::class, 'upload']);
    });
    # SGS/medicos
    Route::group(['prefix' => 'medicos', 'middleware' => 'auth'], function () {
        Route::get('/', [MedicosController::class, 'index']);
        Route::get('/pegar-historico/{id}', [MedicosController::class, 'historico']);
        # CRUD AJAX
        Route::get('/listar', [MedicosController::class, 'datatable']);
        Route::get('/listar-historico/{id}', [MedicosController::class, 'datatableHistorico']);
        Route::get('/ver/{id}', [MedicosController::class, 'show']);
        Route::get('/edit/{id}', [MedicosController::class, 'edit']);
        Route::post('/edit', [MedicosController::class, 'update']);
        Route::get('/add', [MedicosController::class, 'create']);
        Route::post('/add', [MedicosController::class, 'store']);
        Route::get('/remover/{id}', [MedicosController::class, 'destroy']);
        Route::post('/remover', [MedicosController::class, 'destroy']);
    });
    // Outros AJAX do SGS
    Route::group(['prefix' => 'AJAX', 'middleware' => 'auth'], function () {
        // buscar página
        Route::post('/pagina', [SGSController::class, 'buscarPagina']);
        Route::get('/thema', [SGSController::class, 'mudarThema']);
        Route::get('/sininho', [SGSController::class, 'sininho']);
        Route::get('/doc/{index}', [SGSController::class, 'doc']);
    });

    // Menu configurações
    Route::group(['prefix' => 'configuracoes', 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'medico', 'middleware' => 'auth'], function () {
            // SGS/configuracoes/medico/setor
            Route::group(['prefix' => 'setor', 'middleware' => 'auth'], function () {
                // buscar página
                Route::get('/', [MedicosController::class, 'indexSetor']);
                # CRUD AJAX
                Route::get('/listar', [MedicosController::class, 'datatableSetor']);
                Route::get('/ver/{id}', [MedicosController::class, 'showSetor']);
                Route::get('/edit/{id}', [MedicosController::class, 'editSetor']);
                Route::post('/edit', [MedicosController::class, 'updateSetor']);
                Route::get('/add', [MedicosController::class, 'createSetor']);
                Route::post('/add', [MedicosController::class, 'storeSetor']);
                Route::get('/remover/{id}', [MedicosController::class, 'destroySetor']);
                Route::post('/remover', [MedicosController::class, 'destroySetor']);
            });
            // SGS/configuracoes/medico/funcao
            Route::group(['prefix' => 'funcao', 'middleware' => 'auth'], function () {
                // buscar página
                Route::get('/', [MedicosController::class, 'indexFunção']);
                # CRUD AJAX
                Route::get('/listar', [MedicosController::class, 'datatableFunção']);
                Route::get('/ver/{id}', [MedicosController::class, 'showFunção']);
                Route::get('/edit/{id}', [MedicosController::class, 'editFunção']);
                Route::post('/edit', [MedicosController::class, 'updateFunção']);
                Route::get('/add', [MedicosController::class, 'createFunção']);
                Route::post('/add', [MedicosController::class, 'storeFunção']);
                Route::get('/remover/{id}', [MedicosController::class, 'destroyFunção']);
                Route::post('/remover', [MedicosController::class, 'destroyFunção']);
            });
            // SGS/configuracoes/medico/exames
            Route::group(['prefix' => 'exames', 'middleware' => 'auth'], function () {
                // buscar página
                Route::get('/', [MedicosController::class, 'indexExames']);
                # CRUD AJAX
                Route::get('/listar', [MedicosController::class, 'datatableExame']);
                Route::get('/ver/{id}', [MedicosController::class, 'showExame']);
                Route::get('/listar-funcoes/{id}', [MedicosController::class, 'pegarFuncoes']);
                Route::get('/edit/{id}', [MedicosController::class, 'editExame']);
                Route::post('/edit', [MedicosController::class, 'updateExame']);
                Route::get('/add', [MedicosController::class, 'createExames']);
                Route::post('/add', [MedicosController::class, 'storeExame']);
                Route::get('/remover/{id}', [MedicosController::class, 'destroyExame']);
                Route::post('/remover', [MedicosController::class, 'destroyExame']);
            });
        });
        Route::group(['prefix' => 'site', 'middleware' => 'auth'], function () {
            // SGS/configuracoes/medico/setor
            Route::group(['prefix' => 'mensagens', 'middleware' => 'auth'], function () {
                // buscar página
                Route::get('/', [SiteController::class, 'index']);
                # CRUD AJAX
                Route::get('/listar', [SiteController::class, 'datatable']);
                Route::get('/ver/{id}', [SiteController::class, 'show']);
                Route::get('/remover/{id}', [SiteController::class, 'destroy']);
                Route::post('/remover', [SiteController::class, 'destroy']);
            });
        });

        // usuários do sistema
        Route::group(['prefix' => 'usuarios', 'middleware' => 'auth'], function () {
            // buscar página
            Route::get('/', [UserController::class, 'index']);
            Route::get('/pegar-historico/{id}', [UserController::class, 'historico']);
            # CRUD AJAX
            Route::get('/listar', [UserController::class, 'datatable']);
            Route::get('/listar-historico/{id}', [UserController::class, 'datatableHistorico']);
            Route::get('/ver/{id}', [UserController::class, 'show']);
            Route::get('/edit/{id}', [UserController::class, 'edit']);
            Route::post('/edit', [UserController::class, 'update']);
            Route::get('/add', [UserController::class, 'create']);
            Route::post('/add', [UserController::class, 'store']);
            Route::get('/remover/{id}', [UserController::class, 'destroy']);
            Route::post('/remover', [UserController::class, 'destroy']);
        });
        // SGS/configuracoes/cargos
        Route::group(['prefix' => 'cargos', 'middleware' => 'auth'], function () {
            Route::get('/', [CargosController::class, 'index']);
            # CRUD AJAX
            Route::get('/listar', [CargosController::class, 'datatable']);
            Route::get('/ver/{id}', [CargosController::class, 'show']);
            Route::get('/acessos/{id}', [CargosController::class, 'showAcessos']);
            Route::post('/acessos', [CargosController::class, 'updateAcessos']);
            Route::get('/edit/{id}', [CargosController::class, 'edit']);
            Route::post('/edit', [CargosController::class, 'update']);
            Route::get('/add', [CargosController::class, 'create']);
            Route::post('/add', [CargosController::class, 'store']);
            Route::get('/remover/{id}', [CargosController::class, 'destroy']);
            Route::post('/remover', [CargosController::class, 'destroy']);
        });
        // submenu Email
        Route::group(['prefix' => 'email', 'middleware' => 'auth'], function () {
            // SGS/configuracoes/email/disparos
            Route::group(['prefix' => 'disparos', 'middleware' => 'auth'], function () {
                Route::get('/', [EmailController::class, 'indexDisparos']);
                # AJAX
                Route::get('/listar', [EmailController::class, 'datatableDisparos']);
                Route::get('/ver/{id}', [EmailController::class, 'showDisparo']);
                Route::get('/reenviar/{id}', [EmailController::class, 'reenviar']);
                Route::post('/reenviar', [EmailController::class, 'reenviar']);
                Route::get('/remover/{id}', [EmailController::class, 'destroyDisparo']);
                Route::post('/remover', [EmailController::class, 'destroyDisparo']);
            });
            // SGS/configuracoes/email/dinamicos
            Route::group(['prefix' => 'dinamicos', 'middleware' => 'auth'], function () {
                Route::get('/', [EmailController::class, 'indexDinamico']);
                # CRUD AJAX
                Route::get('/listar', [EmailController::class, 'datatableDinamicos']);
                Route::get('/ver/{id}', [EmailController::class, 'showDinamico']);
                Route::get('/edit/{id}', [EmailController::class, 'editDinamico']);
                Route::post('/edit', [EmailController::class, 'updateDinamico']);
                Route::get('/add', [EmailController::class, 'createDinamico']);
                Route::post('/add', [EmailController::class, 'storeDinamico']);
                Route::get('/remover/{id}', [EmailController::class, 'destroyDinamico']);
                Route::post('/remover', [EmailController::class, 'destroyDinamico']);
            });
            // SGS/configuracoes/email/configurar-email
            Route::group(['prefix' => 'configurar-email', 'middleware' => 'auth'], function () {
                Route::get('/', [EmailController::class, 'indexConfigurar']);
                # AJAX
                Route::post('/salvar', [EmailController::class, 'updateAuth']);
                Route::get('/testar', [EmailController::class, 'testar']);
            });
        });
        // submenu Desenvolvedor
        Route::group(['prefix' => 'desenvolvedor', 'middleware' => 'auth'], function () {
            // SGS/configuracoes/desenvolvedor/menus
            Route::group(['prefix' => 'menus', 'middleware' => 'auth'], function () {
                Route::get('/', [DesenvolvedorController::class, 'indexMenu']);
                Route::get('/tabela-submenu/{id}', [DesenvolvedorController::class, 'indexSubMenu']);
                Route::get('/tabela-subsubmenu/{id}', [DesenvolvedorController::class, 'indexSubSubMenu']);
                # CRUD AJAX
                Route::get('/listar', [DesenvolvedorController::class, 'datatableMenu']);
                Route::get('/listar-submenus/{id}', [DesenvolvedorController::class, 'datatableSubMenu']);
                Route::get('/listar-subsubmenus/{id}', [DesenvolvedorController::class, 'datatableSubSubMenu']);
                Route::get('/ver/{id}', [DesenvolvedorController::class, 'showMenu']);
                Route::get('/ver-submenu/{id}', [DesenvolvedorController::class, 'showSubMenu']);
                Route::get('/ver-subsubmenu/{id}', [DesenvolvedorController::class, 'showSubSubMenu']);
                Route::get('/edit/{id}', [DesenvolvedorController::class, 'editMenu']);
                Route::post('/edit', [DesenvolvedorController::class, 'updateMenu']);
                Route::get('/edit-submenu/{id}', [DesenvolvedorController::class, 'editSubMenu']);
                Route::post('/edit-submenu', [DesenvolvedorController::class, 'updateSubMenu']);
                Route::get('/edit-subsubmenu/{id}', [DesenvolvedorController::class, 'editSubSubMenu']);
                Route::post('/edit-subsubmenu', [DesenvolvedorController::class, 'updateSubSubMenu']);
            });
            // SGS/configuracoes/desenvolvedor/documentacao
            Route::group(['prefix' => 'documentacao', 'middleware' => 'auth'], function () {
                Route::get('/', [DesenvolvedorController::class, 'indexDoc']);
                # CRUD AJAX
                Route::get('/listar', [DesenvolvedorController::class, 'datatableDoc']);
                Route::get('/ver/{id}', [DesenvolvedorController::class, 'showDoc']);
                Route::get('/edit/{id}', [DesenvolvedorController::class, 'editDoc']);
                Route::post('/edit', [DesenvolvedorController::class, 'updateDoc']);
                Route::get('/add', [DesenvolvedorController::class, 'createDoc']);
                Route::post('/add', [DesenvolvedorController::class, 'storeDoc']);
                Route::get('/remover/{id}', [DesenvolvedorController::class, 'destroyDoc']);
                Route::post('/remover', [DesenvolvedorController::class, 'destroyDoc']);
            });
            // SGS/configuracoes/desenvolvedor/videos-aulas
            Route::group(['prefix' => 'videos-aulas', 'middleware' => 'auth'], function () {
                Route::get('/', [DesenvolvedorController::class, 'indexVideo']);
                # CRUD AJAX
                Route::get('/listar', [DesenvolvedorController::class, 'datatableVid']);
                Route::get('/ver/{id}', [DesenvolvedorController::class, 'showVid']);
                Route::get('/edit/{id}', [DesenvolvedorController::class, 'editVid']);
                Route::post('/edit', [DesenvolvedorController::class, 'updateVid']);
                Route::get('/add', [DesenvolvedorController::class, 'createVid']);
                Route::post('/add', [DesenvolvedorController::class, 'storeVid']);
                Route::get('/remover/{id}', [DesenvolvedorController::class, 'destroyVideo']);
                Route::post('/remover', [DesenvolvedorController::class, 'destroyVideo']);
            });
            // SGS/configuracoes/desenvolvedor/tarefas
            Route::group(['prefix' => 'tarefas', 'middleware' => 'auth'], function () {
                Route::get('/', [DesenvolvedorController::class, 'indexTarefas']);
                # CRUD AJAX
                Route::get('/listar', [DesenvolvedorController::class, 'datatableTarefas']);
                Route::get('/ver/{id}', [DesenvolvedorController::class, 'showVid']);
                Route::get('/edit/{id}', [DesenvolvedorController::class, 'editVid']);
                Route::post('/edit', [DesenvolvedorController::class, 'updateVid']);
                Route::get('/add', [DesenvolvedorController::class, 'createVid']);
                Route::post('/add', [DesenvolvedorController::class, 'storeVid']);
                Route::get('/remover/{id}', [DesenvolvedorController::class, 'destroyVideo']);
                Route::post('/remover', [DesenvolvedorController::class, 'destroyVideo']);
            });
            // SGS/configuracoes/desenvolvedor/logs
            Route::group(['prefix' => 'logs', 'middleware' => 'auth'], function () {
                Route::get('/', [DesenvolvedorController::class, 'indexLogs']);
                # CRUD AJAX
                Route::get('/listar', [DesenvolvedorController::class, 'datatableLogs']);
                Route::get('/ver/{id}', [DesenvolvedorController::class, 'showLog']);
                Route::get('/remover/{id}', [DesenvolvedorController::class, 'destroyLogs']);
                Route::post('/remover', [DesenvolvedorController::class, 'destroyLogs']);
            });
            // SGS/configuracoes/desenvolvedor/acesso
            Route::group(['prefix' => 'acesso', 'middleware' => 'auth'], function () {
                Route::get('/', [DesenvolvedorController::class, 'indexAcesso']);
                # CRUD AJAX
                Route::post('/salvar', [DesenvolvedorController::class, 'updateAcesso']);
            });
            // SGS/configuracoes/desenvolvedor/variaveis-do-sistema
            Route::group(['prefix' => 'variaveis-do-sistema', 'middleware' => 'auth'], function () {
                // buscar página
                Route::get('/', [VariaveisDoSistemaController::class, 'index']);
                Route::post('/salvar', [VariaveisDoSistemaController::class, 'store']);
            });
            // SGS/configuracoes/desenvolvedor/logos
            Route::group(['prefix' => 'logos', 'middleware' => 'auth'], function () {
                // buscar página
                Route::get('/', [DesenvolvedorController::class, 'indexLogos']);
                Route::post('/salvar', [DesenvolvedorController::class, 'storeLogos']);
            });
        });
    });
});

