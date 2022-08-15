@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Lista de Operadores</a></li>
      <li class="breadcrumb-item"><a href="{{ route('permissions.index') }}">Permissões</a></li>
      <li class="breadcrumb-item active" aria-current="page">Alterar Registro</li>
    </ol>
  </nav>
</div>
<div class="container">
  
  <x-flash-message />
  
  <form method="POST" action="{{ route('permissions.update', $permission->id) }}">
    @csrf
    @method('PUT')
    <div class="form-row">
      <div class="form-group col-md-6">
        <label for="name">Nome</label>
        <input type="text" class="form-control  @error('name') is-invalid @enderror" name="name" value="{{ old('name') ?? $permission->name }}">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <div class="form-group col-md-6">
        <label for="description">Descrição</label>
        <input type="text" class="form-control @error('description') is-invalid @enderror" name="description" value="{{ old('description') ?? $permission->description }}">
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>  
    <button type="submit" class="btn btn-primary"><i class="bi bi-pencil-square"></i> Alterar Dados da Permissão</button>
  </form>
</div>

<x-btn-back route="permissions.index" />
@endsection
