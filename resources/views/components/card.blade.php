@props(['title'])
<div class="container">
  <div class="card">
    <div class="card-header">
      {{$title}}
    </div>
    <div class="card-body">
      {{$slot}}
    </div>
    <div class="card-footer text-right">
      <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalLixeira"><i class="bi bi-trash"></i> Enviar para Lixeira</button>    
    </div>
  </div>
</div>