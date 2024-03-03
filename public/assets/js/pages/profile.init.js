var perfil = ({
    pageAjax: 'SGS/perfil/',
    campos: function () {
        $(".Switch").bootstrapSwitch();
        $('#c_pass').on('switchChange.bootstrapSwitch', function (event, state) {
            if (state) {
                $(".change_password").removeClass("hidden");
            } else {
                $(".change_password").addClass("hidden");
            }
        });
        $("#user_avatar").fileinput({
            "initialPreview": ["\u003Cimg class=\u0022file-preview-image\u0022 src='" + avatar + "' alt='" + fname + "' title='" + fname + "'"],
            "initialCaption": name,
            "overwriteInitial": true,
            'previewFileType': "image",
            'showUpload': false,
            'browseClass': "btn btn-success",
            'removeClass': "btn btn-danger",
            'removeIcon': '<i class="fa fa-trash"></i>',
            'language': 'pt-BR',
            'browseIcon': '<i class="fa fa-image"></i>',
            'allowedFileExtensions': ['jpg', 'jpeg', 'png', 'gif'],
            'elErrorContainer': '#errorBlock'
        });
    },
    init: function () {
        this.campos();
        hashForm.init(this.pageAjax, '#formUpdate', 'salvar', function () {
            $("#loading").addClass('hidden');
        });
    }
}).init();
