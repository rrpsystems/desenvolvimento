
    <div class="form row">
    
        <div class="input-group pt-3 col-6">
            <div class="input-group-prepend">
                <span class="input-group-text pr-3">Codigo</span>
            </div>
            <input type="text" class="form-control {{ $errors->has('accountcode') ? 'is-invalid' : '' }}" id="accountcode" name="accountcode" value="{{old('accountcode') ?? $accountcode->accountcode ?? ''}}" placeholder="Codigo de Conta" >
            @if ($errors->has('accountcode'))
                <div class="invalid-feedback">
                    @lang($errors->first('accountcode').'-accountcode')
                </div>
            @endif
        </div>
    
        <div class="input-group pt-3 col-6">
            <div class="input-group-prepend">
                <span class="input-group-text pr-3">PBX</span>
            </div>
        
            <select class="form-control  {{ $errors->has('pbxes_id') ? 'is-invalid' : '' }} select-pbxes_id" id="pbxes_id" name="pbxes_id" value="{{old('pbxes_id') ?? $accountcode->pbxes_id ?? ''}}" >
                <option></option>
                @foreach ($pbxes as $pbx)
                    @if(old('pbxes_id') == $pbx->name || ($accountcode->pbxes_id ?? '') == $pbx->name)
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
        <input type="text" class="form-control {{ $errors->has('aname') ? 'is-invalid' : '' }}" id="aname" name="aname" value="{{old('aname') ?? $accountcode->aname ?? ''}}" placeholder="Usuario do Codigo de Conta" >
        @if ($errors->has('aname'))
            <div class="invalid-feedback" >
                @lang($errors->first('aname').'-aname')
            </div>
        @endif
    </div>


    <div class="form row">
        <div class="input-group pt-3 col-12">
            <div class="input-group-prepend">
                <span class="input-group-text pr-3">Grupo</span>
            </div>
        
            <select class="form-control {{ $errors->has('groups_id') ? 'is-invalid' : '' }} select-groups_id" id="groups_id" name="groups_id" value="{{old('groups_id') ?? $accountcode->groups_id ?? ''}}" >
                <option></option>
                @foreach ($groups as $group)
                    @if(old('groups_id') == $group->group || ($accountcode->groups_id ?? '') == $group->group)
                        <option value="{{$group->group}}" selected> {{$group->group}} </option>
                    @else
                        <option value="{{$group->group}}"> {{$group->group}} </option>
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
        
            <select class="form-control {{ $errors->has('departaments_id') ? 'is-invalid' : '' }} select-departaments_id" id="departaments_id" name="departaments_id" value="{{old('departaments_id') ?? $accountcode->departaments_id ?? ''}}" >
                <option></option>
                @foreach ($departaments as $departament)
                    @if(old('departaments_id') == $departament->departament || ($accountcode->departaments_id ?? '') == $departament->departament)
                        <option value="{{$departament->departament}}" selected>{{$departament->departament}}</option>
                    @else
                        <option value="{{$departament->departament}}">{{$departament->departament}}</option>
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
        
            <select class="form-control {{ $errors->has('users_id') ? 'is-invalid' : '' }} select-users_id" id="users_id" name="users_id" value="{{old('users_id') ?? $accountcode->users_id ?? ''}}" >
                <option></option>
                @foreach ($users as $user)
                    @if(old('users_id') == $user || ($accountcode->users_id ?? '') == $user->email)
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
            placeholder: "PBX",
            allowClear: true,
            language: {
                noResults: function (params) {
                    return "Não Há PBX Cadastrado";
                }}
        }),

        $('.select-groups_id').select2({
            placeholder: "Grupo do Codigo de Conta",
            allowClear: true,
            language: {
                noResults: function (params) {
                    return "Não Há Grupos Cadastrado";
                }}
        }),
        $('.select-departaments_id').select2({
            placeholder: "Departamento do Codigo de Conta",
            allowClear: true,
            language: {
                noResults: function (params) {
                    return 'Não Há Departamentos Cadastrado';
                }}
        }),
        $('.select-users_id').select2({
            placeholder: "Login do Usuario",
            allowClear: true,
            language: {
                noResults: function (params) {
                    return "Não Há Usuarios Cadastrado";
                }}
        })
    });

    $(document).ready(function(){
    
        $("#delete :input").prop("disabled", true);
        $("#show :input").prop("disabled", true);
    });

</script>
@stop
