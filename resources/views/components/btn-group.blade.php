@props(['route'])
  <div class="btn-group py-1" role="group" aria-label="Opções">
    <a href="{{ route($route) }}" class="btn btn-secondary btn-sm" role="button"><i class="bi bi-plus-circle"></i> Novo Registro</a>
    <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#modalFilter"><i class="bi bi-funnel"></i> Filtrar</button>
    <div class="btn-group" role="group">
      <button id="btnGroupDropOptions" type="button" class="btn btn-secondary dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
       <i class="bi bi-gear"></i> Opções
      </button>
      <div class="dropdown-menu" aria-labelledby="btnGroupDropOptions">
        {{$slot}}        
      </div>
    </div>
  </div>