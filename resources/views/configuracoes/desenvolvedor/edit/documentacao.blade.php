<form method="POST" action="javascript:;" id="formModalUpdate" enctype="multipart/form-data"> 
    @csrf
    <input type="hidden" value="{{ $data->id }}" name="id">
    <div class="row">
        <div class="col-md-12">
            <h3><a href="javascript:;" id="backTo"><i class="fa fa-arrow-left"></i> Voltar</a></h3>
            <hr>
        </div>
        <div class="col-md-6"> 
            <div class="form-group"> 
                <label class="form-label"><span id="field-required">*</span> Identificação</label> 
                <span class="input-with-icon right">
                    <input type="text" class="form-control" id="index" name="index" value="{{ $data->doc_index }}"> 
                </span>
            </div> 
        </div>
        <div class="col-md-6"> 
            <div class="form-group">
                <label class="form-label">Título</label>
                <div class="right">
                    <input type="text" class="form-control" id="title" name="title" value="{{ $data->doc_title }}"> 
                </div>
            </div>
        </div>
        <div class="col-md-12"> 
            <div class="form-group">
                <label class="form-label">Texto</label>
                <div class="input-with-icon  right">
                    <div id="editor">{!! $data->doc_text !!}</div>
                    <textarea id="text" hidden="" class="form-control" name="text">{!! $data->doc_text !!}</textarea>
                </div>
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