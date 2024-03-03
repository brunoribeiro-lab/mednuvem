<form method="POST" action="javascript:;" id="formModalUpdate" enctype="multipart/form-data"> 
    @csrf
    <div class="row">
        <div class="col-md-12">
            <h3><a href="javascript:;" id="backTo"><i class="fa fa-arrow-left"></i> Voltar</a></h3>
            <hr>
        </div>
        <input type="hidden" value="{{ $data->ID }}" name="id">
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <label class="form-label"><span id="field-required">*</span> Nome do Cargo</label>
            <span class="input-with-icon right">
                <i class=""></i>
                <input class="form-control" id="name" name="name" type="text" value="{{ $data->NAME }}" />
            </span>
        </div> 
        <div class="col-xxl-2 col-xl-2 col-lg-4 col-md-6 col-sm-12">
            <div class="row">
                <label class="form-label">Acesso Total</label>
                <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6 col-sm-6 col-xs-6 form-check" style="margin-left: 10px;">
                    <input type="radio" value="1" class="form-check-input" name="root_mode" id="form-root_mode-1"{!! $data->ROOT_ACCESS ? ' checked=""' : '' !!} > 
                    <label for="form-root_mode-1" class="form-check-label">Sim</label>
                </div>
                <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6 col-sm-6 col-xs-6 form-check">
                    <input type="radio" value="0" class="form-check-input" name="root_mode" id="form-root_mode-2"{!! !$data->ROOT_ACCESS ? ' checked=""' : '' !!}> 
                    <label for="form-root_mode-2" class="form-check-label">Não</label>
                </div>
            </div>
        </div>  
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-top-10">
            <label class="form-label"><span id="field-required">*</span> Descrição</label>
            <span class="input-with-icon  right">
                <i class=""></i>
                <textarea  class="form-control"  name="text" id="text" name="text">{{ $data->DESCRIPTION }}</textarea>
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