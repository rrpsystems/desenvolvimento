
<label for="description">Prefixo:</label>
<br>

    <div class="input-group p-2">
        <div class="input-group-prepend">
            <span class="input-group-text pr-3"><i class="fas fa-tag"></i></span>
        </div>
        <input type="text" class="form-control {{ $errors->has('prefix') ? 'is-invalid' : '' }}" id="prefix" name="prefix" value="{{old('prefix') ?? $prefix->prefix ?? ''}}" placeholder="Prefixo" >
        @if ($errors->has('prefix'))
            <div class="invalid-feedback" >
                @lang($errors->first('prefix').'-prefix')
            </div>
        @endif
    </div>

    <div class="input-group p-2">
        <div class="input-group-prepend">
            <span class="input-group-text pr-3"><i class="fas fa-link"></i></span>
        </div>
        <input type="locale" class="form-control {{ $errors->has('locale') ? 'is-invalid' : '' }}" id="locale" name="locale" value="{{old('locale') ?? $prefix->locale ?? ''}}" placeholder="Localidade">
        @if ($errors->has('locale'))
            <div class="invalid-feedback" >
                @lang($errors->first('locale').'-locale')
            </div>
        @endif
    </div>
    <div class="input-group p-2">
        <div class="input-group-prepend">
            <span class="input-group-text pr-3"><i class="fas fa-link"></i></span>
        </div>
        <input type="dadd" class="form-control {{ $errors->has('dadd') ? 'is-invalid' : '' }}" id="dadd" name="dadd" value="{{old('dadd') ?? $prefix->dadd ?? ''}}" placeholder="Digitos Adicionais">
        @if ($errors->has('dadd'))
            <div class="invalid-feedback" >
                @lang($errors->first('dadd').'-dadd')
            </div>
        @endif
    </div>
    <div class="input-group p-2">
        <div class="input-group-prepend">
            <span class="input-group-text pr-3"><i class="fas fa-building"></i></i></span>
        </div>
        
        <select class="form-control {{ $errors->has('service') ? 'is-invalid' : '' }} select-service" id="service" name="service" value="{{old('service') ?? $prefix->service ?? ''}}" >
            <option></option>
            @foreach ($services as $service)
                @if(old('service') == $service || ($prefix->service ?? '') == $service)
                        <option value="{{$service}}" selected>{{$service}}</option>
                @else
                    <option value="{{$service}}">{{$service}}</option>
                @endif
             
            @endforeach
        </select>      
        @if ($errors->has('service'))
            <div class="invalid-feedback">
                @lang($errors->first('service').'-service')
            </div>
        @endif
    </div>
    


@section('js')
<script>
 
  $(function () {
        $('.select-service').select2({
            placeholder: "Serviço"
        })
        
    });

    $(document).ready(function(){
    
        $("#delete :input").prop("disabled", true);
        $("#show :input").prop("disabled", true);
    });

</script>
@stop
