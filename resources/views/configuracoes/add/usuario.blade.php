<form method="POST" action="javascript:;" id="formModalAddNew" enctype="multipart/form-data"> 
    @csrf
    <div class="row">
        <div class="col-md-12">
            <h3><a href="javascript:;" id="backTo"><i class="fa fa-arrow-left"></i> Voltar</a></h3>
            <hr>
        </div>
        <div class="col-xxl-2 col-xl-2 col-lg-4 col-md-6 col-sm-12">
            <label class="form-label">Login</label>
            <div class="d-flex">
                <input type="radio" value="cpf" class="form-check-input" name="tipo_login" id="form-login-1" checked=""> 
                <label for="form-login-1" class="form-check-label margin-right-15">CPF</label>
                <input type="radio" value="cnpj" class="form-check-input" name="tipo_login" id="form-login-2"> 
                <label for="form-login-2" class="form-check-label">CNPJ</label>
            </div>
        </div> 
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <label class="form-label" id="nome"><span id="field-required">*</span> Nome</label>
            <span class="input-with-icon right">
                <i class=""></i>
                <input class="form-control" id="firstname" name="firstname" type="text" />
            </span>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 box-ultimo-nome">
            <label class="form-label">Sobrenome</label>
            <span class="input-with-icon right">
                <i class=""></i>
                <input class="form-control" id="lastname" name="lname" type="text" />
            </span>
        </div> 
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <label class="form-label" id="login-label"><span id="field-required">*</span> CPF</label>
            <span class="input-with-icon right">
                <i class=""></i>
                <input class="form-control" id="username" maxlength="15" type="tel" name="username" />
            </span>
        </div> 
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <label class="form-label"><span id="field-required">*</span> E-mail</label>
            <span class="input-with-icon right">
                <i class=""></i>
                <input class="form-control" id="email" type="email" name="email" />
            </span>
        </div>  
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <label class="form-label"><span id="field-required">*</span> Acesso:</label>
            <div class="input-with-icon right">
                <i class=""></i>
                <select id="business" name="business" class="chosen-select">
                    @foreach ($cargos as $type) 
                    <option value="{{ $type->ID }}">{{ $type->NAME }}</option> 
                    @endforeach
                </select>  
            </div>
        </div>  
        <div class="clearfix margin-top-10"></div>
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 margin-top-10">
            <label class="form-label">Gerar Senha: <i class="fa fa-question-circle input-help" data-index="campos-motorista-dia-salario" title="O que é isso ? Clique para saber"></i></label>
            <span class="input-with-icon right">
                <i class=""></i>
                <select class="chosen-select" id="gerar_senha" name="gerar_senha">
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
        <div class="col-md-12 text-center hidden" id="loading">
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