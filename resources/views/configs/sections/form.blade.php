

    <div class="form-row">
        <div class="col-12">
            <div class="input-group p-2">
                <div class="input-group-prepend">
                    <span class="input-group-text pr-2">Seção</span>
                </div>
                <input type="text" class="form-control {{ $errors->has('section') ? 'is-invalid' : '' }}" id="section" name="section" value="{{old('section') ?? $section->section ?? ''}}" placeholder="Seção"  >
                @if ($errors->has('section'))
                    <div class="invalid-feedback">
                        @lang($errors->first('section').'-section')
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="input-group p-2">
        <div class="input-group-prepend">
            <span class="input-group-text pr-3"><i class="fas fa-building"></i></span>
        </div>
        
        <select class="form-control {{ $errors->has('tenants_id') ? 'is-invalid' : '' }} select-tenants_id" id="tenants_id" name="tenants_id" value="{{old('tenants_id') ?? $section->tenants_id ?? ''}}" >
            <option></option>
            @foreach ($tenants as $tenant)
                @if(old('tenants_id') == $tenant->tenant || ($section->tenants_id ?? '') == $tenant->tenant)
                        <option value="{{$tenant->tenant}}" selected>{{$tenant->tenant}}</option>
                @else
                    <option value="{{$tenant->tenant}}">{{$tenant->tenant}}</option>
                @endif
             
            @endforeach
        </select>      
        @if ($errors->has('tenants_id'))
            <div class="invalid-feedback">
                @lang($errors->first('tenants_id').'-tenants_id')
            </div>
        @endif
    </div>
    
@section('js')
<script>
 
  $(function () {
        $('.select-tenants_id').select2({
            placeholder: "Empresa",
            allowClear: true
        })
    });

    $(document).ready(function(){
    
        $("#delete :input").prop("disabled", true);
        $("#show :input").prop("disabled", true);
    });

</script>
@stop
