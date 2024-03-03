var acessoXML_configuracoes = ({
    pageAjax: 'SGS/configuracoes/desenvolvedor/acesso/',
    init: function () {
        hashForm.init(this.pageAjax, '#formUpdate', 'salvar', function () {
            $("#loading").addClass('hidden');
        });
    }
}).init();
