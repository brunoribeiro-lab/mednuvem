<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                {{ $systemVariables->nome }} © {{ date("Y") }} | Todos os Direitos Reservados
            </div>
            <div class="col-sm-6">
                <div class="text-sm-end d-none d-sm-block">
                    <i class="fas fa-code-commit"></i> Versão: {{  config('app.versao') }} ::  {{  config('app.versao_data') }}
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- Right bar overlay-->
<div class="rightbar-overlay"></div>