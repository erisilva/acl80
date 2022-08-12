@props(['perpages'])
<div class="modal fade" id="modalFilter" tabindex="-1" role="dialog" aria-labelledby="JanelaFiltro" aria-hidden="true">
  <div {{$attributes->merge(['class' => 'modal-dialog modal-dialog-centered'])}} role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-funnel"></i> Filtro</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        {{$slot}}
        <div class="form-group py-2">
          <select class="form-control" name="perpage" id="perpage">
            @foreach($perpages as $perpage)
            <option value="{{$perpage->valor}}"  {{($perpage->valor == session('perPage')) ? 'selected' : ''}}>{{$perpage->nome}}</option>
            @endforeach
          </select>
        </div>
      </div>     
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="bi bi-x-square"></i> Fechar</button>
      </div>
    </div>
  </div>
</div>