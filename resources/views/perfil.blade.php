@php
use \App\Providers\ThemaProvider
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Perfil :: {{ $systemVariables->nome }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <base href="{{ config('app.url') }}"> 
        @include('_template.libs.css.theme') 
        <link rel="stylesheet" type="text/css" href="{{ asset("assets/libs/kartik-v-bootstrap-fileinput/css/fileinput.min.css") }}" />
        <link rel="stylesheet" href="{{ asset("assets/libs/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css") }}" /> 
    </head>
    <body data-sidebar-size="{{ThemaProvider::tamanho()}}" data-layout-mode="{{ThemaProvider::sidebar()}}" data-topbar="{{ThemaProvider::sidebar()}}" data-sidebar="{{ThemaProvider::sidebar()}}" data-topbar="{{ThemaProvider::sidebar()}}" data-layout="horizontal">
        <!-- Begin page -->
        <div id="layout-wrapper">
            @include('_template/header')
            <div class="main-content">
                <div class="page-content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card">
                                    <div class="card-header align-items-center d-flex">
                                        <h4 class="card-title mb-0 flex-grow-1">Minha Conta</h4>
                                    </div><!-- end card header -->
                                    <div class="card-body">
                                        <div class="tab-content">
                                            <form id="formUpdate" method="post" action="javascript:;">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-4 col-lg-4 col-xs-12">
                                                        <div class="form-group text-left">
                                                            <label class="form-label"><span id="field-required">*</span> Nome</label>
                                                            <span class="input-with-icon right">
                                                                <i class=""></i> 
                                                                <input class="form-control" id="firstname" name="firstname" placeholder="Seu Nome" value="{{Auth::user()->user_first_name}}"/>
                                                            </span> 
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-lg-4 col-xs-12">
                                                        <div class="form-group text-left">
                                                            <label class="form-label">Sobrenome</label>
                                                            <span class="input-with-icon right">
                                                                <i class=""></i> 
                                                                <input class="form-control" id="lname" name="lname" placeholder="Seu Sobrenome" value="{{Auth::user()->user_last_name}}"/>
                                                            </span> 
                                                        </div>
                                                    </div> 
                                                    <div class="col-md-4 col-lg-4 col-xs-12">
                                                        <div class="form-group text-left">
                                                            <label class="form-label"><span id="field-required">*</span> Email</label> 
                                                            <span class="input-with-icon right"> 
                                                                <i class=""></i>
                                                                <input type="email" class="form-control" placeholder="Seu Email" value="{{Auth::user()->user_email }}" name="email" disabled=""> 
                                                            </span> 
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-lg-4 col-xs-12 margin-top-15">
                                                        <div class="form-group text-left">
                                                            <label class="form-label"><span id="field-required">*</span> Mudar Senha</label> 
                                                            <div class="input-with-icon right"> 
                                                                <i class=""></i> 
                                                                <input id="c_pass" name="changePassword" class="Switch" type="checkbox" data-on-text="<i class='fa fa-check'></i>" data-off-text="<i class='fa fa-times'></i>">        
                                                            </div> 
                                                        </div>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                    <div class="col-md-4 col-lg-4 col-xs-12 change_password hidden">
                                                        <div class="form-group text-left">
                                                            <label class="form-label"><span id="field-required">*</span> Nova Senha</label> 
                                                            <span class="input-with-icon right"> 
                                                                <i class=""></i> 
                                                                <input class="form-control" id="password" type="password" name="password" required/> 
                                                            </span> 
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-lg-4 col-xs-12 change_password hidden">
                                                        <div class="form-group text-left">
                                                            <label class="form-label"><span id="field-required">*</span> Confirmar Nova Senha</label> 
                                                            <span class="input-with-icon right"> 
                                                                <i class=""></i> 
                                                                <input class="form-control" id="password_confirmation" type="password" name="password_confirmation" required/> 
                                                            </span> 
                                                        </div>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                    <div class="col-md-6 col-lg-6 col-xs-12 margin-top-15" style="margin-bottom: 15px;"> 
                                                        <label class="form-label">Foto</label> 
                                                        <input type="file" name="pic" id="user_avatar" accept="image/jpg, image/png, image/jpeg" data-min-file-count="1"/> 
                                                        <span class="label label-danger small">
                                                            <strong>Atenção :</strong> 
                                                            <em> Apenas JPEG, BMP, JPG, PNG são permitidos.</em>
                                                        </span> 
                                                    </div> 
                                                    <div class="clearfix"></div>
                                                    <div class="col-md-12 text-center hidden" id="loading">
                                                        <p>Carregando...</p>
                                                        <div class="spinner-border text-primary m-1" role="status">
                                                            <span class="sr-only">Carregando...</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 margin-top-15">
                                                        <hr>
                                                        <div class="col-lg-3 col-md-4 col-xs-12 centered-block margin-top-15">
                                                            <button type="submit" class="btn btn-lg btn-success btn-block"><i class="far fa-save"></i> Salvar</button> 
                                                        </div>
                                                    </div>
                                                </div>
                                            </form> 


                                        </div>
                                        <!-- end tab content -->
                                    </div>
                                    <!-- end card body -->
                                </div>
                                <!-- end card -->
                            </div>
                        </div> <!-- end row-->
                    </div> <!-- container-fluid -->
                </div>
                <!-- End Page-content -->
                @include('_template/footer')
                <!-- end main content-->
            </div>
            <!-- END layout-wrapper -->
        </div>
    </body>
    @include('_template.libs.js.theme')  
    <script src="{{ asset("assets/libs/bootstrap-switch/dist/js/bootstrap-switch.min.js") }}"></script>  
    <script src="{{ asset("assets/libs/kartik-v-bootstrap-fileinput/js/fileinput.min.js") }}"></script>  
    <script src="{{ asset("assets/libs/kartik-v-bootstrap-fileinput/js/fileinput_locale_pt-BR.js") }}"></script>  
    <script>
        var avatar = '{{ asset("avatars/". (Auth::user()->user_has_avatar ?? "default.png")) }}';
        var fname = '{{Auth::user()->user_first_name}}';
    </script>
    <!-- dashboard init-->
    <script src="{{ asset("assets/js/pages/profile.init.js") }}"></script>  
</html>
