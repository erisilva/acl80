@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('users.index') }}">Lista de Operadores</a></li>
    </ol>
  </nav>

  <x-flash-message />

  <x-btn-group route="users.create">
    <a class="dropdown-item" href="{{ route('roles.index') }}"><i class="bi bi-layout-sidebar"></i> Perfis</a>
    <a class="dropdown-item" href="{{ route('permissions.index') }}"><i class="bi bi-layout-sidebar"></i> Permissões</a>
    <div class="dropdown-divider"></div>
    <a class="dropdown-item" href="{{route('users.export.xls', ['name' => request()->input('name'), 'email' => request()->input('email')])}}"><i class="bi bi-file-earmark-spreadsheet-fill"></i> Exportar Planilha Excel</a>
    <a class="dropdown-item" href="{{route('users.export.csv', ['name' => request()->input('name'), 'email' => request()->input('email')])}}"><i class="bi bi-file-earmark-spreadsheet-fill"></i> Exportar Planilha CSV</a>
    <a class="dropdown-item" href="{{route('users.export.pdf', ['name' => request()->input('name'), 'email' => request()->input('email')])}}"><i class="bi bi-file-pdf-fill"></i> Exportar PDF</a>
  </x-btn-group>

  <div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Nome</th>
                <th scope="col">E-mail</th>
                <th scope="col">Situação</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{$user->name}}</td>
                <td>{{$user->email}}</td>
                <td>
                @if($user->active == 'N')
                    <i class="fas fa-user-lock"></i>  
                @endif
                </td>
                <td>
                  <div class="btn-group" role="group">
                    <a href="{{route('users.edit', $user->id)}}" class="btn btn-primary btn-sm" role="button"><i class="bi bi-pencil-square"></i></a>
                    <a href="{{route('users.show', $user->id)}}" class="btn btn-primary btn-sm" role="button"><i class="bi bi-trash"></i></a>
                  </div>
                </td>
            </tr>    
            @endforeach                                                 
        </tbody>
    </table>
  </div>
  <p class="text-center">Página {{ $users->currentPage() }} de {{ $users->lastPage() }}. Total de registros: {{ $users->total() }}.</p>
  <div class="container-fluid">
      {{ $users->links() }}
  </div>
</div>

<x-modal-filter class="modal-lg" :perpages="$perpages" >
  <form method="GET" action="{{ route('users.index') }}">
    @csrf
    <div class="form-group">
      <label for="name">Nome</label>
      <input type="text" class="form-control" id="name" name="name" value="{{request()->input('name')}}">
    </div>
    <div class="form-group">
      <label for="email">E-mail</label>
      <input type="text" class="form-control" id="email" name="email" value="{{request()->input('email')}}">
    </div>
    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i> Pesquisar</button>
    <a href="{{ route('users.index') }}" class="btn btn-primary btn-sm" role="button">Limpar</a>
  </form>
</x-modal-filter> 

@endsection
@section('script-footer')
<script>
$(document).ready(function(){
    $('#perpage').on('change', function() {
        perpage = $(this).find(":selected").val(); 
        
        window.open("{{ route('users.index') }}" + "?perpage=" + perpage,"_self");
    });

    $('#btnExportarXLS').on('click', function(){
        var filtro_name = $('input[name="name"]').val();
        var filtro_email = $('input[name="email"]').val();
        window.open("{{ route('users.export.xls') }}" + "?name=" + filtro_name + "&email=" + filtro_email,"_self");
    });

    $('#btnExportarCSV').on('click', function(){
        var filtro_name = $('input[name="name"]').val();
        var filtro_email = $('input[name="email"]').val();
        window.open("{{ route('users.export.csv') }}" + "?name=" + filtro_name + "&email=" + filtro_email,"_self");
    });

    $('#btnExportarPDF').on('click', function(){
        var filtro_name = $('input[name="name"]').val();
        var filtro_email = $('input[name="email"]').val();
        window.open("{{ route('users.export.pdf') }}" + "?name=" + filtro_name + "&email=" + filtro_email,"_self");
    });
}); 
</script>
@endsection