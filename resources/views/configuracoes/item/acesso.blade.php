@php
use App\Models\AccountType;
@endphp

@foreach ($data['acessos'] as $access)
    @if(!empty($access['listing']) && (string) $access['listing'] == "true")
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <div class="form-group">
                <input class="form-check-input" name="listing[{{ $data['menu'] }}]{{ $data['submenu'] ? "[{$data['submenu']}]" : "" }}{{ $data['subsubmenu'] ? "[{$data['subsubmenu']}]" : "" }}" id="listing_{{ $data['menu'] }}_{{ $data['submenu'] ?? "" }}_{{ $data['subsubmenu'] ?? "" }}" {{ AccountType::checked($data['cargo'], 'ACCESS_LISTING', $data['menu'], $data['submenu'], $data['subsubmenu']) }} type="checkbox">
                <label class="form-check-label" for="listing_{{ $data['menu'] }}_{{ $data['submenu'] ? $data['submenu'] : "" }}_{{ $data['subsubmenu'] ? $data['subsubmenu'] : "" }}">Listagem </label>
            </div>
        </div>
    @endif
    @if(!empty($access['form']) && (string) $access['form'] == "true")
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <div class="form-group">
                <input class="form-check-input" name="form[{{ $data['menu'] }}]{{ $data['submenu'] ? "[{$data['submenu']}]" : "" }}{{ $data['subsubmenu'] ? "[{$data['subsubmenu']}]" : "" }}" id="form_{{ $data['menu'] }}_{{ $data['submenu'] ? $data['submenu'] : "" }}_{{ $data['subsubmenu'] ? $data['subsubmenu'] : "" }}" {{ AccountType::checked($data['cargo'], 'ACCESS_FORM', $data['menu'], $data['submenu'], $data['subsubmenu']) }} type="checkbox">
                <label class="form-check-label" for="form_{{ $data['menu'] }}_{{ $data['submenu'] ? $data['submenu'] : "" }}_{{ $data['subsubmenu'] ? $data['subsubmenu'] : "" }}">Formulário</label>
            </div>
        </div>
    @endif
    @if(!empty($access['add']) && (string) $access['add'] == "true")
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <div class="form-group">
                <input class="form-check-input" name="add[{{ $data['menu'] }}]{{ $data['submenu'] ? "[{$data['submenu']}]" : "" }}{{ $data['subsubmenu'] ? "[{$data['subsubmenu']}]" : "" }}" id="add_{{ $data['menu'] }}_{{ $data['submenu'] ? $data['submenu'] : "" }}_{{ $data['subsubmenu'] ? $data['subsubmenu'] : "" }}" {{ AccountType::checked($data['cargo'], 'ACCESS_ADD', $data['menu'], $data['submenu'], $data['subsubmenu']) }} type="checkbox">
                <label class="form-check-label" for="add_{{ $data['menu'] }}_{{ $data['submenu'] ? $data['submenu'] : "" }}_{{ $data['subsubmenu'] ? $data['subsubmenu'] : "" }}">Adicionar</label>
            </div>
        </div>
    @endif
    @if(!empty($access['detail']) && (string) $access['detail'] == "true")
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <div class="form-group">
                <input class="form-check-input" name="preview[{{ $data['menu'] }}]{{ $data['submenu'] ? "[{$data['submenu']}]" : "" }}{{ $data['subsubmenu'] ? "[{$data['subsubmenu']}]" : "" }}" id="preview_{{ $data['menu'] }}_{{ $data['submenu'] ? $data['submenu'] : "" }}_{{ $data['subsubmenu'] ? $data['subsubmenu'] : "" }}" {{ AccountType::checked($data['cargo'], 'ACCESS_PREVIEW', $data['menu'], $data['submenu'], $data['subsubmenu']) }} type="checkbox">
                <label class="form-check-label" for="preview_{{ $data['menu'] }}_{{ $data['submenu'] ? $data['submenu'] : "" }}_{{ $data['subsubmenu'] ? $data['subsubmenu'] : "" }}">Detalhes</label>
            </div>
        </div>
    @endif
    @if(!empty($access['update']) && (string) $access['update'] == "true")
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <div class="form-group">
                <input class="form-check-input" name="update[{{ $data['menu'] }}]{{ $data['submenu'] ? "[{$data['submenu']}]" : "" }}{{ $data['subsubmenu'] ? "[{$data['subsubmenu']}]" : "" }}" id="update_{{ $data['menu'] }}_{{ $data['submenu'] ? $data['submenu'] : "" }}_{{ $data['subsubmenu'] ? $data['subsubmenu'] : "" }}" {{ AccountType::checked($data['cargo'], 'ACCESS_UPDATE', $data['menu'], $data['submenu'], $data['subsubmenu']) }} type="checkbox">
                <label class="form-check-label" for="update_{{ $data['menu'] }}_{{ $data['submenu'] ? $data['submenu'] : "" }}_{{ $data['subsubmenu'] ? $data['subsubmenu'] : "" }}">Editar</label>
            </div>
        </div>
    @endif
    @if(!empty($access['remove']) && (string) $access['remove'] == "true")
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <div class="form-group">
                <input class="form-check-input" name="remove[{{ $data['menu'] }}]{{ $data['submenu'] ? "[{$data['submenu']}]" : "" }}{{ $data['subsubmenu'] ? "[{$data['subsubmenu']}]" : "" }}" id="remover_{{ $data['menu'] }}_{{ $data['submenu'] ? $data['submenu'] : "" }}_{{ $data['subsubmenu'] ? $data['subsubmenu'] : "" }}" {{ AccountType::checked($data['cargo'], 'ACCESS_REMOVE', $data['menu'], $data['submenu'], $data['subsubmenu']) }} type="checkbox">
                <label class="form-check-label" for="remover_{{ $data['menu'] }}_{{ $data['submenu'] ? $data['submenu'] : "" }}_{{ $data['subsubmenu'] ? $data['subsubmenu'] : "" }}">Excluir</label>
            </div>
        </div>
    @endif
    @if(!empty($access['access']) && (string) $access['access'] == "true")
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <div class="form-group">
                <input class="form-check-input" name="access[{{ $data['menu'] }}]{{ $data['submenu'] ? "[{$data['submenu']}]" : "" }}{{ $data['subsubmenu'] ? "[{$data['subsubmenu']}]" : "" }}" id="access_{{ $data['menu'] }}_{{ $data['submenu'] ? $data['submenu'] : "" }}_{{ $data['subsubmenu'] ? $data['subsubmenu'] : "" }}" {{ AccountType::checked($data['cargo'], 'ACCESS_ACCESS', $data['menu'], $data['submenu'], $data['subsubmenu']) }} type="checkbox">
                <label class="form-check-label" for="access_{{ $data['menu'] }}_{{ $data['submenu'] ? $data['submenu'] : "" }}_{{ $data['subsubmenu'] ? $data['subsubmenu'] : "" }}">Gerenciar Acessos</label>
            </div>
        </div>
    @endif
    @if(!empty($access['pdf']) && (string) $access['pdf'] == "true")
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <div class="form-group">
                <input class="form-check-input" name="pdf[{{ $data['menu'] }}]{{ $data['submenu'] ? "[{$data['submenu']}]" : "" }}{{ $data['subsubmenu'] ? "[{$data['subsubmenu']}]" : "" }}" id="pdf_{{ $data['menu'] }}_{{ $data['submenu'] ? $data['submenu'] : "" }}_{{ $data['subsubmenu'] ? $data['subsubmenu'] : "" }}" {{ AccountType::checked($data['cargo'], 'ACCESS_PDF', $data['menu'], $data['submenu'], $data['subsubmenu']) }} type="checkbox">
                <label class="form-check-label" for="pdf_{{ $data['menu'] }}_{{ $data['submenu'] ? $data['submenu'] : "" }}_{{ $data['subsubmenu'] ? $data['subsubmenu'] : "" }}">Exportar PDF</label>
            </div>
        </div>
    @endif 
    @if(!empty($access['resend']) && (string) $access['resend'] == "true")
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <div class="form-group">
                <input class="form-check-input" name="resend[{{ $data['menu'] }}]{{ $data['submenu'] ? "[{$data['submenu']}]" : "" }}{{ $data['subsubmenu'] ? "[{$data['subsubmenu']}]" : "" }}" id="resend_{{ $data['menu'] }}_{{ $data['submenu'] ? $data['submenu'] : "" }}_{{ $data['subsubmenu'] ? $data['subsubmenu'] : "" }}" {{ AccountType::checked($data['cargo'], 'ACCESS_RESEND', $data['menu'], $data['submenu'], $data['subsubmenu']) }} type="checkbox">
                <label class="form-check-label" for="resend_{{ $data['menu'] }}_{{ $data['submenu'] ? $data['submenu'] : "" }}_{{ $data['subsubmenu'] ? $data['subsubmenu'] : "" }}">Reenviar Email</label>
            </div>
        </div>
    @endif 
    @if(!empty($access['historic']) && (string) $access['historic'] == "true")
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <div class="form-group">
                <input class="form-check-input" name="historic[{{ $data['menu'] }}]{{ $data['submenu'] ? "[{$data['submenu']}]" : "" }}{{ $data['subsubmenu'] ? "[{$data['subsubmenu']}]" : "" }}" id="historic_{{ $data['menu'] }}_{{ $data['submenu'] ? $data['submenu'] : "" }}_{{ $data['subsubmenu'] ? $data['subsubmenu'] : "" }}" {{ AccountType::checked($data['cargo'], 'ACCESS_HISTORIC', $data['menu'], $data['submenu'], $data['subsubmenu']) }} type="checkbox">
                <label class="form-check-label" for="historic_{{ $data['menu'] }}_{{ $data['submenu'] ? $data['submenu'] : "" }}_{{ $data['subsubmenu'] ? $data['subsubmenu'] : "" }}">Histórico</label>
            </div>
        </div>
    @endif 
@endforeach