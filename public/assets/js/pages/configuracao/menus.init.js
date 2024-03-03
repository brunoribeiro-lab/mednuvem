/* global Swal */

var menus = ({
    singular: "Menu",
    plural: "Menus",
    pageAjax: 'AJAX/Settings/',
    endpoints: {
        'listing': 'bWVudS9saXN0aW5nCg==',
        'preview': 'bWVudS9wcmV2aWV3Cg==',
        'preview-submenu': 'bWVudS9wcmV2aWV3LXN1Ym1lbnUK',
        'preview-subsubmenu': 'bWVudS9wcmV2aWV3LXN1YnN1Ym1lbnUK',
        'get-update': 'bWVudS9nZXQtdXBkYXRlCg==',
        'get-update-submenu': 'bWVudS9nZXQtdXBkYXRlLXN1Ym1lbnUK',
        'update': 'bWVudS91cGRhdGUK',
        'update-submenu': 'bWVudS91cGRhdGUtc3VibWVudQo=',
        'submenu': 'bWVudS90YWJsZS1zdWJtZW51Cg==',
        'listing_submenu': 'bWVudS9saXN0aW5nLXN1Ym1lbnUK',
        'subsubmenu': 'bWVudS90YWJsZS1zdWJzdWJtZW51Cg==',
        'listing_subsubmenu': 'bWVudS9saXN0aW5nLXN1YnN1Ym1lbnUK',
        'get-update-subsubmenu': 'bWVudS9nZXQtdXBkYXRlLXN1YnN1Ym1lbnUK',
        'update-subsubmenu': 'bWVudS91cGRhdGUtc3Vic3VibWVudQo='
    },
    message: function (msg, type, title) {
        let icon = 'error';
        if (type === 'success') {
            icon = 'success';
        }
        Swal.fire({
            title: title,
            html: msg,
            icon: icon,
            timer: type === 'success' ? 4000 : 8000,
            confirmButtonText: 'Fechar',
            showCancelButton: false,
            confirmButtonColor: "#5156be",
            cancelButtonColor: "#fd625e"
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.timer) {

            }
        });
    },
    actionBackLevel0: function () {
        $("#box-ajax, #box-ajax-level-2, #box-ajax-level-3").addClass('hidden');
        $("#box-listing").removeClass('hidden');
        $("#action-breadcrumb, #action-level-3-last-breadcrumb, #last-breadcrumb").html('');
        $("#action-breadcrumb, #action-level-3-last-breadcrumb, #last-breadcrumb").addClass('hidden');
        $("#manager-breadcrumb").addClass('active');
        var html = $("#manager-breadcrumb").html();
        var text = html.match(/<a[^\b>]+>(.+)[\<]\/a>/)[1];
        $("#manager-breadcrumb").html(text);
        this.loadContain();
        window.location.hash = '';
    },
    actionBackLevel1: function () {
        $("#box-ajax-level-2, #box-ajax-level-3").addClass('hidden');
        $("#box-ajax").removeClass('hidden'); 
        $("#last-breadcrumb, #action-level-3-last-breadcrumb").html('');
        $("#last-breadcrumb, #action-level-3-last-breadcrumb").addClass('hidden');
        $("#action-breadcrumb").addClass('active');
        var html = $("#action-breadcrumb").html();
        var text = html.match(/<a[^\b>]+>(.+)[\<]\/a>/)[1];
        $("#action-breadcrumb").html(text);
        this.loadSubmenuTable($("#listingDataSubmenu").data("id"));
        window.location.hash = '';
    },
    actionBackLevel2: function () {
        $("#box-ajax-level-3").addClass('hidden');
        $("#box-ajax-level-2").removeClass('hidden');
        $("#action-level-3-last-breadcrumb").html('');
        $("#action-level-3-last-breadcrumb").addClass('hidden');
        $("#last-breadcrumb").addClass('active');
        var html = $("#last-breadcrumb").html();
        var text = html.match(/<a[^\b>]+>(.+)[\<]\/a>/)[1];
        $("#last-breadcrumb").html(text);
        this.loadSubSubmenuTable($("#listingDataSubSubmenu").data("id"));
        window.location.hash = '';
    },
    validateForm: function (form, file) {
        var $this = this;
        $(form).validate({
            errorElement: 'span',
            errorClass: 'error',
            focusInvalid: true,
            ignore: ":hidden",
            rules: {},
            errorPlacement: function (error, element) {
                var icon = $(element).parent('.input-with-icon').children('i');
                var parent = $(element).parent('.input-with-icon');
                icon.removeClass('fa fa-check').addClass('fa fa-exclamation');
                parent.removeClass('success-control').addClass('error-control');
            },
            highlight: function (element) {
                var parent = $(element).parent();
                parent.removeClass('success-control').addClass('error-control');
            },
            success: function (label, element) {
                var icon = $(element).parent('.input-with-icon').children('i');
                var parent = $(element).parent('.input-with-icon');
                icon.removeClass("fa fa-exclamation").addClass('fa fa-check');
                parent.removeClass('error-control').addClass('success-control');
            },
            invalidHandler: function () {
                $(form + ' button[type=submit], input[type=submit]').attr('disabled', false);
            },
            submitHandler: function (event) {
                if (!$(form).data('beenSubmitted')) {
                    let button = '<i class="fa fa-plus"></i> Cadastrar';
                    if (form == "#formModalUpdate") {
                        button = '<i class="fa fa-sync"></i> Salvar';
                    }
                    $(form + ' button[type=submit], input[type=submit]').removeClass("btn-success").addClass('btn-dark waves-effect waves-light');
                    $(form + ' button[type=submit], input[type=submit]').html('<i class="bx bx-hourglass bx-spin font-size-16 align-middle me-2"></i> Carregando');
                    $("#loading").removeClass('hidden');
                    $(form).data('beenSubmitted', true);
                    var data = new FormData($(form)[0]);
                    $.ajax({
                        url: $this.pageAjax + file,
                        type: "POST",
                        data: data,
                        processData: false,
                        contentType: false,
                        dataType: "json",
                        success: function (data) {
                            $(form + ' button[type=submit], input[type=submit]').attr('disabled', false);
                            $(form + ' button[type=submit], input[type=submit]').removeClass('btn-dark waves-effect waves-light').addClass('btn-success');
                            $(form + ' button[type=submit], input[type=submit]').html(button);
                            $(form).data('beenSubmitted', false);
                            if (data.error) {
                                $this.message(data.msg, 'danger');
                            } else {
                                $this.message(data.msg, 'success');
                                switch (form) {
                                    case '#formModalUpdateSubmenu':
                                        $this.actionBackLevel1();
                                        break;
                                    case '#formModalUpdateSubSubmenu':
                                        $this.actionBackLevel2();
                                        break;
                                    default:
                                        $this.actionBackLevel0();
                                        break;
                                }
                            }
                        },
                        error: function (data) {
                            $this.message(data.responseText, 'danger');
                            $(form + ' button[type=submit], input[type=submit]').attr('disabled', false);
                            $(form + ' button[type=submit], input[type=submit]').removeClass('btn-dark waves-effect waves-light').addClass('btn-success');
                            $(form + ' button[type=submit], input[type=submit]').html(button);
                            $(form).data('beenSubmitted', false);
                        },
                        beforeSend: function () {
                        },
                        complete: function () {
                            $(form + ' button[type=submit], input[type=submit]').attr('disabled', false);
                            $(form + ' button[type=submit], input[type=submit]').removeClass('btn-dark waves-effect waves-light').addClass('btn-success');
                            $(form + ' button[type=submit], input[type=submit]').html(button);
                            $(form).data('beenSubmitted', false);
                            $("#loading").addClass('hidden');
                        }
                    });
                    return false;
                }
            }
        });
    },
    backToListing: function () {
        var $this = this;
        $("#backTo").click(function () {
            $this.actionBackLevel0();
        });
        $("#backTo2").click(function () {
            $this.actionBackLevel1();
        });
        $("#backTo3").click(function () {
            $this.actionBackLevel2();
        });
    },
    backToListingByBreadcrumb: function () {
        var $this = this;
        $('#manager-breadcrumb a').click(function () {
            $this.actionBackLevel0();
        });
        $('#action-breadcrumb a').click(function () {
            $this.actionBackLevel1();
        });
        $('#last-breadcrumb a').click(function () {
            $this.actionBackLevel2();
        });
    },
    helper: function () {
        var $this = this;
        $(".input-help").click(function () {
            var index = $(this).data("index");
            $.post("AJAX/Doc", {"index": index}, function (json) {
                if (!json.error) {
                    Swal.fire({
                        title: json.title,
                        html: json.text,
                        icon: "info",
                        confirmButtonText: 'Fechar',
                        showCancelButton: false,
                        confirmButtonColor: "#5156be",
                        cancelButtonColor: "#fd625e"
                    });
                } else {
                    $this.message("Ocorreu um erro ao obter a documentação desse campo.", 'danger', "Erro ao obter documentação");
                }
            }, "JSON");
        });
    },
    removerBotao: function () {
        $(".remover-regra").click(function () {
            var $id = $(this).data("id");
            Swal.fire({
                title: 'Remover Regra',
                html: "Tem certeza que deseja remover essa regra de bloqueio de campo ?",
                showDenyButton: false,
                showCancelButton: true,
                confirmButtonText: 'Excluir',
                cancelButtonText: 'Fechar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#regra-" + $id).remove();
                }
            });
        });
    },
    loadModalContentLevel2: function (updateURL, getURL, form, id) {
        id = typeof id !== 'undefined' ? id : false;
        var $this = this;
        $("#box-ajax-level-2").removeClass('hidden');
        $("#box-ajax").addClass('hidden');
        $('#box-ajax-level-2').html('<div class="col-md-12 text-center"><p>Carregando...</p><div class="spinner-border text-primary m-1" role="status"><span class="sr-only">Carregando...</span></div></div>');
        var $param = {};
        if (id > 0) {
            $param = {"id": id};
        }
        $(form).data('beenSubmitted', false);
        $(form + ' button[type=submit], input[type=submit]').attr('disabled', false);
        $.post($this.pageAjax + getURL, $param, function (html) {
            $("#box-ajax-level-2").html(html);
            $(form + ' .chosen-select').chosen({width: "100%", no_results_text: "Nada encontrado com : ", placeholder_text_single: "Selecione uma Referência"});
            $(form + " .valor").mask('#.##0,00', {reverse: true});
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
            $this.helper();
            $this.backToListing();
            $this.validateForm(form, updateURL);
        });
    },
    loadModalContentLevel3: function (updateURL, getURL, form, id) {
        id = typeof id !== 'undefined' ? id : false;
        var $this = this;
        $("#box-ajax-level-3").removeClass('hidden');
        $("#box-ajax-level-2").addClass('hidden');
        $('#box-ajax-level-3').html('<div class="col-md-12 text-center"><p>Carregando...</p><div class="spinner-border text-primary m-1" role="status"><span class="sr-only">Carregando...</span></div></div>');
        var $param = {};
        if (id > 0) {
            $param = {"id": id};
        }
        $(form).data('beenSubmitted', false);
        $(form + ' button[type=submit], input[type=submit]').attr('disabled', false);
        $.post($this.pageAjax + getURL, $param, function (html) {
            $("#box-ajax-level-3").html(html);
            $(form + ' .chosen-select').chosen({width: "100%", no_results_text: "Nada encontrado com : ", placeholder_text_single: "Selecione uma Referência"});
            $(form + " .valor").mask('#.##0,00', {reverse: true});
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
            $this.helper();
            $this.backToListing();
            $this.validateForm(form, updateURL);
        });
    },
    loadModalContent: function (updateURL, getURL, form, id) {
        id = typeof id !== 'undefined' ? id : false;
        var $this = this;
        $("#box-ajax").removeClass('hidden');
        $("#box-listing").addClass('hidden');
        $('#box-ajax').html('<div class="col-md-12 text-center"><p>Carregando...</p><div class="spinner-border text-primary m-1" role="status"><span class="sr-only">Carregando...</span></div></div>');
        var $param = {};
        if (id > 0) {
            $param = {"id": id};
        }
        $(form).data('beenSubmitted', false);
        $(form + ' button[type=submit], input[type=submit]').attr('disabled', false);
        $.post($this.pageAjax + getURL, $param, function (html) {
            $("#box-ajax").html(html);
            $(form + ' .chosen-select').chosen({width: "100%", no_results_text: "Nada encontrado com : ", placeholder_text_single: "Selecione uma Referência"});
            $(form + " .valor").mask('#.##0,00', {reverse: true});
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
            $this.helper();
            $this.backToListing();
            $this.validateForm(form, updateURL);
        });
    },
    loadTable: function (path, target = '#listingData') {
        var $this = this;
        var tableElement = $(target);
        let refresh = "refresh";
        if (target == "#listingDataSubmenu")
            refresh = "refresh_submenu";

        if (target == "#listingDataSubSubmenu")
            refresh = "refresh_subsubmenu";

        var buttons = [{
                "sExtends": refresh,
                "sButtonText": "<i class=\"fas fa-sync\"></i>"
            }];
        tableElement.dataTable({
            "language": {
                "sEmptyTable": "Nenhum registro encontrado",
                "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                "sInfoFiltered": "(Filtrados de _MAX_ registros)",
                "sInfoPostFix": "",
                "sInfoThousands": ".",
                "sLoadingRecords": "Carregando...",
                "sProcessing": "Processando...",
                "sZeroRecords": "Nenhum registro encontrado",
                "sSearch": "Pesquisar",
                "oPaginate": {
                    "sNext": "Próximo",
                    "sPrevious": "Anterior",
                    "sFirst": "Primeiro",
                    "sLast": "Último"
                },
                "oAria": {
                    "sSortAscending": ": Ordenar colunas de forma ascendente",
                    "sSortDescending": ": Ordenar colunas de forma descendente"
                }
            },
            responsive: true,
            "sDom": "<'row'<'col-md-6'l T><'col-md-6'f>r>t<'row'<'col-md-12'p i>>",
            "oTableTools": {
                "aButtons": buttons
            },
            "aoColumns": [
                {"bSortable": false, 'className': 'hidden-md hidden-lg hidden-sm'},
                null,
                null,
                null,
                null,
                {"bSortable": false}
            ],
            "order": [
                [4, "ASC"] // updated date column
            ],
            "bAutoWidth": false,
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": path
        });
        $(target + '_wrapper .dataTables_filter input').addClass("input-medium ");
        $(target + '_wrapper .dataTables_length select').addClass("select2-wrapper span12");
        $(".select2-wrapper").chosen({
            "disable_search": true,
            "width": "55px"
        });
        $(target + ' tbody').on('click', '.goPreview', function (e) {
            var $id = $(this).data('id');
            $this.loadModalContent("", $this.endpoints['preview'], "#formModalUpdate", $id);
            $("#manager-breadcrumb").removeClass('active');
            $("#manager-breadcrumb").html("<a href='javascript:;' title='Clique aqui para voltar a listagem'>" + $("#manager-breadcrumb").html() + "</a>");
            $("#action-breadcrumb").html('<i class="fa fa-eye"></i> Detalhes do ' + $this.singular);
            $("#action-breadcrumb").removeClass('hidden');
            $this.backToListingByBreadcrumb();
        });
        $(target + ' tbody').on('click', '.goPreviewSubmenu', function (e) {
            var $id = $(this).data('id');
            $this.loadModalContentLevel2("", $this.endpoints['preview-submenu'], "#formModalUpdate", $id);
            $("#action-breadcrumb").removeClass('active');
            $("#action-breadcrumb").html("<a href='javascript:;' title='Clique aqui para voltar a listagem'>" + $("#action-breadcrumb").html() + "</a>");
            $("#last-breadcrumb").html('<i class="fa fa-eye"></i> Detalhes do ' + $this.singular);
            $("#last-breadcrumb").removeClass('hidden');
            $this.backToListingByBreadcrumb();
        });
        $(target + ' tbody').on('click', '.goPreviewSubSubmenu', function (e) {
            var $id = $(this).data('id');
            $this.loadModalContentLevel3("", $this.endpoints['preview-subsubmenu'], "#formModalUpdate", $id);
            $("#last-breadcrumb").removeClass('active');
            $("#last-breadcrumb").html("<a href='javascript:;' title='Clique aqui para voltar a listagem'>" + $("#last-breadcrumb").html() + "</a>");
            $("#action-level-3-last-breadcrumb").html('<i class="fa fa-eye"></i> Detalhes do Submenu do Submenu do ' + $this.singular);
            $("#action-level-3-last-breadcrumb").removeClass('hidden');
            $this.backToListingByBreadcrumb();
        });
        $(target + ' tbody').on('click', '.goUpdateSubmenu', function (e) {
            var $id = $(this).data('id');
            $this.loadModalContentLevel2($this.endpoints['update-submenu'], $this.endpoints['get-update-submenu'], "#formModalUpdateSubmenu", $id);
            $("#action-breadcrumb").removeClass('active');
            $("#action-breadcrumb").html("<a href='javascript:;' title='Clique aqui para voltar a listagem'>" + $("#action-breadcrumb").html() + "</a>");
            $("#last-breadcrumb").html('<i class="fas fa-pencil-alt"></i> Editar Submenu do ' + $this.singular);
            $("#last-breadcrumb").removeClass('hidden');
            $this.backToListingByBreadcrumb();
        });
        $(target + ' tbody').on('click', '.goUpdateSubSubmenu', function (e) {
            var $id = $(this).data('id');
            $this.loadModalContentLevel3($this.endpoints['update-subsubmenu'], $this.endpoints['get-update-subsubmenu'], "#formModalUpdateSubSubmenu", $id);
            $("#last-breadcrumb").removeClass('active');
            $("#last-breadcrumb").html("<a href='javascript:;' title='Clique aqui para voltar a listagem'>" + $("#last-breadcrumb").html() + "</a>");
            $("#action-level-3-last-breadcrumb").html('<i class="fas fa-pencil-alt"></i> Editar Submenu do Submenu do ' + $this.singular);
            $("#action-level-3-last-breadcrumb").removeClass('hidden');
            $this.backToListingByBreadcrumb();
        });
        $(target + ' tbody').on('click', '.goUpdate', function (e) {
            var $id = $(this).data('id');
            $this.loadModalContent($this.endpoints['update'], $this.endpoints['get-update'], "#formModalUpdate", $id);
            $("#manager-breadcrumb").removeClass('active');
            $("#manager-breadcrumb").html("<a href='javascript:;' title='Clique aqui para voltar a listagem'>" + $("#manager-breadcrumb").html() + "</a>");
            $("#action-breadcrumb").html('<i class="fas fa-pencil-alt"></i> Editar ' + $this.singular);
            $("#action-breadcrumb").removeClass('hidden');
            $this.backToListingByBreadcrumb();
        });
        $(target + ' tbody').on('click', '.goSubmenu', function (e) {
            var $id = $(this).data('id');
            $("#box-ajax").removeClass('hidden');
            $("#box-listing").addClass('hidden');
            $("#manager-breadcrumb").removeClass('active');
            $("#manager-breadcrumb").html("<a href='javascript:;' title='Clique aqui para voltar a listagem'>" + $("#manager-breadcrumb").html() + "</a>");
            $("#action-breadcrumb").html('<i class="far fa-list-alt"></i> Submenus do ' + $this.singular);
            $("#action-breadcrumb").removeClass('hidden');
            $('#box-ajax').html('<div class="col-md-12 text-center"><p>Carregando...</p><div class="spinner-border text-primary m-1" role="status"><span class="sr-only">Carregando...</span></div></div>');
            $.post($this.pageAjax + $this.endpoints['submenu'], {"id": $id}, function ($html) {
                $("#box-ajax").html($html);
                $this.backToListingByBreadcrumb();
                $this.backToListing();
                $("#listingDataSubmenu").data("id", $id);
                $this.loadSubmenuTable($id);
            });
        });
        $(target + ' tbody').on('click', '.goSubSubmenu', function (e) {
            var $id = $(this).data('id');
            $("#box-ajax-level-2").removeClass('hidden');
            $("#box-ajax").addClass('hidden');
            $("#action-breadcrumb").removeClass('active');
            $("#action-breadcrumb").html("<a href='javascript:;' title='Clique aqui para voltar a listagem'>" + $("#action-breadcrumb").html() + "</a>");
            $("#last-breadcrumb").html('<i class="far fa-list-alt"></i> Submenus do Submenu do ' + $this.singular);
            $("#last-breadcrumb").removeClass('hidden');
            $('#box-ajax-level-2').html('<div class="col-md-12 text-center"><p>Carregando...</p><div class="spinner-border text-primary m-1" role="status"><span class="sr-only">Carregando...</span></div></div>');
            $.post($this.pageAjax + $this.endpoints['subsubmenu'], {"id": $id}, function ($html) {
                $("#box-ajax-level-2").html($html);
                $this.backToListingByBreadcrumb();
                $this.backToListing();
                $("#listingDataSubSubmenu").data("id", $id);
                $this.loadSubSubmenuTable($id);
            });
        });


        $(target + ' tbody').on('click', 'input', function (e) {
            if ($(this).is(':checked')) {
                $(this).parent().parent().parent().addClass('row_selected');
            } else {
                $(this).parent().parent().parent().removeClass('row_selected');
            }
        });
    },
    loadSubmenuTable: function ($id) {
        var $this = this;
        if ($.fn.DataTable.isDataTable('#listingDataSubmenu')) {
            $('#listingDataSubmenu').dataTable().fnReloadAjax($this.pageAjax + $this.endpoints['listing_submenu'] + "?id=" + $id);
        } else {
            // create table
            $this.loadTable($this.pageAjax + $this.endpoints['listing_submenu'] + "?id=" + $id, '#listingDataSubmenu');
        }
        return false;
    },
    loadSubSubmenuTable: function ($id) {
        var $this = this;
        if ($.fn.DataTable.isDataTable('#listingDataSubSubmenu')) {
            $('#listingDataSubSubmenu').dataTable().fnReloadAjax($this.pageAjax + $this.endpoints['listing_subsubmenu'] + "?id=" + $id);
        } else {
            // create table
            $this.loadTable($this.pageAjax + $this.endpoints['listing_subsubmenu'] + "?id=" + $id, '#listingDataSubSubmenu');
        }
        return false;
    },
    loadContain: function () {
        var $this = this;
        if ($.fn.DataTable.isDataTable('#listingData')) {
            $('#listingData').dataTable().fnReloadAjax($this.pageAjax + $this.endpoints['listing']);
        } else {
            // create table
            $this.loadTable($this.pageAjax + $this.endpoints['listing']);
        }
        return false;
    },
    loadButtonsTable: function () {
        var $this = this;
        // button refresh
        TableTools.BUTTONS.refresh = $.extend(true, {}, TableTools.buttonBase, {
            "sNewLine": "<br>",
            "sToolTip": "Atualizar todos " + $this.plural,
            "fnClick": function (nButton, oConfig) {
                $this.loadContain();
            }
        });
        TableTools.BUTTONS.refresh_submenu = $.extend(true, {}, TableTools.buttonBase, {
            "sNewLine": "<br>",
            "sToolTip": "Atualizar todos submenus do " + $this.plural,
            "fnClick": function (nButton, oConfig) {
                $this.loadSubmenuTable($("#listingDataSubmenu").data("id"));
            }
        });
        TableTools.BUTTONS.refresh_subsubmenu = $.extend(true, {}, TableTools.buttonBase, {
            "sNewLine": "<br>",
            "sToolTip": "Atualizar todos submenus do submenus do " + $this.plural,
            "fnClick": function (nButton, oConfig) {
                $this.loadSubSubmenuTable($("#listingDataSubSubmenu").data("id"));
            }
        });
    },
    selecionarColunas: function () {
        $('.checkall').on('click', function () {
            var $this = $(this).parents().parents().parents();
            if ($(this).is(':checked')) {
                $this.find('input[name="checkbox[]"]').prop('checked', true);
                $this.find('input[name="checkbox[]"]').parent().parent().parent().addClass('row_selected');
            } else {
                $this.find('input[name="checkbox[]"]').prop('checked', false);
                $this.find('input[name="checkbox[]"]').parent().parent().parent().removeClass('row_selected');
            }
        });
    },
    init: function () {
        this.selecionarColunas();
        this.loadButtonsTable();
        this.loadContain();
    }
}).init();