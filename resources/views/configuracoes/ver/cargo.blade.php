@php
use App\Providers\Utils;
@endphp
<div class="row">
    <div class="col-md-12">
        <h3><a href="javascript:;" id="backTo"><i class="fa fa-arrow-left"></i> Voltar</a></h3>
        <hr>
    </div>
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">      
        <dt>Nome: </dt>            
        <em><p>{{ $data->NAME }}</p></em>
    </div>
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">   
        <dt>Acesso Total: </dt>            
        <em><p>{{ $data["ROOT_ACCESS"] ? "Sim" : "Não" }}</p></em>
    </div> 
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">   
        <dt>Descrição: </dt>            
        <em><p>{{ $data->DESCRIPTION }}</p></em>
    </div>
    @if ($data->USER_CREATED)
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">   
        <dt>Cadastrado por: </dt>
        <em><p>{{ $data->criado_por_name }}</p></em>
    </div>
    @endif
    @if($data->USER_UPDATED)
    <div class = "form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">
        <dt>Atualizado por: </dt>
        <em><p>{{ $data->atualizado_por_name }}</p></em>
    </div>
    @endif
    @if ($data->UPDATED)
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">   
        <dt>Atualizado em: </dt>            
        <em><p>{{ Utils::dataCompletaPTBR($data->UPDATED) }}</a></p></em>
    </div>
    @endif
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">   
        <dt>Cadastrado em: </dt>            
        <em><p>{{ Utils::dataCompletaPTBR($data->CREATED) }}</p></em>
    </div>
</div> 