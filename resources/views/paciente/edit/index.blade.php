@php
use App\Providers\Utils;
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
            <label class="form-label"><span id="field-required">*</span> Nome do Cliente: <i class="fa fa-question-circle input-help" data-index="paciente-nome" title="O que é isso ? Clique para saber"></i></label>
            <span class="input-with-icon right">
                <i class=""></i>
                <input class="form-control" id="nome" name="nome" type="text" value="{{ $data->nome }}" readonly=""  />
            </span>
        </div>    
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <label class="form-label">CPF <i class="fa fa-question-circle input-help" data-index="paciente-cpf" title="O que é isso ? Clique para saber"></i></label>
            <span class="input-with-icon right">
                <i class=""></i>
                <input class="form-control" id="cpf" maxlength="15" type="tel" name="cpf" value="{{ $data->CPF ? Utils::mask($data->CPF, Utils::$MASK_CPF) : '' }}" />
            </span>
        </div> 
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <label class="form-label">Celular: <i class="fa fa-question-circle input-help" data-index="paciente-celular" title="O que é isso ? Clique para saber"></i></label>
            <span class="input-with-icon right">
                <i class=""></i>
                <input class="form-control phone" id="celular" name="celular" type="tel" value="{{ $data->telefone ? Utils::mask($data->telefone, Utils::$MASK_PHONE) : '' }}" />
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
                    <option value="{{ $clinica->id }}" {!! $clinica->grupo == $data->grupo ? ' selected=""' : '' !!}>{{ $clinica->nome }}</option>
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
                <button type="submit" class="btn btn-lg btn-success btn-block"><i class="far fa-save"></i> Salvar</button> 
            </div>
        </div>
    </div>
</form>