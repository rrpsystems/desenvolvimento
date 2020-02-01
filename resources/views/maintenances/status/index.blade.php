@extends('adminlte::page')

@section('title', 'Manutenções')

@section('content_header')
    <div class="container-fluid">
        <div class="d-flex justify-content-end">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Manutenções</a></li>
                <li class="breadcrumb-item"><a href="#">Status</a></li>
                <li class="breadcrumb-item active">Geral</li>
            </ol>     
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-primary">
                <div class="card-header no-border">
                    <div class="d-flex bd-highlight">
                        <div class="mr-auto p-2 bd-highlight">
                            <h3 class="card-title">Status do Sistema</h3>
                        </div>
                        <div class="p-2 bd-highlight">
                            <div class="row">
                            </div>
                        </div>
                        <div class="p-2 bd-highlight">
					    </div>
                    </div>
                </div>
                <div class="card-body p-3">


                    <h5 class="mt-4 mb-2">Informações de Ligações </h5>
                    <div class="row">
                        
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="info-box bg-info">
                                <span class="info-box-icon">
                                    <i class="far fa-calendar-alt"></i>
                                </span>
                                
                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        Registros
                                    </span>
                                    <span class="info-box-number">
                                       Primeiro {{ date('d/m/Y', strtotime($calls->primeiro)) }}
                                    </span>

                                    <div class="progress">
                                        <div class="progress-bar" style="width: 0%"></div>
                                    </div>
                                    
                                    <span class="progress-description">
                                        Ultimo {{ date('d/m/Y', strtotime($calls->ultimo)) }}
                                    </span>
                                </div>
                            </div>
                        </div>
            
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="info-box bg-primary">
                                <span class="info-box-icon">
                                    <i class="far fa-bookmark"></i>
                                </span>
                                
                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        Total de Registros
                                    </span>
                                    <span class="info-box-number">
                                        {{ $calls->total }}
                                    </span>

                                    <div class="progress">
                                        <div class="progress-bar" style="width: 100%"></div>
                                    </div>
                                    <span class="progress-description">
                                        Registros no Banco
                                    </span>
                                </div>
                            </div>
                        </div>
            
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="info-box bg-success">
                                <span class="info-box-icon">
                                    <i class="far fa-thumbs-up"></i>
                                </span>

                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        Ligações Tarifadas
                                    </span>
                                    <span class="info-box-number">
                                        {{ $calls->tarifadas }}
                                    </span>

                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{substr(($calls->tarifadas * 100 ) / $calls->total, 0, 4)}}%"></div>
                                    </div>
                                    <span class="progress-description">
                                    {{ substr(($calls->tarifadas * 100 ) / $calls->total, 0, 4) }}% Ligações Tarifadas
                                    </span>
                                </div>
                            </div>
                        </div>
            
                        <div class="col-md-3 col-sm-6 col-12">
                        <a href="{{ route('status.show','calls') }}">
                            <div class="info-box bg-danger">
                                <span class="info-box-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                                </span>

                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        Ligações
                                    </span>
                                    <span class="info-box-number">
                                    {{ $calls->erros }}
                                    </span>

                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{substr(($calls->erros * 100 ) / $calls->total, 0,4)}}%"></div>
                                    </div>
                                    <span class="progress-description">
                                        {{ substr(($calls->erros * 100 ) / $calls->total, 0,4) }}% Ligações com Erro
                                    </span>
                                </div>
                            </div>
                        </div>
                        </a>
                    </div>

                          <hr>                

                    <h5 class="mt-4 mb-2">Cadastros</h5>
                    <div class="row">
                        
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="info-box bg-success">
                                <span class="info-box-icon">
                                    <i class="far fa-thumbs-up"></i>
                                </span>

                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        Troncos Cadastrados
                                    </span>
                                    <span class="info-box-number">
                                       {{ $trunks->Cadastrados }}
                                    </span>

                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ substr(($trunks->Cadastrados * 100 ) / ($trunks->NCadastrados+$trunks->Cadastrados), 0,4) }}%"></div>
                                    </div>
                                    
                                    <span class="progress-description">
                                    {{ substr(($trunks->Cadastrados * 100 ) / ($trunks->NCadastrados+$trunks->Cadastrados), 0,4) }}% No Sistema
                                    </span>
                                </div>
                            </div>
                        </div>
            
                        <div class="col-md-3 col-sm-6 col-12">
                            <a href="{{ route('status.show','trunks') }}">
                            <div class="info-box bg-danger">
                                <span class="info-box-icon">
                                    <i class="far fa-thumbs-down"></i>
                                </span>

                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        Troncos Não Cadastrados
                                    </span>
                                    <span class="info-box-number">
                                        {{ $trunks->NCadastrados }}
                                    </span>

                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ substr(($trunks->NCadastrados * 100 ) / ($trunks->NCadastrados+$trunks->Cadastrados), 0,4) }}%"></div>
                                    </div>
                                    <span class="progress-description">
                                    {{ substr(($trunks->NCadastrados * 100 ) / ($trunks->NCadastrados+$trunks->Cadastrados), 0,4) }}% No Sistema
                                    </span>
                                </div>
                            </div>
                            </a>
                        </div>
            
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="info-box bg-success">
                                <span class="info-box-icon">
                                    <i class="far fa-thumbs-up"></i>
                                </span>

                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        Ramais Cadastrados
                                    </span>
                                    <span class="info-box-number">
                                        {{ $extensions->Cadastrados }}
                                    </span>

                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ substr(($extensions->Cadastrados * 100 ) / ($extensions->NCadastrados+$extensions->Cadastrados), 0,4) }}%"></div>
                                    </div>
                                    <span class="progress-description">
                                    {{ substr(($extensions->Cadastrados * 100 ) / ($extensions->NCadastrados+$extensions->Cadastrados), 0,4) }}% No Sistema
                                    </span>
                                </div>
                            </div>
                        </div>
            
                        <div class="col-md-3 col-sm-6 col-12">
                        <a href="{{ route('status.show','extensions') }}">
                            <div class="info-box bg-danger">
                                    <span class="info-box-icon">
                                    <i class="far fa-thumbs-down"></i>
                                </span>
                                                                
                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        Ramais Não Cadastrados
                                    </span>
                                    <span class="info-box-number">
                                    {{ $extensions->NCadastrados }}
                                    </span>

                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ substr(($extensions->NCadastrados * 100 ) / ($extensions->NCadastrados+$extensions->Cadastrados), 0,4) }}%"></div>
                                    </div>
                                    <span class="progress-description">
                                    {{ substr(($extensions->NCadastrados * 100 ) / ($extensions->NCadastrados+$extensions->Cadastrados), 0,4) }}% No Sistema
                                    </span>
                                </div>
                            </div>
                        </a>
                        </div>
                        
                    </div>
                </div>
                <div class="card-footer clearfix">
                    <div class="d-flex bd-highlight">
                        <div class="mr-auto p-2 bd-highlight">
                        </div>
                        <div class="p-2 bd-highlight">
					    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
 
  $(function () {
        $('.select-extensions').select2({
            placeholder: "Selecione Ramais"
        })

    });

    $(document).ready(function(){
    
        $("#delete :input").prop("disabled", true);
        $("#show :input").prop("disabled", true);
    });

    $("#checkbox").click(function(){
        if($("#checkbox").is(':checked') ){
            $("#extensions > optgroup > option").prop("selected","selected");
            $("#extensions").trigger("change");
        }else{
            $("#extensions > optgroup > option").prop("selected","");
            $("#extensions").trigger("change");
        }
    });

</script>
@stop
@section('css')
<link rel="stylesheet" href="{{ asset('vendor/css/icheck/icheck-bootstrap.min.css') }}">
@stop