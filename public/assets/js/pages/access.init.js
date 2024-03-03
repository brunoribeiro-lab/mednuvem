var helper = function () {
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
                message("Ocorreu um erro ao obter a documentação desse campo.", 'danger', "Erro ao obter documentação");
            }
        }, "JSON");
    });
};
$(document).ready(function () {
    /* global zipCode, pageAjax */
    var pageAjax = 'AJAX/Settings/';
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

    function backToListingByBreadcrumb() {
        $('#manager-breadcrumb a').click(function () {
            $("#last-breadcrumb").addClass('hidden');
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
            loadContain();
            if (window.location.hash)
                window.location.hash = '';

            // mudar código do formulário de adicionar
            if ($("#UII").data("add").length > 0) {
                $("#UII").val($("#UII").data("default"));
                $("#UII").attr("title", $("#UII").data("default"));
            }
        });
    }
    function backToListing() {
        $("#backTo").click(function () {
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
            loadContain();
            if (window.location.hash)
                window.location.hash = '';

            // mudar código do formulário de adicionar
            if ($("#UII").data("add").length > 0) {
                $("#UII").val($("#UII").data("default"));
                $("#UII").attr("title", $("#UII").data("default"));
            }
        });
    }
    function message(msg, type, title) {
        let icon = 'error';
        if (type === 'success') {
            icon = 'success';
        }
        Swal.fire({
            title: title,
            html: msg,
            icon: icon,
            confirmButtonText: 'Fechar',
            showCancelButton: false,
            confirmButtonColor: "#5156be",
            cancelButtonColor: "#fd625e"
        });
    }
    var singular = "Cargo";
    var plural = "Cargos";
    // function to load or reload table
    var loadContain = function () {
        $("#reload_info").click(function () {
            loadContain();
        });
        if ($.fn.DataTable.isDataTable('#listingData')) {
            $('#listingData').dataTable().fnReloadAjax(pageAjax + 'YWNjZXNzL2xpc3RpbmcK');
        } else {
            // create table
            load_table(pageAjax + 'YWNjZXNzL2xpc3RpbmcK');
        }
        return false;
    };
    // function to create table
    var load_table = function (path) {
        var tableElement = $('#listingData');
        var buttons = [{
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
                {"bSortable": false},
                null,
                null,
                null,
                {"bSortable": false}
            ],
            "order": [
                [2, "desc"]
            ],
            "bAutoWidth": false,
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": path
        });
        $('#listingData_wrapper .dataTables_filter input').addClass("input-medium ");
        $('#listingData_wrapper .dataTables_length select').addClass("select2-wrapper span12");
        $(".select2-wrapper").chosen({
            "disable_search": true,
            "width": "55px"
        });

        $('#listingData tbody').on('click', '.goUpdate', function (e) {
            var $id = $(this).data('id');
            loadModalContent("YWNjZXNzL3VwZGF0ZQo=", "YWNjZXNzL2dldFVwZGF0ZQo=", "#formModalUpdate", $id);
            $("#manager-breadcrumb").removeClass('active');
            $("#manager-breadcrumb").html("<a href='javascript:;' title='Clique aqui para voltar a listagem'>" + $("#manager-breadcrumb").html() + "</a>");
            $("#action-breadcrumb").html('<i class="fas fa-pencil-alt"></i> Editar ' + singular);
            $("#action-breadcrumb").removeClass('hidden');
            backToListingByBreadcrumb();
        });


        $('#listingData tbody').on('click', '.goPreview', function (e) {
            var $id = $(this).data('id');
            loadModalContent("", "YWNjZXNzL3ByZXZpZXcK", "#formModalUpdate", $id);
            $("#manager-breadcrumb").removeClass('active');
            $("#manager-breadcrumb").html("<a href='javascript:;' title='Clique aqui para voltar a listagem'>" + $("#manager-breadcrumb").html() + "</a>");
            $("#action-breadcrumb").html('<i class="fa fa-eye"></i> Detalhes da ' + singular);
            $("#action-breadcrumb").removeClass('hidden');
            backToListingByBreadcrumb();
        });


        $('#listingData tbody').on('click', '.goAccess', function (e) {
            var $id = $(this).data('id');
            loadModalContent("YWNjZXNzL3VsaXN0Cg==", "YWNjZXNzL2xpc3QK", "#formAccessByAccountType", $id);
            $("#manager-breadcrumb").removeClass('active');
            $("#manager-breadcrumb").html("<a href='javascript:;' title='Clique aqui para voltar a listagem'>" + $("#manager-breadcrumb").html() + "</a>");
            $("#action-breadcrumb").html('<i class="fa fa-key"></i> Acessos do ' + singular);
            $("#action-breadcrumb").removeClass('hidden');
            backToListingByBreadcrumb();
        });

        $('#listingData tbody').on('click', '.goRem', function (e) {
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
                        url: pageAjax + "YWNjZXNzL3JlbW92ZQo=",
                        type: "POST",
                        data: {"checkbox[]": $id},
                        dataType: "json",
                        success: function (data) {
                            if (data.error) {
                                Swal.fire({
                                    title: data.msg,
                                    icon: 'error',
                                    confirmButtonText: 'Fechar',
                                    showCancelButton: false
                                });
                            } else {
                                loadContain();
                                Swal.fire({
                                    title: data.msg,
                                    icon: 'success',
                                    confirmButtonText: 'Fechar',
                                    showCancelButton: false
                                });
                            }
                        },
                        error: function (data) {
                            Swal.fire({
                                title: data.responseText,
                                icon: 'error',
                                confirmButtonText: 'Fechar',
                                showCancelButton: false
                            });
                        },
                    });

                }
            });
        });

        $('#listingData tbody').on('click', 'input', function (e) {
            if ($(this).is(':checked')) {
                $(this).parent().parent().parent().addClass('row_selected');
            } else {
                $(this).parent().parent().parent().removeClass('row_selected');
            }
        });
    };
    var formDeAdd = function () {
        loadModalContent("YWNjZXNzL2luc2VydAo=", "YWNjZXNzL2dldEluc2VydAo=", "#formModalAddNew", true);
        $("#manager-breadcrumb").removeClass('active');
        $("#manager-breadcrumb").html("<a href='javascript:;' title='Clique aqui para voltar a listagem'>" + $("#manager-breadcrumb").html() + "</a>");
        $("#action-breadcrumb").html('<i class="fa fa-plus"></i> Adicionar ' + singular);
        $("#action-breadcrumb").removeClass('hidden');
        backToListingByBreadcrumb();
        // mudar código do formulário de adicionar
        if ($("#UII").data("add").length > 0) {
            $("#UII").val($("#UII").data("add"));
            $("#UII").attr("title", $("#UII").data("add"));
        }
    };
    // function to load buttons from table
    var loadButtonsTable = function () {
        // button add new
        TableTools.BUTTONS.add_new = $.extend(true, {}, TableTools.buttonBase, {
            "sNewLine": "<br>",
            "sToolTip": "Adicionar Novo " + singular,
            "fnClick": function (nButton, oConfig) {
                formDeAdd();
            }
        });
        // button refrash
        TableTools.BUTTONS.refresh = $.extend(true, {}, TableTools.buttonBase, {
            "sNewLine": "<br>",
            "sToolTip": "Atualizar todos " + plural,
            "fnClick": function (nButton, oConfig) {
                loadContain();
            }
        });
        // button multiple delete
        TableTools.BUTTONS.delete_broadcast = $.extend(true, {}, TableTools.buttonBase, {
            "sNewLine": "<br>",
            "sToolTip": "Deletar múltiplos " + plural,
            "fnClick": function (nButton, oConfig) {
                var count_checked = $("[name='checkbox[]']:checked").length;
                if (!count_checked) {
                    var msg = "<strong>Nenhum " + singular + " foi selecionado</strong> <br> Você precisa selecionar pelo menos um " + singular + " para prosseguir com a multi-exclusão.";
                    message(msg, 'danger', "Erro ao excluir");
                } else {
                    var msg = !(count_checked > 1) ? 'Tem certeza que deseja excluir o ' + singular + ' ? <br> <strong>será excluído um ' + singular + '</strong>' : 'Tem certeza que deseja excluir os ' + plural + ' ? <br> <strong>serão excluídos ' + count_checked + ' ' + plural + ' </strong';
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
                                url: pageAjax + "YWNjZXNzL3JlbW92ZQo=",
                                type: "POST",
                                data: $("#multiple_delete").serialize(),
                                dataType: "json",
                                success: function (data) {
                                    if (data.error) {
                                        Swal.fire({
                                            title: data.msg,
                                            icon: 'error',
                                            confirmButtonText: 'Fechar',
                                            showCancelButton: false
                                        });
                                    } else {
                                        loadContain();
                                        Swal.fire({
                                            title: data.msg,
                                            icon: 'success',
                                            confirmButtonText: 'Fechar',
                                            showCancelButton: false
                                        });
                                    }
                                },
                                error: function (data) {
                                    Swal.fire({
                                        title: data.responseText,
                                        icon: 'error',
                                        confirmButtonText: 'Fechar',
                                        showCancelButton: false
                                    });
                                }
                            });

                        }
                    });
                }
            }
        });
    };
    // function to validate form
    var validateForm = function (form, file) {
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
                        url: pageAjax + file,
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
                                message(data.msg, 'danger');
                            } else {
                                $("#box-ajax").addClass('hidden');
                                $("#box-listing").removeClass('hidden');
                                loadContain();
                                message(data.msg, 'success');
                                $("#action-breadcrumb").html('');
                                $("#action-breadcrumb").addClass('hidden');
                                $("#manager-breadcrumb").addClass('active');
                                var html = $("#manager-breadcrumb").html();
                                var text = html.match(/<a[^\b>]+>(.+)[\<]\/a>/)[1];
                                $("#manager-breadcrumb").html(text);
                            }
                        },
                        error: function (data) {
                            message(data.responseText, 'danger');
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
    };
    // function to load user info in modal
    var loadUser = function () {
        $('#myModalPrevUser').on('show.bs.modal', function (e) {
            $("#myModalPrevUser button[name*='modal_button_print']").attr('disabled', false);
            var myID = $(e.relatedTarget).data('id');
            $.post('AJAX/Settings/dXNlci9wcmV2aWV3Cg==', {'id': myID}, function (html) {
                $('#myModalPrevUser .form-row').html(html);
            });
        });
        // preview pic in large resolution
        $('#myModalPrevPic').on('show.bs.modal', function (e) {
            var name = $(e.relatedTarget).data('name');
            var fullPath = $(e.relatedTarget).data('path');
            $("#nameCustomerModalPrevPic").html(name);
            $("#avatarCustomerModalPrevPic").attr('src', fullPath);
            $("#avatarCustomerModalPrevPic").attr('title', name);
        });
    };
    // function to load modal content and load fields
    var loadModalContent = function (updateURL, getURL, form, id) {
        id = typeof id !== 'undefined' ? id : false;
        $("#box-ajax").removeClass('hidden');
        $("#box-listing").addClass('hidden');
        $('#box-ajax').html('<div class="col-md-12 text-center"><p>Carregando...</p><div class="spinner-border text-primary m-1" role="status"><span class="sr-only">Carregando...</span></div></div>');
        var $param = {};
        if (id > 0) {
            $param = {"id": id};
        }
        $(form).data('beenSubmitted', false);
        $(form + ' button[type=submit], input[type=submit]').attr('disabled', false);
        $.post(pageAjax + getURL, $param, function (html) {
            $("#box-ajax").html(html);
            helper();
            backToListing();
            validateForm(form, updateURL);
        });
    };
    //main function to initiate template pages
    loadButtonsTable(); // load table buttons
    loadContain(); // load table
    loadUser(); // preview user
    if (window.location.hash) {
        var hash = window.location.hash.substring(1);
        if (hash == "add") {
            formDeAdd();
        }
    }
});