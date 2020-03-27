@extends('adminlte::page')

@section('title', 'Configurações')

@section('content_header')
    <div class="container-fluid">
        <div class="d-flex justify-content-end">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Relatorios</a></li>
                <li class="breadcrumb-item"><a href="#">Por Troncos</a></li>
                <li class="breadcrumb-item active">Exibir</li>
            </ol>     
        </div>
    </div>
@stop

@section('content')

<div class="d-flex justify-content-center">
    
    <object data="{{asset($file)}}" type="application/pdf" width="100%" height="600">
        <a href="{{asset($file)}}">
            <button type="button" class="btn btn-primary">
                Download
            </button>
        </a>
    </object>

    </div>
@stop
