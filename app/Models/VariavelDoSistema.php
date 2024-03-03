<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Providers\AuthServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Providers\Encryption;
use App\Models\Activity;
use App\Http\Controllers\DesenvolvedorController;
use App\Providers\MenusCollection;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class VariavelDoSistema extends Model {

    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'variaveis_do_sistema';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'clinica',
        'medico',
        'email_recuperar_senha',
        'email_dinamico_contato',
        'email_contato',
        'email_dinamico_balcao',
        'email_balcao',
        'email_dinamico_mensalista',
        'email_mensalista',
        'SGS_cadastro_empresa',
        'CARGO_EMPRESA',
        'CAPTCHAR_ENABLE',
        'CAPTCHAR_KEY',
        'CAPTCHAR_SECRET',
        'updated',
        'nome',
        'email_mode',
        'email_smtp',
        'email_port',
        'email_encrypt',
        'email_username',
        'email_password'
    ];
    public $timestamps = false;

    public static function __texto($message, $strip = false) {
        $systemVariables = VariavelDoSistema::get()->first();
        // Seu código existente
        foreach (self::prefix($systemVariables) as $prefix => $props) {
            $message = preg_replace("/{$prefix}/", $props['replace'], $message);
        }
        return !$strip ? $message : str_replace('"', "&#8220;", $message);
    }

    private static function prefix($systemVariables) {
        // Seu código existente
        return [
            ':nomeDoSite:' => [
                'title' => "Nome do Site",
                'replace' => $systemVariables->nome
            ]
        ];
    }

    public function salvar(Request $request) {
        if (!AuthServiceProvider::acao('ACCESS_FORM', ...array_values(MenusCollection::$menus['variaveis-do-sistema'])))
            return view('error.404SGS');

        // Validation rules
        $rules = [
            'nome_do_sistema' => 'required|min:2|max:250',
            'cargo_clinica' => 'required|int|exists:_ACCOUNT_TYPE,ID,DELETED,0,ROOT_ACCESS,0',
            'cargo_medico' => 'required|int|exists:_ACCOUNT_TYPE,ID,DELETED,0,ROOT_ACCESS,0',
            'email_dinamico_contato' => 'nullable|int|exists:dynamic_emails,id,deleted,0',
            'email_recuperar_senha' => 'nullable|int|exists:dynamic_emails,id,deleted,0',
            'email_dinamico_novo_cliente_p' => 'nullable|int|exists:dynamic_emails,id,deleted,0',
            'email_dinamico_novo_motorista_p' => 'nullable|int|exists:dynamic_emails,id,deleted,0',
            'email_dinamico_novo_cliente' => 'nullable|int|exists:dynamic_emails,id,deleted,0',
            'email_dinamico_novo_medico' => 'nullable|int|exists:dynamic_emails,id,deleted,0'
        ];

        if ($request->filled('email_dinamico_contato')) {
            $rules["email_contato"] = 'required|femail';
        }

        if ($request->filled('email_dinamico_novo_cliente_p')) {
            $rules["email_cliente"] = 'required|femail';
        }

        if ($request->filled('email_dinamico_novo_motorista_p')) {
            $rules["email_motorista"] = 'required|femail';
        }

        if ($request->input('captchar_enable')) {
            $rules["captchar_key"] = 'required|min:30|max:40';
            $rules["captchar_secret"] = 'required|min:30|max:40';
        }

        $messages = [
            '*.exists' => 'Campo Inválido.',
            '*.required' => 'Campo Obrigatório.',
            '*.femail' => 'Email Inválido.',
            '*.*.required' => 'Campo Obrigatório.',
            '*.*.horario' => 'Horário Inválido.',
            '*.*.feriado' => 'Data Inválida.',
            '*.*.integer' => 'Campo Inválido, apenas números.',
            '*.min' => 'O campo deve ter no mínimo :min caracteres',
            '*.max' => 'O campo deve ter no máximo :max caracteres',
        ];
        // validações de dias/horários
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        $data = [
            'nome' => $request->input('nome_do_sistema', NULL),
            'email_recuperar_senha' => $request->input('email_recuperar_senha', NULL),
            'email_dinamico_contato' => $request->input('email_dinamico_contato', NULL),
            'email_dinamico_novo_cliente_p' => $request->input('email_dinamico_novo_cliente_p', NULL),
            'email_dinamico_novo_motorista_p' => $request->input('email_dinamico_novo_motorista_p', NULL),
            'clinica' => $request->input('cargo_clinica', NULL),
            'medico' => $request->input('cargo_medico', NULL),
            'email_dinamico_novo_cliente' => $request->input('email_dinamico_novo_cliente', NULL),
            'email_dinamico_novo_medico' => $request->input('email_dinamico_novo_medico', NULL),
            'captchar_ativar' => $request->input('captchar_enable') ? 1 : 0,
            'updated' => now(),
        ];

        // Add emails
        if ($request->filled('email_dinamico_contato')) {
            $data['email_contato'] = $request->input('email_contato');
        }

        if ($request->filled('email_dinamico_novo_cliente_p')) {
            $data['email_cliente'] = $request->input('email_cliente');
        }

        if ($request->filled('email_dinamico_novo_motorista_p')) {
            $data['email_motorista'] = $request->input('email_motorista');
        }

        if ($request->input('captchar_enable')) {
            $data['captchar_key'] = $request->input('captchar_key');
            $data['captchar_secret'] = $request->input('captchar_secret');
        }
        # self::first()->save($data);
        DB::table('variaveis_do_sistema')->where('id', 1)->update($data);
        Activity::novo("Edição Configurações - Variáveis do Sistema", "edit");
        return response()->json(['error' => false, 'msg' => 'Informações de configurações - Variáveis do Sistema foram salvas com sucesso']);
    }

    /**
     * Salvar Acessos XML
     * 
     * @access public
     * @param Request $request
     * @return json
     * @throws ValidationException
     */
    public function salvarAcessoXML(Request $request) {
        if (!AuthServiceProvider::acao('ACCESS_FORM', ...array_values(MenusCollection::$menus['acesso-xml'])))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $rules = [
            'xml' => 'required|min:10|max:20000',
        ];

        $messages = [
            '*.required' => 'Campo obrigatório',
            '*.min' => 'O campo deve ter no mínimo :min caracteres',
            '*.max' => 'O campo deve ter no máximo :max caracteres',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $variaveisDoSistema = VariavelDoSistema::find(1);
        $variaveisDoSistema->acessos = $request->input('xml');
        $variaveisDoSistema->save();
        Activity::novo("Edição do XML de Acessos", "edit");
        return response()->json(['error' => false, 'msg' => 'Informações do XML de Acesso foi salvo com sucesso']);
    }

    public function salvarAuth(Request $request) {
        if (!AuthServiceProvider::acao('ACCESS_FORM', 5, 20, 4))
            exit('<div class="col-lg-12"><div class="text-center mb-5"><h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1><h4 class="text-uppercase">Desculpe, não encontramos essa página</h4></div></div>');

        $rules = [];
        if ($request->input('mode') == 'native')
            $rules = array_merge($rules, [
                'email' => 'required|email',
            ]);

        if ($request->input('mode') == 'smtp') {
            $rules = array_merge($rules, [
                'email_smtp' => 'required',
                'email' => 'required|email',
                'email_encryption' => 'required|in:SSL,TSL',
                'email_port' => 'required|integer|min:1|max:65535',
            ]);
            if ($request->filled('email_password')) {
                $rules['email_password'] = 'nullable|required|min:5|max:20';
            }
        }
        $messages = [
            '*.required' => 'Campo obrigatório',
            '*.email' => 'Campo Inválido',
            '*.min' => 'O campo deve ter no mínimo :min caracteres',
            '*.max' => 'O campo deve ter no máximo :max caracteres',
            '*.in' => 'Campo Inválido',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $variaveisDoSistema = VariavelDoSistema::find(1);
        // Update _SYSTEM_FEATURES
        $settings = ['email_mode' => $request->input('mode')];

        if (in_array($request->input('mode'), ["native", "smtp"])) {
            $settings['email_username'] = trim($request->input('email'));
        }

        if ($request->input('mode') === "smtp") {
            $settings['email_smtp'] = $request->input('email_smtp');
            $settings['email_port'] = $request->input('email_port');
            $settings['email_encrypt'] = $request->input('email_encryption');

            if ($request->filled('email_password')) {
                $settings['email_password'] = base64_encode(Encryption::encrypt($request->input('email_password')));
            }
        }
        $variaveisDoSistema->update($settings);
        Activity::novo("Edição de Autenticação de Email", "edit");
        return response()->json(['error' => false, 'msg' => 'Informações de Autenticação de Email foram salvas com sucesso']);
    }

    public function storeLogos(Request $request) {
        if (!AuthServiceProvider::acao('ACCESS_FORM', ...array_values(MenusCollection::$menus['logos'])))
            return view('error.404SGS');

        $rules = [];
        $messages = [
            '*.required' => 'Campo Obrigatório.',
            '*.min' => 'O campo deve ter no mínimo :min caracteres',
            '*.max' => 'O campo deve ter no máximo :max caracteres',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        $updateData = [];
        $banners = [
            'logo' => [288, 69],
            'logoR' => [120, 50],
            'icon' => [32, 32], // no resize
            'logoEmail' => [390, 133], // no resize
        ];

        foreach ($banners as $banner => $dimensions) {
            $file = $request->file($banner);

            if (!$file)
                continue;

            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            $uploadPath = "assets/uploads/theme";
            $file->move($uploadPath, $fileName);
            $filename = basename($fileName);
            switch ($banner) {
                case 'logoR':
                    $updateData['logo'] = $filename;
                    break;
                case 'logo':
                    $updateData['logo_dark'] = $filename;
                    break;
                case 'icon':
                    $updateData['FAVOICON'] = $filename;
                    break;
                case 'logoEmail':
                    $updateData['logo_email'] = $filename;
                    break;
            }
        }

        VariavelDoSistema::where('ID', 1)->update($updateData);
        // logo do menu
        $systemVariables = VariavelDoSistema::get()->first();
        $sgs_logo = [
            'light' => asset(sprintf("assets/uploads/theme/%s", $systemVariables->logo)),
            'dark' => asset(sprintf("assets/uploads/theme/%s", $systemVariables->logo_dark))
        ];

        Session::put('SGS_logo', $sgs_logo['light']);
        Session::put('SGS_logo_dark', $sgs_logo['dark']);
        Activity::novo("Edição Configurações - Edição de Configurações Globais", "edit");
        return response()->json(['error' => false, 'msg' => 'As Informações de Configurações Gerais foram salva com sucesso']);
    }
}
