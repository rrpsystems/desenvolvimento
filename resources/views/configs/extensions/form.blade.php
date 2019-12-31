
    <div class="form row">
    
        <div class="input-group pt-3 col-6">
            <div class="input-group-prepend">
                <span class="input-group-text pr-3">Ramal</span>
            </div>
            <input type="text" class="form-control {{ $errors->has('extension') ? 'is-invalid' : '' }}" id="extension" name="extension" value="{{old('extension') ?? $extension->extension ?? ''}}" placeholder="Ramal" >
            @if ($errors->has('extension'))
                <div class="invalid-feedback">
                    @lang($errors->first('extension').'-extension')
                </div>
            @endif
        </div>
    
        <div class="input-group pt-3 col-6">
            <div class="input-group-prepend">
                <span class="input-group-text pr-3">PBX</span>
            </div>
        
            <select class="form-control  {{ $errors->has('pbxes_id') ? 'is-invalid' : '' }} select-pbxes_id" id="pbxes_id" name="pbxes_id" value="{{old('pbxes_id') ?? $extension->pbxes_id ?? ''}}" >
                <option></option>
                @foreach ($pbxes as $pbx)
                    @if(old('pbxes_id') == $pbx->name || ($extension->pbxes_id ?? '') == $pbx->name)
                        <option value="{{$pbx->name}}" selected>{{$pbx->name}}</option>
                    @else
                        <option value="{{$pbx->name}}">{{$pbx->name}}</option>
                    @endif
             
                @endforeach
            </select>      
            @if ($errors->has('pbxes_id'))
                <div class="invalid-feedback">
                    @lang($errors->first('pbxes_id').'-pbxes_id')
                </div>
            @endif
        </div>    

    </div>

    <div class="input-group pt-3">
        <div class="input-group-prepend">
            <span class="input-group-text pr-3">Nome</span>
        </div>
        <input type="text" class="form-control {{ $errors->has('ename') ? 'is-invalid' : '' }}" id="ename" name="ename" value="{{old('ename') ?? $extension->ename ?? ''}}" placeholder="Usuario do Ramal" >
        @if ($errors->has('ename'))
            <div class="invalid-feedback" >
                @lang($errors->first('ename').'-ename')
            </div>
        @endif
    </div>


    <div class="form row">
        <div class="input-group pt-3 col-12">
            <div class="input-group-prepend">
                <span class="input-group-text pr-3">Grupo</span>
            </div>
        
            <select class="form-control {{ $errors->has('groups_id') ? 'is-invalid' : '' }} select-groups_id" id="groups_id" name="groups_id" value="{{old('groups_id') ?? $extension->groups_id ?? ''}}" >
                <option></option>
                @foreach ($pbxes as $pbx)
                    @if(old('groups_id') == $pbx || ($extension->groups_id ?? '') == $pbx)
                        <option value="{{$pbx->name}}" selected> {{$pbx->name}} </option>
                    @else
                        <option value="{{$pbx}}"> {{$pbx->name}} </option>
                    @endif
             
                @endforeach
            </select>      
            @if ($errors->has('groups_id'))
                <div class="invalid-feedback">
                    @lang($errors->first('groups_id').'-groups_id')
                </div>
            @endif
        </div>
    </div>

    <div class="form row">
        <div class="input-group pt-3 col-12">
            <div class="input-group-prepend">
                <span class="input-group-text pr-3">Depto.</span>
            </div>
        
            <select class="form-control {{ $errors->has('departaments_id') ? 'is-invalid' : '' }} select-departaments_id" id="departaments_id" name="departaments_id" value="{{old('departaments_id') ?? $extension->departaments_id ?? ''}}" >
                <option></option>
                @foreach ($pbxes as $pbx)
                    @if(old('departaments_id') == $pbx->name || ($extension->departaments_id ?? '') == $pbx->name)
                        <option value="{{$pbx->name}}" selected>{{$pbx->name}}</option>
                    @else
                        <option value="{{$pbx->name}}">{{$pbx->name}}</option>
                    @endif
             
                @endforeach
            </select>      
            @if ($errors->has('departaments_id'))
                <div class="invalid-feedback">
                    @lang($errors->first('departaments_id').'-departaments_id')
                </div>
            @endif
        </div>
    </div>

    <hr>

    <div class="form-row">
        <div class="input-group pt-3 col-12">
            <div class="input-group-prepend">
                <span class="input-group-text pr-3">Login: </span>
            </div>
        
            <select class="form-control {{ $errors->has('users_id') ? 'is-invalid' : '' }} select-users_id" id="users_id" name="users_id" value="{{old('users_id') ?? $extension->users_id ?? ''}}" >
                <option></option>
                @foreach ($users as $user)
                    @if(old('users_id') == $user || ($extension->users_id ?? '') == $user->email)
                        <option value="{{$user->email}}" selected>{{$user->email}}</option>
                    @else
                        <option value="{{$user->email}}">{{$user->email}}</option>
                    @endif
             
                @endforeach
            </select>      
            @if ($errors->has('users_id'))
                <div class="invalid-feedback">
                    @lang($errors->first('users_id').'-users_id')
                </div>
            @endif
        </div>           
    </div>
  

    
    
@section('js')
<script>
 
  $(function () {
        $('.select-pbxes_id').select2({
            placeholder: "PBX"
        }),
        $('.select-groups_id').select2({
            placeholder: "Grupo de Ramais",
            allowClear: true
        }),
        $('.select-departaments_id').select2({
            placeholder: "Departamento dos Ramais",
            allowClear: true
        }),
        $('.select-users_id').select2({
            placeholder: "Login do Usuario",
            allowClear: true
        })
    });

    $(document).ready(function(){
    
        $("#delete :input").prop("disabled", true);
        $("#show :input").prop("disabled", true);
    });

</script>
@stop
