@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Lista de Operadores</a></li>
      <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Perfis</a></li>
      <li class="breadcrumb-item active" aria-current="page">Exibir Registro</li>
    </ol>
  </nav>
</div>

<div class="container">
  <div class="card">
    <div class="card-header">
      Perfis
    </div>
    <div class="card-body">
      <ul class="list-group list-group-flush">
        <li class="list-group-item">Nome: {{$role->name}}</li>
        <li class="list-group-item">Descrição: {{$role->description}}</li>
      </ul>
    </div>
    <div class="card-footer text-right">
      <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalLixeira"><i class="bi bi-trash"></i> Enviar para Lixeira</button>
      <a href="{{ route('roles.index') }}" class="btn btn-primary" role="button"><i class="bi bi-arrow-left-square"></i> Voltar</i></a>      
    </div>
  </div>  
</div>

<x-modal-trash>
  <form method="post" action="{{route('roles.destroy', $role->id)}}">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger"><i class="bi bi-trash"></i> Apagar Registro</button>
  </form>  
</x-modal-trash>


@endsection
