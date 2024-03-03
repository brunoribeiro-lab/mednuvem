/* global Swal */
var hashTable = ({
    target: '', // table ID
    url: '', // Full endpoint
    aoColumns: [], // columns
    order: [], // order default
    buttons: [], // datatable buttons
    rowButtons: [], // row buttons 
    sDom: "<'row'<'col-md-6'l T><'col-md-6'f>r>t<'row'<'col-md-12'p i>>",
    sInfo: "Mostrando de _START_ até _END_ de _TOTAL_ registros",
    setTarget: function (target) {
        this.target = target;
        return this;
    },
    setURL: function (url) {
        this.url = url;
        return this;
    },
    setColumns: function (aoColumns) {
        this.aoColumns = aoColumns;
        return this;
    },
    setOrder: function (order) {
        this.order = order;
        return this;
    },
    setButtons: function (buttons) {
        this.buttons = buttons;
        return this;
    },
    setRowButtons: function (rowButtons) {
        this.rowButtons = rowButtons;
        return this;
    },
    create: function () {
        var $this = this;
        $(this.target).dataTable({
            "language": {
                "sEmptyTable": "Nenhum registro encontrado",
                "sInfo": $this.sInfo,
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
            "sDom": $this.sDom,
            "oTableTools": {
                "aButtons": $this.buttons
            },
            "aoColumns": $this.aoColumns,
            "order": [$this.order],
            "bAutoWidth": false,
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": $this.url
        });
        $(this.target + '_wrapper .dataTables_filter input').addClass("input-medium ");
        $(this.target + '_wrapper .dataTables_length select').addClass("select2-wrapper span12");
        $(".select2-wrapper").chosen({
            "disable_search": true,
            "width": "55px"
        });
        $(this.target + ' tbody').on('click', 'input', function (e) {
            if ($(this).is(':checked')) {
                $(this).parent().parent().parent().addClass('row_selected');
            } else {
                $(this).parent().parent().parent().removeClass('row_selected');
            }
        });
        if (Object.keys(this.rowButtons).length > 0) {
            // row buttons
            $.each(this.rowButtons, function (k, v) {
                if (typeof v === "function" && k.charAt(0) === '.') {
                    $(this.target + ' tbody').on('click', k, v);
                }
            }.bind(this));
        }
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
        if ($.fn.DataTable.isDataTable(this.target)) {
            $(this.target).dataTable().fnReloadAjax(this.url);
            return true;
        }
        this.create(this.url, this.target);
        this.selecionarColunas();
        return true;
    }
});