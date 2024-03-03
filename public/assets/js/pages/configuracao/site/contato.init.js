/* global Swal */

var setor = ({
    singular: "Mensagem",
    plural: "Mensagens",
    pageAjax: 'SGS/configuracoes/site/mensagens/',
    endpoints: {
        'listing': 'listar',
        'remove': 'remover',
        'preview': 'ver'
    },
    resetView: function () {
        $("#box-ajax, #last-breadcrumb").addClass('hidden');
        $("#box-listing, #action-breadcrumb").removeClass('hidden');

        $("#listingData > thead > tr > th, #listingData > tbody > tr > td")
                .removeClass('hidden')
                .attr('display', "block");

        $("#action-breadcrumb").html('').addClass('hidden');
        $("#manager-breadcrumb").addClass('active');

        var html = $("#manager-breadcrumb").html();
        var text = html.match(/<a[^\b>]+>(.+)[\<]\/a>/)[1];
        $("#manager-breadcrumb").html(text);

        this.tabelaSetor();

        if (window.location.hash)
            window.location.hash = '';

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
            $(form + ' .chosen-select').chosen({width: "100%", no_results_text: "Nada encontrado com : ", placeholder_text_single: "Selecione um Registro"});
            $this.backToListing();
            if (form) {
                var callBack = function () {
                    $this.tabelaSetor();
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
    tabelaSetor: function () {
        var $this = this;
        let buttons = [{
                "sExtends": "refresh",
                "sButtonText": "<i class=\"fas fa-sync\"></i>"
            }, {
                "sExtends": "delete_broadcast",
                "sButtonText": "<i class='fa fa-trash'></i>"
            }];
        if (!window.is_root) {
            buttons = [];
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
        var $order = [3, "desc"];
        var $aoColumns = [
            {"bSortable": false, 'className': 'hidden-md hidden-lg hidden-sm'},
            {"bSortable": false},
            null,
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
                                    $this.tabelaSetor();
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
    loadButtonsTable: function () {
        var $this = this;
        // button refresh
        TableTools.BUTTONS.refresh = $.extend(true, {}, TableTools.buttonBase, {
            "sNewLine": "<br>",
            "sToolTip": "Atualizar " + $this.plural,
            "fnClick": function (nButton, oConfig) {
                $this.tabelaSetor();
            }
        });
        // button multiple delete
        TableTools.BUTTONS.delete_broadcast = $.extend(true, {}, TableTools.buttonBase, {
            "sNewLine": "<br>",
            "sToolTip": "Deletar múltiplas " + $this.plural,
            "fnClick": function (nButton, oConfig) {
                var count_checked = $("[name='checkbox[]']:checked").length;
                if (!count_checked) {
                    var msg = "<strong>Nenhuma " + $this.singular + " foi selecionada</strong> <br> Você precisa selecionar pelo menos uma " + $this.singular + " para prosseguir com a multi-exclusão.";
                    hashForm.message(msg, 'danger', "Erro ao excluir");
                } else {
                    var msg = !(count_checked > 1) ? 'Tem certeza que deseja excluir a ' + $this.singular + ' ? <br> <strong>será excluído uma ' + $this.singular + '</strong>' : 'Tem certeza que deseja excluir as ' + $this.plural + ' ? <br> <strong>serão excluídas ' + count_checked + ' ' + $this.plural + ' </strong';
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
                                        $this.tabelaSetor();
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
    init: function () {
        this.loadButtonsTable();
        this.tabelaSetor();
    }
}).init();