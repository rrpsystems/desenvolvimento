@extends('adminlte::page')

@section('title', 'Configurações')

@section('content_header')
    <div class="container-fluid">
        <div class="d-flex justify-content-end">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Configurações</a></li>
                <li class="breadcrumb-item"><a href="#">Ramais</a></li>
                <li class="breadcrumb-item active">Excluir</li>
            </ol>     
        </div>
    </div>
@stop

@section('content')

<div class="d-flex justify-content-center">
    <div class="p-2 bd-highlight">
                
        <div class="row">
            <div class="col-12 p-0">

                <form  action="{{route('extensions.destroy', $extension->id)}}" method="POST">
                    @method('delete')
                    @csrf
                        
                        <div class="card card-danger card-outline" style="width:500px">
                                
                            <div class="card-header no-border bg-danger">
                                <div class="d-flex flex-row bd-highlight align-items-center ">

                                    <div class="p-1 bd-highlight">
                                        <h3 class="card-title">Excluir Ramal </h3>
                                    </div>

                                    <div class="p-1 bd-highlight ">                  
                                    </div>

                                </div>
                            </div>

                            <div class="card-body p-3" id="delete">
                                @include('configs.extensions.form')
                            </div>

                            <div class="card-footer no-border ">
                                <div class="d-flex bd-highlight ">

                                    <div class="mr-auto p-1 bd-highlight ">
                                    </div>

                                    <div class="p-1 bd-highlight">
                                    <a class="btn btn-outline-warning" href="{{route('extensions.index')}}" >Cancelar</a>      
                                    </div>

                                    <div class="p-1 bd-highlight">
                                    <button type="submit" class="btn btn-outline-danger" style="padding-left:20px; padding-right:20px;">Excluir</button>
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

@section('js')
    <script>
        $(document).ready(function(){
            $("#delete :input").prop("disabled", true);
        });

    </script>
@stop