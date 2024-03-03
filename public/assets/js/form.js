/* global Swal */
var hashForm = ({
    formAJAX: '',
    helper: function () {
        var $this = this;
        $(".input-help").click(function () {
            var index = $(this).data("index");
            $.get("SGS/AJAX/doc/" + index, function (json) {
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
    setarErros: function (json, form) {
        $(form + ' .form-control,' + form + ' .chosen-container,' + form + ' .cke').removeClass('is-invalid');
        let message = '';
        $(form + " .invalid-feedback").remove();
        var total_erros = 0;
        $.each(json.errors, function (k, v) {
            total_erros++;
            var k = k.replace('.', '');
            $(form + " #" + k).removeClass('is-valid').addClass('is-invalid');
            var proximo = $(form + " #" + k).next();
            var anterior = $(form + " #" + k).prev();
            if (anterior.is('div.cke')) {
                anterior.addClass('is-invalid');
                anterior.next().after('<div class="invalid-feedback">' + v + '</div>');
            } else if (proximo.is("div.input-group-append")) {
                $(form + " #" + k).next().after('<div class="invalid-feedback">' + v + '</div>');
            } else if (proximo.is("div.chosen-container")) {
                $(form + " #" + k).next().removeClass('is-valid').addClass('is-invalid');
                // exceção de chosen com botão agrupado a direita.
                if ($(form + " #" + k).next().next().hasClass('input-group-append')) {
                    $(form + " #" + k).next().next().after('<div class="invalid-feedback">' + v + '</div>');
                } else {
                    $(form + " #" + k).next().after('<div class="invalid-feedback">' + v + '</div>');
                }
            } else {
                $(form + " #" + k).after('<div class="invalid-feedback">' + v + '</div>');
            }
            message += v + ' <br>';
        });
        let msg = total_erros > 1 ? "Foi encontrado <strong>" + total_erros + "</strong> erros no formulário" : "Foi encontrado um erro no formulário";
        let title = "Erro ao Cadastrar";
        if (form == "#formModalUpdate")
            title = "Erro ao Salvar";

        this.message(msg, 'danger', title);
    },
    validate: function (form, file, callbackSucess, callBackErro, validation) {
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
                    if (validation() == false)
                        return false;
                    
                    let button = '<i class="far fa-save"></i> Salvar';
                    $(form + ' button[type=submit], input[type=submit]').removeClass("btn-success").addClass('btn-dark waves-effect waves-light');
                    $(form + ' button[type=submit], input[type=submit]').html('<i class="bx bx-hourglass bx-spin font-size-16 align-middle me-2"></i> Carregando');
                    $(form + " #loading").removeClass('hidden');
                    $(form).data('beenSubmitted', true);
                    var data = new FormData($(form)[0]);
                    $.ajax({
                        url: $this.formAJAX + file,
                        type: "POST",
                        data: data,
                        processData: false,
                        contentType: false,
                        dataType: "json",
                        success: function (data) {
                            $(form + ' .form-control,' + form + ' .chosen-container,' + form + ' .cke').removeClass('is-invalid');
                            $(form + ' button[type=submit], input[type=submit]').attr('disabled', false);
                            $(form + ' button[type=submit], input[type=submit]').removeClass('btn-dark waves-effect waves-light').addClass('btn-success');
                            $(form + ' button[type=submit], input[type=submit]').html(button);
                            $(form).data('beenSubmitted', false);
                            if (data.error) {
                                $this.message(data.msg, 'danger', 'Erro ao salvar');
                                callBackErro();
                            } else {
                                $this.message(data.msg, 'success', 'Salvo com sucesso');
                                callbackSucess(data);
                            }
                        },
                        error: function (data) {
                            if (data.status == 422) {
                                var json = data.responseJSON;
                                $this.setarErros(json, form);

                            } else {
                                $this.message(data.responseText, 'danger');
                            }
                            $(form + ' button[type=submit], input[type=submit]').attr('disabled', false);
                            $(form + ' button[type=submit], input[type=submit]').removeClass('btn-dark waves-effect waves-light').addClass('btn-success');
                            $(form + ' button[type=submit], input[type=submit]').html(button);
                            $(form).data('beenSubmitted', false);
                        },
                        beforeSend: function () {
                            // reseta os alertas campos antes de fazer o AJAX
                            $(form + ' .form-control,' + form + ' .chosen-container,' + form + ' .cke').removeClass('is-invalid');
                        },
                        complete: function () {
                            $(form + ' button[type=submit], input[type=submit]').attr('disabled', false);
                            $(form + ' button[type=submit], input[type=submit]').removeClass('btn-dark waves-effect waves-light').addClass('btn-success');
                            $(form + ' button[type=submit], input[type=submit]').html(button);
                            $(form).data('beenSubmitted', false);
                            $(form + " #loading").addClass('hidden');
                        }
                    });
                    return false;
                }
            }
        });
    },
    init: function (ajax, form, file, callbackSucess = function() {}, callBackErro = function(){}, validation = function(){}) {
        this.formAJAX = ajax;
        this.validate(form, file, callbackSucess, callBackErro, validation);
        this.helper();
    }
});