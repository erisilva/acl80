@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Lista de Operadores</a></li>
      <li class="breadcrumb-item"><a href="{{ route('permissions.index') }}">Permissões</a></li>
      <li class="breadcrumb-item active" aria-current="page">Exibir Registro</li>
    </ol>
  </nav>
</div>

<x-card title="Permissões">
  <ul class="list-group list-group-flush">
    <li class="list-group-item">Nome: {{$permission->name}}</li>
    <li class="list-group-item">Descrição: {{$permission->description}}</li>
  </ul>
</x-card>

<x-btn-back route="permissions.index" />

<x-modal-trash>
  <form method="post" action="{{route('permissions.destroy', $permission->id)}}">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger"><i class="bi bi-trash"></i> Apagar Registro</button>
  </form>  
</x-modal-trash>

@endsection
