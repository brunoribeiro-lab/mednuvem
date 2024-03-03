var email_configuracoes = ({
    pageAjax: 'SGS/configuracoes/email/configurar-email/',
    checkHidden: function (val) {
        switch (val) {
            case 'disabled':
                $(".box-auth").addClass('hidden');
                break;
            case 'native':
                $(".box-auth").addClass('hidden');
                $(".box-native").removeClass("hidden");
                break;
            case 'smtp':
                $(".box-auth").removeClass('hidden');
                break;
            default:

                break;
        }
    },
    campos: function () {
        var $this = this;
        $(".select-ambient").chosen({"width": "100%"});
        $(".encrypt").chosen({'width': "100%", "disable_search": true});
        this.checkHidden($("#mode").val());
        $("#mode").change(function () {
            $this.checkHidden($(this).val());
        });
        $("#autenticate").click(function () {
            $("#auth-test-logs").html('');
            $("#loading2").removeClass('hidden');
            $('#autenticate').attr('disabled', true);

            $.ajax({
                url: $this.pageAjax + "testar",
                type: "GET",
                success: function (data) {
                    $("#loading2").addClass('hidden');
                    $("#box-logs").removeClass('hidden');
                    if (typeof data === "string")
                        $("#auth-test-logs").html("Resultado do envio para o email : " + $("#my-email").val() + " <br> " + data.replace(/<br>/, '\n'));

                    if (data.indexOf("Email autenticado com sucesso") >= 0) {
                        hashForm.message(data.msg, 'success', 'Email Autenticado');
                    } else {
                        hashForm.message(data.msg, 'danger', 'Erro ao Autenticar');
                    }
                    $('#autenticate').attr('disabled', false);
                },
                error: function (data) {
                    if (data.status === 422) {
                        var json = data.responseJSON;
                        $this.setarErros(json, form);

                    } else {
                        $this.message(data.responseText, 'danger');
                    }
                    $('#autenticate').attr('disabled', false);
                }
            });
        });
    },

    init: function () {
        this.campos();
        hashForm.init(this.pageAjax, '#formUpdate', 'salvar', function () {
            $("#loading").addClass('hidden');
        });
    }
}).init();
