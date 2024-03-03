var configuracoes_logos = ({
    pageAjax: 'SGS/configuracoes/desenvolvedor/logos/',
    loadImage: function ($target, file) {
        var allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if ($target === "#image_icon") {
            allowed = ['ico', 'png'];
        } else if ($target === '#loading') {
            allowed = ['png', 'gif'];
        }
        $($target).fileinput({
            initialPreview: "<img style=\"height:140px; width:100%\" src=\"" + file + "\">",
            'previewFileType': "image",
            'showUpload': false,
            'browseClass': "btn btn-success",
            'removeClass': "btn btn-danger",
            'removeIcon': '<i class="fa fa-trash"></i>',
            'language': 'pt-BR',
            'browseIcon': '<i class="fa fa-image"></i>',
            'allowedFileExtensions': allowed,
            'elErrorContainer': '#errorBlock'
        });
    },
    campos: function () {
        $('select[name="total_notificacoes"]').change(function () {
            var val = parseInt($(this).val());
            if (!val) {
                $(".box-notificacao").addClass('hidden');
                $("#box-todas-notificacoes").addClass('hidden');
                return;
            }
            $("#box-todas-notificacoes").removeClass('hidden');
            for ($i = 1; $i <= 10; $i++) {
                if ($i <= val) {
                    $("#box-notificacao-" + $i).removeClass('hidden');
                } else {
                    $("#box-notificacao-" + $i).addClass('hidden');
                }
                // remover o separador do último elemento (TOC)
                if ($i === val) {
                    $("#box-notificacao-separador-" + $i).addClass('hidden');
                } else if ($i < val) {
                    $("#box-notificacao-separador-" + $i).removeClass('hidden');
                }
            }
        });
        $('.select-ambient').chosen({"width": "100%", minimumResultsForSearch: -1});
        $('.onlyNumber').keydown(function (e) {
            if (e.shiftKey || e.ctrlKey || e.altKey) { // if shift, ctrl or alt keys held down
                e.preventDefault();         // Prevent character input
            } else {
                var n = e.keyCode;
                if (!((n == 8)              // backspace
                        || (n == 46)                // delete
                        || (n >= 35 && n <= 40)     // arrow keys/home/end
                        || (n >= 48 && n <= 57)     // numbers on keyboard
                        || (n >= 96 && n <= 105))   // number on keypad
                        ) {
                    e.preventDefault();     // Prevent character input
                }
            }
        });
        $("#btn-document").click(function () {
            $("#document-document").trigger('click');
        });
        $("#document-document").change(function () {
            var file = $(this)[0].files[0].name;
            $("#btn-document").html('<i class="fa fa-file-pdf"></i> ' + file);
        });
    },
    init: function () {
        this.campos();
        hashForm.init(this.pageAjax, '#formUpdate', 'salvar', function () {
            $("#loading").addClass('hidden');
        });
        this.loadImage("#image_logo", logo);
        this.loadImage("#image_icon", favoicon);
        this.loadImage("#image_responsive", responsive);
        this.loadImage("#image_email", email);

    }
}).init();
