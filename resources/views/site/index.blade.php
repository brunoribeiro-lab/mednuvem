@php 
use \App\Providers\Captchar; 
use \App\Providers\Converter;
use \APP\Models\Planos;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">
    <head data-mobilemenu="false">
        <meta charset="utf-8" />
        <base href="{{ config('app.url') }}">
        <title>{{ $systemVariables->nome }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" /> 
        @include('site/_template/libs/css/theme') 
        <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    </head>
    <body class="home page-template page-template-template-builder page-template-template-builder-php page page-id-21 wp-embed-responsive theme-mediax woocommerce-no-js tinvwl-theme-style dark-theme elementor-default elementor-kit-9 elementor-page elementor-page-21">
        @include('site/_template/header') 		
        <div class="mediax-fluid">
            <div class="builder-page-wrapper">
                <div data-elementor-type="wp-page" data-elementor-id="21" class="elementor elementor-21">
                    <div class="elementor-element elementor-element-276ffce e-con-full e-flex e-con e-parent" data-id="276ffce" data-element_type="container" data-settings="{&quot;content_width&quot;:&quot;full&quot;}" data-core-v316-plus="true">
                        <div class="elementor-element elementor-element-138c1fa elementor-widget elementor-widget-mediaxbanner2" data-id="138c1fa" data-element_type="widget" data-widget_type="mediaxbanner2.default">
                            <div class="elementor-widget-container">
                                <div class="th-hero-wrapper hero-2" id="hero" data-bg-src="{{ asset('assets/images/hero_bg_2_1.jpg') }}">
                                    <div class="hero-inner">
                                        <div class="container">
                                            <div class="hero-style2">
                                                <span class="sub-title sub">
                                                    <img decoding="async" src="{{ asset('assets/images/title_icon.svg') }}" alt="Shape">Alta Disponibilidade</span>
                                                <h1 class="hero-title2 title">
                                                    <span class="title1">Prontuário <span class="line-text">Médico</span>
                                                    </span>
                                                    <span class="title2">na nuvem</span>
                                                </h1>
                                                <p class="hero-text desc">Somo uma empresa líder no setor da saúde dedicada a revolucionar a maneira como informações médicas são gerenciadas e acessadas. Especializada em hospedagem segura e confiável de exames e receitas médicas na nuvem, a MedNuvem oferece uma solução abrangente e eficiente para profissionais de saúde e pacientes.</p>
                                                <div class="btn-group justify-content-center">
                                                    <a href="#quem-somos" class="th-btn th_btn">Saiba Mais</a>
                                                    <a href="#servicos" class="th-btn style4 th_btn2">Nossos Serviços</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="hero-img hidden-xs">
                                            <img decoding="async" src="{{ asset('assets/images/hero_2_1.png') }}" alt="hero_2_1" />
                                        </div>
                                        <div class="hero-shape1">
                                            <img decoding="async" src="{{ asset('assets/images/hero_shape_2_1.svg') }}" alt="shape">
                                        </div>
                                        <div class="hero-shape2">
                                            <img decoding="async" src="{{ asset('assets/images/hero_shape_2_2.svg') }}" alt="shape">
                                        </div>
                                        <div class="hero-shape3">
                                            <img decoding="async" src="{{ asset('assets/images/hero_shape_2_3.svg') }}" alt="shape">
                                        </div>
                                        <div class="hero-shape4">
                                            <img decoding="async" src="{{ asset('assets/images/hero_shape_2_4.svg') }}" alt="shape">
                                        </div>
                                        <div class="hero-shape5">
                                            <img decoding="async" src="{{ asset('assets/images/hero_shape_2_5.svg') }}" alt="shape">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="elementor-element elementor-element-26cccb3 e-flex e-con-boxed e-con e-parent" data-id="26cccb3" data-element_type="container" data-settings="{&quot;content_width&quot;:&quot;boxed&quot;}" data-core-v316-plus="true">
                        <div class="e-con-inner">
                            <div class="elementor-element elementor-element-d8a5a9e elementor-widget elementor-widget-mediaxshapeimage" data-id="d8a5a9e" data-element_type="widget" data-widget_type="mediaxshapeimage.default">
                                <div class="elementor-widget-container">
                                    <!-- Image -->
                                    <div class="shape-mockup   " data-top="0.1%" data-right="0.1%">
                                        <img decoding="async" src="{{ asset('assets/images/pattern_shape_1.png') }}" alt="Mediax">
                                    </div>
                                    <!-- End Image -->
                                </div>
                            </div>
                            <div class="elementor-element elementor-element-3485852 elementor-widget elementor-widget-mediaxshapeimage" data-id="3485852" data-element_type="widget" data-widget_type="mediaxshapeimage.default">
                                <div class="elementor-widget-container">
                                    <!-- Image -->
                                    <div class="shape-mockup  jump " data-bottom="10%" data-right="3%">
                                        <img decoding="async" src="{{ asset('assets/images/medicine_1.png') }}" alt="Mediax">
                                    </div>
                                    <!-- End Image -->
                                </div>
                            </div>
                            <div class="elementor-element elementor-element-9828276 e-con-full e-flex e-con e-child" data-id="9828276" data-element_type="container" data-settings="{&quot;content_width&quot;:&quot;full&quot;}">
                                <div class="elementor-element elementor-element-932f3a5 elementor-widget elementor-widget-mediaximage" data-id="932f3a5" data-element_type="widget" data-widget_type="mediaximage.default">
                                    <div class="elementor-widget-container">
                                        <div class="img-box1">
                                            <div class="img1">
                                                <img decoding="async" src="{{ asset('assets/images/sobre.png') }}" alt="about_1_3" />
                                            </div>
                                            <div class="about-info">
                                                <h3 class="box-title">{{ $systemVariables->nome }}</h3>
                                                <p class="box-text"> plataforma líder no setor<br> de saúde digital. </p>
                                                <div class="box-review">
                                                    <i class="fa-sharp fa-solid fa-star"></i>
                                                    <i class="fa-sharp fa-solid fa-star"></i>
                                                    <i class="fa-sharp fa-solid fa-star"></i>
                                                    <i class="fa-sharp fa-solid fa-star"></i>
                                                    <i class="fa-sharp fa-solid fa-star"></i>
                                                </div>
                                                <a href="#contato" class="box-link">
                                                    contato@mednuvem.com </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="quem-somos" class="elementor-element elementor-element-09cf400 e-con-full e-flex e-con e-child" data-id="09cf400" data-element_type="container" data-settings="{&quot;content_width&quot;:&quot;full&quot;}">
                                <div class="elementor-element elementor-element-664605f elementor-widget-tablet__width-initial elementor-widget elementor-widget-mediaxsectiontitle" data-id="664605f" data-element_type="widget" data-widget_type="mediaxsectiontitle.default">
                                    <div class="elementor-widget-container">
                                        <div class="title-area  ">
                                            <span class="sub-title ">
                                                <img decoding="async" src="{{ asset('assets/images/title_icon.svg') }}" alt="Shape">Sobre nós</span>
                                            <h2 class="sec-title ">O que é {{ $systemVariables->nome }} ?</h2>
                                            <p class="sec-text">
                                                Com nossa plataforma robusta e altamente confiável, os profissionais de saúde podem enviar documentos médicos, como receitas, exames e relatórios clínicos, para a nuvem de forma rápida e eficiente. Utilizamos tecnologias avançadas de criptografia para proteger os dados durante o trânsito e em repouso, garantindo a confidencialidade e integridade das informações.
                                                Além disso, nossa infraestrutura de alta disponibilidade garante que os dados estejam sempre acessíveis quando necessário, minimizando qualquer tempo de inatividade e interrupção nos serviços. 
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="elementor-element elementor-element-3df6d0a elementor-widget-tablet__width-auto elementor-widget elementor-widget-mediaxfeatures" data-id="3df6d0a" data-element_type="widget" data-widget_type="mediaxfeatures.default">
                                    <div class="elementor-widget-container">
                                        <div class="mt-n1">
                                            <div class="checklist style2 list-two-column">
                                                <ul>
                                                    <li>
                                                        <i class="fas fa-heart-pulse"></i>Armazenamento seguro
                                                    </li>
                                                    <li>
                                                        <i class="fas fa-heart-pulse"></i>Alta disponibilidade
                                                    </li>
                                                    <li>
                                                        <i class="fas fa-heart-pulse"></i>Criptografia avançada
                                                    </li>
                                                    <li>
                                                        <i class="fas fa-heart-pulse"></i>Backup regular
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="elementor-element elementor-element-6ae8106 elementor-widget elementor-widget-mediaxbutton" data-id="6ae8106" data-element_type="widget" data-widget_type="mediaxbutton.default">
                                    <div class="elementor-widget-container">
                                        <div class="btn-wrapper">
                                            <a class="th-btn th_btn " href="sobre/">Saiba Mais</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="servicos" class="elementor-element elementor-element-6b35c77 e-flex e-con-boxed e-con e-parent" data-id="6b35c77" data-element_type="container" data-settings="{&quot;background_background&quot;:&quot;classic&quot;,&quot;content_width&quot;:&quot;boxed&quot;}" data-core-v316-plus="true">
                        <div class="e-con-inner">
                            <div class="elementor-element elementor-element-6a40357 elementor-widget__width-initial elementor-widget elementor-widget-mediaxsectiontitle" data-id="6a40357" data-element_type="widget" data-widget_type="mediaxsectiontitle.default">
                                <div class="elementor-widget-container">
                                    <div class="title-area text-center ">
                                        <span class="sub-title ">
                                            <img decoding="async" src="{{ asset('assets/images/title_icon.svg') }}" alt="Shape">Nossos Serviços</span>
                                        <h2 class="sec-title ">Soluções {{ $systemVariables->nome }}</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="elementor-element elementor-element-c28d798 elementor-widget elementor-widget-mediaxservice" data-id="c28d798" data-element_type="widget" data-widget_type="mediaxservice.default">
                                <div class="elementor-widget-container">
                                    <div class="row gy-4 justify-content-center">
                                        <div class="col-xl-3 col-lg-4 col-sm-6">
                                            <div class="service-card" data-bg-src="{{ asset('assets/images/service_card_1-1.jpg') }}">
                                                <div class="box-shape">
                                                    <img decoding="async" src="{{ asset('assets/images/service_card_bg.png') }}" alt="service_card_bg" />
                                                </div>
                                                <div class="box-icon">
                                                    <img decoding="async" src="{{ asset('assets/images/service_card_1.png') }}" alt="service_card_1" />
                                                </div>
                                                <h3 class="box-title title">
                                                    <a href="#">Nuvem</a>
                                                </h3>
                                                <p class="box-text desc">Todos os arquivos médicos na nuvem.</p>
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-lg-4 col-sm-6">
                                            <div class="service-card" data-bg-src="{{ asset('assets/images/service_card_2.jpg') }}">
                                                <div class="box-shape">
                                                    <img decoding="async" src="{{ asset('assets/images/service_card_bg.png') }}" alt="service_card_bg" />
                                                </div>
                                                <div class="box-icon">
                                                    <img decoding="async" src="{{ asset('assets/images/service_card_2.png') }}" alt="service_card_2" />
                                                </div>
                                                <h3 class="box-title title">
                                                    <a href="#">Alta Disponibilidade</a>
                                                </h3>
                                                <p class="box-text desc">Arquivos disponíveis quando precisar.</p>
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-lg-4 col-sm-6">
                                            <div class="service-card" data-bg-src="{{ asset('assets/images/service_card_3.jpg') }}">
                                                <div class="box-shape">
                                                    <img decoding="async" src="{{ asset('assets/images/service_card_bg.png') }}" alt="service_card_bg" />
                                                </div>
                                                <div class="box-icon">
                                                    <img decoding="async" src="{{ asset('assets/images/service_card_3.png') }}" alt="service_card_3" />
                                                </div>
                                                <h3 class="box-title title">
                                                    <a href="#">Criptografia</a>
                                                </h3>
                                                <p class="box-text desc">Criptografia avançada para maior segurança.</p>
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-lg-4 col-sm-6">
                                            <div class="service-card" data-bg-src="{{ asset('assets/images/service_card_4.jpg') }}">
                                                <div class="box-shape">
                                                    <img decoding="async" src="{{ asset('assets/images/service_card_bg.png') }}" alt="service_card_bg" />
                                                </div>
                                                <div class="box-icon">
                                                    <img decoding="async" src="{{ asset('assets/images/service_card_4.png') }}" alt="service_card_4" />
                                                </div>
                                                <h3 class="box-title title">
                                                    Interface Amigavel
                                                </h3>
                                                <p class="box-text desc">Plataforma intuitiva e com vídeos aulas.</p>
                                            </div>
                                        </div> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="precos" class="elementor-element elementor-element-297f2f3 e-con-full e-flex e-con e-parent" data-id="297f2f3" data-element_type="container" data-settings="{&quot;content_width&quot;:&quot;full&quot;}" data-core-v316-plus="true">
                        <div class="elementor-element elementor-element-49e41c0 e-flex e-con-boxed e-con e-child" data-id="49e41c0" data-element_type="container" data-settings="{&quot;background_background&quot;:&quot;classic&quot;,&quot;content_width&quot;:&quot;boxed&quot;}">
                            <div class="e-con-inner">
                                <div class="elementor-element elementor-element-bdeff66 e-con-full e-flex e-con e-child" data-id="bdeff66" data-element_type="container" data-settings="{&quot;content_width&quot;:&quot;full&quot;}">
                                    <div class="elementor-element elementor-element-d89b214 elementor-widget-tablet__width-initial elementor-widget elementor-widget-mediaxsectiontitle" data-id="d89b214" data-element_type="widget" data-widget_type="mediaxsectiontitle.default">
                                        <div class="elementor-widget-container">
                                            <div class="title-area  ">
                                                <h2 class="sec-title ">Faça uma Consultoria</h2>
                                                <p class="sec-text">Entre em contato e informe quantos médicos tem em sua clínica para passarmos o orçamento de acordo com a sua quantidade de médicos.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="elementor-element elementor-element-d9a0ed7 e-con-full e-flex e-con e-child" data-id="d9a0ed7" data-element_type="container" data-settings="{&quot;content_width&quot;:&quot;full&quot;}">

                                        <div class="elementor-element elementor-element-31fd5a2 elementor-widget elementor-widget-mediaxbutton" data-id="31fd5a2" data-element_type="widget" data-widget_type="mediaxbutton.default">
                                            <div class="elementor-widget-container">
                                                <div class="btn-wrapper">
                                                    <a class="th-btn th_btn style2 shadow-1" href="#contato">Consultoria</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="elementor-element elementor-element-854c861 e-con-full e-flex e-con e-child" data-id="854c861" data-element_type="container" data-settings="{&quot;content_width&quot;:&quot;full&quot;}">
                                    <div class="elementor-element elementor-element-759ed49 elementor-widget elementor-widget-mediaximage" data-id="759ed49" data-element_type="widget" data-widget_type="mediaximage.default">
                                        <div class="elementor-widget-container">
                                            <div class="img-box2">
                                                <img decoding="async" src="{{ asset('assets/images/cta_1.png') }}" alt="cta_1" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="contato" class="elementor-element elementor-element-6e92bb8 e-flex e-con-boxed e-con e-parent" data-id="6e92bb8" data-element_type="container" data-settings="{&quot;background_background&quot;:&quot;classic&quot;,&quot;content_width&quot;:&quot;boxed&quot;}" data-core-v316-plus="true">
                        <div class="e-con-inner">
                            <div class="elementor-element elementor-element-52d9910 elementor-widget elementor-widget-mediaxcontactform" data-id="52d9910" data-element_type="widget" data-widget_type="mediaxcontactform.default">
                                <div class="elementor-widget-container">
                                    <div class="appointment-row">
                                        <div class="schedule-box bg2">
                                            <div class="shape"></div>
                                            <h3 class="box-title">Horário de Atendimento</h3>
                                            <p class="box-text">Respondemos o mais rápido possível.</p>
                                            <p class="box-timing">Segunda - Terça: <span>8am - 6pm</span>
                                            </p>
                                            <p class="box-timing">Quarta - Quinta: <span>8am - 5pm</span>
                                            </p>
                                            <p class="box-timing">Sexta: <span>8am - 4pm</span>
                                            </p>
                                            <p class="box-timing">Sábado: <span>8am - 12pm</span>
                                            </p>
                                            <p class="box-timing">Domingo: <span>Fechado</span>
                                            </p>
                                        </div>
                                        <div class="form-wrap bg">
                                            <div class="img-box4">
                                                <div class="img1">
                                                    <img decoding="async" src="{{ asset('assets/images/form_1_2.jpg') }}" alt="form_1_1" />
                                                </div>
                                                <div class="img2">
                                                    <img decoding="async" src="{{ asset('assets/images/form_1_1.jpg') }}" alt="form_1_2" />
                                                </div>
                                            </div>
                                            <div class="appointment-form">
                                                <h4 class="form-title title">Fale Conosco</h4>
                                                <div class="wpcf7 no-js" id="wpcf7-f781-p21-o1" lang="en-US" dir="ltr">
                                                    <div class="screen-reader-response">
                                                        <p role="status" aria-live="polite" aria-atomic="true"></p>
                                                        <ul></ul>
                                                    </div>
                                                    <form action="javascript:;" method="post" id="form-contato" class="wpcf7-form init" aria-label="Contact form" novalidate="novalidate" data-status="init">
                                                        @csrf
                                                        <div class="row">
                                                            <div class="form-group col-12">
                                                                <span class="wpcf7-form-control-wrap" data-name="text-653">
                                                                    <input size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required form-control" aria-required="true" aria-invalid="false" placeholder="Seu Nome/Nome da Clinica" value="" type="text" name="clinica" id="clinica" />
                                                                </span>
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <span class="wpcf7-form-control-wrap" data-name="email-818">
                                                                    <input size="40" class="wpcf7-form-control wpcf7-email wpcf7-validates-as-required wpcf7-text wpcf7-validates-as-email form-control" aria-required="true" aria-invalid="false" placeholder="Telefone" value="" type="tel" name="telefone" id="telefone" />
                                                                </span>
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <span class="wpcf7-form-control-wrap" data-name="number-137">
                                                                    <input class="wpcf7-form-control wpcf7-number wpcf7-validates-as-required wpcf7-validates-as-number form-control" aria-required="true" aria-invalid="false" placeholder="Email" value="" type="email" name="email" id="email" />
                                                                </span>
                                                            </div>
                                                            <div class="form-group col-12">
                                                                <span class="wpcf7-form-control-wrap" data-name="menu-715">
                                                                    <textarea class="wpcf7-form-control wpcf7-textarea wpcf7-validates-as-required form-control" name="mensagem" id="mensagem" placeholder="Sua mensagem"></textarea>
                                                                </span>
                                                            </div>
                                                            <div class="form-group col-6">
                                                                <span class="wpcf7-form-control-wrap" data-name="text-470">
                                                                </span>
                                                            </div>
                                                            <div class="form-group col-6">
                                                                <span class="wpcf7-form-control-wrap" data-name="text-811">
                                                                </span>
                                                            </div>
                                                            <div class="form-btn col-12">
                                                                <button class="th-btn btn-fw wpcf7" type="submit">Enviar Mensagem</button>
                                                            </div>
                                                        </div>
                                                        <p class="form-messages mb-0 mt-3"></p>
                                                        <div class="wpcf7-response-output" aria-hidden="true"></div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="elementor-element elementor-element-a7ce450 e-flex e-con-boxed e-con e-parent" data-id="a7ce450" data-element_type="container" data-settings="{&quot;background_background&quot;:&quot;classic&quot;,&quot;content_width&quot;:&quot;boxed&quot;}" data-core-v316-plus="true">
                        <div class="e-con-inner">
                            <div class="elementor-element elementor-element-cc60882 elementor-widget elementor-widget-mediaxsectiontitle" data-id="cc60882" data-element_type="widget" data-widget_type="mediaxsectiontitle.default">
                                <div class="elementor-widget-container">
                                    <div class="title-area text-center ">
                                        <span class="sub-title ">
                                            <img decoding="async" src="{{ asset('assets/images/title_icon.svg') }}" alt="Shape">Passo a Passo da Plataforma </span>
                                        <h2 class="sec-title ">Como Funciona o processo</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="elementor-element elementor-element-5eba9f4 elementor-widget elementor-widget-mediaxstep" data-id="5eba9f4" data-element_type="widget" data-widget_type="mediaxstep.default">
                                <div class="elementor-widget-container">
                                    <div class="process-card-wrap">
                                        <div class="process-card">
                                            <div class="box-img">
                                                <div class="img">
                                                    <img decoding="async" src="{{ asset('assets/images/process_card_1.jpg') }}" alt="process_card_1" />
                                                </div>
                                                <p class="box-number">01</p>
                                            </div>
                                            <h3 class="box-title title">Consultoria</h3>
                                            <p class="box-text desc">Primeiro ao entrar em contato e fazermos o orçamento do tamanho da sua clínica.</p>
                                        </div>
                                        <div class="process-card">
                                            <div class="box-img">
                                                <div class="img">
                                                    <img decoding="async" src="{{ asset('assets/images/process_card_2.jpg') }}" alt="process_card_2" />
                                                </div>
                                                <p class="box-number">02</p>
                                            </div>
                                            <h3 class="box-title title">Médicos</h3>
                                            <p class="box-text desc">Após aprovado sua clínica cadastra os seus médicos na plataforma.</p>
                                        </div>
                                        <div class="process-card">
                                            <div class="box-img">
                                                <div class="img">
                                                    <img decoding="async" src="{{ asset('assets/images/process_card_3.jpg') }}" alt="process_card_3" />
                                                </div>
                                                <p class="box-number">03</p>
                                            </div>
                                            <h3 class="box-title title">Pacientes</h3>
                                            <p class="box-text desc">Os médicos cadastram seus pacientes para enviar seus documentos médicos.</p>
                                        </div>
                                        <div class="process-card">
                                            <div class="box-img">
                                                <div class="img">
                                                    <img decoding="async" src="{{ asset('assets/images/process_card_4.jpg') }}" alt="process_card_4" />
                                                </div>
                                                <p class="box-number">04</p>
                                            </div>
                                            <h3 class="box-title title">Envio de Arquivos</h3>
                                            <p class="box-text desc">Os médicos podem acessar os arquivos de seus pacientes a qualquer momento.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="elementor-element elementor-element-564e285 e-flex e-con-boxed e-con e-parent" data-id="564e285" data-element_type="container" data-settings="{&quot;background_background&quot;:&quot;classic&quot;,&quot;content_width&quot;:&quot;boxed&quot;}" data-core-v316-plus="true">
                        <div class="e-con-inner">
                            <div class="elementor-element elementor-element-3cf8e41 e-con-full e-flex e-con e-child" data-id="3cf8e41" data-element_type="container" data-settings="{&quot;content_width&quot;:&quot;full&quot;}">
                                <div class="elementor-element elementor-element-47ee665 elementor-widget elementor-widget-mediaxsectiontitle" data-id="47ee665" data-element_type="widget" data-widget_type="mediaxsectiontitle.default">
                                    <div class="elementor-widget-container">
                                        <div class="title-area  text-center text-xl-start">
                                            <span class="sub-title ">
                                                <img decoding="async" src="{{ asset('assets/images/title_icon_2.svg') }}" alt="title_icon_2" />Faqs </span>
                                            <h2 class="sec-title ">Perguntas Frequentes </h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="elementor-element elementor-element-f5f89a6 elementor-widget elementor-widget-mediaxfaq" data-id="f5f89a6" data-element_type="widget" data-widget_type="mediaxfaq.default">
                                    <div class="elementor-widget-container">
                                        <div class="accordion " id="faqAccordion1">
                                            <div class="accordion-card active ">
                                                <div class="accordion-header" id="collapse-item-65d5054168abe">
                                                    <button class="accordion-button " type="button" data-bs-toggle="collapse" data-bs-target="#collapse-65d5054168abe" aria-expanded="true" aria-controls="collapse-65d5054168abe">01. Como minha clínica ou consultório médico pode se beneficiar da gestão de documentos médicos na nuvem?</button>
                                                </div>
                                                <div id="collapse-65d5054168abe" class="accordion-collapse collapse show" aria-labelledby="collapse-item-65d5054168abe" data-bs-parent="#faqAccordion1">
                                                    <div class="accordion-body">
                                                        <div>
                                                            <div>A {{ $systemVariables->nome }} oferece uma série de benefícios, incluindo acesso rápido e fácil aos registros dos pacientes de qualquer lugar, alta segurança e conformidade com regulamentações de privacidade de dados, e a capacidade de compartilhar informações de forma segura entre os profissionais de saúde.</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-card  ">
                                                <div class="accordion-header" id="collapse-item-65d5054168ae4">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-65d5054168ae4" aria-expanded="false" aria-controls="collapse-65d5054168ae4">02. Como posso ter certeza de que meus documentos médicos estarão seguros na nuvem?</button>
                                                </div>
                                                <div id="collapse-65d5054168ae4" class="accordion-collapse collapse " aria-labelledby="collapse-item-65d5054168ae4" data-bs-parent="#faqAccordion1">
                                                    <div class="accordion-body">
                                                        <div>
                                                            <div>Nossa plataforma utiliza tecnologias avançadas de criptografia e práticas de segurança robustas para proteger seus documentos médicos. Além disso, implementamos medidas de segurança adicionais, como acesso restrito baseado em função e monitoramento contínuo para garantir a integridade e confidencialidade dos dados dos pacientes.</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-card  ">
                                                <div class="accordion-header" id="collapse-item-65d5054168afe">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-65d5054168afe" aria-expanded="false" aria-controls="collapse-65d5054168afe">03. Como é feito o backup dos documentos médicos na nuvem em caso de falhas?</button>
                                                </div>
                                                <div id="collapse-65d5054168afe" class="accordion-collapse collapse " aria-labelledby="collapse-item-65d5054168afe" data-bs-parent="#faqAccordion1">
                                                    <div class="accordion-body">
                                                        <div>
                                                            <div>Realizamos backups regulares de todos os documentos médicos armazenados na nuvem para garantir a disponibilidade contínua dos dados. Além disso, nossa infraestrutura é projetada com redundância e escalabilidade para garantir a recuperação rápida em caso de falhas ou incidentes. Seus documentos médicos estão sempre seguros e acessíveis quando você mais precisa.</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="elementor-element elementor-element-7de80eb e-con-full e-flex e-con e-child" data-id="7de80eb" data-element_type="container" data-settings="{&quot;content_width&quot;:&quot;full&quot;}">
                                <div class="elementor-element elementor-element-ac5d3c4 elementor-widget elementor-widget-mediaximage" data-id="ac5d3c4" data-element_type="widget" data-widget_type="mediaximage.default">
                                    <div class="elementor-widget-container">
                                        <div class="ps-xxl-4">
                                            <div class="faq-img1">
                                                <img decoding="async" src="{{ asset('assets/images/faq_1.png') }}" alt="faq_1" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
        @include('site/_template/footer')
    </body> 
    @include('site/_template/libs/js/theme')  
    <script src="{{ asset('assets/libs/validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/libs/vendor/jquery.mask.min.js') }}"></script> 
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/site/js/contato.min.js') }}"></script>  
</html>
