@php
use App\Providers\Utils;
use App\Providers\Converter;
@endphp
<form method="POST" action="javascript:;" id="formModalUpdate" enctype="multipart/form-data"> 
    @csrf
    <input type="hidden" value="{{ $data->id }}" name="id">
    <div class="row">
        <div class="col-md-12">
            <h3><a href="javascript:;" id="backTo"><i class="fa fa-arrow-left"></i> Voltar</a></h3>
            <hr>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <label class="form-label"><span id="field-required">*</span> Nome do Médico: <i class="fa fa-question-circle input-help" data-index="campos-rua" title="O que é isso ? Clique para saber"></i></label>
            <span class="input-with-icon right">
                <i class=""></i>
                <input class="form-control" id="nome" name="nome" type="text" value="{{ $data->nome }}" readonly=""  />
            </span>
        </div>   
        <div class="col-xxl-2 col-xl-2 col-lg-2 col-md-3 col-sm-12">
            <label class="form-label">Pessoa Física/Júridica</label>
            <div class="input-with-icon right">
                <input type="radio" value="cpf" class="form-check-input" name="tipo_login" id="form-login-1" {!! strlen($data['user_name']) == 11 ? 'checked=""' : ''  !!}> 
                <label for="form-login-1" class="form-check-label margin-right-10">CPF</label>
                <input type="radio" value="cnpj" class="form-check-input" name="tipo_login" id="form-login-2" {!! strlen($data['user_name']) == 11 ? '' : 'checked=""'  !!}> 
                <label for="form-login-2" class="form-check-label">CNPJ</label>
            </div>
        </div> 
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <label class="form-label" id="login-label"><span id="field-required">*</span> {{ strlen($data['user_name']) == 11 ? 'CPF' : 'CNPJ'  }}</label>
            <span class="input-with-icon right">
                <i class=""></i>
                <input class="form-control" id="username" maxlength="15" type="tel" name="username" value="{{strlen($data['user_name']) == 11 ? Utils::mask($data['user_name'], Utils::$MASK_CPF) : Utils::mask($data['user_name'], Utils::$MASK_CNPJ) }}" />
            </span>
        </div> 
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 margin-md-top-10">
            <label class="form-label"><span id="field-required">*</span> E-mail</label>
            <span class="input-with-icon right">
                <i class=""></i>
                <input class="form-control" id="email" type="email" name="email" value="{{ $data->user_email }}" />
            </span>
        </div>   
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <label class="form-label">Celular: <i class="fa fa-question-circle input-help" data-index="campos-rua" title="O que é isso ? Clique para saber"></i></label>
            <span class="input-with-icon right">
                <i class=""></i>
                <input class="form-control phone" id="celular" name="celular" type="tel" value="{{ $data->telefone ? Utils::mask($data->telefone, Utils::$MASK_PHONE) : '' }}" />
            </span>
        </div>    
        <div class="clearfix"></div>
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 margin-top-10">
            <label class="form-label"><span id="field-required">*</span> Setor:</label>
            <div class="input-with-icon right">
                <i class=""></i>
                <select id="setor" name="setor" class="chosen-select-full">
                    <option value="">-- Selecione --</option>
                    @foreach ($setores as $setor)
                    <option value="{{ $setor->id }}" {!! $setor->id == $data->setor ? ' selected=""' : '' !!}>{{ $setor->nome }}</option>
                    @endforeach
                </select>  
            </div>
        </div> 
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 margin-top-10">
            <label class="form-label"><span id="field-required">*</span> Função:</label>
            <div class="input-with-icon right">
                <i class=""></i>
                <select id="funcao" name="funcao" class="chosen-select-full">
                    <option value="">-- Selecione --</option> 
                    @foreach ($funcoes as $funcao)
                    <option value="{{ $funcao->id }}" {!! $funcao->id == $data->funcao ? ' selected=""' : '' !!}>{{ $funcao->nome }}</option>
                    @endforeach
                </select>  
            </div>
        </div> 
        <div class="clearfix"></div>
        <div class="col-xxl-2 col-xl-2 col-lg-4 col-md-6 col-sm-12 margin-top-10">
            <div class="row">
                <label class="form-label">Mudar Senha</label>
                <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6 col-sm-6 col-xs-6 form-check" style="margin-left: 10px;">
                    <input type="radio" value="1" class="form-check-input" name="mudar_senha" id="form-mudar_senha-1"> 
                    <label for="form-mudar_senha-1" class="form-check-label"> Sim</label>
                </div>
                <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6 col-sm-6 col-xs-6 form-check">
                    <input type="radio" value="0" class="form-check-input" name="mudar_senha" id="form-mudar_senha-2"  checked=""> 
                    <label for="form-mudar_senha-2" class="form-check-label"> Não</label>
                </div>
            </div>
        </div> 
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 margin-top-10 box-password hidden">
            <label class="form-label"><span id="field-required">*</span> Senha</label>
            <span class="input-with-icon  right">
                <i class=""></i>
                <input class="form-control" id="password" type="password" name="password" />
            </span>
        </div> 
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 margin-top-10 box-password hidden">
            <label class="form-label"><span id="field-required">*</span> Confirmar Senha</label>
            <span class="input-with-icon  right">
                <i class=""></i>
                <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" />
            </span>
        </div> 
        <div class="col-md-12 text-center margin-top-15 hidden" id="loading">
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