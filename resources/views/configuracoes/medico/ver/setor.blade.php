@php
use App\Providers\Utils;
use App\Providers\Converter; 
@endphp
<div class="row">
    <div class="col-md-12">
        <h3><a href="javascript:;" id="backTo"><i class="fa fa-arrow-left"></i> Voltar</a></h3>
        <hr>
    </div>
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">      
        <dt>Código: </dt>            
        <em><p>SET{{ str_pad($data->id, 6, "0", STR_PAD_LEFT) }}</p></em>
    </div>
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">      
        <dt>Nome do Setor: </dt>            
        <em><p>{{ $data->nome }}</p></em>
    </div> 
    <div class="clearfix"></div>
    @if(Session::get('is_root'))
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">   
        <dt>Usuário do Grupo: </dt>            
        <em><p>{{ $data->source_fullname }}</p></em>
    </div>
    @endif
    @if ($data->criado_por)
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">   
        <dt>Cadastrado por: </dt>
        <em><p>{{ $data->creator_fullname }}</p></em>
    </div>
    @endif
    @if($data->atualizado_por)
    <div class = "form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">
        <dt>Atualizado por: </dt>
        <em><p>{{ $data->updater_fullname }}</p></em>
    </div>
    @endif
    @if ($data->atualizado_em)
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">   
        <dt>Atualizado em: </dt>            
        <em><p>{{ Utils::dataCompletaPTBR($data->atualizado_em) }}</a></p></em>
    </div>
    @endif
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">   
        <dt>Cadastrado em: </dt>            
        <em><p>{{ Utils::dataCompletaPTBR($data->criado_em) }}</p></em>
    </div>
</div> 