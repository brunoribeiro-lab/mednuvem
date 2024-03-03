/* global Swal, bootstrap */

var medico = ({
    singular: "Médico",
    plural: "Médicos",
    pageAjax: 'SGS/medicos/',
    endpoints: {
        'listing': 'listar',
        'remove': 'remover',
        'pegar-historico': 'pegar-historico',
        'listing-historic': 'listar-historico',
        'preview': 'ver',
        'get-add': 'add',
        'insert': 'add',
        'get-update': 'edit',
        'update': 'edit'
    },
    resetView: function () {
        $("#box-ajax").addClass('hidden');
        $("#box-listing").removeClass('hidden');
        $("#listingData > thead > tr > th").removeClass('hidden');
        $("#listingData > tbody > tr > td").removeClass('hidden');
        $("#listingData > thead > tr > th").attr('display', "block");
        $("#listingData > tbody > tr > td").attr('display', "block");
        $("#action-breadcrumb").html('');
        $("#action-breadcrumb").addClass('hidden');
        $("#manager-breadcrumb").addClass('active');
        var html = $("#manager-breadcrumb").html();
        var text = html.match(/<a[^\b>]+>(.+)[\<]\/a>/)[1];
        $("#manager-breadcrumb").html(text);
        this.tabelaCampos();
        if (window.location.hash)
            window.location.hash = '';

        // mudar código do formulário de adicionar
        if ($("#UII").data("add").length > 0) {
            $("#UII").val($("#UII").data("default"));
            $("#UII").attr("title", $("#UII").data("default"));
        }
    },
    backToListing: function () {
        var $this = this;
        $("#backTo").click(function () {
            $this.resetView();
        });
    },
    backToListingByBreadcrumb: function () {
        var $this = this;
        $('#manager-breadcrumb a').click(function () {
            $this.resetView();
        });
    },
    formDeAdd: function () {
        this.getContent(null, 'get-add', '#formModalAddNew', 'insert');
        $("#manager-breadcrumb").removeClass('active');
        $("#manager-breadcrumb").html("<a href='javascript:;' title='Clique aqui para voltar a listagem'>" + $("#manager-breadcrumb").html() + "</a>");
        $("#action-breadcrumb").html('<i class="fa fa-plus"></i> Adicionar ' + this.singular);
        $("#action-breadcrumb").removeClass('hidden');
        this.backToListingByBreadcrumb();
        // mudar código do formulário de adicionar
        if ($("#UII").data("add").length > 0) {
            $("#UII").val($("#UII").data("add"));
            $("#UII").attr("title", $("#UII").data("add"));
        }
    },
    loadButtonsTable: function () {
        var $this = this;
        // button add new
        TableTools.BUTTONS.add_new = $.extend(true, {}, TableTools.buttonBase, {
            "sNewLine": "<br>",
            "sToolTip": "Adicionar Novo " + $this.singular,
            "fnClick": function (nButton, oConfig) {
                $this.formDeAdd();
            }
        });
        TableTools.BUTTONS.refresh_historic = $.extend(true, {}, TableTools.buttonBase, {
            "sNewLine": "<br>",
            "sToolTip": "Atualizar Histórico do Usuário",
            "fnClick": function (nButton, oConfig) {
                $this.tabelaHistoricoUsuario($("#listingDataHistoric").data("id"));
            }
        });
        // button refresh
        TableTools.BUTTONS.refresh = $.extend(true, {}, TableTools.buttonBase, {
            "sNewLine": "<br>",
            "sToolTip": "Atualizar todos " + $this.plural,
            "fnClick": function (nButton, oConfig) {
                $this.tabelaCampos();
            }
        });
        // button multiple delete
        TableTools.BUTTONS.delete_broadcast = $.extend(true, {}, TableTools.buttonBase, {
            "sNewLine": "<br>",
            "sToolTip": "Deletar múltiplos " + $this.plural,
            "fnClick": function (nButton, oConfig) {
                var count_checked = $("[name='checkbox[]']:checked").length;
                if (!count_checked) {
                    var msg = "<strong>Nenhum " + $this.singular + " foi selecionado</strong> <br> Você precisa selecionar pelo menos um " + $this.singular + " para prosseguir com a multi-exclusão.";
                    hashForm.message(msg, 'danger', "Erro ao excluir");
                } else {
                    var msg = !(count_checked > 1) ? 'Tem certeza que deseja excluir o ' + $this.singular + ' ? <br> <strong>será excluído um ' + $this.singular + '</strong>' : 'Tem certeza que deseja excluir os ' + $this.plural + ' ? <br> <strong>serão excluídos ' + count_checked + ' ' + $this.plural + ' </strong';
                    Swal.fire({
                        title: 'Confirmar Exclusão',
                        html: msg,
                        showDenyButton: false,
                        showCancelButton: true,
                        confirmButtonText: 'Excluir',
                        cancelButtonText: 'Fechar',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: $this.pageAjax + $this.endpoints['remove'],
                                type: "POST",
                                data: $("#multiple_delete").serialize(),
                                dataType: "json",
                                success: function (data) {
                                    if (data.error) {
                                        hashForm.message(data.msg, "danger", "Erro ao excluir");
                                    } else {
                                        $this.tabelaCampos();
                                        hashForm.message(data.msg, "success", "Excluído com sucesso");
                                    }
                                },
                                error: function (data) {
                                    hashForm.message(data.responseText, "danger", "Erro ao excluir");
                                }
                            });

                        }
                    });
                }
            }
        });
    },
    tabelaHistoricoUsuario: function (ref) {
        var $order = [2, "DESC"];
        var buttons = [{"sExtends": "refresh_historic", "sButtonText": "<i class=\"fas fa-sync\"></i>"}];
        var $aoColumns = [{"bSortable": false, 'className': 'hidden-md hidden-lg hidden-sm'}, null, null];
        hashTable.setTarget('#listingDataHistoric')
                .setURL(this.pageAjax + this.endpoints['listing-historic'] + "/" + ref)
                .setColumns($aoColumns)
                .setOrder($order)
                .setButtons(buttons)
                .init();
    },
    getContent: function ($id, endpoint, form = null, updateURL = null) {
        var $this = this;
        $("#box-ajax").removeClass('hidden');
        $("#box-listing").addClass('hidden');
        $('#box-ajax').html('<div class="col-md-12 text-center"><p>Carregando...</p><div class="spinner-border text-primary m-1" role="status"><span class="sr-only">Carregando...</span></div></div>');
        var getEndpoint = this.pageAjax + this.endpoints[endpoint];
        if (parseInt($id))
            getEndpoint += "/" + $id;

        $.get(getEndpoint, function (html) {
            $('#box-ajax').html(html);
            $this.backToListing();
            if (form == '#listingDataHistoric') {
                $this.tabelaHistoricoUsuario($id);
                return true;
            }
            if (form) {
                $(form + ' .chosen-select').chosen({width: "calc( 100% - 50px )", no_results_text: "Nada encontrado com : ", placeholder_text_single: "Selecione um Registro"});
                $(form + ' .chosen-select-full').chosen({width: "100%", no_results_text: "Nada encontrado com : ", placeholder_text_single: "Selecione um Registro"});
                $(form + " .phone").mask('(99) 9 9999-9999');
                $(form + ' .onlyNumber').keydown(function (e) {
                    if (e.shiftKey || e.ctrlKey || e.altKey) {
                        e.preventDefault();
                    } else {
                        var n = e.keyCode;
                        if (!((n === 8) || (n === 46) || (n >= 35 && n <= 40) || (n >= 48 && n <= 57) || (n >= 96 && n <= 105))) {
                            e.preventDefault();
                        }
                    }
                });
                $(form + ' #setor').change(function () {
                    $(form + " #funcao").html('<option value="">-- Selecione --</option>');
                    if ($(this).val() == "") {
                        $(form + " #funcao").trigger('chosen:updated');
                        return false;
                    }
                    $.get("SGS/configuracoes/medico/exames/listar-funcoes/" + $(this).val(), function (json) {
                        if (!json.error) {
                            $.each(json.resultado, function (index, data) {
                                $(form + " #funcao").append('<option value="' + data.id + '">' + data.nome + '</option>');
                            });
                        }
                        $(form + " #funcao").trigger('chosen:updated');
                    }, "JSON");

                });
                $(form + ' input[name="mudar_senha"]').change(function () {
                    if (parseInt($(this).val())) {
                        $(form + " .box-password").removeClass('hidden');
                    } else {
                        $(form + " .box-password").addClass('hidden');
                    }
                });
                if ($(form + ' input[name="tipo_login"]:checked').val() == "cpf") {
                    $(form + " #login-label").html('<span id="field-required">*</span> CPF');
                    $(form + " #username").mask('000.000.000-00');
                }
                if ($(form + ' input[name="tipo_login"]:checked').val() == "cnpj") {
                    $(form + " #login-label").html('<span id="field-required">*</span> CNPJ');
                    $(form + " #username").mask('00.000.000/0000-00');
                }
                $(form + ' input[name="tipo_login"]').change(function () {
                    $(form + " #username").val('');
                    if ($(this).val() == "cpf") {
                        $(form + " #login-label").html('<span id="field-required">*</span> CPF');
                        $(form + " #username").mask('000.000.000-00');
                    }
                    if ($(this).val() == "cnpj") {
                        $(form + " #login-label").html('<span id="field-required">*</span> CNPJ');
                        $(form + " #username").mask('00.000.000/0000-00');
                    }
                });
                $(form + ' #gerar_senha').change(function () {
                    if (!parseInt($(this).val())) {
                        $(form + ' .grupo-senha').addClass('hidden');
                        return false;
                    }

                    if (parseInt($(this).val())) {
                        $(form + ' .grupo-senha').removeClass('hidden');
                        return true;
                    }
                });
                var callBack = function () {
                    $this.tabelaCampos();
                    $("#box-ajax").addClass('hidden');
                    $("#box-listing").removeClass('hidden');
                    $("#action-breadcrumb").html('');
                    $("#action-breadcrumb").addClass('hidden');
                    $("#manager-breadcrumb").addClass('active');
                    var html = $("#manager-breadcrumb").html();
                    var text = html.match(/<a[^\b>]+>(.+)[\<]\/a>/)[1];
                    $("#manager-breadcrumb").html(text);
                };
                hashForm.init($this.pageAjax, form, $this.endpoints[updateURL], callBack);
            }
        });
    },
    tabelaCampos: function () {
        var $this = this;
        let buttons = [{
                "sExtends": "add_new",
                "sButtonText": "<i class='fa fa-plus'></i>"
            }, {
                "sExtends": "refresh",
                "sButtonText": "<i class=\"fas fa-sync\"></i>"
            }, {
                "sExtends": "delete_broadcast",
                "sButtonText": "<i class='fa fa-trash'></i>"
            }];
        if (!window.is_root) {
            buttons = [];
            if (window.action['add']) {
                buttons.push({
                    "sExtends": "add_new",
                    "sButtonText": "<i class='fa fa-plus'></i>"
                });
            }
            buttons.push({
                "sExtends": "refresh",
                "sButtonText": "<i class=\"fas fa-sync\"></i>"
            });
            if (window.action['remove']) {
                buttons.push({
                    "sExtends": "delete_broadcast",
                    "sButtonText": "<i class='fa fa-trash'></i>"
                });
            }
        }
        var $order = [6, "desc"];
        var $aoColumns = [
            {"bSortable": false, 'className': 'hidden-md hidden-lg hidden-sm'},
            {"bSortable": false},
            null,
            null,
            null,
            null,
            null,
            {"bSortable": false}
        ];
        var rowButtons = {
            '.goPreview': function (e) {
                var $id = $(this).data('id');
                $this.getContent($id, 'preview');
                $("#manager-breadcrumb").removeClass('active');
                $("#manager-breadcrumb").html("<a href='javascript:;' title='Clique aqui para voltar a listagem'>" + $("#manager-breadcrumb").html() + "</a>");
                $("#action-breadcrumb").html('<i class="fa fa-eye"></i> Detalhes do ' + $this.singular);
                $("#action-breadcrumb").removeClass('hidden');
                $this.backToListingByBreadcrumb();
            },
            '.goHistoric': function (e) {
                var $id = $(this).data('id');
                $this.getContent($id, 'pegar-historico', '#listingDataHistoric');
                $("#manager-breadcrumb").removeClass('active');
                $("#manager-breadcrumb").html("<a href='javascript:;' title='Clique aqui para voltar a listagem'>" + $("#manager-breadcrumb").html() + "</a>");
                $("#action-breadcrumb").html('<i class="far fa-shoe-prints"></i> Histórico do ' + $this.singular);
                $("#action-breadcrumb").removeClass('hidden');
                $this.backToListingByBreadcrumb();
            },
            '.goUpdate': function (e) {
                var $id = $(this).data('id');
                $this.getContent($id, 'get-update', '#formModalUpdate', 'update');
                $("#manager-breadcrumb").removeClass('active');
                $("#manager-breadcrumb").html("<a href='javascript:;' title='Clique aqui para voltar a listagem'>" + $("#manager-breadcrumb").html() + "</a>");
                $("#action-breadcrumb").html('<i class="fas fa-pencil-alt"></i> Editar ' + $this.singular);
                $("#action-breadcrumb").removeClass('hidden');
                $this.backToListingByBreadcrumb();
            },
            '.goRem': function (e) {
                var $id = $(this).data('id');
                Swal.fire({
                    title: 'Confirmar Exclusão',
                    html: 'Tem certeza que deseja excluir ?',
                    showDenyButton: false,
                    showCancelButton: true,
                    confirmButtonText: 'Excluir',
                    cancelButtonText: 'Fechar',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: $this.pageAjax + $this.endpoints['remove'] + "/" + $id,
                            type: "GET",
                            dataType: "json",
                            success: function (data) {
                                if (data.error) {
                                    hashForm.message(data.msg, "danger", "Erro ao excluir");
                                } else {
                                    $this.tabelaCampos();
                                    hashForm.message(data.msg, "success", "Excluído com sucesso");
                                }
                            },
                            error: function (data) {
                                hashForm.message(data.responseText, "danger", "Erro ao excluir");
                            },
                        });

                    }
                });
            },
        };
        hashTable.setTarget('#listingData')
                .setURL(this.pageAjax + this.endpoints['listing'])
                .setColumns($aoColumns)
                .setOrder($order)
                .setButtons(buttons)
                .setRowButtons(rowButtons)
                .init();
    },
    init: function () {
        this.loadButtonsTable();
        this.tabelaCampos();
        if (window.location.hash) {
            var hash = window.location.hash.substring(1);
            if (hash == "add") {
                this.formDeAdd();
            }
        }
    }
}).init();