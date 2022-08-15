@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Lista de Operadores</a></li>
      <li class="breadcrumb-item active" aria-current="page">Exibir Registro</li>
    </ol>
  </nav>
</div>

<x-card title="Operador">
  <ul class="list-group list-group-flush">
    <li class="list-group-item">Nome: {{$user->name}}</li>
    <li class="list-group-item">E-mail: {{$user->email}}</li>
    <li class="list-group-item">Ativo: {{($user->active == 'Y') ? 'Sim' : 'Não'}} </li>
  </ul>
</x-card>

<x-btn-back route="users.index" />

<x-modal-trash>
  <form method="post" action="{{route('users.destroy', $user->id)}}">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger"><i class="bi bi-trash"></i> Apagar Registro</button>
  </form>  
</x-modal-trash>


@endsection
