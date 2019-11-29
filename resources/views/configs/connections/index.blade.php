@extends('adminlte::page')

@section('title', 'Configurações')

@section('content_header')
    <div class="container-fluid">
        <div class="d-flex justify-content-end">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Configurações</a></li>
                <li class="breadcrumb-item active">Coletores</li>
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
                            <h3 class="card-title">Coletores</h3>
                        </div>
                        <div class="p-2 bd-highlight">
                            <div class="row">
                                <form action="{{route('connections.index')}}" method="GET" >
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-prepend">
											<span class="input-group-text">
												<a href="{{route('connections.index')}}">
													<i class="fas fa-recycle"></i>
                                                </a>
                                            </span>
                                        </span>
                                        <input type="text" name="search" class="form-control" value="{{$search ?? ''}}" placeholder="PBX">
                                        <span class="input-group-append">
                                            <button type="submit" class="btn btn-outline-primary btn-flat">Pesquisar</button>
                                        </span>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="p-2 bd-highlight">
                            @can('connections-create')
                                <a class="btn btn-outline-success btn-sm" href="{{ route('connections.create') }}">
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
                                    <th>PBX</th>
                                    <th>Tipo</th>
                                    <th>host</th>
                                    <th>Porta</th>
                                    <th>Usuario</th>
                                    <th>Senha</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($connections as $connection)
                                    <tr>
                                        <td></td>
                                        <td> {{ $connection->name }} </td>
                                        <td> {{ $connection->type }} </td>
                                        <td> {{ $connection->host }} </td>
                                        <td> {{ $connection->port }} </td>
                                        <td> {{ $connection->user }} </td>
                                        <td> {{ $connection->password }} </td>
                                        <td>
                                            <div class="form-inline">
                                                @can('connections-edit')
                                                    <div class="p-1">
                                                        <a class="btn btn-outline-warning btn-xs" href="{{ route('connections.edit',$connection->id) }}">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('connections-delete')
                                                    <div class="p-1">
                                                        <form action="{{ route('connections.destroy', 'del-'.$connection->id) }}" method="POST">
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
                                {{ $connections->appends(['search' => $search])->links('vendor.pagination.sm-float-rigth') }}
                            @else
                                {{ $connections->links('vendor.pagination.sm-float-rigth') }}
                            @endif
						</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
