@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Lista de Operadores</a></li>
      <li class="breadcrumb-item active" aria-current="page">Alterar Registro</li>
    </ol>
  </nav>
</div>
<div class="container">
  
  <x-flash-message />

  <form method="POST" action="{{ route('users.update', $user->id) }}">
    @csrf
    @method('PUT')
    <div class="form-row">
      <div class="form-group col-md-6">
        <label for="name">Nome</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') ?? $user->name }}">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <div class="form-group col-md-6">
        <label for="email">E-mail</label>
        <input type="email" class="form-control" name="email" value="{{ $user->email }}" readonly tabindex="-1">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group col-md-6">
        <label for="password">Nova Senha</label>
        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password">
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="active" {{ ($user->active == 'Y') ? 'checked' : '' }}>
            <label class="form-check-label" for="active">
              Operador Ativo
            </label>
          </div>
      </div>
    </div>
    <div class="container bg-primary text-white">
      <p class="text-center">Perfis</p>
    </div>
    <div class="form-row">
      @foreach($roles as $role)
        @php
          $checked = '';
          if(old('roles') ?? false){
            foreach (old('roles') as $key => $id) {
              if($id == $role->id){
                $checked = "checked";
              }
            }
          }else{
            if($user ?? false){
              foreach ($user->roles as $key => $roleList) {
                if($roleList->id == $role->id){
                  $checked = "checked";
                }
              }
            }
          }
        @endphp
      <div class="form-group col-4">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" name="roles[]" value="{{$role->id}}" {{$checked}}>
            <label class="form-check-label" for="roles">{{$role->description}}</label>
        </div>
      </div>
      @endforeach
    </div>
    <button type="submit" class="btn btn-primary"><i class="bi bi-pencil-square"></i> Alterar Dados do Operador</button>
  </form>
</div>

<x-btn-back route="users.index" />
@endsection
