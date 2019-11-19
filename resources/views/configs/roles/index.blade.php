@extends('adminlte::page')

@section('title', 'Configurações')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                @if($errors->any())
                    @foreach($errors->all() as $error)
                        {!! $error !!}
                    @endforeach
                @elseif(session()->has('msg'))
                    {!! session('msg') !!}
                @endif
            </div>
					
			<div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Configurações</a></li>
                    <li class="breadcrumb-item active">Permissões</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-primary card-outline">
                <div class="card-header no-border">
                    <div class="row">
                        <div class=" col-6">
                            <h3 class="card-title">Permissões</h3>
                        </div>
                        <div class="col-4">
                            <div class="row row justify-content-end">
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
                        <div class="col-2">
                            @can('roles-create')
								<button type="button" class="btn btn-outline-success btn-sm" data-toggle="modal" data-target="#modal-create">
									Cadastrar
								</button>
                            @endcan
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
                                      
                                <tr>
                                    <td>role->id</td>
                                    <td>role->name</td>
                                    <td>
                                        <a class="btn btn-outline-info btn-xs" href="route('roles.show',role->id) ">
                                            <i class="far fa-eye"></i>
                                        </a>
                                        @can('roles-create')
                                            <a class="btn btn-outline-warning btn-xs" href="route('roles.edit',role->id)">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        @endcan
                                        @can('roles-create')
                                            <a class="btn btn-outline-danger btn-xs" href="route('roles.show',role->id.'-del')" >
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        @endcan
                                    </td>
                                </tr>


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
