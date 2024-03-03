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
                                                    <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Nome do Site<span class="symbol required"></span></label>
                                                            <span class="input-with-icon right">
                                                                <i class=""></i>
                                                                <input class="form-control tooltips" value="{{ $data['config']['nome'] }}" id="nome_do_sistema" name="nome_do_sistema" type="text" placeholder="Nome do sistema">
                                                            </span>
                                                        </div>
                                                    </div>  
                                                    <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span>  Cargo de Clinica: <i class="fa fa-question-circle input-help" data-index="configuracao-email-nova-empresa" title="O que é isso ? Clique para saber"></i></label> 
                                                            <div class="input-with-icon right">
                                                                <i class=""></i>
                                                                <select id="cargo_clinica" name="cargo_clinica" class="select-ambient">
                                                                    <option value="">-- Selecione --</option> 
                                                                    @foreach ($data['cargos'] as $cargo)
                                                                    <option value="{{ $cargo['ID'] }}"{!! (int) $data['config']->clinica == (int) $cargo->ID ? ' selected=""' : '' !!}>{{ $cargo['NAME'] }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div> 
                                                    </div>   
                                                    <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span>  Cargo de Médico: <i class="fa fa-question-circle input-help" data-index="configuracao-email-nova-empresa" title="O que é isso ? Clique para saber"></i></label> 
                                                            <div class="input-with-icon right">
                                                                <i class=""></i>
                                                                <select id="cargo_medico" name="cargo_medico" class="select-ambient">
                                                                    <option value="">-- Selecione --</option> 
                                                                    @foreach ($data['cargos'] as $cargo)
                                                                    <option value="{{ $cargo['ID'] }}"{!! (int) $data['config']->medico == (int) $cargo->ID ? ' selected=""' : '' !!}>{{ $cargo['NAME'] }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div> 
                                                    </div>   
                                                    <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> Email - Recuperar Senha: <i class="fa fa-question-circle input-help" data-index="configuracao-financeiro-email-de-confirmacao" title="O que é isso ? Clique para saber"></i></label> 
                                                            <div class="input-with-icon right">
                                                                <select id="email_recuperar_senha" name="email_recuperar_senha" class="select-ambient">
                                                                    <option value="">-- Desativado --</option> 
                                                                    @foreach ($data['emails'] as $email)
                                                                    <option value="{{ $email['id'] }}"{!! (int) $data['config']['email_recuperar_senha'] == (int) $email['id'] ? ' selected=""' : ''; !!}>{{ $email['index'] }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div> 
                                                    </div>
                                                    <div class="col-md-12 margin-top-15">
                                                        <h5>Notificações de Formulários</h5>
                                                    </div>
                                                    <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> Email - Contato: <i class="fa fa-question-circle input-help" data-index="configuracao-financeiro-email-de-confirmacao" title="O que é isso ? Clique para saber"></i></label> 
                                                            <div class="input-with-icon right">
                                                                <select id="email_dinamico_contato" name="email_dinamico_contato" class="select-ambient">
                                                                    <option value="">-- Desativado --</option> 
                                                                    @foreach ($data['emails'] as $email)
                                                                    <option value="{{ $email['id'] }}"{!! (int) $data['config']['email_dinamico_contato'] == (int) $email['id'] ? ' selected=""' : ''; !!}>{{ $email['index'] }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div> 
                                                    </div>
                                                    <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12 box-email-contato{{ empty($data['config']['email_dinamico_contato']) ? ' hidden' : '' }}"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> Email Destinatário: <i class="fa fa-question-circle input-help" data-index="configuracao-gn-client-id" title="O que é isso ? Clique para saber"></i></label> 
                                                            <span class="input-with-icon right">
                                                                <input type="text" id="email_contato" name="email_contato" class="form-control" value="{{ $data['config']['email_contato'] }}">
                                                            </span>
                                                        </div> 
                                                    </div> 
                                                    <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span>  Email - Novo Cliente: <i class="fa fa-question-circle input-help" data-index="configuracao-financeiro-email-de-atendimento" title="O que é isso ? Clique para saber"></i></label> 
                                                            <div class="input-with-icon right">
                                                                <i class=""></i>
                                                                <select id="email_dinamico_novo_cliente_p" name="email_dinamico_novo_cliente_p" class="select-ambient">
                                                                    <option value="0">-- Desativado --</option> 
                                                                    @foreach ($data['emails'] as $email)
                                                                    <option value="{{ $email['id'] }}"{!! (int) $data['config']['email_dinamico_novo_cliente_p'] == (int) $email['id'] ? ' selected=""' : '' !!}>{{ $email['index'] }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div> 
                                                    </div>
                                                    <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12 box-email-cliente{{ empty($data['config']['email_dinamico_novo_cliente_p']) ? ' hidden' : ''; }}"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> Email Destinatário: <i class="fa fa-question-circle input-help" data-index="configuracao-gn-client-id" title="O que é isso ? Clique para saber"></i></label> 
                                                            <span class="input-with-icon right">
                                                                <i class=""></i>
                                                                <input type="text" id="email_cliente" name="email_cliente" class="form-control" value="{{ $data['config']['email_cliente'] }}">
                                                            </span>
                                                        </div> 
                                                    </div> 
                                                    <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span>  Email - Novo Motorista: <i class="fa fa-question-circle input-help" data-index="configuracao-financeiro-email-de-recusado" title="O que é isso ? Clique para saber"></i></label> 
                                                            <div class="input-with-icon right">
                                                                <select id="email_dinamico_novo_motorista_p" name="email_dinamico_novo_motorista_p" class="select-ambient">
                                                                    <option value="0">-- Desativado --</option> 
                                                                    @foreach ($data['emails'] as $email)
                                                                    <option value="{{ $email['id'] }}"{!! (int) $data['config']['email_dinamico_novo_motorista_p'] == (int) $email['id'] ? ' selected=""' : '' !!}>{{ $email['index'] }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div> 
                                                    </div>
                                                    <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12 box-email-motorista{{ empty($data['config']['email_dinamico_novo_motorista_p']) ? ' hidden' : '' }}"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> Email Destinatário: <i class="fa fa-question-circle input-help" data-index="configuracao-gn-client-id" title="O que é isso ? Clique para saber"></i></label> 
                                                            <span class="input-with-icon right">
                                                                <input type="text" id="email_motorista" name="email_motorista" class="form-control" value="{{ $data['config']['email_motorista'] }}">
                                                            </span>
                                                        </div> 
                                                    </div>
                                                    <div class="col-md-12 margin-top-15">
                                                        <h5>Notificações de Criação de Usuários</h5>
                                                    </div>
                                                    <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-top-10"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span>  Email - Clinica: <i class="fa fa-question-circle input-help" data-index="configuracao-email-nova-unidade" title="O que é isso ? Clique para saber"></i></label> 
                                                            <div class="input-with-icon right">
                                                                <i class=""></i>
                                                                <select name="email_dinamico_novo_cliente" class="select-ambient">
                                                                    <option value="">-- Desativado --</option> 
                                                                    @foreach ($data['emails'] as $email)
                                                                    <option value="{{ $email['id'] }}"{!! (int) $data['config']['email_dinamico_novo_cliente'] == (int) $email['id'] ? ' selected=""' : '' !!}>{{ $email['index'] }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div> 
                                                    </div>
                                                    <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-top-10"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span>  Email - Médico: <i class="fa fa-question-circle input-help" data-index="configuracao-email-nova-unidade" title="O que é isso ? Clique para saber"></i></label> 
                                                            <div class="input-with-icon right">
                                                                <i class=""></i>
                                                                <select name="email_dinamico_novo_medico" class="select-ambient">
                                                                    <option value="">-- Desativado --</option> 
                                                                    @foreach ($data['emails'] as $email)
                                                                    <option value="{{ $email['id'] }}"{!! (int) $data['config']['email_dinamico_novo_medico'] == (int) $email['id'] ? ' selected=""' : '' !!}>{{ $email['index'] }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div> 
                                                    </div>

                                                    <div class="col-md-12 margin-top-15">
                                                        <h5>API ReCaptchar</h5>
                                                    </div>
                                                    <div class="col-md-2 col-lg-2 col-xs-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Ativar Captchar <i class="fa fa-question-circle input-help" data-index="captchar_enable" title="O que é isso ? Clique para saber"></i></label>
                                                            <div class="input-group">
                                                                <input{!! !empty($data["config"]["captchar_ativar"]) ? ' checked=""' : ''; !!} name="captchar_enable" class="form-check-input" type="checkbox">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 col-lg-2 col-xs-12 group_captchar{{ empty($data["config"]["captchar_ativar"]) ? ' hidden' : '' }}">
                                                        <div class="form-group">
                                                            <label class="control-label">Client Key <span class="symbol required"></span> <i class="fa fa-question-circle input-help" data-index="captchar_key" title="O que é isso ? Clique para saber"></i></label>
                                                            <span class="input-with-icon right">
                                                                <i class=""></i>
                                                                <input type="text" value="{{ $data["config"]["captchar_key"] }}" id="captchar_key" name="captchar_key" class="form-control">
                                                            </span>
                                                        </div> 
                                                    </div>
                                                    <div class="col-md-2 col-lg-2 col-xs-12 group_captchar{{ empty($data["config"]["captchar_ativar"]) ? ' hidden' : '' }}">
                                                        <div class="form-group">
                                                            <label class="control-label">Client Secret <span class="symbol required"></span> <i class="fa fa-question-circle input-help" data-index="captchar_secret" title="O que é isso ? Clique para saber"></i></label>
                                                            <span class="input-with-icon right">
                                                                <i class=""></i>
                                                                <input type="text" value="{{ $data["config"]["captchar_secret"] }}" id="captchar_secret" name="captchar_secret" class="form-control">
                                                            </span>
                                                        </div>  
                                                    </div>
                                                    <div class="col-md-12 text-center hidden" id="loading">
                                                        <p>Carregando...</p>
                                                        <div class="spinner-border text-primary m-1" role="status">
                                                            <span class="sr-only">Carregando...</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 margin-top-15">
                                                    <hr>
                                                    <div class="col-lg-3 col-md-4 col-xs-12 centered-block margin-top-15">
                                                        <button type="submit" class="btn btn-success btn-lg btn-block"><i class="far fa-save"></i> Salvar</button>
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
    <script src="{{ asset('assets/js/pages/configuracao/desenvolvedor/variaveis.init.js') }}"></script> 
</html>
