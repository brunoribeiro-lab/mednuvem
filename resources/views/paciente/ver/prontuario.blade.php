@php
use App\Providers\Utils;
@endphp
<div class="row">
    <div class="col-md-12">
        <h3><a href="javascript:;" id="backTo"><i class="fa fa-arrow-left"></i> Voltar</a></h3>
        <hr>
    </div> 
    <div class="col-md-12" id="box-btn-document">
        <button class="btn btn-primary" type="button" id="send-document"><i class="fa fa-upload"></i> Enviar Documento</button>
    </div>
    <form action="javascript:;" class="hidden margin-top-5" id="form-upload" method="post" enctype="multipart/form-data">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <input type="hidden" name="paciente" value="{{ $data->id }}">
                    <div class="col-sm order-2 order-sm-1 margin-bottom-10">
                        <div class="d-flex align-items-start mt-3 mt-sm-0">
                            <div class="flex-grow-1 row">
                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-top-10">  
                                    <div class="form-group">
                                        <label class="form-label">Tipo:</label>
                                        <div class="input-with-icon  right"> 
                                            @foreach($tipo_documentos as $tipo_documento)
                                            <input type="radio" value="{{ $tipo_documento->id }}" class="form-check-input" name="tipo_documento" id="form-tipo_documento-{{ $tipo_documento->id }}"> 
                                            <label for="form-tipo_documento-{{ $tipo_documento->id }}" class="form-check-label margin-right-10">{{ $tipo_documento->nome }}</label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-top-10 hidden" id="box-exame">  
                                    <div class="form-group">
                                        <label class="form-label">Exames:</label>
                                        <div class="input-with-icon  right"> 
                                            @foreach($exames as $exame)
                                            <input type="radio" value="{{ $exame->id }}" class="form-check-input" name="exame" id="form-exame-{{ $exame->id }}"> 
                                            <label for="form-exame-{{ $exame->id }}" class="form-check-label margin-right-10">{{ $exame->nome }}</label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div> 
                                <div class="form-group col-xxl-2 col-xl-2 col-lg-3 col-md-6 col-sm-6 col-xs-12 margin-top-10 hidden" id='box-nome'>  
                                    <div class="form-group">
                                        <label class="form-label">Nome:</label>
                                        <div class="input-with-icon  right">
                                            <i class=""></i>
                                            <input name="nome" id="nome" type="text" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-xxl-2 col-xl-2 col-lg-3 col-md-6 col-sm-6 col-xs-12 margin-top-10">  
                                    <div class="form-group">
                                        <label class="form-label">Data:</label>
                                        <div class="input-with-icon  right"> 
                                            <input name="data" id="data" type="date" value="{{ date("Y-m-d") }}" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-6 col-xs-12 margin-top-10">  
                                    <div class="form-group">
                                        <label class="form-label">Enviar Arquivo:</label>
                                        <div class="input-with-icon  right">
                                            <i class=""></i>
                                            <button class="btn btn-white" id="btn-document" type="button"><i class="fa fa-file-pdf"></i> Anexar Documento</button>
                                            <input name="document" id="document-document" type="file" class="form-control hidden" accept="application/pdf">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-top-10">  
                                    <div class="form-group">
                                        <label class="form-label">Descrição:</label>
                                        <span class="input-with-icon  right">
                                            <i class=""></i>
                                            <textarea class="form-control" name="description"></textarea>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-top-10 margin-bottom-10">
                                    <hr>
                                    <div class="col-lg-3 col-md-4 col-xs-12 centered-block margin-top-15">
                                        <button class="btn btn-success btn-block" type="submit"><i class="fa fa-upload"></i> Enviar Documento</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Tabela completa com filtro e as porra -->
                    <div class="col-sm-auto order-1 order-sm-2">
                        <div class="d-flex align-items-start justify-content-end gap-2">
                            <div>
                                <div class="dropdown">
                                    <button class="btn btn-link font-size-16 shadow-none text-muted" id="btn-close" title="Fechar Envio de Documento" type="button">
                                        <i class="fas fa-times-circle "></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- end card body -->
        </div>
    </form>
    <div class="col-12 margin-top-20">
        <table class="table dt-responsive nowrap table-condensed table-hover table-full-width" id="listingDataProntuario">
            <thead>
                <tr>
                    <th class="hidden-md hidden-lg hidden-sm" data-priority="1"></th>
                    <th data-priority="2">Cód.</th>
                    <th data-priority="3">Tipo</th>
                    <th data-priority="4">Tamanho</th>
                    <th data-priority="5">Arquivo</th>
                    <th data-priority="6">Data</th>
                    <th data-priority="3">Baixar</th>
                </tr>
            </thead>
            <tbody><!-- AJAX CONTENT --></tbody>
        </table>
    </div>
</div>
