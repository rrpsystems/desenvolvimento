

    <div class="form-row">
        <div class="col-12">
            <div class="input-group p-2">
                <div class="input-group-prepend">
                    <span class="input-group-text pr-2">Depto.</span>
                </div>
                <input type="text" class="form-control {{ $errors->has('departament') ? 'is-invalid' : '' }}" id="departament" name="departament" value="{{old('departament') ?? $departament->departament ?? ''}}" placeholder="Departamento"  >
                @if ($errors->has('departament'))
                    <div class="invalid-feedback">
                        @lang($errors->first('departament').'-departament')
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="input-group p-2">
        <div class="input-group-prepend">
            <span class="input-group-text pr-3"><i class="fas fa-building"></i></span>
        </div>
        
        <select class="form-control {{ $errors->has('sections_id') ? 'is-invalid' : '' }} select-sections_id" id="sections_id" name="sections_id" value="{{old('sections_id') ?? $departament->sections_id ?? ''}}" >
            <option></option>
            @foreach ($sections as $section)
                @if(old('sections_id') == $section->section || ($departament->sections_id ?? '') == $section->section)
                        <option value="{{$section->section}}" selected>{{$section->section}}</option>
                @else
                    <option value="{{$section->section}}">{{$section->section}}</option>
                @endif
             
            @endforeach
        </select>      
        @if ($errors->has('sections_id'))
            <div class="invalid-feedback">
                @lang($errors->first('sections_id').'-sections_id')
            </div>
        @endif
    </div>
    
@section('js')
<script>
 
  $(function () {
        $('.select-sections_id').select2({
            placeholder: "Seção",
            allowClear: true
        })
    });

    $(document).ready(function(){
    
        $("#delete :input").prop("disabled", true);
        $("#show :input").prop("disabled", true);
    });

</script>
@stop
