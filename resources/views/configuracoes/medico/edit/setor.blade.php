@php
use App\Providers\Converter;
@endphp
<form method="POST" action="javascript:;" id="formModalUpdate" enctype="multipart/form-data"> 
    @csrf
    <div class="row">
        <div class="col-md-12">
            <h3><a href="javascript:;" id="backTo"><i class="fa fa-arrow-left"></i> Voltar</a></h3>
            <hr>
        </div>
        <input type="hidden" value="{{ $data->id }}" name="id">
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <label class="form-label"><span id="field-required">*</span> Nome do Setor</label>
            <span class="input-with-icon right">
                <i class=""></i>
                <input class="form-control" id="nome" name="nome" type="text" value="{{ $data->nome }}" />
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