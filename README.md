<div id="top"></div>
<!--
*** Thanks for checking out the Best-README-Template. If you have a suggestion
*** that would make this better, please fork the repo and create a pull request
*** or simply open an issue with the tag "enhancement".
*** Don't forget to give the project a star!
*** Thanks again! Now go create something AMAZING! :D
-->



<!-- PROJECT SHIELDS -->
<!--
*** I'm using markdown "reference style" links for readability.
*** Reference links are enclosed in brackets [ ] instead of parentheses ( ).
*** See the bottom of this document for the declaration of the reference variables
*** for contributors-url, forks-url, etc. This is an optional, concise syntax you may use.
*** https://www.markdownguide.org/basic-syntax/#reference-style-links
-->
[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![MIT License][license-shield]][license-url]
[![LinkedIn][linkedin-shield]][linkedin-url]



<!-- PROJECT LOGO -->
<br />
<div align="center">

  [![Conesul Gestão no abate de terceiros][logo]](https://nuvemmed.shop/) 

  <h3 align="center">MedNuvem - Gestão Cloud de documentos médicos</h3>

  <p align="center">
    MedNuvem - Gestão Cloud de documentos médicos
    <br />
    <a href="https://github.com/mednuvem/plataforma"><strong>Explore a documentação »</strong></a>
    <br />
    <br />
    <a href="https://nuvemmed.shop/">Demostração Online</a>
    ·
    <a href="https://github.com/mednuvem/plataforma/issues">Reportar Bug</a>
    ·
    <a href="https://github.com/mednuvem/plataforma/issues">Solicitar  Feature</a>
  </p>
</div>



<!-- TABLE OF CONTENTS -->
<details>
  <summary>Sumário</summary>
  <ol>
    <li>
      <a href="#sobre-o-projeto">Sobre O Projeto</a>
      <ul>
        <li><a href="#construído-com">Construído Com</a></li>
      </ul>
    </li>
    <li>
      <a href="#começando">Começando</a>
      <ul>
        <li><a href="#pré-requisitos">Pré-requisitos</a></li>
        <li><a href="#instalação-docker">Instalação</a></li>
      </ul>
    </li>
    <li><a href="#roadmap">Roadmap</a></li>
    <li><a href="#contribuindo">Contribuindo</a></li>
    <li><a href="#licença">Licença</a></li>
    <li><a href="#contato">Contato</a></li>
    <li><a href="#agradecimentos">Agradecimentos</a></li>
  </ol>
</details>



<!-- ABOUT THE PROJECT -->
## Sobre o Projeto

MedNuvem é um sistema médico para gerencimento de arquivos de documento

Caracteristicas:
* Médicos
* Pacientes
* Exames
* Integração com AWS S3

<p align="right">(<a href="#top">voltar ao topo</a>)</p>



### Construído Com

Abaixo está as tecnologias usadas no desenvolvimento desse projeto.

* [Jquery](https://jquery.com/) 
* [Bootstrap](https://getbootstrap.com)
* [Mysql](https://www.mysql.com/)
* [Laravel](https://laravel.com/)

<p align="right">(<a href="#top">voltar ao topo</a>)</p>



<!-- GETTING STARTED -->
## Começando

Veja abaixo todos os passos para fazer a instalação corretamente da aplicação.

### Pré-requisitos

* **PHP 8.2**
* **MariaDB 5+** database
* **Apache, Nginx** Servidor Web

### Instalação Docker

Antes de começar será necessário instalar o [Docker](https://www.docker.com/) no seu servidor/máquina.
com o docker instalado siga os próximos passos abaixo.

1. Clone o repositório
   ```sh
   git clone https://github.com/mednuvem/plataforma.git
   ```
2. Abra o arquivo `.env` e substitua pelas informações corretas:
   ```js
   APP_URL = 'url_do_backend';      // com http ou https ex: http://mednuvem.com
   APP_PORT = 82;                   // ex: 80 
   APP_DEBUG = 0;                   // deixe 2 para ambiente de dev ou 0 para produção
   AWS_ACCESS_KEY_ID=               // access key id do S3
   AWS_SECRET_ACCESS_KEY=           // secret key id do S3
   AWS_DEFAULT_REGION=              // região do S3
   AWS_BUCKET=nome-bucket           // nome do bucket ex: mednuvem-prod
   ```
3. Executando o Docker Composer
   ```sh
   docker-compose up -d --build frigo
   ```
4. Importando o Banco de Dados
   ```sh
   docker-compose run --rm artisan migrate
   ```
Portas expostas detalhadas para o .env de exemplo
 
- **nginx** - `:82`
- **mysql** - `:3308`
- **php** - `:9002`  
- **MAILHOG** - `:1027`  
- **MAILHOG 2** - `:8027`  
- **REDIS** - `:6379`  

### Instalação Manual

Para fazer a instalação da aplicação siga todos os passos abaixos.


1. Clone o repositório
   ```sh
   git clone https://github.com/mednuvem/plataforma.git
   ```
2. Baixe o [`composer.phar`](https://getcomposer.org/composer.phar) executável ou use o instalador.
    ``` sh
    curl -sS https://getcomposer.org/installer | php
    ```
3. Execute o Composer: 
    ``` sh
    php composer.phar update
    ``` 
4. Instalando Tarefas Cron
   ```sh
   php artisan migrate 
   ```
5. Instalando Tarefas Cron
   ```sh
   sudo crontab -e
   * * * * *	/usr/bin/php /var/www/artisan schedule:run >> /dev/null 2>&1
   ```

<p align="right">(<a href="#top">voltar ao topo</a>)</p>

<!-- ROADMAP -->
## Roadmap
 
- [X] Select Clinica para Usuários Roots em Configurações > Médico > Setor
- [X] Select Médico para Usuários Roots em Pacientes
- [X] Select Clinica para Usuários Roots em Médicos
- [X] Ao alterar o nome da Clinica Mudar os uploads no banco de dados e no S3
- [X] Ao alterar o nome do Médico Mudar os uploads no banco de dados e no S3
- [X] Ao alterar o nome do Pacientes Mudar os uploads no banco de dados e no S3
- [X] Finalizar Disparos de Email
- [X] Cadastrar email ao criar cadastro
- [X] Recuperar conta
- [X] Pente Fino no site
- [ ] Fixar bug logar 2x com mesmo usuário

Veja as [issues abertas](https://github.com/mednuvem/plataforma/issues) para uma lista completa de funcionalidades propostas (e problemas conhecidos).
<p align="right">(<a href="#top">voltar ao topo</a>)</p>



<!-- CONTRIBUTING -->
## Contribuindo

As contribuições são o que tornam a comunidade de código aberto um lugar incrível para aprender, inspirar e criar. Qualquer contribuição que você fizer será **bem recebida**.

Se você tiver uma sugestão que tornaria isso melhor, faça um fork do repositório e crie um pull request. Você também pode simplesmente abrir uma issue com a tag "melhoria".
Não se esqueça de dar uma estrela ao projeto! Obrigado novamente!

1. Fork o Projeto
2. Crie sua Branch de Recurso (`git checkout -b melhoria/nome-da-melhoria`)
3. Faça suas mudanças (`git commit -m 'Add alguma melhoria'`)
4. Envie para a Branch (`git push origin melhoria/nome-da-melhoria`)
5. Faça um PR (Pull Request)

<p align="right">(<a href="#top">voltar ao topo</a>)</p>



<!-- LICENSE -->
## Licença

Distribuído sob a Licença MIT. Veja `LICENSE.md` para mais informações.

<p align="right">(<a href="#top">voltar ao topo</a>)</p>



<!-- CONTACT -->
## Contato

* **Bruno Ribeiro  - [Linkedin](https://www.linkedin.com/in/bruno-ribeiro-46675922a/) - bruno.ribeiro.lab@gmail.com**
* **Paulo Henrique - [Linkedin](https://www.linkedin.com/in/paulo-h-nascimento-0250a721a/) - pauli1_mcz@hotmail.com**

Link do Projeto: [https://github.com/mednuvem/plataforma](https://github.com/mednuvem/plataforma)

<p align="right">(<a href="#top">voltar ao topo</a>)</p>



<!-- ACKNOWLEDGMENTS -->
## Agradecimentos

Nesse espaço queria agraçader as pessoas/organizações que ajudam indiretamente no desenvolvimento desse projeto.

* [Img Shields](https://shields.io)
* [Font Awesome](https://fontawesome.com)

<p align="right">(<a href="#top">voltar ao topo</a>)</p>



<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->
[contributors-shield]: https://img.shields.io/github/contributors/brunoribeiro-lab/Best-README-Template.svg?style=for-the-badge
[contributors-url]: https://github.com/brunoribeiro-lab/Best-README-Template/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/brunoribeiro-lab/Best-README-Template.svg?style=for-the-badge
[forks-url]: https://github.com/brunoribeiro-lab/Best-README-Template/network/members
[stars-shield]: https://img.shields.io/github/stars/brunoribeiro-lab/Best-README-Template.svg?style=for-the-badge
[stars-url]: https://github.com/brunoribeiro-lab/Best-README-Template/stargazers
[issues-shield]: https://img.shields.io/github/issues/brunoribeiro-lab/Best-README-Template.svg?style=for-the-badge
[issues-url]: https://github.com/brunoribeiro-lab/Best-README-Template/issues
[license-shield]: https://img.shields.io/github/license/brunoribeiro-lab/Best-README-Template.svg?style=for-the-badge
[license-url]: https://github.com/brunoribeiro-lab/Best-README-Template/blob/master/LICENSE.txt
[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-black.svg?style=for-the-badge&logo=linkedin&colorB=555
[linkedin-url]: https://www.linkedin.com/in/bruno-ribeiro-46675922a/
[logo]: public/assets/uploads/theme/65d547065a6a3.png