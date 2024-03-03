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
        <em><p>{{ $data->ref }}</p></em>
    </div>

    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12 col-sm-12 col-xs-12">      
        <dt>Estado: </dt>            
        <em><p>{{ $data->sended ? "Enviado" : "Pendente" }}</p></em>
    </div>
    <div class="form-group col-lg-3 col-md-3 col-sm-12 col-xs-12">      
        <dt>Assunto: </dt>            
        <em><p>{{ $data->subject }}</p></em>
    </div>
    @if($data->error)
        <div class="form-group col-xxl-4 col-xl-4 col-lg-6 col-md-6 col-sm-12 col-xs-12 col-sm-12 col-xs-12">      
            <dt>Erro do Envio: </dt>            
            <em><p>{{ $data->error }}</p></em>
        </div>
    @endif
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">   
        <dt>Cadastrado em: </dt>            
        <em><p>{{ Utils::dataCompletaPTBR($data->created_at) }}</p></em>
    </div> 
    @if($data->updated_at)
    <div class = "form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">
        <dt>Enviado em : </dt>
        <em><p>{{ Utils::dataCompletaPTBR($data->sended_at) }}</p></em>
    </div>
    @endif
    <div class="col-md-12">
        <dt>Email: </dt>            
        <em><p>{!! $data->email !!}</p></em>
    </div>
</div>
