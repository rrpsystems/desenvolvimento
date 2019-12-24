@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="container-fluid">
        <div class="d-flex justify-content-end">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="$">Dashboard</a></li>
                <li class="breadcrumb-item active">Resumo</li>
            </ol>     
        </div>
    </div>
@stop

@section('content')
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header border-transparent">
                            <div class="row">
                                <div class="col-12 ">
                                    <form action="{{route('resumes.index')}}" method="GET">
                                        <div class="form-row justify-content-end">
                                            <div class="form-group col-md-4 ">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"
                                                            id="validationTooltipUsernamePrepend">Ramais</span>
                                                    </div>
                                                    <select class="form-control" name="exten" onchange="this.form.submit()">
                                                                
                                                        @foreach($extens as $exten):
                                                            @if($loop->first)
                                                                <option value="all">Todos</option>
                                                            @endif

                                                            <option value="{{$exten->extensions_id}}" @if($exten->extensions_id == $e) selected @endif >{{ $exten->extensions_id }}</option>        
                                                        
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <div class="input-group">
                                                    <select class="form-control" name="month" onchange="this.form.submit()">
                                                        @foreach($months as $key => $month)
                                                            <option value="{{$key}}" @if($key == $m) selected @endif>{{$month}}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text"
                                                            id="validationTooltipUsernamePrepend">
                                                            {{date('Y')}}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-transparent">
                            <div class="row">
                                <div class="col-4">
                                    <h3 class="card-title">Comsumo Ramais</h3>
                                </div>
                            </div>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="d-flex">
                                <p class="d-flex flex-column">
                                    <span class="text-success">
                                        <i class="fa fa-arrow-up"></i> Saida : 
                                        {{ ceil( ( $data->time->oc ) / 60 ) . ' Min.' }} | 
                                        {{' Qtd. ' . $data->qtd->oc }} | 
                                        {{'R$ '. number_format( ( $data->val->oc ), 2, ',', '.') }}
                                    </span>
                                </p>
                                <p class="ml-auto d-flex flex-column text-right">
                                    <span class="text-danger">
                                        <i class="fa fa-arrow-down"></i> Entrada : 
                                        {{ ceil( ( $data->time->ic ) / 60 ). ' Min.' }} | 
                                        {{' Qtd. ' . $data->qtd->ic }} | 
                                        {{'R$ '. number_format( ( $data->val->ic ), 2, ',', '.') }}
                                    </span>
                                </p>
                            </div>
                            <div class="position-relative mb-4">
                                <canvas id="resume-chart" height="200"></canvas>
                            </div>
                            <div class="d-flex flex-row justify-content-end">
                                <span class="mr-2">
                                    <i class="fa fa-square text-primary"></i> Minutos
                                </span>
                                <span class="mr-2">
                                    <i class="fa fa-square text-success"></i> Quantidade
                                </span>
                                <span class="mr-2">
                                    <i class="fa fa-square text-danger"></i> Valor
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header no-border">
                            <div class="d-flex justify-content-between">
                                <h3 class="card-title">Taxa Media de Ocupação</h3>
                            </div>
                            <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-tool" data-widget="remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                        </div>
                        <div class="card-body">
                            <div class="position-relative mb-4">
                                <canvas id="visitors-chart" height="200"></canvas>
                            </div>
                            <div class="d-flex flex-row justify-content-end">
                                <span class="mr-2">
                                    <i class="fa fa-square text-gray"></i> Total
                                </span>
                                <span class="mr-2">
                                    <i class="fa fa-square text-primary"></i> Interno
                                </span>
                                <span class="mr-2">
                                    <i class="fa fa-square text-danger"></i> Entrada
                                </span>
                                <span>
                                    <i class="fa fa-square text-success"></i> Saida
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header no-border">
                            <h3 class="card-title">Chamadas</h3>
                            <div class="card-tools">
                            </div>
                            <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-tool" data-widget="remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-striped table-valign-middle">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Quantidade</th>
                                        <th>Minutos</th>
                                        <th>Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td> DDI </td>
                                        <td> {{ $data->qtd->ddi }}</td>
                                        <td> {{ ceil( $data->time->ddi / 60 ) }}</td>
                                        <td> R$ {{ number_format( $data->val->ddi , 2, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td> DDD Movel </td>
                                        <td> {{ $data->qtd->vc2 + $data->qtd->vc3 }}</td>
                                        <td> {{ ceil( ( $data->time->vc2 + $data->time->vc3 ) / 60 ) }}</td>
                                        <td> R$ {{ number_format(( $data->val->vc2 + $data->val->vc3 ), 2, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td> DDD Fixo </td>
                                        <td> {{ $data->qtd->ddd }}</td>
                                        <td> {{ ceil( $data->time->ddd / 60 ) }}</td>
                                        <td> R$ {{ number_format( $data->val->ddd, 2, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td> Local Movel </td>
                                        <td> {{ $data->qtd->vc1 }}</td>
                                        <td> {{ ceil( $data->time->vc1 / 60 ) }}</td>
                                        <td> R$ {{ number_format( $data->val->vc1, 2, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td> Local Fixo </td>
                                        <td> {{ $data->qtd->local }}</td>
                                        <td> {{ ceil( $data->time->local / 60 ) }}</td>
                                        <td> R$ {{ number_format( $data->val->local, 2, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td> Serviços </td>
                                        <td> {{ $data->qtd->serviços }}</td>
                                        <td> {{ ceil( $data->time->serviços / 60 ) }}</td>
                                        <td> R$ {{ number_format( $data->val->serviços, 2, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td> Gratuitas - 0800 </td>
                                        <td> {{ $data->qtd->gratuito }}</td>
                                        <td> {{ ceil( $data->time->gratuito / 60 ) }}</td>
                                        <td> R$ {{ number_format( $data->val->gratuito, 2, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td> Outros </td>
                                        <td> {{ $data->qtd->outros }}</td>
                                        <td> {{ ceil( $data->time->outros / 60 ) }}</td>
                                        <td> R$ {{ number_format( $data->val->outros, 2, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td> Interno </td>
                                        <td> {{ $data->qtd->in }}</td>
                                        <td> {{ ceil( $data->time->in / 60 ) }}</td>
                                        <td> R$ {{ number_format( $data->val->in, 2, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td> Entrada </td>
                                        <td> {{ $data->qtd->ic }}</td>
                                        <td> {{ ceil( $data->time->ic / 60 ) }}</td>
                                        <td> R$ {{ number_format( $data->val->ic, 2, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td> Tie-Line </td>
                                        <td> {{ $data->qtd->tie_line }}</td>
                                        <td> {{ ceil( $data->time->tie_line / 60 ) }}</td>
                                        <td> R$ {{ number_format( $data->val->tie_line, 2, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td> Total de todas as chamadas </td>
                                        <td> {{ $data->qtd->total }}</td>
                                        <td> {{ ceil( $data->time->total / 60 ) }}</td>
                                        <td> R$ {{ number_format( $data->val->total, 2, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
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

@section('js')
<script>

$(function () {

    var $resumeChart = $('#resume-chart')
    var resumeChart  = new Chart($resumeChart, {
        type   : 'bar',
            data   : {
                labels  : ['DDI', 'DDD-Movel', 'DDD-Fixo', 'Local-Movel', 'Local-Fixo', 'Serviços', 'Gratuito', 'Outros', 'Interno', 'Entrada', 'Tie-Line'],
                datasets: [
                    {
                        backgroundColor: '#007bff',
                        borderColor    : '#007bff',
                        data           : [  
                                            {{ ceil( $data->time->ddi / 60 ) }},
                                            {{ ceil( ($data->time->vc2 + $data->time->vc3) / 60 ) }},
                                            {{ ceil( $data->time->ddd / 60 ) }},
                                            {{ ceil( $data->time->vc1 / 60 ) }},
                                            {{ ceil( $data->time->local / 60 ) }},
                                            {{ ceil( $data->time->serviços / 60 ) }},
                                            {{ ceil( $data->time->gratuito / 60 ) }},
                                            {{ ceil( $data->time->outros / 60 ) }},
                                            {{ ceil( $data->time->in / 60 ) }},
                                            {{ ceil( $data->time->ic / 60 ) }},
                                            {{ ceil( $data->time->tie_line / 60 ) }},
                                        ]
                    },{
                        backgroundColor: '#28a745',
                        borderColor    : '#28a745',
                        data           : [ 
                                            {{ $data->qtd->ddi }},
                                            {{  ($data->qtd->vc2 + $data->qtd->vc3) }},
                                            {{  $data->qtd->ddd }},
                                            {{  $data->qtd->vc1 }},
                                            {{  $data->qtd->local }},
                                            {{  $data->qtd->serviços }},
                                            {{  $data->qtd->gratuito }},
                                            {{  $data->qtd->outros }},
                                            {{  $data->qtd->in }},
                                            {{  $data->qtd->ic }},
                                            {{  $data->qtd->tie_line }},
                                        ]
                    },{
                        backgroundColor: '#dc3545',
                        borderColor    : '#dc3545',
                        data           : [ 
                                            {{ $data->val->ddi }},
                                            {{  ($data->val->vc2 + $data->val->vc3) }},
                                            {{  $data->val->ddd }},
                                            {{  $data->val->vc1 }},
                                            {{  $data->val->local }},
                                            {{  $data->val->serviços }},
                                            {{  $data->val->gratuito }},
                                            {{  $data->val->outros }},
                                            {{  $data->val->in }},
                                            {{  $data->val->ic }},
                                            {{  $data->val->tie_line }},
                                        ]
                    }
                ]
            },
    
            options:{
                maintainAspectRatio: false,
                tooltips           : {
                    mode     : 'index',
                    intersect: true
                },
                hover              : {
                    mode     : 'index',
                    intersect: true
                },
                legend             : {
                    display: false
                },
                scales             : {
                    yAxes: [{
                        gridLines: {
                            display      : true,
                            lineWidth    : '4px',
                            color        : 'rgba(0, 0, 0, .2)',
                            zeroLineColor: 'transparent'
                        },
                        ticks:{
                        beginAtZero:true
                    }
                    }],
                    xAxes: [{
                        display  : true,
                        gridLines: {
                            display: false
                        },
                        
                    }]
                    
                }
            }
        })
    
        var $visitorsChart = $('#visitors-chart')
    var visitorsChart = new Chart($visitorsChart, {
        data: {
            labels: ['0hs', '1hs', '2hs', '3hs', '4hs', '5hs', '6hs', '7hs', '8hs', '9hs', '10hs', '11hs', '12hs', '13hs', '14hs', '15hs', '16hs', '17hs', '18hs', '19hs', '20hs', '21hs', '22hs', '23hs'],
            datasets: [{
                    type: 'line',
                    data: [

                        @foreach($data->hour as $h)
                            {{ ceil( $h->total / $days ) }},
                        @endforeach

                    ],
                    backgroundColor: 'transparent',
                    borderColor: '#696969',
                    pointBorderColor: '#696969',
                    pointBackgroundColor: '#696969',
                    fill: false
                },
                {
                    type: 'line',
                    data: [
                        
                        @foreach($data->hour as $h)
                            {{ ceil( $h->ic / $days ) }},
                        @endforeach

                    ],
                    backgroundColor: 'tansparent',
                    borderColor: '#FF0000',
                    pointBorderColor: '#FF0000',
                    pointBackgroundColor: '#FF0000',
                    fill: false
                },
                {
                    type: 'line',
                    data: [

                        @foreach($data->hour as $h)
                            {{ ceil( $h->oc / $days ) }},
                        @endforeach
 
                    ],
                    backgroundColor: 'tansparent',
                    borderColor: '#228B22',
                    pointBorderColor: '#228B22',
                    pointBackgroundColor: '#228B22',
                    fill: false
                },
                {
                    type: 'line',
                    data: [
                        
                        @foreach($data->hour as $h)
                            {{ ceil( $h->in / $days ) }},
                        @endforeach

                    ],
                    backgroundColor: 'tansparent',
                    borderColor: '#007bff',
                    pointBorderColor: '#007bff',
                    pointBackgroundColor: '#007bff',
                    fill: false
                }
            ]
        },
        options:{
                maintainAspectRatio: false,
                tooltips           : {
                    mode     : 'index',
                    intersect: true
                },
                hover              : {
                    mode     : 'index',
                    intersect: true
                },
                legend             : {
                    display: false
                },
                scales             : {
                    yAxes: [{
                        gridLines: {
                            display      : true,
                            lineWidth    : '4px',
                            color        : 'rgba(0, 0, 0, .2)',
                            zeroLineColor: 'transparent'
                        },
                        ticks:{
                        beginAtZero:true
                    }
                    }],
                    xAxes: [{
                        display  : true,
                        gridLines: {
                            display: false
                        },
                        
                    }]
                    
                }
            }
    })
    
    })

    </script>

@stop