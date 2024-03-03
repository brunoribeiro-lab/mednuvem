@php
use \App\Providers\Utils;
use Illuminate\Support\Carbon;
@endphp
<div class="row">
    <div class="col-md-12">
        <h3><a href="javascript:;" id="backTo"><i class="fa fa-arrow-left"></i> Voltar</a></h3>
        <hr>
    </div>
    <div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">      
        <dt>Identificação : </dt>            
        <em><p>{{ $data->doc_index }}</p></em>
        <dt>Título : </dt>            
        <em><p>{{ $data->doc_title }}</p></em>
    </div>

    <div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">   
        @if ($data->updated_at)
        <dt>Atualizado em : </dt>            
        <em><p>{{ Utils::dataCompletaPTBR($data->updated_at) }}</p></em>
        @endif
        <dt>Cadastrado em : </dt>            
        <em><p>{{ Utils::dataCompletaPTBR($data->created_at) }}</p></em>
    </div>
    <div class="col-md-12">
        <dt>Texto : </dt>            
        <em><p>{!! $data->doc_text !!}</p></em>
    </div>
</div>