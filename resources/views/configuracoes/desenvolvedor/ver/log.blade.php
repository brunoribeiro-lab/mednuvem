@php
use \App\Providers\Utils;
use Illuminate\Support\Carbon;
@endphp
<div class="row">
    <div class="col-md-12">
        <h3><a href="javascript:;" id="backTo"><i class="fa fa-arrow-left"></i> Voltar</a></h3>
        <hr>
    </div>  
    <div class="form-group col-xxl-1 col-xl-1 col-lg-2 col-md-3 col-sm-12 col-xs-12">     
        <dt>Cód. Log: </dt>            
        <em><p>{{ sprintf("LOG%s", str_pad($data->id, 6, "0", STR_PAD_LEFT)) }}</p></em>
    </div>
    <div class="form-group col-xxl-1 col-xl-1 col-lg-2 col-md-3 col-sm-12 col-xs-12">     
        <dt>Cód. Erro: </dt>            
        <em><p>{{ $data->level }}</p></em>
    </div> 
    <div class="form-group col-xxl-1 col-xl-1 col-lg-2 col-md-3 col-sm-12 col-xs-12">     
        <dt>Tipo: </dt>            
        <em><p>{{ $data->level_name }}</p></em>
    </div>  
    <div class="form-group col-xxl-1 col-xl-1 col-lg-2 col-md-3 col-sm-12 col-xs-12">     
        <dt>Canal: </dt>            
        <em><p>{{ $data->channel }}</p></em>
    </div> 
    <div class="form-group col-lg-3 col-md-3 col-sm-12 col-xs-12">  
        <dt>Cadastrado em: </dt>
        <em><p>{{ Utils::dataCompletaPTBR($data->created_at) }}</p></em>
    </div>
    <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">     
        <dt>Mensagem: </dt>            
        <em><p>{{ $data->message }}</p></em>
    </div>  
    <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">     
        <dt>Contexto: </dt>            
        <em><p>{{ $data->context }}</p></em>
    </div>  
</div>