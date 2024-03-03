<form method="POST" action="javascript:;" id="formModalUpdate" enctype="multipart/form-data"> 
    @csrf
    <input type="hidden" value="{{ $data->id }}" name="id">
    <div class="row">
        <div class="col-md-12">
            <h3><a href="javascript:;" id="backTo"><i class="fa fa-arrow-left"></i> Voltar</a></h3>
            <hr>
        </div>
        <div class="col-md-6"> 
            <div class="form-group"> 
                <label class="form-label"><span id="field-required">*</span> Título</label> 
                <span class="input-with-icon right">
                    <input type="text" class="form-control" id="title" name="title" value="{{ $data->title }}"> 
                </span>
            </div> 
        </div>
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12"> 
            <div class="form-group"> 
                <label class="form-label"><span id="field-required">*</span> Link do Vídeo</label> 
                <span class="input-with-icon right">
                    <i class=""></i>
                    <input type="text" class="form-control" id="youtube" name="youtube" value="{{ $data->youtube }}"> 
                </span>
            </div> 
        </div>
        <div class="col-md-6"> 
            <div class="form-group"> 
                <label class="form-label"><span id="field-required">*</span> Palavras-Chaves</label> 
                <span class="input-with-icon right">
                    <i class=""></i>
                    <input type="text" class="form-control" id="keyword" name="keyword" value="{{ $data->keywords }}"> 
                </span>
            </div> 
        </div>
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12"> 
            <div class="form-group"> 
                <label class="form-label"><span id="field-required">*</span> Página Exclusiva</label> 
                <span class="input-with-icon right">
                    <i class=""></i>
                    <select class="chosen-select" name="pagina">
                        <option value=""> -- Selecione --</option>
                        @foreach($menu as $m)
                        <optgroup label="{{ $m['title'] }}">
                            @if(!$m['submenu'])
                            <option value="{{ $m['link'] }}" {!! $m['link'] ==  $data->link ? ' selected=""' : ''  !!}>{{ $m['title'] }}</option>
                            @else
                            @foreach($m['submenu'] as $submenu)
                            @if(!$submenu['submenus'])
                            <option value="{{ $submenu['LINK'] }}"{!! $submenu['LINK'] ==  $data->link ? ' selected=""' : ''  !!}>{{ $submenu['NAME'] }}</option>
                            @else
                        <optgroup label="{{$submenu['origem_menu']}}/{{ $submenu['NAME'] }}">
                            @foreach($submenu['submenus'] as $subsubmenu)
                            <option value="{{ $subsubmenu['LINK'] }}"{!! $subsubmenu['LINK'] ==  $data->link ? ' selected=""' : ''  !!}>{{ $subsubmenu['NAME'] }}</option>
                            @endforeach
                        </optgroup>
                        @endif
                        @endforeach
                        @endif
                        </optgroup>
                        @endforeach
                    </select>
                </span>
            </div> 
        </div>
        <div class="col-md-12"> 
            <div class="form-group">
                <label class="form-label">Texto</label>
                <div class="input-with-icon  right">
                    <div id="editor">{!! $data->description !!}</div>
                    <textarea id="text" hidden="" class="form-control" name="text">{!! $data->description !!}</textarea>
                </div>
            </div>
        </div>
        <div class="col-md-12 text-center hidden" id="loading">
            <p>Carregando...</p>
            <div class="spinner-border text-primary m-1" role="status">
                <span class="sr-only">Carregando...</span>
            </div>
        </div>
        <div class="col-md-12 margin-top-15">
            <hr>
            <div class="col-lg-3 col-md-4 col-xs-12 centered-block margin-top-15">
                <button type="submit" class="btn btn-success btn-block btn-lg"><i class="fa fa-sync"></i> Salvar</button>
            </div>
        </div>
    </div>
</form>