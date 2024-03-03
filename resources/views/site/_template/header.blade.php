<div class="preloader">
    <button class="th-btn shadow-1 preloaderCls">Cancel Preloader</button>
    <div class="preloader-inner">
        <div class="loader"></div>
    </div>
</div>
<header class="th-header header-layout1 prebuilt">
    <div class="th-menu-wrapper">
        <div class="th-menu-area text-center">
            <button class="th-menu-toggle">
                <i class="fal fa-times"></i>
            </button>
            <div class="mobile-logo">
                <a class="logo" href="{{ config('app.url') }}">
                    <img class="img-fluid" src="{{ asset('assets/site/img/logo.png') }}" alt="logo" style="width: 200px !important;height: 62px !important;" />
                </a>
            </div>
            <div class="th-mobile-menu">
                <ul id="menu-primary-menu" class="">
                    <li id="menu-item-43" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-43">
                        <a href="{{ config('app.url') }}">Principal</a>
                    </li>
                    <li id="menu-item-43" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-43">
                        <a href="{{ config('app.url') }}#quem-somos">Quem Somos</a>
                    </li>
                    <li id="menu-item-43" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-43">
                        <a href="{{ config('app.url') }}#servicos">Serviços</a>
                    </li>
                    <li id="menu-item-43" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-43">
                        <a href="{{ config('app.url') }}#precos">Preços</a>
                    </li> 
                    <li id="menu-item-43" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-43">
                        <a href="{{ config('app.url') }}#contato">Fale Conosco</a>
                    </li> 
                    <li id="menu-item-43" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-43">
                        @if(!Auth::user())
                        <a href="area-cliente">Área do Cliente</a> 
                        @endif
                        @if(Auth::user() && Auth::user()->user_id)
                        <a href="SGS">Acessar Sistema</a>
                        @endif
                    </li> 
                </ul>
            </div>
        </div>
    </div>
    <div class="popup-search-box d-none d-lg-block">
        <button class="searchClose">
            <i class="fal fa-times"></i>
        </button>
        <form role="search" method="get" action="#">
            <input value="" name="s" required type="search" placeholder="What are you looking for?">
            <button type="submit">
                <i class="fal fa-search"></i>
            </button>
        </form>
    </div>
    <div class="header-top">
        <div class="container">
            <div class="row justify-content-center justify-content-lg-between align-items-center gy-2">
                <div class="col-auto d-none d-lg-block">
                    <div class="header-links">
                        <ul>
                            <li class="d-none d-sm-inline-block">
                                <span class="icon-btn">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <b>Email:</b>
                                <a href="mailto:contato@mednuvem">contato@mednuvem</a>
                            </li> 
                        </ul>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="header-links">
                        <ul> 
                            <li>
                                <div class="social-links">
                                    <span class="social-title">Redes Sociais:</span>
                                    <a href="https://www.linkedin.com/company/mednuvem/" target="_blank">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                    <a href="https://wa.me/5582994120090" target="_blank">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sticky-wrapper">
        <!-- Main Menu Area -->
        <div class="menu-area">
            <div class="container">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto">
                        <div class="header-logo">
                            <div class="logo-bg" data-bg-src="{{ asset('assets/images/logo_bg_1.png') }}"></div>
                            <a class="logo" href="{{ config('app.url') }}">
                                <img class="img-fluid" src="{{ asset(sprintf('assets/uploads/theme/%s',$systemVariables->logo)) }}" alt="logo"  style="width: 200px !important;height: 62px !important;" />
                            </a>
                        </div>
                    </div>
                    <div class="col-auto d-none d-lg-inline-block">
                        <nav class="main-menu d-none d-lg-inline-block ">
                            <ul id="menu-primary-menu-1" class="">
                                <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-43">
                                    <a href="{{ config('app.url') }}">Principal</a>
                                </li>
                                <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-43">
                                    <a href="{{ config('app.url') }}#quem-somos">Quem Somos</a>
                                </li>
                                <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-43">
                                    <a href="{{ config('app.url') }}#servicos">Serviços</a>
                                </li> 
                                <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-43">
                                    <a href="{{ config('app.url') }}#precos">Preços</a>
                                </li> 
                                <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-46">
                                    <a href="{{ config('app.url') }}#contato">Fale Conosco</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <div class="col-auto">
                        <div class="header-button"> 
                            @if(!Auth::user())
                            <a href="area-cliente" class="th-btn th_btn">Área do Cliente</a> 
                            @endif
                            @if(Auth::user() && Auth::user()->user_id)
                            <a class="th-btn th_btn" href="SGS">Acessar Sistema</a>
                            @endif
                            <button type="button" title="Menu" class="th-menu-toggle d-block d-lg-none"><i class="far fa-bars"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>