
    <div class="input-group p-2">
        <div class="input-group-prepend">
            <span class="input-group-text pr-3"><i class="fas fa-tag"></i></span>
        </div>
        <input type="text" class="form-control {{ $errors->has('route') ? 'is-invalid' : '' }}" id="route" name="route" value="{{old('route') ?? $route->route ?? ''}}" placeholder="Nome da Rota" >
        @if ($errors->has('route'))
            <div class="invalid-feedback" >
                @lang($errors->first('route').'-route')
            </div>
        @endif
    </div>

    <div class="input-group p-2">
        <div class="input-group-prepend">
            <span class="input-group-text pr-3"><i class="fas fa-building"></i></i></span>
        </div>
        
        <select class="form-control {{ $errors->has('rpbx') ? 'is-invalid' : '' }} select-rpbx" id="rpbx" name="rpbx" value="{{old('rpbx') ?? $route->rpbx ?? ''}}" >
            <option></option>
            @foreach ($pbxes as $pbx)
                @if(old('rpbx') == $pbx->name || ($route->rpbx ?? '') == $pbx->name)
                        <option value="{{$pbx->name}}" selected>{{$pbx->name}}</option>
                @else
                    <option value="{{$pbx->name}}">{{$pbx->name}}</option>
                @endif
             
            @endforeach
        </select>      
        @if ($errors->has('rpbx'))
            <div class="invalid-feedback">
                @lang($errors->first('rpbx').'-pbx')
            </div>
        @endif
    </div>
    
    <div class="form-row">
        <div class="col-6">
            <div class="input-group p-2">
                <div class="input-group-prepend">
                    <span class="input-group-text pr-2">DDD</span>
                </div>
                <input type="text" class="form-control {{ $errors->has('ddd') ? 'is-invalid' : '' }}" id="ddd" name="ddd" value="{{old('ddd') ?? $route->ddd ?? ''}}" placeholder="Ex. 55XX"  >
                @if ($errors->has('ddd'))
                    <div class="invalid-feedback">
                        @lang($errors->first('ddd').'-ddd')
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="form-row">
        <div class="col-6">
            <div class="input-group p-2">
                <div class="input-group-prepend">
                    <span class="input-group-text pr-3">-</span>
                </div>
                <input type="text" class="form-control {{ $errors->has('drm') ? 'is-invalid' : '' }}" id="drm" name="drm" value="{{old('drm') ?? $route->drm ?? ''}}" placeholder="Removido"  >
                @if ($errors->has('drm'))
                    <div class="invalid-feedback">
                        @lang($errors->first('drm').'-drm')
                    </div>
                @endif

            </div>
        </div>

        <div class="col-6">
            <div class="input-group p-2">
                <div class="input-group-prepend">
                    <span class="input-group-text pr-3">+</i></span>
                </div>
                <input type="text" class="form-control {{ $errors->has('dap') ? 'is-invalid' : '' }}" id="dap" name="dap" value="{{old('dap') ?? $trunk->dap ?? ''}}" placeholder="Adicionado" >
                @if ($errors->has('dap'))
                    <div class="invalid-feedback">
                        @lang($errors->first('dap').'-dap')
                    </div>
                @endif

            </div>
        </div>       
    </div>

<hr>
    <small id="docHelpBlock" class="form-text text-muted pl-2">
        Ligações de Saida:
    </small>

    <div class="input-group p-2">
        <div class="input-group-prepend">
            <span class="input-group-text pr-2"><i class="fas fa-building"></i></i></span>
        </div>
        
        <select class="form-control {{ $errors->has('dialplan') ? 'is-invalid' : '' }} select-dialplan" id="dialplan" name="dialplan" value="{{old('dialplan') ?? $route->dialplan ?? ''}}" >
            <option></option>
            @foreach ($dials as $dial)
                @if(old('dialplan') == $dial || ($route->dialplan ?? '') == $dial)
                        <option value="{{$dial}}" selected>{{$dial}}</option>
                @else
                    <option value="{{$dial}}">{{$dial}}</option>
                @endif
             
            @endforeach
        </select>      
        @if ($errors->has('dialplan'))
            <div class="invalid-feedback">
                @lang($errors->first('dialplan').'-dialplan')
            </div>
        @endif
    </div>



@section('js')
<script>
 
  $(function () {
        $('.select-rpbx').select2({
            placeholder: "PBX"
        }),
        $('.select-dialplan').select2({
            placeholder: "Discagem Saida"
        })
    });

    $(document).ready(function(){
    
        $("#delete :input").prop("disabled", true);
        $("#show :input").prop("disabled", true);
    });

</script>
@stop
