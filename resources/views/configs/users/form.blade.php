
<label for="description">Login de Acesso:</label>
<br>

    <div class="input-group p-2">
        <div class="input-group-prepend">
            <span class="input-group-text pr-3"><i class="fas fa-user"></i></span>
        </div>
        <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" id="name" name="name" value="{{old('name') ?? $user->name ?? ''}}" placeholder="Nome" >
        @if ($errors->has('name'))
            <div class="invalid-feedback" >
                @lang($errors->first('name').'-name')
            </div>
        @endif
    </div>

    <div class="input-group p-2">
        <div class="input-group-prepend">
            <span class="input-group-text pr-3"><i class="fas fa-envelope"></i></span>
        </div>
        <input type="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" id="email" name="email" value="{{old('email') ?? $user->email ?? ''}}" placeholder="Email">
        @if ($errors->has('email'))
            <div class="invalid-feedback" >
                @lang($errors->first('email').'-email')
            </div>
        @endif
    </div>

    <div class="input-group p-2">
        <div class="input-group-prepend">
            <span class="input-group-text pr-3"><i class="fas fa-user-lock"></i></span>
        </div>
        <select class="form-control {{ $errors->has('role') ? 'is-invalid' : '' }} select2" id="role" name="role" value="{{old('role') ?? $user->role ?? ''}}" >
            <option></option>
                @foreach ($roles as $role)
                    @continue($role == 'Root')
                        @if(old('role') == $role || $user->role == $role)
                            <option value="{{$role}}" selected>{{$role}}</option>
                        @else
                    <option value="{{$role}}">{{$role}}</option>
                @endif
                
            @endforeach
        </select>      
        @if ($errors->has('role'))
            <div class="invalid-feedback">
                @lang($errors->first('role').'-role')
            </div>
        @endif
    </div>

    <div class="input-group p-2">
            <div class="input-group-prepend">
                    <span class="input-group-text pr-3"><i class="fas fa-lock"></i></span>
                </div>
                <input type="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" id="password" name="password" value="" placeholder="Digite uma senha"  >
                @if ($errors->has('password'))
            <div class="invalid-feedback">
                @lang($errors->first('password').'-password')
            </div>
        @endif

    </div>

    <div class="input-group p-2">
        <div class="input-group-prepend">
            <span class="input-group-text pr-3"><i class="fas fa-lock"></i></span>
        </div>
        <input type="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" id="password_confirmation" name="password_confirmation" value="" placeholder="Confirme a senha" >
        @if ($errors->has('password_confirmation'))
            <div class="invalid-feedback">
                @lang($errors->first('password_confirmation').'-password_confirmation')
            </div>
        @endif

    </div>


@section('js')
<script>
    $(function () {
        $('.select2').select2({
            placeholder: "Permissão"
        })
    });

</script>
@stop
