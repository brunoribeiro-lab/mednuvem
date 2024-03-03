@php
use \App\Providers\ThemaProvider;
use App\Providers\Utils;
use App\Providers\Converter;
use App\Models\VariaveisDaOperacao;
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
                                                    <div class="col-md-12">
                                                        <h5>Configurações de Email</h5>
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> Confirmação de Pagamento: <i class="fa fa-question-circle input-help" data-index="configuracao-financeiro-email-de-confirmacao" title="O que é isso ? Clique para saber"></i></label> 
                                                            <div class="input-with-icon right">
                                                                <i class=""></i>
                                                                <select id="email_confirmacao_de_pagamento" name="email_confirmacao_de_pagamento" class="select-ambient">
                                                                    <option value="">-- Selecione --</option> 
                                                                    @foreach ($data['emails'] as $email)
                                                                    <option value="{{ $email['id'] }}"{!! (int) $data['config']['EMAIL_PAGAMENTO_CONFIRMADO'] == (int) $email['id'] ? ' selected=""' : '' !!}>{{ $email['index'] }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div> 
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> Confirmação de Atendimento: <i class="fa fa-question-circle input-help" data-index="configuracao-financeiro-email-de-atendimento" title="O que é isso ? Clique para saber"></i></label> 
                                                            <div class="input-with-icon right">
                                                                <i class=""></i>
                                                                <select id="email_atendimento" name="email_atendimento" class="select-ambient">
                                                                    <option value="0">-- Desativado --</option> 
                                                                    @foreach ($data['emails'] as $email)
                                                                    <option value="{{ $email['id'] }}"{!! (int) $data['config']['EMAIL_ATENDIMENTO_CONFIRMADO'] == (int) $email['id'] ? ' selected=""' : '' !!}>{{ $email['index'] }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div> 
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> Cartão Recusado: <i class="fa fa-question-circle input-help" data-index="configuracao-financeiro-email-de-recusado" title="O que é isso ? Clique para saber"></i></label> 
                                                            <div class="input-with-icon right">
                                                                <i class=""></i>
                                                                <select id="email_cartao_recusado" name="email_cartao_recusado" class="select-ambient">
                                                                    <option value="">-- Desativado --</option> 
                                                                    @foreach ($data['emails'] as $email)
                                                                    <option value="{{ $email['id'] }}"{!! (int) $data['config']['EMAIL_PAGAMENTO_RECUSADO'] == (int) $email['id'] ? ' selected=""' : '' !!}>{{ $email['index'] }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div> 
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> Total de Notificações: <i class="fa fa-question-circle input-help" data-index="configuracao-total-notificacoes" title="O que é isso ? Clique para saber"></i></label> 
                                                            <div class="input-with-icon right">
                                                                <i class=""></i>
                                                                <select id="total_notificacoes" name="total_notificacoes" class="select-ambient">
                                                                    <option value="">-- Desativado --</option> 
                                                                    @foreach (range(1, 10) as $num)
                                                                    <option value="{{ $num }}"{!! (int) $data['config']['TOTAL_NOTIFICATIONS'] == $num ? ' selected=""' : '' !!}}>{{ $num > 1 ? "{$num} Notificações" : "1 Notificação" }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div> 
                                                    </div>
                                                    <div class="clearfix"></div>
                                                    <div class="card margin-top-15  padding-bottom-15 padding-top-15{{ (int) $data['config']['TOTAL_NOTIFICATIONS'] > 0 ? '' : ' hidden' }}" id="box-todas-notificacoes">
                                                        @for ($l = 1; $l <= 10; $l++)
                                                        <div class="row box-notificacao {{ $l > (int) $data['config']['TOTAL_NOTIFICATIONS'] ? ' hidden' : '' }}" id="box-notificacao-{{ $l }}">
                                                            <div class="col-md-12 ">
                                                                <h5>{{ $l }}° Notificação</h5>
                                                            </div>
                                                            <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12"> 
                                                                <div class="form-group"> 
                                                                    <label class="form-label"><span class="field-required">*</span> Email da Notificação: <i class="fa fa-question-circle input-help" data-index="configuracao-financeiro-email-de-notificacao" title="O que é isso ? Clique para saber"></i></label> 
                                                                    <div class="input-with-icon right">
                                                                        <i class=""></i>
                                                                        <select id="notificacao_email{{ $l }}" name="notificacao_email[{{ $l }}]" class="select-ambient">
                                                                            <option value="">-- Selecione --</option> 
                                                                            @foreach ($data['emails'] as $email)
                                                                            <option value="{{ $email['id'] }}"{!! $data['config_notificacoes'][$l - 1]['email'] == $email['id'] ? ' selected=""' : ''; !!}>{{ $email['index'] }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div> 
                                                            </div>
                                                            <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12"> 
                                                                <div class="form-group"> 
                                                                    <label class="form-label"><span class="field-required">*</span> Intervalo: <i class="fa fa-question-circle input-help" data-index="configuracao-financeiro-intervalo-de-notificacao" title="O que é isso ? Clique para saber"></i></label> 
                                                                    <div class="input-with-icon right">
                                                                        <i class=""></i>
                                                                        <select id="notificacao_intervalo{{ $l }}" name="notificacao_intervalo[{{ $l }}]" class="select-ambient">
                                                                            <option value="">-- Desativado --</option> 
                                                                            @foreach (range(1, 60) as $i)
                                                                            <option value="{{ $i }}"{!! (int) $data['config_notificacoes'][$l - 1]['intervalo'] == $i ? ' selected=""' : '' !!}>{{ $i }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div> 
                                                            </div>
                                                            <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12"> 
                                                                <div class="form-group"> 
                                                                    <label class="form-label"><span class="field-required">*</span> Período: <i class="fa fa-question-circle input-help" data-index="configuracao-financeiro-periodo-de-notificacao" title="O que é isso ? Clique para saber"></i></label> 
                                                                    <div class="input-with-icon right">
                                                                        <i class=""></i>
                                                                        <select id="notificacao_periodo{{ $l }}" name="notificacao_periodo[{{ $l }}]" class="select-ambient">
                                                                            <option value="">-- Selecione --</option> 
                                                                            <option value="minuto"{!! $data['config_notificacoes'][$l - 1]['periodo'] == 'minutos' ? ' selected=""' : '' !!}>Minutos</option> 
                                                                            <option value="hora"{!! $data['config_notificacoes'][$l - 1]['periodo'] == 'horas' ? ' selected=""' : '' !!}>Horas</option> 
                                                                            <option value="dia"{!! $data['config_notificacoes'][$l - 1]['periodo'] == 'dias' ? ' selected=""' : '' !!}>Dias</option> 
                                                                        </select>
                                                                    </div>
                                                                </div> 
                                                            </div>
                                                            @if($l !== 10)
                                                            <div class="clearfix margin-top-20" id="box-notificacao-separador-{{ $l }}"><hr></div>
                                                            @endif
                                                        </div>
                                                        @endfor
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 box-gerencianet"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> Vencimento Boleto: <i class="fa fa-question-circle input-help" data-index="configuracao-gn-client-id" title="O que é isso ? Clique para saber"></i></label> 
                                                            <span class="input-with-icon right">
                                                                <input type="tel" id="ps_venc_boleto" name="ps_venc_boleto" class="form-control" value="{{ $data['config']['ps_vencimento_boleto'] }}">
                                                            </span>
                                                        </div> 
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 box-gerencianet"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> Vencimento PIX: <i class="fa fa-question-circle input-help" data-index="configuracao-gn-client-id" title="O que é isso ? Clique para saber"></i></label> 
                                                            <span class="input-with-icon right">
                                                                <input type="tel" id="ps_venc_pix" name="ps_venc_pix" class="form-control" value="{{ $data['config']['ps_vencimento_pix'] }}">
                                                            </span>
                                                        </div> 
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 box-gerencianet"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> Juros por Atraso: <i class="fa fa-question-circle input-help" data-index="configuracao-juros-atraso" title="O que é isso ? Clique para saber"></i></label> 
                                                            <span class="input-with-icon right">
                                                                <input type="tel" id="juros_atraso" name="juros_atraso" class="form-control valor" value="{{  Converter::floatReal($data['config']['juros_atraso']) }}">
                                                            </span>
                                                        </div> 
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 box-gerencianet"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> Juros Diários: <i class="fa fa-question-circle input-help" data-index="configuracao-juros-diarios" title="O que é isso ? Clique para saber"></i></label> 
                                                            <span class="input-with-icon right">
                                                                <input type="tel" id="juros_diario" name="juros_diario" class="form-control valor" value="{{ Converter::floatReal($data['config']['juros_diarios']) }}">
                                                            </span>
                                                        </div> 
                                                    </div>
                                                    <div class="col-md-12 margin-top-15">
                                                        <h5>API PagSeguro</h5>
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 box-gerencianet"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> Ambiente: <i class="fa fa-question-circle input-help" data-index="configuracao-gn-ambiente" title="O que é isso ? Clique para saber"></i></label> 
                                                            <div class="input-with-icon right">
                                                                <select id="ps_ambient" name="ps_ambient" class="select-ambient">
                                                                    <option value="">-- Selecione --</option>
                                                                    <option value="0"{!! $data['config']['ps_sandbox'] == "0" ? '  selected=""' : '' !!}>Produção</option>
                                                                    <option value="1"{!! $data['config']['ps_sandbox'] == "1" ? '  selected=""' : '' !!}>Homologação</option>
                                                                </select>
                                                            </div>
                                                        </div> 
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 box-gerencianet"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> APP ID: <i class="fa fa-question-circle input-help" data-index="configuracao-gn-client-id" title="O que é isso ? Clique para saber"></i></label> 
                                                            <span class="input-with-icon right">
                                                                <input type="text" id="ps_app_id" name="ps_app_id" class="form-control" value="{{ $data['config']['ps_app_id'] }}">
                                                            </span>
                                                        </div> 
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 box-gerencianet"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> APP Key: <i class="fa fa-question-circle input-help" data-index="configuracao-gn-client-secret" title="O que é isso ? Clique para saber"></i></label> 
                                                            <span class="input-with-icon right">
                                                                <input type="text" id="ps_app_key" name="ps_app_key" class="form-control" value="{{ $data['config']['ps_app_key'] }}">
                                                            </span>
                                                        </div> 
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 box-gerencianet"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> Email: <i class="fa fa-question-circle input-help" data-index="configuracao-gn-client-secret" title="O que é isso ? Clique para saber"></i></label> 
                                                            <span class="input-with-icon right">
                                                                <input type="text" id="ps_email" name="ps_email" class="form-control" value="{{ $data['config']['ps_email'] }}">
                                                            </span>
                                                        </div> 
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 box-gerencianet"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> Token: <i class="fa fa-question-circle input-help" data-index="configuracao-gn-client-secret" title="O que é isso ? Clique para saber"></i></label> 
                                                            <span class="input-with-icon right">
                                                                <input type="text" id="ps_token" name="ps_token" class="form-control" value="{{ $data['config']['ps_token'] }}">
                                                            </span>
                                                        </div> 
                                                    </div> 
                                                    <div class="col-md-12 margin-top-15">
                                                        <h5>Responsável pelo Pagamento</h5>
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 box-gerencianet"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> Nome: <i class="fa fa-question-circle input-help" data-index="configuracao-holder-nome" title="O que é isso ? Clique para saber"></i></label> 
                                                            <span class="input-with-icon right">
                                                                <input type="text" id="holder_nome" name="holder_nome" class="form-control" value="{{ $data['config']['holder_nome'] }}">
                                                            </span>
                                                        </div> 
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 box-gerencianet"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> Email: <i class="fa fa-question-circle input-help" data-index="configuracao-gn-client-secret" title="O que é isso ? Clique para saber"></i></label> 
                                                            <span class="input-with-icon right">
                                                                <input type="text" id="holder_email" name="holder_email" class="form-control" value="{{ $data['config']['holder_email'] }}">
                                                            </span>
                                                        </div> 
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 box-gerencianet"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> CPF/CNPJ: <i class="fa fa-question-circle input-help" data-index="configuracao-gn-client-secret" title="O que é isso ? Clique para saber"></i></label> 
                                                            <span class="input-with-icon right">
                                                                <input type="text" id="holder_cpf_cnpj" name="holder_cpf_cnpj" class="form-control" value="{{ strlen($data['config']['holder_cpf_cnpj']) == 11 ? Utils::mask( $data['config']['holder_cpf_cnpj'] , Utils::$MASK_CPF) : Utils::mask( $data['config']['holder_cpf_cnpj'] , Utils::$MASK_CNPJ)  }}">
                                                            </span>
                                                        </div> 
                                                    </div> 
                                                    <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 box-gerencianet"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> CEP: <i class="fa fa-question-circle input-help" data-index="configuracao-gn-client-secret" title="O que é isso ? Clique para saber"></i></label> 
                                                            <span class="input-with-icon right">
                                                                <input type="text" id="holder_cep" name="holder_cep" class="form-control" value="{{ Utils::mask( $data['config']['holder_cep'] , Utils::$MASK_CEP) }}">
                                                            </span>
                                                        </div> 
                                                    </div> 
                                                    <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 box-gerencianet"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> Rua: <i class="fa fa-question-circle input-help" data-index="configuracao-holder-rua" title="O que é isso ? Clique para saber"></i></label> 
                                                            <span class="input-with-icon right">
                                                                <input type="text" id="holder_rua" name="holder_rua" class="form-control" value="{{ $data['config']['holder_rua'] }}">
                                                            </span>
                                                        </div> 
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 box-gerencianet"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> Bairro: <i class="fa fa-question-circle input-help" data-index="configuracao-holder-bairro" title="O que é isso ? Clique para saber"></i></label> 
                                                            <span class="input-with-icon right">
                                                                <input type="text" id="holder_bairro" name="holder_bairro" class="form-control" value="{{ $data['config']['holder_bairro'] }}">
                                                            </span>
                                                        </div> 
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 margin-xl-top-10"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> Cidade: <i class="fa fa-question-circle input-help" data-index="configuracao-holder-cidade" title="O que é isso ? Clique para saber"></i></label> 
                                                            <span class="input-with-icon right">
                                                                <input type="text" id="holder_cidade" name="holder_cidade" class="form-control" value="{{ $data['config']['holder_cidade'] }}">
                                                            </span>
                                                        </div> 
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 margin-xl-top-10"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> Estado: <i class="fa fa-question-circle input-help" data-index="configuracao-holder-uf" title="O que é isso ? Clique para saber"></i></label> 
                                                            <span class="input-with-icon right">
                                                                <select id="holder_uf" name="holder_uf" class="select-ambient">
                                                                    <option value="">-- Selecione --</option> 
                                                                    @foreach (VariaveisDaOperacao::$estados as $uf => $estado)
                                                                    <option value="{{ $uf }}"{!! $data['config']['holder_uf'] == $uf ? ' selected=""' : '' !!}>{{ $estado }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </span>
                                                        </div> 
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 margin-xl-top-10"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> Número: <i class="fa fa-question-circle input-help" data-index="configuracao-holder-numero" title="O que é isso ? Clique para saber"></i></label> 
                                                            <span class="input-with-icon right">
                                                                <input type="text" id="holder_numero" name="holder_numero" class="form-control" value="{{ $data['config']['holder_numero'] }}">
                                                            </span>
                                                        </div> 
                                                    </div>
                                                    <!--
                                                    <div class="col-md-12 margin-top-15">
                                                        <h5>API GerenciaNet</h5>
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 box-gerencianet"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> Ambiente: <i class="fa fa-question-circle input-help" data-index="configuracao-gn-ambiente" title="O que é isso ? Clique para saber"></i></label> 
                                                            <div class="input-with-icon right">
                                                                <select id="gn_ambient" name="gn_ambient" class="select-ambient">
                                                                    <option value="">-- Selecione --</option>
                                                                    <option value="0"{!! $data['config']['GN_SANDBOX'] == "0" ? '  selected=""' : '' !!}>Produção</option>
                                                                    <option value="1"{!! $data['config']['GN_SANDBOX'] == "1" ? '  selected=""' : '' !!}>Homologação</option>
                                                                </select>
                                                            </div>
                                                        </div> 
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 box-gerencianet"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> Client ID: <i class="fa fa-question-circle input-help" data-index="configuracao-gn-client-id" title="O que é isso ? Clique para saber"></i></label> 
                                                            <span class="input-with-icon right">
                                                                <input type="text" id="gn_client_id" name="gn_client_id" class="form-control" value="{{ $data['config']['GN_CLIENT_ID'] }}">
                                                            </span>
                                                        </div> 
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 box-gerencianet"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> Client Secret: <i class="fa fa-question-circle input-help" data-index="configuracao-gn-client-secret" title="O que é isso ? Clique para saber"></i></label> 
                                                            <span class="input-with-icon right">
                                                                <input type="text" id="gn_client_secret" name="gn_client_secret" class="form-control" value="{{ $data['config']['GN_CLIENT_SECRET'] }}">
                                                            </span>
                                                        </div> 
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 box-gerencianet"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> Chave PIX: <i class="fa fa-question-circle input-help" data-index="configuracao-gn-chave-pix" title="O que é isso ? Clique para saber"></i></label> 
                                                            <span class="input-with-icon right">
                                                                <i class=""></i>
                                                                <input type="text" id="gn_pix_key" name="gn_pix_key" class="form-control" value="{{ $data['config']['GN_PIX_KEY'] }}">
                                                            </span>
                                                        </div> 
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 box-gerencianet"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> Identificador de Conta: <i class="fa fa-question-circle input-help" data-index="configuracao-gn-id-conta" title="O que é isso ? Clique para saber"></i></label> 
                                                            <span class="input-with-icon right">
                                                                <i class=""></i>
                                                                <input type="text" id="gn_account_id" name="gn_account_id" class="form-control" value="{{ $data['config']['GN_ACCOUNT_ID'] }}">
                                                            </span>
                                                        </div> 
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 box-gerencianet"> 
                                                        <div class="form-group"> 
                                                            <label class="form-label"><span class="field-required">*</span> Enviar Certificado PIX: <i class="fa fa-question-circle input-help" data-index="configuracao-gn-certificado" title="O que é isso ? Clique para saber"></i></label> 
                                                            <span class="input-with-icon right">
                                                                <i class=""></i>
                                                                <button class="btn btn-white" id="btn-document" type="button"><i class="fa fa-file-pdf"></i> Enviar Certificado</button>
                                                                <input name="gn_pix_file" id="document-document" type="file" class="form-control hidden" accept=".p12">
                                                            </span>
                                                        </div> 
                                                    </div>-->
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
    <!-- theme init -->
    <script src="assets/js/form.js"></script> 
    <script src="assets/js/pages/configuracao/desenvolvedor/financeiro.init.js"></script>
    <script src="assets/js/app.js"></script> 
</html>
