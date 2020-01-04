

<div class="form-row">
        <div class="col-12">
            <div class="input-group p-2">
                <div class="input-group-prepend">
                    <span class="input-group-text pr-2">Grupo</span>
                </div>
                <input type="text" class="form-control {{ $errors->has('group') ? 'is-invalid' : '' }}" id="group" name="group" value="{{old('group') ?? $group->group ?? ''}}" placeholder="Grupo"  >
                @if ($errors->has('group'))
                    <div class="invalid-feedback">
                        @lang($errors->first('group').'-group')
                    </div>
                @endif
            </div>
        </div>
    </div>
  

@section('js')
<script>
 
    $(document).ready(function(){
    
        $("#delete :input").prop("disabled", true);
        $("#show :input").prop("disabled", true);
    });

</script>
@stop
