@extends('adminlte::page')

@section('title', 'Configurações')

@section('content_header')
    <div class="container-fluid">
        <div class="d-flex justify-content-end">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Relatorios</a></li>
                <li class="breadcrumb-item"><a href="#">Por Ramais</a></li>
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
                            <h3 class="card-title">Relatorio Por Ramais</h3>
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

                <form action="{{route('byextensions.store')}}" method="POST">
                    @csrf                    
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
                    
                        <div style="padding-right: 30px;" class="form-group col-5 col-md-3">
                            <label for="start_time">Hora Inicial:</label>
                            <input type="time" step="1" class="form-control {{ $errors->has('start_date') ? 'is-invalid' : '' }}" name="start_time" id="start_time" value="{{old('start_date')?substr(old('start_date'),11):'00:00:00'}}">
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
                    
                        <div style="padding-right: 30px;" class="form-group col-5 col-md-3">
                            <label for="end_time">Hora Final:</label>
                            <input type="time" step="1" class="form-control {{ $errors->has('end_date') ? 'is-invalid' : '' }}" name="end_time" id="end_time" value="{{old('end_date')?substr(old('end_date'),11):'23:59:59'}}">
                            @if ($errors->has('end_date'))
                                <div class="invalid-feedback">
                                    @lang($errors->first('end_date').'-end_date')
                                </div>
                            @endif
                        </div>
                    
                    </div>

                    <div class="form-group col-12">
                        <div class="d-flex bd-highlight">
                            <div class="mr-auto p-2 bd-highlight">
                                <label for="extensions">Ramal / Ramais</label>
                            </div>
                            <div class="p-2 bd-highlight">
                                <div class="form-check form-check-inline">
                                    <div class="icheck-primary">
                                        <input  id="checkbox" type="checkbox" >
                                        <label for="checkbox">Selecionar Todos</label>
                                    </div>
                                </div>    
					        </div>
					    </div>
                    
                        <select class="form-control {{ $errors->has('extensions') ? 'is-invalid' : '' }} select-extensions" id="extensions" name="extensions[]" multiple="multiple" data-width="100%" >
                            <option></option>
                            @forelse($extensions as $pbx => $extension)        
                                <optgroup label="{{$pbx}}">     
                                           
                                    @foreach($extension as $exten)
                                        <option value="{{ $exten->extension}}" 
                                            @if (old("extensions"))
                                                {{ (in_array($exten->extension, old("extensions")) ? "selected":"")}}
                                            @endif
                                        >
                                            {{ $exten->extension}} - {{$exten->ename}}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @empty  
                            @endforelse
                        </select>
                        @if ($errors->has('extensions'))
                            <div class="invalid-feedback">
                                @lang($errors->first('extensions').'-extensions')
                            </div>
                        @endif
                    </div>

                    <div class="form-group col-9">
                        <label for="dialNumber">Numero Discado:</label>
                        <input type="text" class="form-control {{ $errors->has('dialNumber') ? 'is-invalid' : '' }}" id="dialNumber" name="dialNumber" value="{{ old('dialNumber') ?? ''}}" placeholder="Numero Discado">
                        @if ($errors->has('dialNumber'))
                            <div class="invalid-feedback">
                                @lang($errors->first('dialNumber').'-dialNumber')
                            </div>
                        @endif
                    </div>

                        
                    <div class="form-group">
                        <hr>
                        <label for="types">Direção da Chamada:</label>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-3 col-md-2">
                            <div class="form-check form-check-inline ">
                                
                                <div class="icheck-success ">
                                    <input  id="OC" type="checkbox" 
                                        @if (old("directions")) 
                                            {{ ( in_array( "OC", old( "directions" ) ) ? "checked":"" )}} 
                                        @else 
                                            checked 
                                        @endif 
                                    value="OC" name="directions[]">
                                    <label for="OC">Saida</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-3 col-md-2">
                            <div class="form-check form-check-inline">
                                <div class="icheck-success">
                                    <input  id="IC" type="checkbox"
                                        @if (old("directions")) 
                                            {{ ( in_array( "IC", old( "directions" ) ) ? "checked":"" )}} 
                                        @else 
                                            checked 
                                        @endif 
                                     value="IC" name="directions[]">
                                    <label for="IC">Entrada</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-3 col-md-2">
                            <div class="form-check form-check-inline">
                                <div class="icheck-success">
                                    <input  id="IN" type="checkbox"
                                        @if (old("directions")) 
                                            {{ ( in_array( "IN", old( "directions" ) ) ? "checked":"" )}} 
                                        @else 
                                            checked 
                                        @endif 
                                     value="IN" name="directions[]">
                                    <label for="IN">Interno</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-3 col-md-2">
                            <div class="form-check form-check-inline">
                                <div class="icheck-success">
                                    <input  id="TL" type="checkbox" 
                                        @if (old("directions")) 
                                            {{ ( in_array( "TL", old( "directions" ) ) ? "checked":"" )}} 
                                        @else 
                                            checked 
                                        @endif 
                                    value="TL" name="directions[]">
                                    <label for="TL">Tie_Line</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <hr>
                        <label for="types">Tipo da Chamada:</label>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-3 col-md-2">
                            <div class="form-check form-check-inline ">
                                
                                <div class="icheck-warning">
                                    <input  id="LDI" type="checkbox" 
                                        @if (old("types")) 
                                            {{ ( in_array( "LDI", old( "types" ) ) ? "checked":"" )}} 
                                        @else 
                                            checked 
                                        @endif 
                                    value="LDI" name="types[]">
                                    <label for="LDI">DDI</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-3 col-md-2">
                            <div class="form-check form-check-inline">
                                <div class="icheck-warning">
                                    <input  id="VC2,VC3" type="checkbox" 
                                        @if (old("types")) 
                                            {{ ( in_array( "VC3", old( "types" ) ) ? "checked":"" )}} 
                                        @else 
                                            checked 
                                        @endif
                                    value="VC2,VC3" name="types[]">
                                    <label for="VC2,VC3">DDD_Movel</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-3 col-md-2">
                            <div class="form-check form-check-inline">
                                <div class="icheck-warning">
                                    <input  id="LDN" type="checkbox" 
                                        @if (old("types")) 
                                            {{ ( in_array( "LDN", old( "types" ) ) ? "checked":"" )}} 
                                        @else 
                                            checked 
                                        @endif
                                    value="LDN" name="types[]">
                                    <label for="LDN">DDD_Fixo</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-3 col-md-2">
                            <div class="form-check form-check-inline">
                                <div class="icheck-warning">
                                    <input  id="SERVIÇOS" type="checkbox" 
                                        @if (old("types")) 
                                            {{ ( in_array( "SERVIÇOS", old( "types" ) ) ? "checked":"" )}} 
                                        @else 
                                            checked 
                                        @endif
                                    value="SERVIÇOS" name="types[]">
                                    <label for="SERVIÇOS">Serviços</label>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="form-row">
                        
                        <div class="form-group col-3 col-md-2">
                            <div class="form-check form-check-inline">
                                <div class="icheck-warning">
                                    <input  id="OUTROS" type="checkbox" 
                                        @if (old("types")) 
                                            {{ ( in_array( "OUTROS", old( "types" ) ) ? "checked":"" )}} 
                                        @else 
                                            checked 
                                        @endif
                                    value="OUTROS" name="types[]">
                                    <label for="OUTROS">Outros</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-3 col-md-2">
                            <div class="form-check form-check-inline">
                                <div class="icheck-warning">
                                    <input  id="VC1" type="checkbox" 
                                        @if (old("types")) 
                                            {{ ( in_array( "VC1", old( "types" ) ) ? "checked":"" )}} 
                                        @else 
                                            checked 
                                        @endif
                                    value="VC1" name="types[]">
                                    <label for="VC1">Local_Movel</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-3 col-md-2">
                            <div class="form-check form-check-inline">
                                <div class="icheck-warning">
                                    <input  id="LOCAL" type="checkbox" 
                                        @if (old("types")) 
                                            {{ ( in_array( "LOCAL", old( "types" ) ) ? "checked":"" )}} 
                                        @else 
                                            checked 
                                        @endif
                                    value="LOCAL" name="types[]">
                                    <label for="LOCAL">Local_Fixo</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group col-3 col-md-2">
                            <div class="form-check form-check-inline">
                                <div class="icheck-warning">
                                    <input  id="GRATUITO" type="checkbox" 
                                        @if (old("types")) 
                                            {{ ( in_array( "GRATUITO", old( "types" ) ) ? "checked":"" )}} 
                                        @else 
                                            checked 
                                        @endif
                                    value="GRATUITO" name="types[]">
                                    <label for="GRATUITO">Gratuito</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer clearfix">
                    <div class="d-flex bd-highlight">
                        <div class="mr-auto p-2 bd-highlight">
                        </div>
                        <div class="p-2 bd-highlight">
                                <button type="submit" class="btn btn-primary">Gerar Relatorio</button>
                            </form>

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
        $('.select-extensions').select2({
            placeholder: "Selecione Ramais"
        })

    });

    $(document).ready(function(){
    
        $("#delete :input").prop("disabled", true);
        $("#show :input").prop("disabled", true);
    });

    $("#checkbox").click(function(){
        if($("#checkbox").is(':checked') ){
            $("#extensions > optgroup > option").prop("selected","selected");
            $("#extensions").trigger("change");
        }else{
            $("#extensions > optgroup > option").prop("selected","");
            $("#extensions").trigger("change");
        }
    });

</script>
@stop
@section('css')
<link rel="stylesheet" href="{{ asset('vendor/css/icheck/icheck-bootstrap.min.css') }}">
@stop