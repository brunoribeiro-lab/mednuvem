<div class="col-md-12 col-md-12 col-xs-12 text-left">
    <label class="form-label">Tipo: </label>
    <div class="right no-padding">
        <select class="form-control filter-select" id="filter-tipo">
            <option value="*"{!! $tipo == "*" ? ' selected=""' : '' !!}>--Todos--</option>
            @foreach($tipos as $t)
            <option value="{{ $t->id }}"{!! $tipo == $t->id ? ' selected=""' : '' !!}>{{ $t->nome }}</option> 
            @endforeach
        </select>
    </div>
</div> 
<div class="col-md-12 col-md-12 col-xs-12 text-left margin-top-10">
    <label class="form-label">Aberto Em: </label>
    <div class="right no-padding">
        <select class="form-control filter-select" id="filter-date">
            <option value="today"{!! $date == 'today' ? ' selected=""' : '' !!}>Hoje</option>  
            <option value="last_15"{!! $date == 'last_15' ? ' selected=""' : '' !!}>Últimos 15 dias</option>
            <option value="last_30"{!! $date == 'last_30' ? ' selected=""' : '' !!}>Últimos 30 dias</option>
            <option value="this_year"{!! $date == 'this_year' ? ' selected=""' : '' !!}>Esse Ano</option>
            <option value="custom_date"{!! $date == 'custom_date' ? ' selected=""' : '' !!}>Dia Especifico</option>
            <option value="custom_ranger"{!! $date == 'custom_ranger' ? ' selected=""' : '' !!}>Intervalo de Datas</option>
        </select>
    </div>
</div>
<div class="col-md-12 col-md-12 col-xs-12 text-left margin-top-10 box-custom-date{{ $date == 'custom_date' ? '' : ' hidden' }}">
    <label class="form-label">Data</label>
    <div class="input-with-icon right">
        <input type="date" name="date_custom" id="date_custom" class="form-control" value="{{ $custom ?? '' }}">
    </div>
</div>
<div class="col-md-12 col-md-12 col-xs-12 text-left margin-top-10 box-custom-ranger{{ $date == 'custom_ranger' ? '' : ' hidden' }}">
    <label class="form-label">Início</label>
    <div class="input-with-icon right">
        <input type="date" name="start" id="start" class="form-control" value="{{ $start ?? '' }}">
    </div>
</div>
<div class="col-md-12 col-md-12 col-xs-12 text-left margin-top-10 box-custom-ranger{{ $date == 'custom_ranger' ? '' : ' hidden' }}">
    <label class="form-label">Fim</label>
    <div class="input-with-icon right">
        <input type="date" name="end" id="end" class="form-control" value="{{ $end ?? '' }}">
    </div>
</div>