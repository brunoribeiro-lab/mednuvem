<form method="POST" action="javascript:;" id="formModalAddNew" enctype="multipart/form-data"> 
    @csrf
    <div class="row">
        <div class="col-md-12">
            <h3><a href="javascript:;" id="backTo"><i class="fa fa-arrow-left"></i> Voltar</a></h3>
            <hr>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
            <label class="form-label"><span id="field-required">*</span> Identificação: <i class="fa fa-question-circle input-help" data-index="email-indentificacao" title="O que é isso ? Clique para saber"></i></label>
            <span class="input-with-icon right">
                <i class=""></i>
                <input class="form-control" id="index" name="index" type="text" />
            </span>
        </div> 
        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <label class="form-label"><span id="field-required">*</span> Assunto: <i class="fa fa-question-circle input-help" data-index="email-assunto" title="O que é isso ? Clique para saber"></i></label>
            <span class="input-with-icon right">
                <i class=""></i>
                <input class="form-control" id="subject" name="subject" type="text" />
            </span>
        </div>  
        <div class="col-md-12 margin-top-10"> 
            <div class="form-group">
                <label class="form-label">Email</label>
                <div class="input-with-icon  right">
                    <i class=""></i>
                    <div id="editor"></div>
                    <textarea id="email" hidden="" name="email" required=""></textarea>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <br>
            <button class="btn btn-primary margin-bottom-10" id="loadPrefix" type="button"><i class='fa fa-eye'></i> Mostrar Prefixos</button>
            <table class="table dt-responsive nowrap table-condensed table-hover table-full-width hidden" id="box-prefix">
                <thead>
                    <tr>
                        <th class="hidden-md hidden-lg hidden-sm" data-priority="1"></th>
                        <th data-priority="2">Prefixo</th>
                        <th data-priority="3">Uso</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prefix as $prefix => $props): ?>
                        <tr role="row" class="odd">
                            <td class=" hidden-md hidden-lg hidden-sm" tabindex="0"></td>
                            <td><?php print $prefix; ?></td>
                            <td><?php print $props['title']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="col-md-12 text-center margin-top-15 hidden" id="loading">
            <p>Carregando...</p>
            <div class="spinner-border text-primary m-1" role="status">
                <span class="sr-only">Carregando...</span>
            </div>
        </div>
        <div class="col-md-12 margin-top-15">
            <hr>
            <div class="col-lg-3 col-md-4 col-xs-12 centered-block margin-top-15">
                <button type="submit" class="btn btn-success btn-lg btn-block"><i class="far fa-plus"></i> Cadastrar</button>
            </div>
        </div>
    </div>
</form>