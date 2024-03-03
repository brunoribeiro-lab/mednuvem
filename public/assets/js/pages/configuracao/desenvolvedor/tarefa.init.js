/* global Swal */

var dev_doc = ({
    singular: "Tarefa",
    plural: "Tarefas",
    pageAjax: 'SGS/configuracoes/desenvolvedor/tarefas/',
    endpoints: {
        'listing': 'listar'
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
        this.tabelaTarefas();
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
    loadButtonsTable: function () {
        var $this = this;
        // button refresh
        TableTools.BUTTONS.refresh = $.extend(true, {}, TableTools.buttonBase, {
            "sNewLine": "<br>",
            "sToolTip": "Atualizar todas " + $this.plural,
            "fnClick": function (nButton, oConfig) {
                $this.tabelaTarefas();
            }
        });
    },
    tabelaTarefas: function () {
        var $this = this;
        let buttons = [{
                "sExtends": "refresh",
                "sButtonText": "<i class=\"fas fa-sync\"></i>"
            }];

        var $order = [1, "desc"];
        var $aoColumns = [
            {"bSortable": false, 'className': 'hidden-md hidden-lg hidden-sm'},
            null,
            {"bSortable": false},
            null,
            null,
        ];
        hashTable.setTarget('#listingData')
                .setURL(this.pageAjax + this.endpoints['listing'])
                .setColumns($aoColumns)
                .setOrder($order)
                .setButtons(buttons)
                .init();
    },
    init: function () {
        this.loadButtonsTable();
        this.tabelaTarefas();
    }
}).init();