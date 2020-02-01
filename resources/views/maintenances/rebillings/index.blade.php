@extends('adminlte::page')

@section('title', 'Retarifação')

@section('content_header')
    <div class="container-fluid">
        <div class="d-flex justify-content-end">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Manutenções</a></li>
                <li class="breadcrumb-item"><a href="#">Retarifação</a></li>
                <li class="breadcrumb-item active">Filtro</li>
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
                            <h3 class="card-title">Retarifação das Chamadas</h3>
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

                <form action="{{route('rebilling.store')}}" method="POST">
                    @csrf                    
                
                    <div class="form-group clearfix">
                      <div class="icheck-success">
                        <input type="radio" name="billing" checked="" id="radioSuccess1" value="all">
                        <label for="radioSuccess1">Retarifar Todas as Ligações
                        </label>
                      </div>
                      <br>
                      <div class="icheck-success">
                        <input type="radio" name="billing" id="radioSuccess2" value="errors">
                        <label for="radioSuccess2">Retarifar Ligações com Erros
                        </label>
                      </div>
                      <br>
                      <div class="icheck-success">
                        <input type="radio" name="billing" id="radioSuccess3" value="period">
                        <label for="radioSuccess3">Retarifar Periodo
                        </label>
                      </div>
                    </div>
                    <br>
                    <div class="form-row">
                    
                        <div class="form-group col-5 col-md-3">
                            <label for="start_date">Data Inicial:</label>
                            <input type="date" class="form-control {{ $errors->has('start_date') ? 'is-invalid' : '' }}" name="start_date" id="start_date" value="{{ old('start_date')?substr(old('start_date'),0,10):date('Y-m-01') }}" >
                            @if ($errors->has('start_date'))
                                <div class="invalid-feedback">
                                    @lang($errors->first('start_date').'-start_date')
                                </div>
                            @endif
                        </div>
                    
                    
                        <div class="form-group col-5 col-md-3">
                            <label for="end_date">Data Final:</label>
                            <input type="date" class="form-control {{ $errors->has('end_date') ? 'is-invalid' : '' }}" name="end_date" id="end_date" value="{{ old('end_date')?substr(old('end_date'),0,10):date('Y-m-t') }}">
                            @if ($errors->has('end_date'))
                                <div class="invalid-feedback">
                                    @lang($errors->first('end_date').'-end_date')
                                </div>
                            @endif
                        </div>
                    
                    </div>
                    
                <div class="card-footer clearfix">
                    <div class="d-flex bd-highlight">
                        <div class="mr-auto p-2 bd-highlight">
                        </div>
                        <div class="p-2 bd-highlight">
                                <button type="submit" class="btn btn-primary">Retarifar</button>
                            </form>

					    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('vendor/css/icheck/icheck-bootstrap.min.css') }}">
@stop