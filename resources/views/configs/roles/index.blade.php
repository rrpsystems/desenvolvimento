@extends('adminlte::page')

@section('title', 'Configurações')

@section('content_header')
    <div class="container-fluid">
        <div class="d-flex justify-content-end">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Configurações</a></li>
                <li class="breadcrumb-item active">Permissões</li>
            </ol>     
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-primary card-outline">
                <div class="card-header no-border">
                    <div class="d-flex bd-highlight">
                        <div class="mr-auto p-2 bd-highlight">
                            <h3 class="card-title">Permissões</h3>
                        </div>
                        <div class="p-2 bd-highlight">
                            <div class="row">
                                <form action="{{route('roles.index')}}" method="GET" >
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-prepend">
											<span class="input-group-text">
												<a href="{{route('roles.index')}}">
													<i class="fas fa-recycle"></i>
                                                </a>
                                            </span>
                                        </span>
                                        <input type="text" name="search" class="form-control" value="{{$search ?? ''}}" placeholder="Permissão">
                                        <span class="input-group-append">
                                            <button type="submit" class="btn btn-outline-primary btn-flat">Pesquisar</button>
                                        </span>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="p-2 bd-highlight">
                            <a class="btn btn-outline-success btn-sm" href="{{ route('roles.create') }}">
                        		Cadastrar
                            </a>
					    </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table no-wrap table-sm table-striped table-valign-middle">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Permissão</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($roles as $role)
                                    <tr>
                                        <td> {{ $role->id }} </td>
                                        <td> {{ $role->name }} </td>
                                        <td>
                                            <div class="form-inline">
                                                @can('roles-list')
                                                    <div class="p-1">
                                                        <a class="btn btn-outline-info btn-xs" href="{{ route('roles.show',$role->id) }}">
                                                            <i class="far fa-eye"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('roles-edit')
                                                    <div class="p-1">
                                                        <a class="btn btn-outline-warning btn-xs" href="{{ route('roles.edit',$role->id) }}">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('roles-delete')
                                                    <div class="p-1">
                                                        <form action="{{ route('roles.destroy', 'del-'.$role->id) }}" method="POST">
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
                                        <td colspan="3" class="text-center"> Não foram encontrados dados para exibição!</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

