<div class="col-xxl-2 col-md-6 col-lg-3 col-xs-12 margin-top-10" id="feriado-{{ $index }}">
    <label class="form-label"><span class="field-required">*</span> Data <i class="fa fa-question-circle input-help" data-index="variaveis-feriado" title="O que é isso ? Clique para saber"></i></label>
    <div class="input-group">
        <input id="feriado{{ $index }}" name="feriado[{{ $index }}]" type="tel" class="form-control feriado">
        <div class="input-group-append">
            <button class="btn btn-danger remover-feriado" title="Remover Feriado" type="button" data-id="{{ $index }}"><i class="fa fa-trash"></i></button>
        </div>
    </div> 
</div> 