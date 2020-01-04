

<div class="form-row">
        <div class="col-12">
            <div class="input-group p-2">
                <div class="input-group-prepend">
                    <span class="input-group-text pr-2">Empresa</span>
                </div>
                <input type="text" class="form-control {{ $errors->has('tenant') ? 'is-invalid' : '' }}" id="tenant" name="tenant" value="{{old('tenant') ?? $tenant->tenant ?? ''}}" placeholder="Empresa"  >
                @if ($errors->has('tenant'))
                    <div class="invalid-feedback">
                        @lang($errors->first('tenant').'-tenant')
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
