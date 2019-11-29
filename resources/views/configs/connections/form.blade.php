
<label for="description">Login de Acesso:</label>
<br>

    <div class="input-group p-2">
        <div class="input-group-prepend">
            <span class="input-group-text pr-3"><i class="fas fa-building"></i></span>
        </div>
        <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" id="name" name="name" value="{{old('name') ?? $connection->name ?? ''}}" placeholder="PBX" >
        @if ($errors->has('name'))
            <div class="invalid-feedback" >
                @lang($errors->first('name').'-name')
            </div>
        @endif
    </div>

    <div class="input-group p-2">
        <div class="input-group-prepend">
            <span class="input-group-text pr-3"><i class="fas fa-link"></i></span>
        </div>
        <input type="host" class="form-control {{ $errors->has('host') ? 'is-invalid' : '' }}" id="host" name="host" value="{{old('host') ?? $connection->host ?? ''}}" placeholder="Endereço do PBX">
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
        <input type="port" class="form-control {{ $errors->has('port') ? 'is-invalid' : '' }}" id="port" name="port" value="{{old('port') ?? $connection->port ?? ''}}" placeholder="Porta">
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
        
        <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }} select2" id="type" name="type" value="{{old('type') ?? $connection->type ?? ''}}" >
            <option></option>
            @foreach ($types as $type)
                @if(old('type') == $type || ($connection->type ?? '') == $type)
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
    </div>
    </div>

    
    <div class="form-row">
    <div class="col-6">
        <div class="input-group p-2">
                <div class="input-group-prepend">
                        <span class="input-group-text pr-3"><i class="fas fa-user"></i></span>
                    </div>
                    <input type="text" class="form-control {{ $errors->has('user') ? 'is-invalid' : '' }}" id="user" name="user" value="{{old('user') ?? $connection->user ?? ''}}" placeholder="Usuario"  >
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
            <input type="text" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" id="password" name="password" value="{{old('password') ?? $connection->password ?? ''}}" placeholder="Senha" >
            @if ($errors->has('password_confirmation'))
                <div class="invalid-feedback">
                    @lang($errors->first('password').'-password')
                </div>
            @endif

        </div>
        </div>
       
    </div>

    <small id="intervalText" class="form-text text-muted">
        Intervalo Entre Coletas
        <output id="intervalOutputId">{{old('interval') ?? $connection->interval ?? '5'}}</output>
        Minutos
    </small>

        <div class="input-group p-2">
            <div class="input-group-prepend">
                <span class="input-group-text pr-3"><i class="fas fa-clock"></i></span>
            </div>
            <input type="range" class="form-control custom-range custom-range-teal" name="interval" id="intervalInputId" value="{{old('interval') ?? $connection->interval ?? '5'}}" min="5" max="90" step="5" oninput="intervalOutputId.value = intervalInputId.value" aria-describedby="intervalText" disabled>
            @if ($errors->has('interval'))
                <div class="invalid-feedback" >
                    @lang($errors->first('interval').'-interval')
                </div>
            @endif
        </div>



@section('js')
<script>
 
  $(function () {
        $('.select2').select2({
            placeholder: "Tipo de Conexão"
        })
    });

    $(document).ready(function(){
    
    $("#delete :input").prop("disabled", true);
});

</script>
@stop
