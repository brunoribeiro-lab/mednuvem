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
use Illuminate\Support\Facades\Storage;

class Documentos extends Model {

    use HasFactory;

    private static $menu = [
        "menu" => 3,
        "submenu" => NULL,
        "subsubmenu" => NULL,
    ];
    protected $table = 'documentos';
    protected $fillable = [
        'deletado',
        'paciente',
        'criado_por',
        'atualizado_por',
        'tipo',
        'exame',
        'nome',
        'descricao',
        'nome_diretorio',
        'tamanho',
        'mime',
        'data',
        'criado_as',
        'atualizado_as'
    ];
    public $timestamps = false;
    private static $actions = []; // ações do CRUD

    public function salvar(Request $request, $nome, $nome_diretorio, $tamanho, $mime) {
        $add = [
            'criado_por' => Auth::user()->user_id,
            'paciente' => $request->input('paciente'),
            'tipo' => $request->input('tipo_documento'),
            'exame' => $request->input('tipo_documento') == 1 ? $request->input('exame') : NULL,
            'nome' => $nome,
            'descricao' => $request->input('description'),
            'nome_diretorio' => $nome_diretorio,
            'tamanho' => $tamanho,
            'mime' => $mime,
            'data' => date("Y-m-d", strtotime($request->input('data'))),
            "criado_as" => now()
        ];
        $formCampo = self::create($add);
    }

    /**
     * Criar a SQL de listar 
     * 
     * @static
     * @access private
     * @param Object $request
     * @param boolean $todos Se true Ignora o limite
     * @return array
     */
    private static function sql_listar($request, $id, $todos = false) {
        $sortMap = [
            2 => "id",
            3 => "nome",
            4 => "CPF",
            5 => "telefone",
            6 => "criado_em",
        ];

        $sSearch = trim($request->input('sSearch'));
        $query = self::join('pacientes', function ($join) {
                    $join->on('pacientes.id', '=', 'documentos.paciente')
                    ->where('pacientes.deletado', 0);
                })
                ->join('tipo_de_documento', function ($join) {
                    $join->on('documentos.tipo', '=', 'tipo_de_documento.id')
                    ->where('tipo_de_documento.deletado', 0);
                })
                ->where('documentos.deletado', 0)
                ->where('documentos.paciente', $id);

        # Se não for root, mostra apenas pacientes do grupo do usuário
        if (!Session::get('is_root')) {
            $query->where('pacientes.grupo', Auth::user()->group);
        }

        if (!empty($sSearch)) {
            $query->where(function ($q) use ($sSearch) {
                $q
                        ->orWhere('pacientes.nome', 'like', "%$sSearch%");
                // pesquisar pelo código
                if (preg_match('/DOC/i', $sSearch))
                    $q->orWhere(function ($query) use ($sSearch) {
                        $query->where('documentos.id', 'like', '%' . (int) Utils::extrairNum($sSearch) . '%');
                    });
            });
        }
        // Filtro Personalizado 
        if ($request->input('tipo') !== "*") {
            $query->where('documentos.tipo', $request->input('tipo'));
        }
        $fdate = "documentos.data";
        switch ($request->input('date')) {
            case 'today':
                $query->whereDate($fdate, '=', date("Y-m-d", time()));
                break;
            case 'last_15':
                $query->whereBetween($fdate, [date("Y-m-d", strtotime("-15 days")), date("Y-m-d")]);
                break;
            case 'last_30':
                $query->whereBetween($fdate, [date("Y-m-d", strtotime("-30 days")), date("Y-m-d")]);
                break;
            case 'this_year':
                $query->whereYear($fdate, date("Y", time()));
                break;
            case 'custom_date':
                $date = $request->input('custom');
                $d = strtotime($date);
                if (!$d)
                    break;

                $query->whereDate($fdate, date("Y-m-d", $d));
                break;
            case 'custom_ranger':
                $start = $request->input('start');
                $end = $request->input('end');
                $ds = strtotime($start);
                $de = strtotime($end);
                if (!$ds or !$de)
                    break;

                $query->whereBetween($fdate, [date("Y-m-d", $ds), date("Y-m-d", $de)]);
                break;
            default:
                break;
        }

        $query->select([
            'documentos.*',
            'tipo_de_documento.nome AS tipo_documento'
        ]);
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
    public static function datatable(Request $request, $id) {
        $clientes = self::sql_listar($request, $id);
        $total = count(self::sql_listar($request, $id, true));
        self::$actions = AuthServiceProvider::acoes(...array_values(self::$menu));
        $row = array();
        foreach ($clientes as $cliente) {
            $nice = Carbon::parse($cliente->criado_em)->diffForHumans();
            $updated = Utils::dataCompletaPTBR($cliente->criado_em);
            $dados = [
                '',
                sprintf("DOC%s", str_pad($cliente->id, 6, '0', STR_PAD_LEFT)),
                strlen($cliente->tipo_documento) >= 50 ? sprintf("<abbr title='%s'>%s</abbr>", $cliente->tipo_documento, Str::limit($cliente->tipo_documento, 50)) : $cliente->tipo_documento,
                Utils::formatarBytes($cliente->tamanho),
                strlen($cliente->nome) >= 50 ? sprintf("<abbr title='%s'>%s</abbr>", $cliente->nome, Str::limit($cliente->nome, 50)) : $cliente->nome,
                date('d/m/Y', strtotime($cliente->data)),
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

    public static function actionButton($query) {
        $buttons[] = "<button class=\"btn btn-primary goDownload\" type=\"button\"  data-id=\"{$query['id']}\" data-filename=\"{$query['nome']}\" title=\"Baixar Documento\"><i class=\"fas fa-cloud-download\"></i> </button>";
        return implode("\n", $buttons);
    }

    public static function baixar(string $id) {
        if (Session::get('is_root')) {
            $documento = self::where('id', $id)->where('deletado', 0)->first();
        } else { // não permitir que usuários sem permissões do paciente baixe
            $documento = self::join('pacientes', function ($join) {
                        $join->on('pacientes.id', '=', 'documentos.paciente')
                        ->where('pacientes.deletado', 0);
                    })
                    ->where('pacientes.grupo', Auth::user()->group)
                    ->where('documentos.id', $id)
                    ->where('documentos.deletado', 0)
                    ->first();
        }

        if (!$documento)
            die("Documento não encontrado");

        if (!Storage::disk('s3')->exists($documento->nome_diretorio))
            return response()->json(['error' => true, 'msg' => "Arquivo não existe no S3"]);

        // URL
        $urlAssinado = Storage::disk('s3')->temporaryUrl(
                $documento->nome_diretorio,
                now()->addMinutes(5) //  tempo de expiração da URL
        );
        return response()->json(['error' => false, 'url' => $urlAssinado, 'fileName' => $documento->nome]);

        // blob bug PDF em branco
        return Storage::disk('s3')->download($documento->nome_diretorio);
    }

    public function enviar(Request $request) {
        $rules = [
            'tipo_documento' => [
                'required',
                'integer',
                'min:0',
                'exists_in:tipo_de_documento,id,deletado,0'
            ],
            'description' => [
                'nullable',
                'min:3',
                'max:500'
            ],
            'data' => [
                'required',
                'data'
            ],
            'document' => [
                'required',
                'file',
                'mimes:pdf',
                'max:10240'  // 10MB em kilobytes
            ]
        ];
        // se for igual a exame
        if ($request->input('tipo_documento') == 1) {
            $rules['exame'] = [
                'required',
                'integer',
                'min:0',
                "exists_in:exames,id,deletado,0"
            ];
        } else {
            $rules['nome'] = [
                'required',
                'string',
                'min:3',
                'max:250',
            ];
        }
        $messages = [
            '*.required' => 'Campo obrigatório',
            '*.min' => 'O campo deve ter no mínimo :min caracteres',
            '*.max' => 'O campo deve ter no máximo :max caracteres',
            '*.integer' => 'Campo inválido, apenas números',
            'document.mimes' => 'O arquivo deve ser um documento PDF',
            'document.max' => 'O tamanho máximo do arquivo é 10MB',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails())
            throw new ValidationException($validator);

        // verifica se o paciente existe
        if (!$id = $request->input('paciente'))
            return response()->json(['error' => true, 'msg' => 'Paciente não encontrado']);

        if (!$paciente = Paciente::buscar($id))
            return response()->json(['error' => true, 'msg' => 'Paciente não encontrado']);

        // se for igual a exame
        if ($request->input('tipo_documento') == 1) {
            $ex = Exame::where('deletado', 0)
                    ->where('id', $request->input('exame'))
                    ->first();
            $nome_arquivo = $ex->nome;
        } else {
            $nome_arquivo = $request->input('nome');
        }
        $date = date("d-m-Y", strtotime($request->input('data')));
        $file = $request->file('document');
        $categoria = TiposDocumentos::where('deletado', 0)->where('id', $request->input('tipo_documento'))->first();
        $fileName = "{$nome_arquivo}.{$file->getClientOriginalExtension()}";
        $diretorio = "{$paciente->clinica_fullname}/{$paciente->nome_medico}/{$paciente->nome}/{$categoria->nome}/{$date}";
        $nome_diretorio = "{$diretorio}/{$fileName}";
        // verificar se o nome existe, caso exista adicionar um auto incremento no nome
        $incremento = 1;
        while (true) {
            if (!Documentos::where('nome_diretorio', $nome_diretorio)->where('deletado', 0)->first())
                break;

            // caso exista, incrementa o nome do arquivo com um número
            $incremento++;
            $fileName = "{$nome_arquivo}{$incremento}.{$file->getClientOriginalExtension()}";
            $nome_diretorio = "{$diretorio}/{$fileName}";
        }

        if (!$path = Storage::disk('s3')->putFileAs($diretorio, $file, $fileName))
            return response()->json(['error' => true, 'msg' => "Ocorreu um erro ao fazer o Upload para o S3 {$nome_diretorio}"]);


        //cadastrar no banco de dados
        $this->salvar($request, $fileName, $nome_diretorio, $file->getSize(), $file->getClientMimeType());
        return response()->json(['error' => false, 'msg' => 'O Documento foi salvo']);
    }
}
