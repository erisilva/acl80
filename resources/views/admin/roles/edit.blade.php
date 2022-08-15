@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Lista de Operadores</a></li>
      <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Perfis</a></li>
      <li class="breadcrumb-item active" aria-current="page">Alterar Registro</li>
    </ol>
  </nav>
</div>
<div class="container">

  <x-flash-message />
  
  <form method="POST" action="{{ route('roles.update', $role->id) }}">
    @csrf
    @method('PUT')
    <div class="form-row">
      <div class="form-group col-md-6">
        <label for="name">Nome</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') ?? $role->name }}">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <div class="form-group col-md-6">
        <label for="description">Descrição</label>
        <input type="text" class="form-control @error('description') is-invalid @enderror" name="description" value="{{ old('description') ?? $role->description }}">
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>
    <div class="container bg-primary text-white">
      <p class="text-center">Permissões</p>
    </div>
    <div class="form-row">
      @foreach($permissions as $permission)
        @php
          $checked = '';
          if(old('permissions') ?? false){
            foreach (old('permissions') as $key => $id) {
              if($id == $permission->id){
                $checked = "checked";
              }
            }
          }else{
            if($role ?? false){
              foreach ($role->permissions as $key => $permissionList) {
                if($permissionList->id == $permission->id){
                  $checked = "checked";
                }
              }
            }
          }
        @endphp
      <div class="form-group col-4">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" name="permissions[]" value="{{$permission->id}}" {{$checked}}>
            <label class="form-check-label" for="permissions">{{$permission->description}}</label>
        </div>
      </div>
      @endforeach
    </div>
    <button type="submit" class="btn btn-primary"><i class="bi bi-pencil-square"></i> Alterar Dados do Perfil</button>
  </form>
</div>

<x-btn-back route="roles.index" />
@endsection
