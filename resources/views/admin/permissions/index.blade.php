@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Lista de Operadores</a></li>
      <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('permissions.index') }}">Permissões</a></li>
    </ol>
  </nav>

  <x-flash-message />

  <x-btn-group route="permissions.create">
    <a class="dropdown-item" href="{{ route('roles.index') }}"><i class="bi bi-layout-sidebar"></i> Perfis</a>
    <a class="dropdown-item" href="{{ route('permissions.index') }}"><i class="bi bi-layout-sidebar"></i> Permissões</a>
    <div class="dropdown-divider"></div>
  </x-btn-group>

  <div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Nome</th>
                <th scope="col">Descrição</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($permissions as $permission)
            <tr>
                <td>{{$permission->name}}</td>
                <td>{{$permission->description}}</td>
                <td>
                  <div class="btn-group" role="group">
                    <a href="{{route('permissions.edit', $permission->id)}}" class="btn btn-primary btn-sm" role="button"><i class="bi bi-pencil-square"></i></a>
                    <a href="{{route('permissions.show', $permission->id)}}" class="btn btn-primary btn-sm" role="button"><i class="bi bi-trash"></i></a>
                  </div>
                </td>
            </tr>    
            @endforeach                                                 
        </tbody>
    </table>
  </div>
  <p class="text-center">Página {{ $permissions->currentPage() }} de {{ $permissions->lastPage() }}. Total de registros: {{ $permissions->total() }}.</p>
  <div class="container-fluid">
      {{ $permissions->links() }}
  </div>
</div>

<x-modal-filter class="modal-lg" :perpages="$perpages" >
  <form method="GET" action="{{ route('permissions.index') }}">
    @csrf
    <div class="form-group">
      <label for="name">Nome</label>
      <input type="text" class="form-control" id="name" name="name" value="{{request()->input('name')}}">
    </div>
    <div class="form-group">
      <label for="description">Descrição</label>
      <input type="text" class="form-control" id="description" name="description" value="{{request()->input('description')}}">
    </div>
    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i> Pesquisar</button>
    <a href="{{ route('permissions.index') }}" class="btn btn-primary btn-sm" role="button">Limpar</a>
  </form>
</x-modal-filter>  

@endsection
@section('script-footer')
<script>
$(document).ready(function(){
    $('#perpage').on('change', function() {
        perpage = $(this).find(":selected").val(); 
        
        window.open("{{ route('permissions.index') }}" + "?perpage=" + perpage,"_self");
    });

    $('#btnExportarCSV').on('click', function(){
        var filtro_name = $('input[name="name"]').val();
        var filtro_description = $('input[name="description"]').val();
        window.open("{{ route('permissions.export.csv') }}" + "?name=" + filtro_name + "&description=" + filtro_description,"_self");
    });

    $('#btnExportarXLS').on('click', function(){
        var filtro_name = $('input[name="name"]').val();
        var filtro_description = $('input[name="description"]').val();
        window.open("{{ route('permissions.export.xls') }}" + "?name=" + filtro_name + "&description=" + filtro_description,"_self");
    });

    $('#btnExportarPDF').on('click', function(){
        var filtro_name = $('input[name="name"]').val();
        var filtro_description = $('input[name="description"]').val();
        window.open("{{ route('permissions.export.pdf') }}" + "?name=" + filtro_name + "&description=" + filtro_description,"_self");
    });
}); 
</script>
@endsection