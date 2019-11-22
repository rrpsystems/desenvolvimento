@extends('adminlte::page')

@section('title', 'Configurações')

@section('content_header')
    <div class="container-fluid">
        <div class="d-flex justify-content-end">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Configurações</a></li>
                <li class="breadcrumb-item"><a href="#">Permissões</a></li>
                <li class="breadcrumb-item active">Exibir</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-primary card-outline">
            <div class="card-header no-border">
                    <div class="d-flex flex-row bd-highlight align-items-center">
                        <div class="p-1 bd-highlight">
                            <h3 class="card-title">Exibir: </h3>
                        </div>
                        <div class="p-1 bd-highlight">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Permissão:</span>
                                </div>
                                <input type="text" class="form-control" id="id" name="name" value="{{old('name') ?? $roles->name ?? ''}}" disabled>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3" id="show">


                    @include('configs.roles.form')

                </div>
                <div class="card-footer no-border">
                    <div class="d-flex bd-highlight">
                        <div class="mr-auto p-1 bd-highlight">

                        </div>
                        <div class="p-1 bd-highlight">

                        </div>
                        <div class="p-1 bd-highlight">
                            <a class="btn btn-outline-primary" href="{{ route('roles.index') }}">Voltar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


@section('js')
<script>

$(document).ready(function(){
    
    $("#show :input").prop("disabled", true);
});

</script>
@stop