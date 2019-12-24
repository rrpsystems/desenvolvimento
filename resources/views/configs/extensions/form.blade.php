

<div class="form-row">
        <div class="col-7">
            <div class="input-group p-2">
                <div class="input-group-prepend">
                    <span class="input-group-text pr-2">Tronco</span>
                </div>
                <input type="text" class="form-control {{ $errors->has('trunk') ? 'is-invalid' : '' }}" id="trunk" name="trunk" value="{{old('trunk') ?? $trunk->trunk ?? ''}}" placeholder="tronco"  >
                @if ($errors->has('trunk'))
                    <div class="invalid-feedback">
                        @lang($errors->first('trunk').'-trunk')
                    </div>
                @endif
            </div>
        </div>
    </div>


    <div class="input-group p-2">
        <div class="input-group-prepend">
            <span class="input-group-text pr-3"><i class="fas fa-tag"></i></span>
        </div>
        <input type="text" class="form-control {{ $errors->has('tname') ? 'is-invalid' : '' }}" id="tname" name="tname" value="{{old('tname') ?? $trunk->tname ?? ''}}" placeholder="Descrição Tronco" >
        @if ($errors->has('tname'))
            <div class="invalid-feedback" >
                @lang($errors->first('tname').'-tname')
            </div>
        @endif
    </div>

    <div class="input-group p-2">
        <div class="input-group-prepend">
            <span class="input-group-text pr-3"><i class="fas fa-building"></i></i></span>
        </div>
        
        <select class="form-control {{ $errors->has('tpbx') ? 'is-invalid' : '' }} select-tpbx" id="tpbx" name="tpbx" value="{{old('tpbx') ?? $trunk->tpbx ?? ''}}" >
            <option></option>
            @foreach ($pbxes as $pbx)
                @if(old('tpbx') == $pbx->name || ($trunk->tpbx ?? '') == $pbx->name)
                        <option value="{{$pbx->name}}" selected>{{$pbx->name}}</option>
                @else
                    <option value="{{$pbx->name}}">{{$pbx->name}}</option>
                @endif
             
            @endforeach
        </select>      
        @if ($errors->has('tpbx'))
            <div class="invalid-feedback">
                @lang($errors->first('tpbx').'-tpbx')
            </div>
        @endif
    </div>
    
    <div class="input-group p-2">
        <div class="input-group-prepend">
            <span class="input-group-text pr-3"><i class="fas fa-building"></i></i></span>
        </div>
        
        <select class="form-control {{ $errors->has('routes_route') ? 'is-invalid' : '' }} select-routes_route" id="routes_route" name="routes_route" value="{{old('routes_route') ?? $trunk->routes_route ?? ''}}" >
            <option></option>
            @foreach ($routes as $route)
                @if(old('routes_route') == $route->route || ($trunk->routes_route ?? '') == $route->route)
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

    
    
    
    
@section('js')
<script>
 
  $(function () {
        $('.select-tpbx').select2({
            placeholder: "PBX"
        }),
        $('.select-routes_route').select2({
            placeholder: "Rota"
        })
    });

    $(document).ready(function(){
    
        $("#delete :input").prop("disabled", true);
        $("#show :input").prop("disabled", true);
    });

</script>
@stop
