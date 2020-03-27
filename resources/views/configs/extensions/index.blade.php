@extends('adminlte::page')

@section('title', 'Configurações')

@section('content_header')
    <div class="container-fluid">
        <div class="d-flex justify-content-end">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Configurações</a></li>
                <li class="breadcrumb-item active">Ramais</li>
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
                            <h3 class="card-title">Ramais</h3>
                        </div>
                        <div class="p-2 bd-highlight">
                            <div class="row">
                                <form action="{{route('extensions.index')}}" method="GET" >
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-prepend">
											<span class="input-group-text">
												<a href="{{route('extensions.index')}}">
													<i class="fas fa-recycle"></i>
                                                </a>
                                            </span>
                                        </span>
                                        <input type="text" name="search" class="form-control" value="{{$search ?? ''}}" placeholder="Ramal">
                                        <span class="input-group-append">
                                            <button type="submit" class="btn btn-outline-primary btn-flat">Pesquisar</button>
                                        </span>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="p-2 bd-highlight">
                            @can('cfg_extensions-create')
                                <a class="btn btn-outline-success btn-sm" href="{{ route('extensions.create') }}">
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
                                    <th>Ramal</th>
                                    <th>Nome</th>
                                    <th>Grupo</th>
                                    <th>Departamento</th>
                                    <th>Login</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($extensions as $extension)
                                    <tr>
                                        <td></td>
                                        <td> {{ $extension->pbxes_id }} </td>
                                        <td> {{ $extension->extension }} </td>
                                        <td> {{ $extension->ename }} </td>
                                        <td> {{ $extension->groups_id }} </td>
                                        <td> {{ $extension->departaments_id }} </td>
                                        <td> {{ $extension->users_id }} </td>
                                        <td>
                                            <div class="form-inline">
                                            @can('cfg_extensions-list')
                                                    <div class="p-1">
                                                        <a class="btn btn-outline-info btn-xs" href="{{ route('extensions.show',$extension->id) }}">
                                                            <i class="far fa-eye"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('cfg_extensions-edit')
                                                    <div class="p-1">
                                                        <a class="btn btn-outline-warning btn-xs" href="{{ route('extensions.edit',$extension->id) }}">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('cfg_extensions-delete')
                                                    <div class="p-1">
                                                        <form action="{{ route('extensions.destroy', 'del-'.$extension->id) }}" method="POST">
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
                                {{ $extensions->appends(['search' => $search])->links('vendor.pagination.sm-float-rigth') }}
                            @else
                                {{ $extensions->links('vendor.pagination.sm-float-rigth') }}
                            @endif
						</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
