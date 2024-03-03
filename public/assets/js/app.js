!function (a) {
    "use strict";
    var e, t, n, o = localStorage.getItem("minia-language"), r = "en";

    $('#modal-assistir-aula').on('shown.bs.modal', function (event) {
        $('#modal-assistir-aula').find('.modal-body').html('<div class="col-md-12 text-center"><p>Carregando...</p><div class="spinner-border text-primary m-1" role="status"><span class="sr-only">Carregando...</span></div></div>');
        var id = event.relatedTarget.dataset.video;
        $('#modal-assistir-aula').find('.modal-body').html('<iframe width="100%" height="515" src="https://www.youtube.com/embed/' + id + '?autoplay=1&origin=https://hashbr.com" title="Assitir Aula" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>');
    });
    $('#modal-assistir-aula').on('hidden.bs.modal', function (e) {
        $("#modal-assistir-aula .modal-body").html("");
    });
    $("#btn-videos").click(function () {
        window.location.href = "SGS/videos-aulas";
    });
    $('.bs-video-modal-xl').on('shown.bs.modal', function (e) {
        var path = e.relatedTarget.dataset.path;
        var title = e.relatedTarget.dataset.title;
        $(".bs-video-modal-xl .modal-body .centered-block").html('<iframe width="100%" height="515" src="https://www.youtube.com/embed/' + path + '?autoplay=1&origin=https://hashbr.com" title="' + title + '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>');
        $(".bs-video-modal-xl").find(".modal-title").html(title);
    });
    $('.bs-video-modal-xl').on('hidden.bs.modal', function (e) {
        console.log("hidden");
        $(".bs-video-modal-xl .modal-body .centered-block").html("");
    });
    let searching = false;
    if ($("#specificSeachVideo").length > 0) {
        $("#specificSeachVideo").on("submit", function () {
            if (searching)
                return;

            searching = true;
            var text = $(this).val();
            console.log(text);
            $.post("SGS/videos-aulas", $(this).serialize(), function (html) {
                $("#content-videos").html(html);
                searching = false;
            });
        });

    }
    $(".submenu-item").click(function () {
        if ($(window).innerWidth() < 992) {
            if (!$(this).find(".dropdown-menu").hasClass("menu-block")) {
                $(this).find(".dropdown-menu").addClass('menu-block');
            } else {
                $(this).find(".dropdown-menu").removeClass('menu-block');
            }
        }
    });
    // show theme customizer
    $(".right-bar-toggle").on("click", function () {
        $("body").toggleClass("right-bar-enabled")
    });
    // change to dark mode
    $("#mode-setting-btn").click(function () {
        var $default = $("body").attr("data-layout-mode");
        if (typeof $default == "undefined" || $default == "light") {
            $("body").attr("data-layout-mode", "dark");
            $("body").attr("data-layout-topbar", "dark");
            $("body").attr("data-topbar", "dark");
            $("body").attr("data-layout-sidebar", "dark");
            $("body").attr("data-sidebar", "dark");
            $.get("SGS/AJAX/thema", {'theme': 'dark'}, function (json) {
                if (json.error) {
                    Swal.fire({
                        title: 'Erro ao mudar tema',
                        html: json.msg,
                        icon: 'error',
                        timer: 8000,
                        confirmButtonText: 'Fechar',
                        showCancelButton: false,
                        confirmButtonColor: "#5156be",
                        cancelButtonColor: "#fd625e"
                    }).then((result) => {
                        if (result.dismiss === Swal.DismissReason.timer) {

                        }
                    });
                }
            }, "JSON");
        } else {
            $("body").attr("data-layout-mode", "light");
            $("body").attr("data-layout-topbar", "light");
            $("body").attr("data-topbar", "light");
            $("body").attr("data-layout-sidebar", "light");
            $("body").attr("data-sidebar", "light");
            $.get("SGS/AJAX/thema", {'theme': 'light'}, function (json) {
                if (json.error) {
                    Swal.fire({
                        title: 'Erro ao mudar tema',
                        html: json.msg,
                        icon: 'error',
                        timer: 8000,
                        confirmButtonText: 'Fechar',
                        showCancelButton: false,
                        confirmButtonColor: "#5156be",
                        cancelButtonColor: "#fd625e"
                    }).then((result) => {
                        if (result.dismiss === Swal.DismissReason.timer) {

                        }
                    });
                }
            }, "JSON");
        }
    });
    // change layout position to fixed
    $("#layout-position-fixed").click(function () {
        $("body").attr("data-layout-scrollable", "false");
        $.cookie('theme_menu_pos', "fixed", {path: '/'});
    });
    // change layout position to scrollable
    $("#layout-position-scrollable").click(function () {
        $("body").attr("data-layout-scrollable", "true");
        $.cookie('theme_menu_pos', "scroll", {path: '/'});
    });
    // set layout width like fuid
    $("#layout-width-fuild").click(function () {
        $("body").attr("data-layout-size", "fluid");
        $.cookie('theme_width', "fluid", {path: '/'});
    });
    // set layout width like boxed
    $("#layout-width-boxed").click(function () {
        $("body").attr("data-layout-size", "boxed");
        $.cookie('theme_width', "boxed", {path: '/'});
    });

    // header color light
    $("#topbar-color-light").click(function () {
        $("body").attr("data-topbar", "light");
        $.cookie('theme_color', "light", {path: '/'});
    });
    // header color dark
    $("#topbar-color-dark").click(function () {
        $("body").attr("data-topbar", "dark");
        $.cookie('theme_color', "dark", {path: '/'});
    });
    // set sidebar size like default
    $("#sidebar-size-default").click(function () {
        $("body").attr("data-sidebar-size", "lg");
        $.cookie('theme_sidebar', "lg", {path: '/'});
    });
    // set sidebar size like compact
    $("#sidebar-size-compact").click(function () {
        $("body").attr("data-sidebar-size", "md");
        $.cookie('theme_sidebar', "md", {path: '/'});
    });
    // set sidebar size like small
    $("#sidebar-size-small").click(function () {
        $("body").attr("data-sidebar-size", "sm");
        $.cookie('theme_sidebar', "sm", {path: '/'});
    });
    // set horizontal menu
    $("#layout-horizontal").click(function () {
        $("#menu-vertical").addClass('hidden');
        $("#menu-horizontal").removeClass('hidden');
        $("body").attr("data-layout", "horizontal");
        $.cookie('theme_menu', "horizontal", {path: '/'});
    });
    // set vertical menu
    $("#layout-vertical").click(function () {
        $("#menu-horizontal").addClass('hidden');
        $("#menu-vertical").removeClass('hidden');
        $("body").attr("data-layout", "vertical");
        $.cookie('theme_menu', "vertical", {path: '/'});
    });
    function verificarSeCodEValido() {
        $('#form-buscar-pagina input[name="cod"]').on('input', function () {
            var code = $(this).val();
            if (code.length !== 3) {
                $(this).removeClass('input-valid').addClass('input-invalid');
                return false;
            }
            $(this).removeClass('input-invalid').addClass('input-valid');
        });
    }
    $('.onlyNumber').keydown(function (e) {
        // Array contendo os códigos das teclas permitidas
        var allowedKeys = [
            8, // Backspace
            13, // Enter
            46, // Delete
            67, // Ctrl + C
            86, // Ctrl + V
            35, // End
            36, // Home
            37, // Seta para a esquerda
            38, // Seta para cima
            39, // Seta para a direita
            40, // Seta para baixo
            48, // 0 (teclado do topo)
            49, // 1 (teclado do topo)
            50, // 2 (teclado do topo)
            51, // 3 (teclado do topo)
            52, // 4 (teclado do topo)
            53, // 5 (teclado do topo)
            54, // 6 (teclado do topo)
            55, // 7 (teclado do topo)
            56, // 8 (teclado do topo)
            57, // 9 (teclado do topo)
            96, // 0 (teclado numérico)
            97, // 1 (teclado numérico)
            98, // 2 (teclado numérico)
            99, // 3 (teclado numérico)
            100, // 4 (teclado numérico)
            101, // 5 (teclado numérico)
            102, // 6 (teclado numérico)
            103, // 7 (teclado numérico)
            104, // 8 (teclado numérico)
            105   // 9 (teclado numérico)
        ];

        if (e.shiftKey || e.altKey) {
            // Impede o funcionamento de Shift + qualquer tecla e Alt + qualquer tecla
            e.preventDefault();
        } else {
            var n = e.keyCode;
            if (!allowedKeys.includes(n)) {
                // Impede todas as teclas, exceto aquelas listadas na array allowedKeys
                e.preventDefault();
            }
        }
    });
    $("#form-buscar-pagina").on("submit", function () {
        var code = $('#form-buscar-pagina input[name="cod"]').val();
        if (code.length !== 3) {
            $('#form-buscar-pagina input[name="cod"]').removeClass('input-valid').addClass('input-invalid');
            verificarSeCodEValido();
            return false;
        }
        $.post("SGS/AJAX/pagina", $("#form-buscar-pagina").serialize(), function (json) {
            if (json.error) {
                Swal.fire({
                    title: 'Não encontrado',
                    html: json.msg,
                    icon: 'error',
                    timer: 8000,
                    confirmButtonText: 'Fechar',
                    showCancelButton: false,
                    confirmButtonColor: "#5156be",
                    cancelButtonColor: "#fd625e"
                }).then((result) => {
                    if (result.dismiss === Swal.DismissReason.timer) {

                    }
                });
            } else {
                window.location.href = json.url;
            }
        }, "JSON");
    }); 
}(jQuery);