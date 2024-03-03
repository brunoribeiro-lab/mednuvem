@php
use \App\Providers\Utils;
use Illuminate\Support\Carbon;
@endphp
<div class="row">
    <div class="col-md-12">
        <h3><a href="javascript:;" id="backTo"><i class="fa fa-arrow-left"></i> Voltar</a></h3>
        <hr>
    </div>   
    <div class="form-group col-lg-3 col-md-3 col-sm-12 col-xs-12">     
        <dt>Título : </dt>            
        <em><p>{{ $data->title }}</p></em>
    </div>
    <div class="form-group col-lg-3 col-md-3 col-sm-12 col-xs-12">     
        <dt>Link : </dt>            
        <em><p><a href="{{ $data->youtube }}" target="_blank">{{ $data->youtube }}</a></p></em>
    </div>
    <div class="form-group col-lg-3 col-md-3 col-sm-12 col-xs-12">     
        <dt>Palavras Chaves : </dt>            
        <em><p>{{ $data->keywords }}</p></em>
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

    @if (!empty($data['updated_at']))
    <div class="form-group col-lg-3 col-md-3 col-sm-12 col-xs-12">  
        <dt>Atualizado em : </dt>            
        <em><p>{{ Utils::dataCompletaPTBR($data->updated_at) }}</p></em>
    </div>
    @endif
    <div class="form-group col-lg-3 col-md-3 col-sm-12 col-xs-12">  
        <dt>Cadastrado em : </dt>            
        <em><p>{{ Utils::dataCompletaPTBR($data->created_at) }}</p></em>
    </div>

    <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">     
        <dt>Foto de Capa: </dt>            
        <em><p><a href='javascript:;' title='Clique aqui para ampliar essa imagem' data-bs-toggle="modal" data-bs-target=".bs-pic-modal-xl" data-path="{{ sprintf("storage/videos/%s", $data->thumbmail) }}"><img src="{{ sprintf("storage/videos/%s", $data->thumbmail) }}" width='200px' height='150px'></a></p></em>
    </div>
    <div class="col-md-12">
        <dt>Texto : </dt>            
        <em><p>{!! $data->description !!}</p></em>
    </div>
</div>