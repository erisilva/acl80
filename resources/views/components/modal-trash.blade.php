<div class="modal fade" id="modalLixeira" tabindex="-1" role="dialog" aria-labelledby="JanelaProfissional" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle"><i class="bi bi-patch-question"></i> Apagar Registro</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger" role="alert">
          <p><strong>Atenção!</strong> Ao excluir esse registro todo e qualquer vínculo que ele tiver com outros dados será excluído.</p>
          <h2>Confirma?</h2>
        </div>
        {{$slot}}
      </div>     
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="bi bi-x-square"></i> Cancelar</button>
      </div>
    </div>
  </div>
</div>