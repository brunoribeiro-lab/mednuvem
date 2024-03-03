@php
use App\Providers\Utils;
@endphp
<form method="POST" action="javascript:;" id="formModalUpdate" enctype="multipart/form-data"> 
    @csrf
    <input type="hidden" value="{{ $user->user_id }}" name="id"> 
    <div class="row">
        <div class="col-md-12">
            <h3><a href="javascript:;" id="backTo"><i class="fa fa-arrow-left"></i> Voltar</a></h3>
            <hr>
        </div>
        <div class="col-xxl-2 col-xl-2 col-lg-4 col-md-6 col-sm-12">
            <div class="row">
                <label class="form-label">Login</label>
                <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6 col-sm-6 col-xs-6 form-check" style="margin-left: 10px;">
                    <input type="radio" value="cpf" class="form-check-input" name="tipo_login" id="form-login-1" {!! strlen($user->user_name) == 11 ? ' checked=""' : '' !!}> 
                    <label for="form-login-1" class="form-check-label">CPF</label>
                </div>
                <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6 col-sm-6 col-xs-6 form-check">
                    <input type="radio" value="cnpj" class="form-check-input" name="tipo_login" id="form-login-2" {!! strlen($user->user_name) !== 11 ? ' checked=""' : '' !!}> 
                    <label for="form-login-2" class="form-check-label">CNPJ</label>
                </div>
            </div>
        </div> 
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <label class="form-label" id="nome"><span id="field-required">*</span> Nome</label>
            <span class="input-with-icon right">
                <i class=""></i>
                <input class="form-control" id="firstname" name="firstname" type="text" value="{{ $user->user_first_name }}" {!!  in_array($user->user_account_type,[$config->clinica, $config->medico ]) ? 'readonly=""' : '' !!} />
            </span>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 box-ultimo-nome">
            <label class="form-label">Sobrenome</label>
            <span class="input-with-icon right">
                <i class=""></i>
                <input class="form-control" id="lastname" name="lname" type="text" value="{{ $user->user_last_name }}" {!!  in_array($user->user_account_type,[$config->clinica, $config->medico ]) ? 'readonly=""' : '' !!} />
            </span>
        </div> 
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <label class="form-label" id="login-label"><span id="field-required">*</span> CPF</label>
            <span class="input-with-icon right">
                <i class=""></i>
                <input class="form-control" id="username" maxlength="15" type="tel" name="username" value="{{strlen($user->user_name) == 11 ? Utils::mask($user->user_name, Utils::$MASK_CPF) : Utils::mask($user->user_name, Utils::$MASK_CNPJ) }}" />
            </span>
        </div> 
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <label class="form-label"><span id="field-required">*</span> E-mail</label>
            <span class="input-with-icon right">
                <i class=""></i>
                <input class="form-control" id="email" type="email" name="email" value="{{ $user->user_email }}" />
            </span>
        </div>  
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <label class="form-label"><span id="field-required">*</span> Acesso:</label>
            <div class="input-with-icon right">
                <i class=""></i>
                <select id="business" name="business" class="chosen-select">
                    @foreach ($accout as $type)
                    <option value="{{ $type->ID }}" {!! $type->ID == $user->user_account_type ? ' selected=""' : '' !!}>{{ $type->NAME }}</option>
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