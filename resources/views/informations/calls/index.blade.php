@extends('adminlte::page')

@section('title', 'Configurações')

@section('content_header')
    <div class="container-fluid">
        <div class="d-flex justify-content-end">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Banco</a></li>
                <li class="breadcrumb-item active">Ligações</li>
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
                            <h3 class="card-title">Ligações</h3>
                        </div>
                        <div class="p-2 bd-highlight">
                            <div class="row">
                                <form action="{{route('calls.index')}}" method="GET" >
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-prepend">
											<span class="input-group-text">
												<a href="{{route('calls.index')}}">
													<i class="fas fa-recycle"></i>
                                                </a>
                                            </span>
                                        </span>
                                        <input type="text" name="search" class="form-control" value="{{$search ?? ''}}" placeholder="Numero">
                                        <span class="input-group-append">
                                            <button type="submit" class="btn btn-outline-primary btn-flat">Pesquisar</button>
                                        </span>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="p-2 bd-highlight">
     
					    </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table no-wrap table-sm table-striped table-valign-middle">
                            <thead class="thead-dark">
                                <tr>
                                    <th></th>
                                    <th>Data</th>
                                    <th>Hora</th>
                                    <th>PBX</th>
                                    <th>Direção</th>
                                    <th>Ramal</th>
                                    <th>Tronco</th>
                                    <th>DDR</th>
                                    <th>Numero Disc.</th>
                                    <th>Numero E164</th>
                                    <th>Prefixo</th>
                                    <th>Serviço</th>
                                    <th>Valor R$</th>
                                    <th>Tarifa</th>
                                    <th>Ring</th>
                                    <th>Duração</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($calls as $call)
                                    <tr>
                                        <td>{{$call->cid}}</td>
                                        <td> {{ date('d/m/Y', strtotime($call->calldate)) }} </td>
                                        <td> {{ date('H:i:s', strtotime($call->calldate)) }} </td>
                                        <td> {{ $call->pbx }} </td>
                                        <td> @lang("calls.$call->direction")</td>
                                        <td> {{ $call->extensions_id }} </td>
                                        <td> {{ $call->trunks_id }} </td>
                                        <td> {{ $call->did }} </td>
                                        <td> {{ $call->dialnumber }} </td>
                                        <td> {{ $call->callnumber }} </td>
                                        <td> {{ $call->locale }} </td>
                                        <td> {{ $call->cservice }} </td>
                                        <td> R$ {{number_format($call->rate, 2, ',', '.') }} </td>
                                        <td> {{ $call->rates_id }} </td>
                                        <td> {{ gmdate("H:i:s", $call->ring) }} </td>
                                        <td> {{ gmdate("H:i:s", $call->billsec) }} </td>
                                        <td> {{ $call->status_id }} </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center"> Não foram encontrados dados para exibição!</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer clearfix">
                <div class="d-flex bd-highlight">
                        <div class="mr-auto p-2 bd-highlight">
                        </div>
                        <div class="p-2 bd-highlight">
                            @if(isset($search))
                                {{ $calls->appends(['search' => $search])->links('vendor.pagination.sm-float-rigth') }}
                            @else
                                {{ $calls->links('vendor.pagination.sm-float-rigth') }}
                            @endif
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