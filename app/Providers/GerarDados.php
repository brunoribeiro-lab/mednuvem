<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;

class GerarDados extends ServiceProvider {

    private static $profissoes = array(
        array(
            'setor' => 'Tecnologia da informação',
            'funcoes' => array(
                'Desenvolvedor de software',
                'Analista de sistemas',
                'Gerente de projetos de TI',
                'Especialista em cibersegurança',
                'Analista de suporte',
                'Arquiteto de soluções',
                'Especialista em inteligência artificial',
                'Especialista em blockchain',
                'Designer de interface',
                'Especialista em big data'
            )
        ),
        array(
            'setor' => 'Saúde',
            'funcoes' => array(
                'Médico',
                'Enfermeiro',
                'Fisioterapeuta',
                'Psicólogo clínico',
                'Nutricionista',
                'Farmacêutico',
                'Terapeuta ocupacional',
                'Radiologista',
                'Cirurgião-dentista',
                'Fonoaudiólogo'
            )
        ),
        array(
            'setor' => 'Finanças',
            'funcoes' => array(
                'Analista financeiro',
                'Contador',
                'Especialista em investimentos',
                'Gerente de contas',
                'Especialista em finanças corporativas',
                'Analista de risco',
                'Especialista em planejamento tributário',
                'Especialista em auditoria',
                'Especialista em mercado de capitais',
                'Gerente de tesouraria'
            )
        ),
        array(
            'setor' => 'Vendas',
            'funcoes' => array(
                'Vendedor',
                'Gerente de vendas',
                'Especialista em marketing',
                'Representante comercial',
                'Executivo de contas',
                'Especialista em comércio eletrônico',
                'Analista de inteligência de mercado',
                'Especialista em vendas técnicas',
                'Consultor de vendas',
                'Especialista em gestão de canais'
            )
        ),
        array(
            'setor' => 'Direito',
            'funcoes' => array(
                'Advogado',
                'Juiz',
                'Promotor de justiça',
                'Defensor público',
                'Delegado de polícia',
                'Especialista em direito empresarial',
                'Especialista em direito tributário',
                'Especialista em direito do consumidor',
                'Especialista em direito do trabalho',
                'Especialista em direito internacional'
            )
        ),
        array(
            'setor' => 'Engenharia',
            'funcoes' => array(
                'Engenheiro mecânico',
                'Engenheiro elétrico',
                'Engenheiro civil',
                'Engenheiro de produção',
                'Engenheiro químico',
                'Engenheiro de petróleo',
                'Engenheiro de software',
                'Engenheiro ambiental',
                'Engenheiro de materiais',
                'Engenheiro de minas'
            )
        ),
        array(
            'setor' => 'Marketing',
            'funcoes' => array(
                'Especialista em marketing digital',
                'Especialista em SEO',
                'Gerente de mídias sociais',
                'Especialista em branding',
                'Especialista em publicidade',
                'Analista de pesquisa de mercado',
                'Gerente de marketing',
                'Especialista em eventos',
                'Especialista em relações públicas',
                'Especialista em marketing de conteúdo'
            )
        ),
        array(
            'setor' => 'Recursos Humanos',
            'funcoes' => array(
                'Gerente de RH',
                'Especialista em recrutamento e seleção',
                'Analista de treinamento e desenvolvimento',
                'Especialista em gestão de desempenho',
                'Especialista em remuneração e benefícios',
                'Especialista em gestão de talentos',
                'Especialista em clima organizacional',
                'Especialista em gestão de conflitos',
                'Especialista em gestão de mudanças',
                'Especialista em comunicação interna'
            )
        ),
        array(
            'setor' => 'Logística',
            'funcoes' => array(
                'Gerente de logística',
                'Especialista em cadeia de suprimentos',
                'Analista de planejamento e controle de estoques',
                'Especialista em transporte e distribuição',
                'Especialista em logística reversa',
                'Especialista em gestão de armazéns',
                'Especialista em comércio exterior',
                'Especialista em logística internacional',
                'Especialista em embalagens e unitização de cargas',
                'Especialista em logística de e-commerce'
            )
        ),
        array(
            'setor' => 'Administração',
            'funcoes' => array(
                'Gerente geral',
                'Gerente de operações',
                'Gerente administrativo-financeiro',
                'Gerente de qualidade',
                'Especialista em gestão estratégica',
                'Especialista em gestão de projetos',
                'Especialista em gestão de processos',
                'Especialista em gestão de inovação',
                'Especialista em gestão de custos',
                'Especialista em gestão de negócios'
            )
        ),
        array(
            'setor' => 'Consultoria',
            'funcoes' => array(
                'Consultor de negócios',
                'Consultor financeiro',
                'Consultor de tecnologia',
                'Consultor de recursos humanos',
                'Consultor de gestão',
                'Consultor de marketing',
                'Consultor de estratégia',
                'Consultor jurídico',
                'Consultor de sustentabilidade',
                'Consultor de inovação'
            )
        ),
        array(
            'setor' => 'Comunicação',
            'funcoes' => array(
                'Jornalista',
                'Assessor de imprensa',
                'Editor de conteúdo',
                'Produtor audiovisual',
                'Apresentador de TV',
                'Locutor de rádio',
                'Redator publicitário',
                'Planejador de mídia',
                'Especialista em comunicação visual',
                'Especialista em relações institucionais'
            )
        ),
        array(
            'setor' => 'Turismo',
            'funcoes' => array(
                'Agente de viagens',
                'Recepcionista de hotel',
                'Gerente de hotel',
                'Guia turístico',
                'Especialista em ecoturismo',
                'Especialista em turismo rural',
                'Especialista em turismo de aventura',
                'Especialista em turismo cultural',
                'Especialista em eventos turísticos',
                'Especialista em gestão de atrações turísticas'
            )
        ),
        array(
            'setor' => 'Saúde',
            'funcoes' => array(
                'Médico',
                'Enfermeiro',
                'Farmacêutico',
                'Nutricionista',
                'Fisioterapeuta',
                'Psicólogo',
                'Terapeuta ocupacional',
                'Radiologista',
                'Odontólogo',
                'Especialista em saúde pública'
            )
        ),
        array(
            'setor' => 'Educação',
            'funcoes' => array(
                'Professor',
                'Diretor escolar',
                'Coordenador pedagógico',
                'Especialista em tecnologia educacional',
                'Especialista em gestão escolar',
                'Especialista em educação especial',
                'Especialista em orientação educacional',
                'Especialista em treinamento corporativo',
                'Especialista em educação a distância',
                'Especialista em pesquisa educacional'
            )
        ),
        array(
            'setor' => 'Artes',
            'funcoes' => array(
                'Artista plástico',
                'Músico',
                'Ator',
                'Dançarino',
                'Escritor',
                'Fotógrafo',
                'Cenógrafo',
                'Figurinista',
                'Designer gráfico',
                'Ilustrador'
            )
        )
    );

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

    public static function gerar() {
        $dadosDaPessoa = json_decode(self::HTTP("https://www.4devs.com.br/ferramentas_online.php", [
                    "acao" => "gerar_pessoa",
                    "pontuacao" => "S",
                    "idade" => "0",
                    "cep_estado" => "MS",
                    "txt_qtde" => "1",
                    "cep_cidade" => ""
                ]), true);

        $dadosDaEmpresa = [];
        $dadosDaEmpresaTexto = self::HTTP("https://www.4devs.com.br/ferramentas_online.php", [
                    "acao" => "gerar_empresa",
                    "pontuacao" => "S",
                    "estado" => "MS",
                    "idade" => rand(1, 15)
        ]);
        try {
            if (preg_match('/id="nome" value="(.*?)"/', $dadosDaEmpresaTexto, $nomeEmpresa))
                $dadosDaEmpresa['nome'] = $nomeEmpresa[1];
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }

        if (preg_match('/id="cnpj" value="(.*?)"/', $dadosDaEmpresaTexto, $cnpj))
            $dadosDaEmpresa['cnpj'] = $cnpj[1];

        if (preg_match('/id="celular" value="(.*?)"/', $dadosDaEmpresaTexto, $celular))
            $dadosDaEmpresa['celular'] = $celular[1];

        $parts = explode("@", $dadosDaPessoa[0]['email']);
        $dadosDaPessoa[0]['email'] = "{$parts[0]}@gmail.com";

        $setor = self::$profissoes[rand(0, count(self::$profissoes) - 1)];
        shuffle($setor['funcoes']);
        $funcao = $setor['funcoes'][0];
        $dadosDaPessoa[0]['setor'] = $setor['setor'];
        $dadosDaPessoa[0]['funcao'] = $funcao;

        return [
            'pessoa' => $dadosDaPessoa[0],
            "empresa" => $dadosDaEmpresa,
            "multiplos" => 70
        ];
    }

    public static function HTTP($url, $param, $method = "POST", $headers = []) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($method == "POST" && count($param) > 0) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
        }
        if (count($headers) > 0)
            curl_setopt($ch, CURLOPT_HEADER, $headers);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if (!$response)
            return [];

        return $response;
    }

}
