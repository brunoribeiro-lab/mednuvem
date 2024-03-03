@php
use \App\Providers\ThemaProvider
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{$data['submenu']["NAME"] }} :: {{ $systemVariables->nome }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <base href="{{ config('app.url') }}">
        @include('_template.libs.css.theme')
        @include('_template.libs.css.datatable') 
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
                                    <li class="breadcrumb-item active" id="manager-breadcrumb"><i class="<?php print $data['submenu']["ICON"]; ?>"></i> <?php print $data['submenu']["NAME"]; ?></li>
                                    <li class="breadcrumb-item active hidden" id="action-breadcrumb"></li>
                                    <li class="breadcrumb-item active hidden" id="last-breadcrumb"></li>
                                </ol>
                                {!! ThemaProvider::videoAulaPagina() !!} 
                            </div><!-- end card header -->
                            <div class="card-body" id="box-listing">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="buy-tab" role="tabpanel">
                                        <div>
                                            <form id="multiple_delete" method="post" action="#">
                                                @csrf
                                                <table class="table dt-responsive nowrap table-condensed table-hover table-full-width" id="listingData">
                                                    <thead>
                                                        <tr>
                                                            <th class="hidden-md hidden-lg hidden-sm" data-priority="1"></th>
                                                            <th style="width: 5px;" class="center uniformjs" data-priority="3"><div class="checkbox check-default" style="margin-right:auto;margin-left:auto;"><input id="select-all" type="checkbox" name="delete" class="checkall"><label for="select-all"></label></div></th>
                                                            <th data-priority="4">Nome</th>
                                                            <th data-priority="5">Cargo</th>
                                                            <th data-priority="6">CPF/CNPJ</th>
                                                            <th data-priority="7">Cadastrado</th>
                                                            <th data-priority="2">Ação</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody><!-- AJAX CONTENT --></tbody>
                                                </table>
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
    @include('_template.libs.js.datatable') 
    <script>
        window.is_root = {{ Session::get('is_root') ? "true" : "false"}};
        window.action = {};
        window.action['add'] = <?php print $data['actions']['add'] ? "true" : "false"; ?>;
        window.action['remove'] = <?php print $data['actions']['remove'] ? "true" : "false"; ?>;
    </script> 
    <script src="{{ asset('assets/js/pages/configuracao/usuarios.init.js') }}"></script>   
</html>
