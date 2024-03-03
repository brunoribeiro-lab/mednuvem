<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider {

    /**
     * Register any application services.
     */
    public function register(): void {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        Carbon::setLocale(str_replace('-', '_', config('app.locale')));
        // Validação de registro existe na tabela
        Validator::extend('exists_in', function ($attribute, $value, $parameters, $validator) {
            $table = $parameters[0];
            $column = isset($parameters[1]) ? $parameters[1] : $attribute;
            $ignoreDeleted = isset($parameters[2]) && $parameters[2] === 'ignore_deleted';

            $query = DB::table($table)->where($column, $value);
            if (!$ignoreDeleted) {
                $deletado_coluna = in_array('deleted', $parameters) ? 'deleted' : 'deletado';
                $query->where($deletado_coluna, 0);
            }

            return $query->count() > 0;
        });
        // Validar Nome da empresa
        Validator::extend('nomeEmpresa', function ($attribute, $value, $parameters, $validator) {
            return Validar::nomeEmpresa($value);
        });
        // validação de CNPJ inválido
        Validator::extend('cnpj', function ($attribute, $value, $parameters, $validator) {
            $onlyNumbers = preg_replace('/\D/', '', $value);
            return Validar::CNPJ($onlyNumbers);
        });
        // validação de CNPJ inválido
        Validator::extend('cpf', function ($attribute, $value, $parameters, $validator) {
            $onlyNumbers = preg_replace('/\D/', '', $value);
            return Validar::CPF($onlyNumbers);
        });
        // validação de RG inválido
        Validator::extend('rg', function ($attribute, $value, $parameters, $validator) {
            return Validar::RG($value);
        });
        // validação de CEP inválido
        Validator::extend('cep', function ($attribute, $value, $parameters, $validator) {
            $onlyNumbers = preg_replace('/\D/', '', $value);
            return Validar::CEP($onlyNumbers);
        });
        // validação de Email inválido
        Validator::extend('femail', function ($attribute, $value, $parameters, $validator) {
            return Validar::email($value);
        });
        // validação de UF inválido
        Validator::extend('uf', function ($attribute, $value, $parameters, $validator) {
            return Validar::UF($value);
        });
        // validação de Telefone inválido
        Validator::extend('telefone', function ($attribute, $value, $parameters, $validator) {
            $onlyNumbers = preg_replace('/\D/', '', $value);
            return Validar::Telefone($onlyNumbers);
        });
        // validação de obrigatório, mas extrai os números
        Validator::extend('only_numbers', function ($attribute, $value, $parameters, $validator) {
            $onlyNumbers = preg_replace('/\D/', '', $value);
            return empty($onlyNumbers) ? false : true;
        });
        // validação de data válida
        Validator::extend('data', function ($attribute, $value, $parameters, $validator) {
            return Validar::data($value);
        });
        // validação de nome completo
        Validator::extend('nomeCompleto', function ($attribute, $value, $parameters, $validator) {
            return Validar::nomeCompleto($value);
        });
        // validação de data de Nascimento
        Validator::extend('dataNasc', function ($attribute, $value, $parameters, $validator) {
            return Validar::nascimento($value);
        });
        // validação numeric
        Validator::extend('numeric', function ($attribute, $value, $parameters, $validator) {
            return is_numeric($value);
        });
        Validator::extend('horario', function ($attribute, $value, $parameters, $validator) {
            return !!strtotime($value);
        });
        Validator::extend('feriado', function ($attribute, $value, $parameters, $validator) {
            $date = \DateTime::createFromFormat('d/m', $value);
            if (!$date)
                return false;

            // Ajuste o ano da data para o ano atual
            $date->setDate(date("Y"), $date->format('m'), $date->format('d'));

            // Verifique se a data é válida (isso captura, por exemplo, datas como 30/02)
            return $date->format('d/m') === $value;
        });
    }
}
