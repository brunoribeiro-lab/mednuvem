@php
use App\Providers\Converter;
@endphp
<form method="POST" action="javascript:;" id="formModalAddNew" enctype="multipart/form-data"> 
    @csrf
    <div class="row">
        @if(!$data['modal'])
        <div class="col-md-12">
            <h3><a href="javascript:;" id="backTo"><i class="fa fa-arrow-left"></i> Voltar</a></h3>
            <hr>
        </div>
        @endif
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <label class="form-label"><span id="field-required">*</span> Nome do Médico: <i class="fa fa-question-circle input-help" data-index="campos-rua" title="O que é isso ? Clique para saber"></i></label>
            <span class="input-with-icon right">
                <i class=""></i>
                <input class="form-control" id="nome" name="nome" type="text" />
            </span>
        </div>  
        <div class="col-xxl-2 col-xl-2 col-lg-2 col-md-3 col-sm-12">
            <label class="form-label">Pessoa Física/Júridica</label>
            <div class="input-with-icon right">
                <input type="radio" value="cpf" class="form-check-input" name="tipo_login" id="form-login-1" checked=""> 
                <label for="form-login-1" class="form-check-label margin-right-10">CPF</label>
                <input type="radio" value="cnpj" class="form-check-input" name="tipo_login" id="form-login-2"> 
                <label for="form-login-2" class="form-check-label">CNPJ</label>
            </div>
        </div> 
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <label class="form-label" id="login-label"><span id="field-required">*</span> CPF</label>
            <span class="input-with-icon right">
                <i class=""></i>
                <input class="form-control" id="username" maxlength="15" type="tel" name="username" />
            </span>
        </div> 
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 margin-md-top-10">
            <label class="form-label"><span id="field-required">*</span> E-mail</label>
            <span class="input-with-icon right">
                <i class=""></i>
                <input class="form-control" id="email" type="email" name="email" />
            </span>
        </div>  
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 margin-md-top-10">
            <label class="form-label">Celular: <i class="fa fa-question-circle input-help" data-index="campos-rua" title="O que é isso ? Clique para saber"></i></label>
            <span class="input-with-icon right">
                <i class=""></i>
                <input class="form-control phone" id="celular" name="celular" type="tel" />
            </span>
        </div>   
        @if(Session::get('is_root'))
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <label class="form-label"><span id="field-required">*</span> Clínica:</label>
            <div class="input-with-icon right">
                <i class=""></i>
                <select id="clinica" name="clinica" class="chosen-select-full">
                    <option value="">-- Selecione --</option>
                    @foreach (\App\Models\Users::clinicas() as $clinica)
                        <option value="{{ $clinica->user_id }}">{{ $clinica->user_first_name }}</option>
                    @endforeach
                </select>  
            </div>
        </div> 
        @endif
        <div class="clearfix"></div>
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 margin-top-10">
            <label class="form-label"><span id="field-required">*</span> Setor:</label>
            <div class="input-with-icon right">
                <i class=""></i>
                <select id="setor" name="setor" class="chosen-select-full">
                    <option value="">-- Selecione --</option>
                    @foreach ($setores as $setor)
                    <option value="{{ $setor->id }}">{{ $setor->nome }}</option>
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
                </select>  
            </div>
        </div> 
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 margin-top-10">
            <label class="form-label">Gerar Senha: <i class="fa fa-question-circle input-help" data-index="campos-motorista-dia-salario" title="O que é isso ? Clique para saber"></i></label>
            <span class="input-with-icon right">
                <i class=""></i>
                <select class="chosen-select-full" id="gerar_senha" name="gerar_senha">
                    <option value="0">Automático</option> 
                    <option value="1">Personalizado</option> 
                </select> 
            </span>
        </div>    
        <div class="col-xxl-2 col-xl-2 col-lg-2 col-md-3 col-sm-12 margin-top-10 hidden grupo-senha">
            <label class="form-label"><span id="field-required">*</span> Senha</label>
            <span class="input-with-icon  right">
                <i class=""></i>
                <input class="form-control" id="password" type="password" name="password" />
            </span>
        </div> 
        <div class="col-xxl-2 col-xl-2 col-lg-2 col-md-3 col-sm-12 margin-top-10 hidden grupo-senha">
            <label class="form-label"><span id="field-required">*</span> Repetir Senha</label>
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
                <button type="submit" class="btn btn-success btn-lg btn-block"><i class="far fa-plus"></i> Cadastrar</button>
            </div>
        </div>
    </div>
</form>