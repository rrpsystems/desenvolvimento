@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="m-0" style="color:blue">Bem Vindo ao Sistema de Tarifação Telefonica!</h4>
                    <p></p>
                <h5 class="mb-0" style="color:green">Você se logou com o usuario <span style="color:blue">{{ auth()->user()->name }}</span> !</h5>
                </div>
            </div>
        </div>
    </div>
@stop
