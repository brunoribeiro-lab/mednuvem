@php
use \App\Providers\ThemaProvider
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $systemVariables->nome }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <base href="{{ config('app.url') }}">
        <!-- preloader css -->
        <link rel="stylesheet" href="assets/css/preloader.min.css" type="text/css" />
        <!-- Sweet Alert-->
        <link href="assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
        <!-- Bootstrap Css -->
        <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <!-- Select2 -->
        <link href="assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <!-- Plugins Css-->
        <link rel="stylesheet" href="assets/libs/jquery-chosen/chosen.min.css">
        <!-- App Css-->
        <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
        <link href="assets/css/home.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/Utils.min.css" rel="stylesheet" type="text/css" />
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset(sprintf("storage/theme/%s",$systemVariables->favoicon)) }}">
        <link rel="icon" type="image/x-icon" href="{{ asset(sprintf("storage/theme/%s",$systemVariables->favoicon)) }}">
    </head>
    <body data-sidebar-size="{{ThemaProvider::tamanho()}}" data-layout-mode="{{ThemaProvider::sidebar()}}" data-topbar="{{ThemaProvider::sidebar()}}" data-sidebar="{{ThemaProvider::sidebar()}}" data-topbar="{{ThemaProvider::sidebar()}}" data-layout="horizontal">
        <!-- Begin page -->
        <div id="layout-wrapper">
            @include('_template/header')
            <div class="main-content">
                <div class="page-content">
                    <div class="container-fluid">
                        <form method="post" action="javascript:;" id="specificSeachVideo">
                            @csrf
                            <div class="col-sm-12 margin-bottom-15">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="keyword" placeholder="Pesquisar Vídeo Aula..." value="<?php print Request::get("keyword"); ?>">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" title="Buscar vídeo aula" type="submit"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="row" id="content-videos">
                            @foreach ($videos as $i => $video)
                            <div class="col-xl-6 col-lg-6 col-sm-12 col-xs-12">
                                <div class="card">
                                    <div class="row g-0 align-items-center">
                                        <div class="col-md-4">
                                            <img class="card-img img-fluid" src="storage/videos/{{ $video->thumbmail }}" alt="Card image">
                                        </div>
                                        <div class="col-md-8">
                                            <div class="card-body">
                                                <h5 class="card-title">{{ $video->title }}</h5>
                                                <p class="card-text"><?php print str_replace(["<div>", "</div>"], ["", ""], strlen($video["description"]) > 250 ? Str::limit($video["description"], 200 - 3) : $video["description"]); ?></p>
                                                <p class="card-text"><button class="btn btn-primary" type="button"  data-bs-toggle='modal' data-bs-target='.bs-video-modal-xl' data-title="<?php print $video["title"]; ?>" data-path="<?php print App\Providers\Utils::extractID($video["youtube"]) ?>"><i class="fa fa-eye"></i> Assistir Tutorial</button></p>
                                                <p class="card-text"><small class="text-muted">Atualizado {{ date("d/m/y", strtotime($video["updated_at"])) }}</small></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- end col -->
                            @endforeach
                            {{ $videos->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                    <!-- End Page-content -->
                </div>
                <!-- end main content-->
            </div>
            <!-- End Page-content -->
            @include('_template/footer')
            <!-- END layout-wrapper -->
        </div>
        <div class="modal fade bs-video-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="myExtraLargeModalLabel">Foto Ampliada</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-md-12  text-center">
                            <div class="centered-block">
                            </div>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </body>
    <!-- JAVASCRIPT -->
    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/jquery/jquery.cookie.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>
    <script src="assets/libs/jquery-chosen/chosen.jquery.js"></script>
    <script src="assets/libs/vendor/jquery.mask.min.js"></script>
    <script src="assets/libs/bootstrap-switch/dist/js/bootstrap-switch.min.js"></script>
    <script src="assets/libs/validation/jquery.validate.min.js"></script>
    <!-- pace js -->
    <script src="assets/libs/pace-js/pace.min.js"></script>
    <!-- twitter-bootstrap-wizard js -->
    <script src="assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>
    <script src="assets/libs/twitter-bootstrap-wizard/prettify.js"></script>
    <!-- Plugins js-->
    <script src="assets/libs/vendor/moment.min.js"></script>
    <script src="assets/libs/vendor/moment-timezones.min.js"></script>
    <!-- Sweet Alerts js -->
    <script src="assets/libs/sweetalert2/sweetalert2.min.js"></script>
    <!-- theme init -->
    <script src="assets/js/app.js"></script>
</html>
