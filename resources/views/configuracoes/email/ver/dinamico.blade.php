@php
use App\Providers\Utils;
@endphp
<div class="row">
    <div class="col-md-12">
        <h3><a href="javascript:;" id="backTo"><i class="fa fa-arrow-left"></i> Voltar</a></h3>
        <hr>
    </div>
    <div class="form-group col-xxl-2 col-xl-2 col-lg-2 col-md-3 col-sm-12 col-xs-12">   
        <dt>Identificação: </dt>            
        <em><p>{{ $data->index }}</p></em>
    </div>
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">      
        <dt>Assunto: </dt>            
        <em><p>{{ $data->subject }}</p></em>
    </div>
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">   
        <dt>Cadastrado em: </dt>            
        <em><p>{{ Utils::dataCompletaPTBR($data->created_at) }}</p></em>
    </div>
    @if ($data->created_by)
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">   
        <dt>Cadastrado por :</dt>
        <em><p>{{ $data->criado_por_name }}</p></em>
    </div>
    @endif
    @if($data->updated_by)
    <div class = "form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">
        <dt>Atualizado por :</dt>
        <em><p>{{ $data->atualizado_por_name }}</p></em>
    </div>
    @endif
    @if($data->updated_at)
    <div class = "form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">
        <dt>Atualizado em : </dt>
        <em><p>{{ Utils::dataCompletaPTBR($data->updated_at) }}</p></em>
    </div>
    @endif
    <div class="col-md-12">
        <dt>Email: </dt>            
        <em><p>{!! $data->message !!}</p></em>
    </div>
</div>
