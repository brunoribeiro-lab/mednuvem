@php 
$cookie = empty($_COOKIE['cookie_aceito']) ? false : true;
@endphp 
<!---footer-wrapper start-->
<footer class="footer-wrapper footer-layout1 prebuilt-foo" data-bg-src="{{ asset('assets/images/footer_bg_1.jpg') }}"> 
    <div class="copyright-wrap">
        <div class="container">
            <div class="row gy-2 align-items-center">
                <div class="col-md-7">
                    <p class="copyright-text ">{{ $systemVariables->nome }} <i class="fal fa-copyright"></i> {{ date("Y") }} | Todos os Direitos Reservados</p>
                </div>
                <div class="col-md-5 text-center text-md-end">
                    <div class="th-social"> 
                        <a href="https://www.linkedin.com/company/mednuvem/" target="_blank">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="https://wa.me/5582994120090" target="_blank">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!---footer-wrapper end-->
<div class="scroll-top">
    <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
    <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" style="transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919, 307.919; stroke-dashoffset: 307.919;"></path>
    </svg>
</div>

@if(!$cookie)
<div class="banner" id="banner-cookie">
    <div class="container">
        <div class="columns">
            <div class="column banner__content--cookie">
                <p>Utilizamos cookies para oferecer uma melhor experiência para você. Ao continuar navegando, você concorda com nossa <a href="/politica-cookies">Política de Cookies</a>.</p>
                <button class="btn btn-lg btn-cookie-bg" id="aceitar-cookie" type="button">Aceitar</button>
            </div>
        </div>
    </div>
</div>
@endif