<form method="POST" action="javascript:;" id="formModalUpdate" enctype="multipart/form-data"> 
    @csrf
    <input type="hidden" value="{{ $data->ID }}" name="id">
    <div class="row">
        <div class="col-md-12">
            <h3><a href="javascript:;" id="backTo"><i class="fa fa-arrow-left"></i> Voltar</a></h3>
            <hr>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12"> 
            <div class="form-group"> 
                <label class="form-label"><span id="field-required">*</span> Nome</label> 
                <span class="input-with-icon right">
                    <input type="text" class="form-control" value="{{ $data->NAME }}" disabled=""> 
                </span>
            </div> 
        </div>
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12"> 
            <div class="form-group">
                <label class="form-label"><span id="field-required">*</span> Código</label>
                <div class="right">
                    <input type="text" class="form-control" id="cod" name="cod" value="{{ $data->CODE }}"> 
                </div>
            </div>
        </div>
        @if($data->CODE_ADD)
            <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12"> 
                <div class="form-group"> 
                    <label class="form-label"><span id="field-required">*</span> Código Adicionar: <i class="fa fa-question-circle input-help" data-index="configuracao-menu-add-cod" title="O que é isso ? Clique para saber"></i></label> 
                    <span class="input-with-icon right">
                        <i class=""></i>
                        <input value="{{ $data->CODE_ADD }}" type="text" class="form-control" id="cod_add" name="cod_add"> 
                    </span>
                </div> 
            </div>
        @endif
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 margin-xs-top-10 margin-sm-top-10"> 
            <div class="form-group"> 
                <label class="form-label"><span id="field-required">*</span> Posição: <i class="fa fa-question-circle input-help" data-index="configuracao-menu-pos" title="O que é isso ? Clique para saber"></i></label> 
                <span class="input-with-icon right">
                    <i class=""></i>
                    <select class="chosen-select" id="pos" name="pos">
                        @for ($i = 1; $i <= 20; $i++)
                        <option value="{{ $i }}" {!! $i == $data->POSITION ? ' selected=""' : ''  !!}>{{ $i; }}° {{ in_array($i, $usados) && $i !== (int) $data->POSITION ? '( Em Uso )' : ''  }}</option>
                        @endfor
                    </select>
                </span>
            </div> 
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
                <button type="submit" class="btn btn-success btn-block btn-lg"><i class="fa fa-sync"></i> Salvar</button>
            </div>
        </div>
    </div>
</form>