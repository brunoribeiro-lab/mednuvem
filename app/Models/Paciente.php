<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Providers\AuthServiceProvider;
use Illuminate\Support\Carbon;
use App\Providers\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Providers\Converter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use App\Models\Documentos;
use Illuminate\Support\Facades\Storage;
use Aws\S3\S3Client;

class Paciente extends Model {

    use HasFactory;

    protected $table = 'pacientes';
    protected $fillable = [
        'deletado',
        'grupo',
        'criado_por',
        'atualizado_por',
        'medico',
        'nome',
        'CPF',
        'telefone',
        'criado_em',
        'atualizado_em'
    ];
    public $timestamps = false;
    private static $menu = [
        "menu" => 3,
        "submenu" => NULL,
        "subsubmenu" => NULL,
    ];
    private static $actions = []; // ações do CRUD

    /**
     * Criar a SQL de listar 
     * 
     * @static
     * @access private
     * @param Object $request
     * @param boolean $todos Se true Ignora o limite
     * @return array
     */
    private static function sql_listar($request, $todos = false) {
        $sortMap = [
            2 => "id",
            3 => "nome",
            4 => "CPF",
            5 => "telefone",
            6 => "criado_em",
        ];

        $sSearch = trim($request->input('sSearch'));
        $query = self::join('users as source', function ($join) {
                    $join->on('pacientes.grupo', '=', 'source.user_id')
                    ->where('source.deleted', 0);
                })
                ->join('medicos', function ($join) {
                    $join->on('pacientes.medico', '=', 'medicos.id')
                    ->where('medicos.deletado', 0);
                })
                ->where('pacientes.deletado', 0);

        # Se não for root, mostra apenas pacientes do grupo do usuário
        if (!Session::get('is_root')) {
            $query->where('pacientes.grupo', Auth::user()->group);
        }

        if (!empty($sSearch)) {
            $query->where(function ($q) use ($sSearch) {
                $q
                        ->orWhere('pacientes.nome', 'like', "%$sSearch%");
                // pesquisar pelo código
                if (preg_match('/PAC/i', $sSearch))
                    $q->orWhere(function ($query) use ($sSearch) {
                        $query->where('pacientes.id', 'like', '%' . (int) Utils::extrairNum($sSearch) . '%');
                    });
            });
        }

        $query->select('pacientes.*')
                ->groupBy('pacientes.id', 'pacientes.deletado', 'pacientes.grupo', 'pacientes.criado_por', 'pacientes.atualizado_por', 'pacientes.medico', 'pacientes.nome', 'pacientes.CPF', 'pacientes.telefone', 'pacientes.criado_em', 'pacientes.atualizado_em'); // Adiciona a coluna deletado ao GROUP BY
        // Ordenação
        $sortColumnIndex = intval($request->input('iSortCol_0', 0));
        $sortDirection = $request->input('sSortDir_0', 'asc');
        $sortBy = $sortMap[$sortColumnIndex] ?? 'id'; // Se a coluna de classificação não existir no mapa, classifique por ID

        $query->orderBy($sortBy, $sortDirection);

        // Paginação
        if (!$todos) {
            $query->limit($request->input('iDisplayLength'))->offset($request->input('iDisplayStart'));
        }

        return $query->get();
    }

    /**
     * Cria um padrão de listagem para o plugin Jquery Datatable
     * 
     * @static
     * @access public
     * @param Object $request
     * @return json
     */
    public static function datatable(Request $request) {
        $clientes = self::sql_listar($request);
        $total = count(self::sql_listar($request, true));
        self::$actions = AuthServiceProvider::acoes(...array_values(self::$menu));
        $row = array();
        foreach ($clientes as $cliente) {
            $nice = Carbon::parse($cliente->criado_em)->diffForHumans();
            $updated = Utils::dataCompletaPTBR($cliente->criado_em);
            $dados = [
                '',
                self::checkBox($cliente->id),
                sprintf("PAC%s", str_pad($cliente->id, 6, '0', STR_PAD_LEFT)),
                strlen($cliente->nome) >= 50 ? sprintf("<abbr title='%s'>%s</abbr>", $cliente->nome, Str::limit($cliente->nome, 50)) : $cliente->nome,
                $cliente->CPF ? Utils::mask($cliente->CPF, Utils::$MASK_CPF) : '-',
                $cliente->telefone ? Utils::mask($cliente->telefone, Utils::$MASK_PHONE) : '-',
                "<abbr title='em {$updated}'>{$nice}</abbr>",
                self::actionButton($cliente)
            ];

            $row[] = $dados;
        }
        $output = array(
            "sEcho" => intval($request->input('sEcho')),
            "iTotalRecords" => $total,
            "iTotalDisplayRecords" => $total,
            "aaData" => $row
        );
        return response()->json($output);
    }

    public static function actionButton($query, $index = "default") {
        if ($index == 'default') {
            $buttons = [];
            if (self::$actions['preview'])
                $buttons[] = "<button class=\"btn btn-success goProntuario\" type=\"button\"  data-id=\"{$query['id']}\" title=\"Prontuário do Paciente\"><i class=\"far fa-clipboard-user\"></i> </button>";

            if (self::$actions['preview'])
                $buttons[] = "<button class=\"btn btn-white goPreview\" type=\"button\"  data-id=\"{$query['id']}\" title=\"Detalhes do Paciente\"><i class=\"fa fa-eye\"></i> </button>";

            if (self::$actions['update'])
                $buttons[] = "<button class=\"btn btn-primary goUpdate\" type=\"button\" data-id=\"{$query['id']}\" title=\"Editar Paciente\"><i class=\"fas fa-pencil-alt\"></i> </button>";

            if (self::$actions['remove'])
                $buttons[] = "<button class=\"btn btn-danger goRem\" type=\"button\" data-id=\"{$query['id']}\" title=\"Excluir Paciente\" data-toggle=\"modal\" data-target=\"#myModalRem\"><i class=\"fa fa-trash\"></i> </button>";

            return implode("\n", $buttons);
        }
    }

    public static function checkBox($id) {
        return "<div class=\" check-default\">
  <input type=\"checkbox\" name=\"checkbox[]\" value=\"{$id}\" id=\"checkbox{$id}\">
  <label for=\"checkbox{$id}\"></label>
</div>";
    }

    /**
     * Salvar/Cadastrar no banco de dados
     * 
     * @access public
     * @param Request $request
     * @param int|NULL $id
     * @return json
     * @throws ValidationException
     */
    public function salvar(Request $request, $id = NULL) {
        if (!AuthServiceProvider::acao($id ? 'ACCESS_UPDATE' : 'ACCESS_ADD', ...array_values(self::$menu)))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        // se for root, verifica se o cliente existe
        if (Session::get('is_root') && $id && !$campo = self::find($id))
            return response()->json(['error' => true, 'msg' => 'Registro não encontrado']);

        // se não for root, verifica se o cliente existe com o mesmo grupo de usuário
        if (!Session::get('is_root') && $id && !$campo = self::find($id)->where('user', Auth::user()->group))
            return response()->json(['error' => true, 'msg' => 'Registro não encontrado']);

        $config = VariavelDoSistema::first();
        $rules = [
            'nome' => [
                'required',
                'string',
                'min:3',
                'max:200',
            ],
            'celular' => [
                'nullable',
                'min:16',
                'max:16',
                'Telefone'
            ],
            'cpf' => [
                'nullable',
                'min:14',
                'max:14',
                'cpf',
                function ($attribute, $value, $validator) use ($request, $id) {
                    $onlyNumbers = preg_replace('/\D/', '', $value);
                    $pac = self::where('deletado', 0)->where('cpf', $onlyNumbers)->first();
                    if ($id)
                        $pac = self::where('deletado', 0)->where('cpf', $onlyNumbers)->where('id', '!=', $id)->first();

                    if ($pac)
                        $validator('CPF já existe.');
                },
            ],
        ];
        if (Session::get('is_root') || Auth::user()->user_account_type == $config->clinica) {
            $rules['medico'] = [
                'required',
                'integer',
                'min:0',
                "exists_in:medicos,id,deletado,0"
            ];
        }
        $messages = [
            '*.*.required_with' => 'Campo obrigatório',
            '*.required' => 'Campo obrigatório',
            '*.min' => 'O campo deve ter no mínimo :min caracteres',
            '*.max' => 'O campo deve ter no máximo :max caracteres',
            '*.in' => 'Campo Inválido',
            '*.telefone' => 'Celular inválido',
            '*.integer' => 'Campo inválido, apenas números',
            'cpf.cpf' => "CPF inválido"
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails())
            throw new ValidationException($validator);

        if (Session::get('is_root') || Auth::user()->user_account_type == $config->clinica) {
            $med = Medico::where('id', $request->input('medico'))->first();
            $medico = $med->id;
        } else {
            $med = Medico::where('id_usuario', Auth::user()->user_id)->first();
            $medico = $med->id;
        }
        if ($id) { // atualizar no banco
            $formCampo = self::find($id);
            $nome_antigo = $formCampo->nome;
            $formCampo->atualizado_por = Auth::user()->user_id;
            $formCampo->grupo = $med->grupo;
            #   $formCampo->nome = $request->input('nome');
            $formCampo->medico = $medico;
            $formCampo->CPF = Utils::extrairNum($request->input('cpf'));
            $formCampo->telefone = Utils::extrairNum($request->input('celular'));
            $formCampo->atualizado_em = now();
            /*  if ($nome_antigo !== $request->input('nome')) {
              $documentos = Documentos::where('paciente', $id)->where('deletado', 0)->get();
              print "Nome Alterado\n";
              foreach ($documentos as $documento) {
              $parts = explode("/", $documento->nome_diretorio);
              $novo = [];    // array com o novo diretorio
              $remover = []; // excluir partes depois do nome encontrado
              foreach ($parts as $index => $nome) {
              $novo[] = $nome == $nome_antigo ? $request->input('nome') : $nome; // substitui o nome do diretorio
              if ($nome == $nome_antigo) { // se for igual o nome remove tudo depois
              foreach ($parts as $i => $p) { // guarda o que deve excluir depois
              if ($i > $index) {
              $remover[] = $p;
              }
              }
              }
              }
              $novoDiretorio = str_replace(implode("/", $remover), '', implode("/", $novo));
              $novoDiretorioC = implode("/", $novo);
              $diretorio = str_replace(implode("/", $remover), "", $documento->nome_diretorio);
              $diretorioC = $documento->nome_diretorio;

              # $novo = str_replace($nome_antigo, $request->input('nome'), $documento->nome_diretorio);
              # $continuação = str_replace($nome_antigo, $request->input('nome'), $documento->nome_diretorio);
              # $novo = substr($novo, 0, strpos($novo, $request->input('nome'))) . "{$request->input('nome')}";
              print "Mudar Documento de \n $diretorioC > $diretorio \n {$novoDiretorioC} -> {$novoDiretorio} \n";

              $this->S3RenomarPasta($diretorio, $novoDiretorio);
              }
              }
              die(); */
            $formCampo->save();
        } else { // cadastrar no banco
            $add = [
                'criado_por' => Auth::user()->user_id,
                'grupo' => $med->grupo,
                'medico' => $medico,
                'nome' => $request->input('nome'),
                'CPF' => Utils::extrairNum($request->input('cpf')),
                'telefone' => Utils::extrairNum($request->input('celular')),
                "criado_em" => now()
            ];
            $formCampo = self::create($add);
        }

        if (!$formCampo)
            return response()->json(['error' => true, 'msg' => 'Não foi possível adicionar o paciente']);

        if ($id) {
            $msg = 'Paciente foi salvo com sucesso';
            Activity::novo("Edição do Paciente", "edit");
        } else {
            $msg = 'Paciente cadastrado com sucesso';
            Activity::novo("Cadastro do Paciente - {$request->input('nome')}");
        }
        return response()->json(['error' => false, 'msg' => $msg]);
    }

    // fazer um teste depois (erro ao copiar, talves limitações do bucket)
    public function S3RenomarPasta($oldFolderName, $newFolderName) {
        // Pegar as credenciais do .env
        $accessKeyId = env('AWS_ACCESS_KEY_ID');
        $secretAccessKey = env('AWS_SECRET_ACCESS_KEY');
        $region = env('AWS_DEFAULT_REGION');
        $version = 'latest'; // Exemplo: 'latest'
        // Crie uma instância do cliente S3 com as credenciais do .env
        $s3 = new S3Client([
            'region' => $region,
            'version' => $version,
            'credentials' => [
                'key' => $accessKeyId,
                'secret' => $secretAccessKey,
            ],
        ]);
        // Lista todos os objetos na pasta antiga
        $objects = $s3->listObjectsV2([
            'Bucket' => env('AWS_BUCKET'),
            'Prefix' => $oldFolderName,
        ]);
        // caso não tenha algum diretório
        if (!is_array($objects['Contents'])) {
            return "Nenhum Arquivo foi encontrado no diretório"; // validar se  tem algum documento no  banco, para caso mude o nome sem enviar nada
        }
        print "Arquivos encontrado no diretório {$oldFolderName}\n\n";
        // copia o arquivo para nova pasta e remove a antiga
        foreach ($objects['Contents'] as $object) {
            $oldKey = $object['Key'];
            $newKey = str_replace($oldFolderName, $newFolderName, $oldKey);
            print $oldKey;
            print "\n";
            print $newKey;
            print "\n";
            print "\n";
            if (!$s3->doesObjectExist(env('AWS_BUCKET'), $oldKey)) {
                print "Arquivo não existe";
                continue;
            }
            // copiar arquivo
            $s3->copyObject([
                'Bucket' => env('AWS_BUCKET'),
                'Key' => $newKey,
                'CopySource' => env('AWS_BUCKET') . "/" . $oldKey,
            ]);
            // apagar arquivo
            /* $s3->deleteObject([
              'Bucket' => env('AWS_BUCKET'),
              'Key' => $oldKey,
              ]); */
            /*  $s3->copyObject([
              'Bucket' => env('AWS_BUCKET'),
              'Key' => $newKey,
              'CopySource' => $oldKey,
              ]);
              $s3->deleteObject([
              'Bucket' => env('AWS_BUCKET'),
              'Key' => $oldKey,
              ]); */
        }

        // remover a pasta antiga

        return true;
    }

    /**
     * Remover Usuário
     * 
     * @param array $ids Ids do usuários
     * @return json
     */
    public static function remover($ids) {
        # se for root pode excluir qualquer uma
        if (Session::get('is_root'))
            self::whereIn('id', $ids)->update(['deletado' => 1]);

        # se não for root, tem uma segurança especial
        if (!Session::get('is_root'))
            self::whereIn('id', $ids)->where('grupo', Auth::user()->group)->update(['deletado' => 1]);

        if (count($ids) > 1) {
            $msg = 'Pacientes excluidos com sucesso';
            Activity::novo(sprintf("Remoção de %d Pacientes", count($ids)), "trash-alt");
        } else {
            $msg = 'Paciente excluido com sucesso ';
            Activity::novo("Remoção de um Paciente", "trash-alt");
        }
        return response()->json(['error' => false, 'msg' => $msg]);
    }

    public static function buscar($id) {
        $where = [
            'pacientes.deletado' => 0,
            'pacientes.id' => $id
        ];

        if (!Session::get('is_root'))
            $where['pacientes.grupo'] = Auth::user()->group;

        return Paciente::join('users as creator', 'pacientes.criado_por', '=', 'creator.user_id')
                        ->join('users as source', 'pacientes.grupo', '=', 'source.user_id')
                        ->join('medicos', 'pacientes.medico', '=', 'medicos.id')
                        ->join('users as clinica', 'medicos.grupo', '=', 'clinica.user_id')
                        ->leftJoin('users as updater', 'pacientes.atualizado_por', '=', 'updater.user_id')
                        ->where($where)
                        ->select(
                                'pacientes.*',
                                'medicos.nome AS nome_medico',
                                'creator.user_first_name as creator_first_name',
                                'creator.user_last_name as creator_last_name',
                                'updater.user_first_name as updater_first_name',
                                'updater.user_last_name as updater_last_name',
                                'clinica.user_first_name as updater_first_name',
                                'clinica.user_last_name as updater_last_name',
                        )
                        ->selectRaw('CONCAT(source.user_first_name, IFNULL(CONCAT(" ", source.user_last_name), "")) as source_fullname')
                        ->selectRaw('CONCAT(creator.user_first_name, IFNULL(CONCAT(" ", creator.user_last_name), "")) as creator_fullname')
                        ->selectRaw('CONCAT(updater.user_first_name, IFNULL(CONCAT(" ", updater.user_last_name), "")) as updater_fullname')
                        ->selectRaw('CONCAT(clinica.user_first_name, IFNULL(CONCAT(" ", clinica.user_last_name), "")) as clinica_fullname')
                        ->first();
    }
}
