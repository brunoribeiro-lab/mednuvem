@php
use \App\Providers\Utils;
use Illuminate\Support\Carbon;
@endphp
<div class="row">
    <div class="col-md-12">
        <h3><a href="javascript:;" id="backTo3"><i class="fa fa-arrow-left"></i> Voltar</a></h3>
        <hr>
    </div>  
    <div class="form-group col-xxl-1 col-xl-2 col-lg-3 col-md-3 col-sm-12 col-xs-12">     
        <dt>Cód do Menu: </dt>            
        <em><p>{{ $data->SUB ? "-" : $data->CODE }}</p></em>
    </div>
    <div class="form-group col-xxl-2 col-xl-2 col-lg-3 col-md-3 col-sm-12 col-xs-12">     
        <dt>Nome: </dt>            
        <em><p>{{ $data->NAME }}</p></em>
    </div>
    <div class="form-group col-xxl-3 col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12">     
        <dt>Link: </dt>            
        <em><p>{!! $data->SUB ? "-" : "<a href='{$data->LINK}' target='_blank'>{$data->LINK}</a>" !!}</p></em>
    </div>
    <div class="form-group col-xxl-1 col-xl-2 col-lg-3 col-md-3 col-sm-12 col-xs-12">     
        <dt>Posição: </dt>            
        <em><p>{{ $data->POSITION }}°</p></em>
    </div>  
    @if (!empty($data['UPDATED']))
    <div class="form-group col-lg-3 col-md-3 col-sm-12 col-xs-12">  
        <dt>Atualizado em : </dt>            
        <em><p>{{ Utils::dataCompletaPTBR($data->UPDATED) }}</p></em>
    </div>
    @endif   
</div>