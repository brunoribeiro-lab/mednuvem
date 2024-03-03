@php
use \App\Providers\ThemaProvider;
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{$data['subsubmenu']["NAME"] }} :: {{$data['submenu']["NAME"] }} :: {{$data['menu']["NAME"] }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <base href="{{ config('app.url') }}">
        @include('_template.libs.css.theme') 
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/libs/kartik-v-bootstrap-fileinput/css/fileinput.min.css') }}" />
    </head>
    <body data-sidebar-size="{{ThemaProvider::tamanho()}}" data-layout-mode="{{ThemaProvider::sidebar()}}" data-topbar="{{ThemaProvider::sidebar()}}" data-sidebar="{{ThemaProvider::sidebar()}}" data-topbar="{{ThemaProvider::sidebar()}}" data-layout="horizontal">
        <!-- Begin page -->
        <div id="layout-wrapper">
            @include('_template/header')
            <div class="main-content">
                <div class="page-content">
                    <div class="container-fluid">
                        <div class="card">
                            <div class="card-header align-items-center d-flex">
                                <ol class="breadcrumb m-0 p-0" id="main-box-title">
                                    <li class="breadcrumb-item"><a href="javascript: void(0);"><i class="<?php print $data['menu']["ICON"]; ?>"></i> <?php print $data['menu']["NAME"]; ?></a></li>
                                    <li class="breadcrumb-item"><i class="<?php print $data['submenu']["ICON"]; ?>"></i> <?php print $data['submenu']["NAME"]; ?></li>
                                    <li class="breadcrumb-item active" id="manager-breadcrumb"><i class="<?php print $data['subsubmenu']["ICON"]; ?>"></i> <?php print $data['subsubmenu']["NAME"]; ?></li>
                                    <li class="breadcrumb-item active hidden" id="action-breadcrumb"></li>
                                    <li class="breadcrumb-item active hidden" id="last-breadcrumb"></li>
                                </ol>
                                {!! ThemaProvider::videoAulaPagina() !!} 
                            </div><!-- end card header -->
                            <div class="card-body" id="box-listing">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="buy-tab" role="tabpanel">
                                        <div>
                                            <form id="formUpdate" method="post" action="javascript:;">
                                                @csrf
                                                <div class="card-body row no-margin no-padding"> 
                                                    <div class="col-md-12 col-sm-12 box-site">
                                                        <div class="form-group">
                                                            <label class="control-label">Icone da Página</label>
                                                            <div class="input-group">
                                                                <input name="icon" id="image_icon" accept="image/png,image/vnd.microsoft.icon" type="file"/>
                                                            </div>    
                                                            <span class="badge badge-info small"><strong>Atenção :</strong> <em> Apenas ICO, PNG são permitidos, Resolução recomendada 32px × 32px.</em></span>
                                                        </div>  
                                                    </div>
                                                    <div class="col-md-6 col-sm-12 box-site">
                                                        <div class="form-group">
                                                            <label class="control-label">Logo Tema Escuro</label>
                                                            <div class="input-group">
                                                                <input name="logo" id="image_logo" accept="image/*" type="file"/>
                                                            </div>     
                                                            <span class="badge badge-info small"><strong>Atenção :</strong> <em> Apenas JPEG, JPG, PNG, GIF são permitidos. Resolução recomendado 180px × 90px</em></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-sm-12 box-site">
                                                        <div class="form-group">
                                                            <label class="control-label">Logo Tema Claro</label>
                                                            <div class="input-group">
                                                                <input name="logoR" id="image_responsive" accept="image/*" type="file"/>
                                                            </div>   
                                                            <span class="badge badge-info small"><strong>Atenção :</strong> <em> Apenas JPEG, JPG, PNG, GIF são permitidos. Resolução recomendado 120px × 60px</em></span>
                                                        </div>
                                                    </div>  
                                                    <div class="col-md-12 col-sm-12 box-site no-padding no-margin">
                                                        <div class="form-group">
                                                            <label class="control-label">Logo do Email</label>
                                                            <div class="input-group">
                                                                <input name="logoEmail" id="image_email" accept="image/png,image/jpg" type="file"/>
                                                            </div>    
                                                            <span class="badge badge-info small"><strong>Atenção :</strong> <em> Apenas JPG, PNG são permitidos, Resolução recomendada 390px × 133px.</em></span>
                                                        </div>  
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <div class="col-md-12 text-center hidden" id="loading">
                                                    <p>Carregando...</p>
                                                    <div class="spinner-border text-primary m-1" role="status">
                                                        <span class="sr-only">Carregando...</span>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="col-md-12 margin-top-15">
                                                    <hr>
                                                    <div class="col-lg-3 col-md-4 col-xs-12 centered-block margin-top-15">
                                                        <button type="submit" class="btn btn-lg btn-success btn-block"><i class="far fa-save"></i> Salvar</button> 
                                                    </div>
                                                </div>
                                            </form> 
                                        </div>
                                    </div>

                                </div>
                                <!-- end tab content -->
                            </div>
                            <div class="card-body hidden" id="box-ajax">
                                <!-- AJAX CONTENT -->
                            </div> 
                            <!-- end card body -->
                        </div>
                    </div>
                    <!-- End Page-content -->
                    @include('_template/footer')
                </div>
                <!-- end main content-->
            </div>
            <!-- END layout-wrapper -->
        </div>
    </body> 
    @include('_template.libs.js.theme') 
    <script>
        var logo = "{{ asset(sprintf('assets/uploads/theme/%s', $data['config']['logo_dark'])) }}";
        var favoicon = "{{ asset(sprintf('assets/uploads/theme/%s', $data['config']['favoicon'])) }}";
        var responsive = "{{ asset(sprintf('assets/uploads/theme/%s', $data['config']['logo'])) }}";
        var email = "{{ asset(sprintf('assets/uploads/theme/%s', $data['config']['logo_email'])) }}";
        var is_root = <?php print Session::get("user_is_root") ? "true" : "false"; ?>;
    </script> 
    <script src="{{ asset('assets/libs/kartik-v-bootstrap-fileinput/js/fileinput.min.js') }}"></script>
    <script src="{{ asset('assets/libs/kartik-v-bootstrap-fileinput/js/fileinput_locale_pt-BR.js') }}"></script>
    <script src="{{ asset('assets/js/pages/configuracao/desenvolvedor/logos.init.js') }}"></script>    
</html>
