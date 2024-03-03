<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use DateTime;

class Validar extends ServiceProvider {

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

    /**
     * Validação de Telefone
     * 
     * @static
     * @access public
     * @param string $telefone Telefone formatado ou não
     * @return boolean
     */
    public static function Telefone($telefone) {
        // Remove caracteres inválidos do telefone
        $telefone = preg_replace('/[^\d]/', '', $telefone);
        // Verifica se o telefone possui 10 ou 11 dígitos (com ou sem DDD)
        if (strlen($telefone) !== 10 && strlen($telefone) !== 11)
            return false;

        // Verifica se todos os caracteres são iguais (ex: 0000000000)
        if (preg_match('/^(\d)\1+$/', $telefone))
            return false;

        // Verifica se o primeiro dígito é válido (0 a 9)
        if ($telefone[0] < '0' || $telefone[0] > '9')
            return false;

        // Verifica se os 2 primeiros números é um DDD válido do Brasil
        $dddsValidos = array('11', '12', '13', '14', '15', '16', '17', '18', '19', '21', '22', '24', '27', '28', '31', '32', '33', '34', '35', '37', '38', '41', '42', '43', '44', '45', '46', '47', '48', '49', '51', '53', '54', '55', '61', '62', '63', '64', '65', '66', '67', '68', '69', '71', '73', '74', '75', '77', '79', '81', '82', '83', '84', '85', '86', '87', '88', '89', '91', '92', '93', '94', '95', '96', '97', '98', '99');
        $ddd = substr($telefone, 0, 2);
        if (!in_array($ddd, $dddsValidos))
            return false;

        // Telefone válido
        return true;
    }

    public static function CPF($cpf = '') {
        // Extrai somente os números
        $cpf = preg_replace('/[^0-9]/is', '', $cpf);
        // Verifica se foi informado todos os digitos corretamente
        if (strlen($cpf) != 11)
            return false;
        // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
        if (preg_match('/(\d)\1{10}/', $cpf))
            return false;
        // Faz o calculo para validar o CPF
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }

    public static function CNPJ($cnpj) {
        $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);
        // Valida tamanho
        if (strlen($cnpj) != 14)
            return false;

        // Verifica se todos os digitos são iguais
        if (preg_match('/(\d)\1{13}/', $cnpj))
            return false;

        // Valida primeiro dígito verificador
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;
        if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto))
            return false;

        // Valida segundo dígito verificador
        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;
        return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
    }

    public static function data($data) {
        $dataSplit = explode('-', $data);
        $dia = intval($dataSplit[2]);
        $mes = intval($dataSplit[1]);
        $ano = intval($dataSplit[0]);
        $dataObj = DateTime::createFromFormat('Y-m-d', $data);
        return (
                $dataObj !== false &&
                (int) $dataObj->format('Y') === $ano &&
                (int) $dataObj->format('m') === $mes &&
                (int) $dataObj->format('d') === $dia
                );
    }

    /**
     * Validação de Data de nascimento
     * 
     * @static
     * @access public
     * @param date $dataNascimento Data de nascimento
     * @param int $idadeMinima Idade minima para validar
     * @return boolean
     */
    public static function nascimento($dataNascimento, $idadeMinima = 14) {
        $dataNascimentoObj = DateTime::createFromFormat('Y-m-d', $dataNascimento);
        $hoje = new DateTime();
        $idadeAtual = $hoje->diff($dataNascimentoObj)->y;
        return $idadeAtual >= $idadeMinima;
    }

    /**
     * Validação de RG
     * 
     * @static
     * @access public
     * @param string $rg RG com máscara
     * @return boolean
     */
    public static function RG($rg) {
        $rg_digits = preg_replace('/\D/', '', $rg);
        if ($rg_digits == '')
            return false;

        // Verifica se o RG é diferente de 9 dígitos
        if (strlen($rg_digits) !== 9)
            return false;

        // Verifica se todos os caracteres são iguais (ex: 99.999.999-9)
        if (preg_match('/^(\d)\1+$/', $rg_digits))
            return false;

        $regex = '/^[0-9]{2}\.[0-9]{3}\.[0-9]{3}\-[0-9]{1}$/';
        return preg_match($regex, $rg);
    }

    /**
     * Validação de Email
     * 
     * @static
     * @access public
     * @param string $email
     * @return boolean
     */
    public static function email($email) {
        // Remove espaços extras
        $email = trim($email);

        // Verifica se o e-mail está em um formato geralmente aceito usando uma expressão regular
        if (!preg_match('/^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
            return false;
        }

        // Verifica se o e-mail é válido usando filter_var
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Verifica se o domínio do e-mail é válido
        list(, $domain) = explode('@', $email);
        if (!checkdnsrr($domain, 'MX')) {
            return false;
        }

        return true;
    }

    /**
     * Validar UF
     * 
     * @param string $uf
     * @return boolean 
     */
    public static function UF($uf) {
        if (strlen($uf) !== 2)
            return false;

        $ufsValidas = array(
            'AC', 'AL', 'AM', 'AP', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA',
            'MG', 'MS', 'MT', 'PA', 'PB', 'PE', 'PI', 'PR', 'RJ', 'RN',
            'RO', 'RR', 'RS', 'SC', 'SE', 'SP', 'TO'
        );

        return in_array(strtoupper($uf), $ufsValidas);
    }

    /**
     * Validação de Nome de empresa
     * 
     * @static
     * @access public
     * @param string $nomeEmpresa
     * @return boolean
     */
    public static function nomeEmpresa($nomeEmpresa) {
        // Remove espaços extras do início e do fim do nome da empresa
        $nomeEmpresa = trim($nomeEmpresa);

        // Verifica se o nome da empresa foi informado
        if (empty($nomeEmpresa))
            return false;

        // Verifica se o nome da empresa tem pelo menos 2 caracteres
        if (strlen($nomeEmpresa) < 2)
            return false;

        // verifica se o nome tem uma sequência de caracteres
        if (preg_match('/^([^\s])\1*$/', $nomeEmpresa))
            return false;

        return true;
    }

    /**
     * Validar nome completo
     * 
     * @static
     * @access public
     * @param string $nomeCompleto
     * @return boolean
     */
    public static function nomeCompleto($nomeCompleto) {
        // Verifica se o nome completo foi informado
        if (!$nomeCompleto)
            return false;

        // Remove espaços extras do início e do fim do nome completo
        $nomeCompleto = trim($nomeCompleto);

        // Verifica se o nome completo é composto por duas ou mais palavras
        $palavras = explode(' ', $nomeCompleto);
        if (count($palavras) < 2)
            return false;

        // Verifica se cada palavra do nome completo tem pelo menos 2 caracteres
        foreach ($palavras as $palavra) {
            if (strlen($palavra) < 2)
                return false;
        }

        // Verifica se o nome completo contém apenas letras e espaços
        $regex = "/^[\p{L}\p{M}\s,'-]+$/u";
        if (!preg_match($regex, $nomeCompleto))
            return false;

        // Se todas as validações passaram, o nome completo é considerado válido
        return true;
    }

    /**
     * Validar CEP
     * 
     * @static
     * @access public
     * @param mixed $cep
     * @return boolean
     */
    public static function CEP($cep) {
        // Remove qualquer caractere não numérico do CEP
        $cep = preg_replace('/\D/', '', $cep);

        // Verifica se o número de dígitos é exatamente 8
        return strlen($cep) == 8 && is_numeric($cep);
    }
}
