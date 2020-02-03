

    <div class="form-row">
        <div class="col-12">
            <div class="input-group p-2">
                <div class="input-group-prepend">
                    <span class="input-group-text pr-2"><i class="fas fa-blender-phone"></i></span>
                </div>
                <input type="text" class="form-control {{ $errors->has('phonenumber') ? 'is-invalid' : '' }}" id="phonenumber" name="phonenumber" value="{{old('phonenumber') ?? $phonebook->phonenumber ?? ''}}" placeholder="Telefone Ex. 55XX"  >
                @if ($errors->has('phonenumber'))
                    <div class="invalid-feedback">
                        @lang($errors->first('phonenumber').'-phonenumber')
                    </div>
                @endif
            </div>
        </div>
    </div>

    
    <div class="form-row">
        <div class="col-12">
            <div class="input-group p-2">
                <div class="input-group-prepend">
                    <span class="input-group-text pr-2"><i class="fas fa-file-signature"></i></span>
                </div>
                <input type="text" class="form-control {{ $errors->has('phonename') ? 'is-invalid' : '' }}" id="phonename" name="phonename" value="{{old('phonename') ?? $phonebook->phonename ?? ''}}" placeholder="Descrição"  >
                @if ($errors->has('phonename'))
                    <div class="invalid-feedback">
                        @lang($errors->first('phonename').'-phonename')
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
    