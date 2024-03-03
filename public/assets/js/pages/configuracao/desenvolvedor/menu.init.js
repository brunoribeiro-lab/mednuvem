/* global Swal */

var dev_doc = ({
    singular: "Menu",
    plural: "Menus",
    pageAjax: 'SGS/configuracoes/desenvolvedor/menus/',
    endpoints: {
        'listing': 'listar',
        'listing-submenus': 'listar-submenus',
        'listing-subsubmenus': 'listar-subsubmenus',
        'tabela-submenu': 'tabela-submenu',
        'tabela-subsubmenu': 'tabela-subsubmenu',
        'preview': 'ver',
        'preview-submenu': 'ver-submenu',
        'preview-subsubmenu': 'ver-subsubmenu',

        'get-update': 'edit',
        'update': 'edit',
        'get-update-submenu': 'edit-submenu',
        'update-submenu': 'edit-submenu',
        'get-update-subsubmenu': 'edit-subsubmenu',
        'update-subsubmenu': 'edit-subsubmenu'
    },
    resetView: function (level = 1) {

        switch (level) {
            case 1:
                var alvo_esconder = '#box-ajax, #box-ajax-level-2, #box-ajax-level-3';
                var alvo = '#box-listing';
                var tabela = "#listingData";
                var breadcrumb_padrao = "#action-breadcrumb, #action-level-3-last-breadcrumb, #last-breadcrumb";
                var breadcrumb_anterior = "#manager-breadcrumb";
                break;
            case 2:
                var alvo_esconder = '#box-ajax-level-2, #box-ajax-level-3';
                var alvo = '#box-ajax';
                var tabela = "#listingDataSubmenu";
                var breadcrumb_padrao = "#last-breadcrumb, #action-level-3-last-breadcrumb";
                var breadcrumb_anterior = "#action-breadcrumb";
                break;
            case 3:
                var alvo_esconder = '#box-ajax-level-3';
                var alvo = '#box-ajax-level-2';
                var tabela = "#listingDataSubSubmenu";
                var breadcrumb_padrao = "#action-level-3-last-breadcrumb";
                var breadcrumb_anterior = "#last-breadcrumb";
                break;
            default:
                var alvo_esconder = '#box-ajax';
                var alvo = '#box-listing';
                var tabela = "#listingData";
                var breadcrumb_padrao = "#action-breadcrumb";
                var breadcrumb_anterior = "#manager-breadcrumb";
                break;
        }

        $(alvo_esconder).addClass('hidden');
        $(alvo).removeClass('hidden');
        $(tabela + " > thead > tr > th").removeClass('hidden');
        $(tabela + " > tbody > tr > td").removeClass('hidden');
        $(tabela + " > thead > tr > th").attr('display', "block");
        $(tabela + " > tbody > tr > td").attr('display', "block");
        $(breadcrumb_padrao).html('');
        $(breadcrumb_padrao).addClass('hidden');
        $(breadcrumb_anterior).addClass('active');
        var html = $(breadcrumb_anterior).html();
        var resultado = html.match(/<a[^\b>]+>(.+)[\<]\/a>/);

        if (resultado && resultado[1]) {
            $(breadcrumb_anterior).html(resultado[1]);
        } else {
            $(breadcrumb_anterior).html(html);
        }
        if (level === 1)
            this.tabelaMenu();

        if (level === 2)
            this.tabelaSubMenu();

        if (level === 3)
            this.tabelaSubSubMenu();

    },
    backToListing: function () {
        var $this = this;
        $("#backTo").click(function () {
            $this.resetView();
        });
        $("#backTo2").click(function () {
            $this.resetView(2);
        });
        $("#backTo3").click(function () {
            $this.resetView(3);
        });
    },
    backToListingByBreadcrumb: function () {
        var $this = this;
        $('#manager-breadcrumb a').click(function () {
            $this.resetView();
        });
        $('#action-breadcrumb a').click(function () {
            $this.resetView(2);
        });
        $('#last-breadcrumb a').click(function () {
            $this.resetView(3);
        });
    },
    formDeAdd: function () {
        this.getContent(null, 'get-add', '#formModalAddNew', 'insert');
        $("#manager-breadcrumb").removeClass('active');
        $("#manager-breadcrumb").html("<a href='javascript:;' title='Clique aqui para voltar a listagem'>" + $("#manager-breadcrumb").html() + "</a>");
        $("#action-breadcrumb").html('<i class="fa fa-plus"></i> Adicionar ' + this.singular);
        $("#action-breadcrumb").removeClass('hidden');
        this.backToListingByBreadcrumb();
    },
    loadButtonsTable: function () {
        var $this = this;
        // button refresh
        TableTools.BUTTONS.refresh = $.extend(true, {}, TableTools.buttonBase, {
            "sNewLine": "<br>",
            "sToolTip": "Atualizar todos " + $this.plural,
            "fnClick": function (nButton, oConfig) {
                $this.tabelaMenu();
            }
        });
        TableTools.BUTTONS.refresh_submenu = $.extend(true, {}, TableTools.buttonBase, {
            "sNewLine": "<br>",
            "sToolTip": "Atualizar todos submenus do " + $this.plural,
            "fnClick": function (nButton, oConfig) {
                $this.tabelaSubMenu();
            }
        });
        TableTools.BUTTONS.refresh_subsubmenu = $.extend(true, {}, TableTools.buttonBase, {
            "sNewLine": "<br>",
            "sToolTip": "Atualizar todos submenus do submenus do " + $this.plural,
            "fnClick": function (nButton, oConfig) {
                $this.tabelaSubSubMenu();
            }
        });
    },
    getContent: function ($id, endpoint, form = null, updateURL = null, level = 1) {
        var $this = this;
        switch (level) {
            case 1:
                var alvo = '#box-ajax';
                var alvo_esconder = '#box-listing';
                break;
            case 2:
                var alvo = '#box-ajax-level-2';
                var alvo_esconder = '#box-ajax';
                break;
            case 3:
                var alvo = '#box-ajax-level-3';
                var alvo_esconder = '#box-ajax-level-2';
                break;
            default:
                var alvo = '#box-ajax';
                var alvo_esconder = '#box-listing';
                break;
        }

        $(alvo).removeClass('hidden');
        $(alvo_esconder).addClass('hidden');
        $(alvo).html('<div class="col-md-12 text-center"><p>Carregando...</p><div class="spinner-border text-primary m-1" role="status"><span class="sr-only">Carregando...</span></div></div>');
        var getEndpoint = this.pageAjax + this.endpoints[endpoint];
        if (parseInt($id))
            getEndpoint += "/" + $id;

        $.get(getEndpoint, function (html) {
            $(alvo).html(html);
            $this.backToListing();
            if (endpoint === 'tabela-submenu')
                $this.tabelaSubMenu(); // carregar submenus AJAX

            if (endpoint === 'tabela-subsubmenu')
                $this.tabelaSubSubMenu(); // carregar subsubmenus AJAX

            if (form) {
                $(form + ' .chosen-select').chosen({width: "100%", no_results_text: "Nada encontrado com : ", placeholder_text_single: "Selecione um Registro"});
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
                var callBack = function () {
                    if (level === 1)
                        $this.tabelaMenu();

                    if (level === 2)
                        $this.tabelaSubMenu();

                    if (level === 3)
                        $this.tabelaSubSubMenu();
                    
                    
                    $(alvo).addClass('hidden');
                    $(alvo_esconder).removeClass('hidden');
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
    tabelaMenu: function () {
        var $this = this;
        let buttons = [{
                "sExtends": "refresh",
                "sButtonText": "<i class=\"fas fa-sync\"></i>"
            }];
        if (!window.is_root) {
            buttons = [];
            buttons.push({
                "sExtends": "refresh",
                "sButtonText": "<i class=\"fas fa-sync\"></i>"
            });
        }
        var $order = [4, "ASC"];
        var $aoColumns = [
            {"bSortable": false, 'className': 'hidden-md hidden-lg hidden-sm'},
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
            '.goUpdate': function (e) {
                var $id = $(this).data('id');
                $this.getContent($id, 'get-update', '#formModalUpdate', 'update');
                $("#manager-breadcrumb").removeClass('active');
                $("#manager-breadcrumb").html("<a href='javascript:;' title='Clique aqui para voltar a listagem'>" + $("#manager-breadcrumb").html() + "</a>");
                $("#action-breadcrumb").html('<i class="fas fa-pencil-alt"></i> Editar ' + $this.singular);
                $("#action-breadcrumb").removeClass('hidden');
                $this.backToListingByBreadcrumb();
            },
            '.goSubmenu': function (e) {
                var $id = $(this).data('id');
                $this.getContent($id, 'tabela-submenu');
                $("#manager-breadcrumb").removeClass('active');
                $("#manager-breadcrumb").html("<a href='javascript:;' title='Clique aqui para voltar a listagem'>" + $("#manager-breadcrumb").html() + "</a>");
                $("#action-breadcrumb").html('<i class="far fa-list-alt"></i> Submenus do ' + $this.singular);
                $("#action-breadcrumb").removeClass('hidden');
                $this.backToListingByBreadcrumb();
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
    tabelaSubMenu: function () {
        var $this = this;
        let buttons = [{
                "sExtends": "refresh_submenu",
                "sButtonText": "<i class=\"fas fa-sync\"></i>"
            }];
        var $order = [4, "ASC"];
        var $aoColumns = [
            {"bSortable": false, 'className': 'hidden-md hidden-lg hidden-sm'},
            null,
            null,
            null,
            null,
            {"bSortable": false}
        ];
        var rowButtons = {
            '.goPreviewSubmenu': function (e) {
                var $id = $(this).data('id');
                $this.getContent($id, 'preview-submenu', null, null, 2);
                $("#action-breadcrumb").removeClass('active');
                $("#action-breadcrumb").html("<a href='javascript:;' title='Clique aqui para voltar a listagem'>" + $("#action-breadcrumb").html() + "</a>");
                $("#last-breadcrumb").html('<i class="fa fa-eye"></i> Detalhes do Submenu');
                $("#last-breadcrumb").removeClass('hidden');
                $this.backToListingByBreadcrumb();
            },
            '.goUpdateSubmenu': function (e) {
                var $id = $(this).data('id');
                $this.getContent($id, 'get-update-submenu', '#formModalUpdate', 'update-submenu', 2);
                $("#action-breadcrumb").removeClass('active');
                $("#action-breadcrumb").html("<a href='javascript:;' title='Clique aqui para voltar a listagem'>" + $("#action-breadcrumb").html() + "</a>");
                $("#last-breadcrumb").html('<i class="fas fa-pencil-alt"></i> Editar Submenu');
                $("#last-breadcrumb").removeClass('hidden');
                $this.backToListingByBreadcrumb();
            },
            '.goSubSubmenu': function (e) {
                var $id = $(this).data('id');
                $this.getContent($id, 'tabela-subsubmenu', null, null, 2);
                $("#action-breadcrumb").removeClass('active');
                $("#action-breadcrumb").html("<a href='javascript:;' title='Clique aqui para voltar a listagem'>" + $("#action-breadcrumb").html() + "</a>");
                $("#last-breadcrumb").html('<i class="far fa-list-alt"></i> Submenus do Submenu do ' + $this.singular);
                $("#last-breadcrumb").removeClass('hidden');
                $this.backToListingByBreadcrumb();
            },

        };
        hashTable.setTarget('#listingDataSubmenu')
                .setURL(this.pageAjax + this.endpoints['listing-submenus'] + "/" + $("#listingDataSubmenu").data("menu"))
                .setColumns($aoColumns)
                .setOrder($order)
                .setButtons(buttons)
                .setRowButtons(rowButtons)
                .init();
    },
    tabelaSubSubMenu: function () {
        var $this = this;
        let buttons = [{
                "sExtends": "refresh_subsubmenu",
                "sButtonText": "<i class=\"fas fa-sync\"></i>"
            }];
        var $order = [4, "ASC"];
        var $aoColumns = [
            {"bSortable": false, 'className': 'hidden-md hidden-lg hidden-sm'},
            null,
            null,
            null,
            null,
            {"bSortable": false}
        ];
        var rowButtons = {
            '.goPreviewSubSubmenu': function (e) {
                var $id = $(this).data('id');
                $this.getContent($id, 'preview-subsubmenu', null, null, 3);
                $("#last-breadcrumb").removeClass('active');
                $("#last-breadcrumb").html("<a href='javascript:;' title='Clique aqui para voltar a listagem'>" + $("#last-breadcrumb").html() + "</a>");
                $("#action-level-3-last-breadcrumb").html('<i class="fa fa-eye"></i> Detalhes do Submenu do Submenu');
                $("#action-level-3-last-breadcrumb").removeClass('hidden');
                $this.backToListingByBreadcrumb();
            },
            '.goUpdateSubSubmenu': function (e) {
                var $id = $(this).data('id');
                $this.getContent($id, 'get-update-subsubmenu', '#formModalUpdate', 'update-subsubmenu', 3);
                $("#last-breadcrumb").removeClass('active');
                $("#last-breadcrumb").html("<a href='javascript:;' title='Clique aqui para voltar a listagem'>" + $("#last-breadcrumb").html() + "</a>");
                $("#action-level-3-last-breadcrumb").html('<i class="fas fa-pencil-alt"></i> Editar Submenu do Submenu');
                $("#action-level-3-last-breadcrumb").removeClass('hidden');
                $this.backToListingByBreadcrumb();
            }
        };
        hashTable.setTarget('#listingDataSubSubmenu')
                .setURL(this.pageAjax + this.endpoints['listing-subsubmenus'] + "/" + $("#listingDataSubSubmenu").data("submenu"))
                .setColumns($aoColumns)
                .setOrder($order)
                .setButtons(buttons)
                .setRowButtons(rowButtons)
                .init();
    },
    init: function () {
        this.loadButtonsTable();
        this.tabelaMenu();
    }
}).init();