@php
use Illuminate\Support\Str
@endphp

<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <button type="button" class="btn btn-sm px-3 font-size-16 d-lg-none header-item waves-effect waves-light" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                <i class="fa fa-fw fa-bars fa-2x"></i>
            </button>
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{ config('app.url') }}/SGS" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ Session::get('SGS_logo_dark') }}" alt="" height="50">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ Session::get('SGS_logo_dark') }}" class="img-fluid" width="230px" height="57px"> 
                    </span>
                </a>
                <a href="{{ config('app.url') }}/SGS" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ Session::get('SGS_logo') }}" alt="" height="50">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ Session::get('SGS_logo') }}"  class="img-fluid" width="230px" height="57px"> 
                    </span>
                </a>
            </div>
            <form class="app-search d-none d-lg-block">
                <input type="text" class="form-control" readonly="" value="{{$data['identificador']['padrão']}}" data-add="{{ $data['identificador']['add'] }}" data-default="{{ $data['identificador']['padrão'] }}" title="{{ $data['identificador']['padrão'] }}" id="UII">
            </form>
        </div>

        <div class="d-flex">
            <div class="dropdown margin-right-15 " style="width: 170px;">
                <form class="app-search d-none d-lg-block" action="javascript:;" id="form-buscar-pagina" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="position-relative">
                        <div class="input-group">
                            <input type="tel" class="form-control onlyNumber" placeholder="ex: 432" name="cod" maxlength="3">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit"><i class="bx bx-search-alt align-middle"></i></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div> 
            <div class="dropdown d-none d-sm-inline-block margin-right-10">
                <button type="button" class="btn header-item" id="mode-setting-btn">
                    <i class="fas fa-moon  icon-lg layout-mode-dark" title="Tema Escuro"></i>
                    <i class="fas fa-sun icon-lg layout-mode-light" title="Tema Claro"></i>
                </button>
            </div>
            <div class="dropdown margin-right-15">
                <button type="button" class="btn header-item" id="btn-videos" title="Ver Vídeos Tutoriais">
                    <i class="fab fa-youtube icon-lg"></i>
                </button>
            </div>
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item bg-soft-light border-start border-end" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user" src="{{ asset('avatars/'. (Auth::user()->user_has_avatar ?? 'default.png')) }}" alt="{{Auth::user()->user_first_name}} {{Auth::user()->user_last_name}}">
                    <span class="d-none d-xl-inline-block ms-1 fw-medium myFullName">
                        {{Auth::user()->user_first_name}} {{Auth::user()->user_last_name}}
                        <p class="no-padding no-margin text-muted"> {{ Session::get('cargo') }}</p>
                    </span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <a class="dropdown-item" href="{{ config('app.url') }}" target="_blank"><i class="mdi mdi-earth font-size-16 align-middle me-1"></i> Site {{ $systemVariables->nome }}</a>
                    <a class="dropdown-item" href="SGS/perfil"><i class="mdi mdi-account-edit font-size-16 align-middle me-1"></i> Minha Conta</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="login/logout"><i class="mdi mdi-logout font-size-16 align-middle me-1"></i> Sair</a>
                </div>
            </div>

        </div>
    </div>
</header>
<div id="menu-horizontal" class="topnav{{ empty($_COOKIE['theme_menu']) || $_COOKIE['theme_menu'] == "horizontal" ? '' : ' hidden' }}">
    <div class="container-fluid">
        <nav class="navbar navbar-light navbar-expand-lg topnav-menu">
            <div class="collapse navbar-collapse" id="topnav-menu-content">
                <ul class="navbar-nav">
                    @foreach ($menu as $menu)
                    @if (!$menu['sub'])
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none{{ $data['default']['menu'] > 0 && $data['default']['menu'] == $menu["id"] ? ' active' : '' }}" href="{{ $menu["link"] == "index" ? Config::get("URL") : $menu["link"] }}" id="topnav-dashboard" role="button">
                            <i class="{{ $menu["icon"] }}"></i> <span data-key="t-{{ $menu["icon"] }}">{{ $menu["title"] }}</span>
                        </a>
                    </li>
                    @endif
                    @if ($menu['sub'])
                    <li class="nav-item dropdown submenu-item">
                        <a class="nav-link dropdown-toggle arrow-none{{ $data['default']['menu'] > 0 && $data['default']['menu'] == $menu["id"] ? ' active' : '' }}" href="javascript:;" id="topnav-pages" role="button">
                            <i class="{{ $menu["icon"] }}"></i> <span data-key="t-apps">{{ $menu["title"] }}</span> <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-pages">
                            @foreach ($menu['submenu'] as $sub)
                            @if (!count($sub['submenus']))
                            <a href="{{ $sub['LINK'] }}" class="dropdown-item{{ $data['default']['submenu'] > 0 && $data['default']['submenu'] == $sub["ID"] ? ' active' : '' }}" data-key="t-{{ $menu["icon"] }}"><i class="{{ $sub["ICON"] }}"></i> {{ $sub['NAME'] }}</a>
                            @endif

                            @if (count($sub['submenus']) > 0)
                            <div class="dropdown">
                                <a href="javascript:;" class="dropdown-item dropdown-toggle arrow-none{{ $data['default']['submenu'] > 0 && $data['default']['submenu'] == $sub["ID"] ? ' active' : '' }}" data-key="t-{{ $menu["icon"] }}" role="button"><i class="{{ $sub["ICON"] }}"></i> 
                                    {{ $sub['NAME'] }} <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu">
                                    @foreach ($sub['submenus'] as $subsubmenu)
                                    <a href="{{ $subsubmenu['LINK'] }}" class="dropdown-item{{ $data['default']['subsubmenu'] > 0 && $data['default']['subsubmenu'] == $subsubmenu["ID"] ? ' active' : '' }}" data-key="t-login"><i class="{{ $subsubmenu["ICON"] }}"></i> {{ $subsubmenu['NAME'] }}</a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </li>
                    @endif
                    @endforeach
                </ul>
            </div>
        </nav>
    </div>
</div>
