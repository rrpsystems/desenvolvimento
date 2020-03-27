@extends('adminlte::page')

@section('title', 'Configurações')

@section('content_header')
    <div class="container-fluid">
        <div class="d-flex justify-content-end">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Configurações</a></li>
                <li class="breadcrumb-item active">Tarifas</li>
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
                            <h3 class="card-title">Tarifas</h3>
                        </div>
                        <div class="p-2 bd-highlight">
                            <div class="row">
                                <form action="{{route('rates.index')}}" method="GET" >
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-prepend">
											<span class="input-group-text">
												<a href="{{route('rates.index')}}">
													<i class="fas fa-recycle"></i>
                                                </a>
                                            </span>
                                        </span>
                                        <input type="text" name="search" class="form-control" value="{{$search ?? ''}}" placeholder="Tarifa">
                                        <span class="input-group-append">
                                            <button type="submit" class="btn btn-outline-primary btn-flat">Pesquisar</button>
                                        </span>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="p-2 bd-highlight">
                            @can( 'cfg_rates-create')
                                <a class="btn btn-outline-success btn-sm" href="{{ route('rates.create') }}">
                        		    Cadastrar
                                </a>
                            @endcan
					    </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table no-wrap table-sm table-striped table-valign-middle">
                            <thead class="thead-dark">
                                <tr>
                                    <th></th>
                                    <th>Tarifa</th>
                                    <th>Rota</th>
                                    <th>Serviço</th>
                                    <th>Tipo</th>
                                    <th>Direção</th>
                                    <th>Valor</th>
                                    <th>start time</th>
                                    <th>Tempo Min</th>
                                    <th>Incremento</th>
                                    <th>Conexao</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rates as $rate)
                                    <tr>
                                        <td></td>
                                        <td> {{ $rate->rname }} </td>
                                        <td> {{ $rate->routes_route }} </td>
                                        <td> {{ $rate->prefixes_service }} </td>
                                        <td> {{ $rate->type }} </td>
                                        <td> {{ $rate->direction }} </td>
                                        <td> R$ {{ number_format( $rate->rate, 2, '.', ' ')}} </td>
                                        <td> {{ $rate->stime }} </td>
                                        <td> {{ $rate->ttmin }} </td>
                                        <td> {{ $rate->increment }} </td>
                                        <td> R$ {{number_format( $rate->connection, 2, '.', ' ') }} </td>
                                        <td>
                                            <div class="form-inline">
                                            @can( 'cfg_rates-list')
                                                    <div class="p-1">
                                                        <a class="btn btn-outline-info btn-xs" href="{{ route('rates.show',$rate->id) }}">
                                                            <i class="far fa-eye"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can( 'cfg_rates-edit')
                                                    <div class="p-1">
                                                        <a class="btn btn-outline-warning btn-xs" href="{{ route('rates.edit',$rate->id) }}">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can( 'cfg_rates-delete')
                                                    <div class="p-1">
                                                        <form action="{{ route('rates.destroy', 'del-'.$rate->id) }}" method="POST">
                                                            @method('DELETE')
                                                            @csrf
                                                            <button class="btn btn-outline-danger btn-xs" >
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center"> Não foram encontrados dados para exibição!</td>
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
                                {{ $rates->appends(['search' => $search])->links('vendor.pagination.sm-float-rigth') }}
                            @else
                                {{ $rates->links('vendor.pagination.sm-float-rigth') }}
                            @endif
						</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
