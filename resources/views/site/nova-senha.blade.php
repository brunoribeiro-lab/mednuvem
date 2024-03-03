@php
use App\Providers\Utils; 
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">
    <head data-mobilemenu="false">
        <meta charset="utf-8" />
        <base href="{{ config('app.url') }}">
        <title>{{ $systemVariables->nome }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        @include('site/_template/libs/css/theme') 
        <!-- Sweet Alert-->
        <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    </head>
    <body> 
        @include('site/_template/header') 
        <section id="contact" class="contact-us section-padding">
            <div class="container">	
                <div class="row">		 
                    <div class="col-lg-12 col-sm-12 col-xs-12">
                        <div class="md-scr-size"> 
                            <div class="auth-page">
                                <div class="container-fluid p-0">
                                    <div class="row g-0">
                                        <div class="col-xxl-5 col-xxl-5 col-lg-8 col-md-10 centered-block" style="height: 76vh;">
                                            <div class="auth-full-page-content d-flex p-sm-5 p-4">
                                                <div class="w-100">
                                                    <div class="d-flex flex-column h-100">
                                                        <div class="auth-content my-auto"> 
                                                            <div class="mb-4 mb-md-2 text-center">
                                                                <h5>Nova Senha</h5>
                                                            </div>
                                                            <form class="mt-4 pt-2" action="javascript:;" id="nova-senha-form" method="post">
                                                                @csrf 
                                                                <input type="hidden" name="token" value="{{ $data->user_password_reset_hash }}">

                                                                <div class="mb-3 margin-top-10">
                                                                    <label class="form-label" id="label-login">{{ strlen($data->user_name) == 11 ? 'CPF' : 'CNPJ'  }}</label>
                                                                    <input type="tel" class="form-control" id="user-phone" name="user_name" value="{{strlen($data['user_name']) == 11 ? Utils::mask($data['user_name'], Utils::$MASK_CPF) : Utils::mask($data['user_name'], Utils::$MASK_CNPJ) }}" placeholder="000.000.000-00" readonly="">
                                                                </div> 
                                                                <div class="mb-3">
                                                                    <div class="d-flex align-items-start">
                                                                        <div class="flex-grow-1">
                                                                            <label class="form-label">Nova Senha</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="input-group auth-pass-inputgroup">
                                                                        <input type="password" class="form-control" placeholder="Digite sua senha" name="password" id="password" aria-label="Password" aria-describedby="password-addon">
                                                                        <button class="btn btn-primary shadow-none ms-0 password-addon" type="button" data-target="#password" title="Clique aqui para ocultar a senha"><i class="fa fa-eye"></i></button>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <div class="d-flex align-items-start">
                                                                        <div class="flex-grow-1">
                                                                            <label class="form-label">Confirmar Senha</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="input-group auth-pass-inputgroup">
                                                                        <input type="password" class="form-control" placeholder="Digite sua senha" name="password_confirmation" id="password_confirmation" aria-label="Password" aria-describedby="password-addon">
                                                                        <button class="btn btn-primary shadow-none ms-0 password-addon" type="button" data-target="#password_confirmation" title="Clique aqui para ocultar a senha"><i class="fa fa-eye"></i></button>
                                                                    </div>
                                                                </div> 
                                                                <div class="row mb-4" id="result"></div>
                                                                <div class="mb-3">
                                                                    <button class="btn btn-primary w-100 waves-effect waves-light" type="submit">Mudar Senha</button>
                                                                </div>
                                                            </form> 
                                                        </div> 
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- end auth full page content -->
                                        </div>
                                    </div>
                                    <!-- end row -->
                                </div>
                                <!-- end container fluid -->
                            </div>
                        </div>	
                    </div><!-- END Col -->
                </div><!-- END ROW -->
            </div><!-- END CONTAINER -->	
        </section>
        @include('site/_template/footer')
    </body> 
    @include('site/_template/libs/js/theme')   
    <script src="{{ asset('assets/libs/validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/libs/vendor/jquery.mask.min.js') }}"></script> 
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/site/js/nova-senha.min.js') }}"></script>  
</html>