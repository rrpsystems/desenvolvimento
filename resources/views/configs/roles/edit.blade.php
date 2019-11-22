@extends('adminlte::page')

@section('title', 'Configurações')

@section('content_header')
    <div class="container-fluid">
        <div class="d-flex justify-content-end">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Configurações</a></li>
                <li class="breadcrumb-item"><a href="#">Permissões</a></li>
                <li class="breadcrumb-item active">Editar</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <form  action="{{route('roles.update',$roles->id )}}" method="POST">
                @method('put')
                @csrf
                <input type="hidden" name="name_old" value="{{$roles->name}}">
                
                <div class="card card-warning card-outline">
                    <div class="card-header no-border">
                        <div class="d-flex flex-row bd-highlight align-items-center">
                            <div class="p-1 bd-highlight">
                                <h3 class="card-title">Editar: </h3>
                            </div>
                            <div class="p-1 bd-highlight">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Permissão:</span>
                                    </div>
                                    <input type="text" class="form-control" id="id" name="name" value="{{old('name') ?? $roles->name ?? ''}}" @if($roles->name =='Root') disabled @endif >
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3" id="edit">
                    
                        @include('configs.roles.form')

                    </div>
                    <div class="card-footer no-border">
                        <div class="d-flex bd-highlight">
                            <div class="mr-auto p-1 bd-highlight">

                            </div>
                            <div class="p-1 bd-highlight">
                                <a class="btn btn-outline-danger" href="{{route('roles.index')}}" >Cancelar</a>      
                            </div>
                            <div class="p-1 bd-highlight">
                                <button type="submit" class="btn btn-outline-success" style="padding-left:20px; padding-right:20px;">Salvar</button>
                            </div>
                        </div>    
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop


