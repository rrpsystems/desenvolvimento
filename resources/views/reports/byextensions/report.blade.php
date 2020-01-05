@extends('adminlte::page')

@section('title', 'Configurações')

@section('content_header')
    <div class="container-fluid">
        <div class="d-flex justify-content-end">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Relatorios</a></li>
                <li class="breadcrumb-item active">Por Ramais</li>
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
                            <h3 class="card-title">Relatorios</h3>
                        </div>
                        <div class="p-2 bd-highlight">
                        </div>
                        <div class="p-2 bd-highlight">
					    </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table no-wrap table-sm table-valign-middle">
                            
                            @forelse($calls as $exten => $dates)
                            
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Ramal: </th>
                                        <th colspan="15" style ="text-align:left;">{{ $exten }} - {{ $extens[$exten][0]->ename }} </th>
                                    </tr>
                                </thead>

                                <thead class="table-secondary">
                                    <tr>
                                        <th>Data / Hora</th>
                                        <th>Direção</th>
                                        <th>Tronco</th>
                                        <th>DDR</th>
                                        <th>Numero Disc.</th>
                                        <th>Localidade</th>
                                        <th>Duração</th>
                                        <th>Serviço</th>
                                        <th>Valor R$</th>
                                    </tr>
                                </thead>

                                @foreach($dates as $date => $calls)

                                    <thead class="table-active">
                                        <tr>
                                            <th style ="text-align:left;">{{ $date }} </th>
                                            <th colspan="15" style ="text-align:left;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($calls as $call)

                                    <tr>
                                        <td></td>
                                        <td> {{ date('H:i:s', strtotime($call->calldate)) }} </td>
                                        <td> @lang("calls.$call->direction")</td>
                                        <td> {{ $call->trunks_id }} </td>
                                        <td> {{ $call->did }} </td>
                                        <td> {{ $call->dialnumber }} </td>
                                        <td> {{ $call->locale }} </td>
                                        <td> {{ gmdate("H:i:s", $call->billsec) }} </td>
                                        <td> {{ $call->cservice }} </td>
                                        <td> R$ {{number_format($call->rate, 2, ',', '.') }} </td>
                                    </tr>

                                    @endforeach
                            
                                    </tbody>
                                @endforeach
                            
                                
                                @empty

                                @endforelse
                                
                        </table>
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
@section('css')
<style>
th,td {
    text-align: center;
    white-space: nowrap;
    }
</style>
@stop