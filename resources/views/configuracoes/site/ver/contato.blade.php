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
        <em><p>{{ $data->nome }}</p></em>
    </div>
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">   
        <dt>Email: </dt>            
        <em><p>{{ $data->email }}</p></em>
    </div> 
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">   
        <dt>Telefone: </dt>            
        <em><p>{{ Utils::mask($data->telefone, Utils::$MASK_PHONE ) }}</p></em>
    </div>
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">   
        <dt>IP: </dt>            
        <em><p>{{ $data->IP }}</p></em>
    </div>
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">   
        <dt>Navegador: </dt>            
        <em><p>{{ $data->navegador }}</p></em>
    </div>
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">   
        <dt>Plataforma: </dt>            
        <em><p>{{ $data->plataforma }}</p></em>
    </div> 
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">   
        <dt>Cadastrado em: </dt>            
        <em><p>{{ Utils::dataCompletaPTBR($data->cadastrado) }}</p></em>
    </div>
    <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">   
        <dt>Mensagem: </dt>            
        <em><p>{{ $data->mensagem }}</p></em>
    </div> 
</div> 