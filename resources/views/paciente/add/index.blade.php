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
            <label class="form-label"><span id="field-required">*</span> Nome do Paciente: <i class="fa fa-question-circle input-help" data-index="paciente-nome" title="O que é isso ? Clique para saber"></i></label>
            <span class="input-with-icon right">
                <i class=""></i>
                <input class="form-control" id="nome" name="nome" type="text" />
            </span>
        </div>   
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <label class="form-label">CPF <i class="fa fa-question-circle input-help" data-index="paciente-cpf" title="O que é isso ? Clique para saber"></i></label>
            <span class="input-with-icon right">
                <i class=""></i>
                <input class="form-control" id="cpf" maxlength="15" type="tel" name="cpf" />
            </span>
        </div> 
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <label class="form-label">Celular: <i class="fa fa-question-circle input-help" data-index="paciente-celular" title="O que é isso ? Clique para saber"></i></label>
            <span class="input-with-icon right">
                <i class=""></i>
                <input class="form-control phone" id="celular" name="celular" type="tel" />
            </span>
        </div>   
        @if(Session::get('is_root') || Auth::user()->user_account_type == $config->clinica)
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <label class="form-label"><span id="field-required">*</span> Médico:</label>
            <div class="input-with-icon right">
                <i class=""></i>
                <select id="medico" name="medico" class="chosen-select-full">
                    <option value="">-- Selecione --</option>
                    @foreach (\App\Models\Medico::listar() as $clinica)
                    <option value="{{ $clinica->id }}">{{ $clinica->nome }}</option>
                    @endforeach
                </select>  
            </div>
        </div> 
        @endif
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