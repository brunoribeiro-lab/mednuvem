<?php

namespace Tests\Unit;

use App\Models\User;
use App\Http\Controllers\CamposController;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Tests\TestCase;
use App\Models\Estado;
use Illuminate\Support\Facades\Session;

class EstadoTest extends TestCase {

    use DatabaseTransactions;

    /**
     * Testa a função salvar do EstadoController.
     *
     * @return void
     */
    public function testSalvarEstado() {
        // Crie um usuário simulado para autenticação
        $user = User::factory()->create();
        $user->update(['group' => $user->user_id]);
        Session::put('is_root', 1);
        // Simule a autenticação do usuário
        $this->actingAs($user);

        // Crie um objeto Request simulado
        $request = new Request([
            'estado' => 'Novo Estado',
        ]);

        // Crie uma instância do seu controlador
        $estadoController = new Estado();

        // Chame a função salvar com o ID nulo para simular um novo registro
        $response = $estadoController->salvar($request, null);
       # dd($response);
        // Verifique se a resposta é um JSON
      /*  $response->assertJson([
            'error' => false,
            'msg' => 'Estado de Entrega cadastrado com sucesso',
        ]);

        // Verifique se o status da resposta é 200 (OK)
        $response->assertStatus(200);
        if ($response->isServerError()) {
            $content = json_decode($response->getContent(), true);
            $this->fail("Erro na resposta JSON: " . json_last_error_msg() . PHP_EOL . json_encode($content, JSON_PRETTY_PRINT));
        }*/
    }
}
