<form method="POST" action="javascript:;" id="formModalAddNew" enctype="multipart/form-data"> 
    @csrf
    <div class="row">
        <div class="col-md-12">
            <h3><a href="javascript:;" id="backTo"><i class="fa fa-arrow-left"></i> Voltar</a></h3>
            <hr>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <label class="form-label"><span id="field-required">*</span> Setor:</label>
            <div class="input-with-icon right">
                <i class=""></i>
                <select id="setor" name="setor" class="chosen-select">
                    <option value="">-- Selecione --</option>
                    @foreach ($data['setores'] as $setor)
                        <option value="{{ $setor->id }}">{{ Session::get('is_root') ? $setor->nome .  " - FUN". str_pad($setor->id, 6, "0", STR_PAD_LEFT) : $setor->nome }}</option>
                    @endforeach
                </select>  
            </div>
        </div> 
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <label class="form-label"><span id="field-required">*</span> Nome da Função</label>
            <span class="input-with-icon right">
                <i class=""></i>
                <input class="form-control" id="nome" name="nome" type="text" />
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