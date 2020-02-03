@extends('adminlte::page')

@section('title', 'Manutenções')

@section('content_header')
    <div class="container-fluid">
        <div class="d-flex justify-content-end">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Manutenções</a></li>
                <li class="breadcrumb-item"><a href="#">Status</a></li>
                <li class="breadcrumb-item active">Codigos de Conta</li>
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
                            <h3 class="card-title">Codigos de Conta Não Cadastrados</h3>
                        </div>
                        <div class="p-2 bd-highlight">
                            <div class="row">
                            </div>
                        </div>
                        <div class="p-2 bd-highlight">
                        <a class="btn btn-outline-primary btn-sm" href="{{ route('status.index') }}">
                        		    Voltar
                                </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                <div class="table-responsive">
                        <table class="table no-wrap table-sm table-striped table-valign-middle">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>PBX</th>
                                    <th>Codigo de Conta</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($accountcodes as $accountcode)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{ $accountcode->pbx }} </td>
                                        <td>{{ $accountcode->accountcodes_id }} </td>
                                        <td> 
                                        <a class="btn btn-outline-success btn-sm" href="{{ route('accountcodes.create', ['pbxes_id='.$accountcode->pbx, 'accountcode='.$accountcode->accountcodes_id]) }}">
                        		            Cadastrar
                                        </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center"> Não foram encontrados dados para exibição!</td>
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
        $('.select-accountcodes').select2({
            placeholder: "Selecione Codigos de Conta"
        })

    });

    $(document).ready(function(){
    
        $("#delete :input").prop("disabled", true);
        $("#show :input").prop("disabled", true);
    });

    $("#checkbox").click(function(){
        if($("#checkbox").is(':checked') ){
            $("#accountcodes > optgroup > option").prop("selected","selected");
            $("#accountcodes").trigger("change");
        }else{
            $("#accountcodes > optgroup > option").prop("selected","");
            $("#accountcodes").trigger("change");
        }
    });

</script>
@stop
@section('css')
<link rel="stylesheet" href="{{ asset('vendor/css/icheck/icheck-bootstrap.min.css') }}">
@stop