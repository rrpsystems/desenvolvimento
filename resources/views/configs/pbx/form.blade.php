
<label for="description">Login de Acesso:</label>
<br>

    <div class="input-group p-2">
        <div class="input-group-prepend">
            <span class="input-group-text pr-3"><i class="fas fa-tag"></i></span>
        </div>
        <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" id="name" name="name" value="{{old('name') ?? $pbx->name ?? ''}}" placeholder="Descrição PBX" >
        @if ($errors->has('name'))
            <div class="invalid-feedback" >
                @lang($errors->first('name').'-name')
            </div>
        @endif
    </div>

    <div class="input-group p-2">
        <div class="input-group-prepend">
            <span class="input-group-text pr-3"><i class="fas fa-building"></i></i></span>
        </div>
        
        <select class="form-control {{ $errors->has('model') ? 'is-invalid' : '' }} select-model" id="model" name="model" value="{{old('model') ?? $pbx->model ?? ''}}" >
            <option></option>
            @foreach ($models as $model)
                @if(old('model') == $model || ($pbx->model ?? '') == $model)
                        <option value="{{$model}}" selected>{{$model}}</option>
                @else
                    <option value="{{$model}}">{{$model}}</option>
                @endif
             
            @endforeach
        </select>      
        @if ($errors->has('model'))
            <div class="invalid-feedback">
                @lang($errors->first('model').'-model')
            </div>
        @endif
    </div>
    
    <div class="input-group p-2">
        <div class="input-group-prepend">
            <span class="input-group-text pr-3"><i class="fas fa-link"></i></span>
        </div>
        <input type="host" class="form-control {{ $errors->has('host') ? 'is-invalid' : '' }}" id="host" name="host" value="{{old('host') ?? $pbx->host ?? ''}}" placeholder="Endereço do PBX">
        @if ($errors->has('host'))
            <div class="invalid-feedback" >
                @lang($errors->first('host').'-host')
            </div>
        @endif
    </div>
    <div class="form-row">
    <div class="col-6">
    <div class="input-group p-2">
        <div class="input-group-prepend">
            <span class="input-group-text pr-3"><i class="fas fa-door-open"></i></span>
        </div>
        <input type="port" class="form-control {{ $errors->has('port') ? 'is-invalid' : '' }}" id="port" name="port" value="{{old('port') ?? $pbx->port ?? ''}}" placeholder="Porta">
        @if ($errors->has('port'))
            <div class="invalid-feedback" >
                @lang($errors->first('port').'-port')
            </div>
        @endif
    </div>
    </div>
    
    <div class="col-6">
    <div class="input-group p-2">
        <div class="input-group-prepend">
            <span class="input-group-text pr-3"><i class="fas fa-code-branch"></i></span>
        </div>
        
        <select class="form-control {{ $errors->has('connection') ? 'is-invalid' : '' }} select-connection" id="connection" name="connection" value="{{old('connection') ?? $pbx->connection ?? ''}}" >
            <option></option>
            @foreach ($connections as $connection)
                @if(old('connection') == $connection || ($pbx->connection ?? '') == $connection)
                        <option value="{{$connection}}" selected>{{$connection}}</option>
                @else
                    <option value="{{$connection}}">{{$connection}}</option>
                @endif
             
            @endforeach
        </select>      
        @if ($errors->has('connection'))
            <div class="invalid-feedback">
                @lang($errors->first('connection').'-connection')
            </div>
        @endif
    </div>
    </div>
    </div>

    
    <div class="form-row">
    <div class="col-6">
        <div class="input-group p-2">
                <div class="input-group-prepend">
                        <span class="input-group-text pr-3"><i class="fas fa-user"></i></span>
                    </div>
                    <input type="text" class="form-control {{ $errors->has('user') ? 'is-invalid' : '' }}" id="user" name="user" value="{{old('user') ?? $pbx->user ?? ''}}" placeholder="Usuario"  >
                    @if ($errors->has('user'))
                <div class="invalid-feedback">
                    @lang($errors->first('user').'-user')
                </div>
            @endif

        </div>
        </div>
        <div class="col-6">
        <div class="input-group p-2">
            <div class="input-group-prepend">
                <span class="input-group-text pr-3"><i class="fas fa-lock"></i></span>
            </div>
            <input type="text" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" id="password" name="password" value="{{old('password') ?? $pbx->password ?? ''}}" placeholder="Senha" >
            @if ($errors->has('password'))
                <div class="invalid-feedback">
                    @lang($errors->first('password').'-password')
                </div>
            @endif

            </div>
        </div>       
    </div>

@section('js')
<script>
 
  $(function () {
        $('.select-model').select2({
            placeholder: "Modelo PBX"
        }),
        $('.select-connection').select2({
            placeholder: "Conexão"
        })
    });

    $(document).ready(function(){
    
        $("#delete :input").prop("disabled", true);
        $("#show :input").prop("disabled", true);
    });

</script>
@stop
