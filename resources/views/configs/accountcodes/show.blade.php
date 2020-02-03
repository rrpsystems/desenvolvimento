@extends('adminlte::page')

@section('title', 'Configurações')

@section('content_header')
    <div class="container-fluid">
        <div class="d-flex justify-content-end">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Configurações</a></li>
                <li class="breadcrumb-item"><a href="#">Codigos de Conta</a></li>
                <li class="breadcrumb-item active">Exibir</li>
            </ol>     
        </div>
    </div>
@stop

@section('content')

<div class="d-flex justify-content-center">
    <div class="p-2 bd-highlight">
                
        <div class="row">
            <div class="col-12 p-0">
                <div class="card card-primary card-outline" style="width:500px">
                    <div class="card-header no-border bg-primary">
                        <div class="d-flex flex-row bd-highlight align-items-center ">

                            <div class="p-1 bd-highlight">
                                <h3 class="card-title">Exibir Codigo de Conta </h3>
                            </div>

                            <div class="p-1 bd-highlight ">                  
                            </div>

                        </div>
                    </div>

                    <div class="card-body p-3 " id="show">
                        @include('configs.accountcodes.form')
                    </div>

                    <div class="card-footer no-border ">
                        <div class="d-flex bd-highlight ">

                            <div class="mr-auto p-1 bd-highlight ">
                            </div>

                            <div class="p-1 bd-highlight">
                            </div>

                            <div class="p-1 bd-highlight">
                            <a class="btn btn-outline-primary" href="{{ route('accountcodes.index') }}">Voltar</a>
                            </div>

                        </div>    
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
