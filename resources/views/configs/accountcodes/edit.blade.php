@extends('adminlte::page')

@section('title', 'Configurações')

@section('content_header')
    <div class="container-fluid">
        <div class="d-flex justify-content-end">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Configurações</a></li>
                <li class="breadcrumb-item"><a href="#">Codigos de Conta</a></li>
                <li class="breadcrumb-item active">Editar</li>
            </ol>     
        </div>
    </div>
@stop

@section('content')

<div class="d-flex justify-content-center">
    <div class="p-2 bd-highlight">
                
        <div class="row">
            <div class="col-12 p-0">

                <form  action="{{route('accountcodes.update',$accountcode->id )}}" method="POST">
                    @method('put')
                    @csrf
                        
                        <div class="card card-success card-outline" style="width:500px">
                                
                            <div class="card-header no-border bg-warning">
                                <div class="d-flex flex-row bd-highlight align-items-center ">

                                    <div class="p-1 bd-highlight">
                                        <h3 class="card-title">Editar Codigo de Conta </h3>
                                    </div>

                                    <div class="p-1 bd-highlight ">                  
                                    </div>

                                </div>
                            </div>

                            <div class="card-body p-3 " id="edit">
                                @include('configs.accountcodes.form')
                            </div>

                            <div class="card-footer no-border ">
                                <div class="d-flex bd-highlight ">

                                    <div class="mr-auto p-1 bd-highlight ">
                                    </div>

                                    <div class="p-1 bd-highlight">
                                        <a class="btn btn-outline-danger" href="{{route('accountcodes.index')}}" >Cancelar</a>      
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
        </div>
    </div>
</div>
@stop

