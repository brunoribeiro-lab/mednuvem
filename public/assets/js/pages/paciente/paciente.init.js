/* global Swal, bootstrap */

var paciente = ({
    singular: "Paciente",
    plural: "Pacientes",
    pageAjax: 'SGS/pacientes/',
    cookie: 'paciente-doc',
    endpoints: {
        'filter': 'filtrar',
        'listing': 'listar',
        'listing-doc': 'listar-prontuario',
        'remove': 'remover',
        'preview': 'ver',
        'get-add': 'add',
        'insert': 'add',
        'get-update': 'edit',
        'update': 'edit',
        'prontuario': 'prontuario',
        'download': 'baixar-documento',
        'upload': 'upload'
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
        this.tabelaPacientes();
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
        // button refresh
        TableTools.BUTTONS.refresh = $.extend(true, {}, TableTools.buttonBase, {
            "sNewLine": "<br>",
            "sToolTip": "Atualizar todos " + $this.plural,
            "fnClick": function (nButton, oConfig) {
                $this.tabelaPacientes();
            }
        });
        TableTools.BUTTONS.refresh_prontuario = $.extend(true, {}, TableTools.buttonBase, {
            "sNewLine": "<br>",
            "sToolTip": "Atualizar todos Documentos do " + $this.plural,
            "fnClick": function (nButton, oConfig) {
                $this.tabelaProntuario($("#listingDataProntuario").data("id"));
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
                                        $this.tabelaPacientes();
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

        TableTools.BUTTONS.filter = $.extend(true, {}, TableTools.buttonBase, {
            "sNewLine": "<br>",
            "sToolTip": "Filtrar Documentos do Paciente",
            "fnClick": function (nButton, oConfig) {
                var $tipo = $("#safe-filter-tipo").val();
                var $date = $("#safe-filter-date").val();
                var $custom = $("#safe-filter-date-custom").val();
                var $start = $("#safe-filter-date-start").val();
                var $end = $("#safe-filter-date-end").val();
                $.get($this.pageAjax + $this.endpoints['filter'], {
                    "tipo": $tipo,
                    "date": $date,
                    "custom": $custom,
                    "start": $start,
                    "end": $end
                }, function (html) {
                    Swal.fire({
                        title: 'Filtrar Documentos do Paciente',
                        html: html,
                        showDenyButton: false,
                        showCancelButton: true,
                        confirmButtonText: 'Filtrar',
                        cancelButtonText: 'Fechar',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            var filtro_json = {
                                'tipo': $("#filter-tipo").find(':selected').val(),
                                'data': $("#filter-date").find(":selected").val(),
                                'data_personalizado': $("#filter-date").find(":selected").val() == "custom_date" ? $("#date_custom").val() : '',
                                'data_inicio': $("#filter-date").find(":selected").val() == "custom_ranger" ? $("#start").val() : '',
                                'data_fim': $("#filter-date").find(":selected").val() == "custom_ranger" ? $("#end").val() : ''
                            };
                            if (typeof $.cookie('filtro') !== "undefined") {
                                const resultado = $this.adicionarOuSubstituirPorIndice($.cookie('filtro'), $this.cookie, filtro_json);
                                if (resultado !== false)
                                    $.cookie('filtro', JSON.stringify(resultado));
                            } else {
                                $.cookie('filtro', JSON.stringify({[$this.cookie]: filtro_json}));
                            }
                            // apply filters
                            $("#safe-filter-tipo").val($("#filter-tipo").find(":selected").val());
                            $("#safe-filter-date").val($("#filter-date").find(":selected").val());
                            $("#safe-filter-date-custom").val($("#date_custom").val());
                            $("#safe-filter-date-start").val($("#start").val());
                            $("#safe-filter-date-end").val($("#end").val());
                            // reload table with filter
                            $this.tabelaProntuario($("#listingDataProntuario").data("id"));
                        }
                    });
                    $("#filter-type, #filter-tipo").select2();
                    $("#filter-date").select2();
                    $("#filter-date").change(function () {
                        var $index = $(this).val();
                        switch ($index) {
                            case 'custom_date':
                                $(".box-custom-ranger").addClass('hidden');
                                $(".box-custom-date").removeClass('hidden');
                                break;
                            case 'custom_ranger':
                                $(".box-custom-date").addClass('hidden');
                                $('.box-custom-ranger').removeClass('hidden');
                                break;
                            default:
                                $(".box-custom-date").addClass('hidden');
                                $(".box-custom-ranger").addClass('hidden');
                                break;
                        }
                    });
                });
            }
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
            $this.backToListing();
            if (form) {
                $(form + ' .chosen-select').chosen({width: "calc( 100% - 50px )", no_results_text: "Nada encontrado com : ", placeholder_text_single: "Selecione um Registro"});
                $(form + ' .chosen-select-full').chosen({width: "100%", no_results_text: "Nada encontrado com : ", placeholder_text_single: "Selecione um Registro"});
                $(form + " .phone").mask('(99) 9 9999-9999');
                $(form + " #cpf").mask('999.999.999-99');
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
                if (endpoint == "prontuario") {
                    $("#listingDataProntuario").data("id", $id);
                    $this.carregarFiltros(true);
                    $this.tabelaProntuario($id);
                }
                $("#btn-document").click(function () {
                    $("#document-document").trigger('click');
                });
                $("#document-document").change(function () {
                    var file = $(this)[0].files[0].name;
                    $("#btn-document").html('<i class="fa fa-file-pdf"></i> ' + file);
                });
                $("#send-document").click(function () {
                    console.log("trigger");
                    if ($("#form-upload").hasClass('hidden')) {
                        $("#form-upload").removeClass('hidden');
                        $("#box-btn-document").addClass('hidden');
                    } else {
                        $("#form-upload").addClass('hidden');
                        $("#box-btn-document").removeClass("hidden");
                    }
                });
                $("#btn-close").click(function () {
                    $("#form-upload").addClass('hidden');
                    $("#box-btn-document").removeClass("hidden");
                });
                $('input[name="tipo_documento"]').change(function () {
                    if (parseInt($(this).val()) == 1) {
                        $("#box-exame").removeClass('hidden');
                        $("#box-nome").addClass('hidden');
                        return true;
                    }
                    $("#box-exame").addClass('hidden');
                    $("#box-nome").removeClass('hidden');
                });
                var callBack = function () {
                    if (endpoint == "prontuario") {
                        $this.tabelaProntuario($("#listingDataProntuario").data("id"));
                        $("#form-upload").trigger('reset');
                        $("#btn-document").html('<i class="fa fa-file-pdf"></i> Anexar Documento');
                    } else {
                        $this.tabelaPacientes();
                        $("#box-ajax").addClass('hidden');
                        $("#box-listing").removeClass('hidden');
                        $("#action-breadcrumb").html('');
                        $("#action-breadcrumb").addClass('hidden');
                        $("#manager-breadcrumb").addClass('active');
                        var html = $("#manager-breadcrumb").html();
                        var text = html.match(/<a[^\b>]+>(.+)[\<]\/a>/)[1];
                        $("#manager-breadcrumb").html(text);
                    }
                };
                var validation = function () {
                    if (endpoint !== "prontuario") {
                        return true;
                    }
                    var inputsArray = $("#form-upload").serializeArray();
                    var inputsObject = {}; // Define inputsObject as an empty object
                    $.each(inputsArray, function (index, field) {
                        inputsObject[field.name] = field.value;
                    });
                    var tipo_documento = inputsObject['tipo_documento'];
                    if (typeof tipo_documento == "undefined" || !tipo_documento.length) {
                        hashForm.message("Selecione o Tipo do documento.", "danger", "Erro ao Enviar");
                        return false;
                    }
                    // exame
                    if (parseInt(tipo_documento) == 1) {
                        var exame = inputsObject['exame'];
                        if (typeof exame == "undefined" || !exame.length) {
                            hashForm.message("Selecione um Exame.", "danger", "Erro ao Enviar");
                            return false;
                        }
                    }
                    // validar nome
                    if (parseInt(tipo_documento) !== 1) {
                        var nameValue = inputsObject['nome'];
                        if (!nameValue.length) {
                            hashForm.message("O Campo nome não pode ser vázio.", "danger", "Erro ao Enviar");
                            return false;
                        }
                    }
                    // validar se tem algum anexo
                    var fileInput = document.querySelector("input[name='document']");
                    if (!fileInput.files.length > 0) {
                        hashForm.message("Nenhum Anexo foi selecionado", "danger", "Erro ao Enviar");
                        return false;
                    }

                    var selectedFile = fileInput.files[0];
                    if (selectedFile.type !== 'application/pdf') {
                        hashForm.message("Documento inválido, apenas PDF é permitido.", "danger", "Erro ao Enviar");
                        return false;
                    }
                    var maxinMB = 10;
                    var maxSizeInBytes = maxinMB * 1024 * 1024; // 10MB in bytes 
                    if (selectedFile.size > maxSizeInBytes) {
                        hashForm.message(`Documento muito pesado, tamanho permitido é no máximo ${maxinMB}MB.`, "danger", "Erro ao Enviar");
                        return false;
                    }
                    return true;
                };
                hashForm.init($this.pageAjax, form, $this.endpoints[updateURL], callBack, '', validation);
            }
        });
    },
    adicionarOuSubstituirPorIndice: function (filtroJson, chave, novoValor) {
        if (typeof filtroJson == "undefined") {
            return {
                [chave]: parsedNovoValor
            };
        }

        try {
            let parsedNovoValor = JSON.parse(filtroJson);
            if (Object.keys(filtroJson).length === 0) {
                parsedNovoValor[chave] = novoValor;
                return parsedNovoValor;
            } else if (parsedNovoValor.hasOwnProperty(chave)) {
                parsedNovoValor[chave] = novoValor;
                return parsedNovoValor;
            }
            // Adiciona a nova chave e novo valor ao objeto
            parsedNovoValor[chave] = novoValor;
            return parsedNovoValor;
        } catch (error) {
            console.error('Erro ao processar o novo valor JSON:', error);
        }
        return false;
    },
    carregarFiltros: function ($reload = false) {
        var filtroJson = $.cookie('filtro');
        if (!$reload && typeof filtroJson === "undefined") {
            $("#safe-filter-status").val('*');
            $("#safe-filter-date").val('this_year');
            return false;
        }
        try {
            const filtro = filtroJson ? JSON.parse(filtroJson) : {};
            if ($reload) {
                // recarrega o filtro padrão, usado quando sai do documento do paciente para outro
                $("#safe-filter-tipo").val('*');
                $("#safe-filter-date").val('this_year');
                delete filtro[this.cookie];
                $.cookie('filtro', JSON.stringify(filtro));
                console.log("limpando cookie");
                return true;
            }

            if (!filtro.hasOwnProperty(this.cookie)) {
                $("#safe-filter-tipo").val('*');
                $("#safe-filter-date").val('this_year');
                return false;
            }
            var valores = filtro[this.cookie];
            $("#safe-filter-tipo").val(valores['tipo']);
            $("#safe-filter-date").val(valores['data']);
            $("#safe-filter-date-custom").val(valores['data_personalizado']);
            $("#safe-filter-date-start").val(valores['data_inicio']);
            $("#safe-filter-date-end").val(valores['data_fim']);
        } catch (error) {
            console.error('Erro ao processar o novo valor JSON:', error);
    }
    },
    urlTabela: function (index = 'listing', $other = '') {
        var $tipo = $("#safe-filter-tipo").val();
        var $date = $("#safe-filter-date").val();
        var $custom = $("#safe-filter-date-custom").val();
        var $start = $("#safe-filter-date-start").val();
        var $end = $("#safe-filter-date-end").val();

        return this.pageAjax + this.endpoints[index] + $other + "?tipo=" + $tipo +
                "&date=" + $date +
                "&custom=" + $custom +
                "&start=" + $start +
                "&end=" + $end;
    },
    tabelaProntuario: function ($id) {
        var $this = this;
        let buttons = [{
                "sExtends": "filter",
                "sButtonText": "<i class='fa fa-search'></i>"
            }, {
                "sExtends": "refresh_prontuario",
                "sButtonText": "<i class=\"fas fa-sync\"></i>"
            }];

        var $order = [1, "desc"];
        var $aoColumns = [
            {"bSortable": false, 'className': 'hidden-md hidden-lg hidden-sm'},
            null,
            null,
            null,
            null,
            null,
            {"bSortable": false}
        ];
        var rowButtons = {
            '.goDownload': function (e) {
                var $id = $(this).data('id');
                $.get($this.pageAjax + $this.endpoints['download'] + '/' + $id, function (json) {
                    if (!json.error) {
                        var url = json.url;
                        var fileName = json.fileName || 'arquivo'; // Use o nome do arquivo fornecido ou um nome padrão
                        // Crie um link temporário
                        var link = document.createElement('a');
                        link.href = url;
                        link.download = ''; // Defina o atributo de download como uma string vazia
                        link.setAttribute('download', fileName); // Adicione o atributo de download com o nome do arquivo
                        link.setAttribute('target', '_blank');
                        // Adicione o link ao corpo do documento e simule o clique para iniciar o download
                        document.body.appendChild(link);
                        link.click();
                        // Remova o link do corpo do documento após o download
                        document.body.removeChild(link);
                    } else {
                        hashForm.message(json.msg, "danger", "Erro ao Baixar");
                    }
                }, "JSON");
            },
            /*'.goDownload': function (e) {
             var $id = $(this).data('id');
             var filename = $(this).data('filename');
             $.ajax({
             url: $this.pageAjax + $this.endpoints['download'] + '/' + $id,
             method: 'GET',
             success: function (data, textStatus, xhr) {
             if (xhr.getResponseHeader('Content-Type') === 'application/json') {
             hashForm.message(data.msg, "danger", "Erro ao Baixar");
             } else {
             // Se a resposta for um Blob, faça o download do arquivo
             var blob = new Blob([data], {type: xhr.getResponseHeader('Content-Type')});
             var url = window.URL.createObjectURL(blob);
             var a = document.createElement('a');
             a.href = url;
             a.download = filename;
             document.body.appendChild(a);
             a.click();
             document.body.removeChild(a);
             window.URL.revokeObjectURL(url);
             }
             },
             error: function (xhr, status, error) {
             hashForm.message("Erro ao fazer a requisição: " + error, "danger", "Erro ao Baixar");
             }
             });
             }*/
        };
        hashTable.setTarget('#listingDataProntuario')
                .setURL($this.urlTabela('listing-doc', "/" + $id))
                .setColumns($aoColumns)
                .setOrder($order)
                .setButtons(buttons)
                .setRowButtons(rowButtons)
                .init();
    },
    tabelaPacientes: function () {
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
            '.goProntuario': function () {
                var $id = $(this).data('id');
                $this.getContent($id, 'prontuario', '#form-upload', 'upload');
                $("#manager-breadcrumb").removeClass('active');
                $("#manager-breadcrumb").html("<a href='javascript:;' title='Clique aqui para voltar a listagem'>" + $("#manager-breadcrumb").html() + "</a>");
                $("#action-breadcrumb").html('<i class="far fa-clipboard-user"></i> Prontuário do ' + $this.singular);
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
                                    $this.tabelaPacientes();
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
        this.carregarFiltros();
        this.loadButtonsTable();
        this.tabelaPacientes();
        if (window.location.hash) {
            var hash = window.location.hash.substring(1);
            if (hash == "add") {
                this.formDeAdd();
            }
        }
    }
}).init();