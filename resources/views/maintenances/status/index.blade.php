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
                                    <i class="fas fa-info"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        Total de Registros
                                    </span>
                                    <span class="info-box-number">
                                        {{ number_format($calls->total, 0, '', '.') }}
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
                                        {{ number_format($calls->tarifadas, 0, '', '.') }}
                                    </span>
                                    <div class="progress">
                                        <div class="progress-bar"
                                            style="width: {{ substr(($calls->tarifadas * 100) / ($calls->total ? $calls->total : 1), 0, 4) }}%">
                                        </div>
                                    </div>
                                    <span class="progress-description">
                                        {{ substr(($calls->tarifadas * 100) / ($calls->total ? $calls->total : 1), 0, 4) }}%
                                        Correto
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6 col-12">
                            <a href="{{ route('status.show', 'calls') }}">
                                <div class="info-box bg-danger">
                                    <span class="info-box-icon">
                                        <i class="far fa-thumbs-down"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">
                                            Ligações Com Erros
                                        </span>
                                        <span class="info-box-number">
                                            {{ number_format($calls->erros, 0, '', '.') }}
                                        </span>
                                        <div class="progress">
                                            <div class="progress-bar"
                                                style="width: {{ substr(($calls->erros * 100) / ($calls->total ? $calls->total : 1), 0, 4) }}%">
                                            </div>
                                        </div>
                                        <span class="progress-description">
                                            {{ substr(($calls->erros * 100) / ($calls->total ? $calls->total : 1), 0, 4) }}%
                                            Errado
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>

                    </div>

                    <hr>

                    <h5 class="mt-4 mb-2">Cadastros</h5>
                    <div class="row">

                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="info-box bg-primary">
                                <span class="info-box-icon">
                                    <i class="fas fa-info"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        Usuarios Cadastrados
                                    </span>
                                    <span class="info-box-number">
                                        {{ number_format($informations->users, 0, '', '.') }}
                                    </span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: 0%"></div>
                                    </div>
                                    <span class="progress-description">
                                        Registros no Banco
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="info-box bg-primary">
                                <span class="info-box-icon">
                                    <i class="fas fa-info"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        Prefixos Cadastrados
                                    </span>
                                    <span class="info-box-number">
                                        {{ number_format($informations->prefixes, 0, '', '.') }}
                                    </span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: 0%"></div>
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
                                    <span class="info-box-text" style="padding-right:25px;">
                                        Troncos Cad.
                                    </span>
                                    <span class="info-box-number">
                                        {{ number_format($trunks->Cadastrados, 0, '', '.') }}
                                    </span>
                                    <div class="progress">
                                        <div class="progress-bar"
                                            style="width: {{ substr(($trunks->Cadastrados * 100) / ($trunks->NCadastrados + $trunks->Cadastrados ? $trunks->NCadastrados + $trunks->Cadastrados : 1), 0, 4) }}%">
                                        </div>
                                    </div>
                                    <span class="progress-description">
                                        {{ substr(($trunks->Cadastrados * 100) / ($trunks->NCadastrados + $trunks->Cadastrados ? $trunks->NCadastrados + $trunks->Cadastrados : 1), 0, 4) }}%
                                        Correto
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6 col-12">
                            <a href="{{ route('status.show', 'trunks') }}">
                                <div class="info-box bg-danger">
                                    <span class="info-box-icon">
                                        <i class="far fa-thumbs-down"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">
                                            Troncos Não Cad.
                                        </span>
                                        <span class="info-box-number">
                                            {{ number_format($trunks->NCadastrados, 0, '', '.') }}
                                        </span>
                                        <div class="progress">
                                            <div class="progress-bar"
                                                style="width: {{ substr(($trunks->NCadastrados * 100) / ($trunks->NCadastrados + $trunks->Cadastrados ? $trunks->NCadastrados + $trunks->Cadastrados : 1), 0, 4) }}%">
                                            </div>
                                        </div>
                                        <span class="progress-description">
                                            {{ substr(($trunks->NCadastrados * 100) / ($trunks->NCadastrados + $trunks->Cadastrados ? $trunks->NCadastrados + $trunks->Cadastrados : 1), 0, 4) }}%
                                            Errado
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>

                    </div>

                    <hr>
                    <br>

                    <div class="row">

                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="info-box bg-success">
                                <span class="info-box-icon">
                                    <i class="far fa-thumbs-up"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text" style="padding-right:25px;">
                                        Ramais Cad.
                                    </span>
                                    <span class="info-box-number">
                                        {{ number_format($extensions->Cadastrados, 0, '', '.') }}
                                    </span>
                                    <div class="progress">
                                        <div class="progress-bar"
                                            style="width: {{ substr(($extensions->Cadastrados * 100) / ($extensions->NCadastrados + $extensions->Cadastrados ? $extensions->NCadastrados + $extensions->Cadastrados : 1), 0, 4) }}%">
                                        </div>
                                    </div>
                                    <span class="progress-description">
                                        {{ substr(($extensions->Cadastrados * 100) / ($extensions->NCadastrados + $extensions->Cadastrados ? $extensions->NCadastrados + $extensions->Cadastrados : 1), 0, 4) }}%
                                        Correto
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6 col-12">
                            <a href="{{ route('status.show', 'extensions') }}">
                                <div class="info-box bg-danger">
                                    <span class="info-box-icon">
                                        <i class="far fa-thumbs-down"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">
                                            Ramais Não Cad.
                                        </span>
                                        <span class="info-box-number">
                                            {{ number_format($extensions->NCadastrados, 0, '', '.') }}
                                        </span>
                                        <div class="progress">
                                            <div class="progress-bar"
                                                style="width: {{ substr(($extensions->NCadastrados * 100) / ($extensions->NCadastrados + $extensions->Cadastrados ? $extensions->NCadastrados + $extensions->Cadastrados : 1), 0, 4) }}%">
                                            </div>
                                        </div>
                                        <span class="progress-description">
                                            {{ substr(($extensions->NCadastrados * 100) / ($extensions->NCadastrados + $extensions->Cadastrados ? $extensions->NCadastrados + $extensions->Cadastrados : 1), 0, 4) }}%
                                            Errado
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
                                    <span class="info-box-text" style="padding-right:25px;">
                                        Cod. Conta Cad.
                                    </span>
                                    <span class="info-box-number">
                                        {{ number_format($accountcodes->Cadastrados, 0, '', '.') }}
                                    </span>
                                    <div class="progress">
                                        <div class="progress-bar"
                                            style="width: {{ substr(($accountcodes->Cadastrados * 100) / ($accountcodes->NCadastrados + ($accountcodes->Cadastrados == 0 ? 1 : $accountcodes->Cadastrados)), 0, 4) }}%">
                                        </div>
                                    </div>
                                    <span class="progress-description">
                                        {{ substr(($accountcodes->Cadastrados * 100) / ($accountcodes->NCadastrados + ($accountcodes->Cadastrados == 0 ? 1 : $accountcodes->Cadastrados)), 0, 4) }}%
                                        Correto
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6 col-12">
                            <a href="{{ route('status.show', 'accountcodes') }}">
                                <div class="info-box bg-danger">
                                    <span class="info-box-icon">
                                        <i class="far fa-thumbs-down"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">
                                            Cod. Conta Não Cad.
                                        </span>
                                        <span class="info-box-number">
                                            {{ number_format($accountcodes->NCadastrados, 0, '', '.') }}
                                        </span>
                                        <div class="progress">
                                            <div class="progress-bar"
                                                style="width: {{ substr(($accountcodes->NCadastrados * 100) / ($accountcodes->NCadastrados + ($accountcodes->Cadastrados == 0 ? 1 : $accountcodes->Cadastrados)), 0, 4) }}%">
                                            </div>
                                        </div>
                                        <span class="progress-description">
                                            {{ substr(($accountcodes->NCadastrados * 100) / ($accountcodes->NCadastrados + ($accountcodes->Cadastrados == 0 ? 1 : $accountcodes->Cadastrados)), 0, 4) }}%
                                            Errado
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
