var variaveis = ({
    pageAjax: 'SGS/configuracoes/desenvolvedor/variaveis-do-sistema/',
    mostrarEmail: function (select, $class) {
        $('select[name="' + select + '"]').change(function () {
            if (parseInt($(this).val()) > 0) {
                $($class).removeClass('hidden');
            } else {
                $($class).addClass('hidden');
            }
        });
    },
    campos: function () {
        $('.select-ambient').chosen({"width": "100%", minimumResultsForSearch: -1});
        this.mostrarEmail('email_dinamico_contato', '.box-email-contato');
        this.mostrarEmail('email_dinamico_novo_cliente_p', '.box-email-cliente');
        this.mostrarEmail('email_dinamico_novo_motorista_p', '.box-email-motorista');

        $('input[name="captchar_enable"]').change(function () {
            if ($(this).is(":checked")) {
                $(".group_captchar").removeClass('hidden');
            } else {
                $(".group_captchar").addClass('hidden');
            }
        });
        $('.onlyNumber').keydown(function (e) {
            // Array contendo os códigos das teclas permitidas
            var allowedKeys = [
                8, // Backspace
                13, // Enter
                46, // Delete
                67, // Ctrl + C
                86, // Ctrl + V
                35, // End
                36, // Home
                37, // Seta para a esquerda
                38, // Seta para cima
                39, // Seta para a direita
                40, // Seta para baixo
                48, // 0 (teclado do topo)
                49, // 1 (teclado do topo)
                50, // 2 (teclado do topo)
                51, // 3 (teclado do topo)
                52, // 4 (teclado do topo)
                53, // 5 (teclado do topo)
                54, // 6 (teclado do topo)
                55, // 7 (teclado do topo)
                56, // 8 (teclado do topo)
                57, // 9 (teclado do topo)
                96, // 0 (teclado numérico)
                97, // 1 (teclado numérico)
                98, // 2 (teclado numérico)
                99, // 3 (teclado numérico)
                100, // 4 (teclado numérico)
                101, // 5 (teclado numérico)
                102, // 6 (teclado numérico)
                103, // 7 (teclado numérico)
                104, // 8 (teclado numérico)
                105   // 9 (teclado numérico)
            ];

            if (e.shiftKey || e.altKey) {
                // Impede o funcionamento de Shift + qualquer tecla e Alt + qualquer tecla
                e.preventDefault();
            } else {
                var n = e.keyCode;
                if (!allowedKeys.includes(n)) {
                    // Impede todas as teclas, exceto aquelas listadas na array allowedKeys
                    e.preventDefault();
                }
            }
        });
    },
    init: function () {
        this.campos();
        hashForm.init(this.pageAjax, '#formUpdate', 'salvar', function () {
            $("#loading").addClass('hidden');
        });
        this.removerItem();
    }
}).init();
