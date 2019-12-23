
    <div class="input-group pt-3">
        <div class="input-group-prepend">
            <span class="input-group-text pr-3"><i class="fas fa-tag"></i></span>
        </div>
        <input type="text" class="form-control {{ $errors->has('rname') ? 'is-invalid' : '' }}" id="rname" name="rname" value="{{old('rname') ?? $rate->rname ?? ''}}" placeholder="Nome Tarifa" >
        @if ($errors->has('rname'))
            <div class="invalid-feedback" >
                @lang($errors->first('rname').'-rname')
            </div>
        @endif
    </div>

    <div class="form row">
    <div class="input-group pt-3 col-6">
        <div class="input-group-prepend">
            <span class="input-group-text pr-3"><i class="fas fa-building"></i></i></span>
        </div>
        
        <select class="form-control {{ $errors->has('routes_route') ? 'is-invalid' : '' }} select-routes_route" id="routes_route" name="routes_route" value="{{old('routes_route') ?? $rate->routes_route ?? ''}}" >
            <option></option>
            @foreach ($routes as $route)
                @if(old('routes_route') == $route->route || ($rate->routes_route ?? '') == $route->route)
                        <option value="{{$route->route}}" selected>{{$route->route}}</option>
                @else
                    <option value="{{$route->route}}">{{$route->route}}</option>
                @endif
             
            @endforeach
        </select>      
        @if ($errors->has('routes_route'))
            <div class="invalid-feedback">
                @lang($errors->first('routes_route').'-routes_route')
            </div>
        @endif
    </div>
    
    <div class="input-group pt-3 col-6">
        <div class="input-group-prepend">
            <span class="input-group-text pr-3"><i class="fas fa-building"></i></i></span>
        </div>
        
        <select class="form-control {{ $errors->has('prefixes_service') ? 'is-invalid' : '' }} select-prefixes_service" id="prefixes_service" name="prefixes_service" value="{{old('prefixes_service') ?? $rate->prefixes_service ?? ''}}" >
            <option></option>
            @foreach ($services as $service)
                @if(old('prefixes_service') == $service || ($rate->prefixes_service ?? '') == $service)
                        <option value="{{$service}}" selected>{{$service}}</option>
                @else
                    <option value="{{$service}}">{{$service}}</option>
                @endif
             
            @endforeach
        </select>      
        @if ($errors->has('prefixes_service'))
            <div class="invalid-feedback">
                @lang($errors->first('prefixes_service').'-prefixes_service')
            </div>
        @endif
    </div>
    </div>

    <div class="row">
    <div class="input-group pt-3 col-6">
        <div class="input-group-prepend">
            <span class="input-group-text pr-3"><i class="fas fa-building"></i></i></span>
        </div>
        
        <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }} select-type" id="type" name="type" value="{{old('type') ?? $rate->type ?? ''}}" >
            <option></option>
            @foreach ($types as $type)
                @if(old('type') == $type || ($rate->type ?? '') == $type)
                        <option value="{{$type}}" selected>{{$type}}</option>
                @else
                    <option value="{{$type}}">{{$type}}</option>
                @endif
             
            @endforeach
        </select>      
        @if ($errors->has('type'))
            <div class="invalid-feedback">
                @lang($errors->first('type').'-type')
            </div>
        @endif
    </div>
    
    <div class="input-group pt-3 col-6">
        <div class="input-group-prepend">
            <span class="input-group-text pr-3"><i class="fas fa-building"></i></i></span>
        </div>
        
        <select class="form-control {{ $errors->has('direction') ? 'is-invalid' : '' }} select-direction" id="direction" name="direction" value="{{old('direction') ?? $rate->direction ?? ''}}" >
            <option></option>
            @foreach ($directions as $direction)
                @if(old('direction') == $direction || ($rate->direction ?? '') == $direction)
                        <option value="{{$direction}}" selected>@lang("calls.$direction")</option>
                @else
                    <option value="{{$direction}}">@lang("calls.$direction")</option>
                @endif
             
            @endforeach
        </select>      
        @if ($errors->has('direction'))
            <div class="invalid-feedback">
                @lang($errors->first('direction').'-direction')
            </div>
        @endif
    </div>
    </div>
    

    <div class="form-row">
        <div class="col-6">
            <div class="input-group pt-3">
                <div class="input-group-prepend">
                    <span class="input-group-text pr-3">Tarifa R$</span>
                </div>
                <input type="text" class="form-control {{ $errors->has('rate') ? 'is-invalid' : '' }}" id="rate" name="rate" value="{{old('rate') ?? $rate->rate ?? ''}}" placeholder="0.000" >
                @if ($errors->has('rate'))
                   <div class="invalid-feedback">
                        @lang($errors->first('rate').'-rate')
                    </div>
                @endif
            </div>
        </div>

        <div class="col-6">
            <div class="input-group pt-3">
                <div class="input-group-prepend">
                    <span class="input-group-text pr-3">Conexão R$</span>
                </div>
                <input type="text" class="form-control {{ $errors->has('connection') ? 'is-invalid' : '' }}" id="connection" name="connection" value="{{old('connection') ?? $rate->connection ?? '0'}}" placeholder="0.000" >
                @if ($errors->has('connection'))
                    <div class="invalid-feedback">
                        @lang($errors->first('connection').'-connection')
                    </div>
                @endif

            </div>
        </div>       
    </div>

    <hr>

    <div class="form-row">
        <div class="col-6">
            <div class="input-group pt-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">S Time</span>
                </div>
                <input type="text" class="form-control {{ $errors->has('stime') ? 'is-invalid' : '' }}" id="stime" name="stime" value="{{old('stime') ?? $stime->stime ?? '3'}}" placeholder="0.000" >
                <div class="input-group-append">
                    <span class="input-group-text">Seg.</span>
                </div>
                @if ($errors->has('stime'))
                   <div class="invalid-feedback">
                        @lang($errors->first('stime').'-stime')
                    </div>
                @endif
            </div>
        </div>

        <div class="col-6">
            <div class="input-group pt-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">T Min</span>
                </div>
                <input type="text" class="form-control {{ $errors->has('ttmin') ? 'is-invalid' : '' }}" id="ttmin" name="ttmin" value="{{old('ttmin') ?? $rate->ttmin ?? '30'}}" placeholder="0.000" >
                <div class="input-group-append">
                    <span class="input-group-text">Seg.</span>
                </div>
                @if ($errors->has('ttmin'))
                    <div class="invalid-feedback">
                        @lang($errors->first('ttmin').'-ttmin')
                    </div>
                @endif

            </div>
        </div>       
    </div>
    <div class="form-row">
        <div class="col-6">
            <div class="input-group pt-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Increm</span>
                </div>
                <input type="text" class="form-control {{ $errors->has('increment') ? 'is-invalid' : '' }}" id="increment" name="increment" value="{{old('increment') ?? $rate->increment ?? '6'}}" placeholder="0.000" >
                <div class="input-group-append">
                    <span class="input-group-text">Seg.</span>
                </div>
                @if ($errors->has('increment'))
                    <div class="invalid-feedback">
                        @lang($errors->first('increment').'-increment')
                    </div>
                @endif

            </div>
        </div>       
    </div>
  

    
    
@section('js')
<script>
 
  $(function () {
        $('.select-routes_route').select2({
            placeholder: "Rota"
        }),
        $('.select-prefixes_service').select2({
            placeholder: "Prefixo Serviço"
        }),
        $('.select-type').select2({
            placeholder: "Tipo de Serviço"
        }),
        $('.select-direction').select2({
            placeholder: "Direção da chamada"
        })
    });

    $(document).ready(function(){
    
        $("#delete :input").prop("disabled", true);
        $("#show :input").prop("disabled", true);
    });

</script>
@stop
