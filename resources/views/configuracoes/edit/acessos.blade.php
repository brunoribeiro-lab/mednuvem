@php
use App\Models\AccountType;
@endphp
<form method="POST" action="javascript:;" id="formAccessByAccountType" enctype="multipart/form-data"> 
    @csrf 
    <div class="row">
        <div class="col-md-12">
            <h3><a href="javascript:;" id="backTo"><i class="fa fa-arrow-left"></i> Voltar</a></h3>
            <hr>
        </div>
        <input type="hidden" value="{{ $data['account']["ID"] }}" name="id">
        @foreach ($data['LIST'] as $menu)
        <div class="card padding-10"> 
            @if (!(int) $menu['SUB'])
            <div class="card-header no-padding" style="padding: 10px !important;">
                <h4 class="card-title">{{ $menu['NAME'] }}</h4>
            </div><!-- end card header -->
            @endif
            @if ((int) $menu['SUB'])
            <div class="card-header no-padding" style="padding: 10px !important;">
                <h4 class="card-title">{{ $menu['NAME'] }}</h4>
            </div><!-- end card header -->
            @endif
            <div class="card-body no-padding row" style="padding: 10px !important;">
                <!-- Ações do Menu -->
                @if (!(int) $menu['SUB'])
                {!! AccountType::item($data['account']["ID"], $menu['XML'], $menu['ID']) !!}
                @endif
                <!-- Ações do submenu -->
                @if ((int) $menu['SUB'])
                @foreach ($menu['submenus'] as $submenu)
                @if ((int) $submenu['SUB'])
                <!-- subsubmenu -->
                <div class="card margin-top-20">
                    <div class="card-header no-padding" style="padding: 10px !important;">
                        <h4 class="card-title">{{ $submenu["NAME"] }}</h4>
                    </div>
                    <div class="card-body no-padding row" style="padding: 10px !important;">
                        @foreach ($submenu['submenus'] as $subsubmenu) 
                        <h5 class="margin-top-10">{{ $subsubmenu["NAME"] }}</h5>
                        {!! AccountType::item($data['account']["ID"], $subsubmenu['XML'], $menu['ID'], $submenu['ID'], $subsubmenu['ID']) !!}
                        @endforeach
                    </div>
                </div>
                @endif
                @if (!(int) $submenu['SUB'])
                <h5 class="margin-top-10">{{ $submenu["NAME"] }}</h5>
                {!! AccountType::item($data['account']["ID"], $submenu['XML'], $menu['ID'], $submenu['ID']) !!}
                @endif
                @endforeach
                @endif  
            </div> 
        </div>
        @endforeach
        <div class="col-md-12 text-center hidden" id="loading">
            <p>Carregando...</p>
            <div class="spinner-border text-primary m-1" role="status">
                <span class="sr-only">Carregando...</span>
            </div>
        </div>
        <div class="col-md-12 margin-top-15">
            <hr>
            <div class="col-lg-3 col-md-4 col-xs-12 centered-block margin-top-15">
                <button type="submit" class="btn btn-lg btn-success btn-block"><i class="far fa-save"></i> Salvar</button> 
            </div>
        </div>
    </div>
</form>