<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Carbon;
use App\Models\FormContato;
use App\Models\VariavelDoSistema;

class Captchar extends ServiceProvider {

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

    public static function contatoForm() {
        $ip = request()->ip();  // Pegar o IP do cliente

        $data = FormContato::where('IP', $ip)
                ->where('deletado', 0)
                ->where('cadastrado', '>', Carbon::now()->subHours(1))  // Mais legível do que strtotime
                ->where('cadastrado', '<=', Carbon::now())
                ->limit(2)
                ->get();

        return $data->count() > 0;
    }

    public static function reCaptchar($index = 'contato') {
        switch ($index) {
            case 'contato':
                $intervalo = self::contatoForm();
                break;
            default:
                return abort(response()->json(['error' => true, 'msg' => 'Index inválida']));
        }

        $config = VariavelDoSistema::first();  // Supondo que há apenas uma entrada

        if ($config->captchar_ativar && $intervalo) {
            $recaptchaResponse = request()->input('g-recaptcha-response');  // Pega input

            if (!$recaptchaResponse)
                return abort(response()->json(['error' => true, 'msg' => 'Não foi possível consultar se você é um robô']));

            $data = [
                'secret' => $config->CAPTCHAR_SECRET,
                'response' => $recaptchaResponse,
                'remoteip' => request()->ip()  // Pega IP do cliente
            ];

            $response = Http::post("https://www.google.com/recaptcha/api/siteverify", $data);

            if (!$response->successful()) {
                return abort(response()->json(['error' => true, 'msg' => 'Não foi possível consultar se você é um robô']));
            }

            $json = $response->json();
            if (!$json['success'])
                return abort(response()->json(['error' => true, 'msg' => 'Por favor marque a opção "Não sou um robô"']));
        }
    }
}
