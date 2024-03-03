<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemMenu;
use App\Models\Activity;
use App\Models\Users;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PerfilController extends Controller {

    /**
     * Display a listing of the resource.
     */
    public function index() {
        $menu = SystemMenu::__construir(); // carrega o menu do SGS
        $data = [// data usada nessa página
            'default' => [
                "menu" => NULL,
                'submenu' => NULL,
                "subsubmenu" => NULL
            ],
            'identificador' => [
                'padrão' => SystemMenu::identificador(),
                'add' => SystemMenu::identificador(true)
            ],
        ];
        return view('perfil')
                        ->with('data', $data)
                        ->with('menu', $menu);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request) {
        // Validação
        $rules = [
            'firstname' => 'required|min:3|max:20',
            'lname' => 'nullable|min:3|max:20',
            'changePassword' => 'nullable',
            'password' => 'nullable|min:6|max:18|confirmed',
        ];
        $messages = [
            '*.required' => 'Campo Obrigatório.',
            '*.min' => 'O campo deve ter no mínimo :min caracteres',
            '*.max' => 'O campo deve ter no máximo :max caracteres',
            'password.confirmed' => 'As Senhas digitadas não coincidem',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        // Upload da capa (mantenha a função como está)
        $file = $request->file('pic');
        if ($file) {
            $extension = $file->getClientOriginalExtension(); // Obtenha a extensão do arquivo original
            $fileName = uniqid() . '.' . $extension; // Gere um nome de arquivo único com a extensão original
            $file->move(public_path('avatars'), $fileName); // Mova o arquivo para a pasta pública 'avatars' com o nome aleatório
            $avatar = $fileName; // Use o nome do arquivo para fins de referência
        }

        // Atualização
        $user = Users::find(Auth::user()->user_id);
        if (!$user)
            return response()->json(['error' => true, 'msg' => 'Usuário não encontrado']);


        $user->user_first_name = $request->input('firstname');
        $user->user_last_name = $request->input('lname', null);

        if (!empty($request->input('changePassword')))
            $user->user_password_hash = Hash::make($request->input('password'));

        if ($file)
            $user->user_has_avatar = $avatar;

        if (!$user->save())
            return response()->json(['error' => true, 'msg' => 'Não foi possível alterar o Perfil']);

        Auth::login($user); // mudar a variavel da sessão atualizada
        Activity::novo("Edição de Perfil", "edit");
        return response()->json(['error' => false, 'msg' => 'Perfil alterado com sucesso']);
    }
}
