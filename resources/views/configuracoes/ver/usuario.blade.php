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
        <em><p>{{ $data->nome_completo }}</p></em>
    </div>
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">   
        <dt>Email: </dt>            
        <em><p>{{ $data["user_email"] }}</p></em>
    </div>
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">   
        <dt>{{ strlen($data['user_name']) == 11 ? 'CPF' : 'CNPJ'  }} </dt>            
        <em><p>{{strlen($data['user_name']) == 11 ? Utils::mask($data['user_name'], Utils::$MASK_CPF) : Utils::mask($data['user_name'], Utils::$MASK_CNPJ) }}</p></em>
    </div>
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">   
        <dt>Cargo: </dt>            
        <em><p>{{ $data["NAME"] }}</p></em>
    </div>
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">   
        <dt>Sexo: </dt>            
        <em><p>{{ $data["user_sex"] == 'M' ? "Masculino" : "Feminino" }}</p></em>
    </div>
    @if ($data->user_last_login)
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">   
        <dt>Último Login: </dt>            
        <em><p>{{ Utils::dataCompletaPTBR($data->user_last_login) }}</a></p></em>
    </div>
    @endif
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">   
        <dt>Cadastrado em: </dt>            
        <em><p>{{ Utils::dataCompletaPTBR($data->user_creation) }}</p></em>
    </div>
</div> 