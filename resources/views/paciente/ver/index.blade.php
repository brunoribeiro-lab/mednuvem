@php
use App\Providers\Utils;
@endphp
<div class="row">
    <div class="col-md-12">
        <h3><a href="javascript:;" id="backTo"><i class="fa fa-arrow-left"></i> Voltar</a></h3>
        <hr>
    </div> 
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">   
        <dt>Código: </dt>            
        <em><p>PAC{{ str_pad($data->id, 6, "0", STR_PAD_LEFT) }}</p></em>
    </div>  
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">      
        <dt>Nome do Paciente: </dt>            
        <em><p>{{ $data->nome }}</p></em>
    </div> 
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">      
        <dt>Celular: </dt>            
        <em><p>{{ $data->telefone ? Utils::mask($data->telefone, Utils::$MASK_PHONE) : '-' }}</p></em>
    </div>  
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">      
        <dt>CPF: </dt>            
        <em><p>{{ $data->CPF ? Utils::mask($data->CPF, Utils::$MASK_CPF) : '-' }}</p></em>
    </div>  
    <div class="clearfix"></div>
    @if(Session::get('is_root'))
        <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">   
            <dt>Usuário do Grupo: </dt>            
            <em><p>{{ $data->source_fullname }}</p></em>
        </div>
    @endif
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">   
        <dt>Cadastrado por: </dt>            
        <em><p>{{ $data->creator_fullname }}</p></em>
    </div> 
    @if($data->updatedBy)
        <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">   
            <dt>Atualizado por: </dt>            
            <em><p>{{ $data->updater_fullname }}</p></em>
        </div>
    @endif
    <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">   
        <dt>Cadastrado em: </dt>            
        <em><p>{{ Utils::dataCompletaPTBR($data->createdAt) }}</p></em>
    </div>
    @if($data->updatedAt)
        <div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">   
            <dt>Atualizado em: </dt>            
            <em><p>{{ Utils::dataCompletaPTBR($data->updatedAt) }}</p></em>
        </div>
    @endif
</div>
